<?php
/**
 * 问题补充
 * @author 林原
 * 
 */

class Askadd extends QModels_Ask_Table
{
	protected $_name ="wd_ask_add";
	private $primary = 'id';
	
	public function getList($where=null, $order=null, $count=null, $offset=null) {
		if($where) $where = " where $where";
		if($order) $order  = " order by $order";
		$sql = "select * from ".$this->_name.$where.$order;
		if($count&&$count>0) $sql .= " limit $offset,$count";
		
		$result = $this->_db->fetchAll($sql);
		return $result;
	}
	
	
	/**
	* 添加问题补充
	*
	* @param 问题补充信息 array
	* @return 插入ID int
	*/
	public function add($param) {
		//去除主键
		unset($param[$this->primary]);
		
		return $this->insert($param);
	}
	
	
}



?>