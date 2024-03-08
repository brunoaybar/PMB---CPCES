<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users.inc.php,v 1.9.2.2 2022/04/01 13:02:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $admin_user_javascript, $id;

require_once($class_path.'/users/users_controller.class.php');

print $admin_user_javascript;

users_controller::proceed($id);