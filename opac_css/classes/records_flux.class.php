<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: records_flux.class.php,v 1.14.2.1 2021/12/28 10:10:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/filter_results.class.php");
require_once("$class_path/notice_affichage.class.php");

global $opac_notice_affichage_class;
if ($opac_notice_affichage_class) {
	if (file_exists($class_path."/notice_affichage/".$opac_notice_affichage_class.".class.php")) {
		require_once($class_path."/notice_affichage/".$opac_notice_affichage_class.".class.php");
	} else {
		require_once($class_path."/notice_affichage.ext.class.php");
	}
}

// definition de la classe de gestion des 'flux RSS'
class records_flux {

// ---------------------------------------------------------------
//		proprietes de la classe
// ---------------------------------------------------------------
	
	protected $notices_list = array();
	protected $title = "";
	protected $link = "";
	protected $description = "";
	protected $copyright = "";
	protected $language = "";
	protected $managingEditor = "";
	protected $webMaster = "";
	protected $imageUrl = "";
	protected $imageTitle = "";
	protected $imageLink = "";
	protected $rssRecordsFormat = 0;
	protected $idTpl = 0;
	public $envoi = "";
	protected $limit = 0;
	
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($flag_all=true) {	
		global $msg;	
		global $opac_biblio_name;
		if($flag_all)$this->getRecords();	
		$this->title = sprintf($msg["record_rss_flux_name"], $opac_biblio_name);;
	}
		
