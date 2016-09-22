<?php
/**
 * config.php （DB Connect）
 * @author xiongzhixin (xzx747@sohu.com) 2006.11
 */
//error_reporting(0);
//header("Content-type:text/html;charset=utf-8");
DBconnect();

/**
 * 连接数据库
 *
 * @param int $flag  （0 表示网站； 1 表示richweb; 2表示bbs）
 */

function DBconnect($flag=0)
{	
	if($flag == 0)
	{
		$__DBHost		= "218.246.21.99";
		$__DBUser		= "9939_com_v2";
		$__DBPwd        = "newslihge#$%&sineweqw";
		$__DBName		= "9939_com_v2";
	}
	else 
	{
		$__DBHost		= "218.246.21.99";
		$__DBUser		= "9939_com_v2sns";
		$__DBPwd        = "snsrewou#*&#inewk";
		$__DBName		= "9939_com_v2sns";
	}
	
	mysql_connect($__DBHost,$__DBUser,$__DBPwd);
	mysql_select_db($__DBName);	
	mysql_query("SET character_set_connection=UTF8, character_set_results=UTF8, character_set_client=binary");
	mysql_query("set names utf8");
}

/**
 * 获取符合条件的记录总数
 *
 * @param unknown_type $sSQL
 */

function getRowsNum($sSQL)
{	
	//echo $sSQL;
	$mRes  = mysql_query($sSQL);
		//$oQuery = $this->query($sSQL);
		if(strpos(strtolower($sSQL),"count("))
		{
			$aRes = mysql_fetch_array($mRes);
			return $aRes[0];
		}
		else
		{
			$iNums =(mysql_num_rows($mRes)) ? mysql_num_rows($mRes) : 0;
			return $iNums;			
		}	
	
	//$iRows = @intval(mysql_num_rows($mRes));
	
	//$iNums = isset($irw) ? mysql_num_rows($mRes) : 0;
	//return $iRows;
}

function getRecordSet($sSQL, $iCount = NULL)
{
	$aRes = mysql_query($sSQL);	
	$aList = array();
	while ($rr = mysql_fetch_assoc($aRes)) $aList[] = $rr;
	if($iCount <> 1)
		return $aList;
	else 
		return $aList[0];
}

/**
	 * 插入数据到指定的数据表
	 *
	 * @param string $sTableName
	 * @param array $aField
*/

function insert($sTableName, $aField) 
{
		$sSQL = "INSERT INTO `{$sTableName}` ";
		$sField = "(";
		$sValue = "(";
		foreach ($aField as $rk => $rv) {
			$sField .= '`'.$rk.'`'.",";
			$sValue .= "'".$rv."',";			
		}
		$sField = substr($sField, 0, -1).")";
		$sValue = substr($sValue, 0, -1).")";
		$sSQL .= $sField." VALUES ".$sValue;	
		//echo $sSQL."<br>";	exit;
		mysql_query($sSQL);
		$iInsertID = mysql_insert_id();
		return $iInsertID;		
}


/**
 * 更新数据操作
 *
 * @param string $sTableName
 * @param array $aField
 * @param string $sWhere
 */


function update($sTableName, $aField, $sWhere) {
	$sSQL = "UPDATE {$sTableName} SET ";
	$sField = "";
	foreach ($aField as $rk => $rv) {
		$sField .= $rk."='".$rv."',";
	}
	$sField = substr($sField, 0, -1);
	$sSQL .= $sField." WHERE {$sWhere}";
	//echo $sSQL."<br>"; //exit;
	return mysql_query($sSQL);
}


function getFileName($iCreationDate,$id)
{
	return date("Y-m",$iCreationDate)."/".$iCreationDate."d".$id.".shtml";
}


define("TIME_INTERVAL",60*60*12);		//cookie时间间隔  （12 hour）
$__WEB_URL = "http://www.ccn.com.cn";		//网址

define("__WEBNAME","中国消费网");
?>