<?php
/**
  * 活动产品
  *　@author 林原 2010-09-09
  * 
  */
class Product extends QModels_Ask_Table {
	protected $_primary = 'proId';
	protected $_name="wd_ask_product";	
	
	/**
	 * 获取数量
	 *
	 */
	public function getCount($where) {
		if(!$where) $where = '1';
		$sql = "SELECT COUNT(*) FROM `wd_ask_product` WHERE $where";
		return $this->_db->fetchOne($sql);
	}
}
?>