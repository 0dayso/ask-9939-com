<?php
/**
   *##############################################
   * @FILE_NAME :SearchindexController.php
   *##############################################
   *
   * @author : 张泽华
   * @MailAddr : zhang-zehua@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Sun Sep 27 14:54:11 CST 2009ead 1.0
   * @DATE : Sun Sep 27 14:54:11 CST 2009
   *
   *==============================================
   * @Desc :  获取搜索数据 
   *==============================================
   */
class SearchindexController extends Zend_Controller_Action
{
	public function init()
	{
        Zend_Loader::loadClass('Keshi', MODELS_PATH);
        $this->keshi_obj = new Keshi();

		$this->view = Zend_Registry::get("view");

		//加载用户
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();
		
		//加载科室
        $CATEGORY = $this->keshi_obj->cache_keshi();
		$this->type_arr = $CATEGORY;

		//加载问答搜索类
		Zend_Loader::loadClass('GetSearchData',MODELS_PATH);
		$this->GetSearchData_obj = new GetSearchData();
		
		//加载资讯搜索类
		Zend_Loader::loadClass('GetZXSearchData',MODELS_PATH);
		$this->ZXSearchData_obj = new GetZXSearchData();
		
		//加载资讯类
		Zend_Loader::loadClass('Ask',MODELS_PATH);
		$this->AskObj = new Ask();

		parent::init();
	}

	/**
  	 *问答搜索列表
  	 * 
  	 */
	public function indexAction()
	{
		$tmp_page = $this->_getParam("page")?$this->_getParam("page"):1;
		$tmp_kw   = $this->_getParam("kw")?$this->_getParam("kw"):"9939";
		$tmp_kw   = str_replace(" ","%20",$tmp_kw);
		 $tmp_url  = "http://211.167.92.198:8080/ask/search?kw=".$tmp_kw."&page=".$tmp_page;
		$tmp_xml  = file_get_contents($tmp_url);  
		//处理xml
		$this->GetSearchData_obj->SetXmlData($tmp_xml);
		$tmp_totle      = $this->GetSearchData_obj->GetTotal();
		$tmp_searchtime = $this->GetSearchData_obj->GetSearchTime();
		$tmp_list       = $this->GetSearchData_obj->GetList();
		$tmp_pagesize   = $this->GetSearchData_obj->GetPageSize();
		foreach ((array) $tmp_list as $k=>$v){
			//用->TAB分割
			$tmp_info = @explode("	",$v['INFO']);
			unset($tmp_list[$k]['info']);
			$tmp_list[$k]['ftuid'] = $tmp_info[0];//发帖人id
			$tmp_list[$k]['fttime'] = $tmp_info[1];//发帖时间
			$tmp_list[$k]['hdstate'] = $tmp_info[2];//是否又最佳答案 1有 0无
			$tmp_list[$k]['hduid'] = $tmp_info[3]; //最佳答案回答者id
			$tmp_list[$k]['tname'] = $this->getTname($v['CID']);       //所属科室
		} 
		//分页处理  
		$this->GetSearchData_obj->SetArr(array('kw'=>$tmp_kw));//翻页传递的变量
		$this->GetSearchData_obj->SetUrl("/Searchindex/index");// 
		$this->GetSearchData_obj->GetPageHtml($tmp_page); //当前也
		$this->view->pagehtml   = $this->GetSearchData_obj->output(1);  
		$this->view->list       = $tmp_list;
		$this->view->searchtime = $tmp_searchtime/1000;
		$this->view->totle      = $tmp_totle;
		$this->view->kw		    =  str_replace("%20"," ",$tmp_kw);;
		$this->view->view_url   = "/ask/show/id/";//问答具体页面路径 
		$this->view->AskObj = $this->AskObj;
		echo $this->view->render("search_list.phtml");
	}
	
	/**
	 * 获取科室名
	 *
	 * @param int $tid  科室id  这里是CID
	 * @return str 
	 */
	public function getTname($tid){ 
		if(!$tid) return ; 
		$tmp_turl = "";
		$tmp_info  = $this->type_arr[$tid];
		$tmp_tname = "<a href='".$tmp_turl.$tid."'>".$tmp_info['name']."</a>";
		if($tmp_info['pID']>0){
			$tmp_info_f1 = $this->type_arr[$tmp_info['pID']];
			$tmp_tname   = "<a href='".$tmp_turl.$tmp_info['pID']."'>".$tmp_info_f1['name']."</a>&gt;".$tmp_tname;
		}
		if($tmp_info_f1['pID']>0){
			$tmp_info_f2 = $this->type_arr[$tmp_info_f1['pID']];
			$tmp_tname   = "<a href='".$tmp_turl.$tmp_info_f1['pID']."'>".$tmp_info_f2['name']."</a>&gt;".$tmp_tname;
		} 
		return $tmp_tname;
	}
	
	/**
	 * 咨询搜索列表
	 *
	 */
	public function zxsearchAction()
	{ 
		$tmp_page = $this->_getParam("page")?$this->_getParam("page"):1;
		$tmp_kw   = $this->_getParam("kw")?$this->_getParam("kw"):"9939";
		$tmp_kw   = str_replace(" ","%20",$tmp_kw);
		$tmp_url  = "http://211.167.92.198:9080/news/search?kw=".$tmp_kw."&page=".$tmp_page;
		$tmp_xml  = file_get_contents($tmp_url); 
		//处理xml
		$this->ZXSearchData_obj->SetXmlData($tmp_xml);
		$tmp_totle      = $this->ZXSearchData_obj->GetTotal();
		$tmp_searchtime = $this->ZXSearchData_obj->GetSearchTime();
		$tmp_list       = $this->ZXSearchData_obj->GetList();
		$tmp_pagesize   = $this->ZXSearchData_obj->GetPageSize();
		foreach ((array) $tmp_list as $k=>$v){
			//用->TAB分割
			$tmp_info = @explode("	",$v['INFO']);
			unset($tmp_list[$k]['INFO']);
			$tmp_list[$k]['url'] = $tmp_info[1];//连接地址
			$tmp_list[$k]['ctime'] = date("Y-m-d",$tmp_info[2]);//发布时间 
			$tmp_list[$k]['img'] = $tmp_info[0];//题图路径 
		}  
		//分页处理  
		$this->ZXSearchData_obj->SetArr(array('kw'=>$tmp_kw));//翻页传递的变量
		$this->ZXSearchData_obj->SetUrl("/Searchindex/zxsearch");// 
		$this->ZXSearchData_obj->GetPageHtml($tmp_page); //当前也
		$this->view->pagehtml   = $this->ZXSearchData_obj->output(1);  
		$this->view->list       = $tmp_list;
		$this->view->searchtime = $tmp_searchtime/1000;
		$this->view->totle      = $tmp_totle;
		$this->view->kw		    =  str_replace("%20"," ",$tmp_kw);
		echo $this->view->render("search_zx_list.phtml");
	}
}

?>