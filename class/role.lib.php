<?php
/**
 * power.lib.php 权限类
 * @author xiongzhixin (xzx747@sohu.com) 2008-08-20
 */

class role 
{	
	/**
	 * 得到角色列表
	 *
	 * @param object $db
	 * @param enum $status
	 * @return unknown
	 */
	function getRoleList($status = NULL) {
		global $db,$tablepre;	
		$sTbl = $tablepre."role";
		if(is_null($status))
			$sWhere = " 1";
		else
			$sWhere = " status = '{$status}'";
		$sSQL = "SELECT id,name,powerID FROM `$sTbl` WHERE {$sWhere}";
		$aList = $db->getRecordSet($sSQL);
		return $aList;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $iRoleID
	 */
	function getRoleInfoByID($iRoleID)
	{
		global $db,$tablepre;	
		$sTbl = $tablepre."role";
		$sSQL = "SELECT * FROM `$sTbl` WHERE id=".$iRoleID;
		$aInfo = $db->getRecordSet($sSQL, 1);
		return $aInfo;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $iRoleID
	 * @return unknown
	 */
	function getRoleNameByID($iRoleID)
	{
		global $db,$tablepre;	
		$sTbl = $tablepre."role";
		$sSQL = "SELECT name FROM `$sTbl` WHERE id=".$iRoleID;
		$aInfo = $db->getRecordSet($sSQL, 1);
		return $aInfo["name"];
	}
	
}
?>