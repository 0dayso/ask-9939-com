<?php
/**
 * rank.php （前台）聚医堂导航模块
 * @author xiongzhixin (xzx747@sohu.com) 2009-05-11
 */
define("OPEN_DEBUG",true);


$a = array(0=>"article",1=>"expert",2=>"resource",3=>"flower",4=>"experience",5=>"visit",6=>"reply");
$aV = array(0=>"博文排行",1=>"找专家",2=>"医学资源",3=>"鲜花排行",4=>"经验值排行",5=>"访问量排行",6=>"问题回复排行");    

$f = intval($_GET['f']);
$sTpl = "doctor_".$a[$f].".tpl.htm";

require_once("../class/common.inc.php");
$db = DBconnect(1);//home

//周排行
$wblog = $db->getRecordSet("select blogid,uid,subject,hits_time from uchome_blog order by viewnum_w desc limit 0,50");
foreach($wblog as &$k)
{
	$r = $db->getRecordSet("select username from uchome_member where uid=".$k['uid'],1);
	$k['username'] = $r['username'];
	$k['u_url'] = "http://home.9939.com/space.php?uid=".$k['uid'];
	$k['b_url'] = "http://home.9939.com/space.php?uid=".$k['uid']."&do=blog&id".$k['blogid'];
	$k['hits_time'] = date('m-d H:i',$k['hits_time']);
}
$tpl->assign("wblog",$wblog);

//月排行
$mblog = $db->getRecordSet("select blogid,uid,subject,hits_time from uchome_blog order by viewnum_m desc limit 0,50");
foreach($mblog as &$k)
{
	$r = $db->getRecordSet("select username from uchome_member where uid=".$k['uid'],1);
	$k['username'] = $r['username'];
	$k['u_url'] = "http://home.9939.com/space.php?uid=".$k['uid'];
	$k['b_url'] = "http://home.9939.com/space.php?uid=".$k['uid']."&do=blog&id".$k['blogid'];
	$k['hits_time'] = date('m-d H:i',$k['hits_time']);
}
$tpl->assign("mblog",$mblog);

//总排行
$zblog = $db->getRecordSet("select blogid,uid,subject,hits_time from uchome_blog order by viewnum desc limit 0,50");
foreach($zblog as &$k)
{
	$r = $db->getRecordSet("select username from uchome_member where uid=".$k['uid'],1);
	$k['username'] = $r['username'];
	$k['u_url'] = "http://home.9939.com/space.php?uid=".$k['uid'];
	$k['b_url'] = "http://home.9939.com/space.php?uid=".$k['uid']."&do=blog&id".$k['blogid'];
	$k['hits_time'] = date('m-d H:i',$k['hits_time']);
}
$tpl->assign("zblog",$zblog);

//右侧
//博文排行榜
$hotblog = $db->getRecordSet("select count(*) as num,uid from uchome_blog group by uid order by num desc limit 0,10");
foreach($hotblog as $k=>&$v)
{
	$r = $db->getRecordSet("select username from uchome_member where uid=".$v['uid'],1);
	$v['name'] = $r['username'];
}
$tpl->assign("hotblog",$hotblog);

//热门博客
$hotdoc = $db->getRecordSet("select uid,username,name from uchome_space order by viewnum desc limit 0,24");
$tpl->assign("hotdoc",$hotdoc);

//医学资源
$docres = $db->getRecordSet("select id,title,filetype,uid,filename,type,disease from uchome_resource order by filename desc,download desc,filename desc limit 0,50");
foreach($docres as $k=>&$v)
{
	$v['file'] = $v['title'].$v['filetype'];
	$v['time'] = date('Y-m-d H:i',$v['filename']);
	$v['url'] = "http://home.9939.com/space.php?uid=".$v['uid']."&do=resource&act=download&id=".$v['id'];
}
$tpl->assign("docres",$docres);

//博客总数
$r = $db->getRecordSet("SELECT count(distinct uid) as num FROM `uchome_blog` ",1);
$bknum = $r['num'];
$tpl->assign("bknum",$bknum);

//博文总数
$r = $db->getRecordSet("SELECT count(*) as num FROM `uchome_blog` ",1);
$bwnum = $r['num'];
$tpl->assign("bwnum",$bwnum);

