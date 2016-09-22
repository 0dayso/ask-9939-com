<?
if($_COOKIE['member_uID'])
{
	$sStr = '<p class="welcome">您好，<font color="Red">'.$_COOKIE['member_nickname'].'</font> 欢迎进入久久健康社区！</p>	<ul class="top-tip">	<li class="g-logined"><a href="http://home.9939.com/user">我的空间</a></li>	<li><a href="http://home.9939.com/user/logout">退出</a></li>	<li class="help"><a href="http://ask.9939.com/rule.shtml" target="_blank">帮助</a></li>	</ul>';
}
else 
{
	$sStr = '<p class="welcome">您好 欢迎进入久久健康社区！</p>	<ul class="top-tip">	<li class="g-login"><a href="/">登录</a></li>	<li><a href="http://ask.9939.com/register">注册</a></li>	<li class="help"><a href="http://ask.9939.com/rule.shtml" target="_blank">帮助</a></li>	</ul>';
}

echo "document.write('$sStr');"
?>