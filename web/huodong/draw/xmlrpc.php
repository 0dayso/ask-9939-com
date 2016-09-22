<?php
	include "core/xmlrpc/app/Gateway.php";
	
	$gateway = new Gateway();
	
	$gateway->setBaseClassPath("services/");
	
	include("adaptersettings.php");
	
	$gateway->service();
?>