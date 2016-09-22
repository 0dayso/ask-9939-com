<?php
Zend_Loader::loadClass('Member',MODELS_PATH);
define(APP_SOAP_GO9939_KEY, '9939userWebServiceKey#*');
//Zend_Loader::loadClass('Asession',MODELS_PATH);
class LoginController extends Zend_Controller_Action {
    private $getpwdtime=172800;//找回密码时间间隔秒
    public function init() {
        $this->view = Zend_Registry::get("view");
        $this->Member_obj=new Member();
        parent::init();
    }

    /*public function checkloginAction() {
        $data=$this->getRequest()->getParams();
        try {
            $ret=$this->Member_obj->checklogin($data, $error);
            if($ret==true) {
                Zend_Adver_Js::helpJsRedirect('/manage/admin',2);
            }
            else {
                Zend_Adver_Js::GoToTop('/manage/',$error);
            }
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    public function exitAction() {
        $ret=$this->user_table->Logout();
        $this->_redirect('/manage');
    }*/
    //找回密码第三部
    
    public function getpwd3ajaxAction(){
        $uid=$this->getRequest()->getParam('uid');
        $r=$this->getRequest()->getParam('r');
        $act=$this->getRequest()->getParam('act');
        if(!$uid||!$r){//参数不合法
            if(!$act)
            echo 'alert("重置链接地址不对或已过期,请重新申请");window.location.href="getpwd.html";';
            else
            echo json_encode(array("error" => "2"));
            exit;
            
        }
        $user=$this->Member_obj->get_one("uid='{$uid}'");
        if(!$user){//无用户
            if(!$act)
            echo 'alert("重置链接地址不对或已过期,请重新申请");window.location.href="getpwd.html";';
            else
            echo json_encode(array("error" => "2"));
            exit;
        }
        if(!$user['getpassword']){//无更改标识
            if(!$act)
            echo 'alert("重置链接地址不对或已过期,请重新申请");window.location.href="getpwd.html";';
            else
            echo json_encode(array("error" => "2"));
            exit;
        }
        $miyao=$user['getpassword'];
        if($this->pwdEncode($miyao)!=$r){
            if(!$act)
            echo 'alert("重置链接地址不对或已过期,请重新申请");window.location.href="getpwd.html";';
            else
            echo json_encode(array("error" => "2"));
            exit;
        }
        $time=mb_substr(("".$miyao),4);
        if($time<time()){//超时
            if(!$act)
            echo 'alert("重置链接地址不对或已过期,请重新申请");window.location.href="getpwd.html";';
            else
            echo json_encode(array("error" => "2"));
            exit;
        }
        if($act=="update"){
            $data['pwd']=$this->getRequest()->getParam('pwd');
            $data['pwd2']=$this->getRequest()->getParam('pwd2');
            if (($error = $this->checkpwd($data)) === true) {//验证数据
                //修改密码
                $password=md5($data['pwd']);
                $db=$this->Member_obj->getAdapter();
                $db->query("update member set password='{$password}',getpassword='' where uid={$user['uid']}");
            }else{
                echo json_encode(array("error" => $error));
                exit;
            }
            echo json_encode(array("error" => "1"));
            exit;
        }
        $this->view->uid=$uid;
        $this->view->r=$r;
        echo "var getpwd3ajax=true;";
        exit;
    }
    public function getpwd3Action(){
        //过时方法，跳转到http://www.9939.com/login/getpwd.html
        header("location: ".WEB_URL."login/getpwd.html");
        exit;
        $uid=$this->getRequest()->getParam('uid');
        $r=$this->getRequest()->getParam('r');
        $act=$this->getRequest()->getParam('act');
        if(!$uid||!$r){//参数不合法
            if(!$act)
            Zend_Adver_Js::helpJsRedirect("/login/getpwd",2,"重置链接地址不对或已过期,请重新申请");
            else
            echo json_encode(array("error" => "2"));
            exit;
            
        }
        $user=$this->Member_obj->get_one("uid='{$uid}'");
        if(!$user){//无用户
            if(!$act)
            Zend_Adver_Js::helpJsRedirect("/login/getpwd",2,"重置链接地址不对或已过期,请重新申请");
            else
            echo json_encode(array("error" => "2"));
            exit;
        }
        if(!$user['getpassword']){//无更改标识
            if(!$act)
            Zend_Adver_Js::helpJsRedirect("/login/getpwd",2,"重置链接地址不对或已过期,请重新申请");
            echo json_encode(array("error" => "2"));
            exit;
        }
        $miyao=$user['getpassword'];
        if($this->pwdEncode($miyao)!=$r){
            if(!$act)
            Zend_Adver_Js::helpJsRedirect("/login/getpwd",2,"重置链接地址不对或已过期,请重新申请");
            echo json_encode(array("error" => "2"));
            exit;
        }
        $time=mb_substr(("".$miyao),4);
        if($time<time()){//超时
            if(!$act)
            Zend_Adver_Js::helpJsRedirect("/login/getpwd",2,"重置链接地址不对或已过期,请重新申请");
            echo json_encode(array("error" => "2"));
            exit;
        }
        if($act=="update"){
            $data['pwd']=$this->getRequest()->getParam('pwd');
            $data['pwd2']=$this->getRequest()->getParam('pwd2');
            if (($error = $this->checkpwd($data)) === true) {//验证数据
                //修改密码
                $password=md5($data['pwd']);
                $db=$this->Member_obj->getAdapter();
                $db->query("update member set password='{$password}',getpassword='' where uid={$user['uid']}");
                
            }else{
                echo json_encode(array("error" => $error));
                exit;
            }
            echo json_encode(array("error" => "1"));
            exit;
        }
        $this->view->uid=$uid;
        $this->view->r=$r;
        echo $this->view->render("getpwd3.phtml");
    }
    //发送邮件
    private function tomail($email,$url){
    //public function tomailAction(){
//        $email="123109769@qq.com";$url;
        
		Zend_Loader::loadClass('smtp',MODELS_PATH);
		$smtpserver = "smtp.9939.com";//SMTP服务器smtp.163.com
		$smtpserverport =25;//SMTP服务器端口
		$smtpusermail = "thinkno-reply@9939.com";//SMTP服务器的用户邮箱
		$smtpemailto = $email;//发送给谁
		$smtpuser = "thinkno-reply@9939.com";//SMTP服务器的用户帐号
		$smtppass = "9939789!@#";//SMTP服务器的用户密码
		$mailsubject2 = '===网站会员更改密码===';//邮件主题
		$mailsubject = mb_convert_encoding($mailsubject2,"GB2312","UTF-8");//utf-8转换为bg2312邮件编码
		$mailbody2 = '<meta http-equiv="Content-Type" content="text/html; charset=GB2312" /><div  style="width:780px; margin:0 auto;">
	<div  style="border-bottom:1px solid #d6d6d6; height:34px; padding:34px 0 9px 22px;">
		<a href="http://www.9939.com"><img src="http://ask.9939.com/email/images/logo.gif" style="border:none;"/></a>
	</div>
	<div  style="padding:40px 22px 20px;">
		<strong>亲爱的'.$email.'， 您好！</strong>
		<p  style=" padding:0; margin:0;font-size:12px; margin:30px 0 38px;">
			感谢您注册久久健康社区会员，愿久久健康网伴随你永久健康
		</p>
		
		<div  style="font-size:14px; line-height:23px; padding-bottom:20px;background:url(http://ask.9939.com/email/images/message_bg.gif) no-repeat center bottom; padding-bottom:25px;">
			<p  style=" padding:0; margin:0;font-size:14px; margin-bottom:10px;">请点击以下连接重置您的密码（连接48小时内有效）。</p>
			<p  style=" padding:0; margin:0;width:520px; font-size:14px;">
			<a href="'.$url.'" style="text-decoration:underline;color:#0000fa;">'.$url.'</a>
</p>
			<p  style=" padding:0; margin:0;margin-top:35px;">(如果无法点击该URL链接地址，请将它复制并粘贴到浏览器的地址输入框，然后单击回车即可）</p>
            <p  style=" padding:0; margin:0;margin-top:35px;">如果您没有申请找回密码，请忽略此邮件</p>
            <p  style=" padding:0; margin:0;margin-top:35px;">会员中心 '.date("Y-m-d",time()).'</p>
		</div>
		<div  style="margin-top:15px; color:#0000fa;  font-size:12px;">
			<a href="http://www.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">去久久健康网</a>|<a href="http://ask.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">到久久问答提问</a>
		</div>
	</div>
</div>';
		//$mailbody2 = "您好!".$email."<BR>您的邮箱已经认证！点击<a href='http://www.9939.com' target='_blank'>这里</a> 进入99健康网";//邮件内容
		
        $mailbody = mb_convert_encoding($mailbody2,"GB2312","UTF-8");//utf-8转换为bg2312邮件编码

		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
		$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		$smtp->debug = false;//true是否显示发送的调试信息
		if($smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype)){
			
		}
	}
      //验证数据合法性
    private function checkpwd($data){
        if ($data['pwd'] == "")
            return "请填写重置密码";
            
        if (strlen($data['pwd']) <6)
            return "密码长度不小于6个字符";
            
        if ($data['pwd2'] == "")
            return "请填写确认密码";
            
        if ($data['pwd2'] != $data['pwd'])
            return "俩次输入密码不一致";    
            
        return true;
    }
    //找回密码第二部
     public function getpwd2ajaxAction(){
        $session = new Zend_Session_Namespace("getpwduser");
        $session->unlock();
        $username=$session->getpwduser;
        $session->unsetAll();
        if(!is_array($username)){
            echo json_encode(array("error" => "信息错误"));
            exit;
        }else{
             /////发送邮件//////////
            
            $this->tomail($username['username'],"".(WEB_URL==""?"http://www.9939.com/":WEB_URL)."login/getpwd.html?r=".$this->pwdEncode($username['miyao'])."&uid=".$username['uid']);
            ////////////////////////
        }
        $this->view->username=$username['username'];
        echo json_encode(array("error" => "1","html"=>$this->view->render("getpwd2ajax.phtml")));
        exit;
       
    }
    public function getpwd2Action(){
        //过时方法，跳转到http://www.9939.com/login/getpwd.html
        header("location: ".WEB_URL."login/getpwd.html");
        exit;
        $session = new Zend_Session_Namespace("getpwduser");
        $session->unlock();
        $username=$session->getpwduser;
        $session->unsetAll();
        if(!is_array($username)){
            Zend_Adver_Js::helpJsRedirect("/login/getpwd");
        }else{
             /////发送邮件//////////
            
            $this->tomail($username['username'],"".(ASK_URL==""?"http://ask.9939.com/":ASK_URL)."login/getpwd3?r=".$this->pwdEncode($username['miyao'])."&uid=".$username['uid']);
            ////////////////////////
        }
        $this->view->username=$username['username'];
        echo $this->view->render("getpwd2.phtml");
    }
    //找回密码第一部
        
