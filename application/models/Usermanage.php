<?php
/**
  *##############################################
  * @FILE_NAME :Usermanage.php
  *##############################################
  *
  * @author : ljf
  * @MailAddr : licaption@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   用户管理模块
  *==============================================
  */

class Usermanage extends QModels_Ask_Table
{
	protected $_primary = 'uid';
	protected $_name="member";
	public  $primary = 'uid';
	public  $username = 'nickname';
	public  $status = 'status';
	
	
	/**
	* 查看用户管理
	*
	* @param 条件
	* @return 用户管理信息 array
	*/
	public function List_Member($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		$temp = $result->toArray();
		foreach ($temp as $k=>&$v)
		{
		}
		return $temp;
	}
	
	/**
	* 查看文章 李军锋
	*
	* @param 条件
	* @return 文章信息 array
	*/
	public function GetList($where='', $order='', $count='', $offset='') {
		//echo "ss"; exit;
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result); exit;
		return $result->toArray();
	}
	
	
	//统计记录数
	public function GetCount($where="1") {	
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `member` where ".$where);
		return $result[0]['num'];
	}	
	
	/**
	* 查看会员信息
	*
	* @param 条件
	* @return 会员信息 array
	*/
	public function get_one_by_name($name) {
		$where = $this->username .' LIKE \'%'. $name .'%\'';
		$sql = 'SELECT `'. implode('`,`', $this->_getCols()) .'` FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchAll($sql); //获取一行
		return $result;
	}
	
	/**
	* 查看会员信息
	*
	* @param 条件
	* @return 会员信息 array
	*/
	public function get_one_by_id($id) {
		$where = $this->primary .'=\''. $id .'\'';
		$sql = 'SELECT `'. implode('`,`', $this->_getCols()) .'` FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchRow($sql); //获取一行
		return $result;
	}
	
	
	/**
	* 编辑会员信息
	*
	* @param 会员信息 array
	* @return  int
	*/
	public function edit ($param) {
		
		$tmp_id = intval($param[$this->primary]); //文章ID
		
		$where = $this->primary .'=\''. $tmp_id .'\'';
		$param = $this->trimCol($param);
		unset($param[$this->primary]);
		#return $this->update($param, $where);
		$this->update($param, $where);
		return 1;
	}
	
	
	
	/**
	* 删除会员
	*
	* @param ID
	* @return bool
	*/
	public function del($id) {
		if(!$id) return '';
		if(is_array($id)) {
			$where = $this->primary .' IN ('. implode(',', $id) .') ';
		} else {
			$where = $this->primary .'='. intval($id);
		}
		$param = array($this->status=>'0');
		$result = $this->update($param, $where); 
		return $result;
	}
	
	
	
	
	/**
	* 
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
	
	
}
?>