<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOPACGeneric.class.php,v 1.17.2.1 2022/02/11 11:33:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $charset, $opac_url_base;
global $opac_etagere_order ;
global $gestion_acces_active, $gestion_acces_empr_notice;
global $opac_autres_lectures_tri;
global $opac_autres_lectures_nb_mini_emprunts;
global $opac_autres_lectures_nb_maxi;
global $opac_autres_lectures_nb_jours_maxi;
global $opac_autres_lectures;

require_once $class_path."/external_services.class.php";

class pmbesOPACGeneric extends external_services_api_class{

	/**
	 * 
	 * @param integer $OPACUserId : Id emprunteur
	 * @param integer $select : Filtre etageres 
	 * @return array
	 */
	public function list_shelves($OPACUserId, $filter = 0) {
		
		global $opac_etagere_order ;
		
		$tableau_etagere = [] ;

		//Par defaut, recuperation des etageres valides et visibles en page d'accueil (retro-compatibilite)
		$where_clause = "where visible_accueil=1 and ( (validite_date_deb<=sysdate() and validite_date_fin>=sysdate()) or validite=1 )";
		switch($filter) {
			case 1 :	
				//Recuperation des etageres valides 
				$where_clause = "where ( (validite_date_deb<=sysdate() and validite_date_fin>=sysdate()) or validite=1 )";
				break;
			case 2 :
				//Recuperation de toutes les etageres
				$where_clause = "";
				break;
			default :
				break;
		}

		$order_clause = "name";
		if ($opac_etagere_order) {
			$order_clause = "order by $opac_etagere_order";
		}
		
		$query = "select idetagere, name, comment from etagere $where_clause $order_clause";
		$result = pmb_mysql_query($query);

		if (pmb_mysql_num_rows($result)) {
			while ($etagere=pmb_mysql_fetch_assoc($result)) {
				$tableau_etagere[] = [
						'id' => $etagere['idetagere'],
						'name' => utf8_normalize($etagere['name']),
						'comment' => utf8_normalize($etagere['comment'])
						];
			}
		}
		return $tableau_etagere;
	}
	
