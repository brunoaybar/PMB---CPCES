<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_parameters.tpl.php,v 1.2.8.1 2022/03/31 14:24:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $scan_request_parameters_content_form;

$scan_request_parameters_content_form ="
<div class='row'>
	!!scan_request_parameters_folder_selector!!
</div>";
