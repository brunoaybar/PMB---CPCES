<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.inc.php,v 1.38.2.1 2022/06/09 13:58:53 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
global $class_path;

// éléments pour la fiche lecteur

$empr_header = "
<div id='categories'>
<h3>$msg[empr_tpl_emprheader]</h3>	
";

$empr_footer ="
</div>";

$message_null_resa=$msg["empr_resa_empty"];
if ($opac_resa) {
	$message_null_resa .= "<br /><br /><small>".$msg["empr_resa_how_to"]."</small><br />
	<form style='margin-bottom:0px;padding-bottom:0px;' action='empr.php' method='post' name='FormName'>
	<INPUT type='button' class='bouton' 'name='lvlx' value='".$msg["empr_make_resa"]."' onClick=\"document.location='./index.php?search_type_asked=simple_search'\">
	</form>";
	if (!$opac_resa_dispo) $message_null_resa .= "<br /><small>".$msg["empr_resa_only_loaned_book"]."</small>";
}

// recherche des valeurs dans la table empr suivant id_empr
$query = "SELECT *, date_format(empr_date_adhesion, '".$msg["format_date_sql"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date_sql"]."') as aff_empr_date_expiration, date_format(date_fin_blocage, '".$msg["format_date_sql"]."') as aff_date_fin_blocage FROM empr WHERE empr_login='$login'";
$result = pmb_mysql_query($query) or die("Query failed ".$query);

// récupération des valeurs MySQL du lecteur et injection dans les variables
while (($line = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC))) {
	$id_empr=$line["id_empr"];
	$empr_cb = $line["empr_cb"];
	$empr_nom = $line["empr_nom"];
	$empr_prenom = $line["empr_prenom"];
	$empr_adr1 = $line["empr_adr1"];
	$empr_adr2 = $line["empr_adr2"];
	$empr_cp = $line["empr_cp"];
	$empr_ville = $line["empr_ville"];
	$empr_mail = $line["empr_mail"];
	$empr_tel1 = $line["empr_tel1"];
	$empr_tel2 = $line["empr_tel2"];
	$empr_prof = $line["empr_prof"];
	$empr_year = $line["empr_year"];
	$empr_categ = $line["empr_categ"];
	$empr_codestat = $line["empr_codestat"];
	$empr_sexe = $line["empr_sexe"];
	$empr_login = $line["empr_login"];
	$empr_password = $line["empr_password"];
	$empr_date_adhesion = $line["empr_date_adhesion"];
	$empr_date_expiration = $line["empr_date_expiration"];
	$aff_empr_date_adhesion = $line["aff_empr_date_adhesion"];
	$aff_empr_date_expiration = $line["aff_empr_date_expiration"];
	$date_fin_blocage = $line["date_fin_blocage"];
	$aff_date_fin_blocage = $line["aff_date_fin_blocage"];
	$empr_statut = $line["empr_statut"];
}
	
$empr_identite = "
<div id='fiche-empr'><h3><span>$empr_prenom $empr_nom</span></h3>
	<div id='fiche-empr-container'>
		<table class='fiche-lecteur'>";

$i=0;
$tab_empr_info=array();
$tab_empr_info[$i]["titre"]=$msg["empr_tpl_cb"];
$tab_empr_info[$i]["class"]="tab_empr_info_cb";
$tab_empr_info[$i++]["val"]=$empr_cb;

if($empr_adr1 || $empr_adr2 || $empr_cp || $empr_ville) {
	if($empr_adr1 && $empr_adr2) 	$empr_adr = $empr_adr1.$msg["empr_adr_separateur"].$empr_adr2;
	else $empr_adr = $empr_adr1.$empr_adr2;
	
	if($empr_adr &&($empr_cp || $empr_ville)) $empr_adr.=$msg["empr_ville_separateur"];
	$empr_adr.="$empr_cp <u>$empr_ville</u>";
	
	$tab_empr_info[$i]["titre"]=$msg["empr_adresse"];
	$tab_empr_info[$i]["class"]="tab_empr_info_adr";
	$tab_empr_info[$i++]["val"]=$empr_adr;
}	
if($empr_tel1 || $empr_tel2){
	if($empr_tel1 && $empr_tel2) $tel=$empr_tel1.$msg["empr_tel2_separateur"].$empr_tel2;
	else $tel.=$empr_tel1.$empr_tel2;
	$tab_empr_info[$i]["titre"]=$msg["empr_tel_titre"];
	$tab_empr_info[$i]["class"]="tab_empr_info_tel";
	$tab_empr_info[$i++]["val"]=$tel;	
}
if($empr_mail){
    $empr_mail_aff = str_replace(";", "<br>", $empr_mail);
	$tab_empr_info[$i]["titre"]=$msg["empr_mail"];
	$tab_empr_info[$i]["class"]="tab_empr_info_mail";
	$tab_empr_info[$i++]["val"]="<a href='mailto:$empr_mail'>$empr_mail_aff</a>";	
}
if ($empr_prof){
	$tab_empr_info[$i]["titre"]=$msg["empr_tpl_prof"];
	$tab_empr_info[$i]["class"]="tab_empr_info_prof";
	$tab_empr_info[$i++]["val"]=$empr_prof;	
}
if ($empr_year){
	$tab_empr_info[$i]["titre"]=$msg["empr_tpl_year"];
	$tab_empr_info[$i]["class"]="tab_empr_info_year";
	$tab_empr_info[$i++]["val"]=$empr_year;
}

