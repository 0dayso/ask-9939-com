<?php
/**
  *##############################################
  * @FILE_NAME :feed.php
  *##############################################
  *
  * @author : liubo
  * @MailAddr : funkfan@sina.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Tue Jun 23 01:23:51 GMT 2009
  * @DATE : Tue Jun 23 01:23:51 GMT 2009
  *
  *==============================================
  * @Desc :会员动态模块
  *==============================================
  */
class Feed extends QModels_Ask_Table 
{
	//数据分页中使用 $this->limit_arr['count'], $this->limit_arr['offset']	
	public $limit_arr;
	
    protected function _setup()
    {
        $this->_name = 'member_feed';
        parent::_setup();
    } 
    
    //查询动态
	public function select_feed($page=true, $where_arr=array(), $limit=null){
		$db = $this->getAdapter();		
		$where = "1";
		
		$uid_str = '';
		foreach ( (array)$where_arr['uid'] as $uid_val){
			$uid_str .= $uid_val.',';				
		}
		$uid_str = substr($uid_str, 0, -1); 

		//if ($where_arr['uid']) {	
			//$where .= $db->quoteInto(' AND uid = ?', $where_arr['uid']);
		if($uid_str){
			$where .= " AND uid IN ($uid_str)";
		}
		if ($where_arr['username']) {
			$where .= $db->quoteInto(' AND username = ?', $where_arr['username']);
		}
		
		if ($where_arr['icon']) {
			$where .= $db->quoteInto(' AND icon = ?', $where_arr['icon']);
		}		
		if ($where_arr['stime'] && $where_arr['etime']) {	
			$where .= $db->quoteInto(' AND (dateline between ? )', $where_arr['stime'].' AND '.$where_arr['etime']);		
		}
		if ($where_arr['orderby']) {
			$orders = $where_arr['orderby'];
		}
		if ($where_arr['ordersc']) {
			$orders .= " ".$where_arr['ordersc'];
		}

		#echo $where, $orders, $limit;exit;
		//判断是否分页
		if($page==true){
			$result = $this->fetchAll($where, $orders, $this->limit_arr['count'], $this->limit_arr['offset']);			
		}
		else {
			if($limit)
				$result = $this->fetchAll($where, $orders, $limit, 0);			
			else
				$result = $this->fetchAll($where);
		}
				
		if ($result)
			return $result->toArray();
		else
			return null;		
	}	
	
	//删除动态
	public function delete_feed($feedid_arr){	
		try{

		$db = $this->getAdapter();	

		foreach ($feedid_arr as $feedid_val){	
			$where = $db->quoteInto('feedid = ?', $feedid_val);
			$rows_affected = $this->delete($where);			
		}		

		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}			
	}		
	
	//插入动态
	public function insert_feed($icon, $title_template='', $title_data=array(), $body_template='', $body_data=array(), $body_general='', $images=array(), $image_links=array(), $target_ids='', $friend='') {
		global $_SGLOBAL;
		$tmp_cookie_array = $_SGLOBAL['cookie'];		
		
		$feedarr = array(
			'icon' => $icon,
			'uid' => $tmp_cookie_array['uid'],
			'username' => $tmp_cookie_array['username'],
			'nickname' => $tmp_cookie_array['nickname'],			
			'dateline' => time(),
			'title_template' => $title_template,
			'body_template' => $body_template,
			'body_general' => $body_general,
			'image_1' => empty($images[0])?'':$images[0],
			'image_1_link' => empty($image_links[0])?'':$image_links[0],
			'image_2' => empty($images[1])?'':$images[1],
			'image_2_link' => empty($image_links[1])?'':$image_links[1],
			'image_3' => empty($images[2])?'':$images[2],
			'image_3_link' => empty($image_links[2])?'':$image_links[2],
			'image_4' => empty($images[3])?'':$images[3],
			'image_4_link' => empty($image_links[3])?'':$image_links[3],
			'target_ids' => $target_ids,
			'friend' => $friend
		);

		//$feedarr = sstripslashes($feedarr);//去掉转义
		$feedarr['title_data'] = serialize($title_data);//数组转化
		$feedarr['body_data'] = serialize($body_data);//数组转化
		//$feedarr = saddslashes($feedarr);//增加转义

		//print_r($feedarr);
		//exit;
		return $this->insert($feedarr);
	}
}
?>