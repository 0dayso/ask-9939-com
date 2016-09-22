<?php
//配置文件
if(!defined('IN_ET')) {
   exit('Access Denied');
}
$C['db_server']='192.168.220.189';  //数据库地址
$C['db_username']='9939_com_v2sns'; //数据库用户名
$C['db_password']='snsrewou#*&#inewk'; //数据库密码
$C['db_name']='9939_com_v2sns';  //数据库
$C['db_bianma']='utf8';
$C['logpath']=ET_ROOT."/log";
$C['path']="/home/web/ask-9939-com/public/";//生成目录
$C['qtime']=time();//获得启动时间
$C['db']="";
?>