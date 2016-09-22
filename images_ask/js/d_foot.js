// JavaScript Document
function dologin(){
		var longinError=function() {
			$('#tlogin-info').html('<font color="red">用户名或密码错误！</font>');
		};
		var html = '';
		var username = $('#l-username').val();
		var password = $('#l-password').val();
		if(username.length==0||password.length==0){
			longinError();	
			return false;
		}
		jQuery.getJSON("http://home.9939.com/buluo/index/do/login/?username="+username+"&password="+password+"&format=json&jsoncallback=?",function(data){
			if(data!='type0'){
					setcookie('localuser',username);
					o = eval( "(" + data + ")" );
					html='<p class="welcome" onmouseover="showlogin('+o.userid+')">您好，<font color="Red">'+o.username+'</font> 欢迎进入久久健康社区！</p><ul class="top-tip"><li><a href="http://home.9939.com/user">我的空间</a></li><li><a href="javascript:;" onclick=" return logout();">退出</a></li><li class="help"><a href="#">帮助</a></li></ul>';
					$('.t-login').addClass('k-display-none');
					$('.temp-iframe').hide();
					$('#login').html(html);
					showlogin(o.userid);
					
					if(typeof(__login)=='function') {
						__login();
					}
				}else if(data=='type0'){
					longinError();
				}
		});
		return false;
	}
	function setcookie(name, value, days)
	{
		var expire = new Date();
		if(days==null || days==0) days=365;
		expire.setTime(expire.getTime() + 3600000*24*days);
		document.cookie = name + "=" + escape(value) + ";expires="+expire.toGMTString();
	}
	function getcookie(name)
	{
		var arg = name + "=";
		var alen = arg.length;
		var clen = document.cookie.length;
		var i = 0;
		while(i < clen)
		{
			var j = i + alen;
			if(document.cookie.substring(i, j) == arg) return getcookieval(j);
			i = document.cookie.indexOf(" ", i) + 1;
			if(i == 0) break;
		}
		return null;
	}
	function getcookieval(offset)
	{
		var endstr = document.cookie.indexOf (";", offset);
		if(endstr == -1)
		endstr = document.cookie.length;
		return unescape(document.cookie.substring(offset, endstr));
	}
	 