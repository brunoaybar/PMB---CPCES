<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: attachments.inc.php,v 1.1.4.1 2022/06/17 15:09:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action;
global $from, $filename;
require_once($class_path."/files_gestion.class.php");

$files_gestion = new files_gestion('attachments');

switch($action) {
	case 'upload':
		$files_gestion->upload($from);
	break;	
	case 'delete':
		$files_gestion->delete(urldecode(stripslashes($filename)));
	break;
	default:
	break;
}

$mailtpl_attachments_ui = list_configuration_mailtpl_attachments_ui::get_instance();
print $mailtpl_attachments_ui->get_display_list();
if(!empty($files_gestion->get_error())) {
	print $files_gestion->get_error();
} else {
	print $mailtpl_attachments_ui->get_files_error();
}