    public function getpwdAction(){
       // echo "http://ask.9939v2.com/login/getpwd3?r=".$this->pwdEncode("89271315105541")."&uid=325692";
        $act=$this->getRequest()->getParam('act');
        if($act=="1"){
            $data['username']=$this->getRequest()->getParam('username');
            $data['yzm']=$this->getRequest()->getParam('yzm');
            if (($error = $this->check($data)) === true) {
                $user=$this->Member_obj->get_one("username='{$data['username']}' or email='{$data['username']}'");
                if(!$user){
                    echo json_encode(array("error" => "用户不存在"));
                    exit;
                }else if($user['checkemail']!="1"){
                    echo json_encode(array("error" => "邮箱未认证无法找回"));
                    exit;
                }else{
                    $miyao=rand(1000,9999).($this->getpwdtime+time());
                    $db=$this->Member_obj->getAdapter();
                    $db->query("update member set getpassword='{$miyao}' where uid={$user['uid']}");
                    
                    $session = new Zend_Session_Namespace("getpwduser");
                    $session->unlock();
                	$session->getpwduser=array("username"=>$user['email'],"uid"=>$user['uid'],"miyao"=>$miyao);
            		$session->lock();
                }
            }else{
                echo json_encode(array("error" => $error));
                exit;
            }
            echo json_encode(array("error" => "1"));
            exit;
        }
        //过时方法，跳转到http://www.9939.com/login/getpwd.html
        header("location: ".WEB_URL."login/getpwd.html");
        exit;
        echo $this->view->render("getpwd1.phtml");
    }
    //验证标识计算
    private function pwdEncode($miyao){
        $sjs=mb_substr(("".$miyao),0,4);
        $time=mb_substr(("".$miyao),4);
        $zonghe=$time+$sjs;
        $jishu=mb_substr(("".$zonghe),5,4);
        $cishu=mb_substr(("".$zonghe),9);
        if($cishu<1){
            $cishu=1;
        }
        $rebound=md5($zonghe);
        for($x=0;$x<$cishu;$x++){ 
            $rebound=md5($rebound.($jishu+$x));
        }
        return $rebound;
    }
    //验证数据合法性
    private function check($data){
        if ($data['yzm'] == "")
            return "验证码不能为空";
        $session = new Zend_Session_Namespace("verify");
		$session->lock();
        $yzm=$session->verify;
        $session->unsetAll();
        
        if($yzm!=md5(strtoupper($data['yzm'])))
            return "验证码不正确";
            
        if ($data['username'] == "")
            return "用户名不能为空";
            
        //if (!preg_match("/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/",$data['username']))
//            return "登陆名格式不正确，应为E-Mail";
        return true;
    }
    //生成验证码
    public function verifyAction(){
        $length=4;
        $mode=1;
        $type='png';
        $width=48;
        $height=22;
        $verifyName='verify';
        $randval ="".rand(1000,9999);
        $session = new Zend_Session_Namespace($verifyName);
        $session->unlock();
    	$session->{$verifyName}=md5($randval);
		$session->lock();
      
        $width = ($length*10+10)>$width?$length*10+10:$width;
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width,$height);
        }else {
            $im = @imagecreate($width,$height);
        }
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);
        $key = mt_rand(0,3);
        $backColor = imagecolorallocate($im, $r[$key],$g[$key],$b[$key]);    //背景色（随机）
		$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        $pointColor = imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));                 //点颜色

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor = imagecolorallocate($im,mt_rand(0,200),mt_rand(0,120),mt_rand(0,120));
		// 干扰
		for($i=0;$i<10;$i++){
			$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
		}
		for($i=0;$i<25;$i++){
			$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pointColor);
		}
		for($i=0;$i<$length;$i++) {
			imagestring($im,5,$i*10+5,mt_rand(1,8),$randval{$i}, $stringColor);
		}
        header("Content-type: image/".$type);
        $ImageFun='image'.$type;
		if(empty($filename)) {
	        $ImageFun($im);
		}else{
	        $ImageFun($im,$filename);
		}
        imagedestroy($im);
    }
    public function indexAction() {
      //过时方法，跳转到http://www.9939.com/login/
        header("location: ".WEB_URL."login");
        exit;
//    phpinfo();
//    exit;
      
        $backUrl = $this->_getParam('backurl'); //获取跳转到的url 林原 2010-08-24
        $this->view->backurl = $backUrl; //跳转url 林原 2010-08-24
        echo $this->view->render("login.phtml");
    }
    public function checkloginAction() {
        //过时方法，跳转到http://www.9939.com/login/
        header("location: ".WEB_URL."login");
        exit;
        $info = $this->getRequest()->getParam('info'); 
        try {
            $uid=$this->Member_obj->checklogin($info, $error);
            if($uid) {
                 ////////连接商成
                if(class_exists("SoapClient",false)){
                   $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php','uri'=>'go9939','encoding'=>'utf8'));
                    echo $client->__call('login', array(APP_SOAP_GO9939_KEY,$info['username'],$info['password']));
                }
                ///////连接ucenter
                if(@include_once(APP_ROOT."/uc_client/uc_config.php")){
                    include_once(APP_ROOT."/uc_client/client.php");
                    echo uc_user_synlogin($uid);
                }
                if(!empty($info['backurl'])){
                    Zend_Adver_Js::helpJsRedirect($info['backurl'],2,"登陆成功"); 
                }else{  
					if($_COOKIE['member_uType']==2){
						Zend_Adver_Js::helpJsRedirect(HOME_9939_URL."doctor/?uid=".$uid,2,"登陆成功");
					}else{
						Zend_Adver_Js::helpJsRedirect(HOME_9939_URL."user/?uid=".$uid,2,"登陆成功");
					}
                }
            } else {
                
                Zend_Adver_Js::GoToTop('/login',"登陆失败");
                                 
            }
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    public function checkloginajaxAction() {
        $info = $this->getRequest()->getParam('info'); 
        try {
             ///////连接ucenter
            if(@include_once(APP_ROOT."/uc_client/uc_config.php")){
                include_once(APP_ROOT."/uc_client/client.php"); 
                list($uid, $username, $password, $email) = uc_user_login($info['username'],$info['password']);
                $row = $this->Member_obj->get_one_by_id($uid);
                if($uid > 0) {
                    setcookie('member_uID',$row['uid'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_username',$row['username'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_uType',$row['uType'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_nickname',$row['nickname'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_pic',str_replace(APP_ROOT,"",APP_PIC_ROOT)."/".$row['pic'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_credit',$row['credit'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_experience',$row['experience'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    setcookie('member_ip',$_SERVER['REMOTE_ADDR'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    if(@include_once(APP_DATA_PATH.'/data_usergroup.php')){
                        $tmp_usergroup_array = $_SGLOBAL['usergroup'];
                        $usergroup_array = array();
                        foreach ($tmp_usergroup_array as $k=>$v){
                            if($row['credit'] >= $v['creditlower'] && $row['uType'] == $v['uType']){
                                $usergroup_array = $v;
                            }
                        }
                        setcookie('member_grouptitle',$usergroup_array['grouptitle'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                        setcookie('member_groupname',$usergroup_array['groupname'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                        setcookie('member_groupicon',"/".$usergroup_array['icon'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
                    }
                } else{
                    echo json_encode(array("error" => "用户名密码错误","url"=>""));
                    exit;
                }
                ////////连接商成
                $ucenter="";
                ////////连接商成
                if(class_exists("SoapClient",false)){
                   $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php','uri'=>'go9939','encoding'=>'utf8'));
                   $ucenter=$client->__call('login', array(APP_SOAP_GO9939_KEY,$info['username'],$info['password']));
                }
                $ucenter.=uc_user_synlogin($uid);
                $ucenter.="<script>try{setTimeout(function() { tiaozhuan(); }, 1000);}catch(e){};</script>";
                if(!empty($info['backurl'])){
                    echo json_encode(array("error" => "1","url"=>$info['backurl'],"html"=>$ucenter));
                    exit;
                }else{  
					if($row['uType']==2){
					    echo json_encode(array("error" => "1","url"=>HOME_9939_URL."doctor/?uid=".$uid,"html"=>$ucenter));
                        exit;
					}else{
					    echo json_encode(array("error" => "1","url"=>HOME_9939_URL."user/?uid=".$uid,"html"=>$ucenter));
                        exit;
					}
                }
            }else{
                $uid=$this->Member_obj->checklogin($info, $error);
                if($uid) {
                     ////////连接商成
                    if(class_exists("SoapClient",false)){
                       $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php','uri'=>'go9939','encoding'=>'utf8'));
                       $html=$client->__call('login', array(APP_SOAP_GO9939_KEY,$info['username'],$info['password']));
                    }
                    $html.="<script>try{setTimeout(function() { tiaozhuan(); }, 1000);}catch(e){};</script>";
                    if(!empty($info['backurl'])){
                        echo json_encode(array("error" => "1","url"=>$info['backurl'],"html"=>$html));
                        exit;
                    }else{  
    					if($_COOKIE['member_uType']==2){
    					    echo json_encode(array("error" => "1","url"=>HOME_9939_URL."doctor/?uid=".$uid,"html"=>$html));
                            exit;
    					}else{
    					    echo json_encode(array("error" => "1","url"=>HOME_9939_URL."user/?uid=".$uid,"html"=>$html));
                            exit;
    					}
                    }
                } else {
                    echo json_encode(array("error" => "用户名密码错误","url"=>""));
                    exit;
                }
            }
        }catch(Exception $e) {
            echo json_encode(array("error" => "系统繁忙，请稍后再试","url"=>""));
            exit;
        }
    }
}
?>