<?php		
$_SGLOBAL['cookie']=Array(	
	'uid'=>isset($_COOKIE['member_uID'])?$_COOKIE['member_uID']:'',
	'username'=>isset($_COOKIE['member_username'])?$_COOKIE['member_username']:'',
	'uType'=>isset($_COOKIE['member_uType'])?$_COOKIE['member_uType']:'',
	'nickname'=>isset($_COOKIE['member_nickname'])?$_COOKIE['member_nickname']:'',
	'pic'=>isset($_COOKIE['member_pic'])?$_COOKIE['member_pic']:'', // 头像
	'credit'=>isset($_COOKIE['member_credit'])?$_COOKIE['member_credit']:'',
	'ip'=>isset($_COOKIE['member_ip'])?$_COOKIE['member_ip']:'',
	'experience'=>isset($_COOKIE['member_experience'])?$_COOKIE['member_experience']:'',
	'grouptitle'=>isset($_COOKIE['member_grouptitle'])?$_COOKIE['member_grouptitle']:'',
	'groupname'=>isset($_COOKIE['member_groupname'])?$_COOKIE['member_groupname']:'',
	'groupicon'=>isset($_COOKIE['member_groupicon'])?$_COOKIE['member_groupicon']:''
	)
?>