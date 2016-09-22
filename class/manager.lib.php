<?php
/**
 * manager.lib.php 权限类
 * @author xiongzhixin (xzx747@sohu.com) 2008-08-20
 */

class manager 
{	
	/**
	 * 取得管理员的全部信息
	 *
	 * @param unknown_type $iID
	 * @return unknown
	 */
	function getManagerInfo($iID) {
		global $db,$tablepre;
		$sTbl = $tablepre."manager";
		$sSQL = "SELECT * FROM $sTbl WHERE id=".$iID;
		$aInfo = $db->getRecordSet($sSQL, 1);
		return $aInfo;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $db
	 */
	function listAllManager($sWhere) {
		global $db,$tablepre;
		$sTbl = $tablepre."manager";
		$sSQL = "SELECT * FROM $sTbl WHERE $sWhere";
		$aList = $db->getRecordSet($sSQL, 15);
		return $aList;
	}
	
	/**
	 * 用户名是否已经存在
	 *
	 * @param string $sUserName
	 */
	function isUserExists($sUserName)
	{
		global $db,$tablepre;
		$sTbl = $tablepre."manager";
		
		$sSQL = "SELECT id FROM `$sTbl` WHERE userName='$sUserName'";
		if(count($db->getRecordSet($sSQL, 1)) > 0)
			return true;
		else
			return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $iUID
	 * @param unknown_type $sFileName
	 * @param unknown_type $sAction
	 */
	
	function checkPower($iUID, $sFileName, $sAction) {
		global $db,$tablepre;
		$sTbl = $tablepre."power";
		
		if($iUID == 1 || $iUID == 2)
			return true;
		//这里对save进行筛选去掉 
		if(preg_match("/save/i", $sAction))
			return true;
		$sSQL = "SELECT id FROM `$sTbl` WHERE fileNameEn='$sFileName' AND powerNameEn='$sAction'";
		$aInfo = $db->getRecordSet($sSQL, 1);
		$iNowPowerID = $aInfo['id'];
		if(@in_array($iNowPowerID, $_SESSION['xzx_powerID']))
			return true;
		else 
			return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $sType
	 */
	function getManagerListByType($sType) {
		$sLowerStr = strtolower($sType);		//小写
		$sUpperStr = strtoupper($sType);		//大写
		if(file_exists("../".CACHE_FILE."/_cache_manager_".$sLowerStr.".php"))
		{
			require_once("../".CACHE_FILE."/_cache_manager_".$sLowerStr.".php");
			$sVarPrefix = "_CACHE_MANAGER_";
			$aReturnArray = ${$sVarPrefix.$sUpperStr};
		} else {
			//不存在缓存文件，开始查询数据库
			$aReturnArray = $this->_getManagerIDAndNameByType($sLowerStr);
		}
		return $aReturnArray;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $sType
	 */
	function _getManagerIDAndNameByType($sType) 
	{
		global $db,$tablepre;
		$sTbl = $tablepre."manager";
		
		switch ($sType)
		{
			case "manager":
				$iRoleID = "(1)";
				break;
			case "yewu":
				$iRoleID = "(6,7)";
				break;
			case "kefu":
				$iRoleID = "(3,4)";
				break;
			default:
				$iRoleID = "()";
				break;
		}
		
		$sSQL = "SELECT id,trueName FROM `$sTbl` WHERE roleID IN {$iRoleID} AND status <> '0'";
		$aList = $db->getRecordSet($sSQL);
		$aList_ = array2Array($aList, "id", "trueName");
		return $aList_;
	}
}
?>