<html>
<head>
<title>Buscar Socio</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> <!--agregado para corregir error de simbolos en el pdf generado -->
</head>
<body>
<div align="Center">
<form action="buscarsocio.php" method="post">
Buscar: <input name="palabra">
<input type="submit" name="buscador" value="Buscar">
</form>
</div>
<?php
include_once ("../../basedato-pmb.php");
global $dni;
if (isset($_POST['buscador']))
{ 
// Tomamos el valor ingresado
$buscar = $_POST['palabra'];
$valor=null;
// Si está vacío, lo informamos, sino realizamos la búsqueda
if(empty($buscar))
{
echo "No se ha ingresado una cadena a buscar";
}else{
$sql = "SELECT empr_nom, empr_prenom, empr_cb,id_empr FROM empr WHERE empr_cb = '".$buscar."'";
$r = consulta($sql); 
$row_prestamo4=null;
	     	  if (mysql_num_rows($r)!=0){
                while ($row_prestamo4=mysql_fetch_array($r)) { 
                 $nom=NULL;
				 $ape=NULL;
				 $dni=NULL;
				 $id=NULL;
                 $nom=$row_prestamo4["empr_prenom"];
				 $ape=$row_prestamo4["empr_nom"];
				 $dni=$row_prestamo4["empr_cb"];
				 $id=$row_prestamo4["id_empr"];
                }
					$sql2 = "SELECT empr_custom_list_lib FROM empr_custom_values inner join empr_custom_lists on empr_custom_small_text=empr_custom_list_value WHERE empr_custom_values.empr_custom_champ='1' and empr_custom_origine='".$id."'";
					$r2 = consulta($sql2); 
					$row_prestamo5=null;
				  if (mysql_num_rows($r2)!=0){
					while ($row_prestamo5=mysql_fetch_array($r2)) { 
					 $carr=NULL;
					 $carr=$row_prestamo5["empr_custom_list_lib"];
					}
				}
					/*$sql3 = "SELECT empr_custom_integer FROM empr_custom_values WHERE empr_custom_champ='9' and empr_custom_origine='".$id."'";
					$r3 = consulta($sql3); 
					$row_prestamo6=null;
				  if (mysql_num_rows($r3)!=0){
					while ($row_prestamo6=mysql_fetch_array($r3)) { 
					 $lu=NULL;
					 $lu=$row_prestamo6["empr_custom_integer"];
					}
				}*/
				$sql2 = "SELECT empr_custom_integer FROM empr_custom_values WHERE empr_custom_champ='9' and empr_custom_origine='".$id."'";
					$r2 = consulta($sql2); 
					$row_prestamo5=null;
				  if (mysql_num_rows($r2)!=0){
					while ($row_prestamo5=mysql_fetch_array($r2)) { 
					 $lu=NULL;
					 $lu=$row_prestamo5["empr_custom_integer"];
					}
				}
			  }
			  else{
			    ?>
            <div onclick="window.opener.document.getElementById('Datos_Garante').value= '' ; window.close()">El numero de usuario ingresado no es valido</div> 
                <?php
			  }
			   
   $valor=
   $valor=$nom." ".$ape."; D.N.I: ".$dni."; Carrera: ".$carr."; L.U ".$lu."";
}

?>

<div align="Center"><div onmouseover="this.style.backgroundColor='#faa918'" onmouseout="this.style.backgroundColor='#fff'" onclick="window.opener.document.getElementById('Datos_Garante').value= '<?php echo $ape.", ".$nom?>' ; window.opener.document.getElementById('Dato_dni').value= '<?php echo $dni?>'; window.opener.document.getElementById('Dato_carrera').value= '<?php echo $carr?>'; window.opener.document.getElementById('Dato_lu').value= '<?php echo $lu?>'; window.close()"> <?php  echo $valor ?> </div> </div>
<?php } else{}?>

</body>
</html>