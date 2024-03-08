// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: driver.js,v 1.1.2.4 2022/06/01 09:04:58 jparis Exp $

/*
Information pour utiliser le driver bibliotheca
rfid_afi_security_codes		true,false	
rfid_driver					bibliotheca	
rfid_serveur_url			http://localhost:21645/
*/

class WebService {
	constructor(url_serveur_rfid) {
		this.url_serveur_rfid = url_serveur_rfid + "TagService/Web/";
	}

	get HTTP_POST() {
		return "POST";
	}

	get HTTP_GET() {
		return "GET";
	}

	async fetch(http_method, fetch_url, data) {

		if (!this.url_serveur_rfid) {
			throw "[Webserivce] URL server not defined !";
		}

		let url = this.url_serveur_rfid + fetch_url;
		let init = {
			method: http_method == this.HTTP_POST ? this.HTTP_POST : this.HTTP_GET,
			cache: 'no-cache'
		};

		if (http_method == this.HTTP_POST) {
			var headers = new Headers();
			headers.append("Content-Type", "application/json");
			init["headers"] = headers;
			init['body'] = JSON.stringify(data);
		} else {
			url += '?';
			for (let prop in data) {
				url += '&' + prop + '=' + data[prop];
			}
		}

		return await fetch(url, init)
	}

	async getItems() {
		try {
			const response = await this.fetch(this.HTTP_GET, 'GetItems');
			const result = await response.json();
			return result.GetItemsResult ?? [];
		} catch (e) {
			console.error(`[WebService - getItems] ${e}`);
			return [];
		}
	}

	async writeTag(tag) {
		try {
			const response = await this.fetch(this.HTTP_POST, 'WriteTag', { "tag": tag });
			return response.ok ? true : false;
		} catch (e) {
			console.error(`[WebService - writeTag] ${e}`);
			return false;
		}
	}

	async setTagSecurity(idTag, IsSecured) {
		try {
			const response = await this.fetch(this.HTTP_POST, 'SetTagSecurity', { "tagId": idTag, "isSecured": IsSecured });
			return response.ok ? true : false;
		} catch (e) {
			console.error(`[WebService - setTagSecurity] ${e}`);
			return false;
		}
	}

	async isOnline() {
		try {
			const response = await this.fetch(this.HTTP_GET, 'IsOnline');
			const result = await response.json();
			return result.IsOnlineResult ?? false;
		} catch (e) {
			console.error(`[WebService - isOnline] ${e}`);
			return false;
		}
	}

	async isConnected() {
		try {
			const response = await this.fetch(this.HTTP_GET, 'IsConnected');
			const result = await response.json();
			return result.IsConnectedResult ?? false;
		} catch (e) {
			console.error(`[WebService - isConnected] ${e}`);
			return false;
		}
	}
}

class Bibliotheca {

	constructor(url_serveur_rfid) {
		this.ws = new WebService(url_serveur_rfid);
		this.isBibliotheca = true;
	}

	// Permet de savoir si on est sur la page des retour de document
	isReturnExplPage() {
		return window.location.href.toLowerCase().includes('categ=retour') ? true : false;
	}

	// Permet de savoir si on est sur la page de lecture rfid
	isRFIDReadPage() {
		return window.location.href.toLowerCase().includes('categ=rfid_read') ? true : false;
	}

	get tagFields() {
		return [
			{
				Name: "ItemID",
				Type: 2,
				Value: null
			},
			{
				Name: "SetNumber",
				Type: 1,
				Value: 1
			},
			{
				Name: "SetSize",
				Type: 1,
				Value: 1
			}
		];
	}

	get TagSecuredActived() {
		return true;
	}
	get TagSecuredDisabled() {
		return false;
	}
	
	get tagFormatISO28560() {
		return 768;
	}

	get tagFormatGenericBlank() {
		return 86;
	}

	get EMPTY_TYPE() {
		return 0;
	}
	
	get EXPL_TYPE() {
		return 1;
	}

	get EMPR_TYPE() {
		return 3;
	}

	getFormatedValue(data, multiple) {

		var items = {
			"empr": [],
			"expl": [],
		};

		if (data.length > 0) {
			var length = (multiple) ? data.length : 1;
			for (let i = 0; i < length; i++) {
				if (this.EMPR_TYPE == data[i].Type) {
					items.empr.push(data[i].Id);
				} else {
					items.expl.push({
						"cb": data[i].Id,
						"tagId": data[i].Tags[0].Id,
						"IsSecured": data[i].IsSecured,
						"IsValid": data[i].IsValid
					});
				}
			}
		}

		return items;
	}

	async getItems(multiple = false, formated = true) {
		try {
			const items = await this.ws.getItems();
			if (formated) {		
				return this.getFormatedValue(items, multiple);
			} else {
				var length = (multiple) ? items.length : 1;
				return items.slice(0, length);
			}
		} catch (e) {
			console.error(`[Bibliotheca - getItems] ${e}`);
			return this.getFormatedValue([], multiple);
		}
	}

	async setTagSecurity(security, cb = "") {
		try {
			var items = await this.ws.getItems();
			
			if (cb && cb != "") {
				const item = items.find(item => item.Id == cb);
				items = item ? [item] : [];
			}

			items = items.filter(item => item.Type == this.EXPL_TYPE);
			for (let i = 0; i < items.length; i++) {
				await this.ws.setTagSecurity(items[i].Tags[0].Id, security);
			}

		} catch (e) {
			console.error(`[Bibliotheca - setTagSecurity] ${e}`);
			return false;
		}
	}

	async writeTag(tag, cb = "", tagId = "") {

		if (![this.TagSecuredActived, this.TagSecuredDisabled].includes(tag.IsTagSecured)) {
			console.error('[Bibliotheca - writeTag] Please check data in tag !');
			return false;
		}
		
		if (tagId && tagId != "") {			
			tag['Id'] = tagId ?? false;
		} else {			
			const items = await this.ws.getItems();
			const item = items[0];
			tag['Id'] = item.Tags[0].Id ?? false;
		}
		
		if (!cb || cb == "") {
			cb = tag['Id'];
		}
		
		var fields = [];
		if (this.tagFormatISO28560 == tag.TagFormat) {			
			for (let i = 0; i < this.tagFields.length; i++) {
				var field = this.tagFields[i];
				if ("ItemID" == field.Name) {
					field.Value = cb;
				}
				fields.push(field);
			}
		}
		tag['Fields'] = fields;
		tag['IsSecutirySupported'] = true;

		if (!tag['Id']) {
			console.error('[Bibliotheca - writeTag] Id tag not found !');
			return false;
		}

		const response = await this.ws.writeTag(tag);
		if (!response) {
			alert("Une erreur est survenue à l'écriture de l'étiquette");
		}
		return response;
	}
}
