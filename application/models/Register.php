<?php
/**
  *##############################################
  * @FILE_NAME :register.php
  *##############################################
  *
  * @author : 李军锋
  * @MailAddr : dreamcastzh@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   注册模块
  *==============================================
  */

class Register extends QModels_Ask_Table
{
	protected $_primary = 'catid';
	protected $_name="member_attention_category";
	
	public function get_gzd_cats($id=0)
	{
		//echo "select catid,catname from $this->_name where type='$id'";
		$r = $this->_db->fetchAll("select catid,catname from `$this->_name` where type='$id'");
		$str = '<option value="">请选择</option>';
		foreach ($r as $k=>$v)
		{
			$str .= "<option value=$v[catid]>$v[catname]</option>";
		}
		return $str;
	}
	
	public function get_gzd($id=0)
	{
		//echo "select catid,aname from `member_attention` where catid='$id'";
		$r = $this->_db->fetchAll("select aid,aname from `member_attention` where catid='$id'");
		$str = '';
		foreach ($r as $k=>$v)
		{
			$str .= "<label><input type='checkbox' name='dis[]' value='$v[aid]'/>$v[aname]</label>";
		}
		return $str;
	}
	
	public function register_member($info='',$info_detail='')
	{
		$this->_primary = 'uid';
		$this->_name="member";	
		$info['password'] = md5($info['password']);
		//print_r($info);
		$r = $this->insert($info);
		if($r) {		#更新用户缓存文件	#addtime 2009-10-30 @author:kxgsy163@163.com
			#echo 'hello';
			
			/**
			Zend_Loader::loadClass('Cache',MODELS_PATH);
			$tmp_cache_obj = new cache();
			$param['userid'] = $r;
			$param['nickname'] = ($info['nickname'] ? $info['nickname'] : '匿名');
			$param['pic'] = ($info['pic'] ? HOME_9939_URL . $info['pic'] : HOME_USER_DEFAULT_PIC);
			$param['uType'] = $info['uType'] ? $info['uType'] : 1;
			#print_r($param);exit;
			
			
			$tmp_cache_obj->editUser($param);
			**/
			
			// 这个缓存文件10M多，屏蔽 xzxin 2010-07-12
			
			


			Zend_Loader::loadClass('Credit',MODELS_PATH);
			$tmp_Credit_obj = new Credit();
			$str = $tmp_Credit_obj->updatespacestatus("get", ($info['uType']==1 ? 'commonReg' : 'doctorReg')); //大众会员注册|医生会员注册
		}
		else 
		{
			echo "register not ok";
		}
		#echo 'good';
		$this->_primary = 'uid';
		if($info['uType'] == 1) $this->_name="member_detail_1";
		else $this->_name="member_detail_2";
		$info_detail['uid'] = $r;
		$this->insert($info_detail);
		#echo '__---';
		return $r;
	}
	
	public function add_credit($id=0,$c)
	{	
		if($id)
		{
			$this->_db->query("update `member` set credit=$c where uid='$id'");
			return true;
		}		
	}
	
	public function get_mem($name)
	{
		if($name)
		{
			$r = $this->_db->fetchAll("select uid from `member` where username='$name'");
			if($r){
				return $r[0]['uid'];
			}else{
				return false;
			}
			
		}		
	}

}
?>