<?php
// +-------------------------------------------------+
// � 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frais.class.php,v 1.17.2.1 2022/02/16 08:19:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frais{
	
    public $id_frais = 0;					//Identifiant du frais annexe
	public $libelle = '';
	public $condition_frais = '';
	public $montant = '000000.00';
	public $num_cp_compta = 0;
	public $num_tva_achat = 0;
	public $add_to_new_order = 0;
	
	//Constructeur.	 
	public function __construct($id_frais= 0) {
		$this->id_frais = intval($id_frais);
		if ($this->id_frais) {
			$this->load();	
		}
	}
		
	// charge un frais annexe � partir de la base.
	public function load(){
		$q = "select * from frais where id_frais = '".$this->id_frais."' ";
		$r = pmb_mysql_query($q) ;
		if(!pmb_mysql_num_rows($r)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->condition_frais = $obj->condition_frais;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;
		$this->add_to_new_order = $obj->add_to_new_order;
	}
	
	public function get_form() {
		global $msg, $charset;
		global $frais_content_form;
		global $acquisition_gestion_tva;
		
		$content_form = $frais_content_form;
		$content_form = str_replace('!!id!!', $this->id_frais, $content_form);
		
		$interface_form = new interface_admin_form('fraisform');
		if(!$this->id_frais){
			$interface_form->set_label($msg['acquisition_ajout_frais']);
			$content_form = str_replace('!!montant!!', '', $content_form);
			$content_form = str_replace('!!cp_compta!!', '', $content_form);
		}else{
			$interface_form->set_label($msg['acquisition_modif_frais']);
			$content_form = str_replace('!!montant!!', htmlentities($this->montant, ENT_QUOTES, $charset), $content_form);
			$content_form = str_replace('!!cp_compta!!', htmlentities($this->num_cp_compta, ENT_QUOTES, $charset), $content_form);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->libelle, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!condition!!', htmlentities($this->condition_frais, ENT_QUOTES, $charset), $content_form);
		
		if ($acquisition_gestion_tva) {
			$form_tva = "<select id='tva_achat' name ='tva_achat' >";
			$q = tva_achats::listTva();
			$res = pmb_mysql_query($q);
			while ($row=pmb_mysql_fetch_object($res)) {
				$form_tva.="<option value='".$row->id_tva."' ";
				if ($this->id_frais) {
					if ($this->num_tva_achat == $row->id_tva) $form_tva.="selected ";
				}
				$form_tva.=">".$row->libelle." - ".$row->taux_tva." %</option>";
			}
			$form_tva.="</select>";
			$content_form = str_replace('!!tva_achat!!', $form_tva, $content_form);
		}
		
		$frais_form_add_to_new_order = "<input type=\"checkbox\" id=\"add_to_new_order\" name=\"add_to_new_order\" class=\"switch\" ".(!empty($this->add_to_new_order) ? "checked=\"checked\" " : "")." value=\"1\" />
        <label for=\"add_to_new_order\">".htmlentities($msg['acquisition_frais_add_to_new_order_enable'], ENT_QUOTES, $charset)."</label></td>";
		$content_form = str_replace('!!add_to_new_order!!', $frais_form_add_to_new_order, $content_form);
		
		$interface_form->set_object_id($this->id_frais)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($content_form)
		->set_table_name('frais')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle, $condition, $montant, $cp_compta, $tva_achat, $add_to_new_order;
		
		$this->libelle = stripslashes($libelle);
		$this->condition_frais = stripslashes($condition);
		$this->montant = stripslashes($montant);
		$this->num_cp_compta = stripslashes($cp_compta);
		$this->num_tva_achat = stripslashes($tva_achat);
		$this->add_to_new_order = intval($add_to_new_order);
	}
	
	public function get_query_if_exists() {
		$query = "select count(1) from frais where libelle = '".addslashes($this->libelle)."' ";
		if ($this->id_frais) $query.= "and id_frais != '".$this->id_frais."' ";
		return $query;
	}
	
	// enregistre le frais annexe en base.
	public function save(){
		if($this->libelle == '') die("Erreur de cr�ation frais"); 
		if($this->id_frais) {		
		    
			$q = "update frais set 
                    libelle ='".addslashes($this->libelle)."', 
                    condition_frais = '".addslashes($this->condition_frais)."', 
                    montant = '".addslashes($this->montant)."', 
                    num_cp_compta = '".addslashes($this->num_cp_compta)."', 
                    num_tva_achat = '".addslashes($this->num_tva_achat)."', 
                    index_libelle = ' ".addslashes(strip_empty_words($this->libelle))." ',
                    add_to_new_order = ".intval($this->add_to_new_order)."
                where id_frais = {$this->id_frais} ";
			pmb_mysql_query($q);
	
		} else {
		
			$q = "insert into frais set 
                    libelle = '".addslashes($this->libelle)."', 
                    condition_frais =  '".addslashes($this->condition_frais)."', 
                    montant = '".addslashes($this->montant)."',  
                    num_cp_compta = '".addslashes($this->num_cp_compta)."',  
                    num_tva_achat = '".addslashes($this->num_tva_achat)."', 
                    index_libelle = ' ".addslashes(strip_empty_words($this->libelle))." ',
                    add_to_new_order = ".intval($this->add_to_new_order)." ";
			pmb_mysql_query($q);
			$this->id_frais = pmb_mysql_insert_id();
		}
	
	}

	public static function check_data_from_form() {
		global $msg;
		global $libelle, $montant;
		
		//V�rification du format du montant
		$montant = str_replace(',','.',$montant);
		if (!is_numeric($montant) || $montant >9999999999.99 ) {
			error_form_message($libelle." ".$msg["acquisition_frais_error"]);
			return false;
		}
		return true;
	}
	
	//supprime un frais annexe de la base
	public static function delete($id=0) {
		global $msg;
		
		$id = intval($id);
		if($id) {
			$total1 = static::hasFournisseurs($id);
			if ($total1==0) {
				$q = "delete from frais where id_frais = '".$id."' ";
				pmb_mysql_query($q);
				return true;
			} else {
				$msg_suppr_err = $msg['acquisition_frais_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_frais_used_fou'] ;
				pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
				return false;
			}
		}
		return true;
	}
	
	//V�rifie si un frais existe			
	public static function exists($id){
		$id = intval($id);
		$q = "select count(1) from frais where id_frais = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//V�rifie si le libell� d'un frais annexe existe d�j�			
	public static function existsLibelle($libelle, $id=0){
		$id = intval($id);
		$q = "select count(1) from frais where libelle = '".$libelle."' ";
		if ($id) $q.= "and id_frais != '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}

	//V�rifie si le frais annexe est utilis� dans les fournisseurs	
	public static function hasFournisseurs($id){
		$id = intval($id);
		if (!$id) return 0;
		$q = "select count(1) from entites where num_frais = '".$id."' and type_entite = '0'";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table frais
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE frais');
		return $opt;
	}
	
	public static function getFraisForNewOrder() {
	    
	    $ret = [];
	    $q = "select 
                frais.id_frais, frais.libelle as libelle_frais, frais.condition_frais, frais.montant as montant_frais, frais.num_cp_compta as num_cp_compta_frais, 
                tva_achats.id_tva, tva_achats.libelle as libelle_tva, tva_achats.taux_tva 
            from frais left join tva_achats on frais.num_tva_achat = tva_achats.id_tva 
            where frais.add_to_new_order=1 order by frais.libelle";
	    $r = pmb_mysql_query($q);
	    if(!pmb_mysql_num_rows($r)) {
	        return [];
	    }
	    while($row=pmb_mysql_fetch_assoc($r)) {
	        $ret[] = $row;
	    }
	    return $ret;
	}
}

