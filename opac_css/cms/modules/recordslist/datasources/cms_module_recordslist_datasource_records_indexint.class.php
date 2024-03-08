<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_indexint.class.php,v 1.3.12.1 2022/02/18 10:36:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_indexint extends cms_module_common_datasource_records_list{

	/*
	 * On d�fini les s�lecteurs utilisable pour cette source de donn�e
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_indexint"
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
			$value['indexint'] = intval($value['indexint']);
			$value['record'] = intval($value['record']);
			if($value['indexint'] != 0){
				$query = "select notice_id from notices where indexint = '".$value['indexint']."' and notice_id != '".$value['record']."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notice_id;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}
			$return = $this->sort_records($return['records']);
			$return["title"] = $this->msg['cms_module_recordslist_datasource_records_indexint_title'];
			return $return;
		}
		return false;
	}
}