<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thesaurus.tpl.php,v 1.3.8.1 2022/05/05 08:04:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour la gestion des thesaurus

global $msg, $thes_browser, $thes_js_form, $thes_content_form;

// $thes_browser : template du browser de thesaurus
$thes_browser = "
<div class='row'>
	<h3>&nbsp;".$msg['thes_liste']."</h3>
</div>
<br />
<br />
<div class='row'>
	<table border='0'>
			!!browser_content!!
	</table>
</div>
<br />
<br />
<div class='row'>
	<input class='bouton' type='button' value='".$msg['thes_ajouter']."' onclick = \"document.location = '!!action!!' \" />
</div>

";


// $thes_js_form : template JS du form de thesaurus
$thes_js_form = "
<script type='text/javascript'>
<!--
function confirm_delete() {
		has_categ='!!thesaurus_as_categ!!';
        result = confirm(\"".$msg['confirm_suppr']."\");
        if(result){
        	if(has_categ == 'oui'){
        		if(confirm(\"".$msg['supp_thes_avec_categ']."\")){
        			return true;
        		}else{
        			return false;
        		}
        	}else{
        		return true;
        	}
        }else {
        	return false;
        }
    }
-->
</script>
";

// $thes_content_form : template du form de thesaurus
$thes_content_form = "
<!-- identifiant thesaurus -->
!!identifiant_thesaurus!!

<!-- libelle thesaurus -->
<div class='row'>
	<label class='etiquette' >".$msg[103]."</label><label class='etiquette'></label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='libelle_thesaurus' name='libelle_thesaurus' data-translation-fieldname='libelle_thesaurus' value=\"!!libelle_thesaurus!!\" />
</div>

<!-- langue defaut -->
<div class='row'>
	<label class='etiquette' >".$msg['thes_langue_defaut']."</label><label class='etiquette'></label>
</div>
<div class='row'>
	!!langue_defaut!!
</div>";
