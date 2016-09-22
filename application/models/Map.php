<?php
/**
  *##############################################
  * @FILE_NAME :Map.php
  *##############################################
  *
  * @author : 李军锋
  * @MailAddr : licaption@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : 2009-10-13
  *
  *==============================================
  * @Desc :   
  *==============================================
  */

class Map extends QModels_Ask_Table
{		
	public function get_parent_keshi(){
		$r = "select id,name from wd_keshi where pID=0 and id!=443 and id!=436 and id!=423 and id!=523 order by id ";
		$rs = $this->_db->fetchAll($r);
		return $rs;
	}
	
	public function get_cat_ask($classid=0,$ofset=0){
		$sql = "select id,title,ctime from wd_ask where classid=$classid order by id desc limit $ofset,15";
		//echo $sql;exit;
		$rs = $this->_db->fetchAll($sql);
		foreach ($rs as $k=>&$v){
			$v['title'] = $this->getSubstr($v['title'],0,20);
		}
		return $rs;
	}
	
	
	/*字符串截取函数
	* $str 字符串
	* $beginStr 开始取的位置
	* $length  要取的长度,个数
	* $isHaveBland 统计是否包含空格符,0 是不统计空格,1统计空格
	* $codingLength   utf-8 $codingLength=3,gb2312 $codingLength=2;
	*/
	public function getSubstr($str,$beginStr=-1,$length=-1,$isHaveBlank=1,$codingLength=3)
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

}