<?php
/**
 * 过程性函数
 *  @author xiongzhixin (xzx747@sohu.com) 2008-07-08
 */

/**
 * 加载库文件
 *
 * @param string $sLibName
 */
function loadLib($sLibName) {
	$sLibFullName = $sLibName.".lib.php";
	//echo ROOT;
	require_once(ROOT."./class/".$sLibFullName);
}


/**
 * 前台分页程序(正常分页)
 *
 * @param string $sPage  	- 显示的页面
 * @param int $iTotalNum	- 总数
 * @param int $iPerpage		- 每页显示数量
 * @param int $iPageNo		- 当前页号
 * @param string $sExt		- 其他参数
 */


function dividePage_1($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
{
	global $tpl;
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;	
	
	
	$sStr = "";
		
	$sStr .= "第{$iPageNo}页  共{$iTotalPage}页 ";	
	$iPrev = $iPageNo - 1;
	$iNext = $iPageNo + 1;
	
	if($iPageNo == 1) {	
		//当前页数是1，第一页没有链接    	
    	$sStr .= "最前页 ";
    	
    } else {
    	//当前页数不是1，第一页有链接
    	$sStr .= "<a href='{$sPage}?pageNo=1&{$sExt}'>最前页</a> ";
    }
    //判断上一页
    if($iPrev < 1) {
    	//没有上一页
    	$sStr .= "  上一页 ";
    } else {
    	//有上一页
    	$sStr .= "<a href='{$sPage}?pageNo={$iPrev}&{$sExt}' >上一页</a> ";
    }
    //判断下一页
    if ($iNext > $iTotalPage) {
    	//没有下一页了
        $sStr .= "下一页 ";
    } else {
    	//还有下一页
    	$sStr .= "<a href='{$sPage}?pageNo={$iNext}&{$sExt}' >下一页</a> ";
    }
    if ($iPageNo >= $iTotalPage) {
        $sStr .= "最末页";
    } else {
    	$sStr .= "<a href='{$sPage}?pageNo={$iTotalPage}&{$sExt}' >最末页</a> ";
    }   
       
	$tpl->assign("Divide_Page", $sStr);
}


/**
 * 伪静态列表分页程序
 *
 * @param string $sPage  	- 显示的页面
 * @param int $iTotalNum	- 总数
 * @param int $iPerpage		- 每页显示数量
 * @param int $iPageNo		- 当前页号
 * @param string $sExt		- 其他参数
 */


#function dividePage_list($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
function dividePage_list($sPage,$iTotalNum,$iPerpage,$iPageNo,$sHtm="html")
{
	global $tpl;
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;	
	
	$sStr = "";
	
	$iPrev = $iPageNo - 1;
	$iNext = $iPageNo + 1;
	
	$sStr = "<a href='{$sPage}1.$sHtm'><  首页</a> ";
	
    //判断上一页
    if($iPrev < 1) {
    	//没有上一页
    	$sStr .= "";
    } else {
    	//有上一页
    	$sStr .= "<a href='{$sPage}{$iPrev}.$sHtm'><  上一页</a> ";
    }
    
    //判断下一页
    if ($iNext > $iTotalPage) {
    	//没有下一页了
        $sStr .= "";
    } else {
    	//还有下一页
    	$sStr .= "<a href='{$sPage}{$iNext}.$sHtm'>下一页  > </a> ";
    }    
    
    $sStr .= "<a href='{$sPage}{$iTotalPage}.$sHtm'>末页  > </a>";
       
	$tpl->assign("Divide_Page", $sStr);
}

/**
 * 医院列表分页程序
 *
 * @param string $sPage  	- 显示的页面
 * @param int $iTotalNum	- 总数
 * @param int $iPerpage		- 每页显示数量
 * @param int $iPageNo		- 当前页号
 * @param string $sExt		- 其他参数
 */


function dividePage_hospital($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
{
	global $tpl;
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;	
	
	$sStr = "";
	
	$iPrev = $iPageNo - 1;
	$iNext = $iPageNo + 1;
	
	$sStr = "<a href='{$sPage}?pageNo=1&{$sExt}'><  首页</a> ";
	
    //判断上一页
    if($iPrev < 1) {
    	//没有上一页
    	$sStr .= "";
    } else {
    	//有上一页
    	$sStr .= "<a href='{$sPage}?pageNo={$iPrev}&{$sExt}'><  上一页</a> ";
    }
    
    //判断下一页
    if ($iNext > $iTotalPage) {
    	//没有下一页了
        $sStr .= "";
    } else {
    	//还有下一页
    	$sStr .= "<a href='{$sPage}?pageNo={$iNext}&{$sExt}'>下一页  > </a> ";
    }    
    
    $sStr .= "<a href='{$sPage}?pageNo=$iTotalPage&{$sExt}'>末页  > </a>";
       
	$tpl->assign("Divide_Page", $sStr);
}


/**
 * 药品列表分页1程序
 *
 * @param string $sPage  	- 显示的页面
 * @param int $iTotalNum	- 总数
 * @param int $iPerpage		- 每页显示数量
 * @param int $iPageNo		- 当前页号
 * @param string $sExt		- 其他参数
 */


function dividePage_medicine1($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
{
	global $tpl;
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;	
	
	$sStr = "";
	
	$iPrev = $iPageNo - 1;
	$iNext = $iPageNo + 1;
	
    //判断上一页
    if($iPrev < 1) {
    	//没有上一页
    	$sStr .= "<div class='area5'><a disabled>上一页</a></div> ";
    } else {
    	//有上一页
    	$sStr .= "<div class='area5'><a href='{$sPage}?pageNo={$iPrev}&{$sExt}'>上一页</a></div>";
    }
    
    //判断下一页
    if ($iNext > $iTotalPage) {
    	//没有下一页了
        $sStr .= " <div class='area5'><a disabled>下一页</a></div> ";
    } else {
    	//还有下一页
    	$sStr .= "<div class='area5'><a href='{$sPage}?pageNo={$iNext}&{$sExt}'>下一页</a></div>";
    }
    
    $sStr .= "<div class='area4'>".$iPageNo."/".$iTotalPage."</div>"; 
       
	$tpl->assign("Divide_Page_Medicine1", $sStr);
}

/**
 * 药品列表分页程序
 *
 * @param string $sPage  	- 显示的页面
 * @param int $iTotalNum	- 总数
 * @param int $iPerpage		- 每页显示数量
 * @param int $iPageNo		- 当前页号
 * @param string $sExt		- 其他参数
 */


function dividePage_medicine2($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
{
	global $tpl;
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;	
	
	$sStr = "";
	
	for($i=$iPageNo; $i<$iTotalPage; $i++)
	{
		if($iTotalPage - $i <=10)
			$sStr .= "<div class='area3'><a class='stay' href='#' target='_self'>$i</a></div>";
	}
	
	/**
<div class='area3'><a class='stay' href='#' target='_self'>1</a></div>
<div class='area3'><a href='#' target='_self'>2</a></div>
<div class='area3'><a href='#' target='_self'>下一页</a></div>
<div class='area3'><a href='#' target='_self'>末页</a></div>
	 */
	
	$iPrev = $iPageNo - 1;
	$iNext = $iPageNo + 1;
	
    //判断上一页
    if($iPrev < 1) {
    	//没有上一页
    	$sStr .= "<div class='area3'><a disabled>上一页</a></div> ";
    } else {
    	//有上一页
    	$sStr .= "<div class='area3'><a href='{$sPage}?pageNo={$iPrev}&{$sExt}'>上一页</a></div>";
    }
    
    //判断下一页
    if ($iNext > $iTotalPage) {
    	//没有下一页了
        $sStr .= " <div class='area3'><a disabled>下一页</a></div> ";
    } else {
    	//还有下一页
    	$sStr .= "<div class='area3'><a href='{$sPage}?pageNo={$iNext}&{$sExt}'>下一页</a></div>";
    }
    
    $sStr .= "<div class='area4'>".$iPageNo."/".$iTotalPage."</div>"; 
       
	$tpl->assign("Divide_Page_Medicine2", $sStr);
}

/**
 * 后台分页程序
 *
 * @param string $sPage  	- 显示的页面
 * @param int $iTotalNum	- 总数
 * @param int $iPerpage		- 每页显示数量
 * @param int $iPageNo		- 当前页号
 * @param string $sExt		- 其他参数
 */
function dividePage($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
{
	global $tpl;
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;
	
	$sStr = " <table width='100%' border='1' cellspacing='0' cellpadding='0' align='center' bordercolordark='#FFFFFF' bordercolorlight='#000000'  bgcolor='#efefef'><tr align=center><td>●";
	$sStr .= "共".$iTotalNum."条记录,每页显示{$iPerpage}条，列出第".$iPageStart;
	$sStr .= "到第";
	$sStr .= $iEndPage;
	$sStr .= "条 ";
	$sStr .= "</td>  <td> <div align='center'>";
	$iPrev = $iPageNo - 1;
	$iNext = $iPageNo + 1;
	
	if($iPageNo == 1) {	
		//当前页数是1，第一页没有链接
    	$sStr .= "第一页";
    } else {
    	//当前页数不是1，第一页有链接
    	$sStr .= "<a href='{$sPage}?pageNo=1&{$sExt}'>第一页</a> ";
    }
    //判断上一页
    if($iPrev < 1) {
    	//没有上一页
    	$sStr .= "  上一页 ";
    } else {
    	//有上一页
    	$sStr .= "<a href='{$sPage}?pageNo={$iPrev}&{$sExt}'>上一页</a> ";
    }
    //判断下一页
    if ($iNext > $iTotalPage) {
    	//没有下一页了
        $sStr .= "下一页 ";
    } else {
    	//还有下一页
    	$sStr .= "<a href='{$sPage}?pageNo={$iNext}&{$sExt}'>下一页</a> ";
    }
    if ($iPageNo >= $iTotalPage) {
        $sStr .= "最后一页";
    } else {
    	$sStr .= "<a href='{$sPage}?pageNo={$iTotalPage}&{$sExt}'>最后一页</a> ";
    }
    $sStr .= "</div> </td> <td width='15%'> ";
    $sStr .= "第".$iPageNo."/".$iTotalPage."页 ";
    $sStr .=  "</td> <form name=\"form1\" method=\"post\" action=\"{$sPage}?{$sExt}\"><td width='15%' align=center><input name=pageNo id='pageNo' type=text class='form_text' size=2  value=$iPageNo> <input type=submit name=Submit2 value=go class='button' id=\"Submit2\"></td> </form></tr></table>";
	$tpl->assign("Divide_Page", $sStr);
}


/**
 * 跳转页面
 *
 * @param string $sURL
 * @param integer $iTime
 * @param string $sMsg
 */
function redirect($sURL, $iTime, $sMsg = "",$bIsLogin = "") {
	global $tpl;
	$tpl->assign("sURL", $sURL);
	$tpl->assign("iTime", $iTime);
	$tpl->assign("sMSG", $sMsg);
	$tpl->assign("__WEBNAME", __WEBNAME);
	if($bIsLogin)
		$tpl->display("admin/redirect_login.tpl.htm");
	else 
		$tpl->display("admin/redirect.tpl.htm");	
	exit;	
}

/** js跳转 **/
function redirect_j($sMsg,$sUrl="")
{
	if($sUrl == "")
		echo "<script>alert('$sMsg');history.go(-1);</script>";
	elseif($sMsg == "") 
		echo "<script>location.href='$sUrl';</script>";			
	else 
		echo "<script>alert('$sMsg');location.href='$sUrl';</script>";	
	exit;
}

/**
 * 出错页面
 *
 * @param unknown_type $sMsg
 */
function showError($sMsg) {
	echo $sMsg;
	exit;
}

//取IP
function getIP() {
	if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		}
		elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		}
		elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
}

/**
 * 数组转换成用某个符号隔开的字符串 added by xzx 2007-11-01
 *
 * @param array $aArr
 * @param string $sFlag
 * @return string
 */
function array2Str($aArr,$sFlag=",")
{
	$sStr = "";
	foreach ($aArr as $iKey => $sValue) $sStr .= ($sValue <> "") ? $sValue."," : "";	
	$sStr = substr($sStr,0,-1);
	return $sStr;
}

/**
 * 数组转换成集合
 *
 * @param array $aArr
 * @param string $sField
 * @return string
 */
function array2Set($aArr,$sFieldName = NULL)
{
	$t = "(";
	$iArrayLength = count($aArr);
	if($iArrayLength == 0)
		return "(-1)";
	for($i = 0; $i <$iArrayLength; $i++)
	{
		if($i <> ($iArrayLength -1) )
		{
			if($sFieldName == "")
				$t .= "\"".$aArr[$i]."\",";
			else 
			{
				if($sFieldName <> NULL)
					$t .= "\"".$aArr[$i][$sFieldName]."\",";
				else 
					$t .= "\"".$aArr[$i]."\",";
			}
		}
		else
		{ 
			if($sFieldName == "")
				$t .= "\"".$aArr[$i]."\")";
			else 
			{
				if($sFieldName <> NULL)
					$t .= "\"".$aArr[$i][$sFieldName]."\")";
				else 
					$t .= "\"".$aArr[$i]."\")";
			}
		}
	}
	return $t;
}

/**
 * 数组中的一维作为key，一维作为value
 *
 * @param array $aArray
 * @param string $sFieldName1	- key
 * @param string $sFieldName2	- value
 * @return array
 */
function array2Array($aArray, $sFieldName1, $sFieldName2)
{
	$t = array();
	foreach ($aArray as $rr)
		$t[$rr[$sFieldName1]] = $rr[$sFieldName2];
	return $t;
}

/**
 * 取得后几天的日期函数  
 *
 * @param unknown_type $sDate
 * @param unknown_type $month_
 * @param unknown_type $day_
 * @param unknown_type $year
 * @return unknown
 */
function _mktime($sDate,$month_ = 0, $day_ = 0, $year_ = 0)
{
	$year = substr($sDate, 0 , 4);
	$month = substr($sDate, 5, 2);
	$day = substr($sDate, 8, 2);
	$hour = substr($sDate, 11, 2);
	$minute = substr($sDate, 14, 2);
	$second = substr($sDate, 17, 2);
	return date("Y-m-d",mktime($hour, $minute, $second, $month + $month_, $day + $day_, $year + $year_));
}

/**
 * 跳转页面
 *
 * @param unknown_type $sUrl
 */
function locationWin($sUrl)
{
	echo "<script>location.href='$sUrl';</script>";
	exit;
}


/**
 * 关闭窗口
 *
 */
function closeWin()
{
	echo "<script>window.close();</script>";
}

/**
 * 权限检查
 *
 * @param unknown_type $sFileName
 * @param unknown_type $sAction
 */
function checkExecPower($sFileName, $sAction)
{
	
	$sAction == str_replace("Save","",$sAction);
	$bIsHavePower = manager::checkPower($_SESSION["xzx_uID"], $sFileName, $sAction);
	if(!$bIsHavePower)
	{	
		redirect("home.php", 5, "你无权执行这个功能");
		exit();
	}
	
}


// 通过采集对页面进行生成
function createHtm($sUploadPath,$sFileName,$id,$sTbl="",$iPage=1,$sWhere="")
{		
	$sUrl = __ADMIN_URL.$_SERVER['PHP_SELF']."?act=showPage&tbl=$sTbl&id=".$id."&page=$iPage&$sWhere";
	$sFilePath = $sUploadPath . "/". substr($sFileName,0,7);	
	if(!is_dir($sFilePath)) 
	{
		mkdir($sFilePath);
		chmod($sFilePath,0777);
	}
		
	$sContents = file_get_contents($sUrl);	
	//echo $sContents; exit;
	@unlink($sUploadPath."/".$sFileName);
	//echo $sUploadPath."/".$sFileName; exit;
	$fp = fopen($sUploadPath."/".$sFileName,"w+");		
	if(fwrite($fp, $sContents))
		return true;
	else
		return false;
	fclose($fp);			
}

/**
 * 获取定长的子字符串
 *
 * @param string $str
 * @param int $lenth
 * @return string
 */

function str_sub($str,$lenth)
{
	$tem=$str;
	if(strlen($str)>$lenth)
	{
		$i=ord(substr($str,$lenth-1,1));
		if($i<=122)
			$tem=substr($str,0,$lenth);
		else
		{
			if(getstrsize($str,$lenth)%2==0)
				$tem=substr($str,0,$lenth);
			else
		    {
			    $tem=substr($str,0,$lenth-1);
			}
		}
		}
	return $tem;
}


function getstrsize($str,$len)
{
	$m=0;
	for($i=0;$i<$len;$i++)
	{
		$j=ord(substr($str,$i,1));
		if(!($j<=122))
			$m++;
	}
	return $m;
}

//日期相减得到天数
function sDate_sDate($Date_1,$Date_2)
{
	$Date_List_1=explode("-",$Date_1);
	$Date_List_2=explode("-",$Date_2);
	$d1=mktime(0,0,0,$Date_List_1[1],$Date_List_1[2],$Date_List_1[0]);
	$d2=mktime(0,0,0,$Date_List_2[1],$Date_List_2[2],$Date_List_2[0]);
	$Days=round(($d1-$d2)/3600/24);
	return $Days;
}

/**
 * 获取类别下拉菜单...
 *
 * @param 类别表 $sClass
 * @param 类别ID $cID
 * @return string
 */
function getOptions($sClass,$cID=0)
{	
	require(ROOT."./include_ask/{$sClass}_options.php");
	$aOld = array("={$cID} ","={$cID}>");
	$aNew = array("={$cID} selected ","={$cID} selected>");
	$sOptions = str_replace($aOld, $aNew,$sOptions);
	return $sOptions;
}

/**
 * 数据库连接
 *
 * @param int $flag
 * @return unknown
 */
function DBconnect($flag =0 )
{	
	global $charset;
	if($flag == 0)
		$db = new Database(DB_HOST,DB_NAME,DB_USER,DB_PW,OPEN_DEBUG);
	elseif($flag == 1)
		$db = new Database(DB_HOST_1,DB_NAME_1,DB_USER_1,DB_PW_1,OPEN_DEBUG);		
	elseif($flag == 2)
		$db = new Database(DB_HOST_2,DB_NAME_2,DB_USER_2,DB_PW_2,OPEN_DEBUG);		
	elseif($flag == 3)
		$db = new Database(DB_HOST_3,DB_NAME_3,DB_USER_3,DB_PW_3,OPEN_DEBUG);		
	elseif($flag == 4)
		$db = new Database('211.167.92.236','9939_com_new','9939_com_new','fsdli%&osdss^**(fgsg',OPEN_DEBUG);		
	elseif($flag == 5)
		$db = new Database('211.167.92.236','9939_com_dzjb','9939_com_dzjb','wofetw%^%teye$%&*ioo^*etyey',OPEN_DEBUG);		
	$db->connect();	

	mysql_query("SET character_set_connection={$charset}, character_set_results={$charset}, character_set_client=binary");
	
	//mysql_query("SET character_set_connection=gbk, character_set_results=gbk, character_set_client=binary ");
	return 	$db;
}


function cache_write($file, $array)
{
	$array = "<?php\nreturn  array(".$array.");\n?>";
	$cachefile = NET_ROOT.'include_ask/'.$file;
	$strlen = file_put_contents($cachefile, $array);
	@chmod($cachefile, 0777);
	return $strlen;
}

function cache_read($file)
{
	$cachefile = NET_ROOT.'include_ask/'.$file;
	return include $cachefile;
}



/*字符串截取函数
 * $str 字符串
 * $beginStr 开始取的位置
 * $length  要取的长度,个数
 * $isHaveBland 统计是否包含空格符,0 是不统计空格,1统计空格
 * $codingLength   utf-8 $codingLength=3,gb2312 $codingLength=2;
*/
function getSubstr($str,$beginStr=-1,$length=-1,$isHaveBlank=1,$codingLength=3) 
{
    $len    =    strlen ($str);
    //$str=str_replace("&nbsp;"," ",$str);//过滤掉html空格
    if($length==-1 ) $length=-$beginStr-1;
    $i        =    0;
    $strCount=0;
    $subStr="";
    while ($i<$len) 
    {
        if (preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",substr($str,$i,1)))
        {
            if($strCount>=$beginStr)
            {
				if(strlen(mb_substr($str,$strCount,1,'utf8'))==2){
        			$subStr    .=    substr($str,$i,$codingLength-1);
				}else{
        			$subStr    .=    substr($str,$i,$codingLength);
				}
            	//echo $i.$subStr.'<br>';
			}
			//过滤特殊字符·····
			if(strlen(mb_substr($str,$strCount,1,'utf8'))==2){
				$i += $codingLength-1;	
			}else{
				$i += $codingLength;
			}
            $strCount++;
        }
        elseif (substr($str,$i,6)=="&nbsp;")// 处理空格
        {
        	if($strCount>=$beginStr)
            {
            	$subStr .= substr($str,$i,6);
            }
            if($isHaveBlank==1)//统计空格
            {
            	$strCount++;
            }
            $i+=6;
        }
        else
        {
            if($strCount>=$beginStr)
            {
            	$subStr .= substr($str,$i,1);
            }
            if($isHaveBlank==1)//统计空格
            {
            	$strCount++;
            }
            else//不统计空格
            {
            	if(substr($str,$i,1)!=" ")
            	{
            		$strCount++;
            	}
            }
            $i+=1;
        }
        
        if($strCount==$length+$beginStr)
        {
        	break;
        }
    }
    if($beginStr==-1) 
    {
    	return $strCount;
    }
    //$subStr=str_replace(" ","&nbsp;",$subStr);//还原html空格
    return $subStr;
}



// kxgsy163@163.com addtime 6.3 域名替换
function resite($url)
{
	$aURL  = array('/jf/', '/drug/', '/tijian/', '/zy/', '/pf/', '/jijiu/', '/baby/', '/xa/', '/xinli/', '/bj/', '/ys/', '/jktp/', '/male/', '/female/', '/meirong/', '/js/', '/video/', '/news/', '/huli/', '/symptom/', '/hospital/', '/medicine/', '/doctor/', '/disease/', '/bdfzt/', '/bybyzt/', '/shenbingzt/', '/tnbzt/', '/fukezt/', '/ganbingzt/','/jingzbzt/', '/smyyzt/', '/weibingzt/', '/xinzangbingz/', '/dianxianzt/', '/huizhan/', '/e-zine/', '/gypd/');
	$__URL = array('http://fitness.9939.com/', 'http://drug.9939.com/', 'http://tijian.9939.com/', 'http://zhongyi.9939.com/', 'http://pianfang.9939.com/', 'http://jijiu.9939.com/', 'http://baby.9939.com/', 'http://sex.9939.com/', 'http://xinli.9939.com/', 'http://baojian.9939.com/', 'http://food.9939.com/', 'http://picture.9939.com/', 'http://man.9939.com/', 'http://lady.9939.com/', 'http://beauty.9939.com/', 'http://js.9939.com/', 'http://video.9939.com/', 'http://news.9939.com/', 'http://nurse.9939.com/', 'http://zz.9939.com/', 'http://hospital.9939.com/', 'http://yiyao.9939.com/', 'http://yisheng.9939.com/', 'http://jb.9939.com/', 'http://bdf.9939.com/', 'http://byby.9939.com/', 'http://shcare.9939.com/', 'http://tnb.9939.com/', 'http://fk.9939.com/', 'http://gb.9939.com/', 'http://jzb.9939.com/', 'http://smyy.9939.com/', 'http://wb.9939.com/', 'http://xzb.9939.com/', 'http://dx.9939.com/', 'http://zhanhui.9939.com/', 'http://ezone.9939.com/', 'http://help.9939.com/');
	//echo $url,"<br>";
	$str = $url;
	$sUrlStart = strpos(substr($str,1),'/');
	$sUrlStart +=1;
	if(empty($sUrlStart)){$sUrlStart=0;}
	$url = str_replace($aURL, $__URL, substr($str,0,$sUrlStart+1));
	$url .= substr($str,$sUrlStart+1);
	return $url;
}
//end



/**
 * 生成编辑器
 * @param   string  input_name  输入框名称
 * @param   string  input_value 输入框值
 */
function create_html_editor($input_name, $input_value = '')
{
    global $tpl;

    $editor = new FCKeditor($input_name);
    $editor->BasePath   = '../include/fckeditor/';
    $editor->ToolbarSet = 'Normal';
    $editor->Width      = '100%';
    $editor->Height     = '320';
    $editor->Value      = $input_value;
    $FCKeditor = $editor->CreateHtml();
    $tpl->assign('FCKeditor', $FCKeditor);
}
?>