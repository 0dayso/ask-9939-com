<?php
/*
	问答首页在线医生
*/

define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');
DBconnect(0);
	$arr = array();
    $ic_time=time()-3600*(24-8);
    $sql_ask="select max(w.askid),m.nickname from wd_answer AS w ,member AS m where m.uid=w.userid and m.uType=2 and w.addtime>$ic_time group by w.userid order by w.addtime desc limit 0,4";
    $result = mysql_query($sql_ask);
    if($result){
        while ($row=mysql_fetch_row($result)) {
             $arr[]=$row;
          }
     }
        $arr_name=$arr;
?>



