<?php
class bb{
	public function __construct(){
		$__DBHost		= "218.246.21.99";
		$__DBUser		= "9939_com_v2sns";
		$__DBPwd        = "snsrewou#*&#inewk";
		$__DBName		= "9939_com_v2sns";

		mysql_connect($__DBHost,$__DBUser,$__DBPwd);
		mysql_select_db($__DBName);	
		mysql_query("SET character_set_connection=UTF8, character_set_results=UTF8, character_set_client=binary");
		mysql_query("set names utf8");
	}
	
	public function getSessionInfo(){
		$gift = array(
			1=>'恭喜您获得一等奖！',
			2=>'恭喜您获得二等奖！',
			3=>'恭喜您获得三等奖！',
			4=>'恭喜您获得幸运奖！',
			5=>'恭喜您获得参与奖！',
			6=>'恭喜您获得参与奖！',
			7=>'恭喜您获得参与奖！'
		);
		$Lvlucky = array(50,100,200,400,600,800,1000);
		for($i=1;$i<=20;$i++){
			array_push($Lvlucky,$Lvlucky[count($Lvlucky)-1]+500);
		}
		$Lv3 = array(3001,6001,10008,20008);
		$Lv2 = array(5008,15008,25008);
		$Lv1 = array(16800,29999);
		
		session_start();
		$ReArr=array();
		$query = mysql_query("SELECT count(*) as count FROM `hd_ask_chg`");
		$aRes = mysql_fetch_array($query);
		$ReArr['data']['aRes']=$aRes[0];
		if(in_array($aRes[0],$Lv1)){
			$idLv = 1;
		}elseif(in_array($aRes[0],$Lv2)){
			$idLv = 2;
		}elseif(in_array($aRes[0],$Lv3)){
			$idLv = 3;
		}elseif(in_array($aRes[0],$Lvlucky)){
			$idLv = 4;
		}else{
			$idLv = mt_rand(5,7);
		}
		$ReArr['data']['idLv']=$idLv;
		$ReArr['data']['lab']="0";
		$ReArr['err']=false;
		$ReArr['data']['aa'] = $_SESSION['aa'];
		if($_SESSION['aa']!=''){
			$ReArr['data']['lab']=$idLv;
			$ReArr['data']['msg']='';
			$ReArr['data']['nextPage']='http://ask.9939.com/web/huodong/cj_ok.php';
			$ReArr['data']['targetType'] = "_self";
			$ReArr['err']=true;

		$uname = $_COOKIE['member_nickname'] ?  $_COOKIE['member_nickname'] : $_COOKIE['member_username'];
		$query = mysql_query("INSERT INTO `hd_ask_gift` (`uid`,`username`,`askid`,`giftid`) value ('".$_COOKIE['member_uID']."','".$uname."','".$_COOKIE['askid']."','".$idLv."')");
		setcookie("idLv", "$idLv", time() + 3600,'/','.9939.com');
		setcookie("passed", "", time() - 360000,'/','.9939.com');
		setcookie("askid", "", time() - 360000,'/','.9939.com');
		}
		
		return $ReArr;
	}
}
?>