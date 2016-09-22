<?php
/**
   *##############################################
   * @FILE_NAME :UserController.php
   *##############################################
   *
   * @author : xzx
   * @MailAddr : xzx747@126.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Tue Sep 15 13:53 CST 2009
   * @DATE : Tue Sep 15 13:53 CST 2009
   *
   *==============================================
   * @Desc :  会员控制器
   *==============================================
   */


Zend_Loader::loadClass('MemberDetail',MODELS_PATH);
Zend_Loader::loadClass('MemberDetailDoctor',MODELS_PATH);
class UserController extends Zend_Controller_Action
{
	private $Member_obj = '';
	private $memberDetailObj = '';
	private $AttentionCatObj = '';
	private $FeedObj = '';
	private $FriendObj = '';
	private $AttentionObj = '';
	private $member_detail_1 = 'MemberDetail';
	private $member_detail_2 = 'MemberDetailDoctor';
	private $tpl = '';

	public function init()
	{
		$this->view = Zend_Registry::get("view");
		//加载会员类
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();

		//获取留言和评论
		Zend_Loader::loadClass('Comment',MODELS_PATH);
		$this->Comment_obj = new Comment();

		parent::init();
	}

	/**
	 * 自己的空间首页
	 *
	 */
	public function indexAction()
	{
		$tmp_cookie_array = $this->Member_obj->getCookie();
		
		$uid = $this->_getParam('uid');
		//echo $uid ; exit;
		if($uid <> $tmp_cookie_array['uid'] && $uid > 0){ // 访问别人空间
			$this->look($uid);	
			exit;
		}	


		//print_r($tmp_cookie_array);exit;
		if($tmp_cookie_array['uid']){
			if(!$this->Member_obj->isLogin()) {
				Zend_Adver_Js::helpJsRedirect("/user",0,'请登录@!');
				exit;
			}
			$this->view->cookie_array = $tmp_cookie_array;
			#$uid = $this->getRequest()->getParam('id');
			$uid = $tmp_cookie_array['uid'];

			#if(!$uid) {
			#$uid = $tmp_cookie_array['uid'];
			#}

			$this->view->user_info = $this->Member_obj->get_one_by_id($uid);		#会员信息
			$this->view->user_info['group_level'] = $tmp_cookie_array['groupname'] .'<img src="'. $tmp_cookie_array['groupicon'] .'"/>';
			#print_r($this->view->user_info);
			$this->newObj($tmp_cookie_array['uType']);
			$this->view->user_info_detail = $this->memberDetailObj->get_one($tmp_cookie_array['uid']);

			$this->getFeed($tmp_cookie_array['uid']);				#获取动态
			
			//////////////////////////////////    短消息总数        //////////////////////////////////////
			Zend_Loader::loadClass('News',MODELS_PATH);
			$tmp_news_obj = new News();
			$where = $tmp_news_obj->getValue('uid') .'=\''. $uid .'\'';
			$tmp_numMessage = $tmp_news_obj->GetCount($where);
			$this->view->numMessage = $tmp_numMessage ? $tmp_numMessage : 0;
			//////////////////////////////////    短消息总数   END  //////////////////////////////////////
			
			//////////////////////////////////  系统消息总数   ///////////////////////////////////////////
			$tmp_num_friend_message_int = $this->getFriendMessageNum($uid);		#好友请求
			$tmp_num_ask_int = $this->getAskMessageNum($uid);					#问答回复
			$tmp_num_notice_int  = $this->getNoticeNum();						#系统公告
			$this->view->noticeNum = $tmp_num_friend_message_int + $tmp_num_notice_int + $tmp_num_ask_int;
			//////////////////////////////////  系统消息      ///////////////////////////////////////////
			
			////////////////////////////////// 留言板 评论     ////////////////////////////////////////////
			$this->view->bbsMessageNum = $this->getBbsMessageNum($uid, 1);			#留言数
			$this->view->bbsReMessageNum = $this->getBbsReMessageNum($uid, 1);		#留言回复数
			$this->view->commentNum = $this->getBbsMessageNum($uid, 0);				#评论
			$this->view->reCommentNum = $this->getBbsReMessageNum($uid, 0);			#评论回复数
			////////////////////////////////// 留言板  评论    ////////////////////////////////////////////
			
			
			//////////////////////////////////   好友   ///////////////////////////////////////////////
			$tmp_friend_array = $this->getFriend($uid);
			$tmp_friend_info_array = array();
			$tmp_friend_info_array[1] = $tmp_friend_info_array[2] = array();
			foreach ($tmp_friend_array as $k => $v) {
				 $tmp_array = $this->Member_obj->get_one_by_id($v['fuid']);
				 $tmp_array['linkurl'] = '/user?uid='.$v['fuid'];
				 $tmp_friend_info_array[$tmp_array['uType']][] = $tmp_array;
			}
			$this->view->friend_array = $tmp_friend_info_array[1];			#普通好友
			$this->view->friend_doc_array = $tmp_friend_info_array[2];		#医生好友
			//////////////////////////////////   好友   ///////////////////////////////////////////////
			
			
			/////////////////////////////////   广告-健康顾问推荐  /////////////////////////////////////////
			if(file_exists(APP_DATA_PATH."/data_adsplace_1.php")) {
				require_once(APP_DATA_PATH."/data_adsplace_1.php"); 
			}
			/////////////////////////////////   广告-健康日报     /////////////////////////////////////////
			if(file_exists(APP_DATA_PATH."/data_adsplace_9.php")) {
				require_once(APP_DATA_PATH."/data_adsplace_9.php"); 
			}
			if(file_exists(APP_DATA_PATH."/data_adsplace_10.php")) {
				require_once(APP_DATA_PATH."/data_adsplace_10.php"); 
			}
			if(file_exists(APP_DATA_PATH."/data_adsplace_15.php")) {
				require_once(APP_DATA_PATH."/data_adsplace_15.php"); 
			}
			$this->view->ads_list = $_ADSGLOBAL;	
	    	/////////////////////////////////   广告END          /////////////////////////////////////////
			
			
			/////////////////////////////////  相同关注  ///////////////////////////////////////////////
			if($this->view->user_info['friend']) {
				$user_dis_string = $this->view->user_info_detail[$this->memberDetailObj->getValue('dis')];
				$where  = $this->memberDetailObj->getValue('primary') .' IN ('. trim($this->view->user_info['friend'], ',') .') ';
				if(trim($user_dis_string, ', ')) {
					$where .= ' AND ('. $this->memberDetailObj->getValue('dis') .' LIKE "%'. str_replace(',', '%" OR `'. $this->memberDetailObj->getValue('dis') .'` LIKE "%', trim($user_dis_string, ', ')) .'%") ';
					$tmp_array = $this->memberDetailObj->getList($where);
					if($tmp_array) {
						$tmp_user_same_friend_array = array();
						foreach($tmp_array as $k => $v) {
							$tmp_same_dis_friend_array[] = $this->Member_obj->get_one_by_id($v['uid']);
						}
						$this->view->same_dis_friend = $tmp_same_dis_friend_array;
					}
				}
				
			}
			/////////////////////////////////  相同关注  ///////////////////////////////////////////////
			
			/**--------------------获取留言和评论-------------------------**/
			$tmp_comment_where = " idtype = 'uid' and id=".$uid;
			$tmp_comment_list  = $this->Comment_obj->getSomelist(10,$tmp_comment_where);
			foreach ((array) $tmp_comment_list as $k=>$v){
				//预处理数据 ##*&*##是回复留言和留言的分隔符
				 $tmp_comment_list[$k]['message'] = @explode("##*&*##",$v['message']);
			} 
			$this->view->userid       = $tmp_cookie_array['uid'];
			$this->view->uid          = $uid;
			$this->view->comment_list = $tmp_comment_list;
			/**--------------------留言和评论结束-------------------------**/
			
			echo $this->view->render("home/header.phtml");
			echo $this->view->render("home/space_".$tmp_cookie_array['uType'].".phtml");

		}
		else
			Zend_Adver_Js::helpJsRedirect("/",0,"请登录");
	}

