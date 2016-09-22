<?
/**
 * history_qz.php 浏览产品的历史记录模块
 * @author xiongzhixin (xzx747@sohu.com) 2009-04-20
 */
require_once("../class/common.inc.php");
$db = DBconnect(1);//home
$shistory_qz = split(",",$_COOKIE['history_qz']);	
$shistory_qz = array_filter($shistory_qz);
if($shistory_qz)
{
	$iNum = count($shistory_qz);	
	for($i=0; $i< $iNum; $i++)
	{
		$aField = $db->getRecordSet("select tagid,tagname from uchome_mtag where tagid=".$shistory_qz[$i],1);
		$sStr .='<li><a href="http://home.9939.com/space.php?do=mtag&tagid='.$aField['tagid'].'" target="_blank">'.$aField['tagname'].'</a></li>';
	}
}
echo $sStr;
?>
