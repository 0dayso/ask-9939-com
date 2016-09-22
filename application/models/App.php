<?php
/**
  *##############################################
  * @FILE_NAME :App.php
  *##############################################
  *
  * @author : zhaonan
  * @MailAddr : 360807702@qq.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   APP模块
  *==============================================
  */

class App extends QModels_Ask_Table
{
	var $db_jb		= null;
	
    public function init(){
        parent::init();
        $this->db_www = $this->db_v2_read;
        $this->db_jb =  $this->db_dzjb_read;
	}

	/**
	 * 科室列表
	 * @param null
	 * @return array
	 */
	public function getKeshi() {
		$sql	= "select id,`name`,child,arrchildid,description,keywords from `wd_keshi`";
	    $result = $this->_db->fetchAll($sql);
		//print_r($result);
		return $result;
	}

	/**
	 * 科室问题列表
	 * @param 条件
	 * @return array
	 */
	public function getKeshiList($classid = 0,$lastId = 0) {
		if($lastId == 0){
			$sql	= "select id,userid,content,ctime from wd_ask where classid = '{$classid}' and `status` = 1 order by id desc limit 10";
		}else{
			$sql	= "select id,userid,content,ctime from wd_ask where classid = '{$classid}' and id < '{$lastId}' and `status` = 1 order by id desc limit 10";
		}
	    $result = $this->_db->fetchAll($sql);
		//print_r($result);
		return $result;
	}

	/**
	 * 医生基本资料
	 * @param 条件
	 * @return array
	 */
	public function getdoctorId($uid = 0){
		$sql	= "select m.uid,d.truename,m.lastpost,m.pic,d.doc_hos from member m , member_detail_2 d where m.uid = '{$uid}' and m.uid = d.uid limit 1";
		//echo $sql;exit;
	    $result = $this->_db->fetchRow($sql);
		//print_r($result);
		return $result;
	}

	/**
	 * 常见疾病
	 */
	public function getChangJianJB(){
		$sql	= "select k.id,k.`name`,k.description from wd_keshi as k,wd_keshi_fushu as f where k.id = f.keshiid and f.pid = 1 and f.type = 1";
	    $result = $this->_db->fetchAll($sql);
		//print_r($result);
		return $result;
	}
	
	/**
	 * 获取回答内容
	 * @param 条件
	 * @return  array
	 */
	public function getAnswerList($askid = ''){
		$sql	= "select id,askid,userid,content,addtime from wd_answer where askid = '{$askid}' order by sort asc";
		$result	= $this->_db->fetchAll($sql);
		return $result;
	}
	
	/**
	 * 获取问题信息
	 * @param 条件
	 * @return  array
	 */
	public function getAskInfo($ask_id = ''){
		$sql	= "select id,classid,class_level1,class_level2,class_level3,title,content,ctime,userid,isReal,age,sexnn from wd_ask where id='{$ask_id}' limit 1";
		$result = $this->_db->fetchAll($sql);
		return $result;
	}	

	/**
	 * 用户问诊信息
	 */
	public function getUserAskList($user_id = '',$limit = 0 ,$offset = 10){
		$sql	= "select id,classid,class_level1,class_level2,class_level3,title,content,ctime,userid,isReal,age,sexnn from wd_ask where userid='{$user_id}' limit {$limit},{$offset}";
		$result = $this->_db->fetchAll($sql);
		return $result;		
	}
	
	/**
	 * 用户问诊信息总数
	 */
	public function getUserAskCount($user_id = ''){
		$sql	= "select count(1) as num from wd_ask where userid='{$user_id}'";
		$result = $this->_db->fetchRow($sql);
		return $result;		
	}
	
	/**
	 * 判断用户是否存在
	 */
	public function existsUsername($username = ''){
		$sql	= "select uid from member where username = '{$username}' limit 1";
		$result	= $this->_db->fetchRow($sql);
		return $result;
	}

	/**
	 * 认证用户
	 */
	public function authUsername($username = '',$password = ''){
		$sql	= "select uid from member where username = '{$username}' and password = '{$password}' limit 1";
		$result	= $this->_db->fetchRow($sql);
		return $result;
	}

	
	/**
	 * 疾病库列表
	 */
	public function getDiseaseList($field = '*',$where = "",$order = ""){
		$sql	= "select $field from 9939_dzjb a ,9939_disease_content b $where $order";
		$result = $this->db_jb->fetchAll($sql);
		return $result;
	}
	
	/**
	 * 
	 * 判断用户是否发过相同问题
	 */
	public function existsUserAsk($uid = '',$title = ''){
		$sql	= "select * from wd_ask where title='{$title}' AND userid={$uid}  ORDER BY `ctime` desc limit 0,1";
		$result = $this->_db->fetchRow($sql);
		return $result;
	}
	
	/**
	 * 获取问答回应排序
	 * 
	 */
	public function getAnswerSort($askid = ''){
		$sql	= "select * from wd_answer where askid = {$askid} order by id desc limit 1";
		$result = $this->_db->fetchRow($sql);
		return $result;
	}
	
	/**
	 * 更新问答回复数量
	 */
	public function updateAnswerNum($askid = ''){
		$sql = "update  `wd_ask_answernum` set answernum=answernum+1  where askid='$askid'";
		$this->_db->query($sql); 
	}
	
	/**
	 * 获取分类
	 */
	public function getCatItem($catid = ''){
		$sql	= "select catid,arrchildid,catname,url from category where catid in ({$catid})";
		//echo $sql;exit;
		$result	= $this->db_www->fetchAll($sql);
		return $result;
	}

	/**
	 * 获取分类详情
	 */
	public function getArticleList($cat_id_str = '',$limit = 0 , $offset = 30){
		$sql	= "select * from article where catid in ({$cat_id_str}) order by articleid desc limit {$limit},{$offset}";
		$result	= $this->db_www->fetchAll($sql);
		return $result;		
	}
	
	/**
	 * 获取用户基本信息
	 */
	public function getUserBase($uid = ''){
		$sql	= "select uid,uType,nickname,username,email,credit,sale_credit,`from`,dateline,lastlogin,pic,wappic,ip,groupname,group_status from member where uid = '{$uid}' and status = 1 limit 1";
		$result	= $this->_db->fetchRow($sql);
		return $result;
	}
	
	public function getUserDetail_1($uid = ''){
		$sql	= "select * from member_detail_1 where uid = '{$uid}' limit 1";
		$result	= $this->_db->fetchRow($sql);
		return $result;
	}
	
	public function getUserDetail_2($uid = ''){
		$sql	= "select * from member_detail_1 where uid = '{$uid}' limit 1";
		$result	= $this->_db->fetchRow($sql);
		return $result;
	}
	
	public function addUser($data){
		$this->_db->insert("member",$data);
		return $this->_db->lastInsertId();
	}

}
?>