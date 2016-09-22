<?php
include "core/json/app/Gateway.php";

$gateway = new Gateway();

$gateway->setBaseClassPath("services/");

include("adaptersettings.php");

$gateway->service();
?>