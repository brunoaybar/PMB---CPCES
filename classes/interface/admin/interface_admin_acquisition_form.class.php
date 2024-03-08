<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_acquisition_form.class.php,v 1.1.2.3 2022/04/25 15:06:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_acquisition_form extends interface_admin_form {
	
	protected $id_entity;
	
	protected $statut;
	
	protected $confirm_cloture_msg;
	
	protected function get_action_delete_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'compta':
				return $msg['supprimer'];
			case 'pricing_systems':
				return $msg['pricing_system_delete'];
			default:
				return parent::get_action_delete_label();
		}
	}
	
	protected function get_action_duplicate_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'pricing_systems':
				return $msg['pricing_system_duplicate'];
			default:
				return parent::get_action_duplicate_label();
		}
		return $msg["duplicate"];
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&id_entity=".$this->id_entity."&action=save&id=".$this->object_id;
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&id_entity=".$this->id_entity."&action=duplicate&id=".$this->object_id;
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&id_entity=".$this->id_entity."&action=delete&id=".$this->object_id;
	}
	
	protected function get_cancel_action() {
		global $sub;
		
		switch ($sub) {
			case 'compta':
				return $this->get_url_base()."&action=list&id_entity=".$this->id_entity;
			default:
				return $this->get_url_base()."&id_entity=".$this->id_entity;
		}
	}
	
	protected function get_cloture_action() {
		return $this->get_url_base()."&id_entity=".$this->id_entity."&action=cloture&id=".$this->object_id;
	}
	
	protected function get_action_cloture_label() {
		global $msg;
		return $msg['acquisition_compta_clot'];
	}
	
	protected function get_display_cloture_action() {
		global $charset;
		
		if($this->statut != STA_EXE_CLO) {
			return "<input type='button' class='bouton' name='cloture_button' id='cloture_button' value='".htmlentities($this->get_action_cloture_label(), ENT_QUOTES, $charset)."' onclick=\"if(confirm('".htmlentities(addslashes($this->confirm_cloture_msg), ENT_QUOTES, $charset)."')){document.location='".$this->get_cloture_action()."';}\" />";
		}
	}
	
	protected function get_display_actions() {
		global $sub;
		
		switch ($sub) {
			case 'compta':
				$display = "
					<div class='left'>
						".$this->get_display_cancel_action()."
						".$this->get_display_submit_action()."
						".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
						".($this->object_id && !empty($this->actions_extension) ? $this->get_display_actions_extension() : "")."
					</div>
					<div class='right'>
						".($this->object_id ? $this->get_display_cloture_action() : "")."
						".($this->object_id ? $this->get_display_delete_action() : "")."
					</div>";
				break;
			default:
				$display = parent::get_display_actions();
		}
		return $display;
	}
	
	public function set_id_entity($id_entity) {
		$this->id_entity = $id_entity;
		return $this;
	}
	
	public function set_statut($statut) {
		$this->statut = $statut;
		return $this;
	}
	
	public function set_confirm_cloture_msg($confirm_cloture_msg) {
		$this->confirm_cloture_msg = $confirm_cloture_msg;
		return $this;
	}
}