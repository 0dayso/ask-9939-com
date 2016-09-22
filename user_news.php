<?
exit;
//require 'include/common.inc.php';
//echo '<script>alert("'.$_COOKIE['member_uID'].'");</script>';
if(!$_COOKIE['member_uID'])
{
	echo "document.write(\"<form action='#' method='post' class='lform' onsubmit='return checkLogin(this);'>用户名：<input name='username' id='l-name' type='text' class='lbox'/> 密码：<input name='password' type='password' class='lbox' id='l-psw'/>&nbsp;<input name='dosubmit' value=1 type='hidden'><input type='button' value='登 陆' class='lbuttom' onclick='dologin()'/>&nbsp;<a href='http://ask.9939.com/register' target='_blank'>注册</a><span id='tlogin-info' style='color:red;'></span>	     </a></form>\")";
}
else 
{
	echo "document.write(\"欢迎您，".$_COOKIE['member_nickname']."&nbsp;&nbsp;<a href='http://home.9939.com/space.php' target='_blank'><font color=red>进入个人空间</font></a>&nbsp;&nbsp;<a href='javascript:;' onclick='logout();'>退出</a>\");";
}
?>
