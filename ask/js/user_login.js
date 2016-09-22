function dologin_index(){
		var longinError_index=function() {
			$('#tlogin-info').html('错误提示：用户名或密码错误！');
		};
		var html = '';
		var username = $('#l-name').val();
		var password = $('#l-psw').val();
		if(username.length==0||password.length==0){
			longinError();
			return false;
		}
		jQuery.getJSON("http://home.9939.com/buluo/index/do/login/?username="+username+"&password="+password+"&format=json&jsoncallback=?",function(data){
			if(data!='type0'){
                    o = eval( "(" + data + ")" );
                    html_index_news='<span class="gren"><a href="http://home.9939.com/user/index/" target="_blank">'+o.username+'</a></span><span><a href="javascript:;" onclick="logout();">退出</a></span><span><a href="http://home.9939.com/ask/" target="_blank" title="">医生入口</a></span><span><a href="http://home.9939.com/ask/" target="_blank" title="">我的提问</a></span>';
					$('#login_news').html(html_index_news);
                    $("#dPop").hide();
				}else if(data=='type0'){
					longinError_index();
				}
		});
		return false;
	}
function dologin(){
		var longinError=function() {
			$('#tlogin-info').html('用户名或密码错误！');
		};
		var html = '';
		var username = $('#l-name').val();
		var password = $('#l-psw').val();
		if(username.length==0||password.length==0){
			longinError();
			return false;
		}
		jQuery.getJSON("http://home.9939.com/buluo/index/do/login/?username="+username+"&password="+password+"&format=json&jsoncallback=?",function(data){
			if(data!='type0'){
					//setcookie('localuser',username);
					o = eval( "(" + data + ")" );
					//首页、列表页右侧
					html_new='<div class="mebd"><div><p class="meName">'+o.username+'</p><p class="meBtn"><a href="#" class="tna1">领久久金币</a><a href="#" class="tna2">会员俱乐部</a></p></div></div><p class="meLogin"><a class="exit" href="javascript:;" onclick="logout();">退出登陆</a><a href="#" class="ty">免费体检馆</a></p>';
					$('.t-login').addClass('k-display-none');
					$('#login_new').html(html_new);
					//专题页右侧
					html_zt = '<p>您好,<em>'+o.username+'</em></p><p><a href="javascript:;" onclick="logout();" class="exit">退出</a><a href="#">进入我的空间</a></p>';
					$('#login_ztright').attr('class', 'dlCg');
					$('#login_ztright').html(html_zt);
					//所有头部
					html='<div class="welCome">您好，<b>'+o.username+'</b> 欢迎进入久久健康网！<a href="http://home.9939.com/user" target="_blank">我的空间</a>&nbsp;&nbsp;<span style="cursor:pointer" onclick="logout();">退出</span></div><span><a href="http://home.9939.com/ask/" target="_blank" title="">医生入口</a></span><span><a href="http://ask.9939.com/asking" target="_blank" title="">我的提问</a></span><span><a target="_top" onclick="javascript:AddFavorite("http://www.9939.com", "久久健康网");return false;"style="cursor:pointer;">加为收藏</a></span>';
					$('#login').html(html);
				}else if(data=='type0'){
					longinError();
				}
		});
		return false;
	}
	function dologin_right(){
		var longinError=function() {
			alert('用户名或密码错误！');
		};
		var html = '';
		var username = $('#l-name-right').val();
		var password = $('#l-psw-right').val();
		if(username.length==0||password.length==0){
			longinError();
			return false;
		}
		$('#l-name').val(username);
		$('#l-psw').val(password);
		dologin();
	}

	function logout(){
		jQuery.getJSON("http://home.9939.com/buluo/index/do/logout/?format=json&jsoncallback=?",function(data){
			var html_new = '<div class="mebd"><div class="iNut"><p><label>用户名：</label><input type="text" name="username" value="" id="l-name-right" /></p><p><label>密&nbsp;&nbsp;码：</label><input type="password" name="password" value="" id="l-psw-right" /></p></div></div><p class="meOut"><input type="button" value="登录" class="aIn" onclick="return dologin_right();"/><a href="http://www.9939.com/register" class="reje">免费注册</a><a href="http://home.9939.com/user" class="reTy">免费体检馆</a></p>';
				$('#login_new').html(html_new);
				html_zt = '<form name="name_right" method="post" target="_blank" action="#" ><div class="dlForm"><p><label>账号：</label><input type="text" name="username" value="" class="dlInput" id="l-name-right"/><input name="" type="button" class="dlBtn" value="登录" onclick="return dologin_right();" /></p>	<p><label>密码：</label><input type="password" name="password" value="" class="dlInput" id="l-psw-right" /><a href="http://www.9939.com/register" target="_blank" class="dlBtn">注册</a></div></form><span id="tlogin-info_right" style="color:red;line-height:35px;"></span>';
				$('#login_ztright').attr('class', 'login_right');
				$('#login_ztright').html(html_zt);
			var html = '<form name="name" action="#" target="_blank" method="post" style="float: left;"><p class="fl"><label>用户名：</label><input type="text" name="username" value="" id="l-name" class="textIn mr" /><label>密码：</label><input type="password" name="password" value="" id="l-psw" class="textIn" /></p><span class="btnA"><input type="submit" value="登录" class="aSp1" onclick="return dologin();"/><a href="http://www.9939.com/register" target="_blank" class="aSp2">注册</a></span><span id="tlogin-info" style="color:red;float:left;line-height:35px;"></span></form><p class="wIcon"><span><i class="spI"></i><a href="javascript:void(0);" onclick="toQzoneLogin()">QQ登陆</a></span></p>';
				$('#login').html(html);
            var html_index_new = '<span class="rIcon1" id="spShow"><i></i><a href="javascript:" onclick="return test();" title="登录">登录</a></span><span class="rIcon2"><i></i><a href="http://www.9939.com/register/" target="_blank" title="注册">注册</a></span><span><a href="http://home.9939.com/ask/" target="_blank" title="医生入口">医生入口</a></span><span><a href="http://home.9939.com/ask/" target="_blank" title="我的提问">我的提问</a></span>';
				$('#login_news').html(html_index_new);
		});

	}