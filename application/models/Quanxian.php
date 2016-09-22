<?php
 /**
  *##############################################
  * @FILE_NAME :Quanxian.php
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Mon Jun 22 17:23:24 CST 2009
  *==============================================
  * @Desc :  文件资源权限类
  *==============================================
  */
 class Quanxian extends QModels_Ask_Table 
 {
 		protected $_name="admin_role_priv";
 	 	public function checkHas($data)
 		{
	 		$qxname=trim($data['qxname']);
	 		$qxcontroller=trim($data['qxcontroller']);
			$qxaction=trim($data['qxaction']);
	 		$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("qxname = ?", $qxname);
	    	$where[] = $db->quoteInto("qxcontroller = ?", $qxcontroller);
	    	$where[] = $db->quoteInto("qxaction = ?", $qxaction);
		    $row = $this->fetchRow($where);
		    if(!$row)
		    {
		    	return false;
		    }
		    else 
		    {
			    if($row->idqx>0)
			    {
			    	return true;
			    }  
			    else 
			    {
			    	return false;
			    }
		    }
 		}
 	
	 	public function add($var,&$error=null)
	 	{
	 		$data['idqx']=intval($var['idqx']);
	 		$data['qxname']=trim($var['qxname']);
	 		$data['qxcontroller']=trim($var['qxcontroller']);
	 		$data['qxaction']=trim($var['qxaction']);
	 		$data['qxdesc']=trim($var['qxdesc']);
	 		if($data['idqx']==0)
	 		{
	 			if($data['qxname']=="")
		 		{
		 			$error="名字为空";
		 			return ;
		 		}
		 		
		 		if($data['qxcontroller']=="")
		 		{
		 			$error="控制器名字为空";
		 			return ;
		 		}
		 		
		 		if($data['qxaction']=="")
		 		{
		 			$error="操作名字为空";
		 			return ;
		 		}
		 		
		 		if($this->checkHas($data)==true)
		 		{
		 			$error="该记录已存在";
		 			return false;
		 		}
		 		
	 			//$data['jsctime']=time();
	 			unset($data['idqx']);
	 			$ret= $this->insert($data);
	 			
	 		}
	 		else
	 		{
	 			$db=$this->getAdapter();
		    	$where[] = $db->quoteInto("idqx = ?", $data['idqx']);
		    	unset($data['idqx']);
	 			$ret=$this->update($data,$where);
	 			if($ret==0) $ret=true;
	 		}
	 		
	 		
	 		return $ret;
	 	}
 	
	 	public function getList($data=array())
	 	{
	 		$page=intval($data['page']);
	 		if($data['pagesize']>0&&$data['page']>0) $offset=($page-1)*$data['pagesize'];
	 		else 
	 		{
	 			$offset=0;
	 		}
	 		
	 		$mod=trim($data['mod']);
	 		$db=$this->getAdapter();
	 		$select = $db->select();
	 		$select->from('admin_role_priv', '*');
	 		if($mod<>"")
	 		{
		    	$select->where('qxcontroller = ?', $mod);
	 		}
	 		
	 		$sql=$select->__toString();
	 		$total=$db->fetchAll($sql);
	 		$total=count($total);
	 		$ret['total']=$total;
	 		
	 		$select->order('idqx');
	 		$select->limit($data['pagesize'],$offset);
			$sql=$select->__toString();
			$list=$db->fetchAll($sql);
			$ret['list']=$list;
	 		return $ret;
	 	}
	 	
	 	public function getModuleList()
	 	{
	 		$sql="SELECT distinct(`qxcontroller`) FROM admin_role_priv";
	 		$db=$this->getAdapter();
	 		$ret=$db->fetchAll($sql);
	 	
	 		return $ret;
	 	}
	 	
	 	public function getOne($id)
	 	{
	 		if(intval($id)==0) return false;
	 		$rows=$this->find($id);
	 		$row=$rows->current();
	 		
	 		if($row->idqx>0)
	 		{
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
		    	$where[] = $db->quoteInto("idqx = ?", $id);
		    	return $this->delete($where);
	 		}
	 		else 
	 		{
	 			$error="参数错误";
	 			return false;
	 		}
	 	}
	 	
	 	public function getControllerAction()
	 	{
	 		$sql="select * from admin_role_priv order by qxcontroller asc";
	 		$db=$this->getAdapter();
	 		$ret=$db->fetchAll($sql);
	 
	 		return $ret;
	 	}
	 	
	 	/**
	 	* @Desc 得到权限编号
	 	* @param array $data
	 	* 	           $data=array('qxcontroller'=>,'qxaction'=>);
	 	* @return int 
	 	*/
	 	public function getQxId($data)
	 	{
	 		$controller=$data['controller'];
	 		$action=$data['action'];
	 		
	 		if($controller&&$action)
	 		{
		 		$db=$this->getAdapter();
		 		$where[] = $db->quoteInto("qxcontroller = ?", $controller);
		 		$where[] = $db->quoteInto("qxaction = ?", $action);
		 		$ret=$this->fetchAll($where);
		 		$ret=$ret[0];
		 		return intval($ret['idqx']);
	 		}
	 		
	 		return 0;
	 	}
 }
?>