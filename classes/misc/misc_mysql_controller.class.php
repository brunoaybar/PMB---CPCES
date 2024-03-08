<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_mysql_controller.class.php,v 1.1.2.2 2022/03/22 13:16:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/misc/misc_controller.class.php");

class misc_mysql_controller extends misc_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = '';
	
	protected static function get_line_mysql_info_from_query($label, $query) {
		return "
			<div class='row'>
				<label class='etiquette'>".$label."</label>
			</div>
		  	<div class='row'>
				".pmb_sql_value($query)."
			</div>";
	}
	
	protected static function get_line_mysql_subtab($label, $url_extra) {
		global $base_path;
		return "
			<div class='row'>
				<a href='".$base_path."/admin.php?categ=misc&sub=mysql".$url_extra."'>".$label."</a>
			</div>";
	}
	
	public static function proceed_info() {
		global $info, $msg, $database_window_title, $tabindexref;
		
		switch ($info) {
			case 'phpinfo':
				echo window_title($database_window_title."Php - Info");
				echo phpinfo();
				break;
			case 'table_index':
				echo window_title($database_window_title."Tables - indexes");
				require_once ("./tables/dataref.inc.php");
				$pb="";
				foreach ($tabindexref as $table=>$key_names) {
					$rqti="show index from $table";
					$resi=pmb_mysql_query($rqti) or die(pmb_mysql_error()."<br />".$rqti);
					$cles_reelles = array();
					for ($i=0;$i<pmb_mysql_num_rows($resi);$i++) {
						$key_name=pmb_mysql_result($resi,$i,'Key_name');
						$col_name=pmb_mysql_result($resi,$i,'Column_name');
						$cles_reelles[$key_name][]=$col_name;
					}
					foreach ($key_names as $key_name=>$col_names) {
						if ($cles_reelles[$key_name]) {
							for($j=0;$j<count($col_names);$j++) {
								if (array_search($col_names[$j],$cles_reelles[$key_name])===false) {
									$pb .= "<br />--------- $table $key_name ".$col_names[$j]." missing";
								}
							}
						} else {
							$pb .= "<br />-- $table $key_name missing";
						}
					}
				}
				
				if ($pb) echo "<b>".$msg['admin_info_table_index_pb']."</b><br />".$pb;
				else echo $msg['admin_info_table_index_ok'];
				break;
				/*case 'verif_base':
				 echo window_title($database_window_title.$msg[verification_verif_base]);
				 require_once("$base_path/admin/misc/verifications/verif_base.inc.php");
				 break;*/
			case 'mysqlinfo':
				echo window_title($database_window_title."MySQL - Info");
				
				echo static::get_line_mysql_info_from_query($msg['sql_info_notices'], "select count(*) as nb from notices");
				echo static::get_line_mysql_info_from_query($msg['sql_info_exemplaires'], "select count(*) as nb from exemplaires");
				echo static::get_line_mysql_info_from_query($msg['sql_info_bulletins'], "select count(*) as nb from bulletins");
				echo static::get_line_mysql_info_from_query($msg['sql_info_authors'], "select count(*) as nb from authors");
				echo static::get_line_mysql_info_from_query($msg['sql_info_publishers'], "select count(*) as nb from publishers");
				echo static::get_line_mysql_info_from_query($msg['sql_info_empr'], "select count(*) as nb from empr");
				echo static::get_line_mysql_info_from_query($msg['sql_info_pret'], "select count(*) as nb from pret");
				echo static::get_line_mysql_info_from_query($msg['sql_info_pret_archive'], "select count(*) as nb from pret_archive");
				
				echo "<hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Database name, host and user</label>
				</div>
			  <div class='row'>
					".DATA_BASE." on ".SQL_SERVER.", user=".USER_NAME."
					</div>
			  <div class='row'>
				<label class='etiquette' >MySQL Server Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_server_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Client Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_client_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Host Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_host_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Protocol Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_proto_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Stat. Information</label>
				</div>
			  <div class='row'>
					".str_replace('  ','<br />',pmb_mysql_stat())."</div><hr />";
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Variables</label>
				</div>
			  <div class='row'><table>" ;
				$result = pmb_mysql_query('SHOW VARIABLES');
				$parity=0 ;
				while ($row = pmb_mysql_fetch_assoc($result)) {
					if ($parity % 2) $pair_impair = "even";
					else $pair_impair = "odd";
					$parity+=1;
					echo "<tr class='$pair_impair'><td>".$row['Variable_name']."</td><td>".$row['Value']."</td></tr>";
				}
				echo "</table></div>" ;
				break;
			case '':
			default:
				print static::get_line_mysql_subtab($msg['719'], '&action=CHECK');
				print static::get_line_mysql_subtab($msg['720'], '&action=ANALYZE');
				print static::get_line_mysql_subtab($msg['721'], '&action=REPAIR');
				print static::get_line_mysql_subtab($msg['722'], '&action=OPTIMIZE');
				print static::get_line_mysql_subtab($msg['admin_info_mysql'], '&info=mysqlinfo');
				print static::get_line_mysql_subtab($msg['admin_info_php'], '&info=phpinfo');
				print static::get_line_mysql_subtab($msg['admin_info_table_index'], '&info=table_index');
// 				print static::get_line_mysql_subtab($msg['admin_info_verif_base'], '&info=verif_base');
				break;
		}
	}
	
	public static function proceed($id=0) {
		global $action;
		global $table;
		global $pmb_set_time_limit;
		
		$id = intval($id);
		if($action) {
			@set_time_limit($pmb_set_time_limit);
			$db = DATA_BASE;
			$tables = pmb_mysql_list_tables($db);
			$num_tables = pmb_mysql_num_rows($tables);
			
			$i = 0;
			while($i < $num_tables) {
				$table[$i] = pmb_mysql_tablename($tables, $i);
				$i++;
			}
			
			echo "<table >";
			foreach ($table as $cle => $valeur) {
				$requete = $action." TABLE ".$valeur." ";
				$res = pmb_mysql_query($requete);
				$nbr_lignes = pmb_mysql_num_rows($res);
				$nbr_champs = @pmb_mysql_num_fields($res);
				if($nbr_lignes) {
					if(!$cle) {
						for($i=0; $i < $nbr_champs; $i++) {
							printf("<th>%s</th>", pmb_mysql_field_name($res, $i));
						}
					}
					for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						echo "<tr>";
						foreach($row as $col) {
							if(!$col) $col="&nbsp;";
							print "<td>$col</td>";
						}
						echo "</tr>";
					}
				}
			}
			echo "</table>";
			
		}
	}
}