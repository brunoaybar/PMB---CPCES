<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ldap_param.inc.php,v 1.11.4.1 2022/01/13 15:53:19 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $REQUEST_URI, $ldap_server, $ldap_basedn, $ldap_port, $ldap_proto, $ldap_filter, $ldap_fields, $ldap_lang, $ldap_groups;
global $ldap_accessible, $ldap_opac_only, $msg;

// param�tres d'acc�s � le serveur LDAP - by MaxMan

// prevents direct script access

if (preg_match('/ldap_param\.inc\.php/', $REQUEST_URI)) {
	include "./forbidden.inc.php";
	forbidden();
}

define('LDAP_SERVER', $ldap_server);  
define('LDAP_BASEDN', $ldap_basedn);  
define('LDAP_PORT'  , $ldap_port);    
define('LDAP_PROTO' , $ldap_proto);  
define('LDAP_FILTER', $ldap_filter);
define('LDAP_FIELDS', $ldap_fields);
define('LDAP_LANG'  , $ldap_lang);
define('LDAP_GROUPS', $ldap_groups); // groups ldap � importer

if ($ldap_accessible) {
	if (LDAP_SERVER) {
		$ldap_error = 1;
		$conn = ldap_connect(LDAP_SERVER, LDAP_PORT);  // must be a valid LDAP server!
		if (!empty($conn)) {
			$x = ldap_read($conn, LDAP_BASEDN, LDAP_FILTER);
			if (preg_match('/resource/i', (string) $x)) {
				$ldap_error = 0;
				ldap_unbind($conn);
			}
		}
		if (!empty($ldap_error) && empty($ldap_opac_only)) {
			print "<h2>".$msg["ldap_error"]."</h2>";
			$ldap_accessible = 0;
		}
	}
}