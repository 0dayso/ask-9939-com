<?php
/**
  *##############################################
  * @FILE_NAME :SurveyItem.php
  *##############################################
  *
  * @author : 魏鹏
   * @MailAddr : 123109769@qq.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   提问调查
  *==============================================
  */

class SurveyItem extends QModels_Ask_Table
{
	protected $_name = 'wd_survey_item';

    
    //查询调查项目列表
    public function List_Item() {
		$sSQL = "select id,content,openask from `$this->_name` ";
        $sSQL.=" where type=1 ";
        $sSQL.=" order by listorder asc ";  
        $result = $this->_db->fetchAll($sSQL);
		return $result;	
	}
    public function updatenum($id){
        $sql="update `$this->_name` set `num`=num+1 where id={$id}";
		if($this->_db->query($sql)){
			return true;
		}else{
			return false;
		}
    }
}
?>