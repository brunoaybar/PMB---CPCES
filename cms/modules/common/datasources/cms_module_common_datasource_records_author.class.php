<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_author.class.php,v 1.7.12.1 2022/02/24 14:58:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_author extends cms_module_common_datasource_records_list{

	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_principal_author"
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
			$value['author'] = intval($value['author']);
			$value['record'] = intval($value['record']);
			if($value['author'] != 0){
				$query = "select distinct responsability_notice from responsability where responsability_author = '".$value['author']."' and responsability_notice != '".$value['record']."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->responsability_notice;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}

			if(!count($return['records'])) return false;
			
			$return = $this->sort_records($return['records']);
			$return["title"] = "Du même auteur";
			return $return;
		}
		return false;
	}
}