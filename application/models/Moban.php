<?php
 /**
  *##############################################
  * @FILE_NAME :moban.php
  *##############################################
  *
  * @author : 谭剑
  * @MailAddr : tanjian56@sina.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:52 CST 2009
  * @DATE : Thu Jun 18 18:00:52 CST 2009
  *
  *==============================================
  * @Desc :   模板模块
  *==============================================
  */

class Moban extends QModels_Ask_Table 
{
	protected $_primary = 'idmb';

	/**
	* 模板
	*
	* @param 模板信息 array
	* @return 插入ID int
	*/
	public function Add_Moban($param) {
		return $this->insert($param);
	}

	/**
	* 编辑
	*
	* @param 模板ID int
	* @param 模板信息 array
	* @return  int
	*/
	public function edit_Moban($id, $param) {
		$where = "idmb = $id";
		return $this->update($param, $where);
	}
	
	/**
	* 模板信息
	*
	* @param 模板ID int
	* @return  模板信息 array
	*/
	public function get_Moban($id) {
		if (!$id = (int)$id) return null;
		$row = $this->fetchRow('idmb='.$id);
		return $row->toArray();
	}
	
	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function List_Moban_count($where) {
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM Moban where ".$where);
		return $result[0]['num'];
	}
	
	/**
	* 查看模板
	*
	* @param 条件
	* @return 模板信息 array
	*/
	public function List_Moban($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result;
	}

	public function GetData($sql) {
		$result = $this->_db->fetchAll($sql);
		return $result;
	}
	
	/**
	* 模板信息
	* 
	* @author xzx 2009-07-10
	* @return  模板信息 array
	*/
	public function GetOne($key,$value)
	{
		/**
		 * idmb 模板id  
		 * idxm 项目id 
		 * mbname 模板名称 
		 * mbpath 模板文件路径 
		 * mbctime 创建时间 
		 * mbdesc 模板备注 
		 * mbkuandu  
		 */
		if(!$key) return ;		
		$select_one_sql = "SELECT `idmb`,`idxm`,`mbname`,`mbpath`,`mbctime`,`mbdesc`,`mbkuandu`,`mbfile`,`idbm`
				FROM `Moban`
				WHERE 1 
				AND `".$key."`='".$value."'"; 
		$re = $this->_db->fetchAll($select_one_sql);
		return $re;
	}

	/*	public function List_Moban_count($where) {
		$slt = $this->select();
		$slt->from($this->info('name'), array('count(*) as num'));
		$result = $this->fetchAll($slt, $where);
		//print_r($result);
		return (int)$result[0]['num'];
	}*/

/*	public function Set_Cols($cols) {
		$this->_cols = $cols;
	}*/

	/**
	 * 获取模板列表（select List Option）
	 * @author xzx
	 * 2009-07-09
	 * @return array $TempOptionsArr
	 */
	public  function GetOptionsArr($where="1"){
		//echo $where; exit;
		$TempResultObjArr=$this->fetchAll($where);
		$TempResultArr=$TempResultObjArr->toArray();
		$TempOptionsArr=array();
		foreach ((array) $TempResultArr AS $val ){
			$TempOptionsArr[$val['idmb']]=$val['mbname'];
		}
		return $TempOptionsArr;
	}
	
	/**
	* 获取模板名称
	*
	* @param 模板id int
	* @author xzx 2009-07-21
	* @return 模板名称 str
	*/
	public function get_MobanName($id,$fieldname='mbname') {
		if (!$id = (int)$id) return null;
		$row = $this->fetchRow('idmb='.$id);
		//print_r($row);
		return $row->$fieldname;
	}

}
?>