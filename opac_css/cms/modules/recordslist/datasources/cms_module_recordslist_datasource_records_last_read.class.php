<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_last_read.class.php,v 1.2.12.1 2022/03/10 08:26:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_last_read extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
	}
	/*
	 * On d�fini les s�lecteurs utilisable pour cette source de donn�e
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_last_read"
		);
	}

	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			$records = array();
			if (is_array($value) && count($value)) {
				for ($i=0; $i<count($value); $i++) {
					$value[$i] = intval($value[$i]);
					$query = "select notice_id from notices where notice_id='".$value[$i]."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result) > 0){
						$row = pmb_mysql_fetch_object($result);
						$records[] = $row->notice_id;
					}
				}
				$records = array_reverse($records);
				$return['records'] = $this->filter_datas("notices",$records);
				if($this->parameters['nb_max_elements'] > 0){
					$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
				}
			}
			return $return;
		}
		return false;
	}
}