<?php
//exit;
/**
 * ip.php 根据IP地址显示不同广告模块
 * @author xiongzhixin (xzx747@sohu.com) 2009-12-10
 */
header("Content-type:text/html;charset=utf-8");
$sFile = $_GET['file'] ? $_GET['file'] : "ask_right";

$sFile = "public/".$sFile;


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
		define(ROOT,dirname(__FILE__));
		require_once(ROOT."/class/IpLocation.lib.php");
		$ipLocation = new ipLocation(ROOT."/class/ipdata.xzx");
	    $sIP =  $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$sIpArea  = "xzx".$ipLocation->getlocation($sIP);
		setcookie("9939_iparea",$sIpArea,null,"/",".9939.com");
		//echo "实时读取".$sIpArea."<br>";
	}

	if(isset($_GET['beijing']))
    {
        if(is_numeric(strpos($sIpArea,"北京")))
        {
                require_once($sFile.".html");
        }
        else if(strpos($sIpArea,"浙江") && in_array($sFile, array('article_body_2', 'article_right')) ){
		    require_once($sFile."_zhejiang.html");
        }
        else
        {
            //echo "其他地方<br>";
            require_once($sFile."_other.html");
        }
    }
	elseif(is_numeric(strpos($sIpArea,"北京")) || is_numeric(strpos($sIpArea,"上海")) || is_numeric(strpos($sIpArea,"广州")))
	{
		//echo "北京或上海或广州<br>";
		require_once($sFile.".html");
	}
    else if(strpos($sIpArea,"浙江") && in_array($sFile, array('article_body_2', 'article_right')) ){
		require_once($sFile."_zhejiang.html");
	}
	else
	{
		//echo "其他地方<br>";
		require_once($sFile."_other.html");
	}
}
?>