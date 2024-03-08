<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


</head>

<body>
<?php

header('Content-Type: text/html; charset=UTF-8');
$enlace= mysqli_connect("localhost", "root", "Fatima$2022", "test_pmb");
 require_once ('excel_reader2.php');
 $data = new Spreadsheet_Excel_Reader("estudiantes.xls");
 $data->setOutputEncoding('CP1251');
 $data->read('estudiantes.xls');
 $cont = 0;
 for ($i = 1; $i <= 655; $i++) { 
	  $apenom=(string)$data->sheets[0]['cells'][$i][1];
	  //$apenom= utf8_decode($apenom);
	  $pieces = explode(",", $apenom);
	  $dni=$pieces[0];;
	  $dni=trim($dni);
	  $dni=(int) filter_var($dni, FILTER_SANITIZE_NUMBER_INT);
	  $ape=$pieces[1];
	  //$ape=ucwords($ape);//pasa la primera letra de cada palabra a mayuscula
	  $ape=trim($ape);
	  $nom=$pieces[2];
	  $nom=ucwords($nom);
	  $nom=trim($nom);	  
	  $mail=$pieces[3];
	  $año=$pieces[4];
	  
	  $consultaDNI=null;
	  $consulta=null;
	  $consulta2=null;
		$consultaDNI="select * from empr where empr_cb=".$dni; 
		//$resultado=mysqli_query($consultaDNI); 
		$resultado=$enlace->query($consultaDNI);
		if ($resultado->num_rows == 0) 
		{  
			//echo "No existen registros en la base de datos. $dni"; 
			$consulta = "INSERT INTO empr (empr_cb,
			empr_nom,
			empr_prenom,
			empr_mail,
			empr_year,
			empr_categ,
			empr_codestat,
			empr_location,
			empr_login,
			empr_password,
			empr_date_expiration, 
			empr_msg,
			empr_date_adhesion,
			empr_creation,
			empr_modif,
			empr_statut,
			empr_lang) VALUES ('$dni','$ape','$nom','$mail','$año',2,2,1,'$dni','$dni','2024-03-31','CARGADO POR ALVARO','2023-03-31','2023-03-31 00:00:00','2023-03-31 00:00:00',2,'es_ES')";
			//$datos=mysql_query($consulta,$enlace)or die(mysql_error());
			$enlace->query($consulta);
			$consulta=null;
			$consulta2=null;
			 $cont++;
		}
		else{
			echo "Existe $dni <br>";
			 //$cont++;
		}
  }
 
echo "Listo $cont";
$enlace->close();
?>
</body>
</html>