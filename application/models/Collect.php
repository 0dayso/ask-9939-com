<?php
/**
   *##############################################
   * @FILE_NAME :Collect.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Mon Sep 21 10:24:50 CST 2009ead 1.0
   * @DATE : Mon Sep 21 10:24:50 CST 2009
   *
   *==============================================
   * @Desc :  用户收藏model
   *==============================================
   */

class Collect extends QModels_Ask_Table
{
	protected $_primary = 'cid';
	protected $_name="member_collect";
	/**
	* 添加收藏
	*
	* @param 收藏信息 array
	* @return 插入ID int
	*/
	public function Add($postarr){
		$insert = $this->insert($postarr);
		if($insert){
			return $insert;
		}
	}

	/**
	* 编辑收藏
	*
	* @param 收藏ID int
	* @param 收藏信息 array
	* @return  int
	*/
	public function Edit($postarr,$cid){
		$db = $this->getAdapter();
		$where  = $db->quoteInto('cid = ?',$cid);
		$update = $this->update($postarr,$where);
		if($update){
			return true;
		}
	}

	/**
	* 查看收藏列表 
	* @param 条件
	* @return 收藏信息 array
	*/
	public function List_Collect($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result->toArray();
	}

	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetCount($where="1") {
		$where = ($where == "") ? "1" : $where; 
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `$this->_name` where ".$where);
		return $result[0]['num'];
	}

	/**
	 * 根据某一条件获取一个单一的收藏
	 *
	 * @param str $where
	 * @return array
	 */
	public function GetOneyOne($where="1") {
		$where = ($where == "") ? "1" : $where; 
		$result = $this->_db->fetchAll("SELECT * FROM `$this->_name` where ".$where);
		return $result[0];
	}
	
	/**
	 * 获取某一会员的部分收藏
	 *
	 * @param int $uid 会员uid
	 * @param int $num 需要提取的条数
	 * @return array
	 */
	public function GetSomeCollect($uid,$num=10){
		if($uid<1) return ;
		$where = " AND uids like '%x".intval($uid)."x%'";
		$result = $this->_db->fetchAll("SELECT * FROM `$this->_name` where 1 ".$where." limit 0,".intval($num));
		return $result;
	}

	/**
	 * @desc 删除一个收藏
	 *
	 * @param int $nis
	 * @return int 返回行数
	 */
	public function Del($cid,$uid)
	{ 
		if(!$cid || !$uid) return; 
		$tmp_one = $this->GetOneyOne("cid=".$cid); 
		if(count($tmp_one)>0){
			$tmp_arr = array();
			$tmp_arr['uids'] = str_replace(",x".$uid."x",'',$tmp_one['uids']);
			$tmp_arr['uids'] = str_replace("x".$uid."x",'',$tmp_arr['uids']);
			$result = $this->Edit($tmp_arr,$cid);
			if($result){
				return true;
			}else {
				return false;
			}
		}else{
			return false;
		} 
	}

	/**
	 * 批量删除
	 *
	 * @param array $cid_arr
	 */
	public function DelMore($cid_arr){
		foreach ((array) $cid_arr as $v){
			$this->Del($v);
		}
	}
	
	/**
	 * 返回收藏类型
	 *
	 * @return array
	 */
	public function GettypeOp(){
		$tmp_type_arr = array();
		$tmp_type_arr['picid']   = "图片";
		$tmp_type_arr['buluo']   = "部落";
		$tmp_type_arr['blog']    = "日志";
		$tmp_type_arr['thread']  = "主题";
		$tmp_type_arr['space']   = "空间";
		return $tmp_type_arr;
	}


	/**
	 * 自定义获取字符
	 *
	 * @param str $str 需要处理的字串
	 * @param int $num 需要截取的个数 
	 * @return unknown
	 */
	function getstr($str,$num)
	{
		$leng=strlen($str);
		if($num>=$leng) return $str;
		$str=preg_replace("#[\r\n\s]#is",' ',$str);
		$str=strip_tags($str);
		$word=0;
		$i=0;
		while($word!=$num)
		{
			if(ord($str[$i])>0xC3)
			{
				$re.=substr($str,$i,3);
				$i+=3;
				$word++;
			}
			else
			{
				$re.=substr($str,$i,1);
				$i++;
				$word++;
			}
		}
		return $re;
	}

}
?>