	/**
	 * 登录
	 *
	 */
	public function loginAction()
	{
		$url = $this->getRequest()->getParam('url');
		if(!$url) $url = '/user/';
		if ($this->validatePost()){
			$r = $this->Member_obj->checklogin($this->_info,$msg);
			if($r) header("location:$url");
			else 
			{
				//Zend_Adver_Js::helpJsRedirect($url,0,$msg);
				$this->view->url = $url;
				echo $this->view->render("home/login.phtml");
			}
		}
		else{
			Zend_Adver_Js::Goback($this->_message);
		}
	}
	
	/**
	 * 退出
	 *
	 */
	public function logoutAction()
	{
		$comeurl = ($this->_getParam('comeurl')) ? $this->_getParam('comeurl') : "/";
		//echo $comeurl;exit;
		
		$this->Member_obj->logout($msg);
		//Zend_Adver_Js::helpJsRedirect($comeurl,0,$msg);
		$this->view->msg = '退出成功';
		$this->view->url = '/';
		echo $this->view->render("home/message.phtml");			
	}

	public function exitAction()
	{
		$ret = $this->Member_obj->Logout();
		$this->_redirect('/user');
	}

	/**
	 * 测试词语屏蔽范例 2009-09-16
	 */
	public function testcensorAction()
	{
		//加载词语屏蔽类
		Zend_Loader::loadClass('Censor',MODELS_PATH);
		$this->Censor_obj = new Censor();
		$str = $this->Censor_obj->replace("法轮功123,反共456");
		echo $str;
	}

	/**
	 * 测试积分范例 2009-09-16
	 */
	public function testcreditAction()
	{
		global $_SGLOBAL;
		$tmp_cookie_array = $_SGLOBAL['cookie'];
		//print_r($tmp_cookie_array); exit;
		if($tmp_cookie_array['uid']){
			//加载积分类
			Zend_Loader::loadClass('Credit',MODELS_PATH);
			$this->Credit_obj = new Credit();
			$str = $this->Credit_obj->updatespacestatus("get","ask_pub"); //健康问答_提问
			echo $str;
		}
		else{
			Zend_Adver_Js::helpJsRedirect("/user",0,"请先登录！");
		}

		/** 增加积分调用
		$str = $this->Credit_obj->updatespacestatus("get","ask_pub"); //健康问答_提问
		$str = $this->Credit_obj->updatespacestatus("get","ask_common_reply"); //健康问答_大众会员回复
		$str = $this->Credit_obj->updatespacestatus("get","ask_doctor_reply"); //健康问答_医生会员回复
		$str = $this->Credit_obj->updatespacestatus("get","blog"); //空间_发表博文
		$str = $this->Credit_obj->updatespacestatus("get","comment"); //空间_博文留言
		$str = $this->Credit_obj->updatespacestatus("get","resource"); //空间_医学资源 
		$str = $this->Credit_obj->updatespacestatus("get","pic"); //空间_图片
		$str = $this->Credit_obj->updatespacestatus("get","thread"); //群组_话题发表
		$str = $this->Credit_obj->updatespacestatus("get","post"); //群组_回复话题
		$str = $this->Credit_obj->updatespacestatus("get","ziliao"); //群组_资料上传
		$str = $this->Credit_obj->updatespacestatus("get","tupu"); //图片频道_上传图片
		$str = $this->Credit_obj->updatespacestatus("get","ask_reward"); //回答悬赏
		$str = $this->Credit_obj->updatespacestatus("get","commonReg"); //大众会员注册
		$str = $this->Credit_obj->updatespacestatus("get","doctorReg"); //医生会员注册
		$str = $this->Credit_obj->updatespacestatus("get","editInfo"); //完善个人资料
		$str = $this->Credit_obj->updatespacestatus("get","inviteReg"); //推荐会员注册
		$str = $this->Credit_obj->updatespacestatus("get","everydayLogin"); //每日登陆
		$str = $this->Credit_obj->updatespacestatus("get","editArchive"); //健康档案
		$str = $this->Credit_obj->updatespacestatus("get","onlineDate"); //在线时间
		**/

		/** 扣除积分调用
		$str = $this->Credit_obj->updatespacestatus("pay","ask_pub"); //问答_提问删除
		$str = $this->Credit_obj->updatespacestatus("pay","ask_common_reply"); //健康问答_大众会员回复删除
		$str = $this->Credit_obj->updatespacestatus("pay","ask_doctor_reply"); //健康问答_医生会员回复删除
		$str = $this->Credit_obj->updatespacestatus("pay","blog"); //空间_博文被删
		$str = $this->Credit_obj->updatespacestatus("pay","comment"); //空间_回复博文被删
		$str = $this->Credit_obj->updatespacestatus("pay","resource"); //空间_下载医学资源
		$str = $this->Credit_obj->updatespacestatus("pay","pic"); //空间_图片被删
		$str = $this->Credit_obj->updatespacestatus("pay","thread"); //群组_话题删除
		$str = $this->Credit_obj->updatespacestatus("pay","post"); //群组_回复删除
		$str = $this->Credit_obj->updatespacestatus("pay","ziliao"); //群组_资料被删
		$str = $this->Credit_obj->updatespacestatus("pay","tupu"); //图片频道_图片被删
		 */
	}

