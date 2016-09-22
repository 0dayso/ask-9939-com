<?php
  /**
   *##############################################
   * @FILE_NAME :Guanggao.php
   *##############################################
   *
   * @author : 李军锋
   * @MailAddr : 
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Tue July 07 17:04 CST 2009
   * @DATE : Tue July 07 17:04 CST 2009
   *
   *==============================================
   * @Desc :  广告数据操作类
   *==============================================
   */
class Adsplace extends QModels_Ask_Table 
{
	protected $_primary = 'placeid';
	protected $_name="home_adsplace";	
	/**
	 * @Desc :添加一条记录 
	 * @param array $postarr
	 * @return int 最新插入记录的主键id
	 */ 
	public function Add($postarr){ 
		$insert_id = $this->insert($postarr);
		return $insert_id;
	}
	
	public function edit_Adsplace($id, $param) {
		$where = "placeid = $id";
		$this->update($param, $where);
		return true;
	} 
	
	/**
	 * @Desc :广告位列表
	 * @param array $data 
	 * @return array
	 */
	public function List_Adsplace($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		$temp = $result->toArray();
		foreach ($temp as &$val){
			$val['num'] = $this->getAdsNum($val['placeid']);
		}
		return $temp;
	} 
	 
	// 获取记录数
	public function GetCount($where="1") {
		//echo "ss".$where; exit;
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `home_adsplace` where ".$where);
		return $result[0]['num'];
	}
	/**
	 * @Desc :获取某字段的值
	 *
	 * @param int $idgg
	 * 2009-07-24
	 * @return string
	 */	
	public function GetValue($idgg,$fieldname='ggtitle')
	{
		if(!$idgg) return ;
				
		$select_one_sql = "SELECT `$fieldname`
				FROM `Guanggao`
				WHERE idgg=".$idgg; 
		//echo $select_one_sql; exit;
		$re = $this->_db->fetchAll($select_one_sql);
		return $re[0][$fieldname];
	}
	
	public function getAdsNum($placeid){
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `home_ads` where placeid=$placeid");
		return $result[0]['num'];
	}
	
	public function jugeAdsplace($name){
		$result = $this->fetchAll("select placeid from `home_adsplace` where placename='$name'");
		return $result[0]['placeid'];
	}	
	
	//删除广告位
	public function del_Adsplace($id) {
		$r = $this->_db->fetchAll("select adsid from `home_ads` where placeid=$id");
		if($r) return false;
		if(strpos($id,',') === false){
			$this->_db->query("delete from `home_adsplace` where placeid=$id");
			return true;
		}
		else {
			$ids = array_filter(explode(',',$id));
			foreach ($ids as $key){
				$this->_db->query("delete from `home_adsplace` where placeid=$key");
			}
			return true;
		}
	}	
}
?>