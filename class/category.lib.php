<?php
if(!defined("IN_WEB")) echo "<script>location.href='/';</script>";
/**
 * 类别操作类
 * @author xiongzhixin (xzx747@sohu.com) 2008-08-15
 */
class category
{	
	/**
	 * 按父类id提取其子类列表
	 *
	 * @param string $sTbl
	 * @param $pID （父类id）
	 * @param $isDisplaySelf （是否显示本身）	
	 * @return array
	 */
	
	function getSubList($sTbl,$pID=0,$iStart=0,$iNum=0,$bDisplaySelf=false)
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
		$sWhere = $bDisplaySelf ? "(pID=$pID OR id=$pID)" : "pID=$pID";	
		$sLimit = ($iNum > 0) ? " limit $iStart,$iNum" : "";
		$sSQL = "SELECT * FROM $sTbl WHERE $sWhere order by orderID asc $sLimit";		
		$aList = $db->getRecordSet($sSQL);
		return $aList;
	}
	
	/**
	 * 按父类id提取其子类列表
	 *
	 * @param string $sTbl
	 * @param $pID （父类id）
	 * @param $isDisplaySelf （是否显示本身）	
	 * @return array
	 */
	
	function getList($iStart=0,$iNum=0,$sTbl,$sWhere)
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
		$sWhere = ($sWhere) ? $sWhere : "1";
		if($sTbl == "9939_buwei_category")	$sWhere = "id<>1656";
								
		$sLimit = ($iNum > 0) ? " limit $iStart,$iNum" : "";
		$sSQL = "SELECT * FROM $sTbl WHERE $sWhere order by orderID asc $sLimit";		
		$aList = $db->getRecordSet($sSQL);
		return $aList;
	}
	
	/**
	 * 根据类别id取得类别名称，如果是子类，则同时获取父类的名称
	 *
	 * @param string $sTbl
	 * @param int $id
	 * @param int $iSort  0表示横排－， 1表示横排→， 2表示竖排
	 * @return string
	 */
	
	function getName($sTbl,$id,$sFlag="-")
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
		
		$aID = split(",",$id);
		//print_r($aID); //exit;
		$sCatName = "";
		
		for($i=count($aID)-1; $i>=0; $i--)
		{
			if($aID[$i])
			{
				$aField = category::getInfo($sTbl_1,$aID[$i]); //
				$sCatName .=  $aField['name'].$sFlag;
			}
		}
		
		/**
		foreach ($aID as $iKey=>$sValue) 
		{
			if($sValue) 
			{
				$aField = category::getInfo($sTbl_1,$sValue); //
				$sCatName .=  $aField['name'].$sFlag;
			}			
		}
		**/
		$sCatName = substr($sCatName,0,-(strlen($sFlag)));		
		return $sCatName;
	}
	
	/**
	 * 根据类别id取得类别名称，如果是子类，则同时获取父类的名称
	 *
	 * @param string $sTbl
	 * @param int $id
	 * @param int $iSort  0表示横排－， 1表示横排→， 2表示竖排
	 * @return string
	 */
	
	function getLink($sTbl,$id,$sDir="",$sTitleClass="white",$sFlag=" >> ")
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
		
		$aID = split(",",$id);
		//print_r($aID); //exit;
		$sCatLink = "";
		$sTitleClass = ($sTitleClass) ? "class='$sTitleClass'" : "";	
		
		for($i=count($aID)-1; $i>=0; $i--)	
		{
			if($aID[$i])
			{
				$sUrl = (strpos($sDir,".php")) ? $sDir."?cID=".$aID[$i] : $sDir.$aID[$i].".shtml";
				$aField = category::getInfo($sTbl_1,$aID[$i]); //								
				$sCatLink .=  "<a href='".$sUrl."' $sTitleClass>".$aField['name']."</a>".$sFlag;					
			}		
		}
		
		/**
		foreach ($aID as $iKey=>$sValue) 
		{
			if($sValue) 
			{
				$aField = category::getInfo($sTbl_1,$sValue); //								
				$sCatLink .=  "<a href='".$sDir.$sValue.".shtml' $sTitleClass>".$aField['name']."</a>".$sFlag;					
			}			
		}
		**/

		//echo $sCatLink."<br>";
		$sCatLink = substr($sCatLink,0,-(strlen($sFlag)+4))."</a>";
		//echo $sCatLink; exit;
		return $sCatLink;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $sTbl
	 * @param int $id
	 * @return array
	 */
	
	function getInfo($sTbl,$id,$sWhere="")
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
			
		if($sWhere)
			$sWhere .= " AND sID=$id";
		else 
			$sWhere = "id=$id";
			
		$sSQL = "SELECT * from $sTbl WHERE $sWhere";
		//echo $sSQL; exit;
		$aList = $db->getRecordSet($sSQL,1);
		//print_r($aList);
		return $aList;
	}
	
	
	function getSubID($sTbl,$pID,$sWhere="")
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
		
		$sWhere = " pID = $pID OR id=$pID";		
		$sSQL = "SELECT id FROM $sTbl WHERE $sWhere";		
		$aList = $db->getRecordSet($sSQL);		
		$sCID = array2Set($aList,"id");	
		return $sCID;		
	}
	
	/**
	 * 生成分类下拉菜单（递归）
	 * @param int $iMaxDepth  -1表示无限级分类； 0表示一级分类； 1表示二级分类；以此类推
	 */
	
	function getOptions($sTbl,$iMaxDepth=-1,$pidMe=0,$sWhere="",$pid=0,$iDepth=0,$bIsFirst=true)
	{
		global $db, $tablepre;
		$sTbl_1 = $sTbl;
		$sTbl = $tablepre.$sTbl_1;
		
		static $sOption;
		if($bIsFirst) $sOption = "";
		$sWhere1 = $sWhere ? $sWhere : "1";
		$sSQL = "select * from $sTbl where pID =$pid AND $sWhere1 order by orderID asc";
		$aRs = $db->getRecordSet($sSQL);			
		$iCounts = count($aRs);	
				
		for($i=0; $i<$iCounts; $i++)
		{
			$iID = ($sWhere) ? $aRs[$i]['sID'] : $aRs[$i]['id'];
			$str = ($pid > 0 ) ?  str_repeat("&nbsp;&nbsp;", $iDepth)."&nbsp;&nbsp;&raquo;&nbsp;" : "";
		   	if($pidMe == $iID) 
	      		$sOption .= "<option value=".$iID." selected>".$str.$aRs[$i]['name']."</option>";
	    	else 
	      		$sOption .= "<option value=".$iID.">".$str.$aRs[$i]['name']."</option>";
	      		
			if($iMaxDepth== -1 || $iMaxDepth>$iDepth) 
				self::getOptions($sTbl_1,$iMaxDepth,$pidMe,$sWhere,$iID,$iDepth+1,false); //递归	
		}
		return $sOption;  				
	}	
}
?>