	/**
	* 验证POST数据
	* 
	* @return boolean
	*/
	public function validatePost() {
		$this->_info['username'] = trim($this->_getParam('username'));
		$this->_info['password'] = trim($this->_getParam('password'));

		if ($this->_info['username'] == '') {
			$this->_message = '用户名必须填写';
			return false;
		}

		if ($this->_info['password'] == '') {
			$this->_message = '密码必须填写';
			return false;
		}

		return true;
	}


	
	
	
	
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////       分隔符      ////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
	* 
	* @author kxgsy163@163.com
	* 对象实例化:用于会员详细信息
	*/
	private function newObj($type=1) {
		$type = ($type==1) ? 1 : 2;
		$this->memberDetailObj = new $this->{'member_detail_'.$type}();								#实例化会员详细信息类
	}


	public function doAction() {
		$tmp_act = strtolower($this->getRequest()->getParam('do'));
		
		#可直接访问:array
		$say_ok_array = array('lookuserinfo', 'lookhealth');
		
		#用户可设置权限：array
		#$say_not_ok_array = array();
		if(!in_array($tmp_act, $say_ok_array)) {
			if($this->Member_obj->isLogin()) {			#判断用户是否登录
				
			} else {
				Zend_Adver_Js::helpJsRedirect("/user", 1, '请登录！'); exit;
			}
		}
		if(in_array($tmp_act, get_class_methods(get_class($this)))) {
			$this->{strtolower($tmp_act)}();
		} else {
			Zend_Adver_Js::helpJsRedirect("/user", 1, '错误的访问地址');
		}
	}

	/**
	* 
	* 用户修改
	*/
	private function edit() {
		$tmp_user_cookie = $this->Member_obj->getCookie();
		$this->view->user_info = $this->Member_obj->get_one_by_id($tmp_user_cookie['uid']);
		$type = ($tmp_user_cookie['uType'] ? $tmp_user_cookie['uType'] : 1);
		$this->newObj($type);
		$tmp_user_info_detail = $this->memberDetailObj->get_one($tmp_user_cookie['uid']);
		$tmp_user_info_detail['address_array'] = explode('-', $tmp_user_info_detail['address']);
		$this->view->user_info_detail = $tmp_user_info_detail;
		$this->setAttentionCat($tmp_user_info_detail['dis'], $type);
		echo $this->view->render("home/header.phtml");
		echo $this->view->render("home/user_info_". $type .".phtml");
		echo $this->view->render("home/left.phtml");
		echo $this->view->render("home/footer.phtml");
	}


	/**
	* 
	* 用户修改
	* @param $sFlag模板文件
	*/
	private function edithealth() {
		$tmp_user_cookie = $this->Member_obj->getCookie();
		$this->view->user_info = $this->Member_obj->get_one_by_id($tmp_user_cookie['uid']);
		$type = ($tmp_user_cookie['uType'] ? $tmp_user_cookie['uType'] : 1);
		if($type!=1) {
			header("Location: /user");
			exit;
		}
		$this->newObj($type);
		$tmp_user_info_detail = $this->memberDetailObj->get_one($tmp_user_cookie['uid']);
		$tmp_user_info_detail['address_array'] = explode('-', $tmp_user_info_detail['address']);
		$this->view->user_info_detail = $tmp_user_info_detail;



		$this->setDisDefault($tmp_user_info_detail[$this->memberDetailObj->getValue('default_dis')]);	#现状疾病
		$this->setHealthOld($tmp_user_info_detail[$this->memberDetailObj->getValue('health_old')]);		#以往健康
		$this->setDisHistoryHome($tmp_user_info_detail[$this->memberDetailObj->getValue('dis_history_mom')], $tmp_user_info_detail[$this->memberDetailObj->getValue('dis_history_dad')]);	#家庭病史
		$this->setAttentionCat($tmp_user_info_detail['dis'], $tmp_user_array['uType']);

		echo $this->view->render("home/header.phtml");
		echo $this->view->render("home/user_info_health.phtml");
		echo $this->view->render("home/left.phtml");
		echo $this->view->render("home/footer.phtml");
	}




