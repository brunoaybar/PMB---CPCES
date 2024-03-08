<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_copy.class.php,v 1.1.2.4 2022/01/17 13:28:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class serialcirc_copy {	

	protected $id;
	
	protected $num_empr;
	
	protected $num_bulletin;
	
	protected $analysis;
	
	protected $date;
	
	protected $state;
	
	protected $comment;
	
	protected $bulletin_id;
	
	protected $bulletin_notice;
	
	protected $bulletin_numero;
	
	protected $bulletin_mention_date;
	
	protected $serial_tit1;
	
	public function __construct($id) {
		$this->id=intval($id);		
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$this->num_empr = 0;
		$this->num_bulletin = 0;
		$this->analysis = '';
		$this->date = '';
		$this->state = 0;
		$this->comment = '';
		$this->bulletin_id = 0;
		$this->bulletin_notice = 0;
		$this->bulletin_numero = '';
		$this->bulletin_mention_date = '';
		$this->serial_tit1 = '';
		$query = "SELECT * FROM serialcirc_copy
			JOIN bulletins ON serialcirc_copy.num_serialcirc_copy_bulletin = bulletins.bulletin_id
			JOIN notices ON bulletins.bulletin_notice = notices.notice_id 
			WHERE id_serialcirc_copy=".$this->id;
		$result = pmb_mysql_query($query);	
		if (pmb_mysql_num_rows($result)) {			
			if($row=pmb_mysql_fetch_object($result)){
				$this->num_empr = $row->num_serialcirc_copy_empr;
				$this->num_bulletin = $row->num_serialcirc_copy_bulletin;
				$this->analysis = $row->serialcirc_copy_analysis;
				$this->date = $row->serialcirc_copy_date;
				$this->state = $row->serialcirc_copy_state;
				$this->comment = $row->serialcirc_copy_comment;
				$this->bulletin_id = $row->bulletin_id;
				$this->bulletin_notice = $row->bulletin_notice;
				$this->bulletin_numero = $row->bulletin_numero;
				$this->bulletin_mention_date = $row->mention_date;
				$this->serial_tit1 = $row->tit1;
			}
		}	
	}
	
	public function copy_accept(){
		global $msg, $biblio_name, $biblio_email, $PMBuseremailbcc;
		
		$objet=$msg["serialcirc_circ_title"];
		$texte_mail = $msg["serialcirc_copy_accepted_mail_text"];
		$texte_mail = str_replace("!!issue!!", $this->serial_tit1."-".$this->bulletin_numero, $texte_mail);
		$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
		
		$empr = serialcirc::empr_info($this->num_empr);
		mailpmb($empr["prenom"]." ".$empr["nom"], $empr["mail"], $objet, $texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
		
		$query = "update serialcirc_copy set serialcirc_copy_state=1  where id_serialcirc_copy=".$this->id;
		pmb_mysql_query($query);
		return true;
	}
	
	public static function copy_isdone($bul_id){
		global $msg,$biblio_name, $biblio_email,$PMBuseremailbcc;
		global $opac_url_base;
		
		$query = "select * from serialcirc_copy where num_serialcirc_copy_bulletin=$bul_id ";
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$serialcirc_copy = new serialcirc_copy($row->id_serialcirc_copy);
			
			$objet=$msg["serialcirc_circ_title"];
			$texte_mail = $msg['serialcirc_copy_isdone_mail_text'];
			$texte_mail = str_replace("!!issue!!", $serialcirc_copy->get_serial_tit1()."-".$serialcirc_copy->get_bulletin_numero(), $texte_mail);
			$texte_mail = str_replace("!!see!!", "<a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$serialcirc_copy->get_bulletin_id()."'>".$serialcirc_copy->get_bulletin_numero()."</a>", $texte_mail);
			$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
			
			$empr = serialcirc::empr_info($serialcirc_copy->get_num_empr());
			mailpmb($empr["prenom"]." ".$empr["nom"], $empr["mail"], $objet, $texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
			// on efface
			$query = "delete from serialcirc_copy where num_serialcirc_copy_bulletin=$bul_id ";
			pmb_mysql_query($query);
		}
	}
	
	public function copy_none(){
		global $msg, $biblio_name, $biblio_email, $PMBuseremailbcc;
		
// 		if(!$this->index_info_copy[$copy_id]) return false;
		$query = "delete from serialcirc_copy where id_serialcirc_copy=".$this->id;
		pmb_mysql_query($query);
// 		$copy=$this->index_info_copy[$copy_id];
		
		$objet = $msg["serialcirc_circ_title"];
		$texte_mail=$msg['serialcirc_copy_no_mail_text'];
		$texte_mail=str_replace("!!issue!!", $this->serial_tit1."-".$this->bulletin_numero, $texte_mail);
		$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
		
		$empr = serialcirc::empr_info($this->num_empr);
		mailpmb($empr["prenom"]." ".$empr["nom"], $empr["mail"], $objet, $texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
		
		return true;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_num_empr() {
		return $this->num_empr;
	}
	
	public function get_bulletin_id() {
		return $this->bulletin_id;
	}
	
	public function get_num_bulletin() {
		return $this->num_bulletin;
	}
	
	public function get_bulletine_date() {
		return $this->bulletine_date;
	}
	
	public function get_analysis() {
		return $this->analysis;
	}
	
	public function get_date() {
		return $this->date;
	}
	
	public function get_state() {
		return $this->state;
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	public function get_bulletin_notice() {
		return $this->bulletin_notice;
	}
	
	public function get_bulletin_numero() {
		return $this->bulletin_numero;
	}
	
	public function get_bulletin_mention_date() {
		return $this->bulletin_mention_date;
	}
	
	public function get_serial_tit1() {
		return $this->serial_tit1;
	}
}