<?php
/**
  *##############################################
  * @FILE_NAME :Article.php
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
  * @Desc :   文章模块
  *==============================================
  */
class Content extends QModels_Ask_Table
{
	protected $_name ="home_position_content";
	protected $primary = 'id';

	/**
	* 查看文章
	*
	* @param 条件
	* @return 文章信息 array
	*/
	public function List_Article($where='', $order='', $count='', $offset='') {
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		return $result->toArray();
	}		
	
	//统计记录数
	public function GetCount($where="1") {
		//echo "ss".$where; exit;
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `home_position_content` where ".$where);
		return $result[0]['num'];
	}
}



?>