<?php
session_start();
header("Content-type:text/html;charset=utf-8");
define("OPEN_DEBUG",false);
if(OPEN_DEBUG) //是否开启调试开关
{
	echo "DEBUG System:<br />";	
	error_reporting(E_ALL);
}
else 
{
	error_reporting(0);
}

define("IN_WEB",true);		//对文件的访问安全作个限制

define("ROOT",substr(dirname(__FILE__), 0, -5));	//文件的主目录
define("I_PERPAGE", 15);		//默认的每页显示数字

//echo ROOT;
require_once(ROOT.'./config.inc.php');
require_once(ROOT.'./class/functions.inc.php');

loadLib("Database");
loadLib("template");
loadLib("log");
loadLib("manager");

/** 连接数据库 **/
$db = DBconnect();

$tpl = new template(__DEFAULT_TEMPLATE);  // 模板设置

define("__SYMPTOM_URL","http://zz.9939.com/"); // 症状首页
define("__HOSPITAL_URL","http://hospital.9939.com/"); // 医院
define("__MEDICINE_URL","http://yiyao.9939.com/"); // 药品
define("__DISEASE_URL","http://jb.9939.com/"); // 疾病
define("__DOCTOR_URL","http://yisheng.9939.com/"); // 医生

define("SKIN_PATH","templates/9939/skins/default/"); // 样式、图片目录

// 医院性质 
$aHospKind = array(1=>"国营",2=>"民营",3=>"合资");
// 医院等级
$aHospLevel = array(1=>"特等医院",2=>"三级甲等",3=>"三级乙等",4=>"三级丙等",5=>"二级甲等",6=>"二级乙等",7=>"二级丙等",8=>"一级甲等",9=>"一级乙等",10=>"一级丙等",11=>"未知");
// 医院类别
$aHospSort = array(1=>"综合医院",2=>"专科医院",3=>"整形美容医院");
// 医院医保
$aHospMedicare = array(0=>"非医保",1=>"医保");

// 医生：教学职称
$aZhiCheng = array(1=>"教授",2=>"副教授",3=>"讲师",4=>"助教",5=>"未知");
$aZhiWu = array(1=>"院长",2=>"副院长",3=>"主任",4=>"副主任",5=>"未知");
$aDegree = array(1=>"博士",2=>"硕士",3=>"学士",4=>"未知");

define('NET_ROOT', str_replace("\\", '/', substr(dirname(__FILE__), 0, -5)));

?>