<?php
  /**
   *##############################################
   * @FILE_NAME :AjaxsearchController.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Mon Sep 28 14:56:16 CST 2009ead 1.0
   * @DATE : Mon Sep 28 14:56:16 CST 2009
   *
   *==============================================
   * @Desc :  ajax输出搜索结果
   *==============================================
   */
class AjaxsearchController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view = Zend_Registry::get("view");

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();
		//加载搜索类
		Zend_Loader::loadClass('GetSearchData',MODELS_PATH);
		$this->GetSearchData_obj = new GetSearchData();

		parent::init();
	}

	/**
  	 * 搜索列表
  	 * 
  	 */
	public function indexAction()
	{ 
		$tmp_kw   = $this->_getParam("kw")?$this->_getParam("kw"):"9939";
		$tmp_kw   = str_replace(" ","%20",$tmp_kw);
//		$tmp_xml  = file_get_contents("http://211.167.92.198:8080/ask/search?kw='".$tmp_kw."'&page=1"); 
		//初始化xml
//		$this->GetSearchData_obj->SetXmlData($tmp_xml); 
//		$tmp_list       = $this->GetSearchData_obj->GetList();  
		/*if(count($tmp_list)>=6){
			$num = 6;
		}else{
			$num = count($tmp_list);
		}*/
		$tmp_result = '<h2>与“<span class="red">'.str_replace("%20"," ",$tmp_kw).'</span>”相关的问题：</h2>';
		/*for ($i=0;$i<$num;$i++){ 
			$tmp_result .= "<dl>";
			$tmp_result .= "<dt><a href='/id/".$tmp_list[$i]['ID']."'>".$tmp_list[$i]['TITLE']."</a></dt>";
			$tmp_result .= "<dd>".$this->GetSearchData_obj->getstr($tmp_list[$i]['CONTENT'],50,true)."</dd>";
			$tmp_result .= "</dl>";
		}*/
		$askList = $this->getSearchData($tmp_kw);
		foreach ($askList as $key => $val) {
			$tmp_result .= "<dl>";
			$tmp_result .= "<dt><a href='/id/".$val['id']."'>".$val['title']."</a></dt>";
			$desc = mb_strlen($val['content'],'utf-8') > 50 ? mb_substr($val['content'],0,50,'utf-8').'...' : $val['content'];
			$tmp_result .= "<dd>".$desc."</dd>";
			$tmp_result .= "</dl>";
		}
		echo $tmp_result;
	}
	
	/**
	 * 获取搜索结果
	 * @author 林原 2010-09-13
	 * @param string $kw 关键字
	 * @return Array
	 */
	private function getSearchData($kw) {
		return array();
		Zend_Loader::loadClass('Ask',MODELS_PATH);
		$ask_obj = new Ask();
		$askList = $ask_obj->fetchAll("title LIKE '%$kw%' ",'id DESC',6);
		if($askList) {
			$askList = $askList->toArray();
		}
		return $askList;
	}
	
	function testAction() {
		$tmp_kw   = $this->_getParam("kw")?$this->_getParam("kw"):"9939";
		$tmp_kw   = str_replace(" ","%20",$tmp_kw);
//		$tmp_xml  = file_get_contents("http://211.167.92.198:8080/ask/search?kw='".$tmp_kw."'&page=1"); 
		//初始化xml
//		$this->GetSearchData_obj->SetXmlData($tmp_xml); 
//		$tmp_list       = $this->GetSearchData_obj->GetList();  
		/*if(count($tmp_list)>=6){
			$num = 6;
		}else{
			$num = count($tmp_list);
		}*/
		$tmp_result = '<h2>与“<span class="red">'.str_replace("%20"," ",$tmp_kw).'</span>”相关的问题：</h2>';
		/*for ($i=0;$i<$num;$i++){ 
			$tmp_result .= "<dl>";
			$tmp_result .= "<dt><a href='/id/".$tmp_list[$i]['ID']."'>".$tmp_list[$i]['TITLE']."</a></dt>";
			$tmp_result .= "<dd>".$this->GetSearchData_obj->getstr($tmp_list[$i]['CONTENT'],50,true)."</dd>";
			$tmp_result .= "</dl>";
		}*/
		$askList = $this->getSearchData($tmp_kw);
		foreach ($askList as $key => $val) {
			$tmp_result .= "<dl>";
			$tmp_result .= "<dt><a href='/id/".$val['id']."'>".$val['title']."</a></dt>";
			$desc = mb_strlen($val['content'],'utf-8') > 50 ? mb_substr($val['content'],0,50,'utf-8').'...' : $val['content'];
			$tmp_result .= "<dd>".$desc."</dd>";
			$tmp_result .= "</dl>";
		}
		echo $tmp_result;
	}

	public function keshilistAction(){ 
		$sAskid = $this->_getParam("askid");
		//echo "SELECT * FROM `wd_keshi` WHERE `id` IN (SELECT `classid` FROM `wd_ask` WHERE `id` IN ($sAskid))";exit;
		$aKeshi = $this->Member_obj->getBySql("SELECT * FROM `wd_keshi` WHERE `id` IN (SELECT `classid` FROM `wd_ask` WHERE `id` IN ($sAskid))");
		//print_r($aKeshi);
		if(count($aKeshi)<1){
			exit;
		}
		$aAllPid = array();
		foreach($aKeshi as $v){
			$aTmp = explode(',',$v['arrparentid']);
			$aTmpPid = array_merge($aAllPid,$aTmp);
			$aAllPid = $aTmpPid;
		}
		$aAllPid = array_unique($aAllPid);
		$sAllPid = implode(',',$aAllPid);
		$aParentKeshi = $this->Member_obj->getBySql("SELECT * FROM `wd_keshi` WHERE `id` IN ($sAllPid)");
		foreach($aParentKeshi as $v){
			$aParent[$v['id']] = $v; 
		}
		foreach($aKeshi as $k => $v){
			$aKeshi[$k]['npid'] = $aKeshi[$k]['pID'];
			$aKeshi[$k]['npidlist'] = $aKeshi[$k]['id'];
			$aKeshi[$k]['nname'] = $aKeshi[$k]['name'];
			while($aKeshi[$k]['npid']!=0){
				$aKeshi[$k]['npidlist'] = $aParent[$aKeshi[$k]['npid']]['id'] . '_' . $aKeshi[$k]['npidlist'];
				$aKeshi[$k]['nname'] = $aParent[$aKeshi[$k]['npid']]['name'] . '-->' . $aKeshi[$k]['nname'];
				$aKeshi[$k]['npid'] = $aParent[$aKeshi[$k]['npid']]['pID'];
			}
		}
		//print_r($aKeshi);
		//$sRes = '<select onchange="getKeshiList(this.value)">';
		foreach($aKeshi as $v){
			$sRes .= '<p><input type="radio" onclick="getKeshiList(this)" name="listkeshi" value="'.$v['npidlist'].'" class="fl-radio"/><label class="label">'.$v['nname'].'</label></p>';
		}
		//$sRes .= '</select>';
		echo $sRes;
	}

	public function updmemberAction(){
        exit;//过时方法，存在严重安全隐患，通过此方法，可更改任何用户的username和密码。请误启用 20110920魏鹏
		$email = $this->_getParam("email");
		$pwd = $this->_getParam("pwd");
		$where = ' username=\''. $email .'\'';
		if($this->Member_obj->get_one($where)){
			echo 1;
			exit;
		}
		$tmp_member_cookie = $this->Member_obj->getCookie();
		$uid = $tmp_member_cookie['uid'];
		$sSql = "UPDATE `member` SET `username`='".$email."',`password`='".md5($pwd)."' WHERE `uid`=".$uid;
		if($this->Member_obj->queryBySql($sSql)){
			echo 0;
			exit;
		}
	}
	
	/**
	 * 	修改密码
	 * @author 林原
	 */
	public function updatepwdAction(){
	    exit;//过时方法，存在严重安全隐患，通过此方法，可更改任何用户的密码。请误启用 20110920魏鹏
		$email = $this->_getParam("email");
		$pwd = $this->_getParam("pwd");
		$tmp_member_cookie = $this->Member_obj->getCookie();
		$uid = $tmp_member_cookie['uid'];
		$sSql = "UPDATE `member` SET `password`='".md5($pwd)."' WHERE `uid`=".$uid;
		
		if($this->Member_obj->queryBySql($sSql)){
			echo 'ok';
			exit;
		} else {
			echo 'no';
			exit;
		}
	}

	public function validatemailAction(){
	    exit;//过时方法，存在严重安全隐患，通过此方法，可随意更改用户邮箱认证。请误启用 20110920魏鹏
		$email = $this->_getParam("email");
		$where = ' username=\''. $email .'\'';
		if(!($this->Member_obj->get_one($where))){
			echo 1;
			exit;
		}
		Zend_Loader::loadClass('smtp',MODELS_PATH);
		/********************************************************************************************/
		/****************************************发送邮件********************************************/
		/********************************************************************************************/
		$smtpserver = "mail.9939.com";//SMTP服务器smtp.163.com
		$smtpserverport =25;//SMTP服务器端口
		$smtpusermail = "no-reply@9939.com";//SMTP服务器的用户邮箱
		$smtpemailto = $email;//发送给谁
		$smtpuser = "no-reply@9939.com";//SMTP服务器的用户帐号
		$smtppass = "service123";//SMTP服务器的用户密码
		$mailsubject2 = '===网站会员认证===';//邮件主题
		$mailsubject = iconv("UTF-8","GB2312","$mailsubject2");//utf-8转换为bg2312邮件编码
		$mailbody2 = '<div  style="width:780px; margin:0 auto;">
	<div  style="border-bottom:1px solid #d6d6d6; height:34px; padding:34px 0 9px 22px;">
		<a href="http://www.9939.com"><img src="http://ask.9939.com/email/images/logo.gif" style="border:none;"/></a>
	</div>
	<div  style="padding:40px 22px 20px;">
		<strong>亲爱的'.$email.'， 您好！</strong>
		<p  style=" padding:0; margin:0;font-size:12px; margin:30px 0 38px;">
			感谢您注册久久健康社区会员，愿久久健康网伴随你永久健康
		</p>
		
		<div  style="font-size:14px; line-height:23px; padding-bottom:20px;background:url(http://ask.9939.com/email/images/message_bg.gif) no-repeat center bottom; padding-bottom:25px;">
			<p  style=" padding:0; margin:0;font-size:14px; margin-bottom:10px;">您只需点击下面的确认链接，即可完成账号激活。</p>
			<p  style=" padding:0; margin:0;width:520px; font-size:14px;">
			<a href="http://ask.9939.com" style="text-decoration:underline;color:#0000fa;">http://ask.9939.com</a>进入99健康网去完善您的资料
</p>
			<p  style=" padding:0; margin:0;margin-top:35px;">(如果无法点击该URL链接地址，请将它复制并粘贴到浏览器的地址输入框，然后单击回车即可）</p>
		</div>
		
		<!--<div  style="background:url(http://ask.9939.com/email/images/message_bg.gif) no-repeat center bottom;  line-height:44px; font-size:14px;">
			您登陆久久健康网的账号是：<span style="background:#e4e4e4; padding:4px; margin-right:23px; color:#c81f03;">xishantian</span>密码：<span style="background:#e4e4e4; padding:4px; margin-right:23px; color:#c81f03;">123456</span>
		</div>-->
		
		<div  style="margin-top:15px; color:#0000fa;  font-size:12px;">
			<a href="http://www.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">去久久健康网</a>|<a href="http://ask.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">到9939问答提问</a>
		</div>
	</div>
</div>';
		//$mailbody2 = "您好!".$email."<BR>您的邮箱已经认证！点击<a href='http://www.9939.com' target='_blank'>这里</a> 进入99健康网";//邮件内容
		$mailbody = iconv("UTF-8","GB2312","$mailbody2");//utf-8转换为bg2312邮件编码
		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
		$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		$smtp->debug = false;//true是否显示发送的调试信息
			if($smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype)){
				$sSql = "UPDATE `member` SET `checkemail`=1 WHERE `username`='$email'";
				if($this->Member_obj->queryBySql($sSql)){
					echo 0;
					exit;
				}
				echo 2;
			}else{
				echo 2;
			}
		exit;
		/****************************************************************************************/
		/****************************************************************************************/
		/****************************************************************************************/
	}
}

?>
