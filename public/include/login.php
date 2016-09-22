<script src="http://www.9939.com/9939/js/user_login.js?v=20151008103000" type="text/javascript" charset="utf-8">
</script>
<?
if(@$_COOKIE['member_uID'])
{
	$sStr ='<span class="gren"><a href="http://home.9939.com/user/index/" target="_blank">'.$_COOKIE['member_nickname'].'</a></span><span><a onclick="logout();">退出</a></span><span><a href="http://home.9939.com/ask/" title="" rel="nofollow">医生入口</a></span><span><a href="http://home.9939.com/ask/" title="我的提问" rel="nofollow">我的提问</a></span>';
    $sStr2="<script>var member_nickname='{$_COOKIE['member_nickname']}';</script>";
}
else
{
    $sStr = '<span class="rIcon1" id="spShow"><i></i><a onclick="return test();" title="登录" rel="nofollow">登录</a></span><span class="rIcon2"><i></i><a href="http://www.9939.com/register/" target="_blank" title="注册" rel="nofollow">注册</a></span><span><a href="http://home.9939.com/ask/" title="医生入口" rel="nofollow">医生入口</a></span><span><a href="http://home.9939.com/ask/" title="我的提问" rel="nofollow">我的提问</a></span>';
}

echo $sStr.@$sStr2;//.$sStr3;
?>