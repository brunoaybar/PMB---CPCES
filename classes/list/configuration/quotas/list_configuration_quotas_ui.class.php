<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_quotas_ui.class.php,v 1.1.2.2 2022/05/23 12:01:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_quotas_ui extends list_configuration_ui {
	
	protected static $quota_instance;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		global $sub;
		
		static::$module = 'admin';
		static::$categ = 'quotas';
		static::$sub = $sub;
		static::_init_quota_data();
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected static function _init_quota_data() {
		static::$quota_instance = quota::get_instance(static::$sub);
	}
	
	protected function get_button_add() {
		return '';
	}
}