//资源总数
$r = $db->getRecordSet("SELECT count(*) as num FROM `uchome_resource` ",1);
$zynum = $r['num'];
$tpl->assign("zynum",$zynum);

//总访问量
$r = $db->getRecordSet("SELECT viewnum FROM `uchome_space` ");
$totalnum = '';
foreach($r as $k=>$v)
{
	$totalnum += $v['viewnum'];
}
$tpl->assign("totalnum",$totalnum);

//总访问量排行
$r = $db->getRecordSet("SELECT viewnum FROM `uchome_space` ");
$totalnumph = array();
foreach($r as $k=>$v)
{
	$totalnum += $v['viewnum'];
}
$tpl->assign("totalnumph",$totalnumph);

//周访问量排行
$r = $db->getRecordSet("SELECT uid,username,viewnum_w FROM `uchome_space` order by viewnum_w desc");
$weeknumph = array();
foreach($r as $k=>$v)
{
	$totalnum += $v['viewnum'];
}
$tpl->assign("weeknumph",$weeknumph);



//医生空间排行
//周排行0-25
$dwblog = $db->getRecordSet("select uid,username,truename,viewnum_w,zhicheng from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by a.viewnum_w desc limit 0,50");
foreach($dwblog as $k=>&$v)
{
	$v['url'] = "http://home.9939.com/space.php?uid=$v[uid]";
}
$tpl->assign("dwblog",$dwblog);

//总排行0-25
$dzblog = $db->getRecordSet("select uid,username,truename,viewnum,zhicheng from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid  order by a.viewnum desc limit 0,50");
foreach($dzblog as $k=>&$v)
{
	$v['url'] = "http://home.9939.com/space.php?uid=$v[uid]";
}
$tpl->assign("dzblog",$dzblog);

//周排行25-50
$dwblog1 = $db->getRecordSet("select uid,username,truename,viewnum_w,zhicheng from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by a.viewnum_w desc limit 50,50");
foreach($dwblog1 as $k=>&$v)
{
	$v['url'] = "http://home.9939.com/space.php?uid=$v[uid]";
}
$tpl->assign("dwblog1",$dwblog1);

//总排行25-50
$dzblog1 = $db->getRecordSet("select uid,username,truename,viewnum,zhicheng from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by a.viewnum desc limit 50,50");
foreach($dzblog1 as $k=>&$v)
{
	$v['url'] = "http://home.9939.com/space.php?uid=$v[uid]";
}
$tpl->assign("dzblog1",$dzblog1);

$jyzph = $db->getRecordSet("select uid,username,truename,flower as credit,zhicheng,doc_hos from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by flower desc limit 0,50");
//$jyzph = $db->getRecordSet("select uid,username,truename,credit,zhicheng,doc_hos from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by credit desc limit 0,50");
foreach($jyzph as $k=>&$v)
{
	$v['url'] = "http://home.9939.com/space.php?uid=$v[uid]";
}
$tpl->assign("jyzph",$jyzph);

//$jyzph1 = $db->getRecordSet("select uid,username,truename,credit,zhicheng,doc_hos from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by credit desc limit 50,50");
$jyzph1 = $db->getRecordSet("select uid,username,truename,flower as credit,zhicheng,doc_hos from uchome_space a,phpcms_member_detail_d b where a.uid=b.userid order by flower desc limit 50,50");
foreach($jyzph1 as $k=>&$v)
{
	$v['url'] = "http://home.9939.com/space.php?uid=$v[uid]";
}
$tpl->assign("jyzph1",$jyzph1);

//在线医生
$online = $db->getRecordSet("select a.uid,a.username,a.nicheng,c.lastlogin from uchome_member a,uchome_session b,uchome_space c where a.uid=b.uid and a.uid=c.uid and a.uType=2 order by c.flower desc limit 0,20");
foreach($online as $k=>&$v)
{
	$v['lastlogin'] = date('Y-m-d H:i',$v['lastlogin']);
}
$tpl->assign("online", $online);

