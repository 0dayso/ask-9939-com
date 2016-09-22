<?php
//exit;
/**
 * ip.php 根据IP地址显示不同广告模块
 * @author wangxiaoguang  2010-10-22
 */
header("Content-type:text/html;charset=utf-8");

require("data/cache_wd_keshi.php");
require("data/Keshi_ads_cache.php");
//$iClassID=$_GET['id'];//得到的是科室id
//print_r($iClassID);exit;
//$iClassID = $this->classid ? $this->classid : $this->info['classid'];

$iClassID = 232;

$pid=explode(",",$CATEGORY[$iClassID]['arrparentid']);//根据科室id得到该科室的父id
$ads=$KESHI_ADS[0][$iClassID];//得到的是可是广告的记录
 
//print_r($ads);exit;

/** start 读取用户的地理位置 */
if($_COOKIE['9939_iparea'])  // xzxin 2010-05-14 优先读取Cookie，避免每次都访问IP库
{		
	
	$sIpArea = $_COOKIE['9939_iparea'];	
	//print_r($sIpArea);exit;
	//echo "Cookie读取:".$sIpArea."<br>";
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
    //$sIpArea = $_COOKIE['9939_iparea'];	
	//print_r($sIpArea);exit;
	//echo "实时读取".$sIpArea."<br>";
}
/** end 读取用户的地理位置 */

//$sIpArea = "局域网对方和您在同一内部网";

//print_r($ads);
if(is_array($ads))
{
	$area=$ads[0]['ad_area'];
	if($area=='全国')
	{
		//echo "asdf";exit;
		$adCode=is_show($ads);
	}
	else
	{
		if(is_show_area($ads,$sIpArea))
		{
			$adCode=$ads[0]['ad_code'];
		}
	}
}

if(!$adCode && isset($pid))
{
	foreach($pid as $key=>$val)
	{
		$ads=$KESHI_ADS[0][$val];
		if($ads)
		{
			if(is_show_area($ads,$sIpArea))
			{
				$adCode=is_show($ads);
				break;
			}
		}
	}
}
//输出广告
if (isset($adCode))
{
	echo stripslashes($adCode);
}




//判断区域显示广告
function is_show_area($ad_area_str,$sIpArea)
{
//	global $sIpArea;
	$ad_areas = explode(',', $ad_area_str[0]['ad_area']);
	//echo $sIpArea;
	//print_r($ad_areas);
	foreach ($ad_areas as $ad_area)
	{
		if (is_numeric(strpos($sIpArea, $ad_area))) 
		{
			return TRUE;
		}
	}
	return FALSE;
}


//查找父id包含的广告
function is_show($tmpad)
{
	foreach ($tmpad as $key => $val)
	{
		if(trim($val['ad_code']))
		{
			$code=trim($val['ad_code']);
		}
	} 
	return $code;
}

?>