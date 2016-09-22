<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect();
require 'func_huodong.php';
header("Content-type: text/html; charset=utf-8");
deldir('attachment');exit;
?>