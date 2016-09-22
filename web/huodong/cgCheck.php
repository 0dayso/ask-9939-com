<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect(1);
require 'func_huodong.php';
$aQuesAll = @include('question.php');

if(!$_COOKIE['member_uID'])
echo '<script>alert("请先登录！");location.href="index.php"</script>';

$info = $_REQUEST['info'];
$count = count($info);
if($count<>10){
	for($i=0;$i<10-$count;$i++){
		$info['bu'.$i] = 1;
	}
}
//echo '<pre>';print_r($aQuesAll);echo '</pre>';
//echo '<pre>';print_r($info);echo '</pre>';
if(is_array($info)){
	foreach($info as $k=>$v){
		if($aQuesAll[$k-1]<>''){
			//echo $aQuesAll[$k-1];echo '<br/>';
			preg_match('~\(([^\)]*)\)~s',$aQuesAll[$k-1],$b);
			//print_r($b);
			//echo $k.'='.$b[1].',';
			if($v<>trim($b[1])){
				$e++;
			}else{
				$r++;
			}
		}else{
			//echo $k.'是空的';
		}
	}
}
//echo '$e======'.$e;
//echo '$r======'.$r;
//exit;
if($r>=7){
	if($_COOKIE['member_uID']){
		$row = getRecordSet("SELECT * FROM `hd_ask_chg` WHERE `uid`='".$_COOKIE['member_uID']."'");
		if($row[0][uid]){
			//echo 'here';
			update('`hd_ask_chg`',array('chgnum'=>$row[0][chgnum]+1),'uid='.$_COOKIE['member_uID']);
		}else{
			//echo 'there';
			$sInid = insert('hd_ask_chg',array('chgnum'=>1,'time'=>time(),'uid'=>$_COOKIE['member_uID']));
		}
		session_start();
		$_SESSION['aa'] = 'here';
		header("Location:cg_ok.php");

	}
}else{
	$e = serialize($e);
	header("Location:cg_false.php?e=$e");
}
?>