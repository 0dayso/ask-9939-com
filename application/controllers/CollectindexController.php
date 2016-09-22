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
   * @Version : Mon Sep 21 11:21:49 CST 2009ead 1.0
   * @DATE : Mon Sep 21 11:21:49 CST 2009
   *
   *==============================================
   * @Desc :  收藏控制
   *==============================================
   */
class CollectindexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view = Zend_Registry::get("view");

		Zend_Loader::loadClass('Collect',MODELS_PATH);
		$this->Collect_obj = new Collect();

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();

		parent::init();
	}

	/**
  	 * 写入收藏
  	 * 说明 在uids里 用户uid的存储格式为：x + uid + x 便于检索
  	 */
	public function indexAction()
	{
		//获取当前用户的cookie
		$tmp_cookie_array = $this->Member_obj->getCookie();
		$tmp_uid     = intval($tmp_cookie_array['uid']);
		if($tmp_uid<1){
			Zend_Adver_Js::Goback("请先登录");
		}

		$tmp_idtype  = $this->_getParam('idtype');
		$tmp_id      = intval($this->_getParam('id'));

		if($tmp_id<1 || $tmp_idtype==""){
			Zend_Adver_Js::Goback("数据错误，操作失败！");
		}

		//判断此会员是否对此项有过收藏
		$tmp_where_re = " idtype='".$tmp_idtype."' and id=".$tmp_id;
		$tmp_reone    = $this->Collect_obj->GetOneyOne($tmp_where_re);

		if(count($tmp_reone)>0){
			$tmp_uids_arr = @explode(",",$tmp_reone['uids']);
			foreach ((array)$tmp_uids_arr as $k=>$v) {
				$tmp_uid_tt[] = trim($v);
			}
			$tmp_uid_one = "x".$tmp_uid."x";

			if(in_array($tmp_uid_one,$tmp_uid_tt)){
				Zend_Adver_Js::Goback("您的收藏夹中已经有了");
			}else{
				$tmp_uids_str           = $tmp_reone['uids'].",x".$tmp_uid."x";
				//更改此条收藏
				$tmp_arr = array();
				$tmp_arr['dateline'] = time();
				$tmp_arr['num']      = intval($tmp_reone['num'])+1;
				$tmp_arr['uids']     = $tmp_uids_str;
				$tpm_result = $this->Collect_obj->Edit($tmp_arr,$tmp_reone['cid']);
				if($tpm_result){
					Zend_Adver_Js::Goback("收藏成功");
				}else{
					Zend_Adver_Js::Goback("收藏失败");
				}
			}
		}else{
			//新写入一条收藏信息
			$tmp_array = array();
			$tmp_array['dateline'] = time();
			$tmp_array['id']       = $tmp_id;
			$tmp_array['idtype']   = $tmp_idtype;
			$tmp_array['num']      = 1;
			$tmp_array['uids']     = "x".$tmp_uid."x";

			$tpm_result = $this->Collect_obj->Add($tmp_array);
			if($tpm_result){
				Zend_Adver_Js::Goback("收藏成功");
			}else{
				Zend_Adver_Js::Goback("收藏失败");
			}
		}
	}

	public function DelOneAction(){
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

		$result = $this->Collect_obj->Del($cid,$tmp_uid);
		if($result){
			Zend_Adver_Js::Goback("删除成功");
		}else{
			Zend_Adver_Js::Goback("删除失败");
		}
	}

}

?>