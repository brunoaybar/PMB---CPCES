// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: proxy.js,v 1.1.2.3 2022/06/07 08:25:02 gneveu Exp $

var f_empr_client;
var f_expl_client;
var f_ack_write;
var f_ack_erase;
var f_ack_detect;
var f_ack_write_empr;
var f_ack_antivol_all;
var f_ack_antivol;
var f_ack_read_uid;
var flag_semaphore_rfid = 0;
var flag_semaphore_rfid_read = 0;
var flag_rfid_active = 1;
var rfid_active_test = 1;
var rfid_active_test_exec = 0;
var rfid_focus_active = 1;
var get_uid_from_cb = new Array();
var pmb_rfid_driver3m;
var bibliotheca;

function afficheErreur() {
	console.error(arguments);
}

function init_rfid_read_cb(empr_client, expl_client) {
	f_empr_client = empr_client;
	f_expl_client = expl_client;

	bibliotheca = new Bibliotheca(url_serveur_rfid);
	if (bibliotheca.isReturnExplPage()) {
		// On a deriver la function pour bibliotheca
		f_expl_client = f_expl_bibli;
	}

	read_cb();
}

function f_expl_bibli(cb_expl, cb_index, cb_count, cb_eas, cb_uid) {

	// TODO
	// Verifier les parties
	var nb_parties = 0;

	// On calcule le nombre de doc traiter
	var count = 0;
	for (var i = 0; i < memo_cb_rfid_js.length; i++) {
		const index = cb_expl.findIndex(cb => cb == memo_cb_rfid_js[i]);
		if (index != -1) {
			count++;
		}
	}

	// On affiche le nombre de doc traiter
	if (cb_expl.length - nb_parties) {
		var node = document.getElementById('indicateur_nb_doc');
		if (node) {
			node.innerHTML = `(${count} / ${cb_expl.length - nb_parties})`;
		}
	}

	for (var i = 0; i < cb_expl.length; i++) {

		if (memo_cb_rfid_js.includes(cb_expl[i])) {
			// document deja retourner
			continue;
		}

		if (list_erreur_cb_count[cb_expl[i]]) {
			const nb = list_erreur_cb_count[cb_expl[i]] ?? 0;
			alert(formatString("Nombre d'&eacute;l&eacute;ments manquants :" + nb))
		}

		document.getElementById('form_cb_expl').value = cb_expl[i];
		// si post_rfid == 1 
		// c'est l'utilisateur qui doit soumettre le formulaire
		if (post_rfid) {
			// On retourne le document
			document.saisie_cb_ex.submit();
		}
		break;
	}
}

function timeout() {
	if (!flag_rfid_active_test) {
		flag_rfid_active = 0;
		return;
	}
}

function read_cb() {
	if (!rfid_focus_active) {
		setTimeout('read_cb()', 2000);
		return;
	}

	if (flag_disable_antivol) {
		return;
	}

	if (!rfid_active_test_exec) {
		rfid_active_test_exec++;
		setTimeout('timeout()', 6000);
	}

	if (flag_semaphore_rfid || flag_semaphore_rfid_read) {
		setTimeout('read_cb()', 2000);
		return;
	}

	flag_rfid_active_test = 0;
	if (!rfid_focus_active) {
		setTimeout('read_cb()', 2000);
		return;
	}

	flag_semaphore_rfid_read = 1;

	var multiple = false;
	// On est sur la page de retour de document
	if (bibliotheca.isReturnExplPage()) {
		multiple = true;
	}
	// On est sur la page de lecture rfid
	if (bibliotheca.isRFIDReadPage()) {
		multiple = true;
	}

	bibliotheca.getItems(multiple).then(result_read_cb);
}

function result_read_cb(result) {
	if (result && 0 !== Object.keys(result).length) {

		var cb_expl = new Array();
		var cb_index = new Array();
		var cb_count = new Array();
		var cb_secur = new Array();
		var cb_uid = new Array();
		var cb_valid = new Array();

		flag_rfid_active_test = 1;
		flag_rfid_active = 1;

		if (f_empr_client) {
			f_empr_client(result.empr);
		}

		const tab_expl = result.expl;
		if (f_expl_client) {
			for (let i = 0; i < tab_expl.length; i++) {
				cb_expl[i] = tab_expl[i].cb;
				let cb_id = tab_expl[i].cb
				cb_valid[cb_id] = tab_expl[i].IsValid;
				cb_uid[i] = tab_expl[i].uid;
				
				cb_secur[i] = (tab_expl[i].IsSecured == 0 ? "Activé": "Désactivé");
			}
			f_expl_client(cb_expl, cb_index, cb_count, cb_secur, cb_valid);
		}
	}

	setTimeout('read_cb()', 2000);
	flag_semaphore_rfid_read = 0;
}


