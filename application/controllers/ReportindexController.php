<?php
/**
   *##############################################
   * @FILE_NAME :ReportindexController.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Mon Sep 21 09:19:34 CST 2009ead 1.0
   * @DATE : Mon Sep 21 09:19:34 CST 2009
   *
   *==============================================
   * @Desc : 举报 前台调用
   *==============================================
   */
class ReportindexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view = Zend_Registry::get("view");

		Zend_Loader::loadClass('Report',MODELS_PATH);
		$this->Report_obj = new Report();

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();

		parent::init();
	}
 

	/**
  	 * 写入举报
  	 *
  	 */
	public function indexAction()
	{
		//获取当前用户的cookie
		$tmp_cookie_array = $this->Member_obj->getCookie();
		$tmp_uid     = intval($tmp_cookie_array['uid']); 
		if($tmp_uid<1){
			Zend_Adver_Js::Goback("请先登录");
		}  
		
		$tmp_message = trim($this->_getParam('message'));
		$tmp_idtype  = $this->_getParam('idtype');
		$tmp_id      = intval($this->_getParam('id'));
		$tmp_author  = $tmp_cookie_array['nickname']?$tmp_cookie_array['nickname']:$tmp_cookie_array['username'];


		if($tmp_id<1 || $tmp_idtype==""){
			Zend_Adver_Js::Goback("数据错误，操作失败！");
		}
		if($tmp_message==""){
			Zend_Adver_Js::Goback("请填写您的举报理由！");
		}
		//对举报理由做字数限制 150个字符
		$tmp_message = $this->Report_obj->getstr($tmp_message,150);

		//判断此会员是否对此项有过举报
		$tmp_where_re = " idtype='".$tmp_idtype."' and id=".$tmp_id;
		$tmp_reone    = $this->Report_obj->GetOneyOne($tmp_where_re);
 
		if(count($tmp_reone)>0){ 
			$tmp_uids_arr   = unserialize($tmp_reone['uids']);
			foreach ((array)$tmp_uids_arr as $k=>$v) {
				$tmp_uid_tt[] = $k;
			} 
			if(@in_array($tmp_uid,$tmp_uid_tt)){
				Zend_Adver_Js::Goback("您已经举报过一次了");
			}else{
				$tmp_uids_arr[$tmp_uid] = $tmp_author;
				$tmp_uids_str           = serialize($tmp_uids_arr);
				//更改此条举报
				$tmp_arr = array();
				$tmp_arr['dateline'] = time();
				$tmp_arr['reason']   = $tmp_reone['reason']."<li>".$tmp_author.":".$tmp_message."</li>";
				$tmp_arr['num']      = intval($tmp_reone['num'])+1;
				$tmp_arr['uids']     = $tmp_uids_str;
				$tpm_result = $this->Report_obj->Edit($tmp_arr,$tmp_reone['rid']);
				if($tpm_result){
					Zend_Adver_Js::Goback("举报成功");
				}else{
					Zend_Adver_Js::Goback("举报失败");
				}
			}
		}else{
			//新写入一条举报信息
			$tmp_array = array();
			$tmp_array['dateline'] = time();
			$tmp_array['id']       = $tmp_id;
			$tmp_array['idtype']   = $tmp_idtype;
			$tmp_array['num']      = 1;
			$tmp_array['uids']     = serialize(array($tmp_uid=>$tmp_author));
			$tmp_array['reason']   = "<li>".$tmp_message."</li>";
			$tpm_result = $this->Report_obj->Add($tmp_array);
			if($tpm_result){
				Zend_Adver_Js::Goback("举报成功");
			}else{
				Zend_Adver_Js::Goback("举报失败");
			}
		}
	}

}

?>