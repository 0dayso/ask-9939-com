<?php
/*
	问答最新问答
*/
//$cid = intval($_GET['cid']);
$cid = intval($this->classid) ?  intval($this->classid) : intval($this->info['classid']);

define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');
DBconnect(0);

$arr="";
$cacheName = 'ask_new_'.$cid;
//echo $cacheName;
//$arr = loadCache($cacheName,24);


if(!$arr) {
    if($cid){
        $sql = "select id,title from wd_ask where classid=$cid order by id desc limit 0,12";
	    $result = mysql_query($sql);
    }else{
        $sql = "select id,title from wd_ask order by id desc limit 0,12";
	    $result = mysql_query($sql);
    }
	if($result) {
		while($row = mysql_fetch_assoc($result)){
			$arr[] = $row;
		}
//		saveCache($cacheName,$arr,6);//缓存小时数
                QLib_Cache_Client::setCache('ask_new', $cacheName, $arr, 6);
	}
}
?>

<?php if($arr) { ?>
<?php	foreach($arr as $val) { ?>
			 <li><a href="/id/<?php echo $val['id'];?>" target="_blank" title="<?php echo $val['title'];?>"><?php echo mb_substr($val['title'],0,22,'utf-8');?></a></li>
<?php	} ?>
<?php } ?>

