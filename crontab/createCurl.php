<?php
/**
 * @desc 生成分页缓存
 * @author xzxin 
 * @date 2010-09-21
 */

require_once('config.php');
$aList = getRecordSet("select id from wd_keshi order by id asc");

foreach ($aList as $v){
	echo "curl http://ask.9939.com/crontab/createPageCache.php?cid=".$v['id']."<br>";
}
?>