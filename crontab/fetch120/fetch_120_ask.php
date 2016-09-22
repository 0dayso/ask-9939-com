<?php

define("ASK_ROOT", dirname(__FILE__));




header("Cache -Control: no-store, no-cache ,must-revalidate");
header("Cache -Control: post-check=0, pre-check=0", false);
ini_set('max_execution_time', 0);
ini_set('max_input_time', -1);
date_default_timezone_set("asia/shanghai");
//echo get_magic_quotes_gpc();die();
include "/home/web/ask-9939-com/crontab/fetch120/include/db_mysql.class.php";
include "/home/web/ask-9939-com/crontab/fetch120/include/AskFetcher.php";

$dblocal = new db_mysql();
$linklocal=$dblocal->connect('192.168.1.99','9939_indata','3edcVFR$','test','','utf8');

$db = new db_mysql();
$link=$db->connect('192.168.1.99','9939_indata','3edcVFR$','9939_com_v2sns','','utf8');
//$link=$db->connect('192.168.66.37','9939','9939pwd','9939_com_v2sns','','utf8');

$time_begin = microtime(true);
$time_step_begin = $time_begin;

//所有的科室
$keshi_array=$db->select("select id,class_level1,class_level2,class_level3,name from wd_keshi");


//#####开始 userid #######
//$userid_start=rand(1000,10000);

//####### 参数，取多少条 #######
//$getCount=$_SERVER["argv"][1];


//抓取一页的内容
$keshihref="http://www.120ask.com/keshi.asp?";
AskFetcher::url_get_contents($keshihref,$strSource);

$strSource=mb_convert_encoding($strSource,'UTF-8');

//抓取分页链接
preg_match_all('/<div.*?class="fanye01".*?>.*?(<a.*?href=".*?">.*?<\/a>).*?<\/div>/si', $strSource, $aryResult);
preg_match_all('/<a.*?href=(.*?)>.*?<\/a>/',$aryResult[1][0],$arr);//分页链接
//链接根部$hrefbase，后边只需再加页数即可
$hrefbase= $keshihref.substr($arr[1][0],strpos($arr[1][0],"&"),strrpos($arr[1][0],"=")+1-strpos($arr[1][0],"&"));