	/**
	* 
	* 查看用户首页
	*/
	private function look() {
		$uid = $this->_getParam('uid');
		if(!$uid) {
			Zend_Adver_Js::helpJsRedirect("/user",0,'参数错误！');
		}
		$this->view->user_info = $tmp_userinfo_array =  $this->Member_obj->get_one_by_id($uid);		#会员信息
		$this->newObj($tmp_userinfo_array['uType']);
		#$this->view->user_info_detail = $this->memberDetailObj->get_one($tmp_userinfo_array['uid']);
		#$this->view->user_info_detail = $this->memberDetailObj->get_one($tmp_userinfo_array['uid']);

		$this->getUserPic($uid);			#用户相册
		$this->getUserBlog($uid);			#用户最新日志


		/**--------------------获取留言和评论-------------------------**/
		$tmp_comment_where = " idtype = 'uid' and id=".$uid;
		$tmp_comment_list  = $this->Comment_obj->getSomelist(10,$tmp_comment_where);
		foreach ((array) $tmp_comment_list as $k=>$v){
			//预处理数据 ##*&*##是回复留言和留言的分隔符
			$tmp_comment_list[$k]['message'] = @explode("##*&*##",$v['message']);
		}
		$tmp_cookie_array		  = $this->Member_obj->getCookie();
		$this->view->userid       = $tmp_cookie_array['uid'];
		$this->view->uid		  = $uid;
		$this->view->comment_list = $tmp_comment_list;
//		print_r($tmp_comment_list);exit;
		/**--------------------留言和评论结束-------------------------**/
		
		
		
		/////////////////////////////  他的好友  //////////////////////////////////////
		$tmp_friend_array = $this->getFriend($uid);
		$tmp_friend_info_array = array();
		foreach ($tmp_friend_array as $k => $v) {
			 $tmp_array = $this->Member_obj->get_one_by_id($v['fuid']);
			 $tmp_array['linkurl'] = '/user?uid='.$v['fuid'];
			 $tmp_friend_info_array[] = $tmp_array;
		}
		$this->view->friend_array = $tmp_friend_info_array;
		/////////////////////////////  他的好友  //////////////////////////////////////
		
		
		
		/////////////////////////////  访客记录  ///////////////////////////////////////
		$this->setVisitor($uid);
		$this->getVisitor($uid);
		/////////////////////////////  访客记录  ///////////////////////////////////////
		
		
		///////////////////////////// 他的部落  ////////////////////////////////////////
		$this->getBuluo($uid);
		///////////////////////////// 他的部落  ////////////////////////////////////////
		
		echo $this->view->render("home/header.phtml");
		echo $this->view->render("home/space_look.phtml");
	}



	/**
	*
	* 查看用户详细信息
	*/
	private function lookuserinfo() {
		$uid = $this->getRequest()->getParam('id');
		if(!$uid) {
			Zend_Adver_Js::helpJsRedirect("/user",0,'参数错误！');
		}
		$this->view->user_info = $tmp_userinfo_array =  $this->Member_obj->get_one_by_id($uid);		#会员信息
		$this->newObj($tmp_userinfo_array['uType']);
		$this->view->user_info_detail = $this->memberDetailObj->get_one($tmp_userinfo_array['uid']);
		$this->view->user_info_detail_dis = $this->getAttentionLook($this->view->user_info_detail['dis']);


		echo $this->view->render("home/header.phtml");
		echo $this->view->render("home/user_info_look.phtml");
		echo $this->view->render("home/left.phtml");
		echo $this->view->render("home/footer.phtml");
	}



	/**
	* 
	* 查看用户健康档案
	*/
	private function lookhealth() {
		$id = $this->getRequest()->getParam('id');
		$tmp_user_info = $this->Member_obj->get_one_by_id($id);
		if($tmp_user_info['uType']!=1) {
			header("Location: /user");
			exit;
		}
		$this->view->user_info = $tmp_user_info;

		$this->newObj($tmp_user_info['uType']);
		$tmp_user_info_detail = $this->memberDetailObj->get_one($id);
		
		////////// 用户设置权限 ///////////
		if(!$this->trueOrFalse($tmp_user_info_detail['level'], $tmp_user_info['friend'])) {
			Zend_Adver_Js::Goback('您没有权限查看！');
		}
		/////////////////////////////////
		echo 'hello';
		
		$this->view->user_info_detail = $tmp_user_info_detail;

		$tmp_disdefault_string = $this->getDisDefault();
		$tmp_healthold_string = $this->getHelthOld();


		#现状健康
		$str = '';
		$tmp_user_info_detail_defaultdis = explode(',', trim($tmp_user_info_detail[$this->memberDetailObj->getValue('default_dis')], ','));
		$tmp_user_dis_default_array = explode(' ', preg_replace("/\s+/", ' ', $tmp_disdefault_string));
		foreach ($tmp_user_info_detail_defaultdis as $k => $v) {
			$str .= $tmp_user_dis_default_array[$v-1] . ' ';
		}
		$this->view->user_info_default_dis = $str;

		#以往健康
		$str = '';
		$tmp_user_info_detail_defaultdis = explode(',', trim($tmp_user_info_detail[$this->memberDetailObj->getValue('health_old')], ','));
		$tmp_user_dis_default_array = explode(' ', preg_replace("/\s+/", ' ', $tmp_healthold_string));
		foreach ($tmp_user_info_detail_defaultdis as $k => $v) {
			$str .= $tmp_user_dis_default_array[$v-1] . ' ';
		}
		$this->view->user_info_default_health = $str;


		#家庭病史
		$str_dad = $str_mom = '';
		$tmp_user_dis_default_array = explode(' ', preg_replace("/\s+/", ' ', '糖尿病 高血压 血脂异常 冠心病 中风 肥胖 癌症 其他'));
		$tmp_mom_dis_array = explode(',', trim($tmp_user_info_detail[$this->memberDetailObj->getValue('dis_history_mom')], ','));
		$tmp_dad_dis_array = explode(',', trim($tmp_user_info_detail[$this->memberDetailObj->getValue('dis_history_dad')], ','));
		foreach ($tmp_user_dis_default_array as $k => $v) {
			if(in_array(($k+1), $tmp_mom_dis_array)) {
				$str_mom .= $v . ' ';
			}
			if(in_array(($k+1), $tmp_dad_dis_array)) {
				$str_dad .= $v . ' ';
			}
		}
		$this->view->user_info_default_dad = ($str_dad ? $str_dad : '无');
		$this->view->user_info_default_mom = ($str_mom ? $str_mom : '无');



		echo $this->view->render("home/header.phtml");
		echo $this->view->render("home/user_info_health_look.phtml");
		echo $this->view->render("home/left.phtml");
		echo $this->view->render("home/footer.phtml");
	}

