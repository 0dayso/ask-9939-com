<?php
/**
  *##############################################
  * @FILE_NAME :Friend.php
  *##############################################
  *
  * @author :   焦雷，李军锋
  * @MailAddr :
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   好友，关注点详细模块
  *==============================================
  */

class Friend extends QModels_Ask_Table
{
	protected $_name = 'member_friend';
	private $primary = 'uid';	
	private $name = 'fusername';
	private $fuid = 'fuid';
	private $time = 'dateline';
	private $status = 'status';
	
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
	* 查看图片
	*
	* @param 条件
	* @return 图片信息 array
	*/
	public function getList($where='', $order='', $count='', $offset='') {
		#echo $where, $order, $count, $offset;
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result->toArray();
	}
	
	// get friend 
	public function get_friend($t,$uid){
		$r = $this->_db->fetchAll("select fuid,fusername from `member_friend` where status=1 and uid='$uid'");
		$temp = array();
		foreach ($r as $k=>$v)
		{
			$rm = $this->_db->fetchAll("select uid,nickname from `member` where uid='$v[fuid]' and uType='$t'");
			if($rm[0]['nickname']) $v['fusername'] = $rm[0]['nickname'];
			if($rm) $temp[] = $v;
		}
		//print_r($temp);exit;
		return $temp;
	}
	
	// delete friend
	public function del_friend($uid,$fuid){
		$this->db->query("delete from `member_friend` where uid='$uid' and fuid='$fuid'");
		return true;
	}
	
	public function get_friend_name($uid){
		$r = $this->_db->fetchAll("select nickname,username from `member` where uid='$uid'");
		return $r[0]['nickname'] ? $r[0]['nickname'] : $r[0]['username'];		
	}

	public function add($param){
		return $this->insert($param);
	}
	
	public function juge_friend($uid,$fuid){
		$r = $this->_db->fetchAll("select * from `member_friend` where uid='$uid' and fuid='$fuid'");
		return $r[0]['uid'];
	}

	
	
	//获取数据总和
	public function numRows($where=1) {
		$sql = 'SELECT count('. $this->primary .') as count FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchRow($sql); 
		return $result['count'];
	}
	
	//好友请求
	public function friendask($fuid=0){
		if($fuid){
			$r = $this->_db->fetchRow("select count(*) as num from member_friend where fuid=$fuid and status=0");
			return $r['num'];
		}
	}
	
	//问答回复
	public function askreplay2($uid=0){
		if($uid){
			$r = $this->_db->fetchRow("select count(1) as num from wd_ask where userid=$uid AND answernewnum>0");
			return $r['num'];
		}
	}
      //问答回复
	public function askreplay($uid=0){
		if($uid){
			$r = $this->_db->fetchRow("select count(1) as num from wd_answer where userid=$uid");
			return $r['num'];
        }
	}
	
	//好友消息
	public function friendnews($uid=0){
		if($uid){
			$r = $this->_db->fetchRow("select count(1) as num from member_message where touid=$uid");
			return $r['num'];
		}
	}

	
}
?>