<?php
$loginform=genere_form_connexion_empr();
print $loginform;
print "<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js\" type=\"text/javascript\"></script>
 
	<script>
	// Esta función te mueve el scroll hasta llegar la id indicado
	// Tiene que recibir el id
	function goToId(idName)
	{
		if($(\"#\"+idName).length)
		{
			var target_offset = $(\"#\"+idName).offset();
			var target_top = target_offset.top;
			$('html,body').animate({scrollTop:target_top},{duration:\"slow\"});
		}
	}
 
	$(document).ready(function(){
			goToId('main');
	});
	</script>";
?>