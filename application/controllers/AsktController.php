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
Zend_Loader::loadClass('Listask',MODELS_PATH);
Zend_Loader::loadClass('QLib_Message_SMS_Client',LIBRARY_PATH);

class AsktController extends Zend_Controller_Action
{
	private $ViewObj;
	private $AskObj = '';
	private $AnswerObj = '';
	private $MemberObj = '';
	private $memberDetailObj = '';
	private $member_detail_1 = 'MemberDetail';
	private $member_detail_2 = 'MemberDetailDoctor';
	private $Credit_obj = '';
	private $list_obj = '';


	public function init() {
		$this->ViewObj = Zend_Registry::get('view');
		$this->AskObj = new Ask();
		$this->AnswerObj = new Answer();
		$this->MemberObj = new Member();
		//加载搜索类
		Zend_Loader::loadClass('GetSearchData',MODELS_PATH);
		$this->GetSearchData_obj = new GetSearchData();

		$this->Credit_obj = new Credit();		#积分类
		$this->list_obj=new Listask();
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
			$this->ViewObj->answerUid  = $this->_getParam("doc_id")>0?$this->_getParam("doc_id"):0;
			$this->ViewObj->token  = $sToken;
			$this->ViewObj->list = $tmp_list;
			$this->ViewObj->num  = $tmp_num;
			$this->ViewObj->kw   = str_replace("%20"," ",$tmp_kw);
			$this->ViewObj->askcontent = $tmp_askcontent;
			$this->ViewObj->user_info = $this->MemberObj->getCookie();
			echo $this->ViewObj->render('/question_testt.phtml');
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}

