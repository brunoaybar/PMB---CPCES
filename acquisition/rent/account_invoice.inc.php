<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account_invoice.inc.php,v 1.3 2021/04/08 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $id, $selected_objects;

require_once($class_path.'/rent/rent_invoice.class.php');

if (empty($id) && empty($selected_objects)) {print "<script> self.close(); </script>" ; die;}

if(empty($id) && !empty($selected_objects)) {
	$commands = explode(',', $selected_objects);
	print "<script type='text/javascript' src='".$base_path."/javascript/popup.js'></script>";
	foreach ($commands as $id) {
		print "
		<script type='text/javascript'>
			var url = './pdf.php?pdfdoc=account_invoice&id=".$id."';
			openPopUp(url,'print_PDF_".$id."', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes');
		</script>";
	}
	print "<script> self.close(); </script>";
} else {
	$invoice=new rent_invoice($id);
	$invoice->gen_invoice();
}