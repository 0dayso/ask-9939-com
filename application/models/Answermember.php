<?php

class Answermember extends QModels_Ask_Table
{
	public $_name ="wd_answer_member";
	private $primary = 'id';
	public function tablename($name){
		if ($name){
			$this->_name = $name;
		}else {
			$this->_name = 'wd_answer_member';
		}
		var_dump($this->_name);
	}
	
	public function getList($where='1', $order='', $count='', $offset='') {
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result->toArray();
	}
	
	
		/**
	* 添加文章
	*
	* @param 文章信息 array
	* @return 插入ID int
	*/
	public function add($param) {
		
		$param['addtime'] or ( $param['addtime']=time());
		
		//去除主键
		unset($param[$this->primary]);
		//去除param数组中键值为非列的值
		$param = $this->trimCol($param);
		//var_dump($param);
		return $this->insert($param);
	}
	
	
	
	public function edit($param=array()) {
		$tmp_id = intval($param[$this->primary]); 
		$where = $this->primary .'=\''. $tmp_id .'\'';
		
		//去除主键
		unset($param[$this->primary]);
		//print_r($param); exit;
		//去除param数组中键值为非列的值
		$param = $this->trimCol($param);
		$param = $this->trimValueIsNull($param);
		return $this->update($param, $where);
	}
	
	
	public function get_one($id='') {
		if(!$id) return ;
		$where = $this->primary .'='. intval($id);
		$sql = 'SELECT `'. implode('`,`', $this->_getCols()) .'` FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchRow($sql); //获取一行
		return $result;
	}
	
	
	
	
	public function numRows($where=1) {
		return ;
	}
	
	
	/**
	* 去除param数组中键值为非列名单元
	*/
	private function trimCol($param) {
		foreach ($param as $k => &$v) {
			if(!in_array($k, $this->_getCols())){
				unset($param[$k]);
			}
		}
		return $param;
	}
	
	
	private function trimValueIsNull($param=array()) {
		if(!$param)return '';
		foreach ($param as $k => $v) {
			if(!$v) {
				unset($param[$k]);
			}
		}
		return $param;
	}
	
	
}



?>