//所以医生
$allmem = $db->getRecordSet("select a.uid,a.nicheng,a.username,b.lastlogin from uchome_member a,uchome_space b where a.uid=b.uid and a.uType=2 order by b.flower desc limit 0,20");
foreach($allmem as $k=>&$v)
{
	$v['lastlogin'] = date('Y-m-d H:i',$v['lastlogin']);
}
$tpl->assign("allmem", $allmem);



$db = DBconnect(0);//ask

//广告
$ads = $db->getRecordSet("select name,url,image from ads where pos=1 order by id desc limit 0,1",1);
$ads['name'] = substr($ads['name'],0,48);
$tpl->assign("ads",$ads);

//公告
$notice = $db->getRecordSet("select name,url from notice  order by id desc limit 0,6");
$tpl->assign("notice",$notice);


$user = $db->getRecordSet("select a.userid,a.username,b.truename from phpcms_member a,phpcms_member_detail_d b where a.userid=b.userid and a.modelid=12 limit 0,20");

//医生总数
$r = $db->getRecordSet("select count(*) as num from phpcms_member a,phpcms_member_detail_d b where a.userid=b.userid and a.modelid=12",1);
$docnum = $r['num'];
$tpl->assign("docnum", $docnum);

//问吧回复排行

$reply = $db->getRecordSet("select count(*) as num,username,a.userid,coalesce(zhicheng,'无') as zhicheng from phpcms_ask_posts a left join phpcms_member_detail_d b on a.isask=0 and a.userid=b.userid group by a.userid order by num desc limit 0,50");
foreach($reply as &$k)
{
	$k['url'] = "http://home.9939.com/space.php?uid=".$k['userid'];
}
$tpl->assign("reply", $reply);

$reply1 = $db->getRecordSet("select count(*) as num,username,a.userid,coalesce(zhicheng,'无') as zhicheng from phpcms_ask_posts a left join phpcms_member_detail_d b on a.isask=0 and a.userid=b.userid group by a.userid order by num desc limit 50,50");
foreach($reply1 as &$k)
{
	$k['url'] = "http://home.9939.com/space.php?uid=".$k['userid'];
}

$tpl->assign("reply1", $reply1);

/*//在线医生
foreach($online as $k=>&$v)
{
	$r = $db->getRecordSet("select truename,zhicheng,doc_hos,doc_keshi,disease,address from phpcms_member a,phpcms_member_detail_d b where a.userid=b.userid and a.uType=2 and a.userid=".$v['uid'],1);
	$v['lastactivity'] = date('Y-m-d H:i:s',$v['lastactivity']);
	$v['truename'] = $r['truename'];
	$v['zhicheng'] = $r['zhicheng'];
	$v['doc_hos'] = $r['doc_hos'];
	$v['doc_keshi'] = $r['doc_keshi'];
	$v['disease'] = $r['disease'];
	$v['address'] = $r['address'];
}
$tpl->assign("online", $online);*/

/*//所有医生
foreach($allmem as $k=>&$v)
{
	$r = $db->getRecordSet("select truename,zhicheng,doc_hos,doc_keshi,disease,address from phpcms_member a,phpcms_member_detail_d b where a.userid=b.userid and a.uType=2 and a.userid=".$v['uid'],1);
	$v['lastlogin'] = date('Y-m-d H:i:s',$v['lastlogin']);
	$v['truename'] = $r['truename'];
	$v['zhicheng'] = $r['zhicheng'];
	$v['doc_hos'] = $r['doc_hos'];
	$v['doc_keshi'] = $r['doc_keshi'];
	$v['address'] = $r['address'];
}*/


//ucenter
$db = DBconnect(2);

foreach($user as &$k)
{
	$r = $db->getRecordSet("select uid,regdate from uc_members where username='".$k['username']."'",1);
	$k['regdate'] = $r['regdate'];
	$k['url'] = "http://home.9939.com/space.php?uid=".$r['uid'];
}
foreach($user as $k=>$v)
{
	$n[$k] = $v['regdate'];
}
array_multisort($n, SORT_DESC, $user);
$tpl->assign("user",$user);


$tpl->assign("sPageTitle", $aV[$f]."_".__WEBNAME1);
$tpl->assign("sKeywords", "");
$tpl->assign("sDescription", "");
$tpl->assign("sFlag", $a[$f]);


$tpl->display($sTpl);
?>