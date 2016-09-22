<?php
/**
   *##############################################
   * @FILE_NAME :Notice.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 9939(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Mon Sep 14 10:04:52 CST 2009ead 1.0
   * @DATE : Mon Sep 14 10:04:52 CST 2009
   *
   *==============================================
   * @Desc : 公告数据
   *==============================================
   */

class Notice extends QModels_Ask_Table
{
	protected $_primary = 'nid';
	protected $_name="member_notice";
	/**
	* 添加公告
	*
	* @param 公告信息 array
	* @return 插入ID int
	*/
	public function Add($postarr){
		$insert = $this->insert($postarr);
		if($insert){
			return $insert;
		}
	}

	/**
	* 编辑公告
	*
	* @param 公告ID int
	* @param 公告信息 array
	* @return  int
	*/
	public function Edit($postarr,$nid){
		$db = $this->getAdapter();
		$where  = $db->quoteInto('nid = ?',$nid);
		$update = $this->update($postarr,$where);
		if($update){
			return true;
		}
	}

	/**
	* 查看公告列表 
	* @param 条件
	* @return 公告信息 array
	*/
	public function List_Notice($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		return $result->toArray();
	}

	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetCount($where="1") {
		$where = ($where == "") ? "1" : $where;
//		echo "SELECT count(*) as num FROM `$this->_name` where ".$where;exit;
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `$this->_name` where ".$where);
		return $result[0]['num'];
	}

	/**
	 * 根据某一条件获取一个单一的公告
	 *
	 * @param str $where
	 * @return array
	 */
	public function GetOneyOne($where="1") {
		$where = ($where == "") ? "1" : $where;
		$result = $this->_db->fetchAll("SELECT * FROM `$this->_name` where ".$where);
		return $result[0];
	}

	/**
	 * @desc 删除一个公告
	 *
	 * @param int $nis
	 * @return int 返回行数
	 */
	public function Del($nid)
	{
		if(!$nid) return;
		$db = $this->getAdapter();
		$where  = $db->quoteInto('nid = ?',$nid);
		$del = $this->delete($where);
		if($del){
			return true;
		}
	}

	/**
	 * 返回公告类型
	 *
	 * @return array
	 */
	public function GettypeOp(){
		$tmp_type_arr = array();
		$tmp_type_arr[1] = "家园";
		$tmp_type_arr[2] = "部落";
		$tmp_type_arr[3] = "问答";
		return $tmp_type_arr;
	}
	
	// 返回数组 多条记录 李军锋
	public function GetSomeNotice($where='1',$num='5',$order='dateline desc')
	{
		$result = $this->_db->fetchAll("SELECT * FROM `$this->_name` where ".$where.' order by '.$order.' limit 0,'.$num);
		return $result;
	}
}
?>