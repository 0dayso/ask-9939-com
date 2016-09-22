<?php
/*
	分页缓存
*/
//$cid = intval($_GET['cid']);

define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');

$cacheName = 'ask_new_'.$sclassid.'_'.$status;
$cacheArr = loadCache($cacheName);



/**
if(!$cacheArr || $sclassid == 537) {
	$sql = "SELECT count(id) as count FROM `wd_ask` where $where";
	$result = mysql_query($sql);
	if($result) {
		if($row = mysql_fetch_assoc($result)){
			$cacheArr = $row['count'];
		}
		//saveCache($cacheName,$cacheArr,8);//缓存8小时
	}
}
**/
?>
