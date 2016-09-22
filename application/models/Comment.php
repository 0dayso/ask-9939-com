<?php
/**
   *##############################################
   * @FILE_NAME :Comment.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Tue Sep 15 17:11:54 CST 2009ead 1.0
   * @DATE : Tue Sep 15 17:11:54 CST 2009
   *
   *==============================================
   * @Desc :  评论
   *==============================================
   */

class Comment extends QModels_Ask_Table
{
	protected $_primary = 'cid';
	protected $_name="member_comment";
	/**
	* 添加评论
	*
	* @param 评论信息 array
	* @return 插入ID int
	*/
	public function Add($postarr){
		$insert = $this->insert($postarr);
		if($insert){
			return $insert;
		}
	}

	/**
	* 编辑评论
	*
	* @param 评论ID int
	* @param 评论信息 array
	* @return  int
	*/
	public function Edit($postarr,$rid){
		$db = $this->getAdapter();
		$where  = $db->quoteInto('cid = ?',$rid);
		$update = $this->update($postarr,$where);
		if($update){
			return true;
		}
	}

	/**
	* 查看评论列表 
	* @param 条件
	* @return 评论信息 array
	*/
	public function List_Comment($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result->toArray();
	}

	public function getSomelist($number=10,$where="1"){
		$sql = "SELECT * FROM `$this->_name` WHERE ".$where." ORDER BY dateline DESC LIMIT ".$number;
		$result = $this->_db->fetchAll($sql);
		return $result;
	}

	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetCount($where="1") {
		$where = ($where == "") ? "1" : $where;
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `$this->_name` where ".$where);
		return $result[0]['num'];
	}

	/**
	 * 根据某一条件获取一个单一的评论
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
	 * @desc 删除一个评论
	 *
	 * @param int $nis
	 * @return int 返回行数
	 */
	public function Del($rid)
	{
		if(!$rid) return;
		$db = $this->getAdapter();
		$where  = $db->quoteInto('cid = ?',$rid);
		$del = $this->delete($where);
		if($del){
			return true;
		}
	}

	/**
	 * 批量删除
	 *
	 * @param array $rid_arr
	 */
	public function DelMore($rid_arr){
		foreach ((array) $rid_arr as $v){
			$this->Del($v);
		}
	}
	/**
	 * 返回评论类型
	 *
	 * @return array
	 */
	public function GettypeOp(){
		$tmp_type_arr = array();
		$tmp_type_arr['picid']  = "图片评论";
		$tmp_type_arr['sid']    = "分享评论";
		$tmp_type_arr['blog'] = "日志评论";
		$tmp_type_arr['uid']    = "留言";
		return $tmp_type_arr;
	}
}
?>