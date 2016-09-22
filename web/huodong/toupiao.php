<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect();
require 'func_huodong.php';
header("Content-type: text/html; charset=utf-8");


$ip = $_SERVER['REMOTE_ADDR'];
$path = $_REQUEST['path'];
$ideaid = $_REQUEST['ideaid'];
$ideaid_path = $ideaid ? '?ideaid='.$ideaid : '';

$sIP = ip2long($ip);
$aIP = getDetail($sIP,'ip');
$aIdea = getDetail($ideaid);

if($aIP[0][tp]>=20){
	echo '<script>alert("投票超过20次，谢谢您的参与！");location.href="'.$path.$ideaid_path.'";</script>';
	exit("投票超过20次，谢谢您的参与！");
}else{
	if($aIP[0][ipid]){
		$sql = "SELECT count(*) as count FROM `hd_hits` WHERE `ipid`='".$aIP[0][ipid]."' AND `ideaid`='$ideaid'";
		$aHits = getRecordSet($sql);
		if($aHits[0][count]>=2){
			echo '<script>alert("单个作品只能投2票，谢谢您的参与！");location.href="'.$path.$ideaid_path.'";</script>';
			exit("单个作品只能投2票，谢谢您的参与！");
		}else{
			update('hd_idea',array('tpnum'=>$aIdea[0][tpnum]+1),'ideaid='.$ideaid);
			update('hd_ip',array('tp'=>$aIP[0][tp]+1),'ipid='.$aIP[0][ipid]);
			$sInsertid = insert('hd_hits',array('ipid'=>$aIP[0][ipid],'ideaid'=>$ideaid,'time'=>time()));
		}
	}
	else{
		update('hd_idea',array('tpnum'=>$aIdea[0][tpnum]+1),'ideaid='.$ideaid);
		$sipInsertid = insert('hd_ip',array('ip'=>$sIP,'tp'=>1));
		$sInsertid = insert('hd_hits',array('ipid'=>$sipInsertid,'ideaid'=>$ideaid,'time'=>time()));
	}
	echo '<script>alert("投票成功！");location.href="'.$path.$ideaid_path.'";</script>';
	exit("投票成功！");
}

?>