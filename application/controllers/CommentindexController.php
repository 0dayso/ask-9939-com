<?php
/**
   *##############################################
   * @FILE_NAME :CommentindexController.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Wed Sep 16 14:42:22 CST 2009ead 1.0
   * @DATE : Wed Sep 16 14:42:22 CST 2009
   *
   *==============================================
   * @Desc :  前台调用评论
   *==============================================
   */
class CommentindexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view = Zend_Registry::get("view");

		//加载评论类
		Zend_Loader::loadClass('Comment',MODELS_PATH);
		$this->Comment_obj = new Comment();

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();

		//屏蔽关键字
		Zend_Loader::loadClass('Censor',MODELS_PATH);
		$this->Censor_obj = new Censor();

		parent::init();
	}

	/**
  	 * 写入评论
  	 *
  	 */
	public function indexAction()
	{
		//获取当前用户的cookie
		$tmp_cookie_array = $this->Member_obj->getCookie();
		if($tmp_cookie_array['uid']<1){
			Zend_Adver_Js::Goback("请先登录");
		}

		$tmp_message = trim($this->_getParam('message'));
		//去掉所有的html标签
		$tmp_message = strip_tags($tmp_message);
		//屏蔽关键字
		$tmp_message = $str = $this->Censor_obj->replace($tmp_message);
		$tmp_idtype  = $this->_getParam('idtype');
		$tmp_id      = intval($this->_getParam('id'));
		$tmp_uid     = intval($this->_getParam('uid'));
		$tmp_cid     = $this->_getParam('cid');
		$tmp_authorid= intval($tmp_cookie_array['uid']);
		$tmp_author  = $tmp_cookie_array['nickname']?$tmp_cookie_array['nickname']:$tmp_cookie_array['username'];
		$tmp_ip      = $_SERVER['REMOTE_ADDR'];
		$tmp_dateline= time();

		if($tmp_id<1 || $tmp_uid<1 || $tmp_authorid<1 || $tmp_idtype==""){
			Zend_Adver_Js::Goback("数据错误，操作失败！");
		}
		if($tmp_message==""){
			Zend_Adver_Js::Goback("请填写提交信息！");
		}

		$p = array();
		$p['idtype']  = $tmp_idtype;
		$p['id']      = $tmp_id;
		$p['uid']	  = $tmp_uid;
		$p['authorid']= $tmp_authorid;
		$p['author']  = $tmp_author;
		$p['ip']	  = $tmp_ip;
		$p['dateline']= $tmp_dateline;

		if($tmp_cid>0){
			/**针对评论的回复**/
			//获取这条评论的信息
			$tmp_where = " cid=".$tmp_cid;
			$tmp_commentinfo = $this->Comment_obj->GetOneyOne($tmp_where);
			if($tmp_commentinfo['status']==1){
				$tmp_mess_arr = @explode("##*&*##",$tmp_commentinfo['message']);
				$tmp_mess_old = $tmp_mess_arr[1];
			}else{
				$tmp_mess_old = $tmp_commentinfo['message'];
			}
			//  ##*&*## 是分隔符  存储数据分别是 原来的信息-新评论信息-原评论人-原评论时间
			$p['message']    =  $tmp_mess_old."##*&*##".$tmp_message."##*&*##".$tmp_commentinfo['author']."##*&*##".date("Y-m-d H:i:s",$tmp_commentinfo['dateline']);
			$p['status']     = 1;
		}else{
			$p['message'] = $tmp_message;
		}
		//写入评论
		$result = $this->Comment_obj->Add($p);
		if($result){
			Zend_Adver_Js::Goback("操作成功");
		}else{
			Zend_Adver_Js::Goback("操作失败");
		}
	}
	/**
	 * 删除评论
	 *
	 */
	public function delAction(){
	    $tmp_cid = $this->_getParam("cid");
		$tmp_cookie_array = $this->Member_obj->getCookie();
		if($tmp_cookie_array['uid']<1){
			Zend_Adver_Js::Goback("请先登录！");
		}
		if($tmp_cid<1){
			Zend_Adver_Js::Goback("数据错误，操作失败！");
		}
		$tmp_result = $this->Comment_obj->Del($tmp_cid);
		if($tmp_result){
			Zend_Adver_Js::Goback("操作成功");
		}else{
			Zend_Adver_Js::Goback("操作失败");
		}
	}
}

?>