<?php
/**
 * @desc 区域定向
 * @author xiongzhixin (xzx747@sohu.com) 2010-07-26
 */

 if($_SERVER['HTTP_CDN_SRC_IP'])
	$sIP = $_SERVER['HTTP_CDN_SRC_IP'];
elseif($_SERVER['HTTP_X_FORWARDED_FOR'])
	$sIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
	$sIP = $_SERVER['REMOTE_ADDR'];


$sIP = $_GET['ip'] ? $_GET['ip'] : $sIP;
if(!$sIP) exit;
header("Content-type:text/html;charset=utf-8");
define(ROOT,dirname(__FILE__));
require_once(ROOT."/class/IpLocation.lib.php");
$ipLocation = new ipLocation(ROOT."/class/ipdata.xzx");
$sIpArea  = $ipLocation->getlocation($sIP);
setcookie("9939_iparea",$sIpArea,null,"/",".9939.com");	
echo $sIpArea."<br>";
?>
