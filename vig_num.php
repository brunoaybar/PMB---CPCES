<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vig_num.php,v 1.14.6.2 2022/04/12 06:46:23 tsamson Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once ("$base_path/includes/init.inc.php");
session_write_close();//Fermeture de la session dès que possible
require_once("$class_path/curl.class.php");

$explnum_id = (int) $explnum_id;
$resultat = pmb_mysql_query("SELECT explnum_id, explnum_mimetype, explnum_vignette FROM explnum WHERE explnum_id = '$explnum_id' ", $dbh);
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	exit ;
} 
global $pmb_docnum_img_folder_id;
$ligne = pmb_mysql_fetch_object($resultat);
if (!empty($pmb_docnum_img_folder_id)) {
    $query = "select repertoire_path from upload_repertoire where repertoire_id ='$pmb_docnum_img_folder_id'";
    $result = pmb_mysql_query($query);
    if(pmb_mysql_num_rows($result)){
        $row=pmb_mysql_fetch_object($result);
        $filename_output=$row->repertoire_path."img_docnum_".$ligne->explnum_id;
        if (file_exists($filename_output)) {
            print file_get_contents($filename_output);
            exit;
        }
    }
}
if ($ligne->explnum_vignette) {
	print $ligne->explnum_vignette;
	exit ;
} else {
	if ($pmb_curl_available) {
		$image_url = 'http';
		if ($_SERVER["HTTPS"] == "on") {$image_url .= "s";}
		$image_url .= "://";
		$image_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].dirname($_SERVER["SCRIPT_NAME"]).'/images/mimetype/unknown.gif';
		$aCurl = new Curl();
		$content = $aCurl->get($image_url);
		$contenu_vignette = $content->body;
	} else {
		$fp = fopen("./images/mimetype/unknown.gif" , "r" ) ;
		$contenu_vignette = fread ($fp, filesize("./images/mimetype/unknown.gif"));
		fclose ($fp) ;			
	}
	print $contenu_vignette ;
}
