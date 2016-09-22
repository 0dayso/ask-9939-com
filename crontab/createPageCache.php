<?php
/**
 * @desc 生成分页缓存
 * @author xzxin 
 * @date 2010-09-21
 */
//echo "ok";exit;
//file_put_contents("log/".date("Y-m-d").".txt","etst".chr(13));exit;

require_once('config.php');
require('../data/cache_wd_keshi_fenli.php');


$sclassid = intval($_GET['cid']);
$aStatus = array(0,1,2,3,4);

if($sclassid!=0){
		$w = "classid in (".$CATEGORY[$sclassid][arrchildid].") and ";
}

$iStartTime = time();

foreach ($aStatus as $status)
{
	if($status<>2){
		$where = $w." status = $status";}
	else{
		$where = $w." point != 0";
	}
	
	if($status==3)//所有，没有状态
		$where = $w.' 1';
	
	if($status==4)//0回复问题
		$where = $w.'  `id` IN (SELECT `askid` FROM `wd_ask_answernum` WHERE `answernum`=0)';
	
	
	$cacheName = 'ask_new_'.$sclassid.'_'.$status;
	$sql = "SELECT count(id) as count FROM `wd_ask` where $where";
	echo $sql."<hr>";
	$result = mysql_query($sql);
	if($result) {
		if($row = mysql_fetch_assoc($result)){
			$cacheArr = $row['count'];
		}
//		saveCache($cacheName,$cacheArr,8);//缓存8小时
                
//                $cacheName = 'ask_new_'.$sclassid.'_'.$status;
                QLib_Cache_Client::setCache('ask_new', $cacheName, $cacheArr, 8);
	}
}

$iEndTime = time();
$iSec = number_format(($iEndTime - $iStartTime)/60,2);
echo "OK--time：".($iEndTime - $iStartTime)."=".$iSec."Minutes!";

$sStr = date("Y-m-d H:i:s")."	".$sclassid."	".($iEndTime - $iStartTime)."	".$iSec."Minutes".chr(13);
file_put_contents("log/".date("Y-m-d")."_page.txt",$sStr,FILE_APPEND);
?>