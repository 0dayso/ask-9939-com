<?php
/**
  *##############################################
  * @FILE_NAME :Credit.php
  *############################################## 
	*
	* @author : xzx
	* @MailAddr : xzx747@126.com
	* @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
	* @PHP Version :  Ver 5.21
	* @Apache  Version : Ver 2.20
	* @MYSQL Version : Ver 5.0
	* @Version : Ver Wen Sep 16 10:00 CST 2009
	* @DATE : Wen Sep 16 10:00 CST 2009
  *
  *==============================================
  * @Desc :  积分模块
  *==============================================
  */

class Credit extends QModels_Ask_Table
{	
	protected $_primary = 'hid';
	protected $_name="member_credit_history";	
	
	/**
	* @Desc 更新积分
	* @param $creditmode pay or get（扣除积分或增加积分）
	* @param $optype 积分操作符 如：ask_pub 健康问答_提问
	* @return bool
	*/
	public function updatespacestatus($creditmode, $optype)
	{	
		global $_SGLOBAL;		
		$tmp_cookie_array = $_SGLOBAL['cookie'];
		if(!$tmp_cookie_array['uid']){
			return false;			
		}
		//加载会员类
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();		
		
		$tmp_cookie_array = $_SGLOBAL['cookie'];
		$tmp_creditrule_array = $_SGLOBAL['creditrule'];
		
		
		/** 输出增加积分调用
		foreach ($tmp_creditrule_array['get'] as $k=>$v){
			echo "\$str = \$this->Credit_obj->updatespacestatus(\"get\",\"".$k."\"); //".$tmp_creditrule_array['getName'][$k]."<br>";
		}
		**/
		
		/** 输出扣除积分调用
		foreach ($tmp_creditrule_array['pay'] as $k=>$v){
			echo "\$str = \$this->Credit_obj->updatespacestatus(\"pay\",\"".$k."\"); //".$tmp_creditrule_array['payName'][$k]."<br>";
		}
		**/
		$iUid = $tmp_cookie_array['uid'];
		$iCredit = $tmp_creditrule_array[$creditmode][$optype];
		
		// 获取会员当前的积分值和经验值
		$iCredit_now = $this->Member_obj->GetValue($iUid,"credit");
		$iExperience_now = $this->Member_obj->GetValue($iUid,"experience");
		
		if($creditmode == 'pay' && $iCredit_now < $iCredit){ // 判断积分是否够用
			Zend_Adver_Js::helpJsRedirect("/user",0,"抱歉，积分不够！");
			return false;
		}
		
		// 更新会员的积分
		$arr =  array();
		
		//echo $creditmode;
		
		//$arr['credit'] = $iCredit_now.(($creditmode == 'get')?"+$iCredit":"-$iCredit");		
		if($creditmode == 'get'){
			$arr['credit'] = $iCredit_now + $iCredit;	 // 增加积分		
			$arr['experience'] = $iExperience_now + $iCredit;	 // 增加经验值
		}
		else if($creditmode == 'pay'){	
			$arr['credit'] = $iCredit_now - $iCredit;	 // 扣除积分			
		}	
		//print_r($arr); //exit;
		try{
			if($this->Member_obj->Edit($arr,$iUid)){
				//更新积分经验值的cookie值
				$this->Member_obj->ssetcookie('member_credit',$arr['credit']);
				$this->Member_obj->ssetcookie('member_experience',$arr['experience']);
				$this->Member_obj->ssetcookie('member_groupinfo',"",$tmp_cookie_array['uType'],$arr['credit']);
			}
		}
		catch (Exception $e){
			echo $e->getMessage();
		}
		
		$arr = array();
		//uid 用户ID nickname 昵称 credit 积分 creditmode get：增加；pay：减少 optype 操作类型：如blog：日志 name 积分操作名称 dateline 历史记录产生的时间戳
		$arr['uid'] = $iUid;
		$arr['nickname'] = $tmp_cookie_array['nickname'];		
		$arr['creditmode'] = $creditmode;
		$arr['optype'] = $optype;
		$arr['credit'] = $iCredit;
		$arr['name'] = $tmp_creditrule_array[$creditmode."Name"][$optype];
		$arr['dateline'] = time();		
		$this->Add($arr); // 插入积分历史记录	
		$msg = $arr['name'].",操作成功！<br><a href='/user'>进入空间</a>";
		return $msg;
		
		/**
		$lastname = $optype=='search'?'lastsearch':'lastpost';
		$credit = $tmp_creditrule_array[$creditmode][$optype];
		$name = $tmp_creditrule_array[$creditmode."Name"][$optype];  // 操作名称
		
		if($credit) {
			$creditsql = ($creditmode == 'get')?"+$credit":"-$credit";
		} else {
			$creditsql = '';
		}
		$creditsql = $creditsql?",credit=credit{$creditsql}":'';
		$updatetimesql = $optype=='search'?'':",updatetime='$_SGLOBAL[timestamp]'";//搜索不更新
		
		
		$_SGLOBAL['db']->query("insert into ".tname('usercredit')." SET addtime='$_SGLOBAL[timestamp]' $creditsql,uid='$_SGLOBAL[supe_uid]',username='$_SGLOBAL[supe_username]',optype='$optype',creditmode='$creditmode',name='$name'");
	
		//更新状态
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET $lastname='$_SGLOBAL[timestamp]' $updatetimesql $creditsql WHERE uid='$_SGLOBAL[supe_uid]'");	
		**/
		/** xzx 2009-5-21 积分明细 **/
	}
	
	/**
	 * @Desc :添加一条记录 
	 * @param array $postarr
	 * @return int 最新插入记录的主键id
	 */ 
	public function Add($postarr){ 		 
		$insert = $this->insert($postarr);
		if($insert){
			return $insert;
		}
	} 
}
?>