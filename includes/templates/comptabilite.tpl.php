<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comptabilite.tpl.php,v 1.9.8.1 2022/04/25 15:06:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $exer_content_form, $msg, $charset, $ptab;

//Template du formulaire d'exercices
$exer_content_form = "
<div class='row'>
	<label class='etiquette' for='libelle'>".htmlentities($msg[103],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type='text' id='libelle' name='libelle' value=\"!!libelle!!\" class='saisie-80em' />
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette'>".htmlentities($msg['calendrier_date_debut'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		!!date_deb!!
	</div>
</div>
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette'>".htmlentities($msg['calendrier_date_fin'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		!!date_fin!!
	</div>
</div>
<br />
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette'>".htmlentities($msg['acquisition_statut'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		!!statut!!
		<!-- case_def -->
	</div>
</div>
";

$ptab[2] = "&nbsp;&nbsp;<input type='checkbox' id='def' name='def' value='1' />".htmlentities($msg['acquisition_statut_ca_def'], ENT_QUOTES, $charset);

