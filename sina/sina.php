<?php 
$con = mysql_connect("192.168.229.47","9939_com_v2sns","snsrewou#*&#inewk");
mysql_select_db('9939_com_v2sns');
if (!$con)
{
	die('Could not connect: ' . mysql_error("失败"));
} 
$uid = $_GET['u_id'];
$ch = curl_init();
$url = "https://api.weibo.com/2/users/show.json?uid=".$uid."&access_token=2.00QA55nB0DuWWa88ffb06029rIRgOE";
/*配置curl-nzd*/
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_PROTOCOLS,CURLPROTO_HTTPS);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HEADER,false);

$sina_json = curl_exec($ch);
curl_close($ch);
$info = $sina_json;


$info = json_decode($info);
$uid = $info->idstr;//uid
$username = 'SINA_9939('.$uid.')';//名字
$pass = md5($username);//生成密码
$screen_name = $info->screen_name;//昵称
$image_url = $info->profile_image_url;//头像
$uType = '4';//类型

define("APP_TIME_INTERVAL",60*60*12);		//cookie时间间隔  （12 hour）
define("APP_DOMAIN",".9939.com");

$resultn=mysql_query("select * from member where username='{$username}'");
$uid = mysql_fetch_row($resultn);
if($uid){
	// 更新最近登录IP，登录时间
	$iarr['ip'] = $_SERVER['REMOTE_ADDR'];
	$iarr['lastlogin'] = time();
	mysql_query("update member set ip=".$iarr['ip'].", lastlogin=".$iarr['lastlogin']." where uid=".$uid[0]);
	setcookie('member_uID',$uid[0],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_username',$uid[3],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_uType',$uid[1],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	$_COOKIE['member_uType'] = $uid[1];
	setcookie('member_nickname',$uid[2],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_pic',$uid[19],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_credit',$uid[6],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_experience',$uid[7],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_ip',$iarr['ip'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	echo '<script>window.open("http://www.9939.com");</script>';
	echo "<script>window.close();</script>";
	echo '<script>window.location.reload();</script>';
}
else{
	 mysql_query("INSERT INTO `member` (`uid`, `uType`,

	`nickname`, `username`, `email`, `password`, `credit`,

	`experience`, `domain`, `viewnum`, `notenum`,

	`friendnum`, `dateline`, `updatetime`, `lastpost`,

	`lastlogin`, `lastsend`, `status`, `newpm`, `pic`,

	`ip`, `isVip`, `title`, `friend`, `salt`,

	`checkemail`, `huodongshenhe`, `getpassword`,

	`rzpassword`, `zdpassword`) VALUES (NULL, '4', '".$screen_name."', '".$username."',

	'', '{$pass}', '0', '0', '', '0', '0', '0', '', '0', '0',

	'0', '0', '1', '0', '".$image_url."', '{$_SERVER['REMOTE_ADDR']}', '3', '', '',

	'', NULL, '0', '', '', NULL)"); 

	// 更新最近登录IP，登录时间
	$iarr['ip'] = $_SERVER['REMOTE_ADDR'];
	$iarr['lastlogin'] = time();

	$resultn=mysql_query("select * from member where nickname='".$arr["nickname"]."'");
	$uid = mysql_fetch_row($resultn);

	mysql_query("update member set ip=".$iarr['ip'].", lastlogin=".$iarr['lastlogin']." where uid=".$uid[0]."");
	setcookie('member_uID',$uid[0],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_username',$uid[3],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_uType',$uid[1],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	$_COOKIE['member_uType'] = $uid[1];
	setcookie('member_nickname',$uid[2],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_pic',$uid[19],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_credit',$uid[6],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_experience',$uid[7],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	setcookie('member_ip',$iarr['ip'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
	echo '<script>window.open("http://www.9939.com");</script>';
	echo "<script>window.close();</script>";
	echo '<script>window.location.reload();</script>';
	
	
}

?>