<?php
function cgChk(){
	$aQuesAll = @include('question.php');
	$count = count($aQuesAll);
	$Qnum = getQuestion();
	foreach($Qnum as $k=>$v){
		preg_match('~<([0-9]+)>~is',$v,$a);
		preg_match('~\(\s*([^\)]*)\)~is',$v,$b);
		$aYes[$a[1]]=$b[1];
		$v = preg_replace('~<[0-9]+>~is',$k,$v);//增加排序号
		$v = preg_replace('~\(\s*[^\)]*\)~is','()',$v);//去掉答案
		$v = preg_replace('~A、~s',"<br/>A、",$v);
		$v = preg_replace('~([A|B|C|D]+)、~s',"<input name=\"info[".$a[1]."]\" value=\"\${1}\" type=\"radio\" />\${1}、",$v);//增加单选
		$str .= $v.'<br/>';
	}
	return $str;
}

function getQuestion($Qnum=array(),$i=1){
	global $count,$aQuesAll;
	if($i>10)
	return $Qnum;
	$randnum = mt_rand(0,$count-1);
	if(!in_array($aQuesAll[$randnum],$Qnum)){
		$Qnum[$i] = $aQuesAll[$randnum];
		$i++;
	}
	return getQuestion($Qnum,$i);	
}

function getIdeaByHits(){
	$sql = "SELECT COUNT(*) as count,`ideaid` FROM `hd_hits` GROUP BY `ideaid` LIMIT 0,6";
	$v   = getRecordSet($sql);
	foreach($v as $key=>&$val){
		$sql = "SELECT * FROM `hd_idea` WHERE `ideaid`='$val[ideaid]'";
		$r = getRecordSet($sql);
		foreach($r as $a=>$b){
			foreach($b as $c=>$d){
				$val[$c] = $d;
			}
		}
	}
	return $v;
}

function getIdeaNews(){
	$sql = "SELECT * FROM `hd_idea` ORDER BY `ideaid` DESC LIMIT 0,6";
	$v   = getRecordSet($sql);
	foreach($v as $key=>&$val){
		$sql = "SELECT * FROM `hd_idea` WHERE `ideaid`='$val[ideaid]'";
		$r = getRecordSet($sql);
		foreach($r as $a=>$b){
			foreach($b as $c=>$d){
				$val[$c] = $d;
			}
		}
	}
	return $v;
}

function getDetail($id,$type=''){
	if($type=='ip'){
		$sql = "SELECT * FROM `hd_ip` WHERE `ip`='$id'";
	}else{
		$sql = "SELECT * FROM `hd_idea`	WHERE `ideaid`='$id'";
	}
	$v   = getRecordSet($sql);
	return $v;
}

function getAllList(){
	//得到总数目
	$TotalNum = getRowsNum("SELECT COUNT(ideaid) as count FROM `hd_idea`");
	$nowPage = empty($_REQUEST["pageNo"]) ? 1 : intval($_REQUEST["pageNo"]); 
	$perPage = defined("PERPAGE") ? PERPAGE : 12;
	$startNo = ($nowPage - 1) * $perPage;
	
	$rows = getRecordSet("select * from `hd_idea` order by ideaid asc limit {$startNo}, $perPage");
	
	$dividePage = dividePage("tp.php", $TotalNum['count'], $perPage, $nowPage, "");
	return array('pages'=>$dividePage,'rs'=>$rows);
}

//导航
function catpos($catid=0)
{
	global $CATEGORY;
	$catid = intval($catid);
	if(!isset($CATEGORY[$catid])) return '';
	$pos = '';
	$arrparentid = array_filter(explode(',', $CATEGORY[$catid]['arrparentid'].','.$catid));
	foreach($arrparentid as $catid)
	{
		$url = $CATEGORY[$catid]['url'];
		$pos .= '<a href="'.$url.'">'.$CATEGORY[$catid]['catname'].'</a> &gt; ';
	}
	$n = strrpos($pos,'&gt;');
	return substr($pos,0,$n);
}

//栏目最热文章

function get_category_hot($catid=0,$ofset=0,$num=10){
	global $CATEGORY;
	$catid = intval($catid);
	$catids = $CATEGORY[$catid][arrchildid];
	$sql = "SELECT title,url FROM `Article` a ,`article_count` b WHERE a.catid in($catids) and a.articleid=b.articleid order by hits desc limit $ofset,$num";
	//echo $sql;	
	$r = getRecordSet($sql);
	return $r;
}	

function zazhi_one_cat(){
	$sql = "SELECT catid FROM `Category` WHERE `parentid` =10143";
	$r = getRecordSet($sql);
	$tmp = array();
	foreach ($r as $k=>$v){
		$tmp[] = $v[catid];
	}
}

function cat_big_pic($catid){
	global $CATEGORY;
	if(strpos($CATEGORY[10145][arrchildid],$catid)) $adsid = 1915;
	elseif(strpos($CATEGORY[10147][arrchildid],$catid)) $adsid = 1916;
	elseif(strpos($CATEGORY[10149][arrchildid],$catid)) $adsid = 1917;
	elseif(strpos($CATEGORY[10151][arrchildid],$catid)) $adsid = 1918;
	elseif(strpos($CATEGORY[10152][arrchildid],$catid)) $adsid = 1919;
	else $adsid = 1920;
	$r = get_ads($adsid);
	return '<a href="'.$r[linkurl].'" target="_blank"><img src="uploadfile/'.$r[imageurl].'" title="'.$r[adsname].'" width="'.$r[width].'" height="'.$r[height].'"/></a>
            <p><a href="'.$r[linkurl].'" target="_blank">'.$r[adsname].'</a></p>';	
}