//Paramètres perso
require_once("$class_path/parametres_perso.class.php");
$p_perso=new parametres_perso("empr");
$perso_=$p_perso->show_fields($id_empr);
if (count($perso_["FIELDS"])) {
	for ($ipp=0; $ipp<count($perso_["FIELDS"]); $ipp++) {
		$p=$perso_["FIELDS"][$ipp];
		if(($p['OPAC_SHOW']==1) && $p["AFF"] !== ''){
			$tab_empr_info[$i]["titre"]=$p["TITRE_CLEAN"];
			$tab_empr_info[$i]["class"]="tab_empr_info_".$p["NAME"];
			$tab_empr_info[$i++]["val"]=$p["AFF"];
		}		
	}
}

$adhesion=str_replace("!!date_adhesion!!","<strong>".$aff_empr_date_adhesion."</strong>",$msg["empr_format_adhesion"]);	
$adhesion=str_replace("!!date_expiration!!","<strong>".$aff_empr_date_expiration."</strong>",$adhesion);	
$tab_empr_info[$i]["titre"]=$msg["empr_tpl_adh"];
$tab_empr_info[$i]["class"]="tab_empr_info_adh";
$tab_empr_info[$i++]["val"]=$adhesion;

if ($date_fin_blocage != "0000-00-00"){
	$date_blocage=array();
	$date_blocage=explode("-",$date_fin_blocage);
	if (mktime(0,0,0,$date_blocage[1],$date_blocage[2],$date_blocage[0])>time()) {
		$tab_empr_info[$i]["titre"]=$msg["empr_tpl_date_fin_blocage"];
		$tab_empr_info[$i]["class"]="tab_empr_info_blocage";
		$blocage=str_replace("!!date_fin_blocage!!","<strong>".$aff_date_fin_blocage."</strong>",$msg["empr_tpl_blocage_pret"]);
		$tab_empr_info[$i++]["val"]=$blocage;
	}
}

//Message(s) de groupe(s)
$query = "SELECT distinct id_groupe, libelle_groupe, comment_opac 
    FROM groupe 
    JOIN empr_groupe ON empr_groupe.groupe_id = groupe.id_groupe
    JOIN empr ON empr.id_empr = empr_groupe.empr_id
    WHERE empr_login='".addslashes($login)."' AND groupe.comment_opac <> '' 
    ORDER BY libelle_groupe";
$result = pmb_mysql_query($query);
if (pmb_mysql_num_rows($result)) {
    $comments = array();
    while ($row = pmb_mysql_fetch_object($result)) {
        if($row->comment_opac) {
            $comments[] = $row->libelle_groupe." : ".$row->comment_opac;
        }
    }
    if(count($comments)) {
    	$tab_empr_info[$i]["titre"]=$msg["empr_groups_comments"];
    	$tab_empr_info[$i]["class"]="tab_empr_groups_comments";
    	$tab_empr_info[$i++]["val"]=implode('<br />', $comments);
    }
}


require_once($class_path.'/event/events/event_empr.class.php');
$event = new event_empr('empr', 'get_additionnal_informations');
$evth = events_handler::get_instance();
$event->set_empr_cb($empr_cb);
$evth->send($event);
$additionnal_informations = $event->get_additionnal_informations();
if ($additionnal_informations){
    foreach ($additionnal_informations as $info){
        $tab_empr_info[$i]["titre"] = $info['titre'];
        $tab_empr_info[$i]["class"] = $info['class'];
        $tab_empr_info[$i++]["val"] = $info['val'];
    }
}

foreach ($tab_empr_info as $ligne){
	$empr_identite.=
	"<tr class='".$ligne["class"]."'>
		<td class='bg-grey align_right'><span class='etiq_champ'>".$ligne["titre"]."</span></td>	
		<td>".$ligne["val"]."</td>
	</tr>";
}

$empr_identite .= "
		</table>
	<br />
	</div>
</div>
";

