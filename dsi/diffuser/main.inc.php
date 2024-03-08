<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.10 2021/04/21 18:40:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $suite, $id_bannette, $liste_bannette, $num_notice;
global $msg, $charset, $base_path, $categ, $sub;

if(!isset($liste_bannette)) $liste_bannette = array();
$id_bannette = intval($id_bannette);
$num_notice = intval($num_notice);

require_once($class_path."/dsi/bannettes_diffusion_controller.class.php") ;

// en visualisation, possibilit� de supprimer des notices � la demande
if ($suite=="suppr_notice") {
	$bannette = new bannette($id_bannette) ;
	$bannette->suppr_notice($num_notice);
	// on r�affiche la bannette de laquelle on a supprim� une notice
	$liste_bannette[] = $id_bannette ;
	$suite = "visualiser";
}

switch($suite) {
	case 'visualiser':
		print "<div class='row dsi_dif_visualiser_buttons'><input type='button' class='bouton' value='".htmlentities($msg["654"], ENT_QUOTES, $charset)."' onclick=\"document.location='".$base_path."/dsi.php?categ=".$categ."&sub=".$sub."'\" />" ;
		break;
}
bannettes_diffusion_controller::proceed();