function get_ads($adsid=0,$num=1){
	if($adsid){
		$sql = "select adsname,a.introduce,linkurl,imageurl,b.width,b.height from ads a, Adsplace b where a.placeid=$adsid and a.placeid=b.placeid";
		//echo $sql;
		$r = getRecordSet($sql,$num);
		return $r;
	}
}

//横幅
function get_hengfu($adsid){
	$r = get_ads($adsid);
	return '<a href="'.$r[linkurl].'" target="_blank"><img src="uploadfile/'.$r[imageurl].'" title="'.$r[adsname].'" width="'.$r[width].'" height="'.$r[height].'" class="subbanner"/></a>';
}

//该栏目下的期刊
function get_qikan($catid){
	$sql = "select a.posid,thumb,name from Article_position a,Position b where a.catid=$catid and a.thumb!='' and a.posid=b.posid limit 0,8";
	//echo $sql;
	$r = getRecordSet($sql);
	foreach ($r as $k=>&$v){
		$v[url] = "http://ezone.9939.com/q.php?catid=$catid&q=$v[posid]";
		if(strpos($v[thumb],'uploadfile/')===false) $v[thumb] = 'uploadfile/'.$v[thumb];
	}
	return $r;
}

//获取期刊名称
function get_qk_name($q){
	$sql = "select name from Position where posid=$q";
	$r = getRecordSet($sql,1);
	return $r[name];
}

//获取期刊内容
function get_qk_content($q,$ofset=0,$num=10){
	$sql = "select title,url from Article_position where posid=$q limit $ofset,$num";
	$r = getRecordSet($sql);
	foreach ($r as $k=>&$v){
		$v['url'] = 'http://ezone.9939.com/'.str_replace('e-zine/','',$v['url']);
	}
	return $r;
}

function get_zazhi_intr($catid){
	$temp = array(10200=>2088,10201=>2089,10202=>2090,10205=>2091,10206=>2092,10207=>2093,10208=>2094,10209=>2095,10210=>2096,	10211=>2097,10212=>2098,
	10213=>2099,10214=>2100,10215=>2101,10216=>2102,10217=>2103,10218=>2104,10219=>2105,10220=>2106,10221=>2107,10222=>2108,10223=>2109,10224=>2110,
	10225=>2111,10226=>2112,10227=>2113,10228=>2114,10229=>2115,10330=>2116,10333=>2117,10332=>2118,10331=>2119);
	$catid = $temp[$catid];
	include("/home/web/ht-9939-com/data/data_adsplace_$catid.php");
	foreach ($_ADSGLOBAL[$catid] as $k=>$v)
	{		
		$str .= '<div style="text-indent:2em">'.$v['introduce'].'</div><div><a href="'.$v['linkurl'].'">
		<img src="'.$v['imageurl'].'" width="238" height="'.$v['height'].'"/></a></div>';		
	}
	return $str;
}

//人气排行
function paihang($catid,$order='hits',$ofset=0,$num=5){
	$sql = "SELECT title,url,thumb,description FROM `Article` a ,`article_count` b WHERE a.catid=$catid and a.articleid=b.articleid and thumb!='' and description!='' order by $order desc limit $ofset,$num";
	$r = getRecordSet($sql);
	foreach ($r as $k=>&$v){
		$v[description] = getSubstr($v[description],0,27);
		if(strpos($v[thumb],'uploadfile/')===false) $v[thumb] = 'uploadfile/'.$v[thumb];
	}
	return $r;
}

//排行——文字链
function paihang_wz($catid,$ofset=0,$num=9){
	$sql = "SELECT title,url FROM `Article` a ,`article_count` b WHERE a.catid=$catid and a.articleid=b.articleid order by hits desc limit $ofset,$num";
	$r = getRecordSet($sql);
	return $r;
}

//投票标题
function toupiao_subject($cid){
	$sql = "select id,title from Toupiao where cid=$cid";
	$r = getRecordSet($sql,1);
	return $r;
}

//投票项目
function toupiao_item($pid){
	$sql = "select tid,themes from Toupiao_themes where pid=$pid";
	//echo $sql;
	$r = getRecordSet($sql);
	return $r;
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

//分页
function dividePage($sPage,$iTotalNum,$iPerpage,$iPageNo,$sExt)
{
	//开始显示的数目
	$iPageStart = ($iPageNo - 1) * $iPerpage + 1;
	//共有多少页
	$iTotalPage = ceil($iTotalNum / $iPerpage);
	if($iPageNo < $iTotalPage)
		$iEndPage = $iPageStart + $iPerpage - 1;
	else 
		$iEndPage = $iTotalNum;
	
	$sStr = " <table width='100%' border='1' cellspacing='0' cellpadding='0' align='center' bordercolordark='#FFFFFF' bordercolorlight='#000000'  bgcolor='#efefef'><tr align=center><td>●";
	$sStr .= "共".$iTotalNum."条记录";
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
    $sStr .=  "</td> <form name=form1 method=post action={$sPage}?{$sExt}><td width='15%' align=center><input name=pageNo type=text class='form_text' size=2  value=$iPageNo> <input type=submit name=Submit2 value=go class='button'></td> </form></tr></table>";
	return $sStr;
}
function deldir($dir){
	if(is_dir($dir)){
		$handel = opendir($dir);
			while(false !== ($file = readdir($handel))){
				if($file<>'.' && $file<>'..'){
					$dir_deep = $dir.'/'.$file;
					if(!is_dir($dir_deep)){
						@unlink($dir_deep);
					}else{
						deldir($dir_deep);
					}
				}
			}
		 closedir($handel);
		 if(@rmdir($dir)) return true;
		 else return false;
	}else{
		echo '目录不存在';
		return false;
	}
}
?>