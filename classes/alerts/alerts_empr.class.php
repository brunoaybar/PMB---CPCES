<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_empr.class.php,v 1.1 2020/12/24 11:01:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_empr extends alerts {
	
	protected function get_module() {
		return 'edit';
	}
	
	protected function get_section() {
		return 'fins_abonnements';
	}
	
	protected function fetch_data() {
		global $pmb_relance_adhesion, $deflt2docs_location,$pmb_lecteurs_localises;
		global $empr_statut_adhes_depassee;
		
		$this->data = array();
		if($pmb_lecteurs_localises){
			$condion_loc=" AND empr_location='".$deflt2docs_location."' ";
		}else{
			$condion_loc="";
		}
		// comptage des emprunteurs proche d'expiration d'abonnement
		$query = " SELECT 1 FROM empr where ((to_days(empr_date_expiration) - to_days(now()) ) <=  $pmb_relance_adhesion ) and empr_date_expiration >= now()  ".$condion_loc." limit 1";
		if($this->is_num_rows_from_query($query)) {
			$this->add_data('empr', 'empr_expir_pro', 'limite');
		}
		
		if (!$empr_statut_adhes_depassee) $empr_statut_adhes_depassee=2;
		
		// comptage des emprunteurs expiration d'abonnement
		$query = "SELECT 1 FROM empr where empr_statut!=$empr_statut_adhes_depassee and empr_date_expiration < now() ".$condion_loc."  limit 1";
		if($this->is_num_rows_from_query($query)) {
			$this->add_data('empr', 'empr_expir_att', 'depasse');
		}
	}
	
}