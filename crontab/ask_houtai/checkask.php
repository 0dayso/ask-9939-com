<?php 
//启动文件
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include_once(ET_ROOT.'/common.inc.php');
define("MODELS_PATH",ET_ROOT."/../../application/models");
define("APP_DATA_PATH",ET_ROOT."/../../data");//缓存文件路径
require_once(APP_DATA_PATH.'/data_censorvalue.php');
L("程序启动");
$db=D();
$askid = 1;
if (file_exists(ET_ROOT.'/checkaskid.log')) {
	$askid = file_get_contents(ET_ROOT.'/checkaskid.log');
}
$askid = intval($askid);
echo $sqltable = "select tablename, tablename_answer from `wd_ask_tablespace` where minid <= ".$askid." and maxid >= ".$askid;
$qt = $db->Q($sqltable);
$tablename = '';
while ($tmp = $db->F($qt)) {
	$tablename = $tmp['tablename'];
}
if (empty($tablename)) {
	echo date('Y-m-d H:i:s').'---done';
	exit;
}
$sql="select id, title, content FROM `wd_ask_history_1` where id > ".$askid." order by id asc limit 0,500";
$query=$db->Q($sql);
$temp="";
$strid = 0;
while ($data=$db->F($query)) {
	$lastid = $data['id'];
	//验证字符串是否含有非法词语
	$checktitle = clearstr($data['title']);
	if(!isSafeStr($checktitle) || preg_match('/&#[0-9a-zA-Z]{5};/', $checktitle)) {
		$strid .= ','.$data['id'];
		continue;
	}
	$checkcons = clearstr($data['content']);
	if(!isSafeStr($checkcons) || preg_match('/&#[0-9a-zA-Z]{5};/', $checkcons)) {
		$strid .= ','.$data['id'];
//		$db->Q($sqldel);
	}
	
}
//备份数据
$sqlcopy = "insert into tmp_ffkeyword select * from ".$tablename." where id in (".$strid.")";
$db->Q($sqlcopy);
//删除问题
$sqldel = "delete from ".$tablename." where id in (".$strid.")";
$db->Q($sqldel);
//echo $sqldel = "delete from ".$tablename." where id in (".$strid.")";
//echo "\n";
//		$db->Q($sqldel);
file_put_contents(ET_ROOT.'/checkaskid.log', $lastid);

/**
 *
 * 清理字符串
 */
function clearstr($str) {
	//去除空格
	$str = str_replace(array(' ','　','&nbsp;','&nbsp',"\r","\n"),'', $str);
	if (empty($str)) {
		return $str;
	}
	//去除所有HTML标签
	$str = strip_tags($str);
	//去除中文标点符号
	$str = str_replace(array('_','=','*','^','%','$','@','!',':',',',';','?','~','`','\\'),'', $str);
	$str = preg_replace('/\xa3([\xa1-\xfe])/e','',$str);
	//转义特殊字符
	$searcharr = array('>','<');
	$replacearr = array('&gt;','&lt;');
	$str = str_replace($searcharr, $replacearr, $str);
	return $str;
}

function isSafeStr($str) {//$key 1普通用户 2医生用户 3是付费医生
		global $_SGLOBAL;
		//判断是否里面有网址
		if(preg_match('/\.com|\.cn|\.mobi|\.co|\.net|\.so|\.org|\.gov|\.tel|\.tv|\.biz|\.cc|\.hk|\.name|\.info|\.asia|\.me/', $str)) {
			return false;
		}
	   //电话 qq号
		if(preg_match('/(\(\d{3,4}\)|\d{3,4}-)?\d{6,8}/', $str)) {
			return false;
		}
		//判断是否里面有中文
		if(!preg_match('/([\x81-\xfe][\x40-\xfe])/', $str)) {
			return false;
		}
		//读取缓存
		foreach($_SGLOBAL['censorvalue'][1] as $val) {
			if($val<>""){
				if(strpos($str,$val)!==false) {
					echo $val."\n";
					return false;
				}
			}
		}
		return true;
	}