	/**
	* 
	* 用户信息保存
	*/
	private function save() {
		$tmp_info = $this->getRequest()->getParam('info');
		$tmp_user_cookie = $this->Member_obj->getCookie();
		$tmp_info[$this->Member_obj->getVarValue('primary')] = $tmp_user_cookie['uid'];
		$url = '/user';
		if($this->Member_obj->editInfo($tmp_info)) {

			$type = ($tmp_user_cookie['uType'] ? $tmp_user_cookie['uType'] : 1);
			$this->newObj($type);
			$tmp_detail = $this->getRequest()->getParam('detail');
			$tmp_detail['doccard'] = $this->uploadPic();
			$this->getParamDate(&$tmp_detail);			#普通会员：取得生日  医生会员：没有
			$this->getPrarmAddress(&$tmp_detail);		#居住地
			$tmp_detail[$this->memberDetailObj->getValue('primary')] =  $tmp_user_cookie['uid'];
			$this->trimValueIsArray(&$tmp_detail);	#整理数组中值为数组的值：会员详细信息（普通会员）-以往健康|家庭病史等
			#print_r($tmp_detail);exit;

			if($this->memberDetailObj->edit($tmp_detail)) {
				Zend_Adver_Js::helpJsRedirect($url, 1, '修改成功');
			} else {
				Zend_Adver_Js::helpJsRedirect($url, 1, '修改详细信息失败');
			}
		} else {
			Zend_Adver_Js::helpJsRedirect($url, 1, '修改失败');
		}
	}




	/**
	* 
	* 用户信息保存
	*/
	private function savehealth() {
		$tmp_user_cookie = $this->Member_obj->getCookie();
		$tmp_info[$this->Member_obj->getVarValue('primary')] = $tmp_user_cookie['uid'];
		$url = '/user';

		$type = ($tmp_user_cookie['uType'] ? $tmp_user_cookie['uType'] : 1);
		if($type!=1) {
			Zend_Adver_Js::helpJsRedirect($url, 1, '修改详细信息失败');
			exit;
		}
		$this->newObj($type);
		$tmp_detail = $this->getRequest()->getParam('detail');
		$tmp_detail[$this->memberDetailObj->getValue('primary')] =  $tmp_user_cookie['uid'];
		$this->trimValueIsArray(&$tmp_detail);	#整理数组中值为数组的值：会员详细信息（普通会员）-以往健康|家庭病史等

		if($this->memberDetailObj->edit($tmp_detail)) {
			Zend_Adver_Js::helpJsRedirect($url, 1, '修改成功');
		} else {
			Zend_Adver_Js::helpJsRedirect($url, 1, '修改详细信息失败');
		}
	}
	/**
	* 
	* 用户修改密码
	*/
	private function editpwd() {
		$tmp_old_pwd = $this->getRequest()->getParam('old_pwd');
		$tmp_new_pwd = $this->getRequest()->getParam('pwd');
		if(!$tmp_new_pwd || !$tmp_old_pwd) {
			Zend_Adver_Js::helpJsRedirect('/user/edit', 1, '请输入旧密码及新密码！');
			exit;
		}
		$tmp_user_cookie = $this->Member_obj->getCookie();

		$where = $this->Member_obj->getVarValue('primary') .'=\''. $tmp_user_cookie['uid'] .'\' AND '. $this->Member_obj->getVarValue('pwd') .'=\''. md5($tmp_old_pwd) .'\'';
		if($this->Member_obj->GetList($where)) {
			$param = array();
			$param[$this->Member_obj->getVarValue('primary')] = $tmp_user_cookie['uid'];
			$param[$this->Member_obj->getVarValue('pwd')] = md5($tmp_new_pwd);
			$this->Member_obj->editInfo($param);
			Zend_Adver_Js::helpJsRedirect('/user', 1, '修改成功！');
		} else {
			Zend_Adver_Js::helpJsRedirect('/user/do/do/edit', 1, '旧密码输入错误！');
			exit;
		}
	}


	private function saveheadpic() {
		$tmp_user_cookie = $this->Member_obj->getCookie();
		if($_FILES) {
			Zend_Loader::loadClass('Pic',MODELS_PATH);
			$tmp_pic_obj = new Pic();
			$_FILES1 = $_FILES;	
			$tmp_file = array();
			$tmp_filename = '';
			foreach ($_FILES1 as $k => $v){
				if($k=='head_pic' && $v['size']>0) {
					$_FILES_tmp = "";
					$_FILES_tmp[$k] = $v;
					$_FILES = $_FILES_tmp;
					$dir = '/'. date('Ym') .'/';				
					if($v['name']){
						$tmp_file = $v;
						$tmp_file['filepath'] = $dir;
						$tmp_file['filename'] = $tmp_pic_obj->uploadPic($dir, $ext);	
						$tmp_filename = $dir . $tmp_file['filename'];			
						if($tmp_pic_obj->thumb($tmp_filename, '85', '85')) {
							@unlink(APP_PIC_ROOT.$tmp_filename);
							$tmp_filename = substr($tmp_filename, 0, -strpos(strrev($tmp_filename), '/')) .'thumb_'. substr($tmp_filename, -strpos(strrev($tmp_filename), '/'));
						}
					}
				}
			}
			$param['pic'] = $tmp_filename;
			$param[$this->Member_obj->getVarValue('primary')] = $tmp_user_cookie['uid']; 
			$tmp_user_info_array = $this->Member_obj->get_one_by_id($tmp_user_cookie['uid']);
			if($this->Member_obj->editInfo($param)) {
				@unlink(APP_ROOT.$tmp_user_info_array['pic']);
				Zend_Adver_Js::helpJsRedirect('/user', 0, '头像设置成功！');
			} else {
				Zend_Adver_Js::helpJsRedirect('/user', 0, '头像设置失败！');
			}
		}
	}

