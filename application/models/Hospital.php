<?php
 /**
  *##############################################
  * @FILE_NAME :User.php
  *##############################################
  * @author : frozen
  * @mail : 276896323@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Fri Jun 19 17:12:03 CST 2009
  *==============================================
  * @Desc :  用户操作类
  *==============================================
  */

Zend_Loader::loadClass("Juese",MODELS_PATH);
class Hospital extends QModels_Ask_Table 
{
	public function init() {
        parent::init();
		$this->db_jb =  $this->db_dzjb_read;		
	}
	
	/**
	* @Desc 
	* @param
	* @param
	* @param
	* @return 
	*/
	
	 protected $_name="hospital";
	 
	
	/**
	* @Desc 验证登陆
	* @param $data array
	* @return bool
	*/
	public function Checklogin($data,&$error)
	{
        
		$username=$data['username'];
		$pwd=md5($data['password']);
		$sql="select * from hospital where username='".$username."' and pwd='".$pwd."'";
		$row=$this->db_jb->fetchRow($sql);
		
    	if($row['hospital_id'])
    	{
    		$manage = new Zend_Session_Namespace('manage');
			$manage->unlock();
    		$manage->user = $row;
			$manage->lock();
    		return true;
    	}
    	else
    	{
    		$error="登录失败";
    	}
	}
	
	/**
	* @Desc 退出系统
	* @param void
	* @return bool
	*/
	public function Logout()
	{
		$manage = new Zend_Session_Namespace('manage');
		if($manage->isLocked())
		{
			$manage->unlock();
		}
		
		return $manage->unsetAll();
	}
	
	/**
	* @Desc 验证用户是否已经登录
	* @param void
	* @return bool
	*/
	public function CheckIsLogin()
	{
		try {
			Zend_Loader::loadClass("Asession",MODELS_PATH);
			$sess_obj=new Asession();
			$sess=$sess_obj->Get_Manage_Session();
					    
			if(intval($sess['hospital_id'])>0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	/**
	* @Desc 获取某用户信息
	* @param void
	* @return bool
	*/
	public function get_one($hospital_id){
		$sql="select * from hospital where hospital_id=".$hospital_id."";
		$row=$this->db_jb->fetchRow($sql);
		return $row; 
	}
    /**
	* @Desc 更新某用户信息
	* @param void
	* @return bool
	*/
	function update_one($info,$hospital_id){
		try{
		     $this->db_jb->update("hospital",$info,"hospital_id=".$hospital_id."");
		}catch(Exception $e){
			echo $e;
		}
	}
	 /**
	* @Desc 按条件查询用户信息
	* @param void
	* @return bool
	*/
	function get_one_get($where){
		$sql="select * from hospital where $where";
		try{
			$row=$this->db_jb->fetchRow($sql);
		}catch(Exception $e){
			var_dump($e);
		}
		return $row;
	}
	
}
?>