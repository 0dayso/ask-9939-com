<?php

//********获取网页内容*******

class AskFetcher
{


	static function url_get_contents($url, &$contents)
	{
		$header = array(
			'User-Agent: Mozilla/5.0 (X11; U; Linux i686; zh-CN; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.04 (lucid) Firefox/3.6.13',
			'Accept: text ml,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: zh-cn,zh;q=0.5',
			'Accept-Encoding: gzip,deflate',
			'Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7',
			'Keep-Alive: 115',
			'Connection: keep-alive',
			'Cookie: ip_to_city=IP+Address+Error'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_ENCODING, "deflate");
		$contents = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		if ($info['http_code'] != '200') {
			//var_dump($url);
			//sleep(4);
			return FALSE;
		}
		curl_close($ch);
		return TRUE;
	}

	//获取2条回复
	static function getAnswer($htmlContent,$docUserid){		
			preg_match_all('/<div.*?class="huifuTime">(.*?)<a.*?>.*?<\/a>.*?<\/div>.*?<p.*?">(.*?)<\/p>.*?/si',$htmlContent,$matches,PREG_PATTERN_ORDER);			
			$answer="";	
			if($matches[1][1]==''){return;}
			if($matches[2][1]==''){return;}
			$huifu1=strtotime(substr($matches[1][0],strpos($matches[1][0],"：")+3));
			$huifu2=strtotime(substr($matches[1][1],strpos($matches[1][1],"：")+3));			
			$content1=addslashes($matches[2][0]);
			$content2=addslashes($matches[2][1]);
			if($content1!=''){
				$id=rand(0,19);
				$answer[0]['userid']=$docUserid[$id];
				//$answer[0]['askid']=$insertId;		
				$answer[0]['sort']=1;
				$answer[0]['addtime']=$huifu1;
				$answer[0]['content']=$content1;
				$answer[0]['ip']="120.120.".rand(0,254).".".rand(0,254);        
					if(($content2)!=''){
						$id=rand(0,19);
						$answer[1]['userid']=$docUserid[$id];
						//$answer[1]['askid']=$insertId;
						$answer[1]['sort']=2;
						$answer[1]['addtime']=$huifu2;
						$answer[1]['content']=$content2;
						$answer[1]['ip']="120.120.".rand(0,254).".".rand(0,254);
					}
			 }

			 return $answer;
			
			

	}

	//查询科室
	static function keshi($keshi_array,$keshi,$db){   
		  
			$cunzai=0;
			foreach($keshi_array as $val){
			  if(in_array($keshi,$val)){
				$rel['class_level1']=$val['class_level1'];
				$rel['class_level2']=$val['class_level2'];
				$rel['class_level3']=$val['class_level3'];
				$rel['classid']=$val['id'];
				$cunzai=1;
			  }
			}
			if($cunzai!=1){
				if(strrpos($keshi,"科")){
				  $ke=substr($keshi,0,strrpos($keshi,"科"));
				}else{
				  $ke=$keshi;
				}
				$ke1=$ke."科";
				$sour=$db->select("select id,class_level1,class_level2,class_level3,name from wd_keshi where name='$ke' or name='$ke1'");				
				if(!empty($sour)){
					$rel['class_level1']=$sour[0]['class_level1'];
					$rel['class_level2']=$sour[0]['class_level2'];
					$rel['class_level3']=$sour[0]['class_level3'];
					$rel['classid']=$sour[0]['id'];
				}else{
					$rel['class_level1']=537;
					$rel['class_level2']=0;
					$rel['class_level3']=0;
					$rel['classid']=537;
				}
			}
			return $rel;
	 

	}
	//获取提问内容：年龄，性别，提问问题
	static function getContent($arr,$db,$userid_start){
		$rel['content']="";
		for($h=3;$h<count($arr);$h++){
		 $rel['content'].=$arr[$h];
		}
        $rel['content']=preg_replace('/曾经治疗情况和效果/',"当前健康困惑或病情描述",$rel['content']);
		$rel['content']=preg_replace('/想得到怎样的帮助/',"治疗情况及预期治疗效果",$rel['content']);
		$rel['content']=preg_replace('/<strong>病情描述:<\/strong>/',"",$rel['content']);
		$rel['content']=addslashes($rel['content']);
		//限制条件：不能有重复标题;不存在图片;必须有年龄、性别和提问问题
		if(strpos($rel['content'],"src")===false&&
			count($arr)>3&&
			strpos($arr[1],"年龄")!=false&&
			strpos($arr[2],"性别")!=false)

			{  	
				$agestr=substr($arr[1],strpos($arr[1],":")+1);		
				$agestr=strip_tags($agestr);				
				if(is_numeric(trim($agestr)))
					{
					  $rel['age']=$agestr;
					}else{
					  $rel['age']="";
					}
				$sex=substr($arr[2],strpos($arr[2],":")+1);
				$sex=strip_tags($sex);
				$rel['sexnn']=$sex;				
				switch(trim($rel['sexnn'])){
				case "男":
					$rel['sexnn']=1;
				break;
				case "女":
					$rel['sexnn']=2;
				break;
				default:
					$rel['sexnn']=0;
				break;	  
				}
				//$rel['title']=$tit;
				$rel['status']=1;
				$rel['point']=0;
				$rel['answernum']=2;
				$rel['broadcast']=0;			 
				$rel['hiddenname']=1;
				$rel['answerUid']=0;
				$rel['userid']=$userid_start;
				$rel['ip']="120.120.".rand(0,254).".".rand(0,254);

			 return $rel;
					  
		}else{

		  return false;

		}
	 

	}
}
