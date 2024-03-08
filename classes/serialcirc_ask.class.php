<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.class.php,v 1.12.2.1 2021/12/21 15:05:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/serialcirc.inc.php"); // constant d�claration 
require_once($include_path."/templates/serialcirc_ask.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/serialcirc_diff.class.php");

class serialcirc_ask {	

	public $ask_info=array();

	public function __construct($id) {
		$this->id=intval($id);		
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$this->ask_info=array();
		$req="select * from serialcirc_ask where id_serialcirc_ask=".$this->id;
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {			
			if($r=pmb_mysql_fetch_object($resultat)){					
				$this->ask_info['id']=$r->id_serialcirc_ask;
				$this->ask_info['num_perio']=$r->num_serialcirc_ask_perio;
				$this->ask_info['num_serialcirc']=$r->num_serialcirc_ask_serialcirc;
				$this->ask_info['type']=$r->serialcirc_ask_type;
				$this->ask_info['statut']=$r->serialcirc_ask_statut;
				$this->ask_info['date']=$r->serialcirc_ask_date;
				$this->ask_info['comment']=$r->serialcirc_ask_comment;			
				$this->ask_info['num_empr']=$r->num_serialcirc_ask_empr;
				$this->ask_info['empr']=$this->empr_info($r->num_serialcirc_ask_empr);
				
				
				if(!$this->ask_info['num_perio']){					
					$this->ask_info['serialcirc_diff'] = new serialcirc_diff($r->num_serialcirc_ask_serialcirc);
					$this->ask_info['num_perio']=$this->ask_info['serialcirc_diff']->id_perio;
					
				}	
				if($this->ask_info['num_perio']){						
					$perio=new serial_display($this->ask_info['num_perio'],0,"","","","","",1,1);
					$this->ask_info['perio']['header']=$perio->header_texte;
					$this->ask_info['perio']['view_link']="./catalog.php?categ=serials&sub=view&view=abon&serial_id=".$this->ask_info['num_perio'];	
					$this->ask_info['perio']['id']=$this->ask_info['num_perio'];	
					$this->ask_info['num_abt_diff']=$this->empr_is_in_circ($this->ask_info['num_empr'],$this->ask_info['num_perio']);
					
					$this->ask_info['abts']=array();
					if(!$this->ask_info['num_abt_diff']) {				
						$req_abt="select * from abts_abts where num_notice=".$this->ask_info['num_perio'];
						$resultat_abt=pmb_mysql_query($req_abt);		
						$i=0;						
						if (pmb_mysql_num_rows($resultat_abt)) {
							while($r_abt=pmb_mysql_fetch_object($resultat_abt)){							
								$this->ask_info['abts'][$i]['id']=$r_abt->abt_id;							
								$this->ask_info['abts'][$i]['name']=$r_abt->abt_name;							
								$this->ask_info['abts'][$i]['link_diff']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r_abt->abt_id.
									"&empr_id=".$this->ask_info['num_empr'];								
								
								$i++;									
							}	
						}	
					}else{
						// d�j� abonn�
						$req_abt="select * from abts_abts where abt_id=".$this->ask_info['num_abt_diff'];
						$resultat_abt=pmb_mysql_query($req_abt);		
						$i=0;						
						if (pmb_mysql_num_rows($resultat_abt)) {
							$r_abt=pmb_mysql_fetch_object($resultat_abt);						
							$this->ask_info['abts'][$i]['id']=$r_abt->abt_id;							
							$this->ask_info['abts'][$i]['name']=$r_abt->abt_name;							
							$this->ask_info['abts'][$i]['link_diff']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r_abt->abt_id;				
									
						}
						
					}	
						
				}
				
			}
		}	
				
		// printr($this->ask_info);
	}
	
