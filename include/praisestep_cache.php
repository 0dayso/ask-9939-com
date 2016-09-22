<?php
/*
	定时更新问答数据 顶踩
*/

define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');
DBconnect(0);
//mark:1：问答回答；2：话题；3：话题回复；4：家园日志
//问答回答
        $sql_ask="select tuserid,puserid,tid,sum(step) as step, sum(praise) as praise ,addtime,mark from praisestep_cache where mark=1 group by tid";
        $result = mysql_query($sql_ask);
        if($result){
			while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
             $arr[]=$row;
			}
            if($arr){
               foreach ($arr as $key=>$var) {
                    $sql="update wd_answer SET praise=praise+".$var[praise]." , step=step+".$var[step]." where id=$var[tid]";
                    $isresult = mysql_query($sql);
                } 
            }          
            if($isresult){
                $move_sql = 'insert into praisestep select * from praisestep_cache where mark=1';
                $del_sql = 'delete from praisestep_cache where mark=1';
                $mv = mysql_query($move_sql);
                if($mv)
                {
                    mysql_query($del_sql);
                }
            }
        }
//话题buluo_thread
        $sql_thread="select tuserid,puserid,tid,sum(step) as step, sum(praise) as praise ,addtime,mark from praisestep_cache where mark=2 group by tid";
        $results = mysql_query($sql_thread);
        if($results){
			while ($rows=mysql_fetch_array($results,MYSQL_ASSOC)) {
             $arrs[]=$rows;
			}
            if($arrs){
                foreach ($arrs as $key=>$var) {
                    $sql="update buluo_thread SET praise=praise+".$var[praise]." , step=step+".$var[step]." where tid=$var[tid]";
                    $isresults = mysql_query($sql);
                }
            }
            if($isresults){
                $move_sql = 'insert into praisestep select * from praisestep_cache where mark=2';
                $del_sql = 'delete from praisestep_cache where mark=2';
                $mv = mysql_query($move_sql);
                if($mv)
                {
                    mysql_query($del_sql);
                }
            }
        }
//话题回复buluo_threadpost
        $sql_threadpost="select tuserid,puserid,tid,sum(step) as step, sum(praise) as praise ,addtime,mark from praisestep_cache where mark=3 group by tid";
        $resultse = mysql_query($sql_threadpost);
        if($resultse){
			while ($rowse=mysql_fetch_array($resultse,MYSQL_ASSOC)) {
             $arrse[]=$rowse;
			}
            if($arrse){
                foreach ($arrse as $k=>$v) {
                    $sql="update buluo_threadpost SET praise=praise+".$v[praise]." , step=step+".$v[step]." where pid=$v[tid]";
                    $isresultse = mysql_query($sql);
                }
            }
            if($isresultse){
                $move_sql = 'insert into praisestep select * from praisestep_cache where mark=3';
                $del_sql = 'delete from praisestep_cache where mark=3';
                $mv = mysql_query($move_sql);
                if($mv)
                {
                    mysql_query($del_sql);
                }
            }
        }
//家园日志member_blog
        $sql_threadpost="select tuserid,puserid,tid,sum(step) as step, sum(praise) as praise ,addtime,mark from praisestep_cache where mark=4 group by tid";
        $resultse = mysql_query($sql_threadpost);
        if($resultse){
			while ($rowse=mysql_fetch_array($resultse,MYSQL_ASSOC)) {
             $arry[]=$rowse;
			}
            if($arry){
                foreach ($arry as $k=>$v) {
                    $sql="update member_blog SET praise=praise+".$v[praise]." , step=step+".$v[step]." where blogid=$v[tid]";
                    $isresultsen = mysql_query($sql);
                }
            }
            if($isresultsen){
                $move_sql = 'insert into praisestep select * from praisestep_cache where mark=4';
                $del_sql = 'delete from praisestep_cache where mark=4';
                $mv = mysql_query($move_sql);
                if($mv)
                {
                    mysql_query($del_sql);
                }
            }
        }
        
?>



