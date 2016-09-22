<?php 
//启动文件
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include_once(ET_ROOT.'/common.inc.php');
L("程序启动");
askzuixin();
function askzuixin(){
    global $C;
	$db=D();
    $filepath=$C['path']."zuixinask2.shtml";
    $sql="select id,title FROM `wd_ask` order by ctime desc limit 0,12";
    $query=$db->Q($sql);
    $temp="";
	while ($data=$db->F($query)) {
		$temp .= '<li><a href="/id/' . $data[id] . '" target="_blank">' . cutstr($data[title], 16) .'</a></li>';
	}
 
    if(file_put_contents($filepath, $temp)){
        L("问答首页最新问题生成成功！");
    }else{
        L("问答首页最新问题生成失败！");
    }
}
?>