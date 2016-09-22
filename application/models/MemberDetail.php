<?php
/**
  *##############################################
  * @FILE_NAME :MemberDetail.php
  *##############################################
  *
  * @author :   矫雷
  * @MailAddr : kxgsy163@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   文章详细模块
  *==============================================
  */

class MemberDetail extends QModels_Ask_Table
{
	protected $_name ="member_detail_1";
	private $primary = 'uid';	
	private $birthday = 'birthday';
	private $telephone = 'telephone';
	private $address = 'address';
	private $default_dis = 'gz_1';
	private $health_old = 'healthold';
	private $dis_history_mom = 'mother';
	private $dis_history_dad = 'dad';
	private $dis = 'dis';
	
	
	public function getList($where='', $order='', $count='', $offset='') {
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result->toArray();
	}
	
	
	
	
	/**
	* 
	* 返回成员变量值
	*/
	public function getValue($var='') {
		if(!$var) return '';
		if(!in_array($var, array_keys(get_object_vars($this)))) return '';
		return $this->$var;
	}
	
	
	
	
	/**
	* 添加文章详细
	*
	* @param 文章详细信息 array
	* @return 插入ID int
	*/
	public function add($param) {
		$param = $this->trimCol($param);
		#print_r($param); exit;
		return $this->insert($param);
	}

	/**
	* 编辑文章
	*
	* @param 文章详细ID int
	* @param 文章详细信息 array
	* @return  int
	*/
	public function edit ($param) {
		
		$tmp_id = intval($param[$this->primary]); //文章ID
		if(!$this->get_one($tmp_id)) { //区分详细信息表是否存在该用户资料
			return $this->add($param);
			exit;
		}
		
		$where = $this->primary .'=\''. $tmp_id .'\'';
		$param = $this->trimCol($param);
		$param = $this->trimValueIsNull($param);
		unset($param[$this->primary]);
		#print_r($param); exit;
		return $this->update($param, $where);
	}
	
	/**
	* 删除文章
	*
	* @param ID
	* @return bool
	*/
	public function del($id) {
		$where = $this->primary .'='. intval($id);
		$result = $this->_db->delete($this->_name, $where); 
		return $result;
	}
	
	
	/**
	* 查看文章
	*
	* @param 条件
	* @return 文章信息 array
	*/
	public function get_one($id) {
        $memberDetailCache = 'detail_'.$id;
        $data = QLib_Cache_Client::getUserCache('memberDetail', $memberDetailCache);
        if($data){
            return $data;
        }else{
            $where = $this->primary .'='. intval($id);
            $sql = 'SELECT `'. implode('`,`', $this->_getCols()) .'` FROM `'. $this->_name .'` WHERE '. $where;
            $result = $this->_db->fetchRow($sql); //获取一行
            QLib_Cache_Client::setUserCache('memberDetail', $memberDetailCache, $result, 24);
            return $result;
        }
		
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