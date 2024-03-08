<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.10.30.1 2022/04/01 13:02:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;
switch ($sub) {
	case 'groups':
		require_once("./admin/users/users_groups.inc.php");
		break;
	case 'users' :
	default:
		require_once("./admin/users/users.inc.php");
		break;
}
?>