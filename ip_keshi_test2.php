<?php
//exit;
/**
 * ip.php 根据IP地址显示不同广告模块
 * @author xiongzhixin (xzx747@sohu.com) 2009-12-10
 */
header("Content-type:text/html;charset=utf-8");
$iClassID = $_GET['classid'];
echo $iClassID.'xxxx';
require("Keshi_cache.php");

$aClassID_1 = explode(",",$CATEGORY[220]['arrchildid']); //男科 2010-07-26
$aClassID_2 = explode(",",$CATEGORY[341]['arrchildid']); //白癜风 2010-08-03
$aClassID_3 = explode(",",$CATEGORY[331]['arrchildid']); //性病科 2010-08-12
$aClassID_4 = explode(",",$CATEGORY[525]['arrchildid']); //心理科 2010-08-16


$aClassID_5 = explode(",",$CATEGORY[277]['arrchildid']); //眼科 2010-09-06
$aClassID_6 = explode(",",$CATEGORY[193]['arrchildid']); //妇产科 





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
	$sIP = '211.162.62.161';
	$sIpArea  = "xzx".$ipLocation->getlocation($sIP);

//	setcookie("9939_iparea",$sIpArea,null,"/",".9939.com");	
	echo "实时读取".$sIpArea."<br>";


//男科：上海定向显示 
if(in_array($iClassID,$aClassID_1))
{		
	if(strpos($sIpArea,"上海"))
	{
		$sStr = '<div style="position:fixed;left:0;top:50%;_position:absolute;_top:expression(documentElement.scrollTop + 300 + \'px\');"><a href="http://kft.zoosnet.net/LR/Chatpre.aspx?id=KFT10880110&lng=cn&r=9939.com&p=long120.cn" target="_blank"><img src="http://www.9939.com/9939/images/bs.gif"></a></div>';
	} elseif(strpos($sIpArea,"广东")) {
		$sStr = '<script type="text/javascript" src="http://www.9939.com/ads/dongguan/nk/float.js"></script>';
	}
}
//性病科：上海定向显示
elseif(in_array($iClassID,$aClassID_3)){
	if(strpos($sIpArea,"上海"))
	{
		$sStr = '<div style="position:fixed;left:0;top:50%;_position:absolute;_top:expression(documentElement.scrollTop + 300 + \'px\');"><a href="http://kft.zoosnet.net/LR/Chatpre.aspx?id=KFT10880110&lng=cn&r=9939.com&p=long120.cn" target="_blank"><img src="http://www.9939.com/9939/images/bs.gif"></a></div>';
	}
}
//白癜风：全国显示 
elseif(in_array($iClassID,$aClassID_2)){
	$sStr ='<script language="javascript" src="http://wt.zoosnet.net/JS/LsJS.aspx?siteid=LRW28553656&float=1"></script>';
}
//心理科：全国显示 
elseif(in_array($iClassID,$aClassID_4)){
	$sStr ='<script language="javascript" src="http://swt.zoosnet.net/JS/LsJS.aspx?siteid=SWT63306067&float=1"></script>';
}
//眼科
elseif(in_array($iClassID,$aClassID_5)){
	//$sStr ='<SCRIPT LANGUAGE="JavaScript" src="http://float2006.tq.cn/floatcard?adminid=8393785&sort=7"></SCRIPT>';
	//$sStr = 'var LiveAutoInvite1=\'来自首页的对话\';var LiveAutoInvite2=\'<P><FONT color=red><STRONG>在线求助眼病专家<BR></STRONG></FONT><BR>&nbsp; <STRONG>点击<FONT color=red>接受</FONT>即可与眼病专家在线咨询<BR><BR>免费电话：400-708-7082&nbsp;&nbsp; &nbsp;010-82358533</STRONG></P>\';//--></script><script language="javascript" src=" http://webservice.zoosnet.net/JS/LsJS.aspx?siteid=LZA20705105&float=1"></script>';
}
//妇产科：广东
elseif(in_array($iClassID,$aClassID_6)){
	if(strpos($sIpArea,"广东")) {
			$sStr = '<script type="text/javascript" src="http://www.9939.com/ads/dongguan/fk/float.js"></script>';
	}
}

echo "<!--xzxin -->".$sStr;
?>