	/**
	* 
	* 关注点数据初始化
	*/
	private function setAttentionCat($id='', $type='') {
		Zend_Loader::loadClass('Attention',MODELS_PATH);
		Zend_Loader::loadClass('AttentionCat',MODELS_PATH);

		if(!$type)return '';
		$this->AttentionCatObj = new AttentionCat();
		$this->AttentionObj = new Attention();
		$tmp_id = $id;
		if( intval($id)!==$id ) {
			$tmp_id_array = explode(',', trim($id, ','));
			$tmp_id = $tmp_id_array[0];
		}

		$tmp_catid_default = intval($this->AttentionObj->getCatByid($tmp_id));
		$where = $this->AttentionCatObj->getValue('type') .'='. $type;
		$tmp_catid_array = $this->AttentionCatObj->getList($where);

		$tmp_cat_option = '';
		foreach ($tmp_catid_array as $k => $v) {
			$tmp_default = ($v[$this->AttentionCatObj->getValue('primary')]==$tmp_catid_default) ? ' selected ' : '';
			$tmp_cat_option .= '<option value="'. $v[$this->AttentionCatObj->getValue('primary')] .'"'. $tmp_default .'>'. $v[$this->AttentionCatObj->getValue('name')] .'</option>';
		}
		$this->view->catid_option = $tmp_cat_option;

		if($tmp_catid_default) {
			$attention_html = '';
			$where = $this->AttentionObj->getValue('catid') .'=\''. $tmp_catid_default .'\'';
			$tmp_attention_array = $this->AttentionObj->getList($where);
			foreach ($tmp_attention_array as $k => $v) {
				$attention_html .= '<input type="checkbox" value="'. $v[$this->AttentionObj->getValue('primary')] .'" name="detail[dis][]"'. (in_array($v[$this->AttentionObj->getValue('primary')], $tmp_id_array) ? ' checked ' : '') .'>'. $v[$this->AttentionObj->getValue('name')];
			}
			$this->view->attention_html_default = $attention_html;
		}
		$this->view->catid_default = $tmp_catid_default;

	}

	/**
	* 
	* 用于会员获取生日：普通会员
	* HTML : tag : date_year|date_month|date_day
	*/
	private function getParamDate($tmp) {
		if($this->getRequest()->getParam('date_year')) {
			$tmp_date = array();
			$tmp_date[] = $this->getRequest()->getParam('date_year');
			$tmp_date[] = $this->getRequest()->getParam('date_month');
			$tmp_date[] = $this->getRequest()->getParam('date_day');
			$tmp[$this->memberDetailObj->getValue('birthday')] = implode('-', $tmp_date);
		}
		return $tmp;
	}


	/**
	* 
	* 用于获取会员居住地址
	* HTML : tag : birthprovince|birthcity
	*/
	private function getPrarmAddress($tmp) {
		if($this->getRequest()->getParam('birthprovince')) {
			$tmp_date = array();
			$tmp_date[] = $this->getRequest()->getParam('birthprovince');
			$tmp_date[] = $this->getRequest()->getParam('birthcity');
			$tmp[$this->memberDetailObj->getValue('address')] = implode('-', $tmp_date);
		}
		return $tmp;
	}



	/**
	* 
	* 设置居住地：JS
	* @param $address:地址 默认参数:北京-东城
	*/
	private function setAddressJs($address='北京-东城') {
		$tmp_address_array = explode('-', $address);
		$this->view->user_info_detail['address_array'] = array();
		$this->view->user_info_detail['address_array'] = $tmp_address_array;
	}


	/**
	* 
	* 整理数组
	* @param $tmp:预处理数组
	* @param $flag:  true:转换成字符串
	* return 二维数组
	*/
	private function trimValueIsArray($tmp=array(), $flag=false) {
		foreach ($tmp as $k => &$v) {
			if(is_array($v)) {
				$this->trimValueIsArray(&$v, true);
			}
		}
		if($flag) {
			$tmp = implode(',', $tmp);
		}
	}




	/**
	*
	* 文本默认数据加载:健康档案之现状健康
	*/
	private function setDisDefault($id='') {
		$sDisDefault = $this->getDisDefault();
		$aDisDefault = explode(' ', preg_replace("/\s+/", ' ', $sDisDefault));
		foreach($aDisDefault as $sKey => $sVal)
		{
			$sDisDef .= '<option id="SELECT_DIS_'.($sKey+1).'" value="'.($sKey+1).'">'.$sVal.'</option>';
		}
		$this->view->user_info_detail['dis_default_select'] = $sDisDef;
		$this->view->dis_default_select_js = 'addD("'.str_replace(',', '");addD("', ','. trim($id, ',')).'");';
	}

	/**
	*
	* 文本默认数据加载:健康档案之以往健康
	*/
	private function setHealthOld($id='') {
		$sHealOld = $this->getHelthOld();

		$str = '';
		$id_array = explode(',', $id);
		foreach(explode(' ', preg_replace('/\s+/', ' ', $sHealOld)) as $k => $v) {
			$str .= '<input type="checkbox" name="detail['. $this->memberDetailObj->getValue('health_old') .'][]" id="f" value="'. ($k+1) .'"'. (in_array(($k+1), $id_array) ? ' checked ' : '') .' />'. $v;
		}
		$this->view->user_info_detail['health_old_checkbox'] = $str;
	}