function mode1_init_rfid_read_cb(empr_client, expl_client) {
	f_empr_client = empr_client;
	f_expl_client = expl_client;

	// RFID init
	bibliotheca = new Bibliotheca(url_serveur_rfid);

	mode1_read_cb();
}

// Pour le pret a la chaine mode1
function mode1_read_cb() {
	flag_semaphore_rfid_read = 1;

	if (!rfid_focus_active) {
		setTimeout('mode1_read_cb()', 5000);
		return;
	}

	// On passe true a getItems car nous sommes en mode1, 
	// pour avoir une selection multiple
	bibliotheca.getItems(true).then(mode1_result_read_cb);
}

function mode1_result_read_cb(result) {

	flag_semaphore_rfid_read = 0;

	if (result && 0 !== Object.keys(result).length) {

		var cb_expl = new Array();
		var cb_index = new Array();
		var cb_count = new Array();
		var cb_eas = new Array();
		var cb_uid = new Array();
		var cb_valid = {};

		flag_rfid_active_test = 1;
		flag_rfid_active = 1;

		if (typeof result.empr[0] !== 'undefined' && 1 == result.empr.length) {
			f_empr_client(result.empr);
		}

		const tab_expl = result.expl;
		if (typeof tab_expl !== 'undefined' && 0 !== tab_expl.length) {
			for (let i = 0; i < tab_expl.length; i++) {
				cb_expl[i] = tab_expl[i].cb;
				let cb_id = tab_expl[i].cb
				cb_valid[cb_id] = tab_expl[i].IsValid;
				cb_uid[i] = tab_expl[i].uid;
			}
			f_expl_client(cb_expl, cb_index, cb_count, cb_eas, cb_uid, cb_valid);
		}
	}

	setTimeout('mode1_read_cb()', 2000);
	flag_semaphore_rfid_read = 0;
}

async function read_uid(f_ack) {
	flag_rfid_active_test = 0;
	flag_semaphore_rfid_read = 1;
	f_ack_read_uid = f_ack;
	
	var items = [];
	const itemsResult = await bibliotheca.getItems(true, false);
	for (let i = 0; i < itemsResult.length; i++) {
		items.push({
			"cb": itemsResult[i].Id,
			"uid": itemsResult[i].Tags[0].Id
		});
	}
	result_read_uid(items);
}

function result_read_uid(resultItems) {
	flag_rfid_active_test = 1;
	flag_rfid_active = 1;
	flag_semaphore_rfid_read = 0;
	
	if (f_ack_read_uid) {
		f_ack_read_uid(resultItems);
	}
}

// Detect presence d'element rfid
function init_rfid_detect(ack_detect) {
	if (!flag_rfid_active) {
		return;
	}
	f_ack_detect = ack_detect;

	bibliotheca = new Bibliotheca(url_serveur_rfid);
	bibliotheca.getItems(true).then(result_detect).catch(afficheErreur);
}


function result_detect(result) {
	const items = [...result.empr, ...result.expl];
	var flag = items.length > 0 ? items.length : 'false';
	if (f_ack_detect) f_ack_detect(flag);
}

// Efface tout !!!
function init_rfid_erase(ack_erase) {
	f_ack_erase = ack_erase;
	if (!flag_rfid_active) {
		return false;	
	}
	read_uid(rfid_erase_suite);
}

function rfid_erase_suite(resultItems) {
	if (!flag_rfid_active) {
		return false;	
	}
	for (let i = 0; i < resultItems.length; i++) {
		const item = resultItems[i];
		bibliotheca.writeTag({
			Type: bibliotheca.EMPTY_TYPE,
			TagFormat: bibliotheca.tagFormatGenericBlank,
			IsTagSecured: true
		}, '', item.tagId);
	}
	
	if (f_ack_erase) {
		f_ack_erase(true);
	}
}


var write_etiquette_data = new Array();

// Programme une etiquette
function init_rfid_write_etiquette(cb, nbtags, ack_write) {
	f_ack_write = ack_write;

	if (!flag_rfid_active) return false;
	write_etiquette_data.ack_write = ack_write;

	bibliotheca.writeTag({
		Type: bibliotheca.EXPL_TYPE,
		TagFormat: bibliotheca.tagFormatISO28560,
		IsTagSecured: true
	}, cb).then(result_write);
}

function result_write(success) {
	if (f_ack_write) {
		f_ack_write(success);
	}
}

