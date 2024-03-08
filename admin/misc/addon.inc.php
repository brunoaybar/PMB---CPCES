<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.6.2.53 2022/05/30 14:47:17 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if( !function_exists('traite_rqt') ) {
	function traite_rqt($requete="", $message="") {

		global $charset;
		$retour="";
		if($charset == "utf-8"){
			$requete=utf8_encode($requete);
		}
		pmb_mysql_query($requete) ; 
		$erreur_no = pmb_mysql_errno();
		if (!$erreur_no) {
			$retour = "Successful";
		} else {
			switch ($erreur_no) {
				case "1060":
					$retour = "Field already exists, no problem.";
					break;
				case "1061":
					$retour = "Key already exists, no problem.";
					break;
				case "1091":
					$retour = "Object already deleted, no problem.";
					break;
				default:
					$retour = "<font color=\"#FF0000\">Error may be fatal : <i>".pmb_mysql_error()."<i></font>";
					break;
			}
		}		
		return "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($message) : $message)."</font></td><td><font size='1'>".$retour."</font></td></tr>";
	}
}
echo "<table>";

/******************** AJOUTER ICI LES MODIFICATIONS *******************************/

switch ($pmb_bdd_subversion) {
    case 0:
        //MO && QV - Ajout d'un param�tre pour activer l'historique de navigation graphique.
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='nav_history_activated' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
                    VALUES (0, 'opac', 'nav_history_activated','0','Activer l\'historique de navigation graphique.\n 0: Non \n 1: Oui','a_general')";
            echo traite_rqt($rqt,"insert opac_nav_history_activated=0 into parametres");
        }
    case 1:
        // DG - Envoi par mail de l'impression
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='print_email' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'print_email', '1', 'Autoriser l\'envoi par mail sur le formulaire d\'impression de recherche ?\n 0 : Non \n 1 : Oui \n 2 : Seulement pour les lecteurs connect�s', 'a_general', '0')";
            echo traite_rqt($rqt,"insert opac_print_email=1 into parametres ");
        }
        
        // DG - Afficher le champ de saisie de l'adresse mail exp�ditrice sur le formulaire d'impression de recherche ?
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='print_email_sender' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'print_email_sender', '1', 'Afficher le champ de saisie de l\'adresse mail exp�ditrice sur le formulaire d\'impression de recherche ?\n 0 : Non \n 1 : Oui', 'a_general', '0')";
            echo traite_rqt($rqt,"insert opac_print_email_sender=1 into parametres ");
        }
        
        // DG - Autoriser l'envoi du formulaire d'impression de recherche � d'autres destinataires ?
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='print_email_recipients' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'print_email_recipients', '1', 'Autoriser l\'envoi du formulaire d\'impression de recherche � d\'autres destinataires ?\n 0 : Non \n 1 : Oui \n 2 : Seulement pour les lecteurs connect�s', 'a_general', '0')";
            echo traite_rqt($rqt,"insert opac_print_email_recipients=1 into parametres ");
        }
    case 2:
        // DG - Grilles de saisie de notices : Modification commentaire du param�tre form_editables pour la gestion avanc�e
        $rqt = "update ignore parametres set comment_param='Grilles de notices �ditables ? \n 0 non \n 1 oui, gestion simple \n 2 oui, gestion avanc�e \nAttention : ce sont deux gestions de grilles diff�rentes, vous pouvez passez de l\'une � l\'autre mais les donn�es restent bien distinctes.' where type_param='pmb' and sstype_param='form_editables'";
        echo traite_rqt($rqt, "update parametres change comment for pmb_form_editables");
        
        // DG - Grilles de saisie de lecteurs : Modification commentaire du param�tre form_empr_editables pour la gestion avanc�e
        $rqt = "update ignore parametres set comment_param='Grilles de lecteurs �ditables ? \n 0 non \n 1 oui, gestion simple \n 2 oui, gestion avanc�e \nAttention : ce sont deux gestions de grilles diff�rentes, vous pouvez passez de l\'une � l\'autre mais les donn�es restent bien distinctes.' where type_param='pmb' and sstype_param='form_empr_editables'";
        echo traite_rqt($rqt, "update parametres change comment for pmb_form_empr_editables");
    case 3:
        // DG - Autoriser ou non la prolongation par le responsable du groupe
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='pret_groupe_prolongation' "))==0){
            global $opac_pret_prolongation;
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'pret_groupe_prolongation', '".intval($opac_pret_prolongation)."', 'Autoriser le responsable � prolonger les pr�ts de son groupe ?\n 0 : Non \n 1 : Oui', 'a_general', '0')";
            echo traite_rqt($rqt,"insert opac_pret_groupe_prolongation=opac_pret_prolongation into parametres ");
        }
    case 4:
    	//DG - Propri�taire par d�faut en cr�ation de document num�rique
    	if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM users LIKE 'deflt_explnum_lenders'"))){
    		$rqt = "ALTER TABLE users ADD deflt_explnum_lenders INT(6) UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_explnum_statut " ;
    		echo traite_rqt($rqt,"ALTER users ADD deflt_explnum_lenders ");
    		
    		//Mise � jour des valeurs selon le param�trage existant
    		$rqt = "UPDATE users SET deflt_explnum_lenders=deflt_lenders";
    		echo traite_rqt($rqt,"UPDATE users SET deflt_explnum_lenders=deflt_lenders");
    	}
    	
    	//DG - Localisation par d�faut en cr�ation de document num�rique
    	if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM users LIKE 'deflt_explnum_location'"))){
    		$rqt = "ALTER TABLE users ADD deflt_explnum_location INT(6) UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_explnum_statut " ;
    		echo traite_rqt($rqt,"ALTER users ADD deflt_explnum_location ");
    		
    		//Mise � jour des valeurs selon le param�trage existant
    		$rqt = "UPDATE users SET deflt_explnum_location=deflt_docs_location";
    		echo traite_rqt($rqt,"UPDATE users SET deflt_explnum_location=deflt_docs_location");
    	}
    case 5:
        //DG - On stocke la provenance du mail
        if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM mails LIKE 'mail_from_uri'"))){
            $rqt = "ALTER TABLE mails ADD mail_from_uri VARCHAR(255) NOT NULL DEFAULT ''" ;
            echo traite_rqt($rqt,"ALTER mails ADD mail_from_uri ");
        }
        
        //DG - On stocke la provenance du mail en attente
        if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM mails_waiting LIKE 'mail_waiting_from_uri'"))){
            $rqt = "ALTER TABLE mails_waiting ADD mail_waiting_from_uri VARCHAR(255) NOT NULL DEFAULT ''" ;
            echo traite_rqt($rqt,"ALTER mails_waiting ADD mail_waiting_from_uri ");
        }
    case 6:
        //DG - Param�tre pour l'orientation du bandeau d'acceptation des cookies
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent_orientation' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent_orientation', 'bottom', 'Emplacement du bandeau d\'acceptation des cookies et des traceurs ? \ntop : en haut \nmiddle : au milieu \nbottom : en bas \npopup : fen�tre','a_general',0)";
            echo traite_rqt($rqt,"insert opac_cookies_consent_orientation into parametres");
        }
        
        //DG - Param�tre pour afficher ou non l'ic�ne de modification des param�tres
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent_show_icon' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent_show_icon', '0', 'Afficher l\'ic�ne de modification des cookies et des traceurs ? \n0 : non \n1 : oui','a_general',0)";
            echo traite_rqt($rqt,"insert opac_cookies_consent_show_icon into parametres");
        }
    case 7:
        // DG - Cr�ation de la table de planches de codes-barres
        // id_barcodes_sheet : Identifiant
        // barcodes_sheet_label : Libell�
        // barcodes_sheet_data : Structure JSON des donn�es de g�n�ration
        // barcodes_sheet_order : Ordre
        $rqt = "create table if not exists barcodes_sheets(
				id_barcodes_sheet int unsigned not null auto_increment primary key,
				barcodes_sheet_label varchar(255) not null default '',
				barcodes_sheet_data text not null,
				barcodes_sheet_order int(11) NOT NULL default 0) ";
        echo traite_rqt($rqt,"create table barcodes_sheets");
    case 8:
        // DG - Cr�ation de la table pour g�rer les cookies et les traceurs
        // id_analytics_service : Identifiant
        // analytics_service_name : Nom du service
        // analytics_service_active : Activ� Oui/Non
        // analytics_service_parameters : Param�tres
        // analytics_service_template : Template calcul�
        // analytics_service_consent_template : Template de consentement calcul�
        $rqt = "create table if not exists analytics_services(
				id_analytics_service int unsigned not null auto_increment primary key,
				analytics_service_name varchar(255) not null default '',
				analytics_service_active int(1) NOT NULL DEFAULT 0,
				analytics_service_parameters mediumtext not null,
				analytics_service_template text not null,
				analytics_service_consent_template text not null)";
        echo traite_rqt($rqt,"create table analytics_services");
    case 9:
        // BT - Ajout d'un param�tre permettant de g�rer l'affichage OPAC des options de groupement sur les bannettes / alertes
        // Le param�tre contenant une faute d'orthographe (banette au lieu de bannette), le case est supprim� et corrig� en dessous (98)
	case 10:
	   // AR - Ajout d'un param�tre pour la gestion des troncatures dans Sphinx
	   if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'sphinx' AND sstype_param = 'troncat_min_length'")) == 0) {
	       $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			 VALUES (0, 'sphinx', 'troncat_min_length', '3', 'Nombre de caract�res minimum du mot cherch� pour d�clencher la troncature. \n La modification de ce param�tre n�cessite la reg�n�ration compl�te des index Sphinx. (via les lignes de commandes)', '', 0)";
            echo traite_rqt($rqt, "insert sphinx_troncat_min_length into parametres");
        }
	case 11:
	    // BT - Correction du nom du param�tre show_bannettes_groupement (case 96)
	    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'opac' AND sstype_param = 'show_banettes_groupement'"))) {
	        $rqt = "DELETE FROM parametres WHERE type_param = 'opac' AND sstype_param = 'show_banettes_groupement'";
	        echo traite_rqt($rqt, "delete opac_show_banettes_groupement");
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'show_bannettes_groupement', '0', 'Affichage des options de groupement dans les alertes ? \n0 : non \n1 : oui', 'f_modules', 0)";
	        echo traite_rqt($rqt, "insert opac_show_bannettes_groupement into parametres");
	    } elseif (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'opac' AND sstype_param = 'show_bannettes_groupement'")) == 0) {
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'show_bannettes_groupement', '0', 'Affichage des options de groupement dans les alertes ? \n0 : non \n1 : oui', 'f_modules', 0)";
	        echo traite_rqt($rqt, "insert opac_show_bannettes_groupement into parametres");
	    }
	case 12:
	    // QV/GN/JP - Correction du champ date dans la table anim_registrations
	    $rqt = "CREATE TABLE IF NOT EXISTS anim_registrations (
			id_registration INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			nb_registered_persons INT(11),
            name VARCHAR(255),
            email VARCHAR(255),
            phone_number VARCHAR(255),
            num_animation INT(11),
            num_registration_status INT(11),
            num_empr INT(11),
            num_origin INT(11),
            date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            hash VARCHAR(255) NULL DEFAULT ''
        )";
	    echo traite_rqt($rqt, 'CREATE TABLE IF NOT EXISTS anim_registrations');
	    
	    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM anim_registrations WHERE FIELD = 'date' and Type = 'datetime'")) == 1) {
            $rqt = "ALTER TABLE anim_registrations CHANGE date date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
            echo traite_rqt($rqt,"ALTER TABLE anim_registrations CHANGE date");
	    }
	    
	    // QV/GN/JP - Ajout du champ hash dans la table anim_registrations
	    if (pmb_mysql_num_rows(pmb_mysql_query('SHOW COLUMNS FROM anim_registrations WHERE Field = "hash"')) == 0) {
	        $rqt = "ALTER TABLE anim_registrations ADD hash VARCHAR(255) NULL DEFAULT '' AFTER date";
	        echo traite_rqt($rqt,"alter table anim_registrations add hash");
	    }
	    
	    // QV/GN - renommage de la colonne nb_registred_persons -> nb_registered_persons
	    if (pmb_mysql_num_rows(pmb_mysql_query('SHOW COLUMNS FROM anim_registrations WHERE Field = "nb_registred_persons"')) == 1) {
	        $rqt = "ALTER TABLE anim_registrations CHANGE nb_registred_persons nb_registered_persons INT(11)";
	        echo traite_rqt($rqt,"ALTER TABLE anim_registrations CHANGE nb_registred_persons nb_registered_persons");
	    }
	case 13:
		// DG - Ajout de la possibilit� de joindre les images dans le mail ( pmb_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails : \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='pmb' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre pmb_mail_html_format");
	case 14:
	    // QV/JP - Passage du champ "chat_message_date" en timestamp s'il est en datetime
        $rqt = "ALTER TABLE chat_messages CHANGE chat_message_date chat_message_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
        echo traite_rqt($rqt,"ALTER TABLE chat_messages CHANGE chat_message_date");
	case 15 :
	    //DB - Modification du libell� des param�tres pmb_mail_methode / opac_mail_methode suite ajout mode d'authentification SMTP
	    $rqt = "update parametres set comment_param = 'M�thode d\'envoi des mails : \n php : fonction mail() de php\n smtp,hote:port,auth,user,pass,(ssl|tls),auth_type\n\nen smtp,\nauth (authentification) = 0 ou 1\nauth_type (type d\'authentification) = Non renseign�, CRAM-MD5, LOGIN, PLAIN ou XOAUTH2' where sstype_param = 'mail_methode'";
	    echo traite_rqt($rqt,"update parametre pmb_mail_methode, opac_mail_methode");
	case 16 :
		// DG - Table de logs
		$rqt = "CREATE TABLE IF NOT EXISTS logs (
					id_log int unsigned not null auto_increment primary key,
					log_service varchar(255) not null default '',
					log_type varchar(255) not null default '',
					log_module varchar(255) not null default '',
					log_label varchar(255) not null default '',
					log_message mediumtext,
					log_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					log_url varchar(255) not null default '',
					log_num_user int(10) unsigned not null default 0,
					log_type_user int(1) unsigned not null default 0,
					log_data mediumtext
        		)";
		echo traite_rqt($rqt,"create table logs");
	case 17 :
	    //GN QV JP - Param�tres pour afficher le champ de recherche sur l'identifiant d'une authorit�
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_authority_id' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES (0, 'pmb', 'show_authority_id', '0', 'Afficher le champ de recherche sur l\'identifiant d\'une autorit� \n 0 : non \n 1 : oui', '',0) ";
	        echo traite_rqt($rqt, "insert pmb_show_authority_id = 0 into parameters");
	    }
	case 18 :
		// DG - Adresse mail du destinataire pour la demande de d�sinscription � une liste de diffusion.
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_list_unsubscribe_mailto' "))==0){
			global $opac_biblio_email;
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES(0,'pmb','mail_list_unsubscribe_mailto','".$opac_biblio_email."','Adresse mail du destinataire pour la demande de d�sinscription � une liste de diffusion.','',0)" ;
			echo traite_rqt($rqt,"insert pmb_mail_list_unsubscribe_mailto into parametres");
		}
	case 19 :
		// DG - On modifie la cl� primaire en retirant l'auto incr�ment
		$req="SHOW COLUMNS from logs like 'id_log'";
		$res=pmb_mysql_query($req);
		if($res && pmb_mysql_num_rows($res)){
			$rqt = "ALTER TABLE logs change id_log uniqid_log varchar(13) NOT NULL default ''";
			echo traite_rqt($rqt,"ALTER TABLE logs change id_log to uniqid_log varchar(13)") ;
		}
	case 20 :
	    //MO - RT Ajout d'un parametre pour activer le pipe comme caratere d'op�rateur OU en recherche
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_fixed_and_operator' "))==0) {
	        $rqt = "insert into parametres (valeur_param, type_param, sstype_param, section_param, comment_param) values ('0', 'pmb', 'search_fixed_and_operator', 'search', 'Emp�cher le caract�re plus (+) d\'�tre interpr�t� comme un op�rateur OU dans les recherches.\nL\'op�rateur OU par d�faut est la ligne verticale ou pipe \'|\'. \n0 : non \n1 : oui')" ;
	        echo traite_rqt($rqt, "Add general setting search_fixed_and_operator");
	    }
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='opac_search_fixed_and_operator' "))==0) {
	        $rqt = "insert into parametres (valeur_param, type_param, sstype_param, section_param, comment_param) values ('0', 'opac', 'opac_search_fixed_and_operator', 'c_recherche', 'Emp�cher le caract�re plus (+) d\'�tre interpr�t� comme un op�rateur OU dans les recherches opac.\nL\'op�rateur OU par d�faut est la ligne verticale ou pipe \'|\'.\n0 : non \n1 : oui')" ;
	        echo traite_rqt($rqt, "Add opac parametre opac_search_fixed_and_operator");
	    }
	case 21 :
		//DG - Nettoyage des entr�es de traductions � partir des param�tres
		// Les donn�es peuvent �tre tronqu�es, il faut donc que les traductions soient dans le champ trans_text
		$rqt = "DELETE FROM translation WHERE trans_table='parametres' AND trans_field='valeur_param' AND trans_small_text != ''";
		echo traite_rqt($rqt,"delete values translations in small_text");
		
		// DG - Table de d�finition/personnalisation des s�lecteurs
		$rqt = "CREATE TABLE IF NOT EXISTS selectors (
					selector_name varchar(255) not null default '',
					selector_parameters_tabs mediumtext,
					primary key(selector_name)
        		)";
		echo traite_rqt($rqt,"create table selectors");
	case 22:
	    // JP-QV - Table de liaisons d'un segment vers des univers
	    $rqt = "CREATE TABLE IF NOT EXISTS search_segments_associated_universes (
                   num_segment int(10) NOT NULL,
                   num_universe int(10) NOT NULL
                )";
	    echo traite_rqt($rqt,"create table search_segments_associated_universes");
	case 23:
	    // DB - Ajout champs d'activation RMC dans tables search_universes et search_segments
	    $req="SHOW COLUMNS from search_universes like 'search_universe_rmc_enabled'";
	    $res=pmb_mysql_query($req);
	    if($res && pmb_mysql_num_rows($res)==0){
	        $rqt = "ALTER TABLE search_universes ADD search_universe_rmc_enabled INT(1) NOT NULL DEFAULT '0' ";
	        echo traite_rqt($rqt,"ALTER TABLE search_universes add search_universe_rmc_enabled") ;
	    }
	    $req="SHOW COLUMNS from search_segments like 'search_segment_rmc_enabled'";
	    $res=pmb_mysql_query($req);
	    if($res && pmb_mysql_num_rows($res)==0){
	        $rqt = "ALTER TABLE search_segments ADD search_segment_rmc_enabled INT(1) NOT NULL DEFAULT '0' ";
	        echo traite_rqt($rqt,"ALTER TABLE search_segments add search_segment_rmc_enabled") ;
	    }
	case 24:
	    $req = "SHOW KEYS FROM search_segments_associated_universes";
	    $res = pmb_mysql_query($req);
	    if ($res && pmb_mysql_num_rows($res) == 0) {
	        $rqt = "ALTER TABLE search_segments_associated_universes ADD PRIMARY KEY (num_segment, num_universe); ";
	        echo traite_rqt($rqt,"ALTER TABLE search_segments_associated_universes ADD PRIMARY KEY") ;
	    }
	case 25:
		//DG - Personnalisation du mode d'affichage dans les s�lecteurs
		if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM selectors LIKE 'selector_display_modes'"))){
			$rqt = "ALTER TABLE selectors ADD selector_display_modes mediumtext" ;
			echo traite_rqt($rqt,"ALTER selectors ADD selector_display_modes ");
		}
	case 26:
		//DG - Param�tre pour la supervision des logs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'supervision' and sstype_param='logs_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'supervision', 'logs_active', '0', 'Supervision des logs activ�e.\n 0 : Non\n 1 : Oui','', 1)";
			echo traite_rqt($rqt,"insert supervision_logs_active into parametres");
		}
		
		//DG - Param�tre pour la supervision des mails
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'supervision' and sstype_param='mails_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'supervision', 'mails_active', '0', 'Supervision des mails activ�e.\n 0 : Non\n 1 : Oui','', 1)";
			echo traite_rqt($rqt,"insert supervision_mails_active into parametres");
		}
	case 27:
		//GN-TS - Param�tre pour afficher la RMC responsive
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='rmc_responsive' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'rmc_responsive', '0', 'Affichage de la recherche multi-crit�res responsive .\n 0 : Non\n 1 : Oui','', 0)";
			echo traite_rqt($rqt,"insert opac_rmc_responsive into parametres");
		}
	case 28:
	    //QV JP - Param�tre pour utiliser la vignette d'un document num�rique si pas de vignette pour la notice
	    $query_old = "select 1 from parametres where type_param= 'opac' and sstype_param='record_use_thumbnail_docnum'";
	    $query_new = "select 1 from parametres where type_param= 'opac' and sstype_param='book_pics_use_thumbnail_docnum'";
	    if (pmb_mysql_num_rows(pmb_mysql_query($query_old))==0 && pmb_mysql_num_rows(pmb_mysql_query($query_new))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'record_use_thumbnail_docnum', '0', 'Utiliser la vignette du premier document num�rique, si la notice n\'en poss�de pas.\n 0 : Non\n 1 : Oui','', 0)";
			echo traite_rqt($rqt,"insert opac_record_use_thumbnail_docnum=0 into parametres");
		}
	case 29:
	    //QV JP - UPDATE du param�tre pour utiliser la vignette d'un document num�rique si pas de vignette pour la notice
	    $query_old = "select 1 from parametres where type_param= 'opac' and sstype_param='record_use_thumbnail_docnum'";
	    $query_new = "select 1 from parametres where type_param= 'opac' and sstype_param='book_pics_use_thumbnail_docnum'";
	    if (pmb_mysql_num_rows(pmb_mysql_query($query_old))==1 && pmb_mysql_num_rows(pmb_mysql_query($query_new))==0) {
	        $rqt = "UPDATE parametres SET sstype_param='book_pics_use_thumbnail_docnum', section_param='e_aff_notice' WHERE type_param= 'opac' and sstype_param='record_use_thumbnail_docnum'";
	        echo traite_rqt($rqt,"Parametre opac_record_use_thumbnail_docnum switch to opac_book_pics_use_thumbnail_docnum");
	    }
	case 30:
	    //TS GN - Param�tre pour sotcker la vignette d'un document num�rique dans un repertoire defini
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='docnum_img_folder_id' "))==0) {
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'docnum_img_folder_id', '0', 'Identifiant du r�pertoire d\'upload des vignettes de documents num�riques.','', 0)";
	        echo traite_rqt($rqt,"insert pmb_docnum_img_folder_id=0 into parametres");
	    }
	case 31:
	    //QV JP - Param�tre pour limiter le nombre de noeud dans les graphes
	    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 from parametres where type_param='pmb' and sstype_param='entity_graph_limit'"))==0) {
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'entity_graph_limit', '20', 'Valeur num�rique, permettant de limiter le nombre de points sur chaque branche du graphe :\n 0 pour illimit�','', 0)";
	        echo traite_rqt($rqt,"insert pmb_entity_graph_limit=20 into parametres");
	    }
	    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 from parametres where type_param='opac' and sstype_param='entity_graph_limit'"))==0) {
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'entity_graph_limit', '20', 'Valeur num�rique, permettant de limiter le nombre de points sur chaque branche du graphe :\n 0 pour illimit�', '', 0)";
	        echo traite_rqt($rqt,"insert opac_entity_graph_limit=20 into parametres");
	    }
	case 32 :
	    // QV JP - Script de v�rification de saisie d'un exemplaire
	    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' AND sstype_param='expl_verif_js' "))==0){
	        $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'pmb', 'expl_verif_js', '', 'Script de v�rification de saisie d\'exemplaire', '', 0)";
	        echo traite_rqt($rqt,"INSERT pmb_expl_verif_js='' INTO parametres");
	    }
	case 33 :
	    //QV JP - Creation de la table pour les types d'animations et jointure avec la table des animations
	    $rqt = "CREATE TABLE IF NOT EXISTS anim_types (
            id_type INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
            label VARCHAR(255) NOT NULL DEFAULT ''
        )";
	    echo traite_rqt($rqt,"CREATE TABLE anim_types");
	    
	    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM anim_types WHERE id_type='1'"))==0) {
	        $rqt = "INSERT INTO anim_types (id_type, label) VALUES (1, 'Type par d�faut')";
	        echo traite_rqt($rqt, 'INSERT DEFAULT VALUES INTO anim_types');
	    }
	    
	    $req="SHOW COLUMNS FROM anim_animations LIKE 'num_type'";
	    $res = pmb_mysql_query($req);
	    if($res && pmb_mysql_num_rows($res)==0){
	        $rqt = "ALTER TABLE anim_animations ADD num_type INT(11) NOT NULL DEFAULT 1 ";
	        echo traite_rqt($rqt,"ALTER TABLE anim_animations ADD num_type=1") ;
	    }
	case 34 :
	    //GN - Ajout d'un template pour envoyer un mail de demande de mot de passe (Objet du mail et corps du mail)
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='send_pwd_mail_obj' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
					VALUES (0, 'empr', 'send_pwd_mail_obj', 'Demande de changement de mot de passe', 'Objet du mail envoy� lors de la demande de mot de passe.', 0)";
	        echo traite_rqt($rqt,"insert empr_send_pwd_mail_obj into parametres");
	    }
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='send_pwd_mail_text' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
					VALUES (0, 'empr', 'send_pwd_mail_text', 'Bonjour, \nAfin de vous connecter � notre site, un mot de passe temporaire a �t� g�n�r� : !!pwd!! \nVous pouvez le modifier en acc�dant � votre compte depuis l\'adresse !!url!!\nCordialement,\nLe responsable de la biblioth�que.', 'Texte du mail envoy� lors de la demande de mot de passe.\nNe pas oublier de mettre !!pwd!! pour ins�rer le mot de passe et !!url!! pour ins�rer le lien de connexion � l\'opac', 0)";
	        echo traite_rqt($rqt,"insert empr_send_pwd_mail_text into parametres");
	    }
	case 35 :
	    //GN - Ajout d'un parametre pour mettre par defaut l'envoi du mot de passe lors de la creation d'un nouvel utilisateur
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='send_pwd_by_mail' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
                    VALUES (0, 'empr', 'send_pwd_by_mail', 0, 'Param�trer par d�faut l\'envoi du mot de passe de l\'emprunteur par mail en cr�ation de compte : 0 : non 1 : oui', 0)";
	        echo traite_rqt($rqt,"insert empr_send_pwd_by_mail into parametres");
	    }
	case 36 :
        //DB - Suppression visibilite et ajout d'une valeur par defaut pour le parametre empr_password_enabled_rules
        $rqt = "UPDATE parametres SET gestion=1 where type_param='empr' and sstype_param='password_enabled_rules' ";
        echo traite_rqt($rqt,"hide empr_password_enabled_rules parameter");
        $rules = array(
            "min_length" => array(
                "enabled" => 1,
                "value" => "12",
                "type" => "regexp",
                "regexp" => "^.{VAR,}$"
            ),
            "min_uppercase_chars" => array(
                "enabled" => 1,
                "value" => "1",
                "type" => "regexp",
                "regexp" => "(?=(?:.*[A-Z]){VAR,}).*"
            ),
            "min_numbers" => array(
                "enabled" => 1,
                "value" => "1",
                "type" => "regexp",
                "regexp" => "(?=(?:.*[0-9]){VAR,}).*"
            )
        );
        $json_rules = json_encode($rules);
        $rqt = "UPDATE parametres SET valeur_param='".addslashes($json_rules)."' where type_param='empr' and sstype_param='password_enabled_rules' and valeur_param='' ";
        echo traite_rqt($rqt,"add default value to empr_password_enabled_rules parameter");
	case 37 :
        // GN - Suppression de espace a la apres le password
        $rqt = "UPDATE parametres SET valeur_param = 'Bonjour, \nAfin de vous connecter � notre site, un mot de passe temporaire a �t� g�n�r� : !!pwd!!\nVous pouvez le modifier en acc�dant � votre compte depuis l\'adresse !!url!!\nCordialement,\nLe responsable de la biblioth�que.' where type_param = 'empr' and sstype_param = 'send_pwd_mail_text'";
        echo traite_rqt($rqt,"update empr_send_pwd_mail_text into parametres");
	case 38:
	    //GN - RT Ajout d'un champ de param�tres JSON dans la table search_universes
	    $rqt = "ALTER TABLE search_universes ADD COLUMN search_universe_settings TEXT DEFAULT ''";
	    echo traite_rqt($rqt,"ALTER TABLE search_universes ADD COLUMN search_universe_settings");
	case 39:
		// DG - Modification du param�tre notice_controle_doublons
		$rqt = "update parametres set comment_param = 'Contr�le sur les doublons en saisie de la notice \n 0: Pas de contr�le sur les doublons, \n 1,tit1,tit2, ... : Recherche par m�thode _exacte_ de doublons sur des champs, d�fini dans le fichier notice.xml  \n 2,tit1,tit2, ... : Recherche par _similitude_ \n 3,tit1,tit2, ... : Recherche par _alphanum_, sur la base des caract�res alphanum�riques et insensible � la casse \nG�n�rer les signatures (nettoyage de base) si l\'on change la valeur du param�tre' where type_param='pmb' and sstype_param = 'notice_controle_doublons'";
		echo traite_rqt($rqt,"update parametres pmb_notice_controle_doublons set comment");
	case 40:
		// DG - Statistiques OPAC - Visible pour tous ? Default value = 1 / on assure la r�tro-compatibilit�
		$rqt = "ALTER TABLE statopac_request ADD autorisations_all INT(1) NOT NULL DEFAULT 1 AFTER autorisations";
		echo traite_rqt($rqt,"ALTER TABLE statopac_request add autorisations_all AFTER autorisations");
	case 41:
	    // TS - suppression des url de vignettes de documents numeriques commen�ant par 'getimage.php', on utilisera vig_num.php
	    $rqt = "UPDATE explnum SET explnum_vignette = '' WHERE explnum_vignette LIKE 'getimage.php%'";
	    echo traite_rqt($rqt,"UPDATE explnum SET explnum_vignette = '' WHERE explnum_vignette LIKE 'getimage.php%'");
	case 42:
	    // TS - Nettoyage des relations entre categories
	    $rqt = " select 1 " ;
	    echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' style='color : #FF0000' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE POUR NETTOYER LES RELATIONS ENTRE CATEGORIES : Admin > Outils > Nettoyage de base</a></b> ") ;
	case 43:
		// DG - Augmentation de la taille du champ log_label de la tables logs
		$rqt = "ALTER TABLE logs MODIFY log_label MEDIUMTEXT";
		echo traite_rqt($rqt,"ALTER TABLE logs MODIFY log_label MEDIUMTEXT");
	case 44:
	    // QV RT - Utilisation d'une popup pour les facettes
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facettes_modal_activate' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
            VALUES(0,'opac','facettes_modal_activate','0','Activer l\'affichage sous forme de popup dans les facettes pour voir plus de r�sultats\n0 : non\n1 : oui','c_recherche',0)" ;
	        echo traite_rqt($rqt,"insert opac_facettes_modal_activate=0 into parametres") ;
	    }
	case 45:
	    // GN/TS - Ajout des tables / parametres signature electronique
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='digital_signature_activate' ")) == 0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
                    VALUES (0, 'pmb', 'digital_signature_activate', 0, 'Param�trer pour activer la signature �lectronique : 0 : non 1 : oui', 0)";
	        echo traite_rqt($rqt,"insert pmb_digital_signature_activate into parametres");
	    }
	    
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='digital_signature_folder_id' ")) == 0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
                    VALUES (0, 'pmb', 'digital_signature_folder_id', 0, 'Identifiant du r�pertoire pour les signatures', 0)";
	        echo traite_rqt($rqt,"insert pmb_digital_signature_folder_id into parametres");
	    }
	    
	    $rqt = "CREATE TABLE IF NOT EXISTS certificates (
            id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL DEFAULT '',
            private_key VARCHAR(255) NOT NULL DEFAULT '',
            cert VARCHAR(255) NOT NULL DEFAULT ''
        )";
	    echo traite_rqt($rqt,"CREATE TABLE certificates");
	    
	    $rqt = "CREATE TABLE IF NOT EXISTS digital_signature (
            id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL DEFAULT '',
            type INT NOT NULL DEFAULT 0,
            fields VARCHAR(65535) NOT NULL DEFAULT '',
            upload_folder INT NOT NULL DEFAULT 0,
            num_cert INT NOT NULL DEFAULT 0
        )";
	    echo traite_rqt($rqt,"CREATE TABLE digital_signature");
	    
	case 46 :
	    // GN - Changement de type de colonne fields dans la table digital_signature
	    $rqt = "ALTER TABLE digital_signature MODIFY fields TEXT";
	    echo traite_rqt($rqt,"ALTER TABLE digital_signature MODIFY fields TEXT");
}



/******************** JUSQU'ICI **************************************************/
/* PENSER � faire +1 au param�tre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en m�me temps */

echo traite_rqt("update parametres set valeur_param='".$pmb_subversion_database_as_it_shouldbe."' where type_param='pmb' and sstype_param='bdd_subversion'","Update to $pmb_subversion_database_as_it_shouldbe database subversion.");
echo "<table>";