	/**
	*
	* 文本默认数据加载:健康档案之家庭病史
	*/
	private function setDisHistoryHome($mom='', $dad='') {
		$mom_id_array = explode(',', $mom);
		$dad_id_array = explode(',', $dad);
		$tmp_dad = $tmp_mom = '';
		for($i=1; $i<=8; $i++) {
			$tmp_mom .= '<td><input type="checkbox" name="detail['. $this->memberDetailObj->getValue('dis_history_mom') .'][]" id="f" value="'. $i .'"'. (in_array($i, $mom_id_array) ? ' checked ' : '') .' /></td>';
			$tmp_dad .= '<td><input type="checkbox" name="detail['. $this->memberDetailObj->getValue('dis_history_dad') .'][]" id="f" value="'. $i .'"'. (in_array($i, $dad_id_array) ? ' checked ' : '') .' /></td>';
		}
		$this->view->user_info_detail['mom_dis_history_checkbox'] = $tmp_mom;
		$this->view->user_info_detail['dad_dis_history_checkbox'] = $tmp_dad;
	}


	
	/**
	*
	* 现状健康数据初始化
	*/
	private function getDisDefault() {
		return '肠胃疾病 肝胆疾病 冠心病 高血压 风湿 类风湿 糖尿病 甲状腺疾病 痛风 神经心理专科 神经遗传病专科 肌无力 癫痫 心血管疾病 脑血管疾病 肾病 男不育 阳痿 早泄 遗精 附丸炎  包皮龟头炎 前列腺疾病  性功能障碍 包皮包茎 泌尿系统感染 泌尿系结石 失眠 抑郁 焦虑 精神分裂 职场减压 烦躁 沟通障碍 强迫综合征 过敏病 淋病 梅毒 白癜风 牛皮癣  性病 尖锐湿疣 荨麻疹 湿疹  眼科 耳鼻喉 口腔  肝病 寄生虫  结核病 肿瘤';

	}
	
	
	/**
	*
	* 以往健康数据初始化
	*/
	private function getHelthOld() {
		return '痛风 中风 焦虑 高血压 冠心病 糖尿病 脂肪肝 癫痫病 风湿热 关节炎 心血管病 反复感冒 肝脏或肾病 慢性疲劳症 眩晕或休克 下肢动脉闭塞 骨或关节损伤 呼吸困难或哮喘疝气 胸痛或胸闷 背部或颈部疼痛 肌肉疼痛或痉挛 经常性头痛心悸 前6个月生过孩子或目前正在哺乳期 患有其他任何可能影响运动的病状或因素';
	}


	/**
	* 
	* 取得好友动态
	* @param $id:用户ID
	*/
	private function getFeed($id='') {
		if ( !$id ) return '';
		$tmp_friend_array = $this->getFriend($id);
		$tmp_fuid_array = array();
		foreach ($tmp_friend_array as $k => $v) {
			$tmp_fuid_array[] = $v[$this->FriendObj->getValue('fuid')];
		}
		Zend_Loader::loadClass('Feed',MODELS_PATH);
		$this->FeedObj = new Feed();
		$param['uid'] = $tmp_fuid_array;
		$param['orderby'] = 'feedid';
		$param['ordersc'] = ' DESC';
		$tmp_result_array = $this->FeedObj->select_feed(0, $param, '10');
		$tmp_result_array = array_slice($tmp_result_array, 0, 10);
		foreach ($tmp_result_array as $k => &$v) {
			$tmp_title_data_array = unserialize($v['title_data']);
			$tmp_title_data_array['actor'] = $v['username'];
			$str = preg_replace("/\{([^\}]*)\}/", "\$tmp_title_data_array[\\1]", str_replace('"', '\'', $v['title_template']));
			ob_start();
			eval("\$str=\"$str\";");
			ob_end_clean();
			$v['title_template_new'] = $str;
		}
		$this->view->feedList = $tmp_result_array;
	}

	
	/**
	* 
	* 朋友列表
	* @param $uid:用户ID
	*/ 
	private function getFriend($id='') {
		if ( !$id ) return '';
		Zend_Loader::loadClass('Friend',MODELS_PATH);
		$this->FriendObj = new Friend();
		$where = $this->FriendObj->getValue('primary').'=\''. $id .'\'';
		$this->view->friendNum = $this->FriendObj->numRows($where);
		return $this->FriendObj->getList($where);
	}

	/**
	* 
	* 获取图片列表：用于首页展示
	* @param $id:用户ID
	*/
	private function getUserPic($id='') {
		if(!$id) return '';
		Zend_Loader::loadClass('Pic',MODELS_PATH);
		$where = $this->Member_obj->getVarValue('primary') .'=\''. $id .'\'';
		$tmp_pic_obj = new Pic();
		$this->view->picNum = $tmp_pic_obj->numRows($where);
		$this->view->picList = $tmp_pic_obj->list_Pic($where , $tmp_pic_obj->getValue('primary').' DESC ', '3');
	}

	/**
	* 
	* 用户日志
	* @param $id:用户ID
	*/
	private function getUserBlog($id='') {
		if(!$id) return '';
		Zend_Loader::loadClass('Blog',MODELS_PATH);
		$tmp_blog_obj = new Blog();
		$where = $tmp_blog_obj->getValue('uid') .'=\''. $id .'\'';
		$tmp_blog_num = $tmp_blog_obj->GetCount($where);			#日志总数
		$this->view->blogNum = $tmp_blog_num;
		$this->view->blogList = $tmp_blog_obj->getBlogWithLookUser($id);
	}



