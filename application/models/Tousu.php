<?php
/**
 * 问答投诉
 * @author 林原
 * 
 */

class Tousu extends QModels_Ask_Table
{
	protected $_name ="wd_tousu";
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
	* 添加投诉
	*
	* @param 投诉信息 array
	* @return 插入ID int
	*/
	public function add($param) {
		//去除主键
		unset($param[$this->primary]);
		
		return $this->insert($param);
	}
	
	
}



?>