<?php
/**
  *##############################################
  * @FILE_NAME :Usergroup.php
  *############################################## 
	*
	* @author : xzx
	* @MailAddr : xzx747@126.com
	* @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
	* @PHP Version :  Ver 5.21
	* @Apache  Version : Ver 2.20
	* @MYSQL Version : Ver 5.0
	* @Version : Ver Mon Sep 14 09:34 CST 2009
	* @DATE : Mon Sep 14 09:34 CST 2009
  *
  *==============================================
  * @Desc :   用户组管理模块
  *==============================================
  */

class Usergroup extends QModels_Ask_Table
{
	protected $_primary = 'gid';
	protected $_name="member_group";

	/**
	 * @Desc :获取列表
	 * @param array $data 
	 * @return array
	 */
	public function GetList($where="1 ")
	{ 		
		$order = " ORDER BY gid asc";	
		/**
		 * idggz  
		 * ggzname 广告组名称 
		 * idxm 项目id  		 
		 */ 	
		$select_sql = "SELECT *
			FROM `member_group`
			WHERE  ".$where.$order;	
		//echo $select_sql; exit;
				
		$re = $this->_db->fetchAll($select_sql); 		
		return $re;
	} 
	
	/**
	 * @DESC:更改一条记录
	 *
	 * @param array $postarr
	 * @param int $idggz
	 * @return bool
	 */
	public function Edit($postarr,$gid){    
		$db = $this->getAdapter();
		$where  = $db->quoteInto('gid = ?',$gid);
		$update = $this->update($postarr,$where);
		if($update){
			return true;
		} 
	} 
	
	//统计记录数
	public function GetCount($where="1") {
		//echo "ss".$where; exit;
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `member_blog` where ".$where);
		return $result[0]['num'];
	}	
	
}
?>