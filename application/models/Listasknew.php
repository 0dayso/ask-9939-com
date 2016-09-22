<?php
/**
  *##############################################
  * @FILE_NAME :list.php
  *##############################################
  *
  * @author : hua
  * @MailAddr : dreamcastzh@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   频道模块
  *==============================================
  */

class Listasknew extends QModels_Ask_Table
{
	protected $_name ="wd_ask";
	/**
	* 查看集团
	*
	* @param 条件
	* @return 集团信息 array
	*/
	public function List_Ask($where, $order=null, $count=null, $offset=null) {
		
		/**
		try {
		$result = $this->fetchAll($where, $order, $count, $offset);
		}catch(Exception $e){
			echo $e->getMessage();
		}
		return $result->toArray();
**/

// xzxin 2009-11-28
		$aList = $this->_db->fetchAll("select userid,id,classid,ctime,browsenum,answernum,term,title,status from wd_ask where $where order by  $order limit $offset,$count");
		//$row = $this->_db->fetchAll("SELECT * FROM `list` where $where");
		return $aList;

	}
	
	
	/**
	 * 取详细信息
	 */
	public function GetDetail($sclassid){
		$where = 'id='.$sclassid;
		
		$row = $this->_db->fetchAll("select * from wd_keshi where $where");
		//$row = $this->_db->fetchAll("SELECT * FROM `list` where $where");
		return $row[0];
	}

	
	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetCount($arrchildid,$status) {
		if($arrchildid!=0){
			if($status!=2){
				$result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where classid in($arrchildid) and status=$status and isShow = 1");
			}
			else{
				$result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where classid in($arrchildid) and point!=0 and isShow = 1");
			}
		}else{
			if($status!=3){
			$result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where status=$status and isShow = 1");
			}else{
			$result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask`");
			}
		}
		return $result[0]['count'];
	}
	
	/**
	* 
	* 数组过滤
	*/
	public function aEmpty($array, $space=1) {
		foreach($array as $k => $v) {
			if(is_array($v)) {
				$v = $this->aempty($v, $space);
				if(empty($v)) unset($array["$k"]);
			} else {
				if($space) $v = trim($v);
				if(empty($v)) unset($array["$k"]);
			}
		}
		return $array;
	}
	
	/**
	 * @desc 部落列表
	 * xzx 2009-09-24
	 *
	 * @param unknown_type $where
	 * @param unknown_type $order
	 * @param unknown_type $count
	 * @param unknown_type $offset
	 * @return unknown
	 */
	public function GetList($where='', $order='', $count='', $offset='') {
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		return $result->toArray();
	}	
	
	/**
	 * 返回一个部落信息 author:zzh
	 *
	 * @param int $bid
	 * @return array
	 */
	public function getlistName($bid){
		if($bid){
			$sql = "select * from list where listid=".$bid;
			$result = $this->_db->fetchAll($sql);
			return $result[0];
		}
	}

	public function getvalidity($s=0,$d=0){
		{
			$total = $s + $d*24*60*60;
			$validity = $total - time();
			$day = intval($validity/(3600*24));
			$y = round(($validity%(3600*24))/3600);
			return $day.'天'.$y.'小时';
		}
	}
	
	public function getKeshi($classid){
		$aClasses = $this->_db->fetchAll("select id,name from wd_keshi where pID=$classid");
		//$result[0] = preg_replace("~^0,~is","",$result[0]);

		/*if($result[0]<>''){
			echo $sChildids = implode(",",$result[0]);
			return 1;
			$aClasses = $this->_db->fetchAll("select name,id from wd_keshi where id in($sChildids)");
		}*/
		return $aClasses;
	}
}
?>