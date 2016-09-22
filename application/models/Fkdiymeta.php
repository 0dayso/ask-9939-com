<?php
 /**
  *##############################################
  * @FILE_NAME :
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Mon Jul 06 14:17:01 CST 2009
  *==============================================
  * @Desc :  留言自定义字段
  *==============================================
  */
Zend_Loader::loadClass("Fksearch",MODELS_PATH);
class Fkdiymeta extends QModels_Ask_Table
{
		public function checkHas($data)
 		{
	 		$qxname=trim($data['fkdiymetakey']);
	 		$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("fkdiymetakey = ?", $qxname);
		    $row = $this->fetchRow($where);
		    if(!$row)
		    {
		    	return false;
		    }
		    else 
		    {
			    if($row->idfkdiymeta>0)
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
	 		$data['idfkdiymeta']=intval($var['idfkdiymeta']);
	 		$data['fkdiymetavalue']=trim($var['fkdiymetavalue']);
	 		$data['fkdiymetakey']=strtolower(trim($var['fkdiymetakey']));
	 		$data['fkdiymetatype']=intval($var['fkdiymetatype']);
	 		$data['fkdiymeta_search']=intval($var['fkdiymeta_search']);
	 		$filters = array(
		    '*'=>'StringTrim','StripTags'); 
			$validators = array(
		    'fkdiymetakey'   =>  array('Alpha','NotEmpty'),
		    'fkdiymetavalue'=> 'NotEmpty'
			);
	 		
			try{
				
				$input = new Zend_Filter_Input($filters, $validators,$data);
				if ($input->isValid("fkdiymetavalue")==false) {
				  	$error="名字不能为空";
				  	return false;
				}
				elseif ($input->isValid("fkdiymetakey")==false) {
				  	$error="键名只能是字母";
				  	return false;
				}
				
				if($data['idfkdiymeta']==0)
				{
					if($this->checkHas($data)==true)
			 		{
			 			$error="该记录已存在";
			 			return false;
			 		}
			 		
			 		unset($data['idfkdiymeta']);
	 				$ret= $this->insert($data);
				}
				else 
				{
					$info=$this->getOne($data['idfkdiymeta']);
					if($info->fkdiymetakey<>$data['fkdiymetakey'])
					{
						$key_change=true;
						$old_key=$info->fkdiymetakey;
						$new_key=$data['fkdiymetakey'];
					}
					
					if($data['fkdiymeta_search']==0)
					{
						if($info->fkdiymeta_search==1)
						{
							$drop_old=true;
							$old_key=$info->fkdiymetakey;
						}
					}
					
					$db=$this->getAdapter();
			    	$where[] = $db->quoteInto("idfkdiymeta = ?", $data['idfkdiymeta']);
			    	unset($data['idfkdiymeta']);
		 			$ret= $this->update($data,$where);
				}
				
				if($ret)
				{
					//搜索字段改名
					if($data['fkdiymeta_search']==1)
			 		{
				 		$fksearch_obj=new Fksearch();
				 		$fksearch_obj->addcol($data['fkdiymetakey']);
				 		if($key_change)
				 		{
				 			$fksearch_obj->dropcol($old_key,$error);
				 		}
			 		}
			 		
			 		
			 		//搜索属性更换成不检索 
			 		//删掉原来的key
			 		if($drop_old)
			 		{
			 			$fksearch_obj=new Fksearch();
			 			$fksearch_obj->dropcol($old_key,$error);
			 		}
			 		
			 		return $ret;
				}
					
			}catch (Exception $e)
			{
				echo $e->getMessage();
			}
	 	}
 	
	 	public function getList($data=array())
	 	{
	 		$page=intval($data['page']);
	 		if($data['pagesize']>0&&$data['page']>0) $offset=($page-1)*$data['pagesize'];
	 		else 
	 		{
	 			$offset=0;
	 		}
	 		
	 		$fkdiymetavalue=trim($data['fkdiymetavalue']);
	 		$db=$this->getAdapter();
	 		$select = $db->select();
	 		$select->from('Fkdiymeta', '*');
	 		if($fkdiymetavalue<>"")
	 		{
		    	$select->where('fkdiymetavalue = ?', $fkdiymetavalue);
	 		}
	 		
	 		$sql=$select->__toString();
	 		
	 		$total=$db->fetchAll($sql);
	 	
	 		$total=count($total);
	 		$ret['total']=$total;
	 		
	 		$select->order('idfkdiymeta');
	 		$select->limit($data['pagesize'],$offset);
			$sql=$select->__toString();
			$list=$db->fetchAll($sql);
			$ret['list']=$list;
	 		return $ret;
	 	}
	 	
	 	
	 	public function getOne($id)
	 	{
	 		if(intval($id)==0) return false;
	 		$rows=$this->find($id);
	 		$row=$rows->current();
	 		
	 		if($row->idfkdiymeta>0)
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
	 		if($ret=$this->getOne($id))
	 		{
	 			$key_name=$ret->fkdiymetakey;
	 			if($key_name)
	 			{
		 			$fksearch_obj=new Fksearch();
		 			if($ret->fkdiymeta_search==1)
		 			{
		 				$fksearch_obj->dropcol($key_name,$error);
		 				if($error)
		 				{
		 					return false;
		 				}
		 			}
	 			}
	 			$db=$this->getAdapter();
		    	$where[] = $db->quoteInto("idfkdiymeta = ?", $id);
		    	return $this->delete($where);
	 		}
	 		else 
	 		{
	 			$error="参数错误";
	 			return false;
	 		}
	 	}
	 	
	 	public function getAll()
	 	{
	 		$ret=$this->fetchAll();
	 		return $ret->toArray();
	 	}
	 	
	 	public function getbykey($key)
	 	{
	 		if(empty($key)==false)
	 		{
	 			$db=$this->getAdapter();
		    	$where[] = $db->quoteInto("fkdiymetakey = ?", $key);
		    	$obj=$this->fetchRow($where);
		    	return $obj;
	 		}
	 	}
}
?>