	/**
	* 
	* 查看用户资料：关注点展示
	* @param $id:关注点ID
	* return array
	*/
	private function getAttentionLook($id) {
		$tmp_id_array = explode(',', trim($id, ','));
		Zend_Loader::loadClass('Attention',MODELS_PATH);
		$this->AttentionObj = new Attention();
		$tmp_result_array = array();
		foreach ($tmp_id_array as $k => $v) {
			$tmp_result_array[] = $this->AttentionObj->get_one($v);
		}
		return $tmp_result_array;
	}


	
	/**
	* 
	* 医生证上传
	* return 完整文件路径、文件名
	*/
	private function uploadPic() {
		if($_FILES) {
			Zend_Loader::loadClass('Pic',MODELS_PATH);
			$tmp_pic_obj = new Pic();
			$_FILES1 = $_FILES;
			$tmp_file = array();
			foreach ($_FILES1 as $k => $v){
				if($k!='doccard') continue;
				$ext++;
				$_FILES_tmp = "";
				$_FILES_tmp[$k] = $v;
				$_FILES = $_FILES_tmp;
				$dir = date('Ym').'/';
				if($v['name']){
					$tmp_file = $v;
					$tmp_file['filepath'] = $dir;
					$tmp_file['filename'] = $tmp_pic_obj->uploadPic($dir, $ext);
				}
			}
			#print_r($tmp_file);exit;
			return '/'. trim($tmp_file['filepath'], '/') .'/'. $tmp_file['filename'];
		}
	}
	
	
	/**
	* 
	* @param $uid:用户ID
	* 返回好友请求总数
	*/
	private function getFriendMessageNum($uid=0){
		if(!$uid) return 0;
		try {
			Zend_Loader::loadClass('Friend',MODELS_PATH);
			$this->FriendObj = new Friend();
			$where = $this->FriendObj->getValue('fuid') .'=\''. $uid .'\'';
			return $this->FriendObj->numRows($where);		#好友请求
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	
	
	/**
	* 
	* @param $uid:用户ID
	* 返回问答回复总数
	*/
	private function getAskMessageNum($uid) {
		if(!$uid) return 0;
		return ;
		try {
			Zend_Loader::loadClass('Ask',MODELS_PATH);
			$tmp_ask_obj = new Ask();
			$where = '';
			return $tmp_ask_obj->numRows($where);		#问答回复总数
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	
	/**
	* 
	* 返回系统消息总数
	*/
	private function getNoticeNum() {
		try {
			Zend_Loader::loadClass('Notice',MODELS_PATH);
			$tmp_notice_obj = new Notice();
			$where = ' type=1 ';
			return $tmp_notice_obj->GetCount($where);		#系统消息
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	* 
	* @param $uid:用户ID
	* @param $sFalg 1:留言  0：评论
	* return 总数:int
	*/ 
	private function getBbsMessageNum($uid=0, $sFalg=1) {
		if(!$uid) return 0;
		try {
			Zend_Loader::loadClass('Comment',MODELS_PATH);
			$tmp_comment_obj = new Comment();
			$where = ' `uid`=\''. $uid .'\' AND `idtype`'. ($sFlag ? '=' : '<>') .'\'uid\' AND `status`=0';
			return $tmp_comment_obj->GetCount($where);		#留言数
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	* 
	* @param $sFalg 1:留言回复  0：评论回复
	* @param $uid:用户ID
	* return 总数:int
	*/ 
	private function getBbsReMessageNum($uid=0, $sFlag=1) {
		if(!$uid) return 0;
		try {
			Zend_Loader::loadClass('Comment',MODELS_PATH);
			$tmp_comment_obj = new Comment();
			$where = ' `uid`=\''. $uid .'\' AND `idtype`'. ($sFlag ? '=' : '<>') .'\'uid\' AND `status`=1';
			return $tmp_comment_obj->GetCount($where);		#留言回复
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	* 
	* @param $level  1:不公开 2：只对医生好友公开 3：只对医生公开  4：对所有人公开
	* 判断是否有权限查看
	* return true:false
	*/
	private function trueOrFalse($level='100', $friend=0) {
		if($level>='4') return true;
		if($level<='1') return false;
		$tmp_cookie_user_array = $this->Member_obj->getCookie();
		if($tmp_cookie_user_array['uType']!='2')return false;
		if($level=='2') {
			if(!$friend) return false;
			if(strpos(','.$tmp_cookie_user_array['uid'].',', $friend)===false) return false;
		}
		return true;
	}
	
	
	
	/**
	* 
	* 最近访客入库:首页
	* @param $uid:用户ID
	*/
	private function setVisitor($uid=0) {
		if(!$uid) return '';
		Zend_Loader::loadClass('Visitor',MODELS_PATH);
		$tmp_visitor_obj = new Visitor();
		$tmp_visitor_obj->do_visitor($uid, 'profile');
	}
	
	
	/**
	* 
	* 返回最近访客:首页
	* @param $uid:用户ID
	*/
	private function getVisitor($uid=0) {
		if(!$uid)return '';
		$where_arr = array(
			'oid' => $uid,
			'icon' => 'profile',
			'orderby' => 'dateline',
			'ordersc' => 'desc',
		);	
		Zend_Loader::loadClass('Visitor',MODELS_PATH);
		$tmp_visitor_obj = new Visitor();
		$result = $tmp_visitor_obj->select_visitor($where_arr, 10);
		$tmp_array = array();
		foreach($result as $k => $v) {
			$tmp_array[] = $this->Member_obj->get_one_by_id($v['uid']);
		}
		$this->view->visitorArray = $tmp_array;
	}
	
	
	private function getBuluo($id=0) {
		if(!$uid)return '';
		Zend_Loader::loadClass('Buluo',MODELS_PATH);
		$tmp_buluo_obj = new buluo();
		$where = ' moderator=\''. $id .'\'';
		$this->view->buluoNum = $tmp_buluo_obj->GetCount($where);
	}
	
}
?>