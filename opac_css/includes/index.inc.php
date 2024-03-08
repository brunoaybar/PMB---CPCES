<?php 
// +-------------------------------------------------+
//   2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index.inc.php,v 1.50 2019/05/29 09:12:29 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $search_type, $opac_show_infopages_id_top, $opac_show_search_title, $opac_show_infopages_id;
global $opac_show_public_bannettes, $opac_bannette_nb_liste;
global $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables;


// affichage recherche
global $search_type;
require_once ($base_path.'/includes/simple_search.inc.php');
require_once ($class_path.'/search_view.class.php');



if ($search_type == "simple_search" && $opac_show_infopages_id_top) {
	// affichage des infopages demand s juste AVANT le formulaire de recherche simple et si !$user_query
	require_once ($base_path.'/includes/show_infopages.inc.php');
	print "<div id='infopages_top'>".show_infopages($opac_show_infopages_id_top)."</div>";
	}

if ($opac_show_search_title) print "<div id='search_block'><h3><span>".$msg['search_block_title']."</span></h3>";
search_view::set_search_type($search_type);
search_view::set_user_query($user_query);
search_view::set_url_base($base_path.'/index.php?');
$display_search_tabs_form=search_view::get_display_search_tabs_form($user_query, $css);
$display_search_tabs_form=str_replace("!!surligne!!","",$display_search_tabs_form);
print $display_search_tabs_form;
print '</div>';
if ($opac_show_search_title) print "</div>";

