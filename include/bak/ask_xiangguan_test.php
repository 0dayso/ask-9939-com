<?php
/*
	科室相关问答
*/
//$cid = intval($_GET['cid']);
//$cid = intval($this->classid) ?  intval($this->classid) : intval($this->info['classid']);
$cid = intval($_GET['classid']);
$sWhere = ($cid > 0) ? "a.classid=$cid" : 1;

define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');

$cacheName = 'ask_xiangguan_'.$cid;
$arr = array();


$sql = "select a.id,a.title,b.askid,b.hits from wd_ask a,wd_ask_count b where ".$sWhere."  and a.id=b.askid order by b.hits desc limit 6";
$result = mysql_query($sql);
if($result) {
	while($row = mysql_fetch_assoc($result)){
		$arr[] = $row;
	}
	saveCache($cacheName,$arr,2);//缓存2小时
}
?>

<?php if($arr) { ?>
<?php	foreach($arr as $val) { ?>
			 <li><a href="/id/<?php echo $val['id'];?>" target="_blank" title="<?php echo $val['title'];?>"><?php echo mb_substr($val['title'],0,18,'utf-8');?></a></li>
<?php	} ?>
<?php } ?>

