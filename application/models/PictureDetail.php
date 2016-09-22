<?php
/**
  *##############################################
  * @FILE_NAME :PictureDetail.php
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
  * @Desc :   图片模型详细模块
  *==============================================
  */



class PictureDetail extends QModels_Article_Table
{
	protected $_name ="picture_detail";
	protected $primary = 'articleid';
	
	
	
	
	
	
	
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
	* 添加图片模型详细
	*
	* @param 图片模型详细信息 array
	* @return 插入ID int
	*/
	public function add($param, $_FILES=array()) {
		$param = $this->trimCol($param);
		$this->insert($param);
	}

	/**
	* 编辑文章
	*
	* @param 图片模型详细ID int
	* @param 图片模型详细信息 array
	* @return  int
	*/
	public function edit ($param) {
		$tmp_articleid = intval($param['articleid']); //文章ID
		$where = "articleid = $tmp_articleid";
		$param = $this->trimCol($param);
		unset($param['articleid']);
		$this->update($param, $where);
	}
	
	
	/**
	* 删除文章
	*
	* @param ID
	* @return bool
	*/
	public function del($articleid) {
		$where = $this->primary .'='. intval($articleid);
		$result = $this->_db->delete($this->_name, $where); 
		return $result;
	}
	
	
	/**
	* 查看文章
	*
	* @param 条件
	* @return 文章信息 array
	*/
	public function get_one($articleid) {
		$where = $this->primary .'='. intval($articleid);
		$sql = 'SELECT `'. implode('`,`', $this->_getCols()) .'` FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchRow($sql); //获取一行
		return $result;
	}
	
	
	/**
	 × 加载默认类对象成员
	 */
	private function newObj() {
		$this->AttachmentObj = new Attachment();
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
	
}
?>