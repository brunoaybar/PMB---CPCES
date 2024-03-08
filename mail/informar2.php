<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/> 
</head>
<body>
<?php
ob_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once('PHPMailer_5.2.4/class.phpmailer.php');
$mail = new PHPMailer(); 
$mail->IsSMTP();
 

//------------------------------------------------------
  $correo_emisor="alvaroernestofernandez@gmail.com";     
  $nombre_emisor="Biblioteca \"Prof. Eusebio Cleto del Rey\" - Facultad de Ciencias Económicas, Jurídicas y Sociales";              
  //$contrasena="patriciorey1977";          
  $contrasena="joapatzewzuarqxe";          
  $correo_destino="carinopatriciarosana@gmail.com";    
  $nombre_destino="Estimado/a";                
//--------------------------------------------------------
 //$mail->SMTPDebug  = 2; // muestra la conexion al smtp                  
  $mail->SMTPAuth   = true;                  
  $mail->SMTPSecure = "tls";
  //$mail->SMTPSecure = "ssl";
  $mail->Host       = "smtp.gmail.com";  
  $mail->Mailer = "smtp";
  //$mail->Host       = "ciunsa.unsa.edu.ar";  
  //$mail->Port       = 25;  
  $mail->Port       = 587;  
  //$mail->Port       = 465;                  
  $mail->Username   = $correo_emisor;       
  $mail->Password   = $contrasena;           
  $mail->AddReplyTo($correo_emisor, $nombre_emisor);
  $mail->AddAddress($correo_destino, $nombre_destino);
  $mail->SetFrom($correo_emisor, $nombre_emisor);
  $mail->Subject = "Biblioteca \"Prof. Eusebio Cleto del Rey\" - Facultad de Ciencias Económicas, Jurídicas y Sociales";
  $cuerpo="<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'/> 
</head>
<body>
<table style='text-align: left; width:100%; height:100px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='vertical-align: top;'><img src='http://biblioeco.unsa.edu.ar/pmb/images/unsa.jpg' width='80' height='80' align='left'><br></td><td style='vertical-align: top; text-align:center;'><h2>Biblioteca \"Prof. Eusebio Cleto del Rey\" - Facultad de Ciencias Económicas, Jurídicas y Sociales</h2><br></td><td style='vertical-align: top;'><img src='http://biblioeco.unsa.edu.ar/pmb/images/facultad.jpg' width='80' height='80'><br></td></tr></tbody></table> <br>
<p><Strong>Estimado/a:</strong></p>

<p>Me dirijo a Usted, a fin de informarle que puede pasar a retirar su certificado de libre deuda 
por la Biblioteca \"Prof. Eusebio Cleto del Rey\" - Facultad de Ciencias Económicas, Jurídicas y Sociales.</p>

<p>Los horarios de atención son de lunes a viernes de 9:00 a 13:00 Hs. y de 14:00 a 19:00 Hs.</p>

<p>Saludos Cordiales.</p>
<p><strong>Dirección de Biblioteca</strong></p>
</body>
</html>";
  $mail->MsgHTML($cuerpo);
  //$mail->AddStringAttachment($doc, 'doc.pdf', 'base64', 'application/pdf');      // Archivos Adjuntos
  $mail->CharSet = 'UTF-8';
  $exito = $mail->Send();
  if($exito){
  echo "<h1 class='MsoSubtitle' style='text-align: center;'>Exito en el envio del mail</h1>";}
  else{
  echo "<h1 class='MsoSubtitle' style='text-align: center;'>Fallo envio de mail</h1>";
  }
ob_end_flush();
?>
</body>
</html>
