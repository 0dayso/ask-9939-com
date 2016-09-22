<?php
/**
 * Editplus4PHP PHP Template
 *
 * This is a Template of PHP Script for Editplus4PHP.
 *
 * @copyright HentStudio (c)2008
 * @author Leo <Zergdo@gmail.com>
 * @package Editplus4PHP
 * @version $Id: template.php, v 0.0.1 2008/09/21 12:16:23 uw Exp $
 */

class insert_ask {
    var $db;
    var $link;


    //插入数据到问答表
     public function add_ask($param) {
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', -1);
        include "include/db_mysql.class.php";
        $db = new db_mysql();
        $link=$db->connect('192.168.220.189','9939_com_v2sns','snsrewou#*&#inewk','9939_com_v2sns','','utf8');
        //$link=$db->connect('192.168.66.37','9939','9939pwd','9939_com_v2sns','','utf8');
        echo "###########".date("Y-m-d H:i:s")."#############\n";
        //提问者ID
        $askUserid=array('372181','372180','372179','372178','372177','372176','372175','372174','372173','372172','372171','372170','372169','372168','372167','372166','372165','372164','372163','372162');
        //医生ID
        $docUserid=array('200014','200015','200036','200037','200038','200039','200040','200041','200042','200043','200044','200045','200051','200052','200088','200089','200090','200091','200092','200093','200094');
        $answerResult=$db->select("select * from wd_120answer order by id  limit $param");
	if(empty($answerResult)){
		echo "数据表为空！\n";
		exit;
	}
	$delid="";
	$nowords = array("假 证", "做 假", "办 假", "卖" , "买");
        foreach($answerResult as $val){
        		$checkTitle = 0;
            $checkTitle=$db->select("select id from wd_ask where title ='".$val['title']."' ");
            if(empty($val['title']) || $checkTitle <=0)
            {
            		echo "title is dubble or empty!\n";
            		$delid .=",".$val['id'];
            		continue;
            }
            $tflag = false;
	        foreach($nowords as $v)
			{
				if(strstr($val['title'], $v))
				{
					$tflag = true;
					break;
				}
			}
			if($flag){
				echo "title is Illegal! \n ";
				continue;
			}
            $val['content'] = empty($val['content'])?$val["title"] : addslashes(strip_tags($val['content']));
            $answernum = 0;
            for($i=1;$i<=3;$i++){
               if(!empty($val['reply'.$i])){
                  $answernum++;
               }
             }
            $data=array(
                "class_level1"=>$val['depart1'],
                "class_level2"=>$val['depart2'],
                "class_level3"=>$val['depart3'],
                "title"=>$val['title'],
                "content"=>$val['content'],
                "ctime"=>time(),
                "point"=>0,
                "answernum"=>$answernum,
                "broadcast"=>0,
                "hiddenname"=>1,
                "age"=>$val['age'],
                "sexnn"=>$val['sexnn'],
                "answerUid"=>0,
                "ip"=>"120.120.".rand(0,254).".".rand(0,254),
            );

            if($val['bestreply']){
                $data["status"]=1;
            }else{
                $data["status"]=0;
            }

            $ask_id=rand(0,19);
            $data['userid']=$askUserid[$ask_id];

           if($db->insert("wd_ask",$data)){
                 $last_insert_id=mysql_insert_id();
                 $succ="success";
                 echo "title:{$rel['title']} id:{$last_insert_id} {$succ}\n";
                 $id=rand(0,19);
				for($i=1;$i<=$answernum;$i++){
                     if($val['reply'.$i]==''){
                        echo "reply is empty!\n";
                        continue;
                     }
		     		$id=rand(0,19);
                     $userAnswer['userid']=$docUserid[$id];
                     $userAnswer['content']=addslashes(strip_tags($val['reply'.$i]));
                     $userAnswer['sort']=1;
                     $userAnswer['addtime']=time();
                     $userAnswer['ip']="120.120.".rand(0,254).".".rand(0,254);
                     $userAnswer['askid']=$last_insert_id;
				   $result = $db->insert("wd_answer",$userAnswer);
				   unset($userAnswer);
                 }
           }
           unset($data);
		$delid .=",".$val['id'];
			sleep(1);
        }
        echo "delete from wd_120answer where id in(".substr_replace($delid,"",strpos($delid,','),1).")\n";
		$delResult=$db->query("delete from wd_120answer where id in(".substr_replace($delid,"",strpos($delid,','),1).")");
	 }
}

$answer=new insert_ask();
$answer->add_ask(10);

?>
