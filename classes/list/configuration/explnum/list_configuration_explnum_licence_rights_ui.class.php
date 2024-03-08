<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_explnum_licence_rights_ui.class.php,v 1.1 2021/04/16 07:59:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_explnum_licence_rights_ui extends list_configuration_explnum_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_explnum_licence_right FROM explnum_licence_rights';
	}
	
	protected function get_object_instance($row) {
		return new explnum_licence_right($row->id_explnum_licence_right);
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'licence' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => 'docnum_statut_libelle',
		); 
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label'
		);
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['licence']) {
			$filters[] = 'explnum_licence_right_explnum_licence_num = "'.$this->filters['licence'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&rightaction=edit&rightid='.$object->get_id();
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["explnum_licence_no_right_defined"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['explnum_licence_right_new'];
	}
	
	protected function get_button_add() {
		global $charset;
		
		return "<input class='bouton' type='button' value='".htmlentities($this->get_label_button_add(), ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&rightaction=edit';\" />";
	}
	
	public static function get_controller_url_base() {
		global $base_path, $id;
		$id = intval($id);
		return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub=licence&action=settings&id='.$id.'&what=rights';
	}
}