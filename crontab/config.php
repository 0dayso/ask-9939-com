<?php
/**
 * author: 林原
 * config_new.php （DB Connect）
 */
//error_reporting(0);
header("Content-type:text/html;charset=utf-8");
DBconnect();

/**
 * 生成缓存
 * @author  LinYuan 2010-4-21
 * @param string $filename 要缓存的文件名
 * @param array $arr 要缓存的数组
 * @param int $time 缓存的时间 单位 小时
 * @return unknown
 */
function saveCache($filename,$data,$time) {
	define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
	$filename = ROOT."cache/".$filename;
	//echo $filename;exit;
	$arr = array('content'=>$data,'addtime'=>time(),'savetime'=>$time);
	$content = serialize($arr);
	file_put_contents($filename,$content);
}




/**
 * 连接数据库
 *
 * 
 */

function DBconnect($flag=0)
{		
	if($flag == 0)
	{
		$__DBHost		= "192.168.220.189";  //read
		$__DBUser		= "9939_com_v2sns";
		$__DBPwd        = "snsrewou#*&#inewk";
		$__DBName		= "9939_com_v2sns";
	}
	elseif($flag == 1)
	{	
		$__DBHost		= "192.168.220.189";  //read
		$__DBUser		= "9939_com_v2";
		$__DBPwd        = "newslihge#$%&sineweqw";
		$__DBName		= "9939_com_v2";
	}
	
	mysql_connect($__DBHost,$__DBUser,$__DBPwd);
	mysql_select_db($__DBName);	
	mysql_query("SET character_set_connection=UTF8, character_set_results=UTF8, character_set_client=binary");
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
	echo $sSQL."<hr>";
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
			$sField .= $rk.",";
			$sValue .= "'".$rv."',";			
		}
		$sField = substr($sField, 0, -1).")";
		$sValue = substr($sValue, 0, -1).")";
		$sSQL .= $sField." VALUES ".$sValue;	
		//echo $sSQL;	exit;
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


?>