<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_publisher.class.php,v 1.3.12.1 2022/02/18 10:36:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_publisher extends cms_module_common_datasource_records_list{

	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_publisher"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			$value['publisher'] = intval($value['publisher']);
			$value['record'] = intval($value['record']);
			if($value['publisher'] != 0){
				$query = "select notice_id from notices where ed1_id = '".$value['publisher']."' and notice_id != '".$value['record']."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notice_id;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}
			if(!count($return['records'])) return false;

			$return = $this->sort_records($return['records']);
			$return["title"] = $this->msg['cms_module_recordslist_datasource_records_publisher_title'];
			return $return;
		}
		return false;
	}
}