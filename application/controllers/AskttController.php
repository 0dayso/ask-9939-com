<?php
/**
   *##############################################
   * @FILE_NAME :manage_PicController.php
   *##############################################
   *
   * @author : 矫雷
   * @MailAddr : kxgsy163@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Thu Jun 18 18:00:29 CST 2009
   * @DATE : Thu Jun 18 18:00:29 CST 2009
   *
   *==============================================
   * @Desc : 相册管理
   *==============================================
   */

Zend_Loader::loadClass('Ask',MODELS_PATH);
Zend_Loader::loadClass('Answer',MODELS_PATH);
Zend_Loader::loadClass('Keshi',MODELS_PATH);
Zend_Loader::loadClass('Member',MODELS_PATH);
Zend_Loader::loadClass('MemberDetail',MODELS_PATH);
Zend_Loader::loadClass('MemberDetailDoctor',MODELS_PATH);
Zend_Loader::loadClass('Credit',MODELS_PATH);	#加载积分类

class AskttController extends Zend_Controller_Action
{
	private $ViewObj;
	private $AskObj = '';
	private $AnswerObj = '';
	private $MemberObj = '';
	private $memberDetailObj = '';
	private $member_detail_1 = 'MemberDetail';
	private $member_detail_2 = 'MemberDetailDoctor';
	private $Credit_obj = '';


	public function init() {
		//exit('ok');
		$this->ViewObj = Zend_Registry::get('view');
		$this->AskObj = new Ask();
		$this->AnswerObj = new Answer();
		$this->MemberObj = new Member();
		//加载搜索类
		Zend_Loader::loadClass('GetSearchData',MODELS_PATH);
		$this->GetSearchData_obj = new GetSearchData();

		$this->Credit_obj = new Credit();		#积分类
        $this->keshi_obj = new Keshi();

		parent::init();
	}



