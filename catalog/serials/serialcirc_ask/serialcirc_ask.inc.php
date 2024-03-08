<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.inc.php,v 1.5.2.1 2022/02/02 13:07:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $asklist_id;

require_once("$class_path/serialcirc_ask.class.php");

switch($sub){		
	case 'circ_ask':		
		switch($action){	
			case 'accept':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->accept();
				}				
			break;		
			case 'refus':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->refus();
				}				
			break;		
			case 'delete':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->delete();
				}				
			break;				
		}
		$list_serialcirc_ask_ui = new list_serialcirc_ask_ui();
		print $list_serialcirc_ask_ui->get_display_list();
	break;		
	default :
		$list_serialcirc_ask_ui = new list_serialcirc_ask_ui();
		print $list_serialcirc_ask_ui->get_display_list();
	break;		
	
}



