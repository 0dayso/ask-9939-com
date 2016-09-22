<?php
  /**
   *##############################################
   * @FILE_NAME :Cs.php
   *##############################################
   *
   * @author : panhongjing
   * @MailAddr : 
   * @copyright : Copyright (c) 2012 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Tue July 07 17:04 CST 2009
   * @DATE : Tue July 07 17:04 CST 2009
   *
   *==============================================
   * @Desc :  手机接口操作类
   *==============================================
   */
class Cs extends QModels_Ask_Table 
{
	protected $_primary = 'id';
	protected $_name="member";
	

	public function GetCount($uid,$mobile) {	
	    $sql = "SELECT uid,update_mobile,mobile,checkmobile,credit FROM ".$this->_name." WHERE uid='$uid' and update_mobile='$mobile'";
        $res = $this->_db->fetchAll($sql);
        return $res;
		//$result = $this->_db->fetchAll("SELECT count(*) as num FROM ".$this->_name." limit 1");
		//return $result[0]['num'];
	}
    public function updateMobile($data){
        $where = $this->_db->quoteInto('uid = ?',$data['uid']);
        unset($data['uid']);

        $res = $this->_db->update($this->_name, $data, $where);
        return $res;
        
    }

	
}
?>