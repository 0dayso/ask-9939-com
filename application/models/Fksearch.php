<?php
 /**
  *##############################################
  * @FILE_NAME :FKsearch.php
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Mon Jul 06 15:43:19 CST 2009
  *==============================================
  * @Desc :留言可搜索扩展属性  
  *==============================================
  */
 class Fksearch extends QModels_Ask_Table 
 {
 	private function haskey($key)
 	{
 		$cols=$this->_getCols();
 		if(in_array($key,$cols))
 		{
 			return true;
 		}
 		
 		return false;
 	}
 	
 	public function addcol($key)
 	{
 		if($this->haskey($key)==false)
 		{
	 		$sql="ALTER TABLE `Fksearch` ADD `".$key."` VARCHAR( 50 ) NULL ;";
	 		$sql.="ALTER TABLE `Fksearch` ADD INDEX ( `".$key."` ) ;";
	 		
	 		$db=$this->getAdapter();
	 		return $db->fetchAll($sql);
 		}
 	}
 	
 	public function dropcol($key,&$error)
 	{
 		if($this->hasdata($key))
 		{
 			$error="该属性有相关数据，请先清空数据后再删除";
 			return false;
 		}
 		
 		if($this->haskey($key))
 		{
	 		$sql="ALTER TABLE `Fksearch` DROP `".$key."` ";
	 		$db=$this->getAdapter();
		 	return $db->fetchAll($sql);
 		}
 	}
 	
 	public function hasdata($key)
 	{
 		if($this->haskey($key))
 		{
	 		$sql="select * from Fksearch where `".$key."` is not null";
	 		$db=$this->getAdapter();
		 	$ret=$db->fetchAll($sql);
		 	if(count($ret)>0)
		 	{
	 			return true;
		 	}
 		}
 		 		
 		return false;
 	}
 	
 	public function add($data)
 	{
 		$var=$this->getValidsData($data);
 		return $this->insert($var);
 	}
 	
 	
 	 public function getValidsData($data)
	 {
	 	 	$ret=$this->_getCols();
			$arr=array();
			foreach($ret as $val)
			{
				if($data[$val]<>"")
				{
					$arr[$val]=$data[$val];
				}
			}
	
			return $arr;	
	 }
	 
	 public function getData($idfk)
	 {
	 	 $db=$this->getAdapter();
	 	 $key_arr=array();
	 	 $where[] = $db->quoteInto("idfk = ?", $idfk);
	 	 $ret=$this->fetchRow($where);
	 	 $new_ret=array();
	 	 if(count($ret)>0)
	 	 {
	 	 	Zend_Loader::loadClass("Fkdiymeta",MODELS_PATH);
	 	 	$fkdiymeta=new Fkdiymeta();
	 	 	$list=$fkdiymeta->getAll();
	 	 	if(count($list)>0)
	 	 	{
	 	 		foreach($list as $val)
	 	 		{
	 	 			$key=$val['fkdiymetakey'];
	 	 			if($ret[$key])
	 	 			{
	 	 				$new_ret[$key]['desc']=$val['fkdiymetavalue'];
	 	 				$new_ret[$key]['key']=$ret[$key];
	 	 			}
	 	 		}
	 	 	}
	 	 }
	 	 
	 	 return $new_ret;
	 }
 }
?>