	public function empr_is_in_circ($id_empr,$id_perio){
		$req="select abt_id,id_serialcirc_diff from serialcirc_diff,serialcirc, abts_abts 
		where num_serialcirc_diff_serialcirc=id_serialcirc and num_serialcirc_abt=abt_id and  num_notice=$id_perio 
		and num_serialcirc_diff_empr=$id_empr";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			if($r=pmb_mysql_fetch_object($resultat)){			
				return $r->abt_id;	
			}		
		}	
		return 0;	
	}
	public function ask_send_mail($empr_id,$objet,$texte_mail){
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		
		$empr_info=$this->empr_info($empr_id);
		$texte_mail=str_replace("!!issue!!", $this->ask_info['perio']['header'], $texte_mail);			
		return mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
	}
	
	public function accept(){
		global $serialcirc_inscription_accepted_mail,$serialcirc_inscription_end_mail,$msg,$charset;
		
		if ($charset=="utf-8") {
			$serialcirc_inscription_accepted_mail = utf8_encode($serialcirc_inscription_accepted_mail);
			$serialcirc_inscription_end_mail = utf8_encode($serialcirc_inscription_end_mail);
		}
		
		$req="update serialcirc_ask set serialcirc_ask_statut=1 where id_serialcirc_ask=".$this->id;
		pmb_mysql_query($req);	
		// send mail
		if($this->ask_info['type']) $this->ask_send_mail($this->ask_info['num_empr'],$msg["serialcirc_circ_title"],$serialcirc_inscription_end_mail);
		else $this->ask_send_mail($this->ask_info['num_empr'],$msg["serialcirc_circ_title"],$serialcirc_inscription_accepted_mail);
	}
	
	public function refus(){
		global $serialcirc_inscription_no_mail,$msg,$charset;
		
		if ($charset=="utf-8") {
			$serialcirc_inscription_no_mail = utf8_encode($serialcirc_inscription_no_mail);
		}
		
		$req="update serialcirc_ask set serialcirc_ask_statut=2 where id_serialcirc_ask=".$this->id;
		pmb_mysql_query($req);	
		// send mail
		$this->ask_send_mail($this->ask_info['num_empr'],$msg["serialcirc_circ_title"],$serialcirc_inscription_no_mail);		
	}
	
	public static function set_inscription($id_perio,$id_empr,$id_serialcirc=0){
		if($id_serialcirc)$circ= ", num_serialcirc_ask_serialcirc= $id_serialcirc ";
		$req="update serialcirc_ask set serialcirc_ask_statut=3 $circ where num_serialcirc_ask_perio=$id_perio and num_serialcirc_ask_empr=$id_empr";
		pmb_mysql_query($req);	
		// send mail
		
	}
	
	public function delete(){
		if($this->ask_info['statut']==0) return; //pas accept� ou refus�e
		$req="delete from serialcirc_ask where id_serialcirc_ask=".$this->id;
		pmb_mysql_query($req);	
			
		// le supprimer de la list de diff si demande de d�sabonnement
		if($this->ask_info['type']==1){
			$req=" DELETE from serialcirc_diff WHERE num_serialcirc_diff_serialcirc=".$this->ask_info['num_serialcirc']." and num_serialcirc_diff_empr=".$this->ask_info['num_empr'];
			pmb_mysql_query($req);	
			
			// et dans le groupe
			$req=" select id_serialcirc_group from serialcirc_group, serialcirc_diff WHERE 
			serialcirc_diff_empr_type=1
			and num_serialcirc_group_diff= id_serialcirc_diff
			and num_serialcirc_diff_serialcirc=".$this->ask_info['num_serialcirc']." 			
			and num_serialcirc_group_empr=".$this->ask_info['num_empr'];
			
			$resultat=pmb_mysql_query($req);	
			while($r=pmb_mysql_fetch_object($resultat)){					
				$req=" DELETE from serialcirc_group	where id_serialcirc_group=$r->id_serialcirc_group";
				pmb_mysql_query($req);					
			}
		}
		
	}
	
	public function empr_info($id){
		$info=array();
		$req="select empr_cb, empr_nom ,  empr_prenom, empr_mail from empr where id_empr=".$id;
		$res_empr=pmb_mysql_query($req);
		if ($empr=pmb_mysql_fetch_object($res_empr)) {			
			$info['cb'] = $empr->empr_cb;
			$info['nom'] = $empr->empr_nom; 
			$info['prenom'] = $empr->empr_prenom;  
			$info['mail'] = $empr->empr_mail;  		
			$info['id_empr']=$id;
			$info['empr_libelle']=$info['nom']." ".$info['prenom']." ( ".$info['cb'] ." ) ";
			$info['view_link']='./circ.php?categ=pret&form_cb='.$empr->empr_cb;
		}
		$this->empr_info[$id]=$info;
		return $info;
	}	
	
} //serialcirc class end