<?php
/**
   *##############################################
   * @FILE_NAME :NewsController.php
   *##############################################
   *
   * @author : ljf
   * @MailAddr :licaption@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Tue Sep 15 13:53 CST 2009
   * @DATE : Tue Sep 15 13:53 CST 2009
   *
   *==============================================
   * @Desc :  消息控制器
   *==============================================
   */

class NewsController extends Zend_Controller_Action {		
	public function init() {	
		
		$this->view = Zend_Registry::get("view");
		//加载消息类
		Zend_Loader::loadClass('Member',MODELS_PATH);
		Zend_Loader::loadClass('News',MODELS_PATH);
		$this->Member_obj = new Member();
		$this->tmp_cookie_array = $this->Member_obj->getCookie();
		
		//print_r($this->tmp_cookie_array);exit;
		//Array ( [uid] => 7 [username] => zhangsan@126.com [uType] => 1 [nickname] => 张三 [pic] => pic1 [credit] => 135 [experience] => 175 [grouptitle] => 一级 [groupname] => 萤火虫 [groupicon] => images/star_01.jpg ) 
		$this->News = new News();
		
		parent::init();
	}
	
	//我的消息
	public function indexAction() {	
		$uid = $this->tmp_cookie_array['uid'];
		if($uid){
			
			$mid = (int)$this->getRequest()->getParam('mid');
			$fid = (int)$this->getRequest()->getParam('fid');
			$hf = (int)$this->getRequest()->getParam('hf');
			$zf = (int)$this->getRequest()->getParam('zf');
			$qf = (int)$this->getRequest()->getParam('qf');
			$mid_r = (int)$this->getRequest()->getParam('mid_r');
			$mid_s = (int)$this->getRequest()->getParam('mid_s');
			$sType = $this->getRequest()->getParam('type');
			$db = $this->News->getAdapter();			
					
			$current = $this->getRequest()->getParam('current');
			$this->view->current = $current;
			$this->view->mid = $mid;
			//var_dump($mid_s);
			$this->view->mid_s = $mid_s;
			$this->view->mid_r = $mid_r;
			$this->view->hf = $hf;//回复			
			$this->view->zf = $zf;//转发			
			$this->view->qf = $qf;//群发
			$mid = $mid_s ? $mid_s : ($mid_r ? $mid_r : $mid);
			$r = $this->News->getMessage($mid);
			//print_r($r);
			$this->view->cuid = $r['fromuid'];
			$this->view->content = $r['content'];
			$this->view->name = $r['name'];
			$this->view->friend = $this->News->get_friend($uid,$this->view->cuid,$hf,$zf,$qf,$fid);
			$uids = ','.$uid.',';		
			if($this->getRequest()->getParam('src')==NULL){
				$receive = $this->pages(" touid='$uid' and uids not like '%$uids%'",'r');//收件箱
				$send = $this->pages(" fromuid='$uid' and uids not like '%$uids%'",'s');//发件箱				
				$this->view->receiveBoxInfo = $receive;
				$this->view->sendBoxInfo = $send;
			}else{
				if($sType=='r'){
					$r = $this->pages(" touid='$uid' and uids not like '%$uids%'",'r');
				}elseif($sType=='s'){
					$r = $this->pages(" fromuid='$uid' and uids not like '%$uids%'",'s');
				}
				echo json_encode($r);
				exit;
			}
			
			//系统消息，好友请求
			$per = 10;
			$p = $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
			$w = " uid=$uid";
			$sys_f = $this->News->get_sys_fri($p,$per,$w);
			$this->view->sys_fri_Info = $sys_f;
			
			//站内公告
			$sys_notice = $this->News->get_sys_notice();
			$this->view->sys_notice_info = $sys_notice;
			
			
			$this->view->seo_array = array("我的消息","消息","页面描述");
			echo $this->view->render("home/news.phtml");
		}	
		else 
			echo $this->view->render("home/login.phtml");
	}
	
