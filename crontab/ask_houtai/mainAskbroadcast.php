<?php 
//启动文件
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include_once(ET_ROOT.'/common.inc.php');
L("程序启动");
askbroadcast();
function askbroadcast(){
    global $C;
	$db=D();
    $filepath=$C['path']."broadcast2.shtml";
    $sql="select id,title FROM `wd_ask` WHERE  answernum>0 order by id desc limit 0,30";
    $query=$db->Q($sql);
    $temp="";
	while ($data=$db->F($query)) {
		$temp .= '<li><a href="/id/' . $data[id] . '">' . cutstr($data[title], 16) . '</a></li>';
	}
    if(file_put_contents($filepath, $temp)){
        L("问答首页问题广播生成成功！");
    }else{
        L("问答首页问题广播生成失败！");
    }
}
?>