<?php
/**
  *##############################################
  * @FILE_NAME :Cat.php
  *##############################################
  *
  * @author : 李军锋
  * @MailAddr : dreamcastzh@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   问答分类模块
  *==============================================
  */

class Cat extends QModels_Ask_Table
{
	protected $_primary = 'Catid';
	protected $_name="member_attention_Category";
	
	public function get_Cats($id=0)
	{
		//echo "select Catid,Catname from $this->_name where type='$id'";
		$r = $this->_db->fetchAll("select Catid,Catname from `$this->_name` where type='$id'");
		$str = '<option value="">请选择</option>';
		foreach ($r as $k=>$v)
		{
			$str .= "<option value=$v[Catid]>$v[Catname]</option>";
		}
		return $str;
	}
	

}
?>