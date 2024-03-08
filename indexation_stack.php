<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_stack.php,v 1.2.8.1 2022/06/27 13:57:59 tsamson Exp $

$base_path=".";
$base_noheader = 1;
$base_nocheck = 1;
$base_nobody = 1;
$base_nosession =1;

ignore_user_abort(true);
require_once($base_path."/includes/init.inc.php");
require_once($class_path."/indexation_stack.class.php");

global $token;
indexation_stack::launch_indexation($token);