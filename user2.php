<?
$ask_url = "http://ask.9939.com/"; 
if($_COOKIE['member_uType']==2){
	$home_url = "http://home.9939.com/doctor/";
}else{
	$home_url = "http://home.9939.com/user/";
}
if($_COOKIE['member_uID'])
{
	$sStr = '<p class="welcome" onmouseover="showlogin('.$_COOKIE['member_uID'].')">您好，<font color="Red">'.$_COOKIE['member_nickname'].'</font> 欢迎进入久久健康社区！</p>	<ul class="top-tip">	<li class="g-logined"><a href="'.$home_url.'index/">我的空间</a></li>	<li><a href="#" onclick="return logout();">退出</a></li>	<li class="help"><a href="'.$ask_url.'rule.shtml" target="_blank">帮助</a></li>	</ul>';
}
else if($_COOKIE['sina_member_nickname'])
{
        $sStr = '<p class="welcome" onmouseover="showlogin('.$_COOKIE['member_uID'].')">您好，<font color="Red">'.$_COOKIE['sina_member_nickname'].'</font> 欢迎进入久久健康社区！</p>	<ul class="top-tip">	<li class="g-logined"><a href="'.$home_url.'index">我的空间</a></li><li><a href="#" onclick="return logout();">退出</a></li>	<li class="help"><a href="'.$ask_url.'rule.shtml" target="_blank">帮助</a></li>	</ul>';
}
else 
{
	$sStr = '<p class="welcome">您好 欢迎进入久久健康社区！</p>	<ul class="top-tip">	<li class="g-login"><span href="#" id="vvvlogin" >登录</span></li>	<li><a href="http://www.9939.com/register">注册</a></li>	<li class="help"><a href="'.$ask_url.'rule.shtml" target="_blank">帮助</a></li>	</ul>';
}
echo "document.write('$sStr');";
?>