	public function retrieve_shelf_content($shelf_id, $OPACUserId) {

		$shelf_id = intval($shelf_id);
		if (!$shelf_id)
			return array();

		//droits d'acces emprunteur/notice
		$acces_j='';
		global $gestion_acces_active, $gestion_acces_empr_notice;
		if ($OPACUserId != -1 && $gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($OPACUserId,4,'notice_id');
		}

		if($acces_j) {
			$statut_j='';
			$statut_r='';
		} else {
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0) or (notice_visible_opac_abon=1 and notice_visible_opac=1)) ";
		}

		$sql = "SELECT object_id FROM etagere LEFT JOIN etagere_caddie ON (etagere_id = idetagere) LEFT JOIN caddie_content ON (caddie_content.caddie_id = etagere_caddie.caddie_id) LEFT JOIN notices ON (object_id = notice_id) $acces_j $statut_j WHERE etagere_id = ".$shelf_id." AND object_id $statut_r GROUP BY object_id";
		$res = pmb_mysql_query($sql);
		$results = array();
		while($row = pmb_mysql_fetch_row($res)) {
			$results[] = $row[0];
		}
		return $results;
	}
	
	public function list_locations() {

		$results = array();
		$sql = "SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac = 1";
		$res = pmb_mysql_query($sql);
		while($row = pmb_mysql_fetch_assoc($res)) {
			$results[] = array(
				"location_id" => $row["idlocation"],
				"location_caption" => utf8_normalize($row["location_libelle"])
			);
		}

		return $results;
	}
	
	public function list_sections($location) {
		
		global $opac_url_base;
		$results = [];
		
		$location = intval($location);
		$requete="select idsection, section_libelle, section_pic from docs_section, exemplaires where expl_location=$location and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
		$resultat=pmb_mysql_query($requete);
		while ($r=pmb_mysql_fetch_object($resultat)) {
			$aresult = array();
			$aresult["section_id"] = $r->idsection;
			$aresult["section_location"] = $location;
			$aresult["section_caption"] = utf8_normalize($r->section_libelle);
			$aresult["section_image"] = $opac_url_base.($r->section_pic ? utf8_normalize($r->section_pic) : "images/rayonnage-small.png");
			$results[] = $aresult;
		}
		return $results;
	}
	
	public function is_also_borrowed_enabled() {
		global $opac_autres_lectures_tri;
		return $opac_autres_lectures_tri ? true : false;
	}
	
	public function also_borrowed ($notice_id=0,$bulletin_id=0) {

		global $opac_autres_lectures_tri;
		global $opac_autres_lectures_nb_mini_emprunts;
		global $opac_autres_lectures_nb_maxi;
		global $opac_autres_lectures_nb_jours_maxi;
		global $opac_autres_lectures;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		$results = array();
		
		if (!$opac_autres_lectures || (!$notice_id && !$bulletin_id)) return $results;
	
		if (!$opac_autres_lectures_nb_maxi) $opac_autres_lectures_nb_maxi = 999999 ;
		if ($opac_autres_lectures_nb_jours_maxi) $restrict_date=" date_add(oal.arc_fin, INTERVAL $opac_autres_lectures_nb_jours_maxi day)>=sysdate() AND ";
		if ($notice_id) $pas_notice = " oal.arc_expl_notice!=$notice_id AND ";
		if ($bulletin_id) $pas_bulletin = " oal.arc_expl_bulletin!=$bulletin_id AND ";
		// Ajout ici de la liste des notices lues par les lecteurs de cette notice
		$rqt_autres_lectures = "SELECT oal.arc_expl_notice, oal.arc_expl_bulletin, count(*) AS total_prets,
					trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '%d/%m/%Y'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id 
				FROM ((((pret_archive AS oal JOIN
					(SELECT distinct arc_id_empr FROM pret_archive nbec where (nbec.arc_expl_notice='".$notice_id."' AND nbec.arc_expl_bulletin='".$bulletin_id."') AND nbec.arc_id_empr !=0) as nbec
					ON (oal.arc_id_empr=nbec.arc_id_empr and oal.arc_id_empr!=0 and nbec.arc_id_empr!=0))
					LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id )
					LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id) 
					LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				WHERE $restrict_date $pas_notice $pas_bulletin oal.arc_id_empr !=0
				GROUP BY oal.arc_expl_notice, oal.arc_expl_bulletin
				HAVING total_prets>=$opac_autres_lectures_nb_mini_emprunts 
				ORDER BY $opac_autres_lectures_tri 
				"; 
	
		$res_autres_lectures = pmb_mysql_query($rqt_autres_lectures); 
		if (!$res_autres_lectures)
			return $results;
		if (pmb_mysql_num_rows($res_autres_lectures)) {
			
			$inotvisible=0;
			$aresult = array();
	
			//droits d'acces emprunteur/notice
			$acces_j='';
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			}
				
			if($acces_j) {
				$statut_j='';
				$statut_r='';
			} else {
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			}
			
			while (($data=pmb_mysql_fetch_array($res_autres_lectures))) { // $inotvisible<=$opac_autres_lectures_nb_maxi
				$requete = "SELECT  1  ";
				$requete .= " FROM notices $acces_j $statut_j  WHERE notice_id='".$data['not_id']."' $statut_r ";
				$myQuery = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($myQuery) && $inotvisible<=$opac_autres_lectures_nb_maxi) { // pmb_mysql_num_rows($myQuery)
					$inotvisible++;
					$titre = $data['tit'];
					// **********
					$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
					$responsab = get_notice_authors($data['not_id']) ;
					$as = array_search ("0", $responsab["responsabilites"]) ;
					if ($as!== FALSE && $as!== NULL) {
						$auteur_0 = $responsab["auteurs"][$as] ;
						$auteur = new auteur($auteur_0["id"]);
						$mention_resp = $auteur->get_isbd();
					} else {
						$aut1_libelle = array();
						$as = array_keys ($responsab["responsabilites"], "1" ) ;
						for ($i = 0 ; $i < count($as) ; $i++) {
							$indice = $as[$i] ;
							$auteur_1 = $responsab["auteurs"][$indice] ;
							$auteur = new auteur($auteur_1["id"]);
							$aut1_libelle[]= $auteur->get_isbd();
						}
						$mention_resp = implode (", ",$aut1_libelle) ;
					}
					$mention_resp ? $auteur = $mention_resp : $auteur="";
				
					$aresult["notice_id"] = $data['not_id'];
					$aresult["notice_title"] = $titre;
					$aresult["notice_author"] = $auteur;
					$results[] = $aresult;
				}
			}
		};
		
		return $results;
		}
	
	public function get_location_information($location_id) {
		$result = array();
		
		$location_id = intval($location_id);
		if (!$location_id)
			throw new Exception("Missing parameter: location_id");
		
		$sql = "SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac = 1 AND idlocation = ".$location_id;
		$res = pmb_mysql_query($sql);
		if ($row = pmb_mysql_fetch_assoc($res))
			$result = array(
				"location_id" => $row["idlocation"],
				"location_caption" => utf8_normalize($row["location_libelle"])
			);

		return $result;
	}
	
	public function get_location_information_and_sections($location_id) {
		return array(
			"location" => $this->get_location_information($location_id),
			"sections" => $this->list_sections($location_id)
		);
	}
	
	public function get_section_information($section_id) {

		global $opac_url_base;
		
		$result = [];
		$section_id = intval($section_id);
		if (!$section_id) {
			throw new Exception("Missing parameter: section_id");
		}
		$requete="select idsection, section_libelle, section_pic, expl_location from docs_section, exemplaires where idsection = ".$section_id." and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
		$resultat=pmb_mysql_query($requete);
		if ($r=pmb_mysql_fetch_object($resultat)) {
			$result["section_id"] = $r->idsection;
			$result["section_location"] = $r->expl_location;
			$result["section_caption"] = utf8_normalize($r->section_libelle);
			$result["section_image"] = $opac_url_base.($r->section_pic ? utf8_normalize($r->section_pic) : "images/rayonnage-small.png");
		}
		return $result;
	}
	
	public function get_all_locations_and_sections() {
		
		global $opac_url_base;
		
		$results = [];
		$sql = "SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac = 1";
		$res = pmb_mysql_query($sql);
		while($row = pmb_mysql_fetch_assoc($res)) {
			$aresult = array(
				'location' => array(
					"location_id" => $row["idlocation"],
					"location_caption" => utf8_normalize($row["location_libelle"])
				),
				'sections' => array(),
			);
			
			$sql2="select idsection, section_libelle, section_pic from docs_section, exemplaires where expl_location=".($row["idlocation"])." and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
			$res2=pmb_mysql_query($sql2);
			while ($r=pmb_mysql_fetch_object($res2)) {
				$asection = array();
				$asection["section_id"] = $r->idsection;
				$asection["section_location"] = $row["idlocation"];
				$asection["section_caption"] = utf8_normalize($r->section_libelle);
				$asection["section_image"] = $opac_url_base.($r->section_pic ? utf8_normalize($r->section_pic) : "images/rayonnage-small.png");
				$aresult['sections'][] = $asection;
			}
			
			$results[] = $aresult;
		}

		return $results;
	}
	
	public function get_infopage($infopage_id, $js_subst = "", $encoding ="") {
		global $charset, $opac_url_base;
		
		$requete = "SELECT content_infopage FROM infopages WHERE id_infopage = $infopage_id";
		$result = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result)) {
			$infopage = pmb_mysql_result($result, 0, 0);
			if (!empty($js_subst)) {
				$infopage = str_replace($opac_url_base."index.php?lvl=infopages&amp;pagesid=", "!!INFOPAGE_URL!!", $infopage);
				preg_match_all("/!!INFOPAGE_URL!!([0-9]+)/", $infopage, $tab);
				$nb_tabs = count($tab[0]);
				for ($i = 0; $i < $nb_tabs; $i++) {
					$infopage = preg_replace("/".$tab[0][$i]."/", "#\" onclick=\"".str_replace("!!id!!", $tab[1][$i], $js_subst).";return false;", $infopage);	
				}
			}
			if ($encoding == "utf-8" && $charset != "utf-8") {
			    return utf8_encode($infopage);
			} elseif ($encoding != "utf-8" && $charset == "utf-8") {
			    return utf8_decode($infopage);
			} else {
			    return $infopage;
			}
		}
	}
	
	public function get_marc_table($type){
		global $charset;
		$marc_list = new marc_list($type);
		if ($charset != "utf-8"){
			foreach($marc_list->table as $key => $value){
				$marc_list->table[$key] = utf8_encode($value);
			}
		}
		return $marc_list->table;
	}	
}
