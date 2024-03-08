<?php
header('Content-Type: text/html; charset=UTF-8');
$enlace= mysqli_connect("localhost", "root", "Fatima$2022", "test_pmb");
 require_once ('excel_reader2.php');
 $data = new Spreadsheet_Excel_Reader("libros2023.xls");
 $data->setOutputEncoding('CP1251');
 $data->read('libros2023.xls');
 $cont = 0;
 $base='766fd83f0d252574e4ab3206972ec3e9';
 for ($i = 1; $i <= 4618; $i++) { 
	$baja=(string)$data->sheets[0]['cells'][$i][3];
	if (strpos($baja, '*') == false) {
		$id=$data->sheets[0]['cells'][$i][2];
		$id=trim($id);
		$id=(int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);	
		$cote=(string)$data->sheets[0]['cells'][$i][4];
		$autor=$data->sheets[0]['cells'][$i][9];
		$titulo=$data->sheets[0]['cells'][$i][10];
		$paginas=$data->sheets[0]['cells'][$i][11];
		$coleccion=$data->sheets[0]['cells'][$i][12];
		$editorial=$data->sheets[0]['cells'][$i][13];
		$editorial_ciudad=$data->sheets[0]['cells'][$i][14];
		$edicion=$data->sheets[0]['cells'][$i][16];
		$anio=$data->sheets[0]['cells'][$i][17];
		$descriptores=$data->sheets[0]['cells'][$i][23];
		$notas=$data->sheets[0]['cells'][$i][24];
		$sql_notice="INSERT INTO `notices` (`notice_id`, `typdoc`, `tit1`, `tit2`, `tit3`, `tit4`, `tparent_id`, `tnvol`, `ed1_id`, `ed2_id`, `coll_id`, `subcoll_id`, `year`, `nocoll`, `mention_edition`, `code`, `npages`, `ill`, `size`, `accomp`, `n_gen`, `n_contenu`, `n_resume`, `lien`, `eformat`, `index_l`, `indexint`, `index_serie`, `index_matieres`, `niveau_biblio`, `niveau_hierar`, `origine_catalogage`, `prix`, `index_n_gen`, `index_n_contenu`, `index_n_resume`, `index_sew`, `index_wew`, `statut`, `commentaire_gestion`, `create_date`, `update_date`, `signature`, `thumbnail_url`, `date_parution`, `opac_visible_bulletinage`, `indexation_lang`, `map_echelle_num`, `map_projection_num`, `map_ref_num`, `map_equinoxe`, `notice_is_new`, `notice_date_is_new`, `opac_serialcirc_demande`, `num_notice_usage`, `is_numeric`) VALUES
		(".$id.", 'a', '".$titulo."', '', '', '', 0, '', 2, 0, 2, 0, '".$anio."', '', '".$edicion."', '', '".$paginas."', '', '', '', '".$notas."', '', '', '', '', '', 0, '', '', 'm', '0', 1, '', '', '', '', ' elementos calculo actuarial ', '', 1, '', '2023-08-18 11:58:02', '2023-10-10 02:30:12', '0', '', '1978-01-01', 0, '', 0, 0, 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0)";
		$enlace->query($sql_notice);
		$sql_ejemplar="INSERT INTO `exemplaires` (`expl_id`, `expl_cb`, `expl_notice`, `expl_bulletin`, `expl_typdoc`, `expl_cote`, `expl_section`, `expl_statut`, `expl_location`, `expl_codestat`, `expl_date_depot`, `expl_date_retour`, `expl_note`, `expl_prix`, `expl_owner`, `expl_lastempr`, `last_loan_date`, `create_date`, `update_date`, `type_antivol`, `transfert_location_origine`, `transfert_statut_origine`, `expl_comment`, `expl_nbparts`, `expl_retloc`, `expl_abt_num`, `transfert_section_origine`, `expl_ref_num`, `expl_pnb_flag`) VALUES
		(".$id.",'".$id."', ".$id.", 0, 1, '".$cote."', 27, 1, 1, 10, '0000-00-00', '0000-00-00', '', '', 2, 0, '0000-00-00', '2023-10-10 01:27:19', '2023-10-10 04:27:19', 0, 1, 1, '', 1, 0, 0, 27, 0, 0)";
		$enlace->query($sql_ejemplar);
	}
	}
 $enlace->close();
?>
</body>
</html>