	public function indexAction() {
		try {
			$tmp_kw = trim($this->_getParam("kw"));
			if($tmp_kw == '输入提问内容，点击“快速提问”' || $tmp_kw == '输入提问内容，点击“我要提问”') $tmp_kw = '';
			$tmp_kw = $tmp_kw ? $tmp_kw : "请输入您的提问标题";
			$tmp_kw   = str_replace(" ","%20",$tmp_kw);
			$tmp_askcontent = $this->_getParam("askcontent");
			//获取搜索内容
			$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
			$this->GetSearchData_obj->SetXmlData($tmp_xml);
			$tmp_list            = $this->GetSearchData_obj->GetList();
			if(count($tmp_list)>=3){
				$tmp_num = 3;
			}else{
				$tmp_num = count($tmp_list);
			}

			foreach ((array) $tmp_list as $k=>$v){
				$tmp_list[$k]['CONTENT'] = $this->GetSearchData_obj->getstr($v['CONTENT'],55,true);
			}
			$aToken = new Zend_Session_Namespace('token');
			$aToken->unlock();
			$sToken = md5(time().$tmp_kw);
			$aToken->token = $sToken;
			$aToken->lock();
			/*
			$aToken = new Zend_Session_Namespace('token');
			if($aToken->isLocked())
			{
				$aToken->unlock();
			}
			Zend_Loader::loadClass('Asession',MODELS_PATH);
			$session_obj = new Asession();
			$aSession = $session_obj->Get_Manage_Session();
			*/
			$this->ViewObj->answerUid  = $this->_getParam("uid")>0?$this->_getParam("uid"):0;
			$this->ViewObj->token  = $sToken;
			$this->ViewObj->list = $tmp_list;
			$this->ViewObj->num  = $tmp_num;
			$this->ViewObj->kw   = str_replace("%20"," ",$tmp_kw);
			$this->ViewObj->askcontent = $tmp_askcontent;
			$this->ViewObj->user_info = $this->MemberObj->getCookie();
//print_R($this->ViewObj->user_info);
			echo $this->ViewObj->render('/question_testt.phtml');
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}

	public function loadcatAction() {
		$id = $this->_getParam('id');
		$id = intval($id);
		#if(!$id) $id='1653';
		$tmp_keshi_obj = new Keshi();
		$where = $tmp_keshi_obj->getValue('pid') .'=\''. $id .'\'';
		$tmp_keshi_list_array = $tmp_keshi_obj->getList($where);
		$tmp_keshi_option_str = '';
		foreach ($tmp_keshi_list_array as $k => $v) {
			$tmp_keshi_option_str .= '<option value="'. $v[$tmp_keshi_obj->getValue('primary')] .'" class="'. $v[$tmp_keshi_obj->getValue('pid')] .'">'. $v[$tmp_keshi_obj->getValue('name')] .'</optioni>';
		}
		echo $tmp_keshi_option_str;
	}


	public function doAction() {
		$tmp_act = $this->getRequest()->getParam('do');
		$say_ok_array = array('looklist');
		if(!in_array($tmp_act, $say_ok_array)) {

		}

		if(in_array($tmp_act, get_class_methods(get_class($this)))) {
			$this->{strtolower($tmp_act)}();
		} else {
			Zend_Adver_Js::helpJsRedirect("/user", 1, '错误的访问地址');
		}
	}



	public function showAction() {
		$id = $this->getRequest()->getParam('id');
		$s = $this->getRequest()->getParam('s');
		$this->ViewObj->s = $s;
		if(!$id) return ;
		$tmp_ask_info = $this->AskObj->get_one($id);
		$this->ViewObj->curentuserid = $tmp_ask_info['userid'];
		$this->ViewObj->title = $tmp_ask_info['title'];
		//var_dump($tmp_ask_info);
		//if(!$tmp_ask_info)  // xzxin 2009-12-04
		//{
			//echo "<script>alert('对不起，您访问的问题不存在！');location.href='/';</script>";
			//exit;
		//}



		//var_dump($tmp_ask_info);
		//if(!$tmp_ask_info) Zend_Adver_Js::helpJsRedirect("/", 1, '该问题已经不存在了!');
		if(!$tmp_ask_info){
			//Zend_Adver_Js::helpJsRedirect("/", 1, '该问题已经不存在了!');
			//最新提问
			$where = " status=1 ";
			$order = " etime desc";
			$tmp = array(
				'li_1'=>'37',//公告
				'li_2'=>'58',//常见病
				'li_3'=>'59',//亚健康
				'li_4'=>'60',//生活类
			);
			foreach ($tmp as $k => $v) {
				$tmp_ads_array = $this->getAds($v);
				$this->ViewObj->$k = $tmp_ads_array;
			}
			echo $this->ViewObj->render('/error.phtml');
			exit;

		}


		$this->getClassByAsk(&$tmp_ask_info);
		//print_r($tmp_ask_info);
		//exit;
		$this->ViewObj->info = $tmp_ask_info;

		$tmp_user_cookie = $this->MemberObj->getInfo($tmp_ask_info['userid']);	#提问者用户
		//var_dump($tmp_user_cookie);
		$tmp_user_cookie['linkurl'] =  $tmp_ask_info['hiddenname'] ? '#' : HOME_9939_URL .'user/?uid='. $tmp_user_cookie['uid'];
		if(strpos($tmp_user_cookie['pic'],'default.jpg')){
			if($tmp_user_cookie['uType']==1){
				$tmp_user_cookie['pic'] = ASK_URL.'/images_ask/images/niming_dz.png';
			}
			else $tmp_user_cookie['pic'] = ASK_URL.'/images_ask/images/niming_ys.png';
		}
		else $tmp_user_cookie['pic'] = HOME_9939_URL . $tmp_user_cookie['pic'];
		//$tmp_user_cookie['pic'] =  $tmp_ask_info['hiddenname'] ? $pic : HOME_9939_URL . $tmp_user_cookie['pic'];
		//$tmp_user_cookie['pic'] =  $tmp_ask_info['hiddenname'] ? HOME_USER_DEFAULT_PIC : HOME_9939_URL . $tmp_user_cookie['pic'];
		$tmp_user_cookie['nickname'] =  $tmp_ask_info['hiddenname'] ? '匿名用户' : $tmp_user_cookie['nickname'];
		$this->ViewObj->user_info = $tmp_user_cookie;

		$this->newObj($tmp_user_cookie['uType']);
		$this->ViewObj->user_info_detail = $this->memberDetailObj->get_one($tmp_ask_info['userid']);

		$where = 'askid='. $tmp_ask_info['id'];
		$order = ' addtime asc';  // xzxin 2009-11-22
		$tmp_answer_array = $this->AnswerObj->getList($where, $order);

		$tmp_good_answer = array();	#最佳答案
		foreach ($tmp_answer_array as $k => &$v) {
			$tmp_answer_array[$k]['user_info'] = $v['user_info'] = $this->MemberObj->getInfo($v['userid']);		#回复用户
			$tmp_answer_array[$k]['user_info']['pic'] = $v['user_info']['pic'] = $tmp_answer_array[$k]['user_info']['pic'] ? HOME_9939_URL . $tmp_answer_array[$k]['user_info']['pic'] : HOME_USER_DEFAULT_PIC;
			#$v['user_info_detail'] = $this->memberDetailObj->get_one($v['userid']);
			if( $tmp_ask_info['bestanswer'] && ($tmp_ask_info['bestanswer']==$v['id']) ) {
				$tmp_good_answer = $v;
				unset($tmp_answer_array[$k]);
			}
		}

		$this->ViewObj->answerList = $tmp_answer_array;
		$this->ViewObj->goodAnswer = $tmp_good_answer;
		$this->ViewObj->askid = $id;
		$this->ViewObj->AskObj = $this->AskObj;
		$this->ViewObj->keshiid = $tmp_ask_info[classid];
		$this->ViewObj->keywords = $tmp_ask_info[keywords];
		$tmp_user_isloogin = $this->MemberObj->getCookie();
		$this->ViewObj->user_isloogin_info = $tmp_user_isloogin;	#当前登录用户
		/**
		echo "<pre>";
		print_r($this->ViewObj->info);
		print_r($this->ViewObj->user_info);
		echo "</pre>";
		//*/

		//print_r($tmp_answer_array);
		//exit;

		//////////////////////////  最新问题  /////////////////////////////

		/**
		$where = " ctime>". strtotime("-1 days");
		$order = ' ctime DESC ';  // xzxin 2009-11-22
		$count = 6;
		$tmp_ask_array = $this->AskObj->getList($where, $order, $count);
		foreach ($tmp_ask_array as $k => &$v) {
			$v['url'] = '/id/'. $v['id'];
			$v['title'] = mb_substr($v['title'], 0, 15, 'utf8');
		}

		//$tmp_ask_array=array();
		$this->ViewObj->newAsk = $tmp_ask_array;
		#print_r($tmp_ask_array);

		**/
		//////////////////////////  最新问题  /////////////////////////////



		/////////////////////////  相关问题  /////////////////////////////

	    /***
		$tmp_kw = preg_replace("/\s|&nbsp;/", "20%", $tmp_ask_info['title']);
		$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
		$this->GetSearchData_obj->SetXmlData($tmp_xml);
		$tmp_list = $this->GetSearchData_obj->GetList();
		if(is_array($tmp_list)) {
			sort($tmp_list);
			$tmp_list = array_splice($tmp_list, 0, 6);
			foreach ($tmp_list as $k => &$v) {
				$v['url'] = '/id/'.$v['ID'];
				$v['TITLE'] = mb_substr(strip_tags($v['TITLE']), 0, 15, 'utf8');
			}
		}

            ***/
		//print_r($tmp_list);
		//$tmp_list=array();
		$this->ViewObj->ask = $tmp_list;
		/////////////////////////  相关问题  /////////////////////////////

		//print_r($tmp_answer_array);
		//exit;

		//页面横图广告
		//$this->ViewObj->aADS = $this->getAds(56);
		echo $this->ViewObj->render('/show_ask.phtml');

	}


	public function show1Action() {
		$id = $this->getRequest()->getParam('id');
		$s = $this->getRequest()->getParam('s');
		$this->ViewObj->s = $s;
		if(!$id) return ;
		$tmp_ask_info = $this->AskObj->get_one($id);
		$this->ViewObj->curentuserid = $tmp_ask_info['userid'];
		$this->ViewObj->title = $tmp_ask_info['title'];
		//var_dump($tmp_ask_info);
		//if(!$tmp_ask_info)  // xzxin 2009-12-04
		//{
			//echo "<script>alert('对不起，您访问的问题不存在！');location.href='/';</script>";
			//exit;
		//}



		//var_dump($tmp_ask_info);
		//if(!$tmp_ask_info) Zend_Adver_Js::helpJsRedirect("/", 1, '该问题已经不存在了!');
		if(!$tmp_ask_info){
			//Zend_Adver_Js::helpJsRedirect("/", 1, '该问题已经不存在了!');
			//最新提问
			$where = " status=1 ";
			$order = " etime desc";
			$tmp = array(
				'li_1'=>'37',//公告
				'li_2'=>'58',//常见病
				'li_3'=>'59',//亚健康
				'li_4'=>'60',//生活类
			);
			foreach ($tmp as $k => $v) {
				$tmp_ads_array = $this->getAds($v);
				$this->ViewObj->$k = $tmp_ads_array;
			}
			echo $this->ViewObj->render('/error.phtml');
			exit;

		}


		$this->getClassByAsk(&$tmp_ask_info);
		//print_r($tmp_ask_info);
		//exit;
		$this->ViewObj->info = $tmp_ask_info;

		$tmp_user_cookie = $this->MemberObj->getInfo($tmp_ask_info['userid']);	#提问者用户
		//var_dump($tmp_user_cookie);
		$tmp_user_cookie['linkurl'] =  $tmp_ask_info['hiddenname'] ? '#' : HOME_9939_URL .'user/?uid='. $tmp_user_cookie['uid'];
		if(strpos($tmp_user_cookie['pic'],'default.jpg')){
			if($tmp_user_cookie['uType']==1){
				$tmp_user_cookie['pic'] = ASK_URL.'/images_ask/images/niming_dz.png';
			}
			else $tmp_user_cookie['pic'] = ASK_URL.'/images_ask/images/niming_ys.png';
		}
		else $tmp_user_cookie['pic'] = HOME_9939_URL . $tmp_user_cookie['pic'];
		//$tmp_user_cookie['pic'] =  $tmp_ask_info['hiddenname'] ? $pic : HOME_9939_URL . $tmp_user_cookie['pic'];
		//$tmp_user_cookie['pic'] =  $tmp_ask_info['hiddenname'] ? HOME_USER_DEFAULT_PIC : HOME_9939_URL . $tmp_user_cookie['pic'];
		$tmp_user_cookie['nickname'] =  $tmp_ask_info['hiddenname'] ? '匿名用户' : $tmp_user_cookie['nickname'];
		$this->ViewObj->user_info = $tmp_user_cookie;

		$this->newObj($tmp_user_cookie['uType']);
		$this->ViewObj->user_info_detail = $this->memberDetailObj->get_one($tmp_ask_info['userid']);

		$where = 'askid='. $tmp_ask_info['id'];
		$order = ' addtime asc';  // xzxin 2009-11-22
		$tmp_answer_array = $this->AnswerObj->getList($where, $order);

		$tmp_good_answer = array();	#最佳答案
		foreach ($tmp_answer_array as $k => &$v) {
			$tmp_answer_array[$k]['user_info'] = $v['user_info'] = $this->MemberObj->getInfo($v['userid']);		#回复用户
			$tmp_answer_array[$k]['user_info']['pic'] = $v['user_info']['pic'] = $tmp_answer_array[$k]['user_info']['pic'] ? HOME_9939_URL . $tmp_answer_array[$k]['user_info']['pic'] : HOME_USER_DEFAULT_PIC;
			#$v['user_info_detail'] = $this->memberDetailObj->get_one($v['userid']);
			if( $tmp_ask_info['bestanswer'] && ($tmp_ask_info['bestanswer']==$v['id']) ) {
				$tmp_good_answer = $v;
				unset($tmp_answer_array[$k]);
			}
		}

		$this->ViewObj->answerList = $tmp_answer_array;
		$this->ViewObj->goodAnswer = $tmp_good_answer;
		$this->ViewObj->askid = $id;
		$this->ViewObj->AskObj = $this->AskObj;
		$this->ViewObj->keshiid = $tmp_ask_info[classid];
		$this->ViewObj->keywords = $tmp_ask_info[keywords];
		$tmp_user_isloogin = $this->MemberObj->getCookie();
		$this->ViewObj->user_isloogin_info = $tmp_user_isloogin;	#当前登录用户
		/**
		echo "<pre>";
		print_r($this->ViewObj->info);
		print_r($this->ViewObj->user_info);
		echo "</pre>";
		//*/

		//print_r($tmp_answer_array);
		//exit;

		//////////////////////////  最新问题  /////////////////////////////

		/**
		$where = " ctime>". strtotime("-1 days");
		$order = ' ctime DESC ';  // xzxin 2009-11-22
		$count = 6;
		$tmp_ask_array = $this->AskObj->getList($where, $order, $count);
		foreach ($tmp_ask_array as $k => &$v) {
			$v['url'] = '/id/'. $v['id'];
			$v['title'] = mb_substr($v['title'], 0, 15, 'utf8');
		}

		//$tmp_ask_array=array();
		$this->ViewObj->newAsk = $tmp_ask_array;
		#print_r($tmp_ask_array);

		**/
		//////////////////////////  最新问题  /////////////////////////////



		/////////////////////////  相关问题  /////////////////////////////

	    /***
		$tmp_kw = preg_replace("/\s|&nbsp;/", "20%", $tmp_ask_info['title']);
		$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
		$this->GetSearchData_obj->SetXmlData($tmp_xml);
		$tmp_list = $this->GetSearchData_obj->GetList();
		if(is_array($tmp_list)) {
			sort($tmp_list);
			$tmp_list = array_splice($tmp_list, 0, 6);
			foreach ($tmp_list as $k => &$v) {
				$v['url'] = '/id/'.$v['ID'];
				$v['TITLE'] = mb_substr(strip_tags($v['TITLE']), 0, 15, 'utf8');
			}
		}

            ***/
		//print_r($tmp_list);
		//$tmp_list=array();
		$this->ViewObj->ask = $tmp_list;
		/////////////////////////  相关问题  /////////////////////////////

		//print_r($tmp_answer_array);
		//exit;

		//页面横图广告
		//$this->ViewObj->aADS = $this->getAds(56);
		echo $this->ViewObj->render('/show_ask_1.phtml');

	}



	/**
	*
	* 采纳最佳答案
	*/
	public function overAction() {
		$id = intval($this->_getParam('id'));
		$aid = intval($this->_getParam('askid'));
		if(!$id) return ;
		$tmp_user_isloogin = $this->MemberObj->getCookie();
		$tmp_ask_user = $this->AskObj->get_one($aid);

		#$tmp_ask_user['userid'] = $tmp_user_isloogin['uid'] = 8; #测试使用

		if($tmp_ask_user['userid']!=$tmp_user_isloogin['uid']) {
			Zend_Adver_Js::Goback('您的权限不够!');
		} else {
			$param = array(
			'id'=>$aid,
			'bestanswer'=>$id,
			'etime'=>time(),
			'status'=>1
			);
			if($this->AskObj->edit($param)) {

				######获得悬赏积分#####
				if($tmp_ask_user['point']>0) {
					$tmp_answer_best_array = $this->AnswerObj->get_one($id);
					$tmp_answer_best_user_array = $this->MemberObj->get_one_by_id($tmp_answer_best_array['userid']);
					$tmp_user_credit = $tmp_answer_best_user_array['credit'] + $tmp_ask_user['point'];
					$arr = array('credit'=>$tmp_user_credit);
					$this->MemberObj->Edit($arr, $tmp_answer_best_array['userid']);
				}
				######获得悬赏积分#####

				Zend_Adver_Js::helpJsRedirect("/id/".$aid, 1, '采纳答案成功!');
			} else {
				Zend_Adver_Js::helpJsRedirect("/id/".$aid, 1, '采纳答案失败!');
			}
		}
	}




	/**
	*
	* 加载HTML文件
	*/
	public function loadhtmlAction() {
		$tpl = $this->_getParam('html');
		$id = $this->_getParam('id');
		$this->ViewObj->aid = $id;
		if($tpl=='edit_question') {
			echo $this->ViewObj->render('/question_edit.phtml');
		} else if($tpl=='edit_point') {
			$tmp_memeber_cookie = $this->MemberObj->getCookie();
			#print_r($tmp_memeber_cookie);
			$tmp_point_option = '';
			if($tmp_memeber_cookie['credit']<20) {
				$tmp_point_option .= '<option value="0">对不起！您没有可用积分！</option>';
			} else {
				for($i=20; $i<=100; $i=$i+20) {
					if($i<$tmp_memeber_cookie['credit']) {
						$tmp_point_option .= '<option value="'. $i .'">'. $i.'分</option>';
					}
				}
			}

			$this->ViewObj->point_option = $tmp_point_option;
			echo $this->ViewObj->render('/point_edit.phtml');
		} else if($tpl=='ok') {
			$tmp_memeber_cookie = $this->MemberObj->getCookie();
			$aid = $this->_getParam('id');
			#echo $this->ViewObj->aid, '---', $aid;
			$tmp_memeber_cookie['pwd'] = $pwd = $this->_getParam('pwd');
			if($this->_getParam('flag')) unset($tmp_memeber_cookie['pwd']);
			$this->ViewObj->user_info = $tmp_memeber_cookie;
			$this->ViewObj->doctor = $this->getAds(50);
			#print_r($this->ViewObj->doctor);
			$tmp_ask_array = $this->AskObj->get_one($aid);
			#print_r($tmp_ask_array);
			$tmp_kw = preg_replace("/\s|&nbsp;/", "20%", $tmp_ask_array['title']);
			$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
			$this->GetSearchData_obj->SetXmlData($tmp_xml);
			$tmp_list = $this->GetSearchData_obj->GetList();
			if(is_array($tmp_list)) {
				sort($tmp_list);
				$tmp_list = array_splice($tmp_list, 0, 5);
				foreach ($tmp_list as $k => &$v) {
					$v['adsname'] = nl2br($v['adsname']);
					$v['desc'] = nl2br($v['desc']);
					$v['url'] = '/id/'.$v['ID'];
					$v['TITLE'] = mb_substr(strip_tags($v['TITLE']), 0, 45, 'utf8');
				}
			}
			#print_r($tmp_list);
			$this->ViewObj->ask = $tmp_list;
			echo $this->ViewObj->render('/question_ok.phtml');
		}
		else if($tpl=='okhd') {
			$tmp_memeber_cookie = $this->MemberObj->getCookie();
			$this->MemberObj->add_ask_chg($tmp_memeber_cookie['uid']);
			$tmp_memeber_cookie['pwd'] = $pwd = $this->_getParam('pwd');
			if($this->_getParam('flag')) unset($tmp_memeber_cookie['pwd']);
			$username = $tmp_memeber_cookie['username'];
			setcookie('passed','ok',time()+3600,'/','.9939.com');
			session_start();
			$_SESSION['aa']='';
			$arr = array('name'=>$username,'pwd'=>$pwd);
			echo json_encode($arr);
			exit;
		}
		elseif ($tpl == 'showlogin'){
			$id = intval($this->_getParam('id'));
			$so = intval($this->_getParam('so'));
			$get_callback=$this->getRequest()->getParam('jsoncallback');
			Zend_Loader::loadClass('Friend',MODELS_PATH);
			$this->fri = new Friend();
			$fan = $this->fri->friendask($id);//好友请求
			$arn = $this->fri->askreplay($id);//问答回复
			$fnn = $this->fri->friendnews($id);//好友消息
			$temp1 = "<span><a href=".HOME_9939_URL."news>好友请求 ".$fan."条</a></span><span><a href=".HOME_9939_URL."ask>问答回复 ".$arn."条</a></span><span><a href=".HOME_9939_URL."news>好友消息 ".$fnn."条</a></span>";
			$temp2 = "<li><span><a href=/Ask>我的提问</a></span></li><li><span><a href=/Ask>我要提问</a></span></li><li><span><a href=".HOME_9939_URL."news>站内消息</a></span></li><li><span><a href=".HOME_9939_URL."user/do/do/edit>完善资料</a></span></li>";

			//热点推荐
			include('/home/web/htsns-9939-com/data/data_adsplace_70.php');
			if($_ADSGLOBAL['70']){
				foreach ($_ADSGLOBAL['70'] as $k=>$v){
					$rdtj .= "<li><a href=".$v['linkurl'].">".$v['adsname']."</a></li>";
				}
			}
			$arr = array('w_up'=>$temp2,'w_right'=>$temp1,'w_hot'=>$rdtj);
			if($so) echo $get_callback."('".json_encode($arr)."')";
			else echo json_encode($arr);
			exit;
		}
		$tag_id = $this->_getParam('tag_id');
		if($tag_id) {
			echo '<script>var __tmp_data=$("#'. $tag_id .'").html();if(!__tmp_data){__tmp_data="请在此输入补充提问内容";}$("#add_content").val(__tmp_data);</script>';
		}
	}


	public function loadhomeAction() {
		$url = $this->_getParam('url');
		#echo HOME_9939_URL . $url;
		$data = file_get_contents(HOME_9939_URL . $url);
		#$data = file_get_contents('/home/web/htsns-9939-com/application/views/scripts/home/friend_add.phtml');
		echo $data;
	}

	/*
	 * 好友请求发送HOME.9939.com
	 * 页面显示与home相同；调页面请到home
	 * @author: kxgsy163@163.com
	 */
	public function homeAction () {
		$url = $this->_getParam('url');
		$note = $this->getRequest()->getParam('note');
		$uid = $_COOKIE['member_uID'];
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Accept-language: en\r\n" .
		              "Cookie: member_uID=$uid\r\n"
		  )
		);
		$context = stream_context_create($opts);
		$data = '/note/'. $this->_getParam('note');
		$data = file_get_contents(HOME_9939_URL . $url. $data, false, $context);
		if($data == 'error') {
			Zend_Adver_Js::Goback('好友添加失败！');
		} else {
			$data = (preg_replace("/.*?alert\((.*?)\).*/is", "\\1", preg_replace('/[\'|"]/', '', $data)));
			Zend_Adver_Js::Goback($data);
		}
	}

	private function save() {
		header("Cache-Control: no-cache, must-revalidate");
		$param = $this->_getParam('info');
		print_r($param);
		if(strlen($param['title'])<2) {		#提问标题
			echo 1;
			exit;
		}

		if(!preg_replace("/\s/", '', $param['content'])) {		#问题描述
			echo 2;
			exit;
		}
		if(!$param['classid']) {			#科室
			echo 3;
			exit;
		}



		//验证字符串是否含有非法词语
		Zend_Loader::loadClass('CheckData',MODELS_PATH);
		if(!CheckData::isSafeStr($param['title'])) {
			Zend_Adver_Js::Goback('标题中含有非法词语！');
			exit;
		}
		if(!CheckData::isSafeStr($param['content'])) {
			Zend_Adver_Js::Goback('问题内容中含有非法词语！');
			exit;
		}

		$sToken = $this->_getParam('token');
		Zend_Loader::loadClass('Asession',MODELS_PATH);
		$session_obj = new Asession();
		$aSession = $session_obj->Get_Token_Session();
		if($aSession==$sToken&&$sToken!=''){
			$aToken = new Zend_Session_Namespace('token');
			if($aToken->isLocked()){
				$aToken->unlock();
				$aToken->token='';
				$aToken->lock();
			}
		}else{
			echo '<script>alert("请不要重复添加!");location.replace("http://ask.9939.com");</script>';
			exit;
		}

		$tmp_member_cookie = $this->MemberObj->getCookie();
		if(!$tmp_member_cookie['uid']) {
			if($this->_getParam('register')) { #账户注册
				#echo 'hello2';
				$newMember = 2;
				$username = $this->_getParam('mail');
				$username = preg_replace("/\s/", '', $username);
				if(!preg_match("/.*@.*\.\w*/", $username)) {
					Zend_Adver_Js::Goback('用户名请使用email!');
				}
				#echo 'hello3';
				$pwd = rand(100000, 999999);
				$tmp_new_user['username'] = $username;
				$tmp_new_user['password'] = $pwd;
				$tmp_new_user['dateline'] = time();
				$tmp_new_user['nickname'] = substr($username, 0, strpos($username, '@'));
				Zend_Loader::loadClass('Register',MODELS_PATH);
				$tmp_register_obj = new Register();
				$where = ' username=\''. $username .'\'';
				if($this->MemberObj->get_one($where)) {
					Zend_Adver_Js::Goback('用户名与其他用户重复');
					exit;
				}
				#echo 'hello4';
				$tmp_new_user_id = $tmp_register_obj->register_member($tmp_new_user);
				#echo '---';
				if($tmp_new_user_id) {
					#echo 'hello5';
					if(!($this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
						Zend_Adver_Js::Goback('注册失败!');
						exit;
					}
					#echo 'hello66';
				} else {
					Zend_Adver_Js::Goback('注册失败!');
					exit;
				}
				#echo 'hello6';
			} elseif($this->_getParam('login')){ #账户登录
				$username = $this->_getParam('username');
				$pwd = $this->_getParam('pwd');
				#echo 'hello7';
				if(!($this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
					Zend_Adver_Js::Goback('登录失败');
					exit;
				}
				#echo 'hello8';
				$sFlag = true;
			}else{
				$newMember = 1;
				$aChars = range('a','z');
				shuffle($aChars);
				$sImpchars = implode('',$aChars);
				$username = substr($sImpchars,0,5);
				$where = ' username=\''. $username .'\'';
				while($this->MemberObj->get_one($where)) {
					shuffle($aChars);
					$sImpchars = implode('',$aChars);
					$username = substr($sImpchars,0,5);
					$where = ' username=\''. $username .'\'';
				}
				$pwd = rand(100000, 999999);
				$tmp_new_user['username'] = $username;
				$tmp_new_user['password'] = $pwd;
				$tmp_new_user['dateline'] = time();
				$tmp_new_user['nickname'] = $username;
				//print_r($tmp_new_user);
				Zend_Loader::loadClass('Register',MODELS_PATH);
				$tmp_register_obj = new Register();
				//echo "ok1";
				$tmp_new_user_id = $tmp_register_obj->register_member($tmp_new_user);
				
				//echo 'loading';
				if($tmp_new_user_id) {
					//echo 'hello5';
					if(!($this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
						Zend_Adver_Js::Goback('注册失败!');
						exit;
					}
					else 
					{
						//echo "ok";
					}
					#echo 'hello66';
				}
			}
			$where = ' username=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\'';
			$tmp_member_cookie = $this->MemberObj->get_one($where);
		}
		$param['userid'] = $tmp_member_cookie['uid'];
		$param['status'] = 0;
		$aCheck = $this->MemberObj->getBySql("SELECT `checkemail` FROM `member` WHERE uid=".$param['userid']);
		$iCheck = $aCheck[0]['checkemail'];

		// xzxin 2010-05-10
		if($_SERVER['HTTP_CDN_SRC_IP'])
			$param['ip'] = $_SERVER['HTTP_CDN_SRC_IP'];
		elseif($_SERVER['HTTP_X_FORWARDED_FOR'])
			$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$param['ip'] = $_SERVER['REMOTE_ADDR'];

		//$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

//print_r($param);exit;
		$id = $this->AskObj->add($param);
              // echo $id;exit;
		setcookie('askid',$id,time()+3600,'/','.9939.com');
		
	
		if($param['point']>0) {		#悬赏扣除积分
			$tmp_user_credit = $tmp_member_cookie['credit']-$param['point'];
			($tmp_user_credit>0) or ($tmp_user_credit=0);
			$this->MemberObj->Edit(array('credit'=>$tmp_user_credit), $param['userid']);
			$this->MemberObj->ssetcookie('member_credit', $tmp_user_credit);
		}
		$str = $this->Credit_obj->updatespacestatus("get","ask_pub"); 	#积分
		$this->ViewObj->newMember = $newMember;
		$this->ViewObj->iCheck = $iCheck;
		$this->ViewObj->askid = $id;
		$this->ViewObj->tmp_new_user = $tmp_new_user;

		/**
		$tmp_kw = preg_replace("/\s|&nbsp;/", "20%", $param['title']);
		$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
		$this->GetSearchData_obj->SetXmlData($tmp_xml);
		$tmp_list = $this->GetSearchData_obj->GetList();
		if(is_array($tmp_list)) {
			sort($tmp_list);
			$tmp_list = array_splice($tmp_list, 0, 5);
			foreach ($tmp_list as $k => &$v) {
				$v['adsname'] = nl2br($v['adsname']);
				$v['desc'] = nl2br($v['desc']);
				$v['url'] = '/id/'.$v['ID'];
				$v['TITLE'] = mb_substr(strip_tags($v['TITLE']), 0, 45, 'utf8');
			}
		}
		$this->ViewObj->ask = $tmp_list;
		**/
		$backUrl = $this->_getParam("backurl");
		if($backUrl) {
			Zend_Adver_Js::helpJsRedirect(urldecode($backUrl),0,"提问成功！");
			exit;
		}

		echo $this->ViewObj->render('/question_cross.phtml');
	}



	public function testAction () {
		#echo $tmp_kw = $this->_getParam('kw');
		$tmp_kw = '一点都很郁闷';
		$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
		$this->GetSearchData_obj->SetXmlData($tmp_xml);
		$tmp_list            = $this->GetSearchData_obj->GetList();
		print_R($tmp_list);
	}

	/**
	*
	* 用于问题相关修改： 问题补充 追加悬赏
	*/
	private function edit() {
		$param = $this->_getParam('info');
		$tmp_member_cookie = $this->MemberObj->getCookie();
		$tmp_ask_info = $this->AskObj->get_one($param['id']);
		if($param['point']) {
			$param['point'] = $param['point'] + $tmp_ask_info['point'];
			$tmp_user_credit = $tmp_member_cookie['credit']-$param['point'];
			($tmp_user_credit>0) or ($tmp_user_credit=0);
			$this->MemberObj->Edit(array('credit'=>$tmp_user_credit), $tmp_ask_info['userid']);
			$this->MemberObj->ssetcookie('member_credit', $tmp_user_credit);
		}
		#$tmp_ask_info['userid'] = $tmp_member_cookie['uid'] = 7; 		#测试专用

		if($tmp_ask_info['userid'] == $tmp_member_cookie['uid']) {
			$param['addtime'] = time();
			$this->AskObj->edit($param);
			Zend_Adver_Js::helpJsRedirect("/id/".$param['id'], 1, '提交成功！!');
		} else {
			Zend_Adver_Js::helpJsRedirect("/id/".$param['id'], 1, '对不起！权限不够！');
		}
	}







	/**
	*
	* 遍历数组：返回2维数组
	* @param $array array 多维数组则过滤否则直接返回
	*/
	private function trimArray($array = array()) {
		foreach($array as $k => $v) {
			if(is_array($v)) {
				unset($array[$k]);
			}
		}
		return $array;
	}


	/**
	*
	* @author kxgsy163@163.com
	* 对象实例化:用于会员详细信息
	*/
	private function newObj($type=1) {
		$type = ($type==1) ? 1 : 2;
		$this->memberDetailObj = new $this->{'member_detail_'.$type}();								#实例化会员详细信息类
	}


	/*
	* 话题底层计数
	*/
	public function addvnAction(){
		$sAskid = $this->getRequest()->getParam('askid');
		$this->AskObj->Add_viewnum($sAskid);
	}



	private function getAds($id=0) {
		if(!$id) return array();
		$dir = '/home/web/htsns-9939-com/data/';
		$filename = 'data_adsplace_'.$id.'.php';
		if(file_exists($dir .$filename)) {
			//echo $dir.$filename."<br>";
			@require($dir.$filename);
			return $_ADSGLOBAL[$id];
		}
		echo 'h';
		return array();
	}


	/**
	 *
	 * 获取问题栏目
	 */
	private function getClassByAsk($tmp=array()) {
		$classid = $tmp['classid'];
        $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($classid),1);
		$array = array();
		while (true) {
			if(!$classid) break;
			if($CATEGORY[$classid]) {
				$array[] = '<a href="/classid/'. $classid .'" target="_blank">'. $CATEGORY[$classid]['name'] .'</a>';
				$classid = $CATEGORY[$classid]['pID'];
			} else {
				break;
			}
		}
		if($array) {
			$tmp['class_html'] = implode(' >> ', array_reverse($array));
		}
	}

}
?>