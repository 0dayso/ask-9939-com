<?php
//公用方法文件
if(!defined('IN_ET')) {
   exit('Access Denied');
}
date_default_timezone_set('Asia/Shanghai');
register_shutdown_function("X");
include(ET_ROOT.'/config.inc.php');

function getDatetime(){
	global $C;
	return date("Y-m-d H:i:s",time());
}
function D(){
	global $C;
	if(!$C['db']){
		include_once(ET_ROOT."/include/db_mysql.class.php");
		$C['db'] = new dbstuff;
		$C['db']->connect($C['db_server'],$C['db_username'],$C['db_password'],$C['db_name'], 0,true);
		@mysql_query("set names ".$C['bianma']);
		return $C['db'];
	}else{
		return $C['db'];
	}
}
//程序停止后调用
function X(){
	global $C;
	$time=time()-$C['qtime'];
	L("程序停止，耗时 $time 秒");
	if($C['db']){
		$C['db']->close();
	}
}
//日志方法
function L($log){
	global $C;
	$path=$C['logpath'];
	mkdirpath($path);
    $riqi=date("Y-m-d",time());
    $riqi=explode("-",$riqi);
    $path.="/".$riqi[0];
    mkdirpath($path);
    $path.="/".$riqi[1];
    mkdirpath($path);
    $path.="/".$riqi[2].".log";
    $fd = @fopen($path, 'a');
    @fputs($fd,getDatetime());
	@fputs($fd, "\r\n" );
	@fwrite($fd,$log."\r\n");
	@fputs($fd, "\r\n" );
	@fclose($fd);
}
//创建日志目录
function mkdirpath($path){
	if (file_exists($path)||@mkdir($path, 0777)) {
        @chmod($C['logpath'], 0777);
    }else{
   		E("创建日志文件夹失败",9);
    }
}
//系统错误日志 $i=9时停止
function E($error,$i=1){
	global $C;
	$f = @fopen('error.log','a');
	$time=time();
	@fputs($f,getDatetime());
	@fputs($f, "\r\n" );
	@fputs($f,$error."\r\n");
	$arr=debug_backtrace();
	foreach ($arr as $v){
		@fputs($f,$v['file']."-".$v['line']."\r\n");
	}
	if($i==9){
		@fputs($f,"严重错误停止"."\r\n");
	}
	@fputs($f, "\r\n" );
	@fclose($f);
	if($i==9){
   		exit;
	}
}
?>