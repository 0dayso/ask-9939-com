<?php
//exit;
/**
 * ip.php 根据IP地址显示不同广告模块
 * @author xiongzhixin (xzx747@sohu.com) 2009-12-10
 */
header("Content-type:text/html;charset=utf-8");
//print_r($this->classid);
//print_r($aClassID);exit;


define(ROOT,dirname(__FILE__));
require_once(ROOT."/class/IpLocation.lib.php");
$ipLocation = new ipLocation(ROOT."/class/ipdata.xzx");

// xzxin 2010-08-03
if($_SERVER['HTTP_CDN_SRC_IP'])
	$sIP = $_SERVER['HTTP_CDN_SRC_IP'];
elseif($_SERVER['HTTP_X_FORWARDED_FOR'])
	$sIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
	$sIP = $_SERVER['REMOTE_ADDR'];

	$sIP = "222.66.160.123";

$sIpArea  = $ipLocation->getlocation($sIP);
setcookie("9939_iparea",$sIpArea,null,"/",".9939.com");	
echo "实时读取".$sIP."--".$sIpArea."<br>";


//echo $sIpArea;
if(strstr($sIpArea,"上海"))
{
	$sStr = '<div style="position:fixed;left:0;top:50%;"><a href="http://kft.zoosnet.net/LR/Chatpre.aspx?id=KFT10880110&lng=cn&e=9939.com" target="_blank"><img src="http://www.9939.com/9939/images/bs.gif"></a></div>';
}

echo "<!--xzxin 2010-07-26-->".$sStr."";
?>