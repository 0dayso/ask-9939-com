<script src="/ask_details/js/user_login.js" type="text/javascript" charset="utf-8"></script>
<?php if($_COOKIE['member_uID']) { ?>
    <div class="rLg">
        <div class="dlQ">
            <b id="login_news">
                <span class="gren"><a href="http://home.9939.com/user/index/" target="_blank"><?php echo $_COOKIE['member_nickname']; ?></a></span>
                <span><a href="javascript:;" onclick="logout();">退出</a></span>
                <span><a href="http://home.9939.com/ask/" target="_blank" title="">医生入口</a></span>
                <span><a href="http://home.9939.com/ask/" target="_blank" title="">我的提问</a></span>
            </b>
        </div>
    </div>
<?php } else { ?>
        <!-- 登录框 start -->
        <div class="List_box_R">
            <div class="rLg">
                <div class="dlQ">
                    
                    <b id="login_news">
                        <span class="rIcon1" id="spShow"><i></i><a title="" rel="nofollow">登录</a></span>
                        <span class="rIcon2"><i></i><a href="http://www.9939.com/register/" title="" rel="nofollow">注册</a></span>
                    </b>
                </div>
            </div>
            <form name="name" action="" method="post">
                <div class="login hide" id="dPop" style="display:none;">
                    <i class="sJo"></i>
                    <span class="sClose" id="aClose"></span>
                    <p>您好，欢迎登录</p>
                    <div class="uInput">
                        <span class="yh"></span>
                        <input type="text" name="username" id="l-name" placeholder="用户名/手机号">
                    </div>
                    <div class="uInput reBd">
                        <span class="mm"></span>
                        <input type="password" name="password" id="l-psw" value="">
                    </div>
                    <p class="foget"><a href="http://www.9939.com/register/" target="" title="注册" class="foRa">注册</a>
                        <span id="tlogin-info" style="color:red;">错误提示</span></p>
                    <p class="dvBtn"><input type="submit" onmouseover="return dologin_index();test();" value="登 录"></p>
                    <p class="qqP">
                        <a href="javascript:void(0);" onmouseover="toQzoneLogin()"><img src="http://www.9939.com/9939/channels/images/qDl.png" alt=""></a>
                        可以使用以下方式登录
                    </p>
                </div>
            </form>
            <script language="javascript">
                /*点击关闭*/
                function test() {
                    var aShow = $("#spShow");
                    var aClose = $("#aClose");
                    var dPop = $("#dPop");
                    aShow.bind("click", function(){dPop.show();})
                    aClose.bind("click", function(){dPop.hide();})
                }
                test();
                function toQzoneLogin(){
                    childWindow = window.open('http://www.9939.com/qq/oauth/qq_login.php', 'TencentLogin', 'width=450,height=320,menubar=0, scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1');
                }
            </script>
        </div>
        <!-- 登录框 end -->
<?php } ?>        