<?php 
//启动文件
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include(ET_ROOT.'/common.inc.php');
//var_dump($C);
L("程序启动");
//E("aaa");
//insceshishuju();
//exit;
 if($C['datebasic']==""){
	E("读取结算日错误",9);
}

$C['qtimeymd']=date("Y-m-d",$C['qtime']-86400);//结算日期
$C['lingdian']=strtotime(date("Y-m-d",$C['qtime'])." 00:00:00");//获得零点时间戳
$C['yuechu']=strtotime(date("Y-m",$C['qtime'])."-{$C['datebasic']} 00:00:00");//获得结算点时间戳
if($C['yuechu']>$C['qtime']){//判断如果当前时间小于当月结算点，取上个月的
    $C['yuechu']=strtotime(date("Y-m-d H:i:s",$C['yuechu'])." -1 month");
}
$C['tablewageold']=jiancebiao();//获得旧数存储表
$C['count']=getCount();//获得要操作的总数
$C['config']=getConfig();//获得工资参数
if(check()===true)qiDong();//验证参数合法开始启动

//统计方法
function qiDong(){
	global $C;
	$db=D();
	$zongyeshu=(int)($C['count']/$C['limit']);
	if($C['count']%$C['limit']!=0){
		$zongyeshu++;
	}
	for ($x=0;$x<$zongyeshu;$x++){
		$ksishi=$x*$C['limit'];
		L("操作".$sql);
		$sql="select * from {$C['tablewage']} where ctime<{$C['lingdian']} ORDER BY id ASC limit $ksishi,{$C['limit']}";
		$query=$db->Q($sql);
		$arrgongzi=array();
		$kaishiid=0;
		$jishuid=0;
		while ($data=$db->F($query)) {
			if($kaishiid==0){
				$kaishiid=$data['id'];
			}
			$jishuid=$data['id'];
			$arrgongzi[$data['uid']][$data['type']]++;
		}
		L("开始更新数据");
		foreach ($arrgongzi as $k=>$v){
			if(updateWageDay($k,$v)!=true){
				L("更新统计数据错误");
				E("更新统计数据错误",9);
			}
		}
	}
	L("开始移动数据");
	$sql="insert into {$C['tablewageold']} SELECT * FROM `{$C['tablewage']}` where ctime<{$C['lingdian']}";
	$db->Q($sql);
	if($db->row_count()>0){
	    L("完成".$sql);
		L("开始删除数据");
		$sql="delete from `{$C['tablewage']}` where ctime<{$C['lingdian']}";
		$db->Q($sql);
		if($db->row_count()>0){
			L("完成".$sql);
		}else{
			L("删除数据发生错误");
			E("删除数据发生错误",9);
		}
	}else{
		L("移动数据发生错误");
		E("移动数据发生错误",9);
	}
}
//更新日工资
function updateWageDay($uid,$arr){
	global $C;
	$db=D();
	if($arr['1']>0){
		$arr['wage']=((float)$arr['1'])*$C['config']['wage'];
	}else{
		$arr['wage']="0.00";
		$arr['1']="0";
	}
	if($arr['2']>0){
		$arr['fine']=((float)$arr['2'])*$C['config']['wage']*$C['config']['fine'];
	}else{
		$arr['fine']="0.00";
		$arr['2']="0";
	}
	$sql="update {$C['tablewageday']} set wage=wage+".($arr['wage']-$arr['fine']).",asknum=asknum+{$arr['1']},tousunum=tousunum+{$arr['2']} where uid={$uid} AND datebasic='{$C['qtimeymd']}'";
	$db->Q($sql);
	if($db->row_count()>0){
		L($sql."执行成功");
		return true;
	}else{
		$sql="INSERT INTO `{$C['tablewageday']}` (`uid` ,`wage` ,`asknum` ,`tousunum`,`datebasic`,ctime)VALUES ( '$uid', '".($arr['wage']-$arr['fine'])."', '{$arr['1']}', '{$arr['2']}','{$C['qtimeymd']}',".time().")";
		if($db->Q($sql)){
			L($sql."执行成功");
			return true;
		}else{
			return false;
		}
	}
}
//insceshishuju();
//验证启动条件是否齐全
function check(){
	global $C;
   
	if($C['count']<1){
		L("没有需要操作的数据停止");
		return false;
	}
	if($C['config']['wage']==""){
		L("配置参数不全工资系数为空");
		return false;
	}
	if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$C['config']['wage'])){
		L("配置参数工资系数不合法");
		return false;
	}
	if($C['config']['fine']==""){
		L("配置参数不全处罚系数为空");
		return false;
	}
	if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$C['config']['fine'])){
		L("配置参数处罚系数不合法");
		return false;
	}
	return true;
}
//获得配置文件
function getConfig(){
	global $C;
	$db=D();
	//读取上月的
	$query=$db->Q("select * from wd_wage_config where ctime<{$C['yuechu']} ORDER BY ctime DESC limit 0,1");
	if ($data=$db->F($query)) {
		$data['wagecfg']=unserialize($data['wagecfg']);
		return $data['wagecfg'];
	}else{
		//读取最新的
		$query=$db->Q("select * from wd_wage_config ORDER BY ctime DESC limit 0,1");
		if ($data=$db->F($query)) {
			$data['wagecfg']=unserialize($data['wagecfg']);
			return $data['wagecfg'];
		}else{
			return false;
		}
	}
}
//返回要操作的数量
function getCount(){
	global $C;
	$db=D();
	$query=$db->Q("select count(*) as zongshu from {$C['tablewage']} where ctime<{$C['lingdian']}");
	if ($data=$db->F($query)) {
		return 0+$data['zongshu'];
	}else{
		return 0;
	}
}
/*
检测表是否存在
*/
function jiancebiao(){
	global $C;
	$db=D();
	$riqi=date("Y-m-d",time());
    $riqi=explode("-",$riqi);
	$oldtable=$C['tablewage']."_".$riqi[0];
	$query=$db->Q("select * from information_schema.TABLES where TABLE_NAME='{$oldtable}'");
	if ($data=$db->F($query)) {
		return $oldtable;
	}else{
		$sqljb="CREATE TABLE `".$oldtable."` (
		  `id` int(11) NOT NULL,
		  `uid` int(11) NOT NULL COMMENT '用户id',
		  `askid` int(11) NOT NULL COMMENT '问题id',
		  `type` tinyint(1) NOT NULL default '1' COMMENT '是否被投诉 1表示正常，2表示投诉',
		  `ctime` int(10) NOT NULL COMMENT '数据库插入时间',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回答表' AUTO_INCREMENT=1";
		$db->Q($sqljb);
		$query=$db->Q("select * from information_schema.TABLES where TABLE_NAME='{$oldtable}'");
		if ($data=$db->F($query)) {
			return $oldtable;
		}else{
			E("创建{$C['tablewage']}表失败",9);
		}
	}
}
//创建测试数据

function insceshishuju(){
	global $C;
	$db=D();
	$tid=1;
	
	$query=$db->Q("SELECT * FROM `member_detail_2` WHERE trueName!='' limit 0,100");
	while  ($data=$db->F($query)) {
		$x=$data['uid'];
		$shuliang= rand(1,100);
		for ($x1=0;$x1<$shuliang;$x1++){
			$bool= rand(1,2);
			$db->Q("INSERT INTO `9939_com_v2sns`.`wd_wage_answer` (`uid` ,`askid` ,`type` ,`ctime`)VALUES ( '$x', '$tid', '$bool', '".time()."');");
			echo $tid."<br>\r\n";
			$tid++;
		}
	}
}
?>