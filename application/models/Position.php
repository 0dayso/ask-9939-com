<?php
/**
  *##############################################
  * @FILE_NAME :Position.php
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
  * @Desc :   推荐位模块
  *==============================================
  */

class Position extends QModels_Ask_Table
{
	protected $_primary = 'posid';
	protected $_name = 'home_position';

	//添加推荐位
	public function Add_Position($param) {
		return $this->insert($param);
	}

	// 编辑推荐位
	public function edit_Position($id, $param) {
		$where = "posid = $id";
		$this->update($param, $where);
		return true;
	}
	
	//删除推荐位
	public function del_Position($id) {
		if(strpos($id,',') === false){
			$this->_db->query("delete from `home_position` where posid=$id");
			$this->_db->query("delete from `home_position_content` where posid=$id");
			return true;
		}
		else {
			$ids = array_filter(explode(',',$id));
			foreach ($ids as $key){
				$this->_db->query("delete from `home_position` where posid=$key");
				$this->_db->query("delete from `home_position_content` where posid=$key");
			}
			return true;
		}
	}	

	//推荐位列表
	public function List_Position($where, $order, $count, $offset) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		$temp = $result->toArray();
		foreach ($temp as $k=>&$v)
		{
			$v['num'] = $this->GetPosArtNum('posid='.$v['posid']);
			$v['num'] = $v['num'] ? $v['num'] : 0;			
		}
		return $temp;
	}
	
	//统计Position记录数
	public function GetCount($where="1") {
		//echo "ss".$where; exit;
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `home_position` where ".$where);
		return $result[0]['num'];
	}
	
	//取推荐位名称
	public function GetPosName($where="") {
		if(!$where) return  false;
		$result = $this->_db->fetchAll("SELECT name FROM `home_position` where ".$where);
		return $result[0]['name'];
	}
	
	//取推荐位信息
	public function GetPos($posid=0) {
		if(!$posid) return  false;
		$result = $this->_db->fetchAll("SELECT * FROM `home_position` where posid=$posid");
		return $result[0];
	}	
		
	//统计某推荐位下的文章数
	public function GetPosArtNum($where="1") {
		//echo "ss".$where; exit;
		$where = ($where == "") ? "1" : $where;		
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `home_position_content` where ".$where);
		return $result[0]['num'];
	}	

	//频道下拉选择框
	public function GetChannel() {	
		$result = $this->_db->fetchAll("SELECT `channelid`, `channelname` FROM `Channel` where 1");
		//print_r($result);
		$str = '<select name=channelid style="width:200px" id=channelid><option value="">请选择</option>';
		foreach ($result as $k=>$v){
			//$s = ($v['channnelid'] = $s) ? ' selected ' : '';
			//$str .= '<option value='.$v['channelid'].$s.'>'.$v['channelname'].'</option>';
			$str .= '<option value='.$v['channelid'].'>'.$v['channelname'].'</option>';
		}
		$str .= '</select>';
		
		return $str;
	}	
	
	//模板下拉选择框
	function GetTpl(){
		$result = $this->_db->fetchAll("SELECT `tplid`, `name` FROM `template` where type=1");
		$str = '<select name=tplid style="width:200px" id=tplid><option value="">请选择</option>';
		foreach ($result as $k=>$v){
			//$s = ($v['tplid'] == $s) ? ' selected ' : '';
			//$str .= '<option value='.$v['tplid'].$s.'>'.$v['name'].'</option>';
			$str .= '<option value='.$v['tplid'].'>'.$v['name'].'</option>';
		}
		$str .= '</select>';	
		return $str;
	}
	
	//级别下拉选择框
	function GetLeval(){
		$result = $this->_db->fetchAll("SELECT `tplid`, `name` FROM `template` where type=1");
		$str = '<select name=lowlevel style="width:200px" id=lowlevel>';
		for ($i=1; $i<11; $i++){
			$s = ($i==5) ? ' selected ' : '';
			$str .= '<option value='.$i.$s.'>'.$i.'</option>';
		}
		$str .= '</select>';	
		return $str;
	}			

	//获取频道id
	public function GetCateId($posid='') {	
		if ($posid) {
			$result = $this->_db->fetchAll("SELECT `channelid` FROM `home_position` where posid=$posid");
			$cid = $result[0]['channelid'];
			if($cid){
				$result = $this->_db->fetchAll("SELECT `arrcatid` FROM `Channel` where channelid=$cid");
				require_once(APP_ROOT.'/Category_cache.php');
				$temp = array_filter(explode(',',$result[0]['arrcatid']));
				$result = '(';
				foreach ($temp as $k=>$v){
					$result .= $CATEGORY[$v]['arrchildid'];			
				}
				$result .= ')';		
				return $result;
			}			
		}
	}	
	
	//取推荐位的文章articleid
	public function get_Articleid($posid) {	
		//echo "SELECT `id` FROM  `home_position_content` where posid=$posid";
		$result = $this->_db->fetchAll("SELECT `id` FROM  `home_position_content` where posid=$posid");
		$str = '';
		if($result)
		{
			$str = '(';
			foreach ($result as $k=>$v){
				$str .= $v['id'].',';
			}
			$str = substr($str,0,-1).')';		
		}
		return $str;		
	}	
		
	//推荐位添加信息
	public function Add_Pos($posid,$articleids,$info,$typeid) {
		//print_r($info);
		$articleids = array_filter(explode(',',$articleids));
		//print_r($articleids);
		foreach ($articleids as $k=>$v)
		{
			//echo"insert into `home_position_content`(`articleid`,posid`, `updatetime`, `title`, `url`, `description`, `thumb`)values(".$v.",".$posid.",".time().",'".$info[$v][title][0]."','".$info[$v][url][0]."','".$info[$v][description][0]."','".$info[$v][thumb][0]."')<br>";
			$sql = "insert into `home_position_content`(`id`, `posid`, `updatetime`, `title`, `thumb`, `uid`)values(".$v.",".$posid.",".time().",'".$info[$v][title][0]."','/".$info[$v][thumb][0]."','".$info[$v][uid][0]."')";
			
			//echo $sql; exit;
			$this->_db->query($sql);
		}		
		return true;
	}
		
	//推荐位移除信息
	public function Sub_Pos($posid,$articleids) {	
		foreach ($articleids as $k=>$v)
		{
			$this->_db->query("delete from `home_position_content` where articleid=$v and posid=$posid");
		}
		return true;
	}	
	
	
	//get catid's catname
	public function getCateName($catid) {	
		if ($catid){
			//echo $catid;
			//echo APP_ROOT.'/Category_cache.php';			
			require(APP_ROOT.'/Category_cache.php');
			//require_once(APP_ROOT.'/Category_cache.php');
			return $CATEGORY[$catid]['catname'];
		}
		return false;		
	}	
	
	// juge repeat pos name
	/*public function jugePos($name,$cid){
		$result = $this->fetchAll("select posid from `home_position` where name='$name' and type=$cid");
		return $result[0]['posid'];
	}*/
	
	//判断修改时是否导致重复
	public function jugePos($pid='',$name='',$cid='',$e=''){
		//$e为true时，是修改，否则是添加
		if($e) $sql = "SELECT count(*) as num FROM `home_position` where name='$name'and type=$cid and posid!=$pid";
		else $sql = "SELECT count(*) as num FROM `home_position` where name='$name'and type=$cid";
		//echo "SELECT count(*) as num FROM `member_blog_category` where uid=$uid and catname='$cname' and catid!=$catid";
		$result = $this->_db->fetchAll($sql);
		return $result[0]['num'];
	}	
	

	// get article detail ljf
	
	public function getDetail($table,$id){
		if($table == 'member_blog')	
			$result = $this->_db->fetchAll("select blogid,subject,pic,uid from `$table` where blogid=$id");
		elseif ($table == 'member')	
			$result = $this->_db->fetchAll("select uid,username,nickname,pic from $table where uid=$id");
		elseif ($table == 'buluo')	 
			$result = $this->_db->fetchAll("select buluoid,buluoname,pic,uid from `$table` where buluoid=$id");
		elseif ($table == 'buluo_thread')	 
			$result = $this->_db->fetchAll("select tid,subject,uid from `$table` where tid=$id");
		return $result[0];		
	}
	
	public function getArticleTitle($aid){
		$result = $this->_db->fetchAll("select title from `home_position_content` where articleid=$aid");
		return $result[0]['title'];		
	}	
	
	/**
	 * 生成所在广告位的缓存文件 xzx 2009-09-21
	 *
	 * @param int $iPosID
	 * @return boolean
	 */
	public function createData($iPosID,$iType,&$msg)
	{
		if(!$iPosID || !$iType) return false;	
		
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$member_obj = new Member();		
		
		$fp = fopen(APP_DATA_PATH."/data_pos_".$iPosID.".php","w");
		$str = "<?php		
\$_POSGLOBAL['$iPosID']=Array(
			";
				
		$tmp_poscontent_list = $this->GetList("posid=".$iPosID,"pos_cid desc",$this->GetValue($iPosID,'num_needs'));
		//print_r($tmp_poscontent_list); exit;
		foreach ($tmp_poscontent_list as $k=>$v){			
			if($iType == 1)// 日志		
				$sUrl = "/blog/view/id/".$v['id'];
			elseif($iType == 2)//会员空间		
				$sUrl = "/user/?uid=".$v['id'];
			elseif ($iType == 3)	//部落		
				$sUrl = "/buluo/index/bid/".$v['id'];	
			elseif($iType == 4) //部落话题		
				$sUrl = "/buluo/view/tid/1/".$v['id'];
			
			
			$str .= $v['id']." => Array
			(
			'id' => '".$v['id']."',
			'uid' => '".$v['uid']."',
			'nickname' => '".$member_obj->GetValue($v['uid'],'nickname')."',
			'updatetime' => '".$v['updatetime']."',
			'title' => '".$v['title']."',			
			'thumb' => '".(($v['thumb']) ? (str_replace(APP_ROOT, '', APP_PIC_ROOT)).$v['thumb'] : "")."',
			'url' => '".$sUrl."'
			),
			";
		}
		
		$str .= ")
		?>";
		
		//echo $str; exit;
		
		if(fwrite($fp,$str)) 
		{
			$msg = "缓存文件生成成功！";
			return true;
		}	
		else 
		{
			$msg = "缓存文件生成失败！";
			return false;	
		}
	}	

	/**
	 * @desc 获取推荐内容列表
	 * 
	 *
	 * @param unknown_type $where
	 * @param unknown_type $order
	 * @param unknown_type $count
	 * @param unknown_type $offset
	 * @return unknown
	 */
	public function GetList($where, $order='', $count=0, $offset=0) {
		$this->_name = 'home_position_content';
		$result = $this->fetchAll($where, $order, $count, $offset);		
		$temp = $result->toArray();		
		return $temp;
	}  
	
	/**
	 * @Desc :获取某字段的值
	 *
	 * @param int $uid
	 * 2009-09-16
	 * @return string
	 */	
	public function GetValue($posid,$fieldname='num_needs')
	{
		if(!$posid) return 0 ;				
		$select_one_sql = "SELECT `$fieldname`
				FROM `home_position`
				WHERE posid=".$posid;
		
		
		
		//echo $select_one_sql; exit;
		$re = $this->_db->fetchAll($select_one_sql);
		return $re[0][$fieldname];
	}
	
}
?>