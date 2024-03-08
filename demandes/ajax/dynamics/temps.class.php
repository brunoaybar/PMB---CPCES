<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: temps.class.php,v 1.4.10.1 2021/12/27 14:05:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class temps{
	
	public $id_element = 0;
	public $champ_entree = "";
	public $champ_sortie = "";
	public $display="";
	public $idobjet = 0;
	
	public function __construct($id_elt,$fieldElt){
		global $quoifaire;
		
		$this->id_element = $id_elt;
		$format_affichage = explode('/',$fieldElt);
		$this->champ_entree = $format_affichage[0];
		if($format_affichage[1]) $this->champ_sortie = $format_affichage[1];		
		$ids = explode("_",$id_elt);
		$this->idobjet = $ids[1];
		
		switch($quoifaire){
			
			case 'edit':
				$this->make_display();
				break;
			case 'save':
				$this->update();
				break;
		}
	}
	
	public function make_display(){
		global $msg,$charset;
		
		$rqt = "select temps_passe from demandes_actions where id_action='".$this->idobjet."'";
		$res = pmb_mysql_query($rqt);
		$act = pmb_mysql_fetch_object($res);
		
		$display ="";
		$submit = "<input type='submit' class='bouton' name='soumission' id='soumission' value='".$msg['demandes_valid_progression']."'/>";
		switch($this->champ_entree){			
			case 'text':
				$display = "<form method='post'><input type='text' class='saisie-5em' id='save_".$this->id_element."' name='save_".$this->id_element."' value='".htmlentities($act->temps_passe,ENT_QUOTES,$charset)."' />$submit</form>";
				break;
			default:
				$display = "<label id='".$this->id_element."' />".htmlentities($act->temps_passe,ENT_QUOTES,$charset)."</label>";
				break;
		}		
		$this->display = $display;
	}
	
	public function update(){
		global $temps, $msg;		
		
		$req = "update demandes_actions set temps_passe='".$temps."' where id_action='".$this->idobjet."'";
		pmb_mysql_query($req);
		switch($this->champ_sortie){
			default :
				if(strpos($temps,$msg['demandes_action_time_unit']) !== false)
					$this->display = $temps;
				else $this->display = $temps.$msg['demandes_action_time_unit'];
			break;
		}
		
	}
}
?>