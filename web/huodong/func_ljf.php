<?php

function get_hj_user(){
	$arr = include('gifts.php');
	$sql = "select * from hd_ask_gift order by giftid desc limit 0,10";
	$r = getRecordSet($sql);
	if($r){
		foreach ($r as $k=>$v){
			$r[$k]['name'] = $arr[$v['giftid']];//奖品名称
			//奖品等级
			if ($v['giftid']<=3) $r[$k]['rank'] = $v['giftid'].'等奖';
			elseif ($v['giftid'] == 4) $r[$k]['rank'] = '幸运奖';
			elseif ($v['giftid'] == 5) $r[$k]['rank'] = '参与奖';
			elseif ($v['giftid'] == 6) $r[$k]['rank'] = '参与奖';
			elseif ($v['giftid'] == 7) $r[$k]['rank'] = '参与奖';
		}
		return $r;
	}
	return false;
}

function getcgnum($uid){
	if($uid){
		$sql = "select chgnum from hd_ask_chg where uid=$uid";
		//echo $sql;
		$result =getRecordSet($sql,1);
		//var_dump($result);exit;
		return intval($result['chgnum']);
		
	}
}


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
?>