	public function viewAction(){
		try {
			$mid = $this->getRequest()->getParam('mid');
			$r = $this->News->getMessage($mid);
			//print_r($r);
			$this->view->content = $r;
			$this->view->seo_array = array("查看我的消息","消息","页面描述");
			echo $this->view->render("home/news_view.phtml");
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}	
	}
	
	// 发送信息
	public function sendAction(){
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($this->validatePost('send')) {
				if ($this->News->send_News($this->_send)) {
					Js_Goto('发送成功', '/news/');
				}
				else {
					Js_Goto('发送失败');
				}
			}
			else {
				Js_Goto($this->_message);
			}
		}
	}
	
	//删除信息
	public function delAction(){
		$uid = $this->tmp_cookie_array['uid'];
		$mid = trim($this->getRequest()->getParam('mid'));
		if($mid && $uid)
		{
			if($this->News->del_News($mid,$uid)) Js_Goto('删除成功', '/news/');
		}		
	}
	
	//清空信息
	public function clearAction(){
		$box = trim($this->getRequest()->getParam('box'));
		$uid = $this->tmp_cookie_array['uid'];
		if($box && $uid)
		{
			$bname = $box == 'r' ? '收件箱' : '发件箱';
			if($this->News->clear_News($box,$uid)) Js_Goto('清空'.$bname.'成功', '/news/');
		}		
	}	
	
	//验证好友请求
	public function yzAction(){
		$uid = $this->getRequest()->getParam('uid');
		$v = $this->getRequest()->getParam('v');
		if($uid && $v)
		{
			try {
				if($this->News->yz_fri($this->tmp_cookie_array['uid'],$uid,$v))
				{
					//修改member表的 friend 和 friendnum；
					$this->News->upate_fri($this->tmp_cookie_array['uid']);
					if($v == 1)
					{
						//把当前登录用户添加成$uid的好友
						$this->News->insert_fri($uid,$this->tmp_cookie_array['uid']);
						//添加动态消息
						$this->News->add_feed($uid);
						Js_Goto('操作成功', '/news/');
					}
				}
				else Js_Goto('验证失败', '/news/');
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}
		Js_Goto('参数错误', '/news/');		
	}
	
	public function validatePost($status='') {
		$uid = $this->tmp_cookie_array['uid'];
		$dateline = time();
		$this->_send['touid'] = $this->getRequest()->getParam('touid');
		if(empty($this->_send['touid']))
		{
			$this->_message = '请选择好友';
			return false;
		}
		$this->_send['content'] = trim($this->getRequest()->getParam('content'));
		if($this->_send['content'] == '') 
		{
			$this->_message = '请填写消息内容';
			return false;
		}	
		$this->_send['fromuid'] = $uid;
		$this->_send['dateline'] = $dateline;
		
		
		return true;
	}

	public function pages($where,$type=''){
		
		$order = 'mid desc';
		$count = 2;//每页条数
		$tmp_page_obj = Zend_Registry::get('PageClass');
		$tmp_page_var = 'page'; //分页变量
		$tmp_page_now = $this->_getParam($tmp_page_var); //当前页码
		$tmp_page_url = '/news/index/type/'.$type; //URL
		$tmp_page_obj->setpublic($this->_my_params, $tmp_page_var,2);
		//var_dump($tmp_page_obj);

					
		$num = $this->News->GetCount($where);
		$tmp_page_obj->set($num, $tmp_page_now, $tmp_page_url, $count);
		$offset = ($tmp_page_obj->nowPageNum - 1) * $count;
		$this->view->pagehtml = $tmp_page_obj->output(1);//返回分页控制HTML			
		$this->view->info = $this->News->List_News($where, $order, $count, $offset); 	
		return array('pagehtml'=>$this->view->pagehtml,'info'=>$this->view->info,'type'=>$type);
	}
	
}
?>
