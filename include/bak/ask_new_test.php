<?php
/*
	问答最新问答
*/
//$cid = intval($_GET['cid']);
$cid = intval($this->classid) ?  intval($this->classid) : intval($this->info['classid']);


define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');
DBconnect(0);


$cacheName = 'ask_new_'.$cid;
//echo $cacheName;
$arr = array();


$sWhere = ($cid > 0) ? "classid=$cid" : 1;
$sql = "select id,title from wd_ask where ".$sWhere." order by id desc limit 12";
$result = mysql_query($sql);
if($result) {
	while($row = mysql_fetch_assoc($result)){
		$arr[] = $row;
	}
	saveCache($cacheName,$arr,6);//缓存小时数
}


?>

<?php if($arr) { ?>
<?php	foreach($arr as $val) { ?>
			 <li><a href="/id/<?php echo $val['id'];?>" target="_blank" title="<?php echo $val['title'];?>"><?php echo mb_substr($val['title'],0,22,'utf-8');?></a></li>
<?php	} ?>
<?php } ?>

