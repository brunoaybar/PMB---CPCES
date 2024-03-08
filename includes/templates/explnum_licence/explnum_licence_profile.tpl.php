<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_profile.tpl.php,v 1.6.8.1 2022/03/31 14:24:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_explnum_licence_profile_content_form, $charset, $msg, $admin_explnum_checkbox_template, $admin_explnum_licence_quotation_variable_selector, $admin_explnum_licence_quotation_variable_selector_option;

$admin_explnum_licence_profile_content_form = "
<div class='row'>
	<label class='etiquette' for='explnum_licence_profile_label'>".htmlentities($msg["docnum_statut_libelle"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_profile_label' name='explnum_licence_profile_label' value='!!explnum_licence_profile_label!!' data-translation-fieldname='explnum_licence_profile_label'>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_profile_uri'>".htmlentities($msg["explnum_licence_uri"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_profile_uri' name='explnum_licence_profile_uri' value='!!explnum_licence_profile_uri!!' data-translation-fieldname='explnum_licence_profile_uri'>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_profile_logo_url'>".htmlentities($msg["explnum_licence_logo_url"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_profile_logo_url' name='explnum_licence_profile_logo_url' value='!!explnum_licence_profile_logo_url!!' data-translation-fieldname='explnum_licence_profile_logo_url'>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_profile_explanation'>".htmlentities($msg["explnum_licence_explanation"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<textarea class='saisie-50em' id='explnum_licence_profile_explanation' name='explnum_licence_profile_explanation' data-translation-fieldname='explnum_licence_profile_explanation'>!!explnum_licence_profile_explanation!!</textarea>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_profile_quotation_rights'>".htmlentities($msg["explnum_licence_profile_quotation_rights"], ENT_QUOTES, $charset)."</label>
	!!quotation_variable_selector!!
</div>
<div class='row'>
	<textarea class='saisie-50em' id='explnum_licence_profile_quotation_rights' name='explnum_licence_profile_quotation_rights' data-translation-fieldname='explnum_licence_profile_quotation_rights'>!!explnum_licence_profile_quotation_rights!!</textarea>
</div>
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='explnum_licence_profile_linked_rights'>".$msg["explnum_licence_profile_linked_rights"]."</label>
	</div>
	<div class='row'>
		!!explnum_licence_profile_linked_rights!!
	</div>
</div>";


$admin_explnum_checkbox_template = "
		<input type='checkbox' id='explnum_licence_profile_rights_!!admin_explnum_right_id!!' value='!!admin_explnum_right_id!!' !!admin_explnum_right_checked!! name='explnum_licence_profile_rights[]'/>
		<label for='explnum_licence_profile_rights_!!admin_explnum_right_id!!'>!!admin_explnum_right_label!!</label>
		";

$admin_explnum_licence_quotation_variable_selector = "
<select id='explnum_licence_quotation_variable_selector'>
	<option value=''>".$msg['dsi_docwatch_datasource_link_constructor_page_var']."</option>
	!!variable_selector_options!!
</select>
<script type='text/javascript'>
	function explnum_licence_add_variable_in_quotation() {
		var value = '{{ ' + this.selectedOptions[0].value + ' }}';
		var template_area = document.getElementById('explnum_licence_profile_quotation_rights');
		var curpos = template_area.selectionStart;
		var before = template_area.value.substr(0, curpos);
		var after = template_area.value.substr(curpos);
		template_area.value = before + value + after;
		template_area.focus();
		template_area.setSelectionRange(curpos + value.length, curpos + value.length);
		this.value = '';
	}
	document.getElementById('explnum_licence_quotation_variable_selector').addEventListener('change', explnum_licence_add_variable_in_quotation);
</script>";

$admin_explnum_licence_quotation_variable_selector_option = "
	<option value='!!option_value!!'>!!option_label!!</option>";