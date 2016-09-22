<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect();
require 'func_huodong.php';
header("Content-type: text/html; charset=utf-8");
//deldir('attachment');exit;
$aInfo = $_REQUEST['info'];

if(!$_COOKIE['member_uID']){
	echo '<script>alert("请先登录再操作。");location.href="join.php";</script>';
	exit('请先登录再操作');
}

$n = getRowsNum("SELECT count(*) as count FROM `hd_idea` WHERE `uid`='{$_COOKIE['member_uID']}' AND `type`='$aInfo[type]'");
if($n){
	echo '<script>alert("您已上传过同类型作品，谢谢参与！");location.href="join.php";</script>';
	exit('您已上传过同类型作品，谢谢参与！');
}

if($aInfo[type]==2){
	if($_FILES['idea']['name']){
		$imageexts = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
		$temp = pathinfo($_FILES['idea']['name']);
		$ext = $temp['extension'];
		if(in_array(strtolower($ext),$imageexts))
		{
			$path = 'attachment/chuangyi/';
			//$name = substr(md5($_FILES['idea']['name'].time()),0,10).'.'.$ext;
			$name = 'cy_[fenge]_'.date("Y",time()).'-'.date("m",time()).'-'.date("d",time()).'_'.substr(md5($_FILES['idea']['name'].time()),0,6).'.'.$ext;
			if(!is_dir($path))mkdir($path,0777,true);
			$size = filesize($_FILES['idea']['tmp_name']);
			$html_size = 3072000;
			if($size>$html_size){
				echo '<script>alert("图片太大了");location.href="join.php";</script>';
				exit('图片太大了');
			}
		}
		else{
			echo '<script>alert("图片格式不对");location.href="join.php";</script>';
			exit('图片格式不对');
		}
	}else{
		echo '<script>alert("请上传图片");location.href="join.php";</script>';
		exit('请上传图片');
	}
				
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$aInfo['uid'] = $_COOKIE['member_uID'];
		$aInfo['time'] = time();
		$aInfo['tpnum'] = 1;
		$aInfo['nickname'] = $_COOKIE['member_nickname'];
		$sInsertid = insert("hd_idea",$aInfo);
		
		//改库将[fenge]替换成实际的id
		$name = preg_replace("~\[fenge\]~is",$sInsertid,$name);
		$full_name = $path.$name;
		$aPic = array('pic'=>$name);
		update("hd_idea",$aPic,'ideaid='.$sInsertid);
		
		//以实际文件名上传图片
		if(copy($_FILES['idea']['tmp_name'],$full_name)){
			$idea  = $name;
			chmod($full_name,0777);
		}else
		echo '<script>alert("copy图片失败");location.href="join.php";</script>';	
		//Js_Goto('copy图片失败','/buluo/index/do/create');
		
		//Js_Goto($sInsertid ? '添加成功' : '添加失败', '/buluo/index/bid/'.$sInsertid);
		echo '<script>alert("添加成功");location.href="success.php?ideaid='.$sInsertid.'";</script>';
		exit('添加成功');
	}
}
elseif($aInfo[type]==1){
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$aInfo['uid'] = $_COOKIE['member_uID'];
		$aInfo['time'] = time();
		$aInfo['tpnum'] = 1;
		$aInfo['nickname'] = $_COOKIE['member_nickname'];
		$sInsertid = insert("hd_idea",$aInfo);
		echo '<script>alert("添加成功");location.href="success.php?ideaid='.$sInsertid.'";</script>';
		exit('添加成功');
	}
}
else{
	echo '<script>alert("路径非法，请正常浏览。");history.go(-1);</script>';
	exit('路径非法，请正常浏览。');
}
?>