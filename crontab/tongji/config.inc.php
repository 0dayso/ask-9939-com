<?php
//配置文件
if(!defined('IN_ET')) {
   exit('Access Denied');
}
$C['db_server']='192.168.220.189';
$C['db_username']='9939_com_v2sns';
$C['db_password']='snsrewou#*&#inewk';
$C['db_name']='9939_com_v2sns';
$C['db_bianma']='utf8';
$C['db_bianma']='utf8';
$C['tablewage']='wd_wage_answer';
$C['tablewageday']='wd_wage_day';
$C['limit']='200';
$C['logpath']=ET_ROOT."/log";
$C['apppath']="/home/web/htsns-9939-com/config/app.ini";//配置文件
$C['datebasic']="";
$C['qtime']=time();//获得启动时间
$C['db']="";
initconfig();
function initconfig(){
    global $C;
    $app = @file_get_contents($C['apppath']);
    $app = explode("\n",$app);
    $bool=false;
    foreach($app as $v){
        if($bool){
            $datebasic = explode("=",$v);
            if(trim($datebasic[0])=="config.datebasic"){
                $C['datebasic']=trim($datebasic[1]);
            }
            break;
        }
        if($v=="[wageconfig]"){
           $bool=true;
        }
    }
}
?>