<?php
//exit;
/**
 * ip.php ����IP��ַ��ʾ��ͬ���ģ��
 * @author xiongzhixin (xzx747@sohu.com) 2009-12-10
 */
header("Content-type:text/html;charset=utf-8");
$iClassID = $this->classid ? $this->classid : $this->info['classid'];
require("Keshi_cache.php");

$aClassID_1 = explode(",",$CATEGORY[220]['arrchildid']); //�п� 2010-07-26
$aClassID_2 = explode(",",$CATEGORY[341]['arrchildid']); //���� 2010-08-03
$aClassID_3 = explode(",",$CATEGORY[331]['arrchildid']); //�Բ��� 2010-08-12
$aClassID_4 = explode(",",$CATEGORY[525]['arrchildid']); //����� 2010-08-16
//$aClassID_5 = explode(",",$CATEGORY[278]['arrchildid']); //������ 2010-09-03
//$aClassID_6 = explode(",",$CATEGORY[282]['arrchildid']); //����� 2010-09-03

$aClassID_5 = explode(",",$CATEGORY[277]['arrchildid']); //�ۿ� 2010-09-06
$aClassID_6 = explode(",",$CATEGORY[193]['arrchildid']); //������ 



if($_COOKIE['9939_iparea'])  // xzxin 2010-05-14 ���ȶ�ȡCookie������ÿ�ζ�����IP��
{		
	$sIpArea = $_COOKIE['9939_iparea'];	
	//echo "Cookie��ȡ:".$sIpArea."<br>";
}
else
{
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
	
	$sIpArea  = "xzx".$ipLocation->getlocation($sIP);
	setcookie("9939_iparea",$sIpArea,null,"/",".9939.com");	
	//echo "ʵʱ��ȡ".$sIpArea."<br>";
}

//�пƣ��Ϻ�������ʾ 
if(in_array($iClassID,$aClassID_1))
{		
	if(strpos($sIpArea,"�Ϻ�"))
	{
		$sStr = '<div style="position:fixed;left:0;top:50%;_position:absolute;_top:expression(documentElement.scrollTop + 300 + \'px\');"><a href="http://kft.zoosnet.net/LR/Chatpre.aspx?id=KFT10880110&lng=cn&r=9939.com&p=long120.cn" target="_blank"><img src="http://www.9939.com/9939/images/bs.gif"></a></div>';
	} elseif(strpos($sIpArea,"�㶫")) {
		$sStr = '<script type="text/javascript" src="http://www.9939.com/ads/dongguan/nk/float.js"></script>';
	}
}
//�Բ��ƣ��Ϻ�������ʾ
elseif(in_array($iClassID,$aClassID_3)){
	if(strpos($sIpArea,"�Ϻ�"))
	{
		$sStr = '<div style="position:fixed;left:0;top:50%;_position:absolute;_top:expression(documentElement.scrollTop + 300 + \'px\');"><a href="http://kft.zoosnet.net/LR/Chatpre.aspx?id=KFT10880110&lng=cn&r=9939.com&p=long120.cn" target="_blank"><img src="http://www.9939.com/9939/images/bs.gif"></a></div>';
	}
}
//���磺ȫ����ʾ 
elseif(in_array($iClassID,$aClassID_2)){
	$sStr ='<script language="javascript" src="http://wt.zoosnet.net/JS/LsJS.aspx?siteid=LRW28553656&float=1"></script>';
}
//����ƣ�ȫ����ʾ 
elseif(in_array($iClassID,$aClassID_4)){
	$sStr ='<script language="javascript" src="http://swt.zoosnet.net/JS/LsJS.aspx?siteid=SWT63306067&float=1"></script>';
}
//�ۿ�
elseif(in_array($iClassID,$aClassID_5)){
	//$sStr ='<SCRIPT LANGUAGE="JavaScript" src="http://float2006.tq.cn/floatcard?adminid=8393785&sort=7"></SCRIPT>';
	//$sStr = 'var LiveAutoInvite1=\'������ҳ�ĶԻ�\';var LiveAutoInvite2=\'<P><FONT color=red><STRONG>���������۲�ר��<BR></STRONG></FONT><BR>&nbsp; <STRONG>���<FONT color=red>����</FONT>�������۲�ר��������ѯ<BR><BR>��ѵ绰��400-708-7082&nbsp;&nbsp; &nbsp;010-82358533</STRONG></P>\';//--></script><script language="javascript" src=" http://webservice.zoosnet.net/JS/LsJS.aspx?siteid=LZA20705105&float=1"></script>';
}
//�����ƣ��㶫
elseif(in_array($iClassID,$aClassID_6)){
	if(strpos($sIpArea,"�㶫")) {
			$sStr = '<script type="text/javascript" src="http://www.9939.com/ads/dongguan/fk/float.js"></script>';
	}
}
echo "<!--xzxin -->".$sStr;
?>