<?php
  /**
   *##############################################
   * @FILE_NAME :Ads.php
   *##############################################
   *
   * @author : 李军锋
   * @MailAddr : 
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Tue July 07 17:04 CST 2009
   * @DATE : Tue July 07 17:04 CST 2009
   *
   *==============================================
   * @Desc :  广告数据操作类
   *==============================================
   */
class Ads extends QModels_Ask_Table 
{
	protected $_primary = 'adsid';
	protected $_name="home_ads";
			
	//添加一条记录 
	public function Add($postarr){ 
		//print_r($postarr); exit;		 
		$insert = $this->insert($postarr);
		if($insert){
			return $insert;
		}
	} 
	
	//更改一条记录
	public function edit_Ads($id, $param) {
		$where = "adsid = $id";
		$this->update($param, $where);
		return true;
	} 

	public function GetCount($where="1") {	
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `home_ads` where ".$where);
		return $result[0]['num'];
	}
	
	//获取广告列表
	public function List_Ads($where, $order='', $count=0, $offset=0) {
		$result = $this->fetchAll($where, $order, $count, $offset);
		$temp = $result->toArray();
		foreach ($temp as &$val){
			if($val[type] == 1) $val[type] = '文字链';
			else $val[type] = '图片';
			$val[adsplacename] = $this->getAdsplaceName($val[placeid]);
		}
		return $temp;
	}  
	
	//获取广告位选择框
	public function getAdsplace(){
		$result = $this->_db->fetchAll("SELECT placeid,placename FROM `home_adsplace`");
		$str = "<select id=placeid name=placeid style='width:20%'>";
		foreach ($result as $val){
			$str .= "<option value=$val[placeid]>$val[placename]</option>";
		}
		$str .= "</select>";
		return $str;
	}
	//获取广告位名称
	public function getAdsplaceName($placeid){
		$result = $this->_db->fetchAll("SELECT placename FROM `home_adsplace` where placeid=$placeid");
		return $result[0][placename];
	}	
	
	public function jugeAds($name){
		$result = $this->fetchAll("select adsid from `home_ads` where adsname='$name'");
		return $result[0]['adsid'];
	}	
	
	//删除广告
	public function del_Ads($id) {
		if(strpos($id,',') === false){
			$this->_db->query("delete from `home_ads` where adsid=$id");
			return true;
		}
		else {
			$ids = array_filter(explode(',',$id));
			foreach ($ids as $key){
				$this->_db->query("delete from `home_ads` where adsid=$key");
			}
			return true;
		}
	}		
	
	/**
	 * 生成所在广告位的配置文件 xzx 2009-09-21
	 *
	 * @param int $iPlaceid
	 * @return boolean
	 */
	public function createData($iPlaceID,&$msg)
	{
		if(!$iPlaceID) return false;
		
		$fp = fopen(APP_DATA_PATH."/data_adsplace_".$iPlaceID.".php","w");
		$str = "<?php		
\$_ADSGLOBAL['$iPlaceID']=Array(
			";
		
		$tmp_ads_list = $this->List_Ads("placeid=".$iPlaceID);
		
		foreach ($tmp_ads_list as $k=>$v){
			$str .= $v['adsid']." => Array
			(
			'adsid' => '".$v['adsid']."',
			'adsname' => '".$v['adsname']."',
			'introduce' => '".$v['introduce']."',
			'placeid' => '".$v['placeid']."',
			'type' => '".$v['type']."',		
			'linkurl' => '".$v['linkurl']."',
			'imageurl' => '".(($v['imageurl']) ? (str_replace(APP_ROOT, '', APP_PIC_ROOT)).'/'.$v['imageurl'] : "")."'							
			),
			";
		}
		
		$str .= ")
		?>";
		
		if(fwrite($fp,$str)) 
		{
			$msg = "配置文件生成成功！";
			return true;
		}	
		else 
		{
			$msg = "配置文件生成失败！";
			return false;	
		}	
	}
	
}
?>