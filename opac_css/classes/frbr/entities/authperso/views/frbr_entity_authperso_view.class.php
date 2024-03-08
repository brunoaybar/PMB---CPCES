<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_view.class.php,v 1.4.2.1 2021/08/09 13:25:55 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_view extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% if authperso.info.view %}
	{{ authperso.info.view }}
{% else %}
	{{ authperso.name }} : {{ authperso.info.isbd }}
{% endif%}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos �l�ments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_authperso_view_title"];
		$render_datas['authperso'] = authorities_collection::get_authority('authority', 0, ['num_object' => $datas[0], 'type_object' => AUT_TABLE_AUTHPERSO]);
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_authperso_view_title']
		);
		$authperso = array(
			'var' => "authperso",
			'desc' => $this->msg['frbr_entity_authperso_view_label'],
			'children' => authority::get_properties(AUT_TABLE_AUTHPERSO,"authperso")
		);
		$format[] = $authperso;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}