<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: global_vars.inc.php,v 1.12 2021/03/12 10:08:04 arenou Exp $

// fichier de configuration g�n�rale

// prevents direct script access
pt_register ("SERVER", "REQUEST_URI");
if(preg_match('/global_vars\.inc\.php/', $REQUEST_URI)) {
	require_once('./forbidden.inc.php'); forbidden();
}

/* VERSION SUPER GLOBALS */
/* on commence par tout unset... */
//$arr = array_merge(&$_ENV, &$_GET, &$_POST, &$_COOKIE,  &$_FILES, &$_REQUEST, &$_SERVER);
//while(list($__key__PMB) = each($arr)) unset(${$__key__PMB});                 
//$arr = array_merge(&$HTTP_GET_VARS, &$HTTP_POST_VARS,&$HTTP_POST_FILES,&$HTTP_COOKIE_VARS, &$HTTP_SERVER_VARS, &$HTTP_ENV_VARS );
//while(list($__key__PMB) = each($arr)) unset(${$__key__PMB});                 

$forbidden_overload = [
    'base_path',
    'include_path',
    'class_path',
    'overload_global_parameters'
];

function add_sl(&$var) {
	if (is_array($var)) {
		reset($var);
		foreach ($var as $k => $v) {
			add_sl($var[$k]);
		}
	} else {
		$var=addslashes($var);
	}
}

/* on r�cup�re tout sans se poser de question, attention � la s�curit� ! */
foreach ($_GET as $__key__PMB => $val) {
    if (!in_array($__key__PMB,$forbidden_overload)) {
		add_sl($val);
		$GLOBALS[$__key__PMB] = $val;
	}
}
foreach ($_POST as $__key__PMB => $val) {
    if (!in_array($__key__PMB,$forbidden_overload)) {
		add_sl($val);
		$GLOBALS[$__key__PMB] = $val;
	}
}

//Post de fichiers
foreach ($_FILES as $__key__PMB => $val) {
    if (!in_array($__key__PMB,$forbidden_overload)) {
		add_sl($val);
		$GLOBALS[$__key__PMB] = $val;
	}
}

// Inutile
//while (list($key, $val) = @each($_REQUEST)) {
//	$GLOBALS[$key] = $val;
//	}

// R�cup�r�es par pt_register
//while (list($key, $val) = @each($_SERVER)) {
//	$GLOBALS[$key] = $val;
//	}
	
function pt_register() {
	$num_args = func_num_args();
	$vars = array();
	if ($num_args >= 2) {
		$method = strtoupper(func_get_arg(0));
		
		if (	($method != 'SESSION') && 
			($method != 'GET') && 
			($method != 'POST') && 
			($method != 'SERVER') && 
			($method != 'COOKIE') && 
			($method != 'FILES') && 
			($method != 'REQUEST') && 
			($method != 'ENV')) {
			die('The first argument of pt_register must be one of the following: 
				SESSION, GET, POST, SERVER, COOKIE, FILES, REQUEST or ENV');
			}
		
		$varname = "_{$method}";
		global ${$varname};
		
		for ($i = 1; $i < $num_args; $i++) {
			$parameter = func_get_arg($i);
			if (isset(${$varname}[$parameter])) {
		        	global ${$parameter};
				${$parameter} = ${$varname}[$parameter];
				}
			}
		
		} else {
	    		die('You must specify at least two arguments');
			}
	
	} /* fin pt_register() */

/* quand register_globals sera � off il faudra r�cup�rer en automatique
	le strict minum : */
pt_register ("COOKIE", "PhpMyBibli-SESSID","PhpMyBibli-LOGIN","PhpMyBibli-SESSNAME","PhpMyBibli-LOGIN","PhpMyBibli-LANG");
pt_register ("SERVER", "REMOTE_ADDR","HTTP_USER_AGENT", "PHP_SELF", "REQUEST_URI", "SCRIPT_NAME");


