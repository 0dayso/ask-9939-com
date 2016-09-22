<?php
/**
  *##############################################
  * @FILE_NAME :SurveyText.php
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
  * @Desc :   提问调查用户填写表
  *==============================================
  */

class SurveyText extends QModels_Ask_Table
{
	protected $_name = 'wd_survey_text';

    /**
	* 添加调查项目
	*
	* @param 调查项目内容 array
	* @return 插入ID int
	*/
	public function add($param) {
		$param = $this->trimCol($param);
		unset($param[$this->primary]);
		return $this->insert($param);
	}
    //查询调查项目列表
    public function List_All($where='1', $order='', $count=-1, $offset=-1) {
		$sSQL = "select id,type,content,ctime from `$this->_name` ";
        if($where!="")
            $sSQL.=" where ".$where." ";
        if($order!="")
            $sSQL.=" order by ".$order." ";  
        if($count!=-1&&$offset!=-1){
            $sSQL.=" limit $offset,$count";
        }
        $result = $this->_db->fetchAll($sSQL);
		return $result;	
	}
    public function update($data,$where){
        return $this->_db->update($this->_name, $data,$where);
    }
    public function delete($id){
        return $this->_db->delete($this->_name, 'id = '.$id);
    }
    //删除全部已读信息
    public function deleteYiDu(){
        return $this->_db->delete($this->_name, 'type=2');
    }
    /**
	* 查询单个项目
	*
	* @param 条件
	* @return 关注点栏目信息 array
	*/
	public function getById($id) {
		//获得月初时间戳
		$result=$this->_db->fetchRow("select * from `$this->_name` where id=$id");
		return $result;
	}
    //查询数量
    public function GetCount($where="1") {
		$where = !$where ? "1" : $where;
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `$this->_name` where ".$where);
		return $result[0]['num'];
	}
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