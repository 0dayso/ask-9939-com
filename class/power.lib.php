<?php
/**
 * power.lib.php 权限类
 * @author xiongzhixin (xzx747@sohu.com) 2008-08-20
 */

class power 
{		
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $iStatus
	 * @return unknown
	 */
	
	function getPowerList($iStatus = NULL) {
		global $db,$tablepre;	
		if(is_null($iStatus))
			$sSQL = "SELECT * FROM `{$tablepre}power` WHERE 1 order by fileNameEn DESC,id desc";
		else 
			$sSQL = "SELECT * FROM `{$tablepre}power` WHERE status='{$iStatus}' ORDER BY fileNameEn DESC,id DESC";
		$aPowerList = $db->getRecordSet($sSQL);
		return $aPowerList;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $iPowerID
	 */
	function getPowerNameByID($iPowerID)
	{
		global $db,$tablepre;	
		$sSQL = "SELECT id,fileNameCn,powerNameCn FROM `{$tablepre}power` WHERE id=".$iPowerID;
		$aInfo = $db->getRecordSet($sSQL, 1);
		return $aInfo;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $iPowerID
	 * @return unknown
	 */
	function getPowerInfoByID($iPowerID)
	{
		global $db,$tablepre;	
		$sSQL = "SELECT * FROM `{$tablepre}power` WHERE id=".$iPowerID;
		$aInfo = $db->getRecordSet($sSQL, 1);
		return $aInfo;
	}
}
?>