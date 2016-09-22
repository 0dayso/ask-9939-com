<?php
/**
  *##############################################
  * @FILE_NAME :Category.php
  *##############################################
  *
  * @author : ljf
  * @MailAddr : licaption@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   日志分类模块
  *==============================================
  */

class Category extends QModels_Ask_Table
{
	protected $_primary = 'catid';
	protected $_name="member_blog_category";	
	
	//看日志分类
	public function List_Category($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		$temp = $result->toArray();
		foreach ($temp as $k=>&$v)
		{
			$v['username'] = $this->getAuthor($v['uid']);
			$v['num'] = $this->getBlogNums($v['catid']);
		}
		return $temp;
	}
	//统计记录数
	public function GetCount($where="1") {
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `member_blog_category` where ".$where);
		return $result[0]['num'];
	}	
	
	//获取作者
	public function getAuthor($uid=0){
		 $result = $this->_db->fetchAll("select username from `member` where uid=$uid");
		 return $result[0]['username'];
	}
	
	//获取日志数
	public function getBlogNums($cid=0){
		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `member_blog` where catid=$cid");
		return $result[0]['num'];
	}
	
	//判断修改时是否导致重复
	public function jugeCat($catid=0,$cname='',$e=''){
		//$e为true时，是修改，否则是添加
		if($e) $sql = "SELECT count(*) as num FROM `member_blog_category` where catname='$cname' and catid!=$catid";
		else $sql = "SELECT count(*) as num FROM `member_blog_category` where catname='$cname'";
		//echo "SELECT count(*) as num FROM `member_blog_category` where uid=$uid and catname='$cname' and catid!=$catid";
		$result = $this->_db->fetchAll($sql);
		return $result[0]['num'];
	}

	//添加日志分类
	public function add_Category($param){
		return $this->insert($param);
		return true;
	}
		
	//修改日志分类
	public function edit_Category($id, $param) {
		$where = "catid = $id";
		$this->update($param, $where);
		return true;
	}	
	
	//删除日志分类
	public function del_Category($id) {
		if(strpos($id,',') === false){
			$num = $this->getBlogNums($id);
			if($num) return 2;//该分类有日志，不能删除
			else{
				$this->_db->query("delete from `member_blog_category` where catid=$id");
				return true;
			}
		}
		else {
			$ids = array_filter(explode(',',$id));
			foreach ($ids as $key){
				$num = $this->getBlogNums($key);
				if($num) return 2;//该分类有日志，不能删除
				else $this->_db->query("delete from `member_blog_category` where catid=$key");			
			}
			return true;
		}
	}	
}
?>