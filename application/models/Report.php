<?php
  /**
   *##############################################
   * @FILE_NAME :Report.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Tue Sep 15 09:38:32 CST 2009ead 1.0
   * @DATE : Tue Sep 15 09:38:32 CST 2009
   *
   *==============================================
   * @Desc :  举报数据
   *==============================================
   */

class Report extends QModels_Ask_Table
{
	protected $_primary = 'rid';
	protected $_name="member_report";
	/**
	* 添加举报
	*
	* @param 举报信息 array
	* @return 插入ID int
	*/
	public function Add($postarr){
		$insert = $this->insert($postarr);
		if($insert){
			return $insert;
		}
	}

	/**
	* 编辑举报
	*
	* @param 举报ID int
	* @param 举报信息 array
	* @return  int
	*/
	public function Edit($postarr,$rid){
		$db = $this->getAdapter();
		$where  = $db->quoteInto('rid = ?',$rid);
		$update = $this->update($postarr,$where);
		if($update){
			return true;
		}
	}

	/**
	* 查看举报列表 
	* @param 条件
	* @return 举报信息 array
	*/
	public function List_Report($where, $order, $count, $offset) {
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
	 * 根据某一条件获取一个单一的举报
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
	 * @desc 删除一个举报
	 *
	 * @param int $nis
	 * @return int 返回行数
	 */
	public function Del($rid)
	{
		if(!$rid) return;
		$db = $this->getAdapter();
		$where  = $db->quoteInto('rid = ?',$rid);
		$del = $this->delete($where);
		if($del){
			return true;
		}
	}

	/**
	 * 批量删除
	 *
	 * @param array $rid_arr
	 */
	public function DelMore($rid_arr){
		foreach ((array) $rid_arr as $v){
			$this->Del($v);
		} 
	}
	/**
	 * 返回举报类型
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
		$tmp_type_arr['ask']  	 = "提问"; 
		$tmp_type_arr['answer']  = "回答"; 
		return $tmp_type_arr;
	}
	
	/**
	 * 获取举报查阅状态
	 *
	 * @return array
	 */
	public function GetnewOp(){
		$tmp_type_ar = array();
		$tmp_type_ar['-1'] = "不限";
		$tmp_type_ar[0]    = "待处理";
		$tmp_type_ar[1]    = "已忽略";
		return $tmp_type_ar;
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