<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: enter_localisation.inc.php,v 1.24.2.2 2022/02/11 15:38:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $opac_nb_localisations_per_line;
global $opac_view_filter_class, $opac_map_activate;

require_once($class_path."/show_localisation.class.php");
require_once($class_path."/map/map_location_home_page_controler.class.php");

if (!$opac_nb_localisations_per_line) $opac_nb_localisations_per_line=6;
print "<div id=\"location\">";
print "<h3><span>".$msg["l_browse_title"]."</span></h3>";
print "<div id='location-container'>";

$requete="";
if($opac_view_filter_class){
	if(!empty($opac_view_filter_class->params["nav_sections"])) {
		$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 
		  and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]).")  order by location_libelle ";
	}
} else {
	$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 order by location_libelle ";
}
if ($requete) {
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)>1) {
		print "<table class='center' style='width:100%'>";
		$npl=0;
		$ids=array();
		$tab_locations = array();
		while ($r=pmb_mysql_fetch_object($resultat)) {
			if($opac_map_activate==1 || $opac_map_activate==3) {
	        	$ids[] = $r->idlocation;
	        	$tab_locations[$r->idlocation]=array();
	        	$tab_locations[$r->idlocation]["id"] = $r->idlocation;
	            $tab_locations[$r->idlocation]['libelle'] = $r->location_libelle;
	            $tab_locations[$r->idlocation]['code_champ'] = 90;
	            $tab_locations[$r->idlocation]['code_ss_champ'] = 4;
	            $tab_locations[$r->idlocation]['url'] = "./index.php?lvl=section_see";
	            $tab_locations[$r->idlocation]['param'] = "&location=" . $r->idlocation . ($r->css_style?"&opac_css=" . $r->css_style:""); 
	            $tab_locations[$r->idlocation]['flag_home_page'] = true;
	        } else {  
				if ($npl==0) print "<tr>";
				if ($r->location_pic) $image_src = $r->location_pic ;
					else  $image_src = get_url_icon("bibli-small.png");
				print "<td class='center'>
						<a href='./index.php?lvl=section_see&location=".$r->idlocation.($r->css_style?"&opac_css=".$r->css_style:"")."'><img src='$image_src' style='border:0px' alt='".$r->location_libelle."' title='".$r->location_libelle."'/></a>
						<br /><a href='./index.php?lvl=section_see&location=".$r->idlocation.($r->css_style?"&opac_css=".$r->css_style:"")."'><b>".$r->location_libelle."</b></a></td>";
				$npl++;
				if ($npl==$opac_nb_localisations_per_line) {
					print "</tr>";
					$npl=0;
				}
	        }
		}
	    if($opac_map_activate==1 || $opac_map_activate==3) {
	    	print '<tr><td>' . map_location_home_page_controler::get_map_location_home_page( $ids, $tab_locations, array(), array()) . '</td></tr>';     
	    }
		if ($npl!=0) {
			while ($npl<$opac_nb_localisations_per_line) {
				print "<td></td>";
				$npl++;
			}
			print "</tr>";
		}
		print "</table>";
	} else {
		if (pmb_mysql_num_rows($resultat)) {
			$location=pmb_mysql_result($resultat,0,0);
			show_localisation::set_num_location($location);
			print show_localisation::get_display_sections_list();
		}
	}
}
print "</div>";
print "</div>";
?>