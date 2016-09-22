<?php
/**
 * @author xzxin 2010-06-30
 * 需求人：徐小详
 * 1、	根据该问题所属终极问答栏目名称关键词（即问答所属栏目作为关键字），调取资讯版块最新发布文章
 * 2、	如果问答终极栏目没有对应的关键字的资讯，则以一级问答栏目作为关键词，匹配资讯版块最新发布的文章
 * 3、	如果一级栏目没有对应的关键字文章匹配，则调取网站最新发布文章（不过这种可能性已经很小）
 */
$iCatid = $this->info['classid'];
$aCat = explode("|",$this->info['catname']);

$sWhere = "status='20' AND (";
foreach ($aCat as $k=>$v){
	$sWhere .= " title like '%".$v."%' OR ";
}

$sWhere = substr($sWhere,0,-3).")";
define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');


DBconnect(1);

$cacheName = 'article_'.$cid;
$sql = "select url,title from Article where $sWhere order by articleid desc limit 12";
echo "<!--xzxin ".$sql."-->";//exit;
$arr = getRecordSet($sql);
saveCache($cacheName,$arr,10);//缓存小时数
?>

<?php if($arr) { ?>
<?php	foreach($arr as $val) { ?>
			 <li><a href="<?php echo $val['url'];?>" target="_blank" title="<?php echo $val['title'];?>"><?php echo mb_substr($val['title'],0,18,'utf-8');?></a></li>
<?php	} ?>
<?php } ?>

