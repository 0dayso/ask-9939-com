<?php
/**
  *##############################################
  * @FILE_NAME :WageAnswer.php
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
  * @Desc :   医生回答与投诉记录模块
  *==============================================
  */

class WageAnswer extends QModels_Ask_Table
{
	protected $_name = 'wd_wage_answer';
	
	public function addWageAsk($askid,$uid){
		$param['uid']=$uid;
		$param['askid']=$askid;
		$param['type']="1";
		$param['ctime']=time();
		$this->add($param);
	}
	public function addWageTousu($askid,$uid){
		$param['uid']=$uid;
		$param['askid']=$askid;
		$param['type']="2";
		$param['ctime']=time();
		$this->add($param);
		
	}
	public function add($param) {
		$param = $this->trimCol($param);
		unset($param[$this->primary]);
		return $this->insert($param);
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