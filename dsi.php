<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi.php,v 1.12 2021/04/28 06:52:35 dgoron Exp $

// d�finition du minimum n�c�ssaire 
$base_path=".";
$base_auth = "DSI_AUTH";  
$base_use_dojo=1;
$base_title = "\$msg[dsi_menu_title]";    
require_once ("$base_path/includes/init.inc.php");  

// modules propres � autorites.php ou � ses sous-modules
require_once($class_path."/modules/module_dsi.class.php");
require_once($class_path.'/interface/dsi/interface_dsi_form.class.php');
require_once($class_path."/notice_tpl_gen.class.php");
require("$include_path/templates/dsi.tpl.php");

module_dsi::get_instance()->proceed_header();

include("./dsi/main.inc.php");

module_dsi::get_instance()->proceed_footer();

// deconnection MYSql
pmb_mysql_close();
