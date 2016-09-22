<?php 
//启动文件
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include_once(ET_ROOT.'/common.inc.php');
L("程序启动");
askxuansh();
function askxuansh(){
    global $C;
	$db=D();
    $filepath=$C['path']."xsask2.shtml";
    $sql="select a.id,a.title FROM `wd_ask` a inner join (select id from wd_ask WHERE point>0 order by id desc limit 0,12) b on a.id=b.id";
    $query=$db->Q($sql);
    $temp="";
	while ($data=$db->F($query)) {
		$temp .= '<li><a href="/id/' . $data[id] . '" target="_blank">' . cutstr($data[title], 16) .'</a></li>';
	}
    if(file_put_contents($filepath, $temp)){
        L("问答首页悬赏问题生成成功！");
    }else{
        L("问答首页悬赏问题生成失败！");
    }
}
?>