/*if (is_file(ASK_ROOT."/tongji/".date("Y-m-d").".txt")) {
	$record = unserialize(file_get_contents(ASK_ROOT."/tongji/".date("Y-m-d").".txt"));
	$i = $record["page"];
}
else {
	$i=1;
}*/
echo "\n start:".date("Y-m-d H:i:s")."#########################\n";
$i=1;
$count=0;
$nowords = array("假 证", "做 假", "办 假", "卖" , "买");
echo $timenow = time()-86400;
$time_step_begin = 0;
do{
	echo "\n";
	echo "collection listpage No $i ...\n";
	$Result="";
	$pagehref=$hrefbase.$i;
	
	AskFetcher::url_get_contents($pagehref,$Source);
	$time_step_begin = microtime(true) - $time_step_begin;

	echo "over, time:$time_step_begin\n ";
	$Source = mb_convert_encoding($Source,'UTF-8','gbk');

	//抓取问题列表
	preg_match_all('/<table .+?>(.+?)<\/table>/si', $Source, $aryResult);

	$str = $aryResult[0][0];
	preg_match_all('/<a.*?class=\"listHref1\".*?href=\"([^\"]*)\" .*?>.*?<span class="cname">(.*?)<\/span>.*?<\/a>/si', $str, $aryResult, PREG_PATTERN_ORDER);

	$time_step_begin = microtime(true) - $time_step_begin;
	//列表时间过滤
	preg_match_all('/<td.*?class=\"dtime1\".*?id=\"dtime1\">.*?<\/td>/si', $str, $listtime, PREG_PATTERN_ORDER);
	$Result["listtime"] = $listtime[3];

	//抓取回复数
	$Result["href"]=$aryResult[1];

	$js="";
	foreach($Result["href"] as $val){
	  $js.=substr($val,strrpos($val,"/")+1,strpos($val,".")-strrpos($val,"/")-1)."|";
	}

	$jsurl=substr($js,0,strrpos($js,"|"));

	echo "collection reply...\n";
	$hp="http://www.120ask.com/browse/getStatusNew.asp?fs=".$jsurl."&t=0";
	AskFetcher::url_get_contents($hp,$hfs);


	$hfs =preg_replace("/,]/","]",$hfs);
	$href=json_decode($hfs,true);

	$huifushu=array();
	foreach($href as $valhf){
	  $huifushu[]=$valhf[0];
	}

	$time_step_begin = microtime(true) - $time_step_begin;
	echo "over, time:$time_step_begin \n ";
	//获取到所有的标题
	$Result["question"]=$aryResult[2];

	//抓取提问内容页
	for($j=0;$j<30;$j++){
		echo "\n@@@@@@@@@@@@@@\n";
		$ask120idarr = explode("/",$Result["href"][$j]);
		echo $ask120id = str_replace(".htm","",$ask120idarr[(count($ask120idarr) -1)]);
		//列表时间过滤
		if(strstr($Result["listtime"][$j], "天"))
		{
			echo "delete：".$Result["listtime"][$j]."\n";
			continue;
		}
		//$ask120id = 23801899;
		$dblocal->select_db('test');
		$idcount = $dblocal->select("select askid from fetch120askid where askid = '".$ask120id."'");
		if(is_array($idcount) && !empty($idcount[0]) && $idcount[0]["askid"] > 0)
		{
			echo "id is existe!\n";
			continue;
		}else{
			$dblocal->insert("fetch120askid", array("askid" => $ask120id));
		}
		//判断标题是否重复
		$tit=$Result["question"][$j];
        if(empty($tit)){
            echo "title is empty!\n";
            continue;
        }
        $title=addslashes(strip_tags($tit));
        $flag = false;
        foreach($nowords as $v)
		{
			if(strstr($title, $v))
			{
				$flag = true;
				break;
			}
		}
		if($flag){
			echo "title is undefined \n ";
			continue;
		}
		$db->select_db('9939_com_v2sns');
		//获取科室
		preg_match_all('/<a.*?href=\"([^\"]*)\" .*?class=\"listHref1\".*?>(.*?)<\/a>.*?/si', $str,$aryResult1, PREG_PATTERN_ORDER);
		$Result["keshi"]=$aryResult1[2];
		$keshi=$Result["keshi"][$j];
		$rel1=AskFetcher::keshi($keshi_array,$keshi,$db);

		//如果url地址不存在，则跳出此次循环

		echo "collection contentpage...\n ";

		//echo $quesHref="http://www.120ask.com".$Result["href"][$j];
		echo $quesHref=$Result["href"][$j];
		echo "\n";

		if(AskFetcher::url_get_contents($quesHref,$quesContent)===false){  echo "url is not existe!\n";continue;}
		$quesCont = mb_convert_encoding($quesContent,'UTF-8','gbk');


		//抓取提问时间和回复内容
		preg_match_all('/<div.*?id="askTime".*?>(.*?)<\/div>.*?(<p>.*?<\/p>)/si',$quesCont,$matches,PREG_PATTERN_ORDER);
		$asktime = strtotime(trim($matches[1][0]));
		if($asktime < $timenow)
		{
			echo "delete：".$matches[1][0]."#####\n";
			continue;
		}
		$content1=preg_replace('/<h3.*?class="renewOffer">.*?<\/h3>/',"",$matches[2][0]);
		
		$arr=explode("<br />",$content1);
		$time_step_begin = microtime(true) - $time_step_begin;
		echo "over, time:$time_step_begin \n ";

			if(AskFetcher::getContent($arr)!==false)
			 {
				//获取年龄、性别、提问内容
				$rel2=AskFetcher::getContent($arr,$db);

				$rel=array_merge($rel1,$rel2);
				/*$rel['ctime']=strtotime(substr($matches[1][0],strpos($matches[1][0],"：")+3));
				$rel['etime']=$rel['ctime'];*/
				$rel["title"]=$tit;

                //抓取回复数(包括最佳答案，会员参考意见等回复)
                $answer=AskFetcher::getAnswer($quesCont);
                if(@$answer['bestreply'] !=NULL || @$answer['bestreply'] != ''){
                    $rel['bestreply']=@$answer['bestreply'];
                }
                $rel['reply1']=@$answer['reply1'];
                $rel['reply2']=@$answer['reply2'];
                $rel['reply3']=@$answer['reply3'];

                $rel['fromsite']=$quesHref;
                if(is_array($rel)&& count($rel) != 0)
                {
                    
                    if($db->insert("wd_120answer", $rel)){
                        $last_insert_id=mysql_insert_id();
                        $succ="add successed\n";
                        echo " id:{$last_insert_id} \n$succ\n";
                        //echo "title:{$rel['title']} id:{$last_insert_id} {$succ}\n";
                    }
                    else
                    {
                        echo $succ="add error!\n";
                    }
                    $count++;
                    
                    $caiji=$count;
                    echo "collection question No: $count \n";
                    $time_step_begin=microtime(true) - $time_step_begin;

                }
				//if ($count==$getCount) {break;}

			 }
			 else
			 {
				echo "content is bad  \n ";
				continue;

			 }
			 echo "%%%%%%%%%%%%%%%%%%%\n";
			 sleep(2);
	}

	//if ($count == $getCount) {break;}
	if($i<200)
	{
		$i++;
	}else{
		break;
	}
sleep(2);
}while(1);
//统计添加数目

$ancount=3*$count;//回复数目
$caiji1=3*$caiji;
echo "已成功添加：".$count."条问答；  ".$ancount."条回复。
\n".date("Y-m-d H:i:s")."：共采集".$caiji."条问答；".$caiji1."条回复\n";//显示已加条数

?>