	public function loadcatAction() {
		$id = $this->_getParam('id');
		$id = intval($id);
		$tmp_keshi_obj = new Keshi();
		$where = $tmp_keshi_obj->getValue('pid') .'=\''. $id .'\'';
		$tmp_keshi_list_array = $tmp_keshi_obj->getList($where);
		$tmp_keshi_option_str = '';
		foreach ($tmp_keshi_list_array as $k => $v) {
			$tmp_keshi_option_str .= '<option value="'. $v[$tmp_keshi_obj->getValue('primary')] .'" class="'. $v[$tmp_keshi_obj->getValue('pid')] .'">'. $v[$tmp_keshi_obj->getValue('name')] .'</optioni>';
		}
		echo $tmp_keshi_option_str;
	}
  //生成验证码
    public function verifyAction(){
        $imgWidth = 70;
        $imgHeight = 30;
        $authimg = imagecreate($imgWidth, $imgHeight);
        $bgColor = ImageColorAllocate($authimg, 255, 255, 255);
        $fontfile = APP_ROOT."/heiti.ttf";
        $white = imagecolorallocate($authimg, 234, 185, 95);
        imagearc($authimg, 150, 8, 20, 20, 75, 170, $white);
        imagearc($authimg, 180, 7, 50, 30, 75, 175, $white);
        imageline($authimg, 20, 20, 180, 30, $white);
        imageline($authimg, 20, 18, 170, 50, $white);
        imageline($authimg, 25, 50, 80, 50, $white);
        $noise_num = 400;
        $line_num = 4;
        imagecolorallocate($authimg, 0xff, 0xff, 0xff);
        $rectangle_color = imagecolorallocate($authimg, 0xAA, 0xAA, 0xAA);
        $noise_color = imagecolorallocate($authimg, 0x00, 0x00, 0x00);
        $font_color = imagecolorallocate($authimg, 0x00, 0x00, 0x00);
        $line_color = imagecolorallocate($authimg, 0x00, 0x00, 0x00);
        for ($i = 0; $i < $noise_num; $i++) {
            imagesetpixel($authimg, mt_rand(0, $imgWidth), mt_rand(0, $imgHeight), $noise_color);
        }
        for ($i = 0; $i < $line_num; $i++) {
            imageline($authimg, mt_rand(0, $imgWidth), mt_rand(0, $imgHeight), mt_rand(0, $imgWidth),
                mt_rand(0, $imgHeight), $line_color);
        }
       
        $str = $this->getVerifyCode();
        $session = new Zend_Session_Namespace("verifyt");
        $session->unlock();
        $session->verify=md5(strtoupper($str));
        $session->lock();
        ImageTTFText($authimg, 20, 0, 8, 25, $font_color, $fontfile, $str);
        ImagePNG($authimg);
        ImageDestroy($authimg);
       
    }
    private function getVerifyCode($length=4){
        $letters = 'bcdfghjklmnpqrstvwxyz';
		$vowels = 'aeiou';
		$code = '';
		for($i = 0; $i < $length; ++$i)
		{
			if($i % 2 && mt_rand(0,10) > 2 || !($i % 2) && mt_rand(0,10) > 9)
				$code.=$vowels[mt_rand(0,4)];
			else
				$code.=$letters[mt_rand(0,20)];
		}

		return $code;
    }
    /*
		验证验证码
    */
    public function checkverifyAction(){
    	
    	$session = new Zend_Session_Namespace("verifyt");
        $session->lock();
        $yzm=$session->verify;
        $session->unsetAll();
        if($yzm!=md5(strtoupper($this->getRequest()->getParam('verify')))){
        	echo json_encode(array("verify"=>"0"));
        	exit();
        }else{
        	echo json_encode(array("verify"=>"1"));
        	exit();
        }

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
		//if(!$tmp_ask_info)  // xzxin 2009-12-04
		//{
			/*echo "<script>alert('对不起，您访问的问题不存在！');location.href='/';</script>";*/
			//exit;
		//}

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


		$this->getClassByAsk($tmp_ask_info);
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
		$order = ' addtime asc';
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
			/*echo "<script>alert('对不起，您访问的问题不存在！');location.href='/';</script>";*/
			//exit;
		//}

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


		$this->getClassByAsk($tmp_ask_info);
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
    //wpg 验证数据合法性
    private function savecheck(){

        header("Cache-Control: no-cache, must-revalidate"); 
		Zend_Loader::loadClass('CheckData',MODELS_PATH);
		$tmp_member_cookie = $this->MemberObj->getCookie();
        echo json_encode(array("error"=>$tmp_member_cookie['credit']));
        	exit();
		//验证ip 林原 2010-09-26
		if(!CheckData::isSafeIp()) {
		    echo json_encode(array("error" => "你的ip被限制！"));
			exit;
		}
		
		$param = $this->_getParam('info');
		
		if(strlen($param['title'])<2) {		#提问标题
		    echo json_encode(array("error" => "标题长度必须大于2"));
			exit;
		}
		if(!preg_replace("/\s/", '', $param['content'])) {		#问题描述
		    echo json_encode(array("error" => "请填写问题描述"));
			exit;
		}
		if(!$param['classid']) {			#科室
		    echo json_encode(array("error" => "请选择科室"));
			exit;
		}

		if($param['title'] == '请输入您的问题标题'|| $param['title'] == '填写你的问题,立即为你解答') {
			$param['title'] = mb_substr($param['content'],0,10,'utf-8').'...';
		}
        
		if(!$tmp_member_cookie['uid']) {
			if($this->_getParam('register')=="1") { #账户注册
				
			} elseif($this->_getParam('register')=="0"){ #账户登录
				
			}else{
        		$ip = $_SERVER['REMOTE_ADDR'];
			    $where = ' ip=\''. $ip .'\'';
                $db=$this->MemberObj->getAdapter();
                $data=$db->fetchRow("select * from member where ip='{$ip}'  ORDER BY `dateline` desc limit 0,1 ");
                $fenzhong=60*5;
                if(($data['dateline']+$fenzhong)>=time()){
                     echo json_encode(array("error" => "游客无法连续提问"));
			         exit;
                }
			}
		}else{
            $db=$this->MemberObj->getAdapter();
            $data=$db->fetchRow("select * from wd_ask where title='{$param['title']}' AND userid={$tmp_member_cookie['uid']}  ORDER BY `ctime` desc limit 0,1 ");
            $fenzhong=60*5;
            if(($data['ctime']+$fenzhong)>=time()){
                 echo json_encode(array("error" => "请不要连续提问相同问题"));
		         exit;
            }
		}
        $creditzong=0;
        if($param['point']>0) {		#悬赏扣除积分
            $creditzong+=$param['point'];
            if($creditzong>$tmp_member_cookie['credit']){
                echo json_encode(array("error" => "积分不够,需要".$creditzong.",您现有积分".($tmp_member_cookie['credit']!=""?$tmp_member_cookie['credit']:0)));
			    exit;
            }
        }
        if($param['hiddenname']=="1") {		#悬赏扣除积分

            $creditzong+=2;
            if($creditzong>$tmp_member_cookie['credit']){
                echo json_encode(array("error" => "积分不够,需要".$creditzong.",您现有积分".($tmp_member_cookie['credit']!=""?$tmp_member_cookie['credit']:0)));
			    exit;                   
            }
            
        }
        if($param['broadcast']=="1") {		#悬赏扣除积分
            $creditzong+=20;
            if($creditzong>$tmp_member_cookie['credit']){
                echo json_encode(array("error" => "积分不够,需要".$creditzong.",您现有积分".($tmp_member_cookie['credit']!=""?$tmp_member_cookie['credit']:0)));
			    exit;   
               
            }
        }
		//验证字符串是否含有非法词语
		if(!CheckData::isSafeStr($param['title'])) {
		    echo json_encode(array("error" => "标题中含有非法词语！"));
			exit;
		}
		if(!CheckData::isSafeStr($param['content'])) {
		    echo json_encode(array("error" => "问题内容中含有非法词语！"));
			exit;
		}
		if(!CheckData::isSafeStr($param['help'])) {
		    echo json_encode(array("error" => "寻求帮助中含有非法词语！"));
			exit;
		}
        echo json_encode(array("error" => "1"));
		exit;
		  
    }
     //wpg 验证数据合法性快速提问
    private function savecheckkuaisu(){
        header("Cache-Control: no-cache, must-revalidate"); 
		Zend_Loader::loadClass('CheckData',MODELS_PATH);
		$tmp_member_cookie = $this->MemberObj->getCookie();
		
		//验证ip 林原 2010-09-26
		if(!CheckData::isSafeIp()) {
		    echo json_encode(array("error" => "你的ip被限制！"));
			exit;
		}
		
		$param = $this->_getParam('info');
		
		if(strlen($param['title'])<2) {		#提问标题
		    echo json_encode(array("error" => "标题长度必须大于2"));
			exit;
		}
		if(!preg_replace("/\s/", '', $param['content'])) {		#问题描述
		    echo json_encode(array("error" => "请填写问题描述"));
			exit;
		}
		if(!$param['classid']) {			#科室
		    echo json_encode(array("error" => "请选择科室"));
			exit;
		}

		if($param['title'] == '请输入您的问题标题'|| $param['title'] == '填写你的问题,立即为你解答') {
			$param['title'] = mb_substr($param['content'],0,10,'utf-8').'...';
		}
		

		//验证字符串是否含有非法词语
		if(!CheckData::isSafeStr($param['title'])) {
		    echo json_encode(array("error" => "标题中含有非法词语！"));
			exit;
		}
		if(!CheckData::isSafeStr($param['content'])) {
		    echo json_encode(array("error" => "问题内容中含有非法词语！"));
			exit;
		}
      
        $tmp_member_cookie = $this->MemberObj->getCookie();
		if(!$tmp_member_cookie['uid']) {
			if($this->_getParam('register')=="1") { #账户注册
                $ip = $_SERVER['REMOTE_ADDR'];
			    $where = ' ip=\''. $ip .'\'';
                $db=$this->MemberObj->getAdapter();
                $data=$db->fetchRow("select * from member where ip='{$ip}'  ORDER BY `dateline` desc limit 0,1 ");
                $fenzhong=60*5;
                if(($data['dateline']+$fenzhong)>=time()){
                     echo json_encode(array("error" => "请不要连续注册"));
			         exit;
                }
				$newMember = 2;
				$username = $this->_getParam('username');
				$username = preg_replace("/\s/", '', $username);
				if(!preg_match("/.*@.*\.\w*/", $username)) {
                    echo json_encode(array("error" => "用户名请使用email!"));
			        exit;
				}
				$pwd = rand(100000, 999999);
				$tmp_new_user['username'] = $username;
				$tmp_new_user['password'] = $this->_getParam('pwd');
				$tmp_new_user['dateline'] = time();
				$tmp_new_user['nickname'] = substr($username, 0, strpos($username, '@'));
                //保存生成密码
				$tmp_new_user['zdpassword'] = $pwd;
				Zend_Loader::loadClass('Register',MODELS_PATH);
				$tmp_register_obj = new Register();
				$where = ' username=\''. $username .'\'';
				if($this->MemberObj->get_one($where)) {
                    echo json_encode(array("error" => "用户名与其他用户重复!"));
			        exit;
				}
			} else if($this->_getParam('register')=="0"){ #账户登录
				$username = $this->_getParam('username');
				$pwd = $this->_getParam('pwd');
				if(!($this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
                    echo json_encode(array("error" => "用户名与密码不正确!"));
			        exit;
				}
			}else{
        		$ip = $_SERVER['REMOTE_ADDR'];
			    $where = ' ip=\''. $ip .'\'';
                $db=$this->MemberObj->getAdapter();
                $data=$db->fetchRow("select * from member where ip='{$ip}'  ORDER BY `dateline` desc limit 0,1 ");
                $fenzhong=60*5;
                if(($data['dateline']+$fenzhong)>=time()){
                     echo json_encode(array("error" => "游客无法连续提问"));
			         exit;
                }
			}
		}else{
            $db=$this->MemberObj->getAdapter();
            $data=$db->fetchRow("select * from wd_ask where title='{$param['title']}' AND userid={$tmp_member_cookie['uid']}  ORDER BY `ctime` desc limit 0,1 ");
            $fenzhong=60*5;
            if(($data['ctime']+$fenzhong)>=time()){
                 echo json_encode(array("error" => "请不要连续提问相同问题"));
		         exit;
            }
		}
        echo json_encode(array("error" => "1"));
		exit;
		  
    }

    private function keydes($askid){
    	$result=$this->AskObj->getList("id=".$askid);
        $classid = $result['0']['classid'];
        if($result['0']['class_level3'] != 0){
            $classid = 'dis_'.$result['0']['classid'];
        }
        $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($classid));
    	$res['key']=$CATEGORY[$classid]['keywords'];
    	$res['des']=$CATEGORY[$classid]['description'];
    	return $res;
    }

     private function telmal($askid){
     	$tmp_user_cookie = $this->MemberObj->getCookie();
    	 $ask=$this->AskObj->getList("id=".$askid);   	
		$res['istel']=$ask['0']['ismobile'];
		$res['ismal']=$ask['0']['ismail'];
		$res['classid']=$ask['0']['classid'] ? $ask['0']['classid'] : '32';
		$res['tel']=$this->MemberObj->GetValue($tmp_user_cookie['uid'],'update_mobile');
		$res['mal']=$this->MemberObj->GetValue($tmp_user_cookie['uid'],'email'); 

		return $res;
    }

    private function latest($askid){
    	 $classid=$this->AskObj->getList("id=".$askid);   	
		$class=array($classid[0]['class_level3'],$classid[0]['class_level2'],$classid[0]['class_level1']);
		if ($classid['0']['classid']!='0') {
			foreach ($class as $k => $v) {
				if($v=='0') continue;
				$list=array();
				$where="classid=".$v." and answernum>0";
				$list=$this->list_obj->List_Ask($where, "id desc", "18","0");
				if (count($list)>18) break;
			}
		}else{
				$list=$this->list_obj->List_Ask("answernum>0", "ctime desc", "18","0");
		}
		
		return $list;
    }
	private function save() {
		header("Cache-Control: no-cache, must-revalidate"); 
		Zend_Loader::loadClass('CheckData',MODELS_PATH);
		$referfer = $_SERVER['HTTP_REFERER'];
		$looktime = substr($_COOKIE['ask_wktime'], 5, 10);
//                print_r($looktime);exit;
		$time = time() - $looktime;
		setcookie('ask_wktime',0,time()-86400,'/',APP_DOMAIN);
		/*if ($time <= 50 || $looktime < 0 || empty($_COOKIE['ask_wktime'])) {
			Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,"非法访问！");
			exit;
		}*/
		if (strstr($referfer, '.9939.com') === false) {
			Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,"非法访问！");
			exit;
		}
		//验证ip 林原 2010-09-26
		if(!CheckData::isSafeIp()) {
			Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,"你的ip被限制！");
			exit;
		}
		
