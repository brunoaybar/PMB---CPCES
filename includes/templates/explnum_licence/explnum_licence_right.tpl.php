<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_right.tpl.php,v 1.4.8.1 2022/03/31 14:24:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_explnum_licence_right_content_form, $msg, $charset;

$admin_explnum_licence_right_content_form = "
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='explnum_licence_right_type'>".$msg["explnum_licence_right_type"]."</label>
	</div>
	<div class='row'>
		<input type='radio' group='right_type' value='1' !!explnum_licence_right_type_1!! name='explnum_licence_right_type' id='explnum_licence_right_type_1'>
		<label class='etiquette' for='explnum_licence_right_type_1'>".$msg["explnum_licence_right_quotation_right_authorisation"]."</label>
		<input type='radio' group='right_type' value='0' !!explnum_licence_right_type_0!! name='explnum_licence_right_type' id='explnum_licence_right_type_0'>
		<label class='etiquette' for='explnum_licence_right_type_0'>".$msg["explnum_licence_right_quotation_right_prohibition"]."</label>
	</div>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_right_label'>".htmlentities($msg["docnum_statut_libelle"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_right_label' name='explnum_licence_right_label' value='!!explnum_licence_right_label!!' data-translation-fieldname='explnum_licence_right_label'>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_right_logo_url'>".htmlentities($msg["explnum_licence_logo_url"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_right_logo_url' name='explnum_licence_right_logo_url' value='!!explnum_licence_right_logo_url!!' data-translation-fieldname='explnum_licence_right_logo_url'>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_right_explanation'>".htmlentities($msg["explnum_licence_explanation"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<textarea class='saisie-50em' id='explnum_licence_right_explanation' name='explnum_licence_right_explanation' data-translation-fieldname='explnum_licence_right_explanation'>!!explnum_licence_right_explanation!!</textarea>
</div>";