<?php
/**
   *##############################################
   * @FILE_NAME :CollectindexController.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Thu Sep 24 10:31:32 CST 2009ead 1.0
   * @DATE : Thu Sep 24 10:31:32 CST 2009
   *
   *==============================================
   * @Desc : 收藏列表
   *==============================================
   */
class CollectlistController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view = Zend_Registry::get("view");

		//加载收藏类
		Zend_Loader::loadClass('Collect',MODELS_PATH);
		$this->Collect_obj = new Collect();

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();

		//加载日志
		Zend_Loader::loadClass('Blog',MODELS_PATH);
		$this->Blog = new Blog();

		//加载部落
		Zend_Loader::loadClass('Buluo',MODELS_PATH);
		$this->Buluo = new Buluo();

		//加载话题
		Zend_Loader::loadClass('Thread',MODELS_PATH);
		$this->Thread = new Thread();

		parent::init();
	}

	/**
  	 * 收藏列表
  	 * 说明 在字段uids里 用户uid的存储格式为：x + uid + x 便于检索
  	 */
	public function indexAction()
	{
		//获取当前用户的cookie
		$tmp_cookie_array = $this->Member_obj->getCookie();
		$tmp_uid     = intval($tmp_cookie_array['uid']);
		if($tmp_uid<1){
			Zend_Adver_Js::Goback("请先登录");
		}

		//获取收藏列表
		$tmp_idtype = $this->_getParam("idtype");
		$tmp_idtype = $tmp_idtype?$tmp_idtype:"blog";
		//uid两边加x是为了检索不出错
		$where = " idtype='{$tmp_idtype}' and uids like '%x".$tmp_uid."x%' ";

		//设置分页
		$count        = 20;
		$tmp_page_obj = Zend_Registry::get('PageClass');
		$this->_my_params =	$this->_request->getParams();
		$tmp_page_var = 'page'; //分页变量
		$tmp_page_now = $this->_getParam($tmp_page_var); //当前页码
		$tmp_page_url = '/Collectlist'; //URL
		$tmp_page_obj->setpublic($this->_my_params, $tmp_page_var);
		$num = $this->Collect_obj->GetCount($where);
		$tmp_page_obj->set($num, $tmp_page_now, $tmp_page_url, $count);
		$offset = ($tmp_page_obj->nowPageNum - 1) * $count;

		$this->view->pagehtml = $tmp_page_obj->output(1); //返回分页控制HTML
		$collect_list         = $this->Collect_obj->List_Collect($where, $order, $count, $offset);
		foreach ((array) $collect_list as $k=>$v){
			//初始化显示数据
			$result_arr   = $this->resultarr($v['idtype'],$v['id']);
			if($result_arr['title']!=""){
				$collect_list[$k]['titlename'] = $result_arr['title'];
				$collect_list[$k]['url']       = $result_arr['url'];
				$collect_list[$k]['uptime']    = $result_arr['uptime'];
			}else{ 
				//如果此条原始信息已经被系统删除
				unset($collect_list[$k]);
			}
		}

		$this->view->result   = $collect_list;
		$this->view->idtype   = $tmp_idtype;
		echo $this->view->render("home/collect_list.phtml");
	}

	/**
	 * 初始化收藏的标题和链接路径【待完善】×××××××××××××××××××××××××××××××××××××××××
	 *
	 * @param str $idtype
	 * @param int $id
	 * @return array
	 */
	public function resultarr($idtype,$id){
		$tmp_arr = array();
		if($idtype=="ask"){
			//获取一个问答

		}elseif ($idtype=="blog"){
			//获取一个日志信息
			$tmp_kk = $this->Blog->getBlogName($id);
			$tmp_arr['title'] = $tmp_kk['subject'];
			$tmp_arr['url']   = "/blog/view/id/$id";
			$tmp_arr['uptime']= $tmp_kk['dateline'];
			 
		}elseif ($idtype=="picid"){
			//获取一个图片信息

		}elseif ($idtype=="space"){
			//获取一个家园信息

		}elseif ($idtype=="thread"){
			//获取一个话题信息
			$tmp_kk = $this->Thread->getThreadName($id);
			$tmp_arr['title'] = $tmp_kk['subject'];
			$tmp_arr['url']   = "/buluo/view/tid/$id";
			$tmp_arr['uptime']= $tmp_kk['dateline'];
		}elseif ($idtype=="buluo"){
			//获取一个部落信息
			$tmp_kk = $this->Buluo->getBuluoName($id);
			$tmp_arr['title'] = $tmp_kk['buluoname'];
			$tmp_arr['url']   = "/buluo/?bid=$id";
			$tmp_arr['uptime']= $tmp_kk['dateline'];
		}
		return $tmp_arr;
	}

	public function deloneAction(){

		//获取当前用户的cookie
		$tmp_cookie_array = $this->Member_obj->getCookie();
		$tmp_uid     = intval($tmp_cookie_array['uid']);
		if($tmp_uid<1){
			Zend_Adver_Js::Goback("请先登录");
		}

		$tmp_cid = intval($this->_getParam('cid'));
		if($tmp_cid<1){
			Zend_Adver_Js::Goback("请选择您要删除的项");
		}

		$result = $this->Collect_obj->Del($tmp_cid,$tmp_uid);
		if($result){
			Zend_Adver_Js::Goback("删除成功");
		}else{
			Zend_Adver_Js::Goback("删除失败");
		}
	}

}

?>