		$param = $this->_getParam('info');
        $s_param = base64_encode(serialize($param));
        $ask_info_code = md5("ask_9939_qas_info"); 
        setcookie($ask_info_code, $s_param ,time()+2*60,'/',APP_DOMAIN);
//        var_dump($param);exit;
        //验证字符串是否含有非法词语
		Zend_Loader::loadClass('CheckData',MODELS_PATH);
        $re_error="error_return";
//        print_r($param['content']);exit;
                if(!CheckData::isSafeStr($param['content'])) {
        	$error="问题内容中含有非法词语！";
			setcookie($re_error, base64_encode(serialize($error)) ,time()+2*60,'/',APP_DOMAIN);
			Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,'');
			exit;
		}
		if(!is_array($param)){
			$error="提问内容不全，请重新填写";
			setcookie($re_error, base64_encode(serialize($error)) ,time()+2*60,'/',APP_DOMAIN);
            Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,'');
		}

		if(!preg_replace("/\s/", '', $param['content'])) {		#问题描述
			echo 2;
			exit;
		}
        $session = new Zend_Session_Namespace("verifyt");
        $session->lock();
        $yzm=$session->verify;
        $session->unsetAll();
        if($yzm!=md5(strtoupper($this->getRequest()->getParam('verifyt')))){
        //if($yzm!=md5($this->getRequest()->getParam('verifyt'))){
        	$error="验证码不正确！";
			setcookie($re_error, base64_encode(serialize($error)) ,time()+2*60,'/',APP_DOMAIN);
                Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,'');
                exit;
        }

		$checkcons = $this->clearstr($param['content']);
		if(!CheckData::isSafeStr($param['content']) || preg_match('/&#[0-9a-zA-Z]{5};/', $checkcons)) {
			$error="标题中含有非法词语！";
			setcookie($re_error, base64_encode(serialize($error)) ,time()+2*60,'/',APP_DOMAIN);
			Zend_Adver_Js::helpJsRedirect(ASK_URL."Asking/index/kw/",0,'');
			exit;
		}
                
		$sToken = $this->_getParam('token');
		Zend_Loader::loadClass('Asession',MODELS_PATH);
		$session_obj = new Asession();
		$aSession = $session_obj->Get_Token_Session();
		$aSession2 = $session_obj->Get_Token_Session2(); //林原 2010-09-25
		if($aSession==$sToken&&$sToken!=''){
			$aToken = new Zend_Session_Namespace('token');
			if($aToken->isLocked()){
				$aToken->unlock();
				$aToken->token='';
				$aToken->lock();
			}
		} elseif($aSession2==$sToken&&$sToken!=''){
			$aToken = new Zend_Session_Namespace('token');
			if($aToken->isLocked()){
				$aToken->unlock();
				$aToken->token2='';
				$aToken->lock();
			}
		} else{
			/*echo '<script>alert("请不要重复添加!");location.replace("http://ask.9939.com");</script>';*/
//			exit;
		}

		$islogin = 0;
		$tmp_member_cookie = $this->MemberObj->getCookie();
		if(!$tmp_member_cookie['uid']) {
			if($this->_getParam('register')=="1") { #账户注册
				$newMember = 2;
				$username = $this->_getParam('username');
				$username = preg_replace("/\s/", '', $username);
				if(!preg_match("/.*@.*\.\w*/", $username)) {
					Zend_Adver_Js::Goback('用户名请使用email!');
				}

				$tmp_new_user['username'] = $username;
				$tmp_new_user['password'] = $this->_getParam('pwd');
				$tmp_new_user['dateline'] = time();
				$tmp_new_user['nickname'] = substr($username, 0, strpos($username, '@'));
				Zend_Loader::loadClass('Register',MODELS_PATH);
				$tmp_register_obj = new Register();
				$where = ' username=\''. $username .'\'';
				if($this->MemberObj->get_one($where)) {
					Zend_Adver_Js::Goback('用户名与其他用户重复');
					exit;
				}
				$tmp_new_user_id = $tmp_register_obj->register_member($tmp_new_user);
                $tmp_member_cookie['uid']=$tmp_new_user_id;
				if($tmp_new_user_id) {
					if(!($this->MemberObj->checklogin(array('username'=>$username, 'password'=>$tmp_new_user['password'])))) {
						Zend_Adver_Js::Goback('注册失败!');
						exit;
					}
				} else {
					Zend_Adver_Js::Goback('注册失败!');
					exit;
				}
			} elseif($this->_getParam('register')=="0"){ #账户登录
				$username = $this->_getParam('username');
				$pwd = $this->_getParam('pwd');
				if(!($tmp_member_cookie['uid']=$this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
					Zend_Adver_Js::Goback('登录失败');
					exit;
				}
				$sFlag = true;
			}else{
			    $ip = $_SERVER['REMOTE_ADDR'];
			    $where = ' ip=\''. $ip .'\'';
                $db=$this->MemberObj->getAdapter();
                $data=$db->fetchRow("select * from member where ip='{$ip}'  ORDER BY `dateline` desc limit 0,1 ");
                $fenzhong=60*5;
                if(($data['dateline']+$fenzhong)>=time()){
                     Zend_Adver_Js::Goback('游客无法连续提问');
					exit;
                }
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
                //保存生成密码
				$tmp_new_user['zdpassword'] = $pwd;
                
				Zend_Loader::loadClass('Register',MODELS_PATH);
				$tmp_register_obj = new Register();
				$tmp_new_user_id = $tmp_register_obj->register_member($tmp_new_user);
                $tmp_member_cookie['uid']=$tmp_new_user_id;
				if($tmp_new_user_id) {
					if(!($this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
						Zend_Adver_Js::Goback('注册失败!');
						exit;
					}
				}
			}
            
       
		}else{
			$islogin = 1;
            $db=$this->MemberObj->getAdapter();
            $data=$db->fetchRow("select * from wd_ask where title='{$param['title']}' AND userid={$tmp_member_cookie['uid']}  ORDER BY `ctime` desc limit 0,1 ");
            $fenzhong=60*5;
            if(($data['ctime']+$fenzhong)>=time()){
                 Zend_Adver_Js::Goback('请不要连续提问相同问题');
					exit;
            }
		}


        $where = ' uid=\''.$tmp_member_cookie['uid'].'\'';
        
        $tmp_member_cookie = $this->MemberObj->get_one($where);
        //暂时去掉积分
//        $creditzong=0;
//        if($param['point']>0) {		#悬赏扣除积分
//            $creditzong+=$param['point'];
//            if($creditzong>$tmp_member_cookie['credit']){
//                Zend_Adver_Js::Goback('积分不够,需要'.$creditzong.',您现有积分 '.($tmp_member_cookie['credit']!=""?$tmp_member_cookie['credit']:0));
//            }
//        }
//        if($param['hiddenname']=="1") {		#悬赏扣除积分
//            $creditzong+=2;
//            if($creditzong>$tmp_member_cookie['credit']){
//                Zend_Adver_Js::Goback('积分不够,需要'.$creditzong.',您现有积分 '.($tmp_member_cookie['credit']!=""?$tmp_member_cookie['credit']:0));
//            }
//        }
//        if($param['broadcast']=="1") {		#悬赏扣除积分
//        
//            $creditzong+=20;
//            if($creditzong>$tmp_member_cookie['credit']){
//                Zend_Adver_Js::Goback('积分不够,需要'.$creditzong.',您现有积分 '.($tmp_member_cookie['credit']!=""?$tmp_member_cookie['credit']:0));
//            }
//        }
		$param['userid'] = $tmp_member_cookie['uid'];
		$param['status'] = 0;
		$iCheck = $tmp_member_cookie['checkemail'];
		if($_SERVER['HTTP_CDN_SRC_IP']){
			$param['ip'] = $_SERVER['HTTP_CDN_SRC_IP'];
		}elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
			$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$param['ip'] = $_SERVER['REMOTE_ADDR'];
        }
        
        $param['classid']=($param['class_level3']!=0) ? $param['class_level3'] : (($param['class_level2']!=0) ? $param['class_level2'] : $param['class_level1']);
		if ($param['class_level1']==15) {			
			if ($param['class_level2']==0) {
				$param['class_level1']==15;
			}else{
				$param['class_level1']=$param['class_level2'];
			$param['class_level2']=$param['class_level3'];
			$param['class_level3']=0;
			}
		}

		$param['ctime'] = time();
		$hsid = intval($this->_getParam('hsid'));
                //print_r($param);
        $class = $this->AskObj->triage($param);
//        var_dump($class);exit;
        if($class['class_level3']!='0'){
            $param['classid']= $class['class_level3'];
        }elseif ($class['class_level2']!='0') {
            $param['classid']= $class['class_level2'];
        }  else {
            $param['classid']= $class['class_level1'];
        }
        $param['class_level1'] = $class['class_level1'];
        $param['class_level2'] = $class['class_level2'];
        $param['class_level3'] = $class['class_level3'];
        $param['title'] = mb_substr($param['content'], 0, 20, utf8);
		$id = $this->AskObj->add($param);

		$param['id'] = $id;


	//	$this->addAnswerDefault($param); 禁止自动回答
		/*if($id > 0 && $hsid > 0) {
			$ksid = intval($this->_getParam('ksid'));
			$jyid = intval($this->_getParam('jyid'));
			$doc_id = intval($this->_getParam('doc_id'));
			$sid = $this->AskObj->intTab("wd_ask_hospital",array('wd_id'=>$id,'doctor_id'=>$param['answerUid'],'jiayuanID'=>$jyid,'hospital_id'=>$hsid,'keshi_id'=>$ksid,'wd_title'=>$param['title'],'addtime'=>$param['ctime']));			
		}*/



		setcookie('askid',$id,time()+3600,'/',APP_DOMAIN);
       
		setcookie($ask_info_code,  '',time()-2*60,'/',APP_DOMAIN);


        /*if($creditzong>0){
            $tmp_user_credit = $tmp_member_cookie['credit']-$creditzong;
            if($tmp_user_credit<0)$tmp_user_credit=0;
            $this->MemberObj->Edit(array('credit'=>$tmp_user_credit), $tmp_member_cookie['uid']);
            $this->MemberObj->ssetcookie('member_credit', $tmp_user_credit);
        }*/
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
		Zend_Adver_Js::helpJsRedirect(ASK_URL."askt/cross?id=".$id."&islogin=".$islogin);
		/**
       if($tmp_member_cookie['zdpassword']){
            Zend_Adver_Js::helpJsRedirect(ASK_URL."askt/cross?id=".$id);
       }else{
            Zend_Adver_Js::helpJsRedirect(ASK_URL."id/".$id);
       }
	   **/
        //
	}
    /**
     * 非注册用户更改用户密码
     */
    public function crossAction(){
        $act=$this->getRequest()->getParam('act');
        $tmp_user_cookie = $this->MemberObj->getCookie();
        if(!$tmp_user_cookie['uid']){
             if($act){
                echo json_encode(array("error" => "用户非法"));
                exit;
             }else{
                Zend_Adver_Js::helpJsRedirect(ASK_URL);
             }
        }       
        $where = ' uid=\''.$tmp_user_cookie['uid'].'\''; 
        $this->ViewObj->tmp_new_user = $this->MemberObj->get_one($where);
        if(!$this->ViewObj->tmp_new_user['zdpassword']){//防止重复更改
            if($act){
                echo json_encode(array("error" => "用户非法"));
                exit;
            }else{
               // Zend_Adver_Js::helpJsRedirect(ASK_URL);
            }
        }      
        if("update"==$act){
            $data['username']=$this->getRequest()->getParam('username');
            $data['password']=$this->getRequest()->getParam('password');
            $data['oldusername']=$this->getRequest()->getParam('oldusername');
            $data['oldpassword']=$this->getRequest()->getParam('oldpassword');
            if (($error = $this->check($data)) === true) {
                if($this->ViewObj->tmp_new_user['zdpassword']!=$data['oldpassword']){
                    echo json_encode(array("error" => "用户非法"));
                    exit;
                }
                if($this->ViewObj->tmp_new_user['username']!=$data['oldusername']){
                    echo json_encode(array("error" => "用户非法"));
                    exit;
                }
                $member=$this->MemberObj->get_one("username='{$data['username']}' or email='{$data['username']}'");
                if($member){
                    echo json_encode(array("error" => "用户名存在请更改"));
                    exit;
                }
                if(!$this->MemberObj->Edit(array('zdpassword'=>'yes','username'=>$data['username'],'password'=>md5($data['password'])), $tmp_user_cookie['uid'])){
                    echo json_encode(array("error" => "更改失败请稍侯在试"));
                    exit;
                }
                echo json_encode(array("error" => "1"));
                exit;
            }else{
                echo json_encode(array("error" => $error));
                exit;
            }
        }
        $id=$this->_getParam("id");
		$islogin=$this->_getParam("islogin");
        if(!$id){
            Zend_Adver_Js::helpJsRedirect(ASK_URL);
        }
        $this->ViewObj->askid = $id;
		$this->ViewObj->askislogin = $islogin;
		//提问问题的keywords，description
		$keydes=$this->keydes($id);
		$this->ViewObj->keywords=$keydes['key'];
		$this->ViewObj->description=$keydes['des'];
		//手机邮箱是否填写//提问疾病的classid
		$this->ViewObj->telmal=$this->telmal($id);
		//文章
		$this->ViewObj->list=$this->latest($id);
                $this->ViewObj->uid = $tmp_user_cookie['uid']; //返回用户id
                //唯一标识
                $asktoken = mt_rand(100000, 999999);
                $this->ViewObj->asktoken = $asktoken;
                setcookie('asktoken',md5('ask_9939_com_123abc_'.$asktoken),time()+3600,'/',APP_DOMAIN);
        echo $this->ViewObj->render('/question_success.phtml');        
    }

    public function sendmobileAction(){
    	
    	$aid = intval($this->_getParam('askid'));
        $tmp_user_cookie = $this->MemberObj->getCookie();
        $mobile=$this->getRequest()->getParam('mobile');
        if (!preg_match("/1[34578]{1}\d{9}$/", $mobile)) {
        	echo json_encode(array("msg"=>"0"));
        	exit;
        }
        $this->AskObj->ismobiles($aid,'1');
        echo json_decode($this->MemberObj->Edit(array("update_mobile"=>$mobile),$tmp_user_cookie['uid']));

    }
     public function sendemailAction(){
    	
    	$aid = intval($this->_getParam('askid'));
        $tmp_user_cookie = $this->MemberObj->getCookie();
        $email=$this->getRequest()->getParam('email');
         if (!preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/", $email)) {
        	echo json_encode(array("msg"=>"0"));
        	exit;
        }
        $this->AskObj->ismails($aid,'1');
        echo json_decode($this->MemberObj->Edit(array("email"=>$email),$tmp_user_cookie['uid']));

    }
    public function ismobileAction(){
        $aid = intval($this->_getParam('askid'));
        $tmp_user_cookie = $this->MemberObj->getCookie();
        $data['ismobile']=$this->getRequest()->getParam('ismobile');
        echo json_encode($this->AskObj->ismobiles($aid,$data['ismobile']));
    }
    public function ismailAction(){
        $aid = intval($this->_getParam('askid'));
        $tmp_user_cookie = $this->MemberObj->getCookie();
        $data['ismail']=$this->getRequest()->getParam('ismail');
        echo json_encode($this->AskObj->ismails($aid,$data['ismail']));
    }
    public function tomailAction(){
        $tmp_user_cookie = $this->MemberObj->getCookie();
        if(!$tmp_user_cookie['uid']){
            echo json_encode(array("error" => "用户非法"));
            exit;
        }
        $where = ' uid=\''.$tmp_user_cookie['uid'].'\''; 
        $tmp_user_cookie = $this->MemberObj->get_one($where);       
        $password=$this->getRequest()->getParam('password');
        $mail=$this->getRequest()->getParam('mail');
        $data['password']=$this->getRequest()->getParam('password');
        $data['mail']=$this->getRequest()->getParam('mail');
        if (($error = $this->checkmail($data)) === true) {
            if($tmp_user_cookie['password']!=md5($data['password'])){
                echo json_encode(array("error" => "密码错误"));
                exit;
            }
            if($tmp_user_cookie['email']!=""){
                echo json_encode(array("error" => "已经填写了认证邮箱"));
                exit;
            }
            $member=$this->MemberObj->get_one("email='{$data['mail']}' or username='{$data['mail']}'");
            if($member){
                echo json_encode(array("error" => "邮箱已存在请更改"));
                exit;
            }

            $miyao=rand(1000,9999).(time());
            if(!$this->MemberObj->Edit(array('rzpassword'=>$miyao,'email'=>$data['mail']), $tmp_user_cookie['uid'])){
                echo json_encode(array("error" => "更改失败请稍侯在试"));
                exit;
            }

            $this->tomail($data['mail'],ASK_URL."askt/renzheng?id={$tmp_user_cookie['uid']}&r=".md5($this->pwdEncode($miyao).$data['mail']));
            echo json_encode(array("error" => 1));
            exit;
        }else{
            echo json_encode(array("error" => $error));
            exit;
        }
    }
    //认证邮箱
    public function renzhengAction(){
        $uid=$this->getRequest()->getParam('id');
        $r=$this->getRequest()->getParam('r');
        if(!$uid||!$r){//参数不合法
            Zend_Adver_Js::helpJsRedirect("/",2,"认证链接地址不对,请重新申请");
            
        }
        $user=$this->MemberObj->get_one("uid='{$uid}'");
        if(!$user){//无用户
            Zend_Adver_Js::helpJsRedirect("/",2,"认证链接地址不对,请重新申请");
        }
        if(!$user['rzpassword']){//无认证标识
            Zend_Adver_Js::helpJsRedirect("/",2,"认证链接地址不对,请重新申请");
        }
        $miyao=$user['rzpassword'];
        if(md5($this->pwdEncode($miyao).$user['email'])!=$r){
            Zend_Adver_Js::helpJsRedirect("/",2,"认证链接地址不对,请重新申请");
        }
        if($this->MemberObj->Edit(array('rzpassword'=>'','checkemail'=>'1'), $uid)){
            $this->MemberObj->checklogin('',$msg,$uid);
            echo $this->ViewObj->render('/question_renzheng.phtml');    
        }else{
            Zend_Adver_Js::helpJsRedirect("/",2,"系统繁忙请过会刷新重试");
        }
        
    }
        //发送邮件
  //发送邮件
    private function tomail($email,$url){
        
		Zend_Loader::loadClass('smtp',MODELS_PATH);
		$smtpserver = "smtp.9939.com";//SMTP服务器smtp.163.com
		$smtpserverport =25;//SMTP服务器端口
		$smtpusermail = "thinkno-reply@9939.com";//SMTP服务器的用户邮箱
		$smtpemailto = $email;//发送给谁
		$smtpuser = "thinkno-reply@9939.com";//SMTP服务器的用户帐号
		$smtppass = "9939789!@#";//SMTP服务器的用户密码
		$mailsubject2 = '===网站会员邮箱认证===';//邮件主题
		$mailsubject = mb_convert_encoding($mailsubject2,"GB2312","UTF-8");//utf-8转换为bg2312邮件编码
		$mailbody2 = '<meta http-equiv="Content-Type" content="text/html; charset=GB2312" /><div  style="width:780px; margin:0 auto;">
	<div  style="border-bottom:1px solid #d6d6d6; height:34px; padding:34px 0 9px 22px;">
		<a href="http://www.9939.com"><img src="http://ask.9939.com/email/images/logo.gif" style="border:none;"/></a>
	</div>
	<div  style="padding:40px 22px 20px;">
		<strong>亲爱的'.$email.'， 您好！</strong>
		<p  style=" padding:0; margin:0;font-size:12px; margin:30px 0 38px;">
			感谢您注册久久健康社区会员，愿久久健康网伴随你永久健康
		</p>
		
		<div  style="font-size:14px; line-height:23px; padding-bottom:20px;background:url(http://ask.9939.com/email/images/message_bg.gif) no-repeat center bottom; padding-bottom:25px;">
			<p  style=" padding:0; margin:0;font-size:14px; margin-bottom:10px;">请点击以下连接认证您的邮箱。</p>
			<p  style=" padding:0; margin:0;width:520px; font-size:14px;">
			<a href="'.$url.'" style="text-decoration:underline;color:#0000fa;">'.$url.'</a>
</p>
			<p  style=" padding:0; margin:0;margin-top:35px;">(如果无法点击该URL链接地址，请将它复制并粘贴到浏览器的地址输入框，然后单击回车即可）</p>
            <p  style=" padding:0; margin:0;margin-top:35px;">如果您没有邮箱认证，请忽略此邮件</p>
            <p  style=" padding:0; margin:0;margin-top:35px;">会员中心 '.date("Y-m-d",time()).'</p>
		</div>
		<div  style="margin-top:15px; color:#0000fa;  font-size:12px;">
			<a href="http://www.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">去久久健康网</a>|<a href="http://ask.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">到久久问答提问</a>
		</div>
	</div>
</div>';
		//$mailbody2 = "您好!".$email."<BR>您的邮箱已经认证！点击<a href='http://www.9939.com' target='_blank'>这里</a> 进入99健康网";//邮件内容
		
        $mailbody = mb_convert_encoding($mailbody2,"GB2312","UTF-8");//utf-8转换为bg2312邮件编码

		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
		$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		$smtp->debug = false;//true是否显示发送的调试信息
		if($smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype)){
			
		}
	}
    //验证标识计算
    private function pwdEncode($miyao){
        $sjs=mb_substr(("".$miyao),0,4);
        $time=mb_substr(("".$miyao),4);
        $zonghe=$time+$sjs;
        $jishu=mb_substr(("".$zonghe),5,4);
        $cishu=mb_substr(("".$zonghe),9);
        if($cishu<1){
            $cishu=1;
        }
        $rebound=md5($zonghe);
        for($x=0;$x<$cishu;$x++){ 
            $rebound=md5($rebound.($jishu+$x));
        }
        return $rebound;
    }
       //验证数据合法性
    private function checkmail($data){
        if ($data['mail'] == "")
            return "请填写E－mail";
        if (!preg_match("/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/", $data['mail']))
            return "E－mail格式不正确";
            
        if ($data['password'] == "")
            return "请填写密码";
      
        return true;
    }
    //验证数据合法性
    private function check($data)
    {
        if ($data['username'] == "")
            return "请填写用户名";
        if (!preg_match("/^[\w]+$/", $data['username']))
            return "用户名只能是下划线，数字，字母";
            
        if ($data['password'] == "")
            return "请填写密码";
        if (strlen($data['password'])<6)
            return "密码长度不小于6个字符";
      
        return true;
    }
    
	/**
	* 添加用户默认答案
	*/
	private function addAnswerDefault($param=array()) {
		if(!$this->tmp_answer_default_array){
			$this->tmp_answer_default_array = $this->getDefaultUserAnswerList();
		}
		$tmp_ask_classid = $param['classid'];
		$info['userid'] = rand(16000, 17999) * 10;
		$info['askid'] = $param['id'];
		$info['content'] = $this->tmp_answer_default_array[$tmp_ask_classid];
		$info['addtime'] = time() + 45;

		//xzxin 2010-05-11
		$info['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

		if($info['content']) {
			$this->AnswerObj->addAnswer($info);
			$this->AskObj->editAnswerNum($info['askid']);
		}
	}
	/**
	* 获取默认用户答案列表
	*/
	function getDefaultUserAnswerList(){
		$row = 1;
		$handle = fopen(APP_DATA_PATH."/userAnswer.csv","r");
		$csvList = array();
		while ($data = fgetcsv($handle, 1000, ",")) {
			$num = count($data); 
			$row++;
			if(is_numeric($data[1])){
				$link = $data[3];
				$csvList[$data[1]] = str_replace($link,"<a href='{$link}'>{$link}</a>",$data[2]);
			} 
		} 
		fclose($handle);
		return $csvList;
    }
	public function ttAction(){
		set_time_limit(0);
		$begin = empty($_REQUEST['begin'])?0:intval($_REQUEST['begin']);
		$db = $this->AskObj->getAdapter();
		@ini_set('memory_limit','160M');
		//echo "总共：".$db->fetchOne("SELECT count(*) FROM wd_ask")."条记录。<br />"; 
		while($askList = $db->fetchAll("select * from wd_ask ORDER BY id limit {$begin},10")){
			foreach($askList AS $k => $v){
				$db->query("DELETE FROM wd_answer WHERE askid = {$v['id']} AND sort = 0"); 
				$this->addAnswerDefault($v);
			}
			file_put_contents(APP_ROOT."/slog/log.txt","已经更新到：第".$v["id"]."条\n",FILE_APPEND);
			$begin = $begin + 10;
			if($begin%50==0){
				echo $begin;
				exit;
				//header("Location: http://ask.9939v2.com/askt/useranswers/?begin={$begin}"); 
			} 
		}
		echo "更新完成!";
	}
	/****
	*	重新添加用户默认答案
	*	2011-5-10
	*   zxg
	*/
	public function useranswersAction(){
		set_time_limit(0);
		$begin = empty($_REQUEST['begin'])?0:intval($_REQUEST['begin']);
		$db = $this->AskObj->getAdapter();
		@ini_set('memory_limit','160M');
		echo "总共：".$db->fetchOne("SELECT count(*) FROM wd_ask")."条记录。<br />"; 
		while($askList = $db->fetchAll("select * from wd_ask ORDER BY id limit {$begin},100")){
			foreach($askList AS $k => $v){
				$db->query("DELETE FROM wd_answer WHERE askid = {$v['id']} AND sort = 0"); 
				$this->addAnswerDefault($v);
			}
			file_put_contents(APP_ROOT."/slog/log.txt","已经更新到：第".$v["id"]."条\n",FILE_APPEND);
			$begin = $begin + 100;
			if($begin%500==0){
				header("Location: http://ask.9939v2.com/askt/useranswers/?begin={$begin}"); 
			} 
		}
		echo "更新完成!"; 
	}

	public function testAction () {
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
			Zend_Adver_Js::helpJsRedirect("id/".$param['id'], 1, '提交成功！!');
		} else {
			Zend_Adver_Js::helpJsRedirect("id/".$param['id'], 1, '对不起！权限不够！');
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
	private function getClassByAsk(&$tmp=array()) {
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
	
	/**
	 *
	 * 清理字符串
	 */
	private function clearstr($str) {
		//去除空格
		$str = str_replace(array(' ','　','&nbsp;','&nbsp'),'', $str);
		if (empty($str)) {
			return $str;
		}
		//去除所有HTML标签
		$str = strip_tags($str);
		//去除标点符号
		$str = preg_replace('/\xa3([\xa1-\xfe])/e','',$str);
		//转义特殊字符
		$searcharr = array('>','<');
		$replacearr = array('&gt;','&lt;');
		$str = str_replace($searcharr, $replacearr, $str);
		return $str;
	}
    
    
    public function save_ask(){
        $req = $this->_getParam('ask');
        var_dump($req);
    }
    
    /**
     * 发从短信验证码
     * @return  array 
     */
    public function mobileCodeAction(){
        $mobile = $this->_getParam('mobile');
        $asktoken = $this->_getParam('asktoken');
        if($mobile!==""){
            //验证是唯一标示
            if($_COOKIE['asktoken'] != md5('ask_9939_com_123abc_'.$asktoken)){
                $asktoken = mt_rand(100000, 999999);
                setcookie('asktoken',md5('ask_9939_com_123abc_'.$asktoken),time()+3600,'/',APP_DOMAIN);
                echo json_encode(array("msg"=>'3',"flag"=>'系统超时，请重新获取！',"asktoken"=>$asktoken));
                exit;
            }
            $member = $this->AskObj->getMember($mobile);
            if($member){
                //判断当前用户在一个小时之内请求的次数，大于五次，返回失败！
                if(!$_COOKIE[$mobile.'-mobile_code_start_time']){
                    setcookie($mobile.'-mobile_code_start_time',time(),time()+3600,'/',APP_DOMAIN);
                    setcookie($mobile.'-mobile_code_count',1,time()+3600,'/',APP_DOMAIN);
                }
                $mobile_code_count = $_COOKIE[$mobile.'-mobile_code_count'] + 1;

                $start_time = $_COOKIE[$mobile.'-mobile_code_start_time'] + 3600;
                if($start_time >= time() && $mobile_code_count>3){
                    echo json_encode(array("msg"=>'访问过于平凡，请稍后请求！'));
                    exit;
                }
                setcookie($mobile.'-mobile_code_count',$mobile_code_count,time()+3600,'/',APP_DOMAIN);
                //生成新的网站验证标识
                $code = rand(100000,999999);
                $content = '您正在绑定手机号，校验码'.$code.'，请于30分钟内输入，工作人员不会索取，请勿泄漏';
                $ismobile = $this->AskObj->addMobile($mobile,$code);
                $mobile = QLib_Message_SMS_Client::send($mobile, $content);
                //重新生成验证标识唯一值
                $asktoken = mt_rand(100000, 999999);
                setcookie('asktoken',md5('ask_9939_com_123abc_'.$asktoken),time()+3600,'/',APP_DOMAIN);
                echo json_encode(array("msg"=>1,"asktoken"=>$asktoken,"count"=>$mobile_code_count));
                exit;
            }else{
                echo json_encode(array("msg"=>'手机号已经绑定，请重新输入！'));
                exit;
            }
        }else{
             echo json_encode(array("msg"=>'手机号不能为空，重新输入！'));
            exit;
        }
        
    }
    
    /**
     * 提问成功，绑定手机号
     */
    public function bindingMobileAction(){
        $uid = $_COOKIE['member_uID'];
        $mobile = $this->_getParam('mobile');
        $npwd = $this->_getParam('npwd');
        $code = $this->_getParam('code');

        if($uid =='' || $mobile=='' || $npwd=='' || $code==''){ //判断字段不能为空
            echo json_encode(array("msg"=>'字段不能为空，请重新填写！！'));
            exit;
        }else{
            if(!preg_match("/^1[3|4|5|7|8][0-9]\d{8}$/", $mobile)){ //判断手机号
                echo json_encode(array("msg"=>'手机号错误，请重新输入！！'));
                exit;
            }
            if(strlen($npwd)<6 || !preg_match("/^(\w){6,16}$/", $npwd)){ //判断密码
                echo json_encode(array("msg"=>'密码格式错误，请重新输入！！'));
                exit;
            }
            $user = $this->AskObj->getMember($mobile); //判断当前手机号是否已经被绑定
            if(!$user){
                echo json_encode(array("msg"=>'手机号已经被绑定，请重新输入！！'));
                exit;
            }
            
            $getcode = $this->AskObj->mobileCode($mobile); //根据手机号，查询数据库短信验证码
            if($getcode['code']==$code){
                if($getcode['addtime']<(time()+1800)){
                    //修改用户信息
                    $password = md5($npwd);
                    $set = array (
                        'mobile' => $mobile,
                        'checkmobile'=>1,
                        'password'=>$password,
                        'is_binding_mobile'=>1,
                        'zdpassword'=>$npwd
                    );
                    $result = $this->AskObj->updateMember($uid,$set);  
                    echo json_encode(array("msg"=>1));
                    exit;
                }else{
                    echo json_encode(array("msg"=>'验证码超时，请重新获取！'));
                    exit;
                }
            }else{
                echo json_encode(array("msg"=>'验证码输入错误，请重新获取！'));
                exit;
            }
        }
    }
    
    /**
     *  跳转用户信息
     */
    public function tiaozhuanAction(){
        $uid = $_COOKIE['member_uID']; //获取用户id
        $act=$this->getRequest()->getParam('act');
        $tmp_user_cookie = $this->MemberObj->getCookie();
        if(!$uid){
             if($act){
                echo json_encode(array("error" => "用户非法"));
                exit;
             }else{
                Zend_Adver_Js::helpJsRedirect(ASK_URL);
             }
        }
        $member = $this->AskObj->getMember_2($uid);
        if($member){
            $username = $member['username'];
            $pwd = $member['zdpassword'];
            if(!($tmp_member_cookie['uid']=$this->MemberObj->checklogin(array('username'=>$username, 'password'=>$pwd)))) {
                    Zend_Adver_Js::Goback('登录失败');
                    exit;
            }
            $this->ViewObj->user = $tmp_user_cookie['uid']; //返回用户id
            $this->ViewObj->tmp_new_user = $member; //返回用户id
            //唯一标识
            $asktoken = mt_rand(100000, 999999);
            $this->ViewObj->asktoken = $asktoken;
            setcookie('asktoken',md5('ask_9939_com_123abc_'.$asktoken),time()+3600,'/',APP_DOMAIN);
            echo $this->ViewObj->render('/question_success_2.phtml'); 
        }else{
            echo'<script>alert("用户不存在");</script>';
            exit;
        }
    }
    
    /**
     * 根据当前用户修改用户名密码
     */
    public function updateMemberAction(){
        $uid = $_COOKIE['member_uID']; //获取用户id
        $username = $this->_getParam('username');
        $password = $this->_getParam('password');
        $passwords = $this->_getParam('passwords');
        if($username=="" || $password=="" || $passwords==""){
            echo json_encode(array("msg"=>'用户名不能为空'));
            exit;
        }
        if(strlen($password)<6 || !preg_match("/^(\w){6,16}$/", $password)){ //判断密码
            echo json_encode(array("msg"=>'密码格式错误，请重新输入！！'));
            exit;
        }
        if($password != $passwords){
            echo json_encode(array("msg"=>'密码不一致，请重新输入！！'));
            exit;
        }
        $user = $this->AskObj->getMemberOne($username,$uid);
        if(!empty($user)){
            echo json_encode(array("msg"=>'用户名已经存在，请重新填写！！'));
            exit;
        }
        //根据用户直接修改用户名和密码，然后登陆
        $paw = md5($password);
        $data = array(
            'username'=>$username,
            'password'=>$paw,
            'zdpassword'=>$password,
            'is_update_password'=>1,
            'nickname'=>$username
        );
        $member = $this->AskObj->updateMember($uid,$data);
        if($member){
            if(!($tmp_member_cookie['uid']=$this->MemberObj->checklogin(array('username'=>$username, 'password'=>$password)))) {
                    Zend_Adver_Js::Goback('登录失败');
                    exit;
            }
            echo json_encode(array("msg"=>1));
            exit;
        }else{
            echo json_encode(array("msg"=>'修改失败,请重新填写用户信息!'));
            exit;
        }
    }
    
    /**
     * 判断用户名是否存在 ajax请求
     * return msg =1 不存在 
     */
    public function getMemberAction(){
        $username = $this->_getParam('username');
        if(empty($username)){
             echo json_encode(array("msg"=>'用户名不能为空，请重新填写！！'));
             exit;
        }
        $uid = $_COOKIE['member_uID']; //获取用户id
        $user = $this->AskObj->getMemberOne($username,$uid);
        if(!empty($user)){
            echo json_encode(array("msg"=>'用户名已经存在，请重新填写！！'));
            exit;
        }else{
            echo json_encode(array("msg"=>'1'));
            exit;
        }
    }
}
