<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inhtml.inc.php,v 1.2.10.1 2022/03/17 14:02:23 dgoron Exp $

global $include_path, $func_format;

require_once ($include_path . "/misc.inc.php");

if(empty($func_format)) {
	$func_format= array();
}
$func_format['if_logged']= 'aff_if_logged';

function aff_if_logged($param) {
	if ($_SESSION['id_empr_session']) {
		$ret = $param[1];
	}else {
		$ret = $param[2];
	}
	return $ret;
}
?>