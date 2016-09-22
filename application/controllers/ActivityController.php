<?php
/**
   *##############################################
   * @FILE_NAME :Act2010springfoodController.php
   *##############################################
   *
   * @author : xzxin
   * @MailAddr : xzx747@126.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @DATE : Tue Aug 21 9:21 CST 2010
   *
   *==============================================
   * @Desc :  活动
   *==============================================
   */
Zend_Loader::loadClass('MemberDetail',MODELS_PATH);
class ActivityController extends Zend_Controller_Action
{
	
	private $memberDetail_obj;
	
	public function init()
	{
		$this->view = Zend_Registry::get("view");		

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();
		
		
		$tmp_cookie_array = $this->Member_obj->getCookie();
		$this->tmp_uid = intval($tmp_cookie_array['uid']);
		$this->nickname = $tmp_cookie_array['nickname'];
		
		$this->memberDetail_obj = new MemberDetail();
		
		parent::init();
	}


	public function m201008Action()
	{
		//获取参加活动的人数
		$count = $this->Member_obj->GetMemberCount("`nickname`  LIKE '爱m%' AND huodongshenhe=1 AND status=1");
		$this->view->count = 10-$count;
		
		if($this->getRequest()->isPost()) {
			if($this->view->count<=0) {
				echo 'end';
				//Zend_Adver_Js::helpJsRedirect('/activity/m201008',0,"活动已经结束！");
			} elseif (!$this->tmp_uid) {
				//Zend_Adver_Js::helpJsRedirect('/activity/m201008',0,"请先登录！");
				echo 'noreg';
			} else {
				//$data = $this->_getParam('uinfo'); //获取详细信息

				$nickname = $this->_getParam('nickname'); //获取昵称
				$truename = $this->_getParam('truename');//真实名字
				$telephone = $this->_getParam('telephone');//电话
				$address = $this->_getParam('address');//地址
				$postcode = $this->_getParam('postcode');//邮编

				$data = array('truename'=>$truename,'telephone'=>$telephone,'address'=>$address,'postcode'=>$postcode);


				if(empty($nickname) || empty($truename) || empty($telephone) || empty($address) || empty($postcode) ) {
					echo 'nickname:'.$nickname;
					echo 'truename:'.$truename;
					echo 'telephone:'.$telephone;
					echo 'address:'.$address;
					echo 'postcode:'.$postcode;
					echo 'null';
					exit;
				}
				$this->memberDetail_obj->update($data,'uid='.$this->tmp_uid); //修改详细信息
				$this->Member_obj->update(array('nickname'=>$nickname),'uid='.$this->tmp_uid); //修改昵称 
				echo 'ok';
				//Zend_Adver_Js::helpJsRedirect('/',0,'提交信息成功!');
			}
			exit;	
		}
		
		//获取用户详细信息
		if($this->tmp_uid) {
			$userInfo = $this->memberDetail_obj->fetchRow('uid='.$this->tmp_uid);
			$this->view->userInfo = $userInfo;
			$this->view->nickName = $this->nickname;
		}
		
		echo $this->view->render('huodong/201008.phtml');
	}
		
}

?>