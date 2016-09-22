<?php
 /**
  *##############################################
  * @FILE_NAME :Juese.php
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Mon Jun 22 17:08:37 CST 2009
  *==============================================
  * @Desc :  用户角色类
  *==============================================
  */
 
 class Juese extends QModels_Ask_Table
 {
 	protected $_name="admin_role";
 	
 	public function checkHas($data)
 	{
 		try{
	 		$username=$data['jsname'];
			$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("jsname = ?", $username);
		    $row = $this->fetchRow($where);
			
		    if(!$row)
		    {
		    	return false;
		    }  
		    else 
		    {
		    	if($row->idjs>0)
		    	{
		    		return true;
		    	}
		    	else 
		    	{
		    		return false;	
		    	}
		    }
 		}
 		catch (Exception $e)
 		{
 			echo $e->getMessage();
 		}
 	}
 	
 	public function add($var,&$error=null)
 	{
 		$data['idjs']=intval($var['idjs']);
 		$data['jsname']=trim($var['jsname']);
 		$data['jsdesc']=trim($var['jsdesc']);
 		$data['jsquanxian']=serialize($var['jsquanxian']);
 	//	$data['jscategory']=serialize($var['jscategory']);
 	//	$data['jsmenus']=serialize($var['jsmenus']);
 		
 		if($data['idjs']==0)
 		{
 			if($data['jsname']=="")
	 		{
	 			$error="名字为空";
	 			return ;
	 		}
	 		
	 		if($this->checkHas($data)==true)
	 		{
	 			$error="用户角色已存在";
	 			return false;
	 		}
	 		
 			$data['jsctime']=time();
 			unset($data['idjs']);
 			return $this->insert($data);
 		}
 		else
 		{
 			$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("idjs = ?", $data['idjs']);
	    	unset($data['idjs']);
 			return $this->update($data,$where);
 		}
 	}
 	
 	public function getList($data=array())
 	{
 		$list= $this->fetchAll();
 		return $list->toArray();
 	}
 	
 	public function getAll()
 	{
 		$list= $this->fetchAll();
 		return $list->toArray();
 	}
 	
 	public function getOne($id)
 	{
 		if(intval($id)==0) return false;
 		$rows=$this->find($id);
 		$row=$rows->current();
 		if($row->idjs>0)
 		{
 			$row->jsquanxian=unserialize($row->jsquanxian);
 		
 			if(count($row->jsquanxian)==0) $row->jsquanxian=array();
 			
 			
 			return $row;
 		}
 		else
 		{
 			return null;
 		}
 	}
 	
 	public function del($id,&$error)
 	{
 		if($this->getOne($id))
 		{
 			$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("idjs = ?", $id);
	    	return $this->delete($where);
 		}
 		else 
 		{
 			$error="参数错误";
 			return false;
 		}
 	}
 	
 	public  function GetOptionsArr(){
		Zend_Loader::loadClass("Asession",MODELS_PATH);
		$tmp_sess_obj = new Asession();
		$tmp_sess_arr = $tmp_sess_obj->Get_Manage_Session();
		//超级管理员角色
		if($tmp_sess_arr['idjs']>1){
			$where="`idjs` =".$tmp_sess_arr['idjs'];
		}
		
		$return_arr=array();
		$result = $this->fetchAll($where);
		if($result){
			$tmp_arr = $result->toArray();
			foreach ((array)$tmp_arr AS $key => $val){

				$return_arr[$val['idjs']]=$val['jsname'];

			}
			return $return_arr;
		}else{
			return null;
		}
	}
 }
?>