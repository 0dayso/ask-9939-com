<?php
//登录日志：0为用户名不存在，1为登录正确，2为密码错误
class log {			
	/**
	 * 记录登录日志
	 *
	 * @param unknown_type $sName
	 * @param unknown_type $sPwd
	 * @param unknown_type $ip
	 * @param int $bFlag (1 表示登录成功, 2 表示密码错误 3 表示帐户不存在
	 * @param int $iAdminType (0 表示总后台，1 表示分站后台，2 表示个人会员，3 表示企业会员，4 表示专家)
	 */
	function insertLoginLog($sName, $sPwd, $ip, $bFlag,$iAdminType=0)
	{
		global $db, $tablepre;
		
		$sTbl = $tablepre."log_login";
		//echo "<br>类中的前缀".$tablepre."<br>".$sTbl;				
		$aField					= array();
		$aField["userName"]		= $sName;
		$aField["pwd"] 			= $sPwd;
		$aField["ip"]			= $ip;
		$aField["status"]		= $bFlag;
		$aField["loginTime"]	= date("Y-m-d H:i:s");		
		$aField['adminType']    = $iAdminType;
		$db->insert($sTbl,$aField);
		return true;
	}
	
	/**
	 * 所有登录日志
	 *
	 * @return array $aLoginAll		- 所有登录日志信息
	 */
	function loginAll($iStartNo,$iPerpage,$sWhere="1")
	{
		global $db,$tablepre,$aAdminType;
		$sTbl = $tablepre."log_login";
				
		$aLoginAll = array();
		$sSQL = "SELECT * FROM $sTbl where $sWhere ORDER BY id DESC LIMIT $iStartNo,$iPerpage";
		$aLoginAll = $db->getRecordSet($sSQL);
		for($i=0; $i<count($aLoginAll); $i++)
		{
			$iType = $aLoginAll[$i]['adminType'];
			$aLoginAll[$i]['adminTypeName'] = $aAdminType[$iType];
		}
		return $aLoginAll;
	}
}
?>