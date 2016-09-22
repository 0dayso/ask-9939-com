<?php
/**
  *##############################################
  * @FILE_NAME :listd.php
  *##############################################
  *
  * @author : 李军锋
  * @MailAddr : licaption@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   问答医师团模块
  *==============================================
  */

class Listd extends QModels_Ask_Table
{	
	//医师排行
	public function doc_paihang(){
		$r = $this->_db->fetchAll("select uid,nickname,username from member where 1 order by credit desc limit 0,10");
		foreach ($r as $k=>&$v)
		{
			$v[url] = 'http://home.9939.com/user/?uid='.$v[uid];			
			$v[name] = $v[nickname] ? $v[nickname] : $v[username];
		}
		return $r;
	}
	
	//医生团成员列表
	public function mem_list($class='',$area=''){
		if($class) $sql = "select a.uid,a.truename,a.doc_keshi,a.zhicheng,a.doc_hos,a.memo,b.nickname,b.username,b.pic from member_detail_2 a,member b where a.uid=b.uid and b.uType=2 and a.doc_keshi like '%".$class."%' order by b.credit desc limit 0,10";
		elseif($area) $sql = "select a.uid,a.truename,a.doc_keshi,a.zhicheng,a.doc_hos,a.memo,b.nickname,b.username,b.pic from member_detail_2 a,member b where a.uid=b.uid and b.uType=2 and a.address like '%".$area."%' order by b.credit desc limit 0,10";
		else $sql = "select a.uid,a.truename,a.doc_keshi,a.zhicheng,a.doc_hos,a.memo,b.nickname,b.username,b.pic from member_detail_2 a,member b where a.uid=b.uid and b.uType=2 order by b.credit desc limit 0,10";
		//echo $sql;
		$r = $this->_db->fetchAll($sql);
		foreach ($r as $k=>&$v)
		{
			$v[name] = $v[truename] ? $v[truename] : ($v[nickname] ? $v[nickname] : $v[username]);
		}
		return $r;		
	}
	
}
?>