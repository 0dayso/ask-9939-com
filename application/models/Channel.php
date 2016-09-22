<?php
/**
  *##############################################
  * @FILE_NAME :Channel.php
  *##############################################
  *
  * @author : hua
  * @MailAddr : dreamcastzh@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   频道模块
  *==============================================
  */

class Channel extends QModels_Ask_Table
{
	/**
	* 查看频道
	*
	* @param 条件
	* @return 集团信息 array
	*/
	public function List_Channel($where, $order=null, $count=null, $offset=null) {
	//	echo $where.'<br/>';echo $order.'<br/>';echo $count.'<br/>';echo $offset.'<br/>';

		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		return $result->toArray();
	}
	
	/**
	 * 
	 */
	public function Add_Channel($arr){
		return $this->insert($arr);
	}
	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetCount($where="1") {
		//echo "ss".$where; exit;
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `Channel` where ".$where);
		return $result[0]['num'];
	}
	
	/**
	* 查模板
	*
	* @param 条件
	* @return 集团信息 array
	*/
	public function List_Tpl($channelid,$type='add') {
		$result = $this->_db->fetchAll("select name,channelid from template where type = 1");
		if($type=='add'){
			foreach($result as $k=>$v){
				$html .="<option value='$v[channelid]'>$v[name]</option>";
			}
		}elseif($type=='edit'){
			$row = $this->fetchRow('channelid = "'.$channelid.'"');
			foreach($result as $k=>$v){
				
				$tag = ($v[channelid]==$row->tplid) ? "selected=selected" : '';
				$html .="<option value='$v[channelid]' $tag>$v[name]</option>";
			}
		}
		return $html;
	}
	
	/**
	 * 读取缓存文件
	 */
	public function Category(){
		require(APP_ROOT.'/Category_cache.php');
		return $CATEGORY;
	}
	
	/**
	 * 取详细信息
	 */
	public function GetDetail($channelid){
		$where = 'channelid=\''.$channelid.'\'';
		$row = $this->fetchRow('channelid = "'.$channelid.'"');
		//$row = $this->_db->fetchAll("SELECT * FROM `Category` where $where");
		return $row;
	}
	
	/**
	 * 入库函数
	 */
	public function Do_rest($a,$type,$where=null,$table='Channel'){
		/*引入缓存*/
		$CATEGORY = $this->Category();
		$a['arrcatid'] = substr(preg_replace("~[^0-9,]~is","",$a['catnames']),0,-1);
		$aCatid = explode(",",$a['arrcatid']);
		unset($a['catnames']);
		arsort($aCatid);
		//过滤栏目id
		for($n=0;$n<count($aCatid);$n++){
			$arrparentid = $CATEGORY[$aCatid[$n]][arrparentid];//echo '<br/>';
			for($i=0;$i<count($aCatid);$i++){
					if(strpos($arrparentid,$aCatid[$i])!==false)
					$aCatid[$i] = null;
			}
		}
		//过滤空值
		$aCatid = array_filter($aCatid);
		$a['arrcatid'] = implode(",",$aCatid);
		
		$a['setting'] = "array (
				  'meta_title' => '".$a['meta_title']."',
				  'meta_keywords' => '".$a['meta_keywords']."',
				  'meta_description' => '".$a['meta_description']."',
				  'upload_allowext' => 'doc|docx|xls|ppt|wps|zip|rar|txt|jpg|jpeg|gif|bmp|swf|png',
				  'upload_maxsize' => '1024000',
				  'thumb_enable' => '1',
				  'thumb_width' => '300',
				  'thumb_height' => '300',
				  'watermark_enable' => '0',
			)";
		unset($a['meta_title'],$a['meta_keywords'],$a['meta_description']);
		if($type=='add'){
			$insert_id = $this->Add_Channel($a);
			/*插入排序id*/
			$row = $this->fetchRow('channelid = "'.$insert_id.'"');
			$row->listorder = $insert_id;
			$row->save();
			return $insert_id;
		}elseif($type == 'edit'){
			$row = $this->fetchRow('channelid = "'.$a['channelid'].'"');
			//print_r($a);exit;
			$rows_affected = $this->_db->update($table, $a, $where);
			return 1;
		}
	}
	
	/**
	 * 取包含的栏目
	 */
	public function getCatehtml($where){
		//$result = $this->List_Channel($where);
		$result = $this->_db->fetchAll("SELECT * FROM `Category` where ".$where);
		foreach($result as $k=>$v){
			$html .= "$v[catname]($v[catid]),";
		}
		//$html = substr($html,0,-1);
		return $html;
	}
	
	public function Del($channelid){
		
		$where = $this->_db->quoteInto('channelid = ?', "$channelid");
		$rows_affected = $this->_db->delete('Channel', $where);
		
		return $rows_affected;

	}
	
	//add kerry;
	public function getAll()
	{
		$sql="SELECT * FROM `Channel`";
		$ret=$this->_db->fetchAll($sql);
		return $ret; 
	}
	
	/**
	 * Enter 格式化频道数据
	 * author 张泽华
	 * @return array
	 */
	public function getChannelOptions()
	{
		$tmp_channel_arr = $this->getAll();
		foreach ((array) $tmp_channel_arr as $v){
			$option_arr['list'][$v['channelid']] = $v['channelname'];
			$option_arr['data'][$v['channelid']] = $v['url'];
		}
		return $option_arr;
	}
	
	/**
	 * Enter 获取频道名称
	 * author 张泽华
	 * @return str
	 */
	public function getChName($chid)
	{
		if(!$chid) return ; 
		$sql="SELECT * FROM `Channel` WHERE channelid=".$chid;
		$tmp_chOne=$this->_db->fetchAll($sql); 
		return $tmp_chOne[0]['channelname'];
	}
	
}
?>