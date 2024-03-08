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
$mail = new PHPMailer;
   $mail->isSMTP();   
   $mail->Host = 'mail.consejosalta.org.ar';
   $mail->SMTPAuth = true;
   $mail->Username = 'noresponder@consejosalta.org.ar';
   $mail->Password = 'Revista10';
   $mail->setFrom('noresponder@consejosalta.org.ar', 'Your Name');
   $mail->addReplyTo('noresponder@consejosalta.org.ar', 'Your Name');
   $mail->addAddress('alvaroernestofernandez@gmail.com', 'Receiver Name');
   $mail->Subject = 'Checking if PHPMailer works';
   $mail->isHTML(true);
   $mail->Body = "<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'/> 
</head>
<body>
<p><Strong>Estimado/a:</strong></p>

<p>Me dirijo a Usted, a fin de informarle que puede</p>


<p>Saludos Cordiales.</p>
</body>
</html>";
   //$mail->addAttachment('attachment.txt');
   if (!$mail->send()) {
       echo 'Mailer Error: ' . $mail->ErrorInfo;
   } else {
       echo 'The email message was sent.';
   }
ob_end_flush();
?>
</body>
</html>