if ($search_type == "simple_search") {
	// affichage des infopages demand s juste apr s le formulaire de recherche simple et si !$user_query
	/*if ($opac_show_infopages_id) {
		require_once ($base_path.'/includes/show_infopages.inc.php');
		print "<div id='infopages'>".show_infopages($opac_show_infopages_id)."</div>";
	}
	// affichage des du navigateur de p riodiques
	if ($opac_show_perio_browser) {
		require_once($base_path."/classes/perio_a2z.class.php");	
		$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);		
		print $perio_a2z=$a2z->get_form();		
	}	
	// affichage cat gories
	if ($opac_show_categ_browser) {
		$opac_show_categ_browser_tab=explode(" ",$opac_show_categ_browser);
		if (!empty($opac_show_categ_browser_tab[1])) 
			$opac_show_categ_browser_home_id_thes=$opac_show_categ_browser_tab[1];
		require_once ($base_path.'/classes/categorie.class.php');
		require_once ($base_path.'/includes/templates/categories.tpl.php');
		require_once ($base_path.'/categ/categories.inc.php');
	}*/
	// affichage des bannettes auxquelles le lecteur est abonn 
	if ($opac_show_subscribed_bannettes) {
		require_once($base_path."/includes/bannette_func.inc.php");		
		$affiche_bannette_tpl="
		<div class='bannette' id='banette_!!id_bannette!!'>
			!!diffusion!!
		</div>
		";
		$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!","bannettes_private-container_2", ""));
		if($aff){
			$bannettes= "<div id='bannettes_subscribed'>\n";
			$bannettes.= "<h3><span>".$msg['accueil_bannette_privee']."</span></h3>";
			$bannettes.= "<div id='bannettes_private-container'>";
			$bannettes.= $aff;
			$bannettes.= "</div><!-- fermeture #bannettes-container -->\n";
			$bannettes.= "</div><!-- fermeture #bannettes -->\n";
			print $bannettes;
		} 		
	}
	
	//affichage des bannettes publiques s lectionn es (et restantes si $opac_show_subscribed_bannettes est activ ) pour la page d'accueil	
 	if ($opac_show_public_bannettes) {
 		require_once($base_path."/includes/bannette_func.inc.php");
 		$affiche_bannette_tpl="
		<div class='bannette' id='banette_!!id_bannette!!'>
 			!!diffusion!!
 		</div>";
 		$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./index.php?lvl=bannette_see&id_bannette=!!id_bannette!!","bannettes-public-container_2", "",true));
 		if($aff){
 			$bannettes= "<div id='bannettes_public'>\n";
 			$bannettes.= "<h3><span>".$msg['accueil_bannette_public']."</span></h3>";
 			$bannettes.= "<div id='bannettes_public-container'>";
 			$bannettes.= $aff;
 			$bannettes.= "</div><!-- fermeture #bannettes-container -->\n";
			$bannettes.= "</div><!-- fermeture #bannettes -->\n";
 			print $bannettes;
 		}
 	}

	/*if ($opac_show_section_browser==1) {
		if ($opac_sur_location_activate==1) require_once($base_path."/includes/enter_sur_location.inc.php");
		else require_once($base_path."/includes/enter_localisation.inc.php");
	}
	*/
	// affichage marguerite des couleurs
	if ($opac_show_marguerite_browser) require_once ($base_path.'/indexint/marguerite_browser.inc.php');

	// affichage tableau des 100 cases du savoir
	if ($opac_show_100cases_browser) require_once ($base_path.'/indexint/100cases_browser.inc.php');

	// affichage derniers ouvrages saisis
	if ($opac_show_dernieresnotices) {
		print "
		<div class='wrapper'>
		<div class='card'>
			<a href='https://ebooks.errepar.com/library?gad_source=1&gclid=CjwKCAiApuCrBhAuEiwA8VJ6JiTai56xGUH7SytfpllPknIQT4gp7WAjQKRc-k3K1Abz7RLFsJ7sYBoC9iYQAvD_BwE' target='_blank'>
				<img src='images/Errepar.png' alt='Errepar Logo'>
			</a>
		</div>
	
		<div class='card'>
		<a href='https://www.thomsonreuters.com.ar/es.html' target='_blank'>
			<img src='images/thompson.jpg' alt=''>
		</a>
		</div>
	
		<div class='card'>
			<img src='images/BibliotecaCPCE_Cristian_Pablo.jpg' alt=''>
			<div class='caption'></div>
		</div>
	</div>
		";
		
		print "<div id='carrusel'>
				<style>
		#carrusel img {
			max-width: 38%; /* Establecer el ancho máximo al 100% */
			height: auto; /* Hacer que la altura se ajuste automáticamente */
		}
		</style>
		<h1>NOVEDADES BIBLIOGRÁFICAS</h1>
		<ul id=\"c\"> 
		

		<li><img src=\"images/carrusel/Compliance_Tributario.jpg\"><p>Compliance Tributario</p></li>
		<li><img src=\"images/carrusel/Convenio_multilateral-2.jpg\"><p>Convenio Multilateral</p></li>
		<li><img src=\"images/carrusel/Curso_de_Derecho_Financiero.jpg\"><p>Curso de Derecho Financiero</p></li>
		<li><img src=\"images/carrusel/Derecho_sociedad_internet.jpg\"><p>Derecho, Sociedad e Internet</p></li>

		<li><img src=\"images/carrusel/Impuestos_a_las_ganancias.jpg\"><p>Impuesto a las ganancias</p></li>
		<li><img src=\"images/carrusel/Lecturas_de_contabilidad_basica.jpg\"><p>Lecturas de Contabilidad Basica</p></li>
		<li><img src=\"images/carrusel/Norma_Unificada_Argentina.jpg\"><p>Norma Unificada Argentina</p></li>
		<li><img src=\"images/carrusel/Régimen_Simplificado_para_pequeños_contribuyentes_monotributista.jpg\"><p>Regimen Simplificado para pequeños contribuyentes monotributistas</p></li> 
	</ul>
		</div>
		<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js\"></script>
	<script type='text/javascript'>
		var timer = 4000;

var i = 0;
var max = $('#c > li').length;
 
	$(\"#c > li\").eq(i).addClass('active').css('left','0');
	$(\"#c > li\").eq(i + 1).addClass('active').css('left','25%');
	$(\"#c > li\").eq(i + 2).addClass('active').css('left','50%');
	$(\"#c > li\").eq(i + 3).addClass('active').css('left','75%');
 

	setInterval(function(){ 

		$(\"#c > li\").removeClass('active');

		$(\"#c > li\").eq(i).css('transition-delay','0.25s');
		$(\"#c > li\").eq(i + 1).css('transition-delay','0.5s');
		$(\"#c > li\").eq(i + 2).css('transition-delay','0.75s');
		$(\"#c > li\").eq(i + 3).css('transition-delay','1s');

		if (i < max-4) {
			i = i+4; 
		}

		else { 
			i = 0; 
		}  

		$(\"#c > li\").eq(i).css('left','0').addClass('active').css('transition-delay','1.25s');
		$(\"#c > li\").eq(i + 1).css('left','25%').addClass('active').css('transition-delay','1.5s');
		$(\"#c > li\").eq(i + 2).css('left','50%').addClass('active').css('transition-delay','1.75s');
		$(\"#c > li\").eq(i + 3).css('left','75%').addClass('active').css('transition-delay','2s');
	
	}, timer);
	</script>
		";
		require_once ($base_path.'/includes/templates/last_records.tpl.php');
		require_once ($base_path.'/includes/last_records.inc.php');
	}

	// affichage des  tag res de l'accueil
	/*if ($opac_show_etageresaccueil) {
		require_once ($base_path.'/includes/templates/etagere.tpl.php');
		$aff_etagere = affiche_etagere(1, "", 1, $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables, "./index.php?lvl=etagere_see&id=!!id!!", $liens_opac);
		if ($aff_etagere) {
			print $etageres_header;
			print $aff_etagere ;
			print $etageres_footer;
		}
	}*/

	// affichage des flux rss
	if ($opac_show_rss_browser) require_once ($base_path.'/includes/rss.inc.php');

	//define( 'AFF_ETA_NOTICES_NON', 0 );
	//define( 'AFF_ETA_NOTICES_ISBD', 1 );
	//define( 'AFF_ETA_NOTICES_PMB', 2 );
	//define( 'AFF_ETA_NOTICES_BOTH', 4 );
	//define( 'AFF_ETA_NOTICES_REDUIT', 8 );
	//define( 'AFF_ETA_NOTICES_DEPLIABLES_NON', 0 );
	//define( 'AFF_ETA_NOTICES_DEPLIABLES_OUI', 1 );
	// param tres :
	//	$accueil : filtres les  tag res de l'accueil uniquement si 1
	//	$etageres : les num ros des  tag res s par s par les ',' toutes si vides
	//	$aff_commentaire : affichage du commentaire associ    l' tag re
	//	$aff_notices_nb : nombres de notices affich es : toutes = 0 
	//	$mode_aff_notice : mode d'affichage des notices, REDUIT (titre+auteur principal) ou ISBD ou PMB ou les deux : dans ce cas : (titre + auteur) en ent te du truc,   faire dans notice_display.class.php
	//	$depliable : affichage des notices une par ligne avec le bouton de d pliable
	//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le r sultat de la fonction 
	//function affiche_etagere($accueil=0, $etageres="", $aff_commentaire=0, $aff_notices_nb=0, $mode_aff_notice=AFF_ETA_NOTICES_BOTH, $depliable=AFF_ETA_NOTICES_DEPLIABLES_OUI, $htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" ) {

}