// Programme une carte lecteur
var write_patron_data = new Array();
function init_rfid_write_empr(cb, ack_write) {
	f_ack_write_empr = ack_write;
	
	if (!flag_rfid_active)  {
		return false;	
	}

	write_patron_data.ack_write = ack_write;
	bibliotheca.writeTag({
		Type: bibliotheca.EMPR_TYPE,
		TagFormat: bibliotheca.tagFormatISO28560,
		IsTagSecured: false
	}, cb).then(result_write_empr);
}

function result_write_empr(success) {
	if (f_ack_write_empr) {
		f_ack_write_empr(success);
	}
}

// Active / desactive un antivol
// cb | code barre du livre
// level | boolean (ativation ou non de l'antivol)
// ack_antivol | callback
async function init_rfid_antivol(cb, level, ack_antivol) {
	
	if (typeof memo_cb_rfid_js != "undefined" && memo_cb_rfid_js.length > 0) {
		// petit hack car cb n'est pas replace
		cb = memo_cb_rfid_js[memo_cb_rfid_js.length - 1];
	}

	if ("!!expl_cb!!" == cb && typeof memo_cb_rfid_js != "undefined" && memo_cb_rfid_js.length == 0) {
		return false;
	}
	
	var name = ack_antivol.name;
	if (!flag_rfid_active || name.includes("mode1_ack_antivol_pret")) {
		return false;
	}

	f_ack_antivol = ack_antivol;
	if (!bibliotheca) {
		bibliotheca = new Bibliotheca(url_serveur_rfid);
	}

	var afi = rfid_afi_security_off;
	if (level) {
		afi = rfid_afi_security_active;
	}

	if (!get_uid_from_cb[cb]) {
		await bibliotheca.setTagSecurity(afi, cb);

		param_antivol_level = level;
		param_antivol_cb = cb;

		var name = f_ack_antivol.name;
		if (name.includes("ack_antivol_pret")) {
			// petit hack pour les pret de doc
			return false;
		}

		f_ack_antivol();
	} else {
		var list = get_uid_from_cb[cb];
		if (!list) f_ack_antivol(0);
		for (var i = 0; i < list.length; i++) {
			uidlist[i] = new Array();
			uidlist[i]['uid'] = list[i];
		}
		await bibliotheca.setTagSecurity(afi);
	}
}

function rfid_antivol_suite_1(retVal) {
	for (i = 0; i < retVal.length; i++) {
		var uid = retVal[i].uid;
		if (!retVal[i].error) {
			if (!retVal[i].type) {
				if (!get_uid_from_cb[retVal[i].cb]) get_uid_from_cb[retVal[i].cb] = new Array();
				get_uid_from_cb[retVal[i].cb][get_uid_from_cb[retVal[i].cb].length] = uid;
			}
		}
	}

	var level = param_antivol_level;
	var cb = param_antivol_cb;

	var afi = rfid_afi_security_off;
	if (level) {
		afi = rfid_afi_security_active;
	}
	var uidlist = new Array();
	var list = get_uid_from_cb[cb];
	if (!list) {
		f_ack_antivol(0)
	};
	for (var i = 0; i < list.length; i++) {
		uidlist[i] = new Array();
		uidlist[i]['uid'] = list[i];
	}
	bibliotheca.setTagSecurity(afi);
}


// Active / desactive tous les antivols
var rfid_antivol_all_data = new Array();

function init_rfid_antivol_all(level, ack_antivol) {
	f_ack_antivol = ack_antivol;


	bibliotheca = new Bibliotheca(url_serveur_rfid);
	
	//pour enlever l'antivol
	rfid_antivol_level = level;
	bibliotheca.getItems(true).then(result_rfid_antivol_1);
}

function result_rfid_antivol_1(items) {
	var afi = rfid_antivol_level ? rfid_afi_security_active : rfid_afi_security_off;
	bibliotheca.setTagSecurity(afi).then(result_rfid_antivol);
}

function result_rfid_antivol(success) {
	f_ack_antivol(1);
}

function effacer_ligne_tableau(array, valueOrIndex) {
	var output = [];
	var j = 0;
	for (var i in array) {
		if (i != valueOrIndex) {
			output[j] = array[i];
			j++;
		}
	}
	return output;
}

function formatString(encodedStr) {
	var parser = new DOMParser();
	// convertie les "&eacute;" en "é", etc.
	var dom = parser.parseFromString(encodedStr, 'text/html');
	// remplace les multiples espaces en 1 seul
	var str = dom.body.textContent.replace(/(\s){2,}/gm, ' ');
	return str.trim();
}