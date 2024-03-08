<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_situation.class.php,v 1.7.2.6 2022/04/28 14:36:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class resa_situation{
	
	public $id_resa;
	
	public $resa;
	
	public $resa_idnotice;
	
	public $resa_idbulletin;
	
	public $resa_cb;
	
	public $idlocation;
	
	public $rank;
	
	public $no_aff;
	
	public $lien_deja_affiche;
	
	public $lien_transfert;
	
	public $display;
	
	protected $display_already_called;
	
	public function __construct($id_resa= 0) {
		$this->id_resa = intval($id_resa);
	}

	public function initialize_no_aff() {
	    global $pmb_transferts_actif, $transferts_choix_lieu_opac;
	    
	    // on compte le nombre total d'exemplaires prêtables pour la notice
	    $total_ex = $this->resa->get_number_expl_lendable();
	    if($this->resa->get_restrict_expl_location_query() && !$total_ex) $this->no_aff=1;
	    // on compte le nombre d'exemplaires sortis
	    $total_sortis = $this->resa->get_number_expl_out();
	    
	    // on compte le nombre d'exemplaires en circulation
	    $total_in_circ = $this->resa->get_number_expl_in_circ();
	    
	    // on en déduit le nombre d'exemplaires disponibles
	    $total_dispo = $total_ex - $total_sortis - $total_in_circ;
	    if(!$total_dispo) {
            if ( ($pmb_transferts_actif=="1") &&  $transferts_choix_lieu_opac!=3) {// && ($f_loc!=0) ?
                $this->no_aff=0;
            }
	    }
	}
	
	protected function is_first_availability() {
		//on regarde si les rangs précédents sont validés
		$is_first_availability = true;
		$ranks = recupere_rangs($this->resa->id_notice, $this->resa->id_bulletin/*, $this->filters['removal_location']*/);
		if(!empty($ranks)) {
			$ranks = array_slice($ranks, 0, $this->rank, true);
			foreach ($ranks as $id_resa=>$rank) {
				if($rank < $this->rank) {
					if($is_first_availability && empty(reservation::get_cb_from_id($id_resa))) {
						$is_first_availability = false;
					}
				}
			}
		}
		return $is_first_availability;
	}
	
	protected function get_display_other_location_lendable($location=0, $outside=false) {
		$display = '';
		$expl_locations = $this->resa->get_expl_locations_lendable($location, $outside);
		if(count($expl_locations) == 1) {
			$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$expl_locations[0]."'";
			$display .= "<br />(".pmb_mysql_result(pmb_mysql_query($rqt),0).")";
		}
		return $display;
	}
	
	protected function get_display_availability_next_rank($total_available=0, $location=0, $outside=false) {
		global $msg;
		
		$display = '';
		$total_reserved = $this->resa->get_number_expl_reserved();
		if ($total_reserved == ($this->rank-1)) {
			$total_reserved_localized = $this->resa->get_number_expl_reserved($location, $outside);
			$total_available_not_reserved = $total_available - $total_reserved_localized;
			if($total_available_not_reserved>0) {
				if($outside) {
					$display .= $msg["resa_expl_dispo_other_location"];
					$display .= $this->get_display_other_location_lendable($location, $outside);
				} else {
					// un exemplaire est disponible pour le réservataire (affichage : disponible)
					$display .= "<strong>".$msg['expl_resa_available']."</strong>";
				}
			}
		}
		return $display;
	}
	
	/**
	 * Calcul de la colonne situation
	 * @param integer $info_gestion
	 */
	public function get_display($info_gestion=NO_INFO_GESTION) {
		global $msg;
		global $pmb_transferts_actif, $f_loc, $transferts_choix_lieu_opac;
		global $has_resa_available; // utilisé au niveau de la fiche lecteur
		
		//A-t-on déjà appelé cette méthode ? $display peut être égale à une chaîne vide
		if(!empty($this->display_already_called) && isset($this->display)) {
			return $this->display;
		}
		
		//Pour memo : la condition avant
		//if (($this->resa->id_notice != $this->precedenteresa_idnotice) || ($this->resa->id_bulletin != $this->precedenteresa_idbulletin)) {
		if ($this->rank == 1 || $this->is_first_availability()) {
			$this->lien_deja_affiche = false;
			// détermination de la date à afficher dans la case retour pour le rang 1
			// disponible, réservé ou date de retour du premier exemplaire
			
			// on compte le nombre total d'exemplaires prêtables pour la notice
			$total_ex = $this->resa->get_number_expl_lendable();
			if($this->resa->get_restrict_expl_location_query() && !$total_ex) $this->no_aff=1;
			// on compte le nombre d'exemplaires sortis
			$total_sortis = $this->resa->get_number_expl_out();
			
			// on compte le nombre d'exemplaires en circulation
			$total_in_circ = $this->resa->get_number_expl_in_circ();
			
			// on en déduit le nombre d'exemplaires disponibles
			$total_dispo = $total_ex - $total_sortis - $total_in_circ;
			
			$this->lien_transfert = false;
			if($total_dispo>0) {
				if($this->resa->date_debut && $this->resa->date_debut != '0000-00-00') { //résa validée ?
					$has_resa_available = true;
				}
				// un exemplaire est disponible pour le réservataire (affichage : disponible)
				$this->display = "<strong>".$msg['expl_resa_available']."</strong>";
				//est-il en rayon dans une autre localisation ?
				$expl_locations = $this->resa->get_expl_locations_lendable(0, true);
				if($this->idlocation && count($expl_locations) == 1 && !in_array($this->idlocation, $expl_locations)) {
					$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$expl_locations[0]."'";
					$this->display .= "<br />(".pmb_mysql_result(pmb_mysql_query($rqt),0).")";
				}
				if($this->resa_cb && $this->resa->formatted_date_fin) $this->display = "<strong>".$msg['expl_reserve']."</strong>";
				elseif($this->rank>$total_dispo)	$this->display = "<strong>".$msg['expl_resa_already_reserved']."</strong>";
				if ( ($pmb_transferts_actif=="1") && ($info_gestion==GESTION_INFO_GESTION) ) {
					$dest_loc = resa_loc_retrait($this->resa->id);
					if ($dest_loc!=0) {
						$total_ex = $this->resa->get_number_expl_lendable($dest_loc);
						if ($total_ex==0) {
							//on a pas d'exemplaires sur le site de retrait
							//on regarde si on en ailleurs
							$total_ex = $this->resa->get_number_expl_lendable($dest_loc, true);
							if ($total_ex!=0) {
								//on en a au moins un ailleurs!
								//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
								$query = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert AND etat_transfert=0 AND origine=4 AND origine_comp=".$this->resa->id;
								$tresult = pmb_mysql_query($query);
								if (pmb_mysql_num_rows($tresult)) {
									//on a un transfert en cours
									$this->display = "<strong>" . $msg["transferts_circ_resa_lib_en_transfert"] . "</strong>";
								} elseif($total_ex>=$this->rank)	{
									$this->lien_transfert = true;
									if($this->resa->transfert_resa_dispo($dest_loc)){
										$this->display = $msg["resa_expl_dispo_other_location"];
										$this->display .= $this->get_display_other_location_lendable($dest_loc, true);
									}
								}
							}
						} //if ($total_ex==0)
					} //if ($dest_loc!=0)
				} //if ( ($pmb_transferts_actif=="1") && ($info_gestion==GESTION_INFO_GESTION) )
			} else {
				if($total_dispo) {
					// un ou des exemplaires sont disponibles, mais pas pour ce réservataire (affichage : reservé)
					$this->display = $msg["resa_expl_reserve"];
				} else {
					// rien n'est disponible, on trouve la date du premier retour
					$query = "SELECT date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour from pret p, exemplaires e ";
					if ($this->resa->id_notice) $query .= " WHERE e.expl_notice=".$this->resa->id_notice;
					elseif ($this->resa->id_bulletin) $query .= " WHERE e.expl_bulletin=".$this->resa->id_bulletin;
					else $query .= " WHERE 0"; // ni bulletin ni notice
					$query .= " AND e.expl_id=p.pret_idexpl";
					$query .= " ORDER BY p.pret_retour LIMIT 1";
					$tresult = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($tresult)) {
						$this->display = pmb_mysql_result($tresult, 0, 0);
						$info_retour_prevu=$this->display;
					}else {
						if($total_in_circ) {
							$this->display = $msg['transferts_circ_retour_filtre_circ'];
						} else {
							$this->display = $msg["resa_no_expl"];
						}
						$info_retour_prevu='';
					}
					if ( ($pmb_transferts_actif=="1") &&  $transferts_choix_lieu_opac!=3) {// && ($f_loc!=0) ?
						//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
						$query = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert AND etat_transfert=0 AND origine=4 AND origine_comp=".$this->resa->id;
						$this->no_aff=0;
						$tresult = pmb_mysql_query($query);
						if (pmb_mysql_num_rows($tresult)) {
							//on a un transfert en cours
							$this->display = "<strong>" . $msg["transferts_circ_resa_lib_en_transfert"] . "</strong>";
						} else {
						    if($f_loc) {
						        $total_ex = $this->resa->get_number_expl_transferts_lendable($f_loc, true);
						    } else {
						        $dest_loc = resa_loc_retrait($this->resa->id);
						        $total_ex = $this->resa->get_number_expl_transferts_lendable($dest_loc, true);
						    }
							if($total_ex>=$this->rank)	{
								$this->lien_transfert = true;
								if($this->resa->transfert_resa_dispo($f_loc)){
									$this->display = $msg["resa_expl_dispo_other_location"];
									if($info_retour_prevu)$this->display = $msg["resa_condition"]." : ".$info_retour_prevu."<br>".$this->display;
									$this->display .= $this->get_display_other_location_lendable($f_loc, true);
								}
							}
						}
					}
				}
			}
		} else {
			$this->display='';
			if($this->resa_cb && $this->resa->formatted_date_fin) $this->display = "<strong>".$msg['expl_reserve']."</strong>";
			if ($this->lien_deja_affiche) {
				$this->lien_transfert = false;
			}
			if ((!$this->lien_transfert)&&($pmb_transferts_actif=="1")&&($info_gestion==GESTION_INFO_GESTION)&&(!$this->lien_deja_affiche)) {
				$dest_loc = resa_loc_retrait($this->resa->id);
				
				//on est sur la même notice que la ligne précédente, donc sur une résa de rang 2 ou plus
				// on compte le nombre total d'exemplaires prêtables pour la notice
				// sur la localisation de retrait
				$total_ex = $this->resa->get_number_expl_lendable($dest_loc);
				// on compte le nombre d'exemplaires sortis
				// sur la localisation de retrait
				$total_sortis = $this->resa->get_number_expl_out($dest_loc);
				
				// on en déduit le nombre d'exemplaires disponibles
				$total_dispo = $total_ex - $total_sortis;
				
				//S'il n'y a aucun exemplaire dispo pour le rang en cours, on va regarder ailleurs...
				if ($total_dispo < $this->rank) {
					if ($dest_loc!=0) {
						$total_ex = $this->resa->get_number_expl_lendable($dest_loc, true);
						if ($total_ex!=0) {
							//on en a au moins un ailleurs!
							//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
							$query = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert AND etat_transfert=0 AND origine=4 AND origine_comp=".$this->resa->id;
							$tresult = pmb_mysql_query($query);
							if (!pmb_mysql_num_rows($tresult)) {
								$this->lien_transfert = true;
								$this->lien_deja_affiche = true;
								
								//Si les rangs précédents ont été réservés, on regarde la disponibilité du suivant...
								$this->display = $this->get_display_availability_next_rank($total_ex, $dest_loc, true);
							}
						}
					}
				} else {
					//Si les rangs précédents ont été réservés, on regarde la disponibilité du suivant...
					$this->display = $this->get_display_availability_next_rank($total_dispo, $dest_loc);
				}
			}
		}
		$this->display_already_called = true;
		return $this->display;
	}
	
	public static function get_conditions() {
		global $msg;
		global $pmb_transferts_actif;
		
		$conditions = array(
				'expl_resa_available' => $msg['expl_resa_available'],
				'resa_expl_reserve' => $msg['resa_expl_reserve']
		);
		if($pmb_transferts_actif) {
			$conditions['transferts_circ_resa_lib_en_transfert'] = $msg['transferts_circ_resa_lib_en_transfert'];
		}
		return $conditions;
	}
	
	public function get_id_resa() {
		return $this->id_resa;
	}
	
	public function get_no_aff() {
		return $this->no_aff;
	}
	
	public function get_lien_deja_affiche() {
		return $this->lien_deja_affiche;
	}
	
	public function set_resa($resa) {
		$this->resa = $resa;
		return $this;
	}
	
	public function set_resa_cb($resa_cb) {
		$this->resa_cb = $resa_cb;
		return $this;
	}
	
	public function set_idlocation($idlocation) {
		$this->idlocation = $idlocation;
		return $this;
	}
	
	public function set_rank($rank) {
		$this->rank = $rank;
		return $this;
	}
	
	public function set_no_aff($no_aff) {
		$this->no_aff = $no_aff;
		return $this;
	}
	
	public function set_lien_deja_affiche($lien_deja_affiche) {
		$this->lien_deja_affiche = $lien_deja_affiche;
		return $this;
	}
}