<?php
/*
	资讯栏目页底部右侧
*/
define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
require_once(ROOT.'/config.php');
DBconnect(0);
$name=iconv('gbk','utf-8',$_GET['q']);
$catname=iconv('gbk','utf-8',$_GET['catname']);
$n=$_GET['n'];
if($n=='wenda'){
    $sql_ask="SELECT arrchildid FROM `wd_keshi` WHERE `name` LIKE '%$name%'";
    $result = mysql_query($sql_ask);
    if($result){
        while ($row=mysql_fetch_row($result)) {
            $arr[]=$row;
        }
    }
    if(!$arr){
        $sql_ask="SELECT arrchildid FROM `wd_keshi` WHERE `name` LIKE '%$catname%'";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row=mysql_fetch_row($result)) {
                $arr[]=$row;
            }
        }
       
    }
    if($arr){
        foreach ($arr as $k=>$v){
            $arrs[]= $v[0];
        }
        $cat=implode(",",$arrs);
    }    
    if($cat){
        $sql_ask="SELECT id,title FROM `wd_ask` WHERE class_level1 in($cat) or class_level2 in($cat) or class_level3 in($cat) order by id desc limit 0,10";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row_new=mysql_fetch_row($result)) {
                $arr_new[]=$row_new;
            }
        } 
    }else{
        $sql_ask="SELECT id,title FROM `wd_ask` WHERE 1=1 order by ctime desc limit 0,10";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row_new=mysql_fetch_row($result)) {
                $arr_new[]=$row_new;
            }
        }
    }
    echo json_encode($arr_new);
}elseif($n=='huati'){
    $sql_ask="SELECT buluoid FROM `buluo` WHERE `buluoname` LIKE '%$name%'";
    $result = mysql_query($sql_ask);
    if($result){
        while ($row=mysql_fetch_row($result)) {
            $arr[]=$row;
        }
    }
    if(!$arr){
        $sql_ask="SELECT buluoid FROM `buluo` WHERE `buluoname` LIKE '%$catname%'";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row=mysql_fetch_row($result)) {
                $arr[]=$row;
            }
        } 
    }
    if($arr){
        foreach ($arr as $k=>$v){
            $arrs[]= $v[0];
        }
        $cat=implode(",",$arrs);
    }
    if($cat){
        $sql_ask="SELECT tid,subject FROM `buluo_thread` WHERE buluoid in($cat) order by dateline desc limit 0,10";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row_new=mysql_fetch_row($result)) {
                $arr_new[]=$row_new;
            }
        } 
    }else{
        $sql_ask="SELECT tid,subject FROM `buluo_thread` WHERE 1=1 order by dateline desc limit 0,10";
        $result = mysql_query($sql_ask);
        if($result){
            while ($row_new=mysql_fetch_row($result)) {
                $arr_new[]=$row_new;
            }
        }
    }
    echo json_encode($arr_new);
}else{
    echo "参数有问题";
}
?>