	public function getRecords() {
		$this->notices_list=array();
		$req = "SELECT notice_id FROM notices ORDER by create_date DESC limit ".$this->limit;
		$res = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($res)) {
			while($r=pmb_mysql_fetch_object($res)){
				$this->notices_list[] = $r->notice_id;
			}
		}
		if(!count($this->notices_list)) return array();
		$filter=new filter_results($this->notices_list);
		$this->notices_list=$filter->get_array_results();
		return $this->notices_list;
	}
	
	public function setRecords($notices_list) {
		global $type_search;
		$this->notices_list=array();
		if(!count($notices_list)){
			return array();
		}
		
		if (TYPE_EXTERNAL == $type_search) {
		    //Tri des notices sur date d'import
		    $notices_list = $this->sort_external_notices_list($notices_list);
		    //Limit de la liste
		    return $this->notices_list = array_slice($notices_list, 0, $this->limit);
		} 
		
		$req = "SELECT notice_id FROM  notices	WHERE notice_id in (".implode(",", $notices_list).") ORDER by create_date DESC limit ".$this->limit;
		$res = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($res)) {
			while($r=pmb_mysql_fetch_object($res)){
				$this->notices_list[]= $r->notice_id;
			}
		}
		if(!count($this->notices_list)) return array();
		$filter=new filter_results($this->notices_list);
		$this->notices_list=$filter->get_array_results();
		return $this->notices_list;
	}
	
	public function setTitle($title) {
		$this->title=$title;
	}
	
	public function setDescription($description) {
		$this->description=$description;
	}
	
	public function setCopyright($copyright) {
		$this->copyright=$copyright;
	}
	
	public function setLanguage($language) {
		$this->language=$language;
	}
	
	public function setManagingEditor($managingEditor) {
		$this->managingEditor=$managingEditor;
	}
	
	public function setWebMaster($webMaster) {
		$this->webMaster=$webMaster;
	}
	
	public function setLink($link) {
		$this->link=$link;
	}
	
	public function setImageUrl($url) {
		$this->imageUrl=$url;
	}
	
	public function setImageTitle($title) {
		$this->imageTitle=$title;
	}
	
	public function setImageLink($link) {
		$this->imageLink=$link;
	}
	
	public function setRssRecordsFormat($rssRecordsFormat) {
	    $this->rssRecordsFormat=$rssRecordsFormat;
	}
	
	public function setIdTpl($idTpl) {
	    $this->idTpl=$idTpl;
	}
	
	// ---------------------------------------------------------------
	//		generation du fichier XML
	// ---------------------------------------------------------------
	public function xmlfile() {		
	    global $pmb_bdd_version, $charset, $type_search;
		if (!$charset) $charset='ISO-8859-1';
		
		$this->envoi="<?xml version=\"1.0\" encoding=\"".$charset."\"?>
		<!-- RSS generated by PMB on ".addslashes(date("D, d/m/Y H:i:s"))." -->
		<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
			<channel>
				<title>".htmlspecialchars ($this->title,ENT_QUOTES, $charset)."</title>
				<link>".htmlspecialchars ($this->link,ENT_QUOTES, $charset)."</link>
				<description>".htmlspecialchars ($this->description,ENT_QUOTES, $charset)."</description>
				<language>".htmlspecialchars ($this->language,ENT_QUOTES, $charset)."</language>
				<copyright>".htmlspecialchars ($this->copyright,ENT_QUOTES, $charset)."</copyright>
				<lastBuildDate>".addslashes(date("D, d M Y H:i:s O"))."</lastBuildDate>
				<docs>http://backend.userland.com/rss</docs>
				<generator>PMB Version ".$pmb_bdd_version."</generator>
				<managingEditor>".htmlspecialchars ($this->managingEditor,ENT_QUOTES, $charset)."</managingEditor>
				<webMaster>".htmlspecialchars ($this->webMaster,ENT_QUOTES, $charset)."</webMaster>
				<ttl>".$this->ttl_rss_flux."</ttl>";
		if ($this->img_url_rss_flux) {
			$this->envoi.="
				<image>
					<url>".htmlspecialchars ($this->imageUrl,ENT_QUOTES, $charset)."</url>
					<title>".htmlspecialchars ($this->imageTitle,ENT_QUOTES, $charset)."</title>
					<link>".htmlspecialchars ($this->imageLink,ENT_QUOTES, $charset)."</link>
				</image>" ;
		}
		if (TYPE_EXTERNAL == $type_search) {
		    $this->envoi.=$this->aff_external_notices_list($this->notices_list);
		}else{
		    $this->envoi.=$this->aff_notices_list($this->notices_list);
		}
	    $this->envoi.="</channel></rss>";
		
		if($charset=='utf-8') {
			$this->envoi = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
					'|[\x00-\x7F][\x80-\xBF]+'.
					'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
					'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
					'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
					'', $this->envoi );
		} else {
			$this->envoi = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]/',
					'', $this->envoi );
		}
		return $this->envoi;
	}

	private function aff_notices_list($notices_list){
		global $charset,$opac_url_base ;
		global $opac_notice_affichage_class;
		
		$retour_aff="";
		foreach($notices_list as $notice_id){
			$req = "select notice_id, create_date, niveau_biblio from notices where notice_id = $notice_id";
			$res = pmb_mysql_query($req);
			if($r=pmb_mysql_fetch_object($res)){
				
				if($opac_notice_affichage_class != ""){
					$notice = new $opac_notice_affichage_class($r->notice_id, "", "", 1, 0, 0, 1, true);
				}else $notice = new notice_affichage($r->notice_id, "", "", 1, 0, 0, 1, true);
				$notice->visu_expl = 0 ;
				$notice->visu_explnum = 0 ;
				$notice->do_header_without_html();
				
				$retour_aff .= "<item>
					<title>".htmlspecialchars ($notice->notice_header_without_html,ENT_QUOTES, $charset)."</title>
					<pubDate>".htmlspecialchars ($r->create_date,ENT_QUOTES, $charset)."</pubDate>
					<link>".htmlspecialchars ($opac_url_base."index.php?lvl=notice_display&id=".$r->notice_id,ENT_QUOTES, $charset)."</link>" ;
				
				switch ($this->rssRecordsFormat) {
					case 'H': //Template de notices
						$image = '';
						if($this->idTpl){
						    $tpl = notice_tpl_gen::get_instance($this->idTpl);
							$desc = $tpl->build_notice($r->notice_id);
						}
						break;
					case '9': //Templates Django de notices
						break;
					case '1':
					default:
						$image = $this->do_image($notice->notice->code,$notice->notice->thumbnail_url,$notice->notice->tit1) ;
						$notice->do_isbd(0,0);
						$desc=$notice->notice_isbd;
						break;
				}
				$desc_explnum=$this->do_explnum($notice->notice_id, $r->niveau_biblio);
				$desc = str_replace("<br />","<br/>",$desc);
				$retour_aff .= "	<description>".htmlspecialchars(strip_tags($image.$desc,"<table><tr><td><br/><img>"),ENT_QUOTES, $charset)."</description>";
				$retour_aff .= $desc_explnum;
				$retour_aff .= "</item>" ;
			}				
		}		
		return $retour_aff;
	}
	
	private function aff_external_notices_list($notices_list){
		global $charset;
		
		$retour_aff="";
		foreach($notices_list as $notice_id){
		    
			$req = "select source_id from external_count where rid = $notice_id";
			$res = @pmb_mysql_query($req);
			
			if(0 < pmb_mysql_num_rows($res)){
			    
			    $notice = new notice_affichage_unimarc($notice_id, []);
				$notice->do_isbd(0,0);
				
				$notice->do_header();
				
				$retour_aff .= "<item>
					<title>".htmlspecialchars (strip_tags($notice->notice_header),ENT_QUOTES, $charset)."</title>
					<pubDate>".htmlspecialchars ($notice->year,ENT_QUOTES, $charset)."</pubDate>
					<link>".htmlspecialchars ($notice->notice->lien,ENT_QUOTES, $charset)."</link>" ;
				
				$image = $this->do_image($notice->notice->code,'',$notice->notice->tit1) ;
				$desc=$notice->notice_isbd;
				
				$desc = str_replace("<br />","<br/>",$desc);
				$retour_aff .= "	<description>".htmlspecialchars(strip_tags($image.$desc,"<table><tr><td><br/><img>"),ENT_QUOTES, $charset)."</description>";
				$retour_aff .= "</item>" ;
			}				
		}		
		return $retour_aff;
	}
	
	private function sort_external_notices_list($notices_list){
		$notices_tab = array();
		$i = 0;
		foreach($notices_list as $notice_id){
		    
			$req = "select source_id from external_count where rid = $notice_id";
			$res = @pmb_mysql_query($req);
			
			if($r=pmb_mysql_fetch_object($res)){
			    $query = "SELECT date_import FROM entrepot_source_$r->source_id WHERE recid = $notice_id";
			    $result = pmb_mysql_query($query);
			    $resp = pmb_mysql_fetch_object($result);
			    $notices_tab[$i] = [
    			    "id" => $notice_id,
			        "date_import" => $resp->date_import,
			    ];
			    $i++;
			}				
		}		
		//On tri
		usort($notices_tab, array("records_flux", "sort_external_records"));
        
        //On r�ajoute au tableau les ids tri�s
		$notices_list = array();
		foreach($notices_tab as $not){
            $notices_list[] = $not['id'];
		}
		return ($notices_list);
	}
	
	private static function sort_external_records($a,$b){
	    if ($a['date_import'] == $b['date_import']) {
	        return 0;
	    }
	    return ($a['date_import'] < $b['date_import']) ? 1 : -1;
	}	

	public function set_limit($limit){
		$limit += 0;
		$this->limit = $limit;
	}
	
	private function do_image($code,$vigurl="",$tit1="") {
		global $charset;
		global $opac_show_book_pics ;
		global $opac_book_pics_url ;
		global $opac_book_pics_msg ;
		global $msg;
		$image = "";
		if ($code<>"" || $vigurl<>"") {
			if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $vigurl)) {
				$url_image_ok=getimage_url($code, $vigurl);
				$title_image_ok = "";
				if(!$vigurl) {
					$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
				}
				if(!trim($title_image_ok)){
					$title_image_ok = htmlentities($tit1, ENT_QUOTES, $charset);
				}
				$image = "<img src='".$url_image_ok."' title=\"".$title_image_ok."\" class='align_right' hspace='4' vspace='2'  alt='".$msg["opac_notice_vignette_alt"]."'/>";
			}
		}
		return $image ;
	}
	
	
	// fonction retournant les infos d'exemplaires numeriques pour une notice
	private function do_explnum($no_notice, $niveau_biblio="m") {
		global $charset;
		global $opac_url_base ;
	
		if (!$no_notice) return "";
		if (!$charset) $charset='ISO-8859-1';
	
		create_tableau_mimetype() ;
	
		// recuperation du nombre d'exemplaires
		$requete = "SELECT explnum_id, explnum_notice, explnum_nom, explnum_mimetype, explnum_url, length(explnum_data) as taille ";
		$requete .= "FROM explnum JOIN explnum_statut on explnum_statut.id_explnum_statut = explnum.explnum_docnum_statut ";
		if($niveau_biblio != "b"){
			$requete .= "JOIN notices ON explnum.explnum_notice=notice_id AND explnum.explnum_bulletin=0 ";
			$requete .= "JOIN notice_statut ON notices.statut=notice_statut.id_notice_statut ";
			$requete .= "WHERE explnum.explnum_notice='".$no_notice."' ";
		}else{//Pour les notices de bulletin
			$requete .= "JOIN bulletins ON explnum.explnum_bulletin=bulletins.bulletin_id AND explnum.explnum_notice=0 ";
			$requete .= "JOIN notices ON bulletins.num_notice=notices.notice_id ";
			$requete .= "JOIN notice_statut ON notices.statut=notice_statut.id_notice_statut ";
			$requete .= "WHERE bulletins.num_notice='".$no_notice."' ";
		}
		$requete .= "AND explnum_statut.explnum_visible_opac=1 and explnum_statut.explnum_visible_opac_abon=0 ";
		$requete .= "AND notice_statut.explnum_visible_opac=1 and notice_statut.explnum_visible_opac_abon=0 ";
		$requete .= "ORDER BY explnum_mimetype, explnum_id";
		$res = pmb_mysql_query($requete);
	
		$retour = "";
		while (($expl = pmb_mysql_fetch_object($res))) {
			$url=htmlspecialchars ($opac_url_base."doc_num.php?explnum_id=".$expl->explnum_id,ENT_QUOTES, $charset) ;
			$mime=htmlspecialchars ($expl->explnum_mimetype,ENT_QUOTES, $charset) ;
			$retour .= "<enclosure url=\"".$url."\" type=\"".$mime."\" length=\"".$expl->taille."\" />";
		}
		return $retour;
	}
} # fin de definition