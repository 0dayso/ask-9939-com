<?php 
//启动文件
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include(ET_ROOT.'/common.inc.php');
L("程序启动");
 //if($C['datebasic']==""){
//	E("读取结算日错误",9);
//}
$C['yuechu']=strtotime(date("Y-m-d",$C['qtime'])." 00:00:00");//获得结算点时间戳
if($C['yuechu']>$C['qtime']){//判断如果当前时间小于当月结算点，取上个月的
    $C['yuechu']=strtotime(date("Y-m-d H:i:s",$C['yuechu'])." -1 month");
}
$C['config']=getConfig();//获得工资参数
if(check()===true)qiDong();//验证参数合法开始启动

//统计方法
function qiDong(){
	global $C;
	$db=D();
    $sql="select count(*) as count from member where uType=2 AND isVip='1'";//获得医生总数
    $yishengz=getCount($sql);
	$zongyeshu=getYeShu($yishengz);
	for ($x=0;$x<$zongyeshu;$x++){
		$ksishi=$x*$C['limit'];
		$sql="select uid from member where uType=2 AND isVip='1' ORDER BY uid ASC limit $ksishi,{$C['limit']}";
		L("操作".$sql);
		$query=$db->Q($sql);
        while ($data=$db->F($query)) {
            L("开始更新".$data['uid']);
            gengXinYongHu($data['uid']);
            L("更新完毕".$data['uid']);
        }
	}

}
//更新用户的数据
function gengXinYongHu($uid){
	global $C;
	$db=D();
    //SELECT * FROM `wd_answer` WHERE userid=1
    $arrzongshu=array();
    $sql="select count(*) as count from wd_answer where userid={$uid}";//获得医生总数
    $anzongshu=getCount($sql);
    $zongyeshu=getYeShu($anzongshu);
	for ($x=0;$x<$zongyeshu;$x++){
		$ksishi=$x*$C['limit'];
        $sql="select id,addtime from wd_answer where userid={$uid} limit $ksishi,{$C['limit']}";
		L("操作".$sql);
		$query=$db->Q($sql);
        while ($data=$db->F($query)) {
            $datebasic=date("Y-m-d",$data['addtime']);
            $arrzongshu[$datebasic]['1']++;
        }
    }
    //SELECT * FROM `wd_tousu` as a,wd_answer as b WHERE a.content_type=2 and a.content_id=b.id
    $sql="select count(*) as count from wd_tousu as a,wd_answer as b WHERE a.content_type=2 and b.userid={$uid} and a.content_id=b.id ";//获得医生总数
    $anzongshu=getCount($sql);
    $zongyeshu=getYeShu($anzongshu);
	for ($x=0;$x<$zongyeshu;$x++){
		$ksishi=$x*$C['limit'];
        $sql="select a.time as addtime from  wd_tousu as a,wd_answer as b WHERE a.content_type=2 and b.userid={$uid} and a.content_id=b.id limit $ksishi,{$C['limit']}";
		L("操作".$sql);
		$query=$db->Q($sql);
        while ($data=$db->F($query)) {
            $datebasic=date("Y-m-d",$data['addtime']);
            $arrzongshu[$datebasic]['2']++;
        }
    }
    foreach($arrzongshu as $k=>$v){
        updateWageDay($uid,$k,$v);
    }
}

//更新日工资
function updateWageDay($uid,$datebasic,$arr){
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
	$sql="update {$C['tablewageday']} set wage=wage+".($arr['wage']-$arr['fine']).",asknum=asknum+{$arr['1']},tousunum=tousunum+{$arr['2']} where uid={$uid} AND datebasic='{$datebasic}'";
	$db->Q($sql);
	if($db->row_count()>0){
		L($sql."执行成功");
		return true;
	}else{
		$sql="INSERT INTO `{$C['tablewageday']}` (`uid` ,`wage` ,`asknum` ,`tousunum`,`datebasic`,ctime)VALUES ( '$uid', '".($arr['wage']-$arr['fine'])."', '{$arr['1']}', '{$arr['2']}','{$datebasic}',".time().")";
		if($db->Q($sql)){
			L($sql."执行成功");
			return true;
		}else{
			return false;
		}
	}
}
function getYeShu($yishengz){
	global $C;
	$db=D();
    $zongyeshu=(int)($yishengz/$C['limit']);
	if($yishengz%$C['limit']!=0){
		$zongyeshu++;
	}
    return $zongyeshu;
}
//查询总数
function getCount($sql){
	global $C;
	$db=D();
    $query=$db->Q($sql);
	if ($data=$db->F($query)) {
		return 0+$data['count'];
	}else{
		return 0;
	}
}
//insceshishuju();
//验证启动条件是否齐全
function check(){
	global $C;
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

?>