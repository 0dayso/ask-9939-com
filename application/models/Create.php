<?php
/**
  *##############################################
  * @FILE_NAME :pmodule.php
  *##############################################
  *
  * @author : 张华
  * @MailAddr : dreamcastzh@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : 2009-10-13
  *
  *==============================================
  * @Desc :   
  *==============================================
  */

class Create extends QModels_Ask_Table
{
	
	public function init() {
		try {
            parent::init();
            $this->db_www = $this->db_v2_read;
            $this->db_jb =  $this->db_dzjb_read;
            
			$this->TPL = array(
					"a"		=> '<a href=\"$URL\">$TITLE</a>',
					
				"li_span_a" => '<li><span>[$CATNAME]</span><a target=\"_blank\" href=\"$URL\">$TITLE</a></li>',
				
				"li_a_span" => '<li><a href=\"$URL\" target=\"_blank\">$TITLE</a><a href=\"$URLII\" target=\"_blank\"><span>$USERNAME</span></a></li>',
				
				"li_a"		=> '<li><a href=\"$URL\" target=\"_blank\">$TITLE</a></li>',
				
			"li_a_img_em"	=> '<li><a href=\"$URL\"><img src=\"$THUMB\" /></a><em>$TITLE</em></li>',
				
				"li_img"	=>	'<li><img src=\"$THUMB\" width=\"$WIDTH\" height=\"$HEIGHT\"/></li>',
				
				"a_img"		=> '<a href=\"$URL\" target=\"_blank\"><img src=\"$THUMB\" width=\"$WIDTH\" height=\"$HEIGHT\"/></a>',
				
				"a_img_span"=> '<a href=\"$URL\" target=\"_blank\"><img src=\"$THUMB\" width=\"$WIDTH\" height=\"$HEIGHT\"/><span>$TITLE</span></a>',
				
				"a_span"	=>'<a href=\"$URL\" target=\"_blank\"><img src=\"$THUMB\" width=\"$WIDTH\" height=\"$HEIGHT\"/>
								<span>$TITLE</span>
							</a>',
				
				"mix_a"		=> '<div class=\"hot-news $STYLE\">
				                    <div class=\"pic\">
				                      	<a href=\"$URL\" target=\"_blank\"><img src=\"$THUMB\" width=\"$WIDTH\" height=\"$HEIGHT\"/></a>                    
				                    </div>
				                    <div class=\"content\">
				                     	<h2><a href=\"$URL\" target=\"_blank\">$TITLE</a></h2>
				                      	<P>{$DESC}…… </P>
				                    </div>
				              </div>',
				
				"mix_slide"	=>	'<div class=\"slide-thumb\">
				                    <a href=\"$URL\"><img src=\"$THUMB\" width=\"$WIDTH\" height=\"$HEIGHT\"/></a>
									<div class=\"slide-thumb-textinfo\">
				                      <h3>$TITLE</h3>
									  <p>$DESC</p>
									</div>
				                </div>'
			);
			//	@require(APP_ROOT.'../www/Category_cache.php');
			//	$this->CATEGORY = $this->view->CATEGORY = $CATEGORY;
			//	$this->sCATEGORY = var_export($this->CATEGORY,true);
			$this->ASK_URL = "http://ask.9939.com";
			$this->BU_URL = "http://home.9939.com";
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}
	
	public function Module_POS($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($count) ? 0 : $offset;
		$pos = $this->getPos($posid,0,$count,$offset);
		//构造html
		foreach($pos as $k=>$v){
			$URL	=	$v[url];
			$TITLE	=	$this->getSubstr($v[title],0,$words);
			$THUMB	=	$v[thumb];
			$WIDTH	=	$width;
			$HEIGHT	=	$height;
			$DESC	=	$this->getSubstr($v[description],0,$desc);
			$html	=	$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	public function Module_ADS($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($offset) ? 0 : $offset;
		$ads = $this->getAds($adsid,0,$count,$offset);

		if($Array_Back){
			return $ads;
		}else{
			//构造html
			if(is_array($ads)){
				foreach($ads as $k=>$v){
					$URL	=	$v[linkurl];
					$TITLE	=	$this->getSubstr($v[adsname],0,$words);
					$THUMB	=	$v[imageurl];
					$WIDTH	=	$width;
					$HEIGHT	=	$height;
					$DESC	=	$this->getSubstr($v[introduce],0,$desc);
					$html	=	$this->TPL[$temp];
					eval("\$Html .= \"$html\";");
				}
			}
		}

		return $Html;
	}
	
	public function Module_Art($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($count) ? 0 : $offset;
		if($type=='new'){
			$pos = $this->getArtByHits($catid,$count);
		}else{
			$pos = $this->getArt($catid,$count,' articleid DESC',$offset,$type);
		}
		
		//构造html
		
		foreach($pos as $k=>$v){
			$URL		=	$v[url];
			$TITLE  	=	$this->getSubstr($v[title],0,$words);
			$CATNAME	=	$this->getSubstr($this->CATEGORY[$v[catid]]['catname'],0,2);
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	public function Module_Cat($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($count) ? 0 : $offset;
		$aCat = $this->getCat($catid,$count,$offset);
		foreach($aCat as $k=>$v){
			$URL		=	$v[url];
			$TITLE  	=	$this->getSubstr($v[catname],0,$words);
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	public function Module_Ask($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($count) ? 0 : $offset;
		$aAsk = $this->getAsk($type,$count,0);
		foreach($aAsk as $k=>$v){
			$URL		=	$v[url];
			$TITLE  	=	$this->getSubstr($v[title],0,$words);
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	public function Module_Buluo($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($count) ? 0 : $offset;
		$aBuluo = $this->getBuluo($type,$count);
		foreach($aBuluo as $k=>$v){
			$URL		=	$v[url];
			$TITLE  	=	$this->getSubstr($v[buluoname],0,$words);
			$THUMB		= 	"http://home.9939.com/".$v[pic];
			$WIDTH		=	$width;
			$HEIGHT		=	$height;
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}

	public function Module_Thread($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($offset) ? 0 : $offset;
		$aThread = $this->getThread('',$count,$type);
		foreach($aThread as $k=>$v){
			$URL		=	$this->BU_URL . "/buluo/view/tid/$v[tid]";
			$TITLE  	=	$this->getSubstr($v[subject],0,$words);
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	public function Module_Link($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$num = empty($num) ? 0 : $num;
		preg_match_all("~'catname' => '$catname',.*?'url' => '([^']*)',~is",$this->sCATEGORY,$a);
		return $a[1][$num];
	}
	
	//取与问答相关文章
	public function Module_Corr_Art($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$aArt = $this->getCorArt($keywords,$count);
		foreach($aArt as $k=>$v){
			$URL		=	$v[url];
			$TITLE  	=	$this->getSubstr($v[title],0,$words);
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	//取与问答相关疾病
	public function Module_Corr_Dis($sParameter){
		/*获得参数*/
		extract($this->get_para($sParameter));
		$aDis = $this->getCorDis($kid,$count);
		foreach($aDis as $k=>$v){
			$URL		=	"http://jb.9939.com/dis/{$v[contentid]}/";
			$TITLE  	=	$this->getSubstr($v[title],0,$words);
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}
	
	/*public function Module_Pic($sParameter){
		//获得参数
		extract($this->get_para($sParameter));
		$count = is_null($count) ? 1 : $count;
		$offset= is_null($count) ? 0 : $offset;
		$aPic = $this->getPic($catid,$count);
		foreach($aBuluo as $k=>$v){
			$URL		=	$v[url];
			$USERNAME	=	$v[username];
			$html =$this->TPL[$temp];
			eval("\$Html .= \"$html\";");
		}
		return $Html;
	}*/
	
	/* 函数说明：获得参数函数
	*  示例图链接:
	*  参数说明: $sParameter 获得页面传递的参数
	*  edit by : zhanghua 2009-7-30
	*/
	public function get_para($sParameter){
		if(strpos($sParameter,",")!==false){
			$aParameter = explode(",",$sParameter);
			foreach($aParameter as $k=>$v){
				$temp_arr = explode("=",$v);
				$sPara[$temp_arr[0]] = $temp_arr[1];
			}
		}else{
			$temp_arr = explode("=",$sParameter);
			$sPara[$temp_arr[0]] = $temp_arr[1];
		}
		return $sPara;
	}
	
	
	/**
	 * 
	 * 返回推荐位相关数组
	 * @param $id:推荐位ID
	 * @param $flag: true:返回HTML
	 * @param $count 返回条数
	 * @param $offset 
	 * @desc $flag为1时 返回HTML格式： <li><a href="URL地址">TITLE（11个字符）</a></li>
	 * 		 支持取得推荐信息部分数据：$count:取得条数 $offset：第几条开始
	 */
	public function getPos($id=0, $flag=0, $count=0, $offset=0) {
		if(!$id) return array();
		@include(APP_ROOT . "/data/data_pos_$id.php");
		$tmp_pos_array = $_POSGLOBAL[$id];
		if($count) {
			$tmp_pos_array = @array_splice($tmp_pos_array, $offset, $count);
		} else {
			$tmp_pos_array = @array_splice($tmp_pos_array, $offset);
		}
		if(is_array($tmp_pos_array)) {
			if($flag) {		#返回HTML
				$this->view->array = $tmp_pos_array;
				@require_once(APP_ROOT . "/data/POS/AD_POS_ARRAY.php");
				return $this->view->render('/tpl/public/'.($aTemp[$id] ? $aTemp[$id] : 'li.phtml'));
			} else {		#返回数组
				return $tmp_pos_array;
			}
		} else {
			return array();
		}
	}
	
	/**
	 * 
	 * 返回栏目位相关咨询数组
	 * @param $id:栏目ID
	 * @param $flag: true:返回HTML
	 * @param $count:条数
	 * @desc $flag为1时 返回HTML格式： <li><a href="URL地址">TITLE（11个字符）</a></li>
	 */
	public function getArt($id=0, $count=10, $order=' articleid DESC ', $offset=0, $type='new') {
		try{
		if($id!=0){
			$where = $this->CATEGORY[$id]['child'] ? "catid IN (". $this->CATEGORY[$id]['arrchildid'] .") " : " catid='$id' ";
			$where.=' and status=20 ';
		}else{
			$where =' status=20 ';
		}
		if($type=='new'){
			$sql = "select * from Article where status=20 order by articleid desc limit 0,10";
		}else{
			$type = $type ? '_'.$type : '';
			$r = $this->db_www->fetchAll("select * from article_count order by hits{$type} desc limit 0,10");
			foreach($r as $k=>$v){$sArticleid .= $v[articleid].',';}
			$sArticleid = substr($sArticleid,0,-1);
			$sql = "select * from Article where status=20 and articleid in ($sArticleid)";
		}
		//$tmp_art_array = $this->art_obj->List_Article($where, $order, $count, $offset);		#取得资讯文章
		$tmp_art_array = $this->db_www->fetchAll($sql);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		if(is_array($tmp_art_array)) {
			return $tmp_art_array;
		} else {
			return array();
		}
	}
	
	public function getArtByHits($catid=0, $count='10', $order=' hits DESC', $flag=0, $tplid=0) {
		if(!$catid) {
			return $flag ? '' : array();
		}
		
		$tmp_art_array = $this->html_obj->getArtByHits($catid, $count, $order);
		$tmp_tpl_array = array('0'=>'li.phtml', '1'=>'li_tag.phtml');
		$tplname = $tmp_tpl_array[$tplid] ? $tmp_tpl_array[$tplid] : $tmp_tpl_array[0];
		if(is_array($tmp_art_array)) {
			if($flag == 1) {		#返回HTML
				$this->view->array = $tmp_art_array;
				return $this->view->render('/tpl/public/'. $tplname);
			} else {		#返回数组
				return $tmp_art_array;
			}
		} else {
			return $flag ? '' : array();
		}
	}
	/**
	 * 
	 * 返回广告数组
	 * @param $id:广告位ID
	 * @param $flag: true:返回HTML
	 * @param $count:条数
	 * @desc $flag为true时 返回HTML格式： <li><a href="URL地址">TITLE（11个字符）</a></li>
	 */
	public function getAds($id=0, $flag=0 ,$count=0, $offset=0) {
		if(!$id) return array();
		@require(APP_ROOT . "/data/data_adsplace_$id.php");
		$tmp_ads_array = $_ADSGLOBAL[$id];
		if($count) {
			$tmp_ads_array = @array_splice($tmp_ads_array, $offset, $count);
		}else {
			$tmp_ads_array = @array_splice($tmp_ads_array, $offset);
		}
		if(!is_array($tmp_ads_array)) { return $flag ? '' : array(); }
		foreach ($tmp_ads_array as $k => &$v) {
			$v['linkurl'] = str_replace('/list/index/','/',$v['linkurl']);
			$v['linkurl'] = str_replace('/ask/show/','/',$v['linkurl']);
			$v['imageurl'] = HOME_9939_URL.$v['imageurl'];
		}
		if(is_array($tmp_ads_array)) {
			if($flag) {		#返回HTML
				$this->view->array = $tmp_ads_array;
				return $this->view->render('/tpl/public/li.phtml');
			} else {		#返回数组
				return $tmp_ads_array;
			}
		} else {
			return array();
		}
	}
	
	
	public function getctime($linkurl){
		//echo $linkurl;
		$l = strrpos($linkurl,'/') + 1;
		$id = substr($linkurl,$l);		
		//exit("SELECT ctime FROM `wd_ask` where id=$id");
		$result = $this->_db->fetchAll("SELECT ctime FROM `wd_ask` where id=$id");
		$ct = $result[0]['ctime'];
		$ct = date('Y-m-d',$ct);
		return $ct;
	}	

	public function getCat($catid=0, $count=1 , $offset=0 , $flag=0) {
		//@require(APP_ROOT.'/Category_cache.php');
		if(!$catid) return '';
		$tmp_catid_string = $this->CATEGORY[$catid]['child'] ? $this->CATEGORY[$catid]['arrchildid'] : '';
		$tmp_catid_array = explode(',', $tmp_catid_string);
		if(!is_array($tmp_catid_array)) { return $flag ? '' : array(); }
		foreach ($tmp_catid_array as $k => $v) {
			if($v==$catid)continue;
			$tmp_array[] = $this->CATEGORY[$v];		#栏目临时数组
		}
		if($count) {
			$tmp_array = @array_splice($tmp_array , $offset, $count);
		}else {
			$tmp_array = @array_splice($tmp_array , $offset);
		}
		return $tmp_array;
	}
	
	public function getPic($id,$ofset=0,$num=1){
		$ids = $this->CATEGORY[$id]['arrchildid'];		
		$sql = "select url,title,thumb from Article where catid in($ids) and status=20 and thumb!='' order by updatetime desc limit $ofset,$num";
		$r = $this->_db->fetchAll($sql);
		return $r;	
	}	
	
	//取最新问题
	public function getAsk($type, $count=5) {
		switch($type){
			case 'day':
				$order = ' order by hits_day DESC';
				break;
			case 'week':
				$order = ' order by hits_week DESC';
				break;
			case 'month':
				$order = ' order by hits_month DESC';
				break;
			case 'new':
				$where = ' AND status=1 ';
				break;
		}
		$limit = " LIMIT 0, $count";
		$sql = "SELECT * FROM `wd_ask_count` WHERE 1 $where $order $limit";
		try {
			$result = $this->_db->fetchAll($sql);
			foreach($result as $k=>$v){
				$sAskid .= $v[askid].',';
			}
			$sAskid = substr($sAskid,0,-1);
			$rs = $this->_db->fetchAll("select * from wd_ask where id in ($sAskid)");
		}
		catch(Exception $e)	{			
			echo $e->getMessage();
		}
		foreach ($rs as $k => &$v) {
			$v['url'] = $this->ASK_URL .'/id/'. $v['id'];
		}
		return $rs;
	}
	
	
	//取部落
	public function getBuluo($type, $count=5) {
		switch($type){
			case 'week':
				$order = ' order by hits_week DESC';
				break;
			case 'month':
				$order = ' order by hits_month DESC';
				break;
			case 'hits':
				$order = ' order by hits DESC';
				break;
			case 'new':
				$where = ' AND status=1 ';
				break;
		}
		$limit = " LIMIT 0, $count";
		$sql = "SELECT * FROM `buluo_view_count` WHERE 1 $where $order $limit";
		try {
			$result = $this->_db->fetchAll($sql);
			foreach($result as $k=>$v){
				$sBuluoid .= $v[buluoid].',';
			}
			$sBuluoid = substr($sBuluoid,0,-1);
			$rs = $this->_db->fetchAll("select * from buluo where buluoid in ($sBuluoid)");
		}
		catch(Exception $e)	{			
			echo $e->getMessage();
		}
		foreach ($rs as $k => &$v) {
			$v['url'] = $this->BU_URL .'/buluo/index/bid/'. $v['buluoid'];
		}
		return $rs;
	}
	
	public function getThread($catid,$count,$type){
		if($catid!=0){
			$sql = "select distinct(buluoid) from buluo where catid=$catid";
		}else{
			$sql = "select * from buluo_view_count order by hits{$type} limit 0,$count";
		}
		$rs = $this->_db->fetchAll($sql);
		foreach($rs as $k=>$v){
				$sBuluoid .= $v[buluoid].',';
			}
		$sBuluoid = substr($sBuluoid,0,-1);
		$where = "where buluoid in ($sBuluoid)";
		
		$rs = $this->_db->fetchAll("select * from buluo_thread $where order by tid desc limit 0,$count");
		return $rs;
	}
	
	//获取相关文章
	public function getCorArt($keywords,$count){
		//echo "select * from Article where keywords like ('%$keywords%') order by articleid desc limit 0,$count";
		$rs = $this->db_www->fetchAll("select * from Article where keywords like ('%$keywords%') order by articleid desc limit 0,$count");
		return $rs;
	}
	
	//获取相关文章
	public function getCorDis($kid,$count){
		$r = $this->_db->fetchAll("select name from wd_keshi where id=$kid");
		$rs = $this->db_jb->fetchAll("select id from 9939_section_category where name='".$r[0][name]."'");
		$res = $this->db_jb->fetchAll("select * from 9939_dzjb a ,9939_disease_content b where b.keshi like ('%".$rs[0][id]."%') and a.contentid=b.contentid order by a.contentid desc limit 0,$count");
		return $res;
	}
	
	
	//问答分类
	public function getCat_ask($where='1'){
		$r = $this->_db->fetchAll("select id,name from wd_keshi where pID =0 AND $where");
		$temp = array();
		if($r)
		{
			foreach($r as $k=>$v)
			{				
				//$v[url] = '/list.php?classid='.$v[id];
				$sql = "select id,name from wd_keshi where pID=".$v[id];
				$s = $this->_db->fetchAll($sql);
				foreach ($s as $kk=>&$vv)
				{
					$vv[url] = '/classid/'.$vv[id];
				}
				$temp[$v['id']]['child'] = $s;
				$temp[$v['id']]['name'] = $v['name'];
				$temp[$v['id']]['url'] = '/classid/'.$v[id];
			}
			return $temp;
		}
	}
	
	//问答分类 显示所有级别
	public function getCat_ask_index($where=''){
		$sql = "select id,name,url from wd_keshiask where pid=0";
		$r = $this->_db->fetchAll($sql);
		foreach($r as $k=>$v){
			//$r[$k][url] = '/classid/'.$v[id];
			//$r[$k]['url'] = str_replace('/classid/','/classidn/',$r[$k]['url']);
			$sql = "select id,name,url from wd_keshiask where pid=$v[id]";
			$rr = $this->_db->fetchAll($sql);
			foreach($rr as $kk=>$vv){
				//$rr[$kk][url] = '/classid/'.$vv[id];
				//$rr[$kk]['url'] = str_replace('/classid/','/classidn/',$rr[$kk]['url']);
			}
			// $rr 是二级栏目
			if($rr) $r[$k][child] = $rr;
			
			foreach($rr as $kkk=>$vvv){
				$sql = "select id,name,url from wd_keshiask where pid=$vvv[id] order by listorder";
				$rrr = $this->_db->fetchAll($sql);
				foreach($rrr as $kkkk=>$vvvv){
					//$rrr[$kkkk][url] = '/classid/'.$vvvv[id];
					//$rrr[$kkkk][url] = str_replace('/classid/','/classidn/',$rrr[$kkkk][url]);
				}
				//$rrr 是三级栏目
				if($rrr) $r[$k][child][$kkk][child] = $rrr;
			}
		}
		return $r;
		/*$sql = "select id,name from wd_keshiask where pID=0 and $where";
		$r = $this->_db->fetchAll($sql);
		foreach($r as $k=>$v){
			$r[$k][url] = '/classid/'.$v[id];
			$sql = "select id,name from wd_keshi where pID=$v[id]";
			$rr = $this->_db->fetchAll($sql);
			foreach($rr as $kk=>$vv){
				$rr[$kk][url] = '/classid/'.$vv[id];
			}
			// $rr 是二级栏目
			if($rr) $r[$k][child] = $rr;
			
			foreach($rr as $kkk=>$vvv){
				$sql = "select id,name from wd_keshi where pID=$vvv[id]";
				$rrr = $this->_db->fetchAll($sql);
				foreach($rrr as $kkkk=>$vvvv){
					$rrr[$kkkk][url] = '/classid/'.$vvvv[id];
				}
				//$rrr 是三级栏目
				if($rrr) $r[$k][child][$kkk][child] = $rrr;
			}
		}
		return array_filter($r);*/			
	}
	
	public function ret_arr($pid){
		$sql = "select id,name from wd_keshi where pID=".$v[id];
		$s = $this->_db->fetchAll($sql);
		if($s){
			foreach ($s as $k=>$v){
				$s[$k][url] = '/classid/'.$v[id];
				$s[$k][title] = '/classid/'.$v[id];
				$re[] = $s;
			}
		}
		return $re;
	}
	

	//积分榜
	public function getask_jfb($where=1){
		$r = $this->_db->fetchAll("select uid,nickname,username,credit,experience from member where $where order by credit desc limit 0,50");
		Zend_Loader::loadClass('Member',MODELS_PATH); 
		$this->Member = new Member();
		foreach ($r as $k=>&$v)
		{
			$v[xh] = $k+1;
			$v[name] = $v[nickname] ? $v[nickname] : $v[username];
			$v[url] = 'http://home.9939.com/user/?uid='.$v[uid];
			$info = $this->Member->getInfo($v[uid]);
			//var_dump($info);
			//echo $v['uid']; exit;
			$v[grouptitle] = $info[grouptitle];
			$v[groupname] = $info[groupname];
		}
		return $r;
	}
	
	//回答榜
	public function getask_hdb($where=1){
		$r = $this->_db->fetchAll("select uid,nickname,username,credit,experience from member where $where order by credit desc limit 0,50");		
		foreach ($r as $k=>&$v)
		{
			$v[xh] = $k+1;
			$v[name] = $v[nickname] ? $v[nickname] : $v[username];
			$v[url] = 'http://home.9939.com/user/?uid='.$v[uid];
			//echo "select count(askid) as num,askid from wd_answer where userid='$v[uid]' group by userid";
			$rs = $this->_db->fetchAll("select count(askid) as num,askid from wd_answer where userid='$v[uid]' group by userid");
			$v[replynums] = $rs[0]['num'];
			$num = 0;
			foreach ($rs as $kk=>$val)
			{
				//echo "select id from wd_ask where bestanswer=$val[askid]";exit;
				//if($this->_db->fetchRow("select id from wd_ask where bestanswer=$val[askid]")) $num++;
			}
			$v[rate] = round(($num*100/$v[replynums])).'%';
		}			
		return $r;
	}

	//上周积分榜
	public function getlastjf($where=1){
		$r = $this->_db->fetchAll("select uid,nickname,username,credit,experience from member where $where  order by credit desc limit 0,50");
		$lastweek = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));
		if($r)
		{
			foreach ($r as $k=>&$v)
			{
				$v[xh] = $k+1;
				$v[name] = $v[nickname] ? $v[nickname] : $v[username];
				$v[url] = 'http://home.9939.com/user/?uid='.$v[uid];
				//echo "select credit,creditmode from member_credit_history where dateline>$lastweek and uid=$v[uid]";
				$rs = $this->_db->fetchAll("select credit,creditmode from member_credit_history where dateline>$lastweek and uid=$v[uid]");
				if($rs)
				{
					foreach ($rs as $kk=>$val)
					{
						if($val[creditmode] == 'pay') $num_pay += $val[credit];
						if($val[creditmode] == 'get') $num_get += $val[credit];
					}
					$v[lastwcredit] = $num_get-$num_pay;
				}
			}
		}

		return $r;
	}
	
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
	
	//获取医师是详细信息
	public function getDetailInfo($uid){
		if($uid){
			//echo "select zhicheng,doc_keshi,doc_hos,memo from member_detail_2 where uid=$uid";
			$r = $this->_db->fetchRow("select zhicheng,doc_keshi,doc_hos,memo from member_detail_2 where uid=$uid");
			return $r;
		}
	}
	
	//最新加入的医师
	public function get_NewInDoc(){
		$r = $this->_db->fetchAll("select uid,nickname,username,pic from member where 1 order by dateline desc limit 0,5");
		foreach ($r as $k=>&$v)
		{
			$v[url] = 'http://home.9939.com/space.php?uid='.$v[uid];			
			$v[name] = $v[nickname] ? $v[nickname] : $v[username];
		}
		return $r;
	}
	
	//取问题
	public function getAsk_index($where="1",$count=7,$start=0) {
		$limit = " LIMIT $start, $count";
		$sql = "SELECT id,title FROM `wd_ask` WHERE $where order by id desc $limit";
		//echo $sql;
		try {
			$result = $this->_db->fetchAll($sql);
		}
		catch(Exception $e)	{			
			echo $e->getMessage();
		}
		foreach ($result as $k => &$v) {
			$v['url'] = $this->ASK_URL .'/id/'. $v['id'];
			$v[title] = $this->getSubstr($v[title],0,15);
		}
		return $result;
	}

	// 统计记录数
	public function GetCount1($status) {
		if($status==0) $where = " status = $status";//待解决问题生成
		if($status==1) $where = " status = $status";//已解决问题生成
		if($status==2) $where =" point != 0";///悬赏问题生成
		if($status==3) $where = " 1";//全部问题生成
		if($status==4) $where =" answernum=0 ";//零回复问题生成
		$where .= " and isShow=1";			
			
		$result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where $where");
		return $result[0]['count'];
	}
	
	
	/*字符串截取函数
	* $str 字符串
	* $beginStr 开始取的位置
	* $length  要取的长度,个数
	* $isHaveBland 统计是否包含空格符,0 是不统计空格,1统计空格
	* $codingLength   utf-8 $codingLength=3,gb2312 $codingLength=2;
	*/
	public function getSubstr($str,$beginStr=-1,$length=-1,$isHaveBlank=1,$codingLength=3)
	{
		$len    =    strlen ($str);
		//$str=str_replace("&nbsp;"," ",$str);//过滤掉html空格
		if($length==-1 ) $length=-$beginStr-1;
		$i        =    0;
		$strCount=0;
		$subStr="";
		while ($i<$len)
		{
			if (preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",substr($str,$i,1)))
			{
				if($strCount>=$beginStr)
				{
					if(strlen(mb_substr($str,$strCount,1,'utf8'))==2){
						$subStr    .=    substr($str,$i,$codingLength-1);
					}else{
						$subStr    .=    substr($str,$i,$codingLength);
					}
					//echo $i.$subStr.'<br>';
				}
				//过滤特殊字符·····
				if(strlen(mb_substr($str,$strCount,1,'utf8'))==2){
					$i += $codingLength-1;
				}else{
					$i += $codingLength;
				}
				$strCount++;
			}
			elseif (substr($str,$i,6)=="&nbsp;")// 处理空格
			{
				if($strCount>=$beginStr)
				{
					$subStr .= substr($str,$i,6);
				}
				if($isHaveBlank==1)//统计空格
				{
					$strCount++;
				}
				$i+=6;
			}
			else
			{
				if($strCount>=$beginStr)
				{
					$subStr .= substr($str,$i,1);
				}
				if($isHaveBlank==1)//统计空格
				{
					$strCount++;
				}
				else//不统计空格
				{
					if(substr($str,$i,1)!=" ")
					{
						$strCount++;
					}
				}
				$i+=1;
			}

			if($strCount==$length+$beginStr)
			{
				break;
			}
		}
		if($beginStr==-1)
		{
			return $strCount;
		}
		//$subStr=str_replace(" ","&nbsp;",$subStr);//还原html空格
		return $subStr;
	}

}