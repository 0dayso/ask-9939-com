<?php
/**
   *##############################################
   * @FILE_NAME :manage_PicController.php
   *##############################################
   *
   * @author : 矫雷
   * @MailAddr : kxgsy163@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Thu Jun 18 18:00:29 CST 2009
   * @DATE : Thu Jun 18 18:00:29 CST 2009
   *
   *==============================================
   * @Desc : 相册管理
   *==============================================
   */

Zend_Loader::loadClass('Pic',MODELS_PATH);
Zend_Loader::loadClass('Member',MODELS_PATH);

class PicController extends Zend_Controller_Action
{
	private $ViewObj;
	private $Member_obj = '';
	private $FeedObj = '';
	private $PicObj;
	private $perpage = 20;
	private $orderby = '';
	private $ordersc = 'DESC';
	private $stime = '';
	private $etime = '';
	private $sql_where = ' 1 ';
	private $friend = '';
	private $friend_level = 100;
	
	public function init() {	
    	$this->ViewObj = Zend_Registry::get('view');
    	$this->PicObj = new Pic();
    	$this->Member_obj = new Member();
    	#成员初始化数据
    	$this->ViewObj->order_by_default = $this->orderby = $this->PicObj->getValue('primary');
    	$this->getSelectFriend();						#访问权限以下拉单形式显示
    	
		parent::init();
	}

	
	
	public function indexAction() {
		try {
			$where  = $this->sql_where;
			
			$tmp_user_array = $this->Member_obj->getCookie();
			$param['uid'] = $tmp_user_array['uid'];
			if(!$param['uid']) {
				echo $this->ViewObj->render("home/login.phtml");
				//header('Location: /user');
				exit;
			}
			$where .= ' AND '. $this->PicObj->getValue('userid') .'=\''. $param['uid'] .'\'';
			$order = $this->orderby . ' ' .$this->ordersc ;
			$count = $this->perpage;												#每页条数
			
			//设置分页
			$tmp_page_obj = Zend_Registry::get('PageClass');
			$this->_my_params =	$this->trimArray($this->_request->getParams());		#去除条件中的数组
			$tmp_page_var = 'page';													#分页变量
			$tmp_page_now = $this->_getParam($tmp_page_var); 						#当前页码
			$tmp_page_url = '/manage/Pic/search';									#URL
			$tmp_page_obj->setpublic($this->_my_params, $tmp_page_var);			
			
			$num = $this->PicObj->numRows($where);									#数据总数
			$tmp_page_obj->set($num, $tmp_page_now, $tmp_page_url, $count);
			$offset = ($tmp_page_obj->nowPageNum - 1) * $count;
			$this->ViewObj->pagehtml = $tmp_page_obj->output(1);					#返回分页控制HTML			
			
			$tm_pic_array =  $this->PicObj->list_Pic($where, $order, $count, $offset);
			foreach ($tm_pic_array as $k => &$v) {
				$v['file'] = $v['thumb'] ? str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$v['filepath'].'thumb_'.$v['filename']) : str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$v['filepath'].$v['filename']);
			}
			$this->ViewObj->Pic_list = $tm_pic_array;
			$this->ViewObj->User_list = $this->getUser($this->ViewObj->Pic_list);	#获取图片作者
			#print_r($this->ViewObj->User_list);
			
			
			$this->ViewObj->friend = preg_replace("/value=\"". $this->friend_level ."\"/", "\\0 selected ", $this->friend);
			
			$this->ViewObj->userid = $tmp_user_array['uid'];
			
