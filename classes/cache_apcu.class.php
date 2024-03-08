<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cache_apcu.class.php,v 1.3.2.2 2022/02/17 09:34:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cache_apcu extends cache_factory {

	public function setInCache($key, $value) {
		global $CACHE_MAXTIME;
		
		return apcu_store($key, $value, $CACHE_MAXTIME);
	}

	public function getFromCache($key) {
		return apcu_fetch($key);
	}

	public function clearCache() {
	    return apcu_clear_cache();
	}
	
	public function deleteCache() {
		global $KEY_CACHE_FILE_XML;
		if(class_exists('APCuIterator', false) && ('cli' !== \PHP_SAPI || filter_var(ini_get('apc.enable_cli'), \FILTER_VALIDATE_BOOLEAN))) {
			return apcu_delete(new APCUIterator('#^'.$KEY_CACHE_FILE_XML.'#'));
		} else {
			return false;
		}
	}
}