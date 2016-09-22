<?php
/**
   *##############################################
   * @FILE_NAME :MapController.php
   *##############################################
   *
   * @author : 李军锋
   * @MailAddr : licaption@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Thu Jun 18 18:00:29 CST 2009
   * @DATE : Thu Jun 18 18:00:29 CST 2009
   *
   *==============================================
   * @Desc : 问答地图
   *==============================================
   */

class MapController extends Zend_Controller_Action
{
	public function init() {
		
		$this->ViewObj = Zend_Registry::get('view');
		Zend_Loader::loadClass('Map',MODELS_PATH);
		$this->map = new Map();
		parent::init();		
	}


	public function indexAction() {		
		include(APP_ROOT."/Keshi_cache.php");		
		$this->ViewObj->CATEGORY = $CATEGORY;
		$keshi = $this->map->get_parent_keshi();
		$this->ViewObj->keshi = $keshi;
		
		$page = $this->_getParam('page');
		$page = isset($page) ? $page : 1;
		
		$classid = $this->_getParam('classid');
		$classid = isset($classid) ? $classid : 3;
		$this->ViewObj->classid = $classid;
		
		$arrchild = $CATEGORY[$classid]['arrchildid'];
		$arrchild = explode(',',$arrchild);
		$count = count($arrchild);
		//echo $count;exit;
		if($count>2){
			//$page_str = '分页：';
			$left = $count % 2;
			if($left) $page_num = ($count / 2)+1;
			else $page_num = $count / 2;	

			$page_num = intval($page_num);
			//$page_str .= '一共'.$page_num.'页 ';

			$page_str = "";
			for($i=1; $i<=$page_num; $i++){				
				if($page == $i){
					$font_start = '<font color="red">';
					$font_end = '</font>';
				}
				else $font_start = $font_end = '';
				$page_str .= '<a href="/map/index/classid/'.$classid.'/page/'.$i.'">'.$font_start.$i.$font_end.'</a> ';				
				//if($i>=9) break;
			}
			//$page_str .='<a href="/map/index/classid/'.$classid.'/page/'.($page+1).'"><font color="red"> >> </font></a>';
			$this->ViewObj->page_str = $page_str;
		}

		$arr_index1 = ($page - 1) * 2;
		$arr_index2 = ($page - 1) * 2 + 1;
		
		
		$rs1 = $this->map->get_cat_ask($arrchild[$arr_index1]);
		//var_dump($rs1);exit;
		$rs2 = $this->map->get_cat_ask($arrchild[$arr_index1],15);

		//print_r($arr_index1);
		//print_r($arr_index2);exit;


		if($arrchild[$arr_index2])
		{
			$rs3 = $this->map->get_cat_ask($arrchild[$arr_index2]);
			$rs4 = $this->map->get_cat_ask($arrchild[$arr_index2],15);		
		}

		
		
		$this->ViewObj->cat1 = '<a target="_blank" href="/classid/'.$arrchild[$arr_index1].'">'.$CATEGORY[$arrchild[$arr_index1]]['name'].'</a>';
		$this->ViewObj->cat2 = '<a target="_blank" href="/classid/'.$arrchild[$arr_index2].'">'.$CATEGORY[$arrchild[$arr_index2]]['name'].'</a>';
		
		$this->ViewObj->rs1 = $rs1;
		$this->ViewObj->rs2 = $rs2;
		$this->ViewObj->rs3 = $rs3;
		$this->ViewObj->rs4 = $rs4;
		//exit("1");
				
		echo $this->ViewObj->render('/ask_map.phtml');	
	}
		
}
?>