			echo $this->ViewObj->render('home/header.phtml');
			echo $this->ViewObj->render('home/pic_list.phtml');
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}
	
	
	public function doAction() {
		$tmp_act = $this->getRequest()->getParam('do');
		
		$say_ok_array = array('looklist');
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
	* 查看图片列表
	*/
	private function looklist() {
		$uid = $this->getRequest()->getParam('id');
		if(!$uid)return ;
		$where  = $this->PicObj->getValue('userid') .'=\''. $uid .'\'';
		$where .= $this->getSqlWhere($uid);
		
		
		$tm_pic_array =  $this->PicObj->list_Pic($where);
		foreach ($tm_pic_array as $k => &$v) {
			$v['file'] = $v['thumb'] ? str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$v['filepath'].'thumb_'.$v['filename']) : str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$v['filepath'].$v['filename']);
		}
		$this->ViewObj->Pic_list = $tm_pic_array;
		$tmp_array[0] = $this->ViewObj->Pic_list[0];
		$tmp_user_info = $this->getUser($tmp_array);	#获取图片作者
		$this->ViewObj->user_name = $tmp_user_info[$uid];
		$this->ViewObj->userid = $uid;
		echo $this->ViewObj->render('home/header.phtml');
		echo $this->ViewObj->render('home/pic_list_look.phtml');
		echo $this->ViewObj->render('home/left.phtml');
		echo $this->ViewObj->render('home/footer.phtml');
		
	}
	
	
	/**
	* 
	* 图片底层展示
	*/
	public function lookAction() {
		$uid = $this->getRequest()->getParam('id');
		if(!$uid)return ;
		$this->ViewObj->userid = $uid;
		echo $this->ViewObj->render('home/header.phtml');
		echo $this->ViewObj->render('home/pic_look.phtml');
		echo $this->ViewObj->render('home/left.phtml');
		echo $this->ViewObj->render('home/footer.phtml');
	}
	
	
	
	/**
	* 
	* 图片底层展示
	*/
	public function xmlAction() {
		$uid = $this->getRequest()->getParam('id');
		if(!$uid)return ;
		$where  = $this->PicObj->getValue('userid') .'=\''. $uid .'\'';
		$where .= $this->getSqlWhere($uid);
		
		
		$tm_pic_array =  $this->PicObj->list_Pic($where);
		foreach ($tm_pic_array as $k => &$v) {
			$v['file'] = str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$v['filepath'].$v['filename']);
			$v['thumb'] = $v['thumb'] ? str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$v['filepath'].'thumb_'.$v['filename']) : $v['file'];
		}
		$this->ViewObj->Pic_list = $tm_pic_array;
		$tmp_array[0] = $this->ViewObj->Pic_list[0];
		$tmp_user_info = $this->getUser($tmp_array);	#获取图片作者
		$this->ViewObj->user_name = $tmp_user_info[$uid];
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo $this->ViewObj->render('home/pic_look_xml.phtml');
		exit;
	}
	
	
	
	private function edit() {
		$id = $this->getRequest()->getParam('id');
		$this->ViewObj->info = $this->PicObj->get_one_Pic(intval($id));
	}
	
	
	
	/**
	* 
	* 图片保存
	*/
	private function save() {
		$param = $this->getRequest()->getParam('info');
		$act = ($param['picid']) ? 'edit_Pic' : 'add_Pic';		#动作名
		$js_title = ($param['picid']) ? '修改' : '添加';			#
		
		$tmp_user_array = $this->Member_obj->getCookie();		#取得会员信息
		
		if(!$param['picid']) {
			$tmp_pic_array = $this->upLoadFile();
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////// 会员动态 ///////////////////////////////////////////////////
			Zend_Loader::loadClass('Feed',MODELS_PATH);
			$this->FeedObj = new Feed();
			$icon = 'album';
			$title_template = '{touser} 上传了新的图片';
			$title_data['touser'] = '<a href="/user/?uid='. $tmp_user_array['uid'] .'">'. $tmp_user_array['nickname'] .'</a>';
			$this->FeedObj->insert_feed($icon, $title_template, $title_data);
			///////////////////////////////////////////// END  //////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			
			//////////////////////////////////////////// 积分处理 ////////////////////////////////////////////////////
			$this->creditDisplay('get');			#上传图片添加积分
			//////////////////////////////////////////// 积分处理 ////////////////////////////////////////////////////
		}
		
		$param['uid'] = $tmp_user_array['uid'];
		$param['dateline'] = time();
		$param['postip'] = $_SERVER['REMOTE_ADDR'];
		$param['type'] = $tmp_pic_array['type'];
		$param['size'] = $tmp_pic_array['size'];
		$param['thumb'] = $this->PicObj->thumb($tmp_pic_array['filepath'].$tmp_pic_array['filename']);
		$param['filename'] = $tmp_pic_array['filename'];
		$param['filepath'] = $tmp_pic_array['filepath'];
		
		if($this->PicObj->{$act}($param)) {
			Zend_Adver_Js::helpJsRedirect("/pic", 1, $js_title .'成功！'); 
		} else {
			Zend_Adver_Js::helpJsRedirect("/pic", 1, $js_title .'失败！'); 
		}
	}
	
	
	/**
	* 
	* 内容删除
	*/
	private function del() {
		$id = $this->getRequest()->getParam('id');
		$tmp_user_array = $this->Member_obj->getCookie();
		$param['uid'] = $tmp_user_array['uid'];
		$param['picid'] = $id;
		
		$result = $this->PicObj->get_one_Pic($id);
		if($result) {
			unlink( APP_PIC_ROOT . $result['filepath'] . $result['filename'] );
			if($result['thumb']) {
				unlink( APP_PIC_ROOT . $result['filepath'] .'thumb_'. $result['filename'] );
			}
		}
		
		
		if($this->PicObj->del_Pic($param))                                                       //假删除：数据状态为0
		{
			//////////////////////////////////////////// 积分处理 ////////////////////////////////////////////////////
			$this->creditDisplay('pay');				#删除图片减少积分
			//////////////////////////////////////////// 积分处理 ////////////////////////////////////////////////////
			Zend_Adver_Js::helpJsRedirect('/pic/', 1, '删除成功'); 
		} else {
			Zend_Adver_Js::helpJsRedirect('/pic/', 1, '删除失败');
		}
	}
	
	
	/**
	* 
	* 内容删除复选形式
	*/
	private function delallAction() {
		$picid_array = $this->getRequest()->getParam('a_picid');
		#print_r($picid_array);exit;
		if(is_array($picid_array)) {
			$url = '?page='. $this->getRequest()->getParam('page');
			if($this->PicObj->del_Pic($picid_array))                                                       //假删除：数据状态为0
			{
				Zend_Adver_Js::helpJsRedirect('/manage/Pic/search/'. $url, 1, '删除成功'); 
			} else {
				Zend_Adver_Js::helpJsRedirect('/manage/Pic/search/'. $url, 1, '删除失败');
			}
		} else {
			Zend_Adver_Js::helpJsRedirect('/manage/Pic/?page='. $this->getRequest()->getParam('page'), 1, '删除成功'); 
		}
	}
	
	
	
	
	
	
	
	
	/**
	 * fck编辑器
	 * @param $name 控件名称
	 * @param string $content 值
	 */
	private function fckAction($name='content',$content='')
	{
		@include(APP_ROOT. "/editor/Editor.php");   //加载编辑器
    	$editor = new Editor();
    	$editor->setArray(array('name'=>$name, 'value'=>$content));  
    	$this->ViewObj->editor = $editor->getEditor();
	}
	
	


	
	/**
	* 
	* 文件上传：图片
	* return 文件名 array
	*/
	private function upLoadFile() {
		$sFlag = false;
		if($_FILES) {
			//图片上传
			$_FILES1 = $_FILES;	
			$tmp_file = array();
			foreach ($_FILES1 as $k => $v){
				if($k=='pic' && $v['size']) $sFlag = true;
				$ext++;
				$_FILES_tmp = "";
				$_FILES_tmp[$k] = $v;
				$_FILES = $_FILES_tmp;
				$dir = '/'. date('Ym') .'/';				
				if($v['name']){
					$tmp_file = $v;
					$tmp_file['filepath'] = $dir;
					$tmp_file['filename'] = $this->PicObj->uploadPic($dir, $ext);						
				}
			}	
			
		} 
		if(!$sFlag) {	
			Zend_Adver_Js::helpJsRedirect('/Pic/', 0, '请选择图片！');
		}
		return $tmp_file;
		
	}
	

	
	
	
	/**
	* 
	* 文章查询
	*/
	public function searchAction() {
		$this->getSearch();
		$this->indexAction();
		exit;
	}
	
	
	
	/**
	*
	* 获取查询条件
	*/
	private function getSearch() {
		
		
	}
	
	
	
	/**
	*
	* 根据FROM提交用户名称取得用户id
	* return uid
	*/
	private function getUserByName() {
		$tmp_username = trim($this->getRequest()->getParam('username'));
		Zend_Loader::loadClass('Usermanage',MODELS_PATH);					#加载用户类
		$user = new Usermanage();											#实例化
		$tmp_user_array = $user->get_one_by_name($tmp_username);			#返回ID
		$tmp_userid_array = array();
		foreach ($tmp_user_array as $k => $v) {
			$tmp_userid_array[] = $v[$user->primary];
		}
		#return implode(',', $tmp_userid_array);							#用户名匹配失败显示全部
		return ($tmp_userid_array ? implode(',', $tmp_userid_array) : -1);	
	}
	
	
	
	/**
	*
	* 根据FROM提交用户ID取得用户信息
	* @param $id 用户ID 
	* return uid
	*/
	private function getUserById($id) {
		//return '接口未开通';
		Zend_Loader::loadClass('Usermanage',MODELS_PATH);					#加载用户类
		$user = new Usermanage();											#实例化
		$tmp_username = $user->get_one_by_id($id);
		return $tmp_username[$user->username];
	}
	
	
	
	
	/**
	*
	* @param $array 图片数组:支持二维数组
	* 返回图片作者信息
	*/
	private function getUser($array = array()) {
		$tmp_user_array = array();
		foreach($array as $k => $v) {
			if(!isset($v[$this->PicObj->getValue('userid')])) {
				$tmp_user_array[$v[$this->PicObj->getValue('userid')]] = $this->getUser($v);
			} else {
				#$tmp_user_array = '接口未开通';
				$tmp_user_array[$v[$this->PicObj->getValue('userid')]] = $this->getUserById($v[$this->PicObj->getValue('userid')]);
			}
		}
		return $tmp_user_array;
	}
	/**
	* 
	* 遍历数组：返回2维数组
	* @param $array array 多维数组则过滤否则直接返回
	*/
	private function trimArray($array = array()) {
		foreach($array as $k => $v) {
			if(is_array($v)) {
				unset($array[$k]);
			}
		}
		return $array;
	}
	
	
	
	/**
	* 
	* 相册访问权限生成下拉单形式
	*/
	private function getSelectFriend() {
		$friend_array = array('0'=>'全站可见', '1'=>'全部好友可见', '2'=>'指定好友可见', '3'=>'仅自己可见', '4'=>'指定密码');
		$str = '<option value="100">不限</option>';
		foreach($friend_array as $k => $v) {
			$str .= '<option value="'. $k .'">'. $v .'</option>';
		}
		$this->friend = $str;
	}

	
	public function loadhtmlAction() {
		$tpl = $this->getRequest()->getParam('html');
		if(!$tpl) return '';
		if($tpl=='picadd') {
			echo preg_replace("/\r\n/", '', $this->ViewObj->render("home/pic_add.phtml"));
		} else if($tpl=='picedit') {
			$this->edit();
			echo preg_replace("/\r\n/", '', $this->ViewObj->render("home/pic_edit.phtml"));
		}
	 	else if($tpl=='friendadd') {
	 		//$this->ViewObj->id = $this->getRequest()->getParam('fid');
			echo preg_replace("/\r\n/", '', $this->ViewObj->render("home/friend_add.phtml"));
		}
	}
	
	
	/**
	* 
	* friend:相册隐私设置:'0'全站用户可见,'1'为全好友可见,'2'为仅医生好友可见,'3'为仅自己可见
	* @param $uid:被访问用户
	*/
	private function getSqlWhere($uid=0) {
		if(!$uid) return ;
		Zend_Loader::loadClass('Member',MODELS_PATH);					#加载用户类
		$tmp_member_obj = new Member();
		$tmp_user_cookie = $tmp_member_obj->getCookie();
		$tmp_user_info_array = $tmp_member_obj->get_one_by_id($tmp_user_cookie['uid']);
		if(strpos($tmp_user_info_array['friend'], ','.$uid.',')!==false) {
			if($tmp_user_cookie['uType']==2) {
				$where = ' AND '. $this->PicObj->getValue('friend').'<=2';
			} else {
				$where = ' AND '. $this->PicObj->getValue('friend').'<=1';
			}
		} else {
			$where = ' AND '. $this->PicObj->getValue('friend').'<1';
		}
		return $where;
	}
	
	
	
	
	
	/**
	* 
	* 会员积分处理
	* @param $str:规则
	*/
	private function creditDisplay($str='') {
		if(!$str) return ;
		Zend_Loader::loadClass('Credit',MODELS_PATH);
		$tmp_credit_obj = new Credit();
		$tmp_credit_obj->updatespacestatus($str,"pic");		#空间_图片
	}
	
	
}
?>