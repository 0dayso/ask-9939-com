<?php
//exit;
/**
 * ip.php 根据IP地址显示不同广告模块
 * @author xiongzhixin (xzx747@sohu.com) 2009-12-10
 */
header("Content-type:text/html;charset=utf-8");
$sFile = "ask_right";

define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录

$sFile = ROOT."public/".$sFile;


//require_once($sFile.".html");
//exit;



if($_GET['a'] == "bj" )
{
	//echo "北京或上海或广州<br>";
	require_once($sFile.".html");
	exit;
}
elseif($_GET['a'] == "zhejiang") 
{
	//echo "其他地方<br>";
	require_once($sFile."_zhejiang.html");
}
else
{
	if($_COOKIE['9939_iparea'])  // xzxin 2010-05-14 优先读取Cookie，避免每次都访问IP库
	{		
		$sIpArea = $_COOKIE['9939_iparea'];	
		//echo "Cookie读取:".$sIpArea."<br>";
	}
	else
	{
		//define(ROOT,dirname(__FILE__));
		define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
		require_once(ROOT."/class/IpLocation.lib.php");
		$ipLocation = new ipLocation(ROOT."/class/ipdata.xzx");
	$sIP =  $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$sIpArea  = "xzx".$ipLocation->getlocation($sIP);
		setcookie("9939_iparea",$sIpArea,null,"/",".9939.com");	
		//echo "实时读取".$sIpArea."<br>";
	}

	//echo $sIpArea;
	if(strpos($sIpArea,"浙江")!==false)
	{
		//echo "浙江<br>";
		require_once($sFile."_zhejiang.html");	
	}	
	elseif(strpos($sIpArea,"北京")!==false)
	{
		require_once($sFile.".html");
	}
	else 
	{
		require_once($sFile."_other.html");
	}
	//require_once($sFile.".html");
}
?>