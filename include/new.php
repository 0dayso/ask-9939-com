<?php
/*
	等级更新
*/
set_time_limit(3600*24);
define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');
DBconnect(0);
$i = 0;
        $sql_ask="select * from member";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
               $sql_asks="select groupname from member_group where creditlower<=$row[credit] and uType=$row[uType] ORDER BY gid desc limit 1";
               if($row['uType']!=2){
                    $sql_asks="select groupname from member_group where creditlower<=$row[credit] and uType=1 ORDER BY gid desc limit 1";
               }
               $results = mysql_query($sql_asks);
               if($results){
                $rows=mysql_fetch_array($results,MYSQL_ASSOC);
                    $sql="update member SET groupname='".$rows[groupname]."' where uid=$row[uid]";
                    $isresult = mysql_query($sql);
                    if(!$isresult){
                        error_log($row['uid'].',', 3, 'up.log');
                    }else{
                        echo $i;
                        echo '<br>';
                    }
               }
              $i++;    
            }
        }
?>



