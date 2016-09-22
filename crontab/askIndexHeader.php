<?php
/**
 * @desc 生成问答头部 今日已解决的问答数量
 * @author PHJ 
 * @date 2014-11-19
 */
$iStartTime = date("Y-m-d");
$isStopTime = date("Y-m-d",strtotime("-1 day"));
$data=array("11","13","15","16","18");
if(file_exists("/home/web/ask-9939-com/data/askIndexHeader".$iStartTime.".txt")){
    $nums=file_get_contents("/home/web/ask-9939-com/data/askIndexHeader".$iStartTime.".txt");
    $nums+=$data[rand(0,4)];
    file_put_contents("/home/web/ask-9939-com/data/askIndexHeader".$iStartTime.".txt",$nums);
}else{
    $nums=$data[rand(0,4)];
    file_put_contents("/home/web/ask-9939-com/data/askIndexHeader".$iStartTime.".txt",$nums);
}
if(file_exists("/home/web/ask-9939-com/data/askIndexHeader".$isStopTime.".txt")){
    unlink("/home/web/ask-9939-com/data/askIndexHeader".$isStopTime.".txt");
}
?>