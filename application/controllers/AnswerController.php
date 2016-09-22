<?php

/**
 * ##############################################
 * @FILE_NAME :manage_PicController.php
 * ##############################################
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
 * ==============================================
 * @Desc : 相册管理
 * ==============================================
 */
Zend_Loader::loadClass('Answer', MODELS_PATH);
Zend_Loader::loadClass('Member', MODELS_PATH);
Zend_Loader::loadClass('Ask', MODELS_PATH);
Zend_Loader::loadClass('Credit', MODELS_PATH); #加载积分类
Zend_Loader::loadClass('Answermember', MODELS_PATH);

class AnswerController extends Zend_Controller_Action {

    private $AnswerObj = '';
    private $AnswermemberObj = '';
    private $MemberObj = '';

    /**
     * Enter description here ...
     * @var Ask
     */
    private $AskObj = '';
    private $Credit_obj = '';

    public function init() {
        $this->ViewObj = Zend_Registry::get('view');
        $this->AnswerObj = new Answer();
        $this->MemberObj = new Member();
        $this->AskObj = new Ask();
        $this->Credit_obj = new Credit();  #积分类
        $this->AnswermemberObj = new Answermember();
        parent::init();
    }

    public function indexAction() {
//		echo "维护中";
        try {
            ini_set('max_execution_time', 0);
            $param = $this->_getParam('answer');
            $tmp_user_cookie = $this->MemberObj->getCookie();
//                        print_r($tmp_user_cookie);
//                        exit;
            $reg = false; //注册
            $session = new Zend_Session_Namespace("verify");
            $session->lock();
            $yzm = $session->verify;
            $session->unsetAll();
            if (!$tmp_user_cookie['uid']) {
                if ($this->_getParam('register1')) { #账户注册
                    $reg = true;
                    $username = $this->_getParam('mail');
                    $pwd = rand(100000, 999999);
                    $tmp_new_user['username'] = $username;
                    $tmp_new_user['password'] = $pwd;
                    $tmp_new_user['dateline'] = time();
                    $tmp_new_user['nickname'] = substr($username, 0, strpos($username, '@'));
                    Zend_Loader::loadClass('Register', MODELS_PATH);
                    $tmp_register_obj = new Register();
                    $where = ' username=\'' . $username . '\'';
                    if ($this->MemberObj->get_one($where)) {
//						Zend_Adver_Js::Goback('回复失败！帐号重复!'); 
                        echo('回复失败！帐号重复!');
                        exit;
                    }
                    if ($tmp_register_obj->register_member($tmp_new_user)) {
                        if (!($this->MemberObj->checklogin(array('username' => $username, 'password' => $pwd)))) { //登录
//							Zend_Adver_Js::Goback('回复失败！'); 
                            echo('回复失败！');
                            exit;
                        }
                    } else {
//						Zend_Adver_Js::Goback('回复失败！'); 
                        echo('回复失败！');
                        exit;
                    }
                } else { #账户登录
                    $username = $this->_getParam('username');
                    $pwd = $this->_getParam('pwd');
                    if (!($this->MemberObj->checklogin(array('username' => $username, 'password' => $pwd)))) {
//						Zend_Adver_Js::Goback('回复失败！用户名或密码错误！'); 
                        echo('回复失败！用户名或密码错误！');
                        exit;
                    }
                }
                #$tmp_user_cookie = $this->MemberObj->getCookie();
            }

            if ($yzm != md5(strtoupper($this->getRequest()->getParam('verify')))) {
                //if($yzm!=md5($this->getRequest()->getParam('verify'))){
                $contentnew = $param['content'];
                setcookie('content', $contentnew, time() + 60, '/', APP_DOMAIN);

                $suggestnew = $param['suggest'];
                setcookie('suggest', $suggestnew, time() + 60, '/', APP_DOMAIN);

//                Zend_Adver_Js::Goback("验证码不正确");
//                exit;
            }
            //$where = ' username=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\'';
            //$where = ' username=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\' OR username_old=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\'';	  // xzxin2010-05-31	

            $where = ' uid=\'' . $tmp_user_cookie['uid'] . '\'';
            $tmp_user_cookie = $this->MemberObj->get_one($where);
            $param['userid'] = $tmp_user_cookie['uid'];
            $userType = $tmp_user_cookie['uType'];
            $isVip = $tmp_user_cookie['isVip'];
            if($userType == 1 || $userType == 3){
                echo '非医生用户不可以回答。';
                exit;
            }
            
            if($isVip == 2){
                echo '您的账号是住站医生状态，暂时未通过医生资质审核，请联系网站客服。';
                exit;
            }elseif($isVip == 0){
                echo '您的账号目前处于待审核状态，请耐心等候。';
                exit; 
            }
            
            ############不可对  自己提的问题  做回复 处理 ###################
            $result = $this->AskObj->get_one($param['askid']);
            //var_dump($result);
            if ($result['userid'] == $param['userid']) {
//				Zend_Adver_Js::Goback('抱歉！不可以对自己的问题回复！！');
                echo('抱歉！不可以对自己的问题回复！！');
            }
            ############不可对  自己提的问题  做回复 处理 ###################

            $point_get_with_answer = false;
            if ($param['point'] > 0) {
                $point_get_with_answer = true;
                unset($param['point']);
            }
            #print_r($tmp_user_cookie);exit;
            //验证字符串是否为空 LinYuan
            if (!trim($param['content'])) {
//				Zend_Adver_Js::Goback('内容不能为空！');
                echo('内容不能为空！');
                exit;
            }

            Zend_Loader::loadClass('CheckData', MODELS_PATH);
            //验证ip 林原 2010-09-26
            if (!CheckData::isSafeIp()) {
//				Zend_Adver_Js::Goback("你的ip被限制！");
                echo("你的ip被限制！");
                exit;
            }
            //验证字符串是否含有非法词语 LinYuan
            if (!CheckData::isSafeStr($param['content'])) {
                setcookie("content", $param['content'], time() + 180, "/", APP_DOMAIN);
//				Zend_Adver_Js::Goback('内容中含有非法词语！');
                echo('内容中含有非法词语！');
                exit;
            }


            //添加回复顺序 LinYuan
            $row = $this->AnswerObj->getList('askid=' . $param['askid'], 'sort desc', 1, 0);
            if ($row) {
                $param['sort'] = $row[0]['sort'] + 1;
            } else {
                $param['sort'] = 1;
            }

            
            
            foreach( $row as $k=>$val ){
                if( $tmp_user_cookie['uid'] == $val['userid'] ){
                    echo '您已经回答过该问题，请不要重复回答。';
                    exit;
                }
            }
            
            
            
            // xzxin 2010-05-10  //[HTTP_CDN_SRC_IP] => 124.42.107.54
            if ($_SERVER['HTTP_CDN_SRC_IP'])
                $param['ip'] = $_SERVER['HTTP_CDN_SRC_IP'];
            elseif ($_SERVER['HTTP_X_FORWARDED_FOR'])
                $param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else
                $param['ip'] = $_SERVER['REMOTE_ADDR'];

            //$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];


            if ($tmp_user_cookie['uType'] == "2") {
                //获取当前问题的总回答数，超过2条禁止回复
                $num = $this->AnswerObj->getAnswerNum("askid=" . $param['askid']);
                if ($num >= 2) {
                    echo '当前问题已有2人回复，请回复别的问题。';
                    exit;
                }

                if (($id = $this->AnswerObj->addAnswer($param))) {
                    //付费医生用户记录 //wpg
                    if ($tmp_user_cookie['uType'] == "2" && $tmp_user_cookie['isVip'] == "1") {
                        Zend_Loader::loadClass('WageAnswer', MODELS_PATH);
                        $WageAnswer = new WageAnswer();
                        $WageAnswer->addWageAsk($id, $tmp_user_cookie['uid']);
                    }
                    //////////////////////
                    //发送互动邮件
                    $db = $this->AnswerObj->getAdapter();
                    $askdata = $db->fetchRow("select userid,title,ismobile,ismail from wd_ask where id={$param['askid']}");
                    $memberdata = $db->fetchRow("select email,checkemail,checkmobile,mobile from member where uid={$askdata['userid']}");
                    if ($memberdata['checkemail'] == 1 && $askdata['ismail'] == 1) {
                        $this->tomail($memberdata['email'], $askdata['title'], ASK_URL . "id/" . $param['askid']);
                    }
                    if ($memberdata['checkmobile'] == 1 && $askdata['ismobile'] == 1) {
                        //调用发短信接口   $memberdata['mobile']
                        if ($memberdata['mobile']) {
                            $sms_path = dirname(dirname(dirname(__FILE__)));
                            require_once($sms_path . "/include/lib_sms.php");
                            $reg_sMsg = "尊敬的久久健康网用户你好,你在本站提问的问题已有回复 [久久健康网]";
                            sendsms($memberdata['mobile'], $reg_sMsg);
                        }
                    }
                    if ($point_get_with_answer) { #回复悬赏问题 获得积分
                        $str = $this->Credit_obj->updatespacestatus("get", "ask_reward");
                    }

                    $uName = ($tmp_user_cookie['uType'] == 1 ? 'common' : 'doctor'); #积分
                    $str = $this->Credit_obj->updatespacestatus("get", "ask_" . $uName . "_reply"); //健康问答_大众会员回复 #积分
                    //				die("...");       
                    $this->AskObj->editAnswerNum($param['askid']);

                    if ($result['answernum'] == 0) { #问答回复数：>=1 自动加上一条回复：#time:2009-11-17 #kxgsy163@163.com
                        //$this->addAnswerDefault($result);禁止自动回答
                    }



                    if ($reg) { //注册
                        $this->ViewObj->askid = $param['askid'];
                        $this->ViewObj->username = $username;
                        $this->ViewObj->pwd = $pwd;

                        echo $this->ViewObj->render('/answer_cross.phtml');
                        exit;
                    } else {
                        //设置cookie5分钟失效，5分钟内禁止在此回答
                        setcookie("last_answer" . $param['askid'], time(), time() + 5 * 60, '/', APP_DOMAIN);

                        //更新医生的回答总数
//                        $tmp = $this->MemberObj->GetValue($tmp_user_cookie['uid'], 'totalanswer');
//                        $totalanswer = $tmp['totalanswer'] + 1;
                        $tmp = $this->AnswerObj->numRows('userid='.$tmp_user_cookie['uid']);
                        $totalanswer = $tmp + 1;
                        $totalanswerArr = array('totalanswer' => $totalanswer);
                        
                        $cacheName = 'member_'.$tmp_user_cookie['uid'];
                        $arr = array();
                        QLib_Cache_Client::setUserCache('member', $cacheName, $arr, 0);
                        if ($this->MemberObj->Edit($totalanswerArr, $tmp_user_cookie['uid'])) {
                            echo 'success';
                            exit;
                        }
                        //Zend_Adver_Js::helpJsRedirect("/id/". $param['askid'], 0, '回复成功！');
                        //Zend_Adver_Js::helpJsRedirect("/id/". $param['askid']);
                    }
                    //$this->ViewObj->url = 'http://ask.9939.com/id/'.$param['askid'];
                    //echo $this->ViewObj->render('/answer_ok.phtml');
                } else {
                    echo 'not ok';
                    exit;
                }
            } else {
                if (($id = $this->AnswermemberObj->add($param))) {
                    //付费医生用户记录 //wpg
                    if ($tmp_user_cookie['uType'] == "2" && $tmp_user_cookie['isVip'] == "1") {
                        Zend_Loader::loadClass('WageAnswer', MODELS_PATH);
                        $WageAnswer = new WageAnswer();
                        $WageAnswer->addWageAsk($id, $tmp_user_cookie['uid']);
                    }
                    //////////////////////
                    //发送互动邮件
                    $db = $this->AnswermemberObj->getAdapter();
                    $askdata = $db->fetchRow("select userid,title,ismobile,ismail from wd_ask where id={$param['askid']}");
                    $memberdata = $db->fetchRow("select email,checkemail,checkmobile,mobile from member where uid={$askdata['userid']}");
                    if ($memberdata['checkemail'] == 1 && $askdata['ismail'] == 1) {
                        $this->tomail($memberdata['email'], $askdata['title'], ASK_URL . "id/" . $param['askid']);
                    }
                    if ($memberdata['checkmobile'] == 1 && $askdata['ismobile'] == 1) {
                        //调用发短信接口   $memberdata['mobile']
                        if ($memberdata['mobile']) {
                            $sms_path = dirname(dirname(dirname(__FILE__)));
                            require_once($sms_path . "/include/lib_sms.php");
                            $reg_sMsg = "尊敬的久久健康网用户你好,你在本站提问的问题已有回复 [久久健康网]";
                            sendsms($memberdata['mobile'], $reg_sMsg);
                        }
                    }
                    if ($point_get_with_answer) { #回复悬赏问题 获得积分
                        $str = $this->Credit_obj->updatespacestatus("get", "ask_reward");
                    }

                    $uName = ($tmp_user_cookie['uType'] == 1 ? 'common' : 'doctor'); #积分
                    $str = $this->Credit_obj->updatespacestatus("get", "ask_" . $uName . "_reply"); //健康问答_大众会员回复 #积分
                    //				die("...");       
                    $this->AskObj->editAnswerNum($param['askid']);

                    if ($result['answernum'] == 0) { #问答回复数：>=1 自动加上一条回复：#time:2009-11-17 #kxgsy163@163.com
                        //$this->addAnswerDefault($result);禁止自动回答
                    }



                    if ($reg) { //注册
                        $this->ViewObj->askid = $param['askid'];
                        $this->ViewObj->username = $username;
                        $this->ViewObj->pwd = $pwd;
                        echo $this->ViewObj->render('/answer_cross.phtml');
                        exit;
                    } else {
                        //Zend_Adver_Js::helpJsRedirect("/id/". $param['askid']);
                        echo 'success';
                        exit;
                    }
                } else {
                    echo 'not ok';
                    exit;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    public function indexoldAction() {
//		echo "维护中";
        try {
            ini_set('max_execution_time', 0);
            $param = $this->_getParam('answer');
            $tmp_user_cookie = $this->MemberObj->getCookie();
//                        print_r($tmp_user_cookie);
//                        exit;
            $reg = false; //注册
            $session = new Zend_Session_Namespace("verify");
            $session->lock();
            $yzm = $session->verify;
            $session->unsetAll();
            if (!$tmp_user_cookie['uid']) {
                if ($this->_getParam('register1')) { #账户注册
                    $reg = true;
                    $username = $this->_getParam('mail');
                    $pwd = rand(100000, 999999);
                    $tmp_new_user['username'] = $username;
                    $tmp_new_user['password'] = $pwd;
                    $tmp_new_user['dateline'] = time();
                    $tmp_new_user['nickname'] = substr($username, 0, strpos($username, '@'));
                    Zend_Loader::loadClass('Register', MODELS_PATH);
                    $tmp_register_obj = new Register();
                    $where = ' username=\'' . $username . '\'';
                    if ($this->MemberObj->get_one($where)) {
//						Zend_Adver_Js::Goback('回复失败！帐号重复!'); 
                        echo('回复失败！帐号重复!');
                        exit;
                    }
                    if ($tmp_register_obj->register_member($tmp_new_user)) {
                        if (!($this->MemberObj->checklogin(array('username' => $username, 'password' => $pwd)))) { //登录
//							Zend_Adver_Js::Goback('回复失败！'); 
                            echo('回复失败！');
                            exit;
                        }
                    } else {
//						Zend_Adver_Js::Goback('回复失败！'); 
                        echo('回复失败！');
                        exit;
                    }
                } else { #账户登录
                    $username = $this->_getParam('username');
                    $pwd = $this->_getParam('pwd');
                    if (!($this->MemberObj->checklogin(array('username' => $username, 'password' => $pwd)))) {
//						Zend_Adver_Js::Goback('回复失败！用户名或密码错误！'); 
                        echo('回复失败！用户名或密码错误！');
                        exit;
                    }
                }
                #$tmp_user_cookie = $this->MemberObj->getCookie();
            }

            if ($yzm != md5(strtoupper($this->getRequest()->getParam('verify')))) {
                //if($yzm!=md5($this->getRequest()->getParam('verify'))){
                $contentnew = $param['content'];
                setcookie('content', $contentnew, time() + 60, '/', APP_DOMAIN);

                $suggestnew = $param['suggest'];
                setcookie('suggest', $suggestnew, time() + 60, '/', APP_DOMAIN);

//                Zend_Adver_Js::Goback("验证码不正确");
//                exit;
            }
            //$where = ' username=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\'';
            //$where = ' username=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\' OR username_old=\''. ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) .'\'';	  // xzxin2010-05-31	

            $where = ' uid=\'' . $tmp_user_cookie['uid'] . '\'';
            $tmp_user_cookie = $this->MemberObj->get_one($where);
            $param['userid'] = $tmp_user_cookie['uid'];
            $userType = $tmp_user_cookie['uType'];
            $user_group_status = $tmp_user_cookie['group_status'];
            if($userType == 1 || $userType == 3){
                echo '非医生用户不可以回答。';
                exit;
            }
            
            if($user_group_status !== 1){
                if($user_group_status == 2){
                    echo '您的账号暂时未通过医生资质审核，请联系网站客服。';
                    exit;
                }else{
                    echo '您的账号目前处于待审核状态，请耐心等候。';
                    exit;                    
                }
            }
            
            ############不可对  自己提的问题  做回复 处理 ###################
            $result = $this->AskObj->get_one($param['askid']);
            //var_dump($result);
            if ($result['userid'] == $param['userid']) {
//				Zend_Adver_Js::Goback('抱歉！不可以对自己的问题回复！！');
                echo('抱歉！不可以对自己的问题回复！！');
            }
            ############不可对  自己提的问题  做回复 处理 ###################

            $point_get_with_answer = false;
            if ($param['point'] > 0) {
                $point_get_with_answer = true;
                unset($param['point']);
            }
            #print_r($tmp_user_cookie);exit;
            //验证字符串是否为空 LinYuan
            if (!trim($param['content'])) {
//				Zend_Adver_Js::Goback('内容不能为空！');
                echo('内容不能为空！');
                exit;
            }

            Zend_Loader::loadClass('CheckData', MODELS_PATH);
            //验证ip 林原 2010-09-26
            if (!CheckData::isSafeIp()) {
//				Zend_Adver_Js::Goback("你的ip被限制！");
                echo("你的ip被限制！");
                exit;
            }
            //验证字符串是否含有非法词语 LinYuan
            if (!CheckData::isSafeStr($param['content'])) {
                setcookie("content", $param['content'], time() + 180, "/", APP_DOMAIN);
//				Zend_Adver_Js::Goback('内容中含有非法词语！');
                echo('内容中含有非法词语！');
                exit;
            }


            //添加回复顺序 LinYuan
            $row = $this->AnswerObj->getList('askid=' . $param['askid'], 'sort desc', 1, 0);
            if ($row) {
                $param['sort'] = $row[0]['sort'] + 1;
            } else {
                $param['sort'] = 1;
            }


            // xzxin 2010-05-10  //[HTTP_CDN_SRC_IP] => 124.42.107.54
            if ($_SERVER['HTTP_CDN_SRC_IP'])
                $param['ip'] = $_SERVER['HTTP_CDN_SRC_IP'];
            elseif ($_SERVER['HTTP_X_FORWARDED_FOR'])
                $param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else
                $param['ip'] = $_SERVER['REMOTE_ADDR'];

            //$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];


            if ($tmp_user_cookie['uType'] == "2") {
                //获取当前问题的总回答数，超过2条禁止回复
                $num = $this->AnswerObj->getAnswerNum("askid=" . $param['askid']);
                if ($num > 2) {
                    echo '当前问题已有2人回复，请回复别的问题。';
                    exit;
                }

                if (($id = $this->AnswerObj->add($param))) {
                    //付费医生用户记录 //wpg
                    if ($tmp_user_cookie['uType'] == "2" && $tmp_user_cookie['isVip'] == "1") {
                        Zend_Loader::loadClass('WageAnswer', MODELS_PATH);
                        $WageAnswer = new WageAnswer();
                        $WageAnswer->addWageAsk($id, $tmp_user_cookie['uid']);
                    }
                    //////////////////////
                    //发送互动邮件
                    $db = $this->AnswerObj->getAdapter();
                    $askdata = $db->fetchRow("select userid,title,ismobile,ismail from wd_ask where id={$param['askid']}");
                    $memberdata = $db->fetchRow("select email,checkemail,checkmobile,mobile from member where uid={$askdata['userid']}");
                    if ($memberdata['checkemail'] == 1 && $askdata['ismail'] == 1) {
                        $this->tomail($memberdata['email'], $askdata['title'], ASK_URL . "id/" . $param['askid']);
                    }
                    if ($memberdata['checkmobile'] == 1 && $askdata['ismobile'] == 1) {
                        //调用发短信接口   $memberdata['mobile']
                        if ($memberdata['mobile']) {
                            $sms_path = dirname(dirname(dirname(__FILE__)));
                            require_once($sms_path . "/include/lib_sms.php");
                            $reg_sMsg = "尊敬的久久健康网用户你好,你在本站提问的问题已有回复 [久久健康网]";
                            sendsms($memberdata['mobile'], $reg_sMsg);
                        }
                    }
                    if ($point_get_with_answer) { #回复悬赏问题 获得积分
                        $str = $this->Credit_obj->updatespacestatus("get", "ask_reward");
                    }

                    $uName = ($tmp_user_cookie['uType'] == 1 ? 'common' : 'doctor'); #积分
                    $str = $this->Credit_obj->updatespacestatus("get", "ask_" . $uName . "_reply"); //健康问答_大众会员回复 #积分
                    //				die("...");       
                    $this->AskObj->editAnswerNum($param['askid']);

                    if ($result['answernum'] == 0) { #问答回复数：>=1 自动加上一条回复：#time:2009-11-17 #kxgsy163@163.com
                        //$this->addAnswerDefault($result);禁止自动回答
                    }



                    if ($reg) { //注册
                        $this->ViewObj->askid = $param['askid'];
                        $this->ViewObj->username = $username;
                        $this->ViewObj->pwd = $pwd;


                        /* 获取相关问题//加载搜索类

                          Zend_Loader::loadClass('GetSearchData',MODELS_PATH);
                          $GetSearchData_obj = new GetSearchData();
                          $tmp_kw = preg_replace("/\s|&nbsp;/", "20%", $result['title']);
                          $tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
                          $GetSearchData_obj->SetXmlData($tmp_xml);
                          $tmp_list = $GetSearchData_obj->GetList();
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
                          $this->ViewObj->ask = $tmp_list; //相关问题
                         */
                        /* 获取相关问题 */


                        echo $this->ViewObj->render('/answer_cross.phtml');
                        exit;
                    } else {
                        //设置cookie5分钟失效，5分钟内禁止在此回答
                        setcookie("last_answer" . $param['askid'], time(), time() + 5 * 60, '/', APP_DOMAIN);

                        //更新医生的回答总数
                        $tmp = $this->MemberObj->GetValue($tmp_user_cookie['uid'], 'totalanswer');
                        $totalanswer = $tmp['totalanswer'] + 1;
                        $totalanswerArr = array('totalanswer' => $totalanswer);
                        if ($this->MemberObj->Edit($totalanswerArr, $tmp_user_cookie['uid'])) {
                            echo 'success';
                            exit;
                        }
                        //Zend_Adver_Js::helpJsRedirect("/id/". $param['askid'], 0, '回复成功！');
                        //Zend_Adver_Js::helpJsRedirect("/id/". $param['askid']);
                    }
                    //$this->ViewObj->url = 'http://ask.9939.com/id/'.$param['askid'];
                    //echo $this->ViewObj->render('/answer_ok.phtml');
                } else {
                    echo 'not ok';
                    exit;
                }
            } else {
                if (($id = $this->AnswermemberObj->add($param))) {
                    //付费医生用户记录 //wpg
                    if ($tmp_user_cookie['uType'] == "2" && $tmp_user_cookie['isVip'] == "1") {
                        Zend_Loader::loadClass('WageAnswer', MODELS_PATH);
                        $WageAnswer = new WageAnswer();
                        $WageAnswer->addWageAsk($id, $tmp_user_cookie['uid']);
                    }
                    //////////////////////
                    //发送互动邮件
                    $db = $this->AnswermemberObj->getAdapter();
                    $askdata = $db->fetchRow("select userid,title,ismobile,ismail from wd_ask where id={$param['askid']}");
                    $memberdata = $db->fetchRow("select email,checkemail,checkmobile,mobile from member where uid={$askdata['userid']}");
                    if ($memberdata['checkemail'] == 1 && $askdata['ismail'] == 1) {
                        $this->tomail($memberdata['email'], $askdata['title'], ASK_URL . "id/" . $param['askid']);
                    }
                    if ($memberdata['checkmobile'] == 1 && $askdata['ismobile'] == 1) {
                        //调用发短信接口   $memberdata['mobile']
                        if ($memberdata['mobile']) {
                            $sms_path = dirname(dirname(dirname(__FILE__)));
                            require_once($sms_path . "/include/lib_sms.php");
                            $reg_sMsg = "尊敬的久久健康网用户你好,你在本站提问的问题已有回复 [久久健康网]";
                            sendsms($memberdata['mobile'], $reg_sMsg);
                        }
                    }
                    if ($point_get_with_answer) { #回复悬赏问题 获得积分
                        $str = $this->Credit_obj->updatespacestatus("get", "ask_reward");
                    }

                    $uName = ($tmp_user_cookie['uType'] == 1 ? 'common' : 'doctor'); #积分
                    $str = $this->Credit_obj->updatespacestatus("get", "ask_" . $uName . "_reply"); //健康问答_大众会员回复 #积分
                    //				die("...");       
                    $this->AskObj->editAnswerNum($param['askid']);

                    if ($result['answernum'] == 0) { #问答回复数：>=1 自动加上一条回复：#time:2009-11-17 #kxgsy163@163.com
                        //$this->addAnswerDefault($result);禁止自动回答
                    }



                    if ($reg) { //注册
                        $this->ViewObj->askid = $param['askid'];
                        $this->ViewObj->username = $username;
                        $this->ViewObj->pwd = $pwd;
                        echo $this->ViewObj->render('/answer_cross.phtml');
                        exit;
                    } else {
                        //Zend_Adver_Js::helpJsRedirect("/id/". $param['askid']);
                        echo 'success';
                        exit;
                    }
                } else {
                    echo 'not ok';
                    exit;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }    
    
    //发送邮件
    private function tomail($email, $title, $url) {
        Zend_Loader::loadClass('smtp', MODELS_PATH);
        $smtpserver = "smtp.9939.com"; //SMTP服务器smtp.163.com
        $smtpserverport = 25; //SMTP服务器端口
        $smtpusermail = "thinkno-reply@9939.com"; //SMTP服务器的用户邮箱
        $smtpemailto = $email; //发送给谁
        $smtpuser = "thinkno-reply@9939.com"; //SMTP服务器的用户帐号
        $smtppass = "9939789!@#"; //SMTP服务器的用户密码
        $mailsubject2 = '===问题已经回复==='; //邮件主题
        $mailsubject = mb_convert_encoding($mailsubject2, "GB2312", "UTF-8"); //utf-8转换为bg2312邮件编码
        $mailbody2 = '<meta http-equiv="Content-Type" content="text/html; charset=GB2312" /><div  style="width:780px; margin:0 auto;">

	<div  style="padding:40px 22px 20px;">
		<p  style=" padding:0; margin:0;font-size:12px; margin:30px 0 38px;">
			尊敬的客户您好！您提问的[<a href="' . $url . '">' . $title . '</a>]问题已经回复，请您点击
            <br>
            <a href="' . $url . '">' . $url . '</a>
            <br><br>
            	感谢您对久久健康网的关注 祝您和家人身体健康！
		</p>
		
	
		<div  style="margin-top:15px; color:#0000fa;  font-size:12px;">
			<a href="http://www.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">去久久健康网</a>|<a href="http://ask.9939.com" target="_blank" style=" margin:0 5px;font-size:12px;color:#0000fa; text-decoration:none;">到久久问答提问</a>
		</div>
	</div>
</div>';
        //$mailbody2 = "您好!".$email."<BR>您的邮箱已经认证！点击<a href='http://www.9939.com' target='_blank'>这里</a> 进入99健康网";//邮件内容

        $mailbody = mb_convert_encoding($mailbody2, "GB2312", "UTF-8"); //utf-8转换为bg2312邮件编码

        $mailtype = "HTML"; //邮件格式（HTML/TXT）,TXT为文本邮件
        $smtp = new smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass); //这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug = false; //true是否显示发送的调试信息
        if ($smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype)) {
            
        }
    }

    public function doAction() {
        $tmp_act = $this->getRequest()->getParam('do');
        $say_ok_array = array('looklist');
        if (!$this->MemberObj->isLogin()) {
            //Zend_Adver_Js::Goback('您没有操作此功能的权限');
            //exit;
        }

        if (in_array($tmp_act, get_class_methods(get_class($this)))) {
            $this->{strtolower($tmp_act)}();
        } else {
            Zend_Adver_Js::helpJsRedirect("/user", 1, '错误的访问地址');
        }
    }

    public function testAction() {
        echo $this->ViewObj->render('/answer_cross.phtml');
    }

    private function edit() {
        $param = $this->_getParam('info');
        $tmp_member_cookie = $this->MemberObj->getCookie();
        $tmp_answer_info = $this->AnswerObj->get_one($param['id']);

        #$tmp_answer_info['userid'] = $tmp_member_cookie['uid'] = 8; #测试使用

        if ($tmp_answer_info['userid'] != $tmp_member_cookie['uid']) {
            Zend_Adver_Js::Goback('修改失败！');
            exit;
        }
        if ($this->AnswerObj->edit($param)) {
            Zend_Adver_Js::helpJsRedirect('/ask/show/id/' . $tmp_answer_info['askid'], 0, '修改成功！');
        } else {
            Zend_Adver_Js::Goback('修改失败！');
        }
    }

    public function loadhtmlAction() {
        $tpl = $this->_getParam('html');
        $id = $this->_getParam('id');
        $this->ViewObj->id = $id;
        if ($tpl == 'edit_answer') {
            echo $this->ViewObj->render('/answer_edit.phtml');
        }

        $tag_id = $this->_getParam('tag_id');
        if ($tag_id) {
            echo '<script>$("#add_answer_content").val($("#' . $tag_id . '").html());</script>';
        }
    }

    /**
     * 
     * 遍历数组：返回2维数组
     * @param $array array 多维数组则过滤否则直接返回
     */
    private function trimArray($array = array()) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                unset($array[$k]);
            }
        }
        return $array;
    }

    /**
     * 
     * 问答回复数：>=1
     * 自动加上一条回复：
     * 回复用户：随机大众会员
     * 按照：测试机：\doc\11-11问答回复导入数据.txt
     * time:2009-11-17 
     * @author:kxgsy163@163.com
     */
    private function addAnswerDefault($param = array()) {
        $tmp_answer_default_array = $this->getDefaultAnswerArray();
        $tmp_ask_classid = $param['classid'];
        $info['userid'] = rand(16000, 17999) * 10;
        $info['askid'] = $param['id'];
        $info['content'] = $tmp_answer_default_array[$tmp_ask_classid];
        $info['addtime'] = time() + 45;

        //xzxin 2010-05-11
        $info['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

        if ($info['content']) {
            $this->AnswerObj->add($info);
            $this->AskObj->editAnswerNum($info['askid']);
        }
    }

    private function getDefaultAnswerArray() {
        return array(
            '4' => '两性生活,(两性生活有你的激情更精彩，更多令你销魂的方法随我来<a href="http://sex.9939.com/">http://sex.9939.com/</a>',
            '5' => '减肥瘦体,(怎么更瘦，怎么瘦地更多更健康，快来这里学习吧<a href="http://fitness.9939.com/">http://fitness.9939.com/</a>',
            '6' => '休闲健身,(更多休闲的好去处，更多健身的好方法，点击这里就能看到<a href="http://js.9939.com/">http://js.9939.com/</a>',
            '7' => '百姓健康,(百姓健康热点荟萃，你需要的都在这里<a href="http://baojian.9939.com/">http://baojian.9939.com/</a>',
            '8' => '营养饮食,(日常精品菜肴，五湖四海健康好滋味，随时供你浏览<a href="http://food.9939.com/">http://food.9939.com/</a>',
            '9' => '家居健康，(想住地更舒适吗？想住地更健康吗？快来这里看看吧！<a href="http://ask.9939.com/classid/15">http://ask.9939.com/classid/15</a>',
            '10' => '保健养生,(更多保健养生的好方法，更多长生不老的秘密，点击这里就能知道<a href="http://baojian.9939.com/">http://baojian.9939.com/</a>',
            '11' => '育儿,(这些宝宝问题你见过吗？这样照顾宝宝更健康你知道吗？快来看看吧<a href="http://baby.9939.com/">http://baby.9939.com/</a>',
            '12' => '百姓生活,(老百姓日常生活中都会遇见的健康问题，你不能不看<a href="http://baojian.9939.com/">http://baojian.9939.com/</a>',
            '13' => '美容塑身,(让你可以年轻二十岁的美容方法，让你可以更迷人百倍的美容产品快随我来<a href="http://beauty.9939.com/">http://beauty.9939.com/</a>',
            '14' => '其它(生活热点),(正在你身边发生的生活热点问题，你必须要会的生活绝技<a href="http://news.9939.com/">http://news.9939.com/</a>',
            '16' => '生活百科,(生活百科知识大杂烩，你的生活百科宝典<a href="http://ask.9939.com/classid/16">http://ask.9939.com/classid/16</a>',
            '17' => '电脑健康,(想知道更多电脑对你健康影响的危害吗快来看看吧<a href="http://ask.9939.com/classid/17">http://ask.9939.com/classid/17</a>',
            '18' => '手机与健康,(更多手机与健康问题，学会避免随手带给你的危害<a href="http://ask.9939.com/classid/20">http://ask.9939.com/classid/20</a>',
            '19' => '家具与健康,(家具有时带给您的危害不止这些，详情请点击这里查看<a href="http://ask.9939.com/classid/20">http://ask.9939.com/classid/20</a>',
            '20' => '环境与健康,(温馨提示：现代人的健康取决于周围环境对其自身的影响，点击这里查看<a href="http://ask.9939.com/classid/20">http://ask.9939.com/classid/20</a>',
            '21' => '其它,(更多其它大众健康问题，请点击这里<a href="http://baojian.9939.com/">http://baojian.9939.com/</a>',
            '23' => '腰疼,(更多腰痛问题，更多腰痛知识，一定不要错过<a href="http://jb.9939.com/dis/140853/">http://jb.9939.com/dis/140853/</a>',
            '24' => '易怒,(健康提示：易怒是人体内脏不协调的表现，更多请点击<a href="http://ask.9939.com/classid/24">http://ask.9939.com/classid/24</a>',
            '25' => '头晕,(想知道自己为什么头晕吗？想知道更多头晕的知识吗?<a href="http://zz.9939.com/zz/141491.shtml">http://zz.9939.com/zz/141491.shtml</a>',
            '26' => '黑眼圈,(黑眼圈提示你已经亚健康，想了解更多请到这里来<a href="http://ask.9939.com/classid/26">http://ask.9939.com/classid/26</a>',
            '27' => '多汗,(多汗证明你已体虚，怎么治疗现在就去弄个明白<a href="http://zz.9939.com/zz/271990.shtml">http://zz.9939.com/zz/271990.shtml</a>',
            '28' => '四肢肿,(四肢肿胀，四肢异常问题在这里有更多的答案<a href="http://ask.9939.com/classid/28">http://ask.9939.com/classid/28</a>',
            '29' => '抽筋,(夜里抽筋怎么办？治疗抽筋的好方法，统统都在这里<a href="http://ask.9939.com/classid/29">http://ask.9939.com/classid/29</a>',
            '30' => '其它,(其它亚健康问题，找治疗亚健康医院、医生请到这里来<a href="http://ask.9939.com/classid/22">http://ask.9939.com/classid/22</a>',
            '34' => '高血压,(高血压吃什么？降不下来怎么办？点击这里找答案<a href="http://jb.9939.com/dis/139141/">http://jb.9939.com/dis/139141/</a>',
            '35' => '心脏病,(预防和彻底治疗心脏病的知识，你不可不知道<a href="http://jb.9939.com/dis/272026/">http://jb.9939.com/dis/272026/</a>',
            '36' => '冠心病,(有冠心病问题，找治疗冠心病医院医生请到这里<a href="http://jb.9939.com/dis/139250/">http://jb.9939.com/dis/139250/</a>',
            '37' => '心率失常,(带你全面了解心率失常，还你一颗平常心<a href="http://ask.9939.com/classid/37">http://ask.9939.com/classid/37</a>',
            '38' => '心肌炎,(心肌炎的具体症状是什么怎么治疗更有效<a href="http://jb.9939.com/dis/140675/">http://jb.9939.com/dis/140675/</a>',
            '39' => '动脉硬化,(动脉硬化是现代社会老年人的第一杀手<a href="http://jb.9939.com/dis/138875/">http://jb.9939.com/dis/138875/</a>',
            '40' => '心绞痛,(心绞痛有哪些表现及如何诊断<a href="http://jb.9939.com/dis/140676/">http://jb.9939.com/dis/140676/</a>',
            '41' => '其它,(点击这里了解更全面的心血管内科知识<a href="http://jb.9939.com/sec/1661.shtml">http://jb.9939.com/sec/1661.shtml</a>',
            '43' => '糖尿病,(你必须知道的糖尿病饮食原则<a href="http://jb.9939.com/dis/140357/">http://jb.9939.com/dis/140357/</a>',
            '44' => '甲亢,(警惕心动过速、恶心呕吐，预防甲亢不容忽视<a href="http://jb.9939.com/dis/139482/">http://jb.9939.com/dis/139482/</a>',
            '45' => '高血脂,(少食多餐降血脂 高血脂患者该吃什么 <a href="http://jb.9939.com/dis/139146/">http://jb.9939.com/dis/139146/</a>',
            '46' => '淋巴瘤,(不得不看，恶性淋巴瘤偏方治疗和用药原则<a href="http://jb.9939.com/dis/139720/">http://jb.9939.com/dis/139720/</a>',
            '47' => '性早熟,(预防性早熟从少吃激素食物做起<a href="http://ask.9939.com/classid/47">http://ask.9939.com/classid/47</a>',
            '48' => '其它,(其它内分泌科问题，找内分科医院、医生请点击这里<a href="http://ask.9939.com/classid/48">http://ask.9939.com/classid/48</a>',
            '50' => '痛风,(痛风病人必须应注意的事情您知道吗<a href="http://jb.9939.com/dis/140388/">http://jb.9939.com/dis/140388/</a>',
            '51' => '药物过敏,(小心这些常用药也会使你过敏<a href="http://jb.9939.com/dis/140855/">http://jb.9939.com/dis/140855/</a>',
            '52' => '红斑狼疮,(鉴别和诊断红斑狼疮知识从这里开始<a href="http://jb.9939.com/dis/139982/">http://jb.9939.com/dis/139982/</a>',
            '53' => '关节炎,(更多、更新、更强的关节治疗知识就在这里<a href="http://jb.9939.com/dis/139041/">http://jb.9939.com/dis/139041/</a>',
            '54' => '风湿性关节炎,(把握治疗时间可轻松治疗风湿性关节炎<a href="http://jb.9939.com/dis/139041/">http://jb.9939.com/dis/139041/</a>',
            '55' => '其它,(有其它风湿免疫科科问题，找其它风湿免疫科医院、医生到这里来<a href="http://ask.9939.com/classid/49">http://ask.9939.com/classid/49</a>',
            '57' => '胃病,(胃病知识、胃病治疗、祝你打赢保胃站<a href="http://jb.9939.com/dis/140470/">http://jb.9939.com/dis/140470/</a>',
            '58' => '便秘,(了解便秘知识，不让便秘让你变丑变老<a href="http://jb.9939.com/dis/138673/">http://jb.9939.com/dis/138673/</a>',
            '59' => '腹痛,(警惕！腹痛易与这些疾病混淆<a href="http://jb.9939.com/dis/138930/">http://jb.9939.com/dis/138930/</a>',
            '60' => '腹泻,(杜绝腹泻，你必须了解更多腹泻知识<a href="http://jb.9939.com/dis/139078/">http://jb.9939.com/dis/139078/</a>',
            '61' => '胃炎,(怎么治疗胃炎更有效，怎么治疗胃炎不复发？<a href="http://jb.9939.com/dis/139821/">http://jb.9939.com/dis/139821/</a>',
            '62' => '肝硬化,(更多治疗肝硬化的好方法不容错过<a href="http://jb.9939.com/dis/139109/">http://jb.9939.com/dis/139109/</a>',
            '63' => '胃癌,(带您全面了解胃癌知识，看看你都了解吗<a href="http://jb.9939.com/dis/140469/">http://jb.9939.com/dis/140469/</a>',
            '64' => '消化性溃疡,(消化性溃疡综合检测与诊断你必须看看<a href="http://jb.9939.com/dis/140651/">http://jb.9939.com/dis/140651/</a>',
            '65' => '消化不良,(消化不良会给你带来更多问题<a href="http://jb.9939.com/dis/139173/">http://jb.9939.com/dis/139173/</a>',
            '66' => '消化道出血,(教你如何鉴别消化道出血<a href="http://jb.9939.com/dis/140611/">http://jb.9939.com/dis/140611/</a>',
            '67' => '食管癌,(食管癌后饮食有学问<a href="http://jb.9939.com/dis/140280/">http://jb.9939.com/dis/140280/</a>',
            '68' => '其它(有其它消化内科问题，找其它消化内科医院、医生请到疾病查询来<a href="http://jb.9939.com/sec/1662.shtml">http://jb.9939.com/sec/1662.shtml</a>',
            '70' => '头痛,(偏头痛需综合治疗不能以偏概全<a href="http://jb.9939.com/dis/140007/">http://jb.9939.com/dis/140007/</a>',
            '71' => '癫痫,(你不得不看的癫痫治疗指南<a href="http://jb.9939.com/dis/139079/">http://jb.9939.com/dis/139079/</a>',
            '72' => '脑梗塞,(实用的脑梗塞患者家庭护理方法 <a href="http://jb.9939.com/dis/139904/">http://jb.9939.com/dis/139904/</a>',
            '73' => '神经衰弱,(神经衰弱患者必看的治疗知识<a href="http://ask.9939.com/classid/73">http://ask.9939.com/classid/73</a>',
            '74' => '坐骨神经痛,(异常实用的坐骨神经痛保健方法一览<a href="http://jb.9939.com/dis/141146/">http://jb.9939.com/dis/141146/</a>',
            '75' => '昏迷,(绝对有用，更多应对突然昏迷的知识<a href="http://ask.9939.com/classid/75">http://ask.9939.com/classid/75</a>',
            '76' => '其它(神经内科),(有其它神经内科问题，找其他神经内科医院、医生请到这里<a href="http://ask.9939.com/classid/69">http://ask.9939.com/classid/69</a>',
            '78' => '感冒,(感冒、鼻塞、头痛怎么办？点击这里解决<a href="http://jb.9939.com/dis/139112/">http://jb.9939.com/dis/139112/</a>',
            '79' => '肺结核,(必看！四十年顽固性肺结核治疗的经验<a href="http://jb.9939.com/dis/139010/">http://jb.9939.com/dis/139010/</a>',
            '80' => '哮喘,(中医治疗哮喘病真实突破集萃<a href="http://jb.9939.com/dis/141149">http://jb.9939.com/dis/141149</a>',
            '81' => '肺炎,(入冬时得肺炎是怎么回事？<a href="http://jb.9939.com/dis/139022/">http://jb.9939.com/dis/139022/</a>',
            '82' => '支气管炎,(冬天这样预防支气管炎更好<a href="http://jb.9939.com/dis/139830/">http://jb.9939.com/dis/139830/</a>',
            '83' => '甲型H1N1流感,(有甲型H1N1流感问题，找甲型H1N1流感医院、医生就到这里<a href="http://ask.9939.com/classid/83">http://ask.9939.com/classid/83</a>',
            '84' => '气胸,(气胸复发的根本原因是什么<a href="http://ask.9939.com/classid/84">http://ask.9939.com/classid/84</a>',
            '85' => '肺气肿,(奇妙解决肺气肿的办法<a href="http://jb.9939.com/dis/139015/">http://jb.9939.com/dis/139015/</a>',
            '86' => '其它(呼吸内科),(有其它呼吸内科问题，找其它呼吸内科医院、医生请到这里来<a href="http://jb.9939.com/sec/1660.shtml">http://jb.9939.com/sec/1660.shtml</a>',
            '88' => '血液科,(有血液科问题，找血液科医院医生请到这里来<a href="http://jb.9939.com/sec/1666.shtml">http://jb.9939.com/sec/1666.shtml</a>',
            '89' => '白血病,(必读白血病基本知识一览<a href="http://jb.9939.com/dis/138612/">http://jb.9939.com/dis/138612/</a>',
            '90' => '过敏性紫绀,(找过敏性紫绀的最佳治疗方案请到这里<a href="http://jb.9939.com/dis/139258/">http://jb.9939.com/dis/139258/</a>',
            '91' => '血友病,(找血友病的最佳治疗方案请到这里<a href="http://jb.9939.com/dis/140764/">http://jb.9939.com/dis/140764/</a>',
            '92' => '低血糖症,(务必警惕婴儿低血糖的危险<a href="http://jb.9939.com/dis/140694/">http://jb.9939.com/dis/140694/</a>',
            '93' => '出血性疾病,(出血性疾病的详细信息和治疗方法<a href="http://jb.9939.com/dis/138752/">http://jb.9939.com/dis/138752/</a>',
            '94' => '其它(血液科),(有其它血液科问题，找其它血液科医院、医生亲到这里来<a href="http://jb.9939.com/sec/1666.shtml">http://jb.9939.com/sec/1666.shtml</a>',
            '96' => '尿道炎,(男人避免尿道炎必做的几个检查<a href="http://jb.9939.com/dis/139937/">http://jb.9939.com/dis/139937/</a>',
            '97' => '血尿,(血尿可能预示的几个严重病变<a href="http://jb.9939.com/dis/140754/">http://jb.9939.com/dis/140754/</a>',
            '98' => '膀胱炎,(关于膀胱炎的知识你知道多少<a href="http://jb.9939.com/dis/139977/">http://jb.9939.com/dis/139977/</a>',
            '99' => '尿毒症,(了解这些知识尿毒症不再是不治之症<a href="http://jb.9939.com/dis/139939/">http://jb.9939.com/dis/139939/</a>',
            '100' => '尿失禁,(防止尿失禁遗传的必修生活习惯<a href="http://jb.9939.com/dis/139944/">http://jb.9939.com/dis/139944/</a>',
            '101' => '其他(肾内科),(有其它肾内科问题，找消其它肾内科医院、医生请到这里来<a href="http://jb.9939.com/sec/1663.shtml">http://jb.9939.com/sec/1663.shtml</a>',
            '104' => '颈椎病,(颈椎病的病理改变过程你知道吗<a href="http://jb.9939.com/dis/139581/">http://jb.9939.com/dis/139581/</a>',
            '105' => '骨折,(骨折有哪些表现及如何诊断<a href="http://jb.9939.com/dis/139242/">http://jb.9939.com/dis/139242/</a>',
            '106' => '腰椎间盘突出,(更多腰椎间盘突出问题就在这里<a href="http://ask.9939.com/classid/106">http://ask.9939.com/classid/106</a>',
            '107' => '骨质增生,(骨质增生易于那些病混淆你知道吗<a href="http://jb.9939.com/dis/139244/">http://jb.9939.com/dis/139244/</a>',
            '108' => '骨质疏松,(更多骨质疏松问题，更多骨质疏松知识就在这里<a href="http://ask.9939.com/classid/108">http://ask.9939.com/classid/108</a>',
            '109' => '肩周炎,(更多肩周炎问题，更多肩周炎知识就在这里<a href="http://ask.9939.com/classid/109">http://ask.9939.com/classid/109</a>',
            '110' => '其它(骨科),(有其它骨科问题，找其他骨科医院、医生请到这里<a href="http://jb.9939.com/sec/1674.shtml">http://jb.9939.com/sec/1674.shtml</a>',
            '112' => '肺癌,(肺癌如何做到早发现早治疗？<a href="http://jb.9939.com/dis/139000/">http://jb.9939.com/dis/139000/</a>',
            '113' => '鸡胸,(找鸡胸手术就到9939疾病查询<a href="http://jb.9939.com/dis/139357/">http://jb.9939.com/dis/139357/</a>',
            '114' => '胸腔积液,(更多胸腔积液问题，更多胸腔积液知识点击这里<a href="http://ask.9939.com/classid/114">http://ask.9939.com/classid/114</a>',
            '115' => '心包积液,(更多心包积液问题，更多心包积液知识点击这里<a href="http://ask.9939.com/classid/115">http://ask.9939.com/classid/115</a>',
            '116' => '其它,(有其它心胸外科问题，找其它心胸外科医院、医生请到这里来<a href="http://jb.9939.com/sec/1669.shtml">http://jb.9939.com/sec/1669.shtml</a>',
            '117' => '手术科,(有其它手术科问题，找其它手术科医院、医生请到这里<a href="http://ask.9939.com/classid/117">http://ask.9939.com/classid/117</a>',
            '119' => '肾结石,(肾结石容易与哪些疾病混淆<a href="http://jb.9939.com/dis/140238/">http://jb.9939.com/dis/140238/</a>',
            '120' => '前列腺增生,(前列腺曾增生患者必知的饮食习惯<a href="http://jb.9939.com/dis/140041/">http://jb.9939.com/dis/140041/</a>',
            '121' => '输尿管结石,(输卵管结石患者必做的几项运动<a href="http://jb.9939.com/dis/140327/">http://jb.9939.com/dis/140327/</a>',
            '122' => '尿道结石,(更多尿道结石知识就在9939疾病查询<a href="http://jb.9939.com/dis/139932/">http://jb.9939.com/dis/139932/</a>',
            '123' => '膀胱结石,(找治疗膀胱结石的医院、就在9939疾病查询<a href="http://jb.9939.com/dis/139972/">http://jb.9939.com/dis/139972/</a>',
            '124' => '其它,(有其他泌尿外科问题，找其它泌尿外科医院医生就到这里<a href="http://jb.9939.com/sec/1672.shtml">http://jb.9939.com/sec/1672.shtml</a>',
            '126' => '触电,(更多触电问题，更多精彩回答就在这里<a href="http://ask.9939.com/classid/126">http://ask.9939.com/classid/126</a>',
            '127' => '烧伤,(更多烧伤问题，更多精彩回复和烧伤知识就在这里<a href="http://jb.9939.com/dis/140189/">http://jb.9939.com/dis/140189/</a>',
            '128' => '冻僵,(更多冻僵问题，更多精彩回复和冻僵知识就在这里<a href="http://jb.9939.com/dis/138879/">http://jb.9939.com/dis/138879/</a>',
            '129' => '热烧伤,(更多热烧伤问题，更多精彩回复和热烧伤知识就在这里<a href="http://jb.9939.com/dis/140103/">http://jb.9939.com/dis/140103/</a>',
            '130' => '其它(烧伤科),(有其它烧伤科问题，找其它烧伤科医院、医生就在这里<a href="http://jb.9939.com/sec/1676.shtml">http://jb.9939.com/sec/1676.shtml</a>',
            '132' => '中风,(带您全面了解中风知识，看看你都了解吗<a href="http://jb.9939.com/dis/141066/">http://jb.9939.com/dis/141066/</a>',
            '133' => '脑出血,(带您全面了解脑出血知识，看看你都了解吗<a href="http://jb.9939.com/dis/139897/">http://jb.9939.com/dis/139897/</a>',
            '134' => '脑积水,(带您全面了解脑积水知识，看看你都了解吗<a href="http://jb.9939.com/dis/139905/">http://jb.9939.com/dis/139905/</a>',
            '135' => '脑膜炎,(带您全面了解脑膜炎知识，看看你都了解吗<a href="http://jb.9939.com/dis/139910/">http://jb.9939.com/dis/139910/</a>',
            '136' => '脑震荡,(带您全面了解脑震荡知识，看看你都了解吗<a href="http://jb.9939.com/dis/139924/">http://jb.9939.com/dis/139924/</a>',
            '137' => '其它(脑外科),(有其它脑外科问题，找其它脑外科医院、医生请到这里来<a href="http://jb.9939.com/sec/1670.shtml">http://jb.9939.com/sec/1670.shtml</a>',
            '139' => '脑血栓,(带您全面了解脑血栓知识，看看你都了解吗<a href="http://jb.9939.com/dis/139921/">http://jb.9939.com/dis/139921/</a>',
            '140' => '血管肉瘤,(预防和彻底治疗血管肉瘤的知识，你不可不知道<a href="http://jb.9939.com/dis/140750/">http://jb.9939.com/dis/140750/</a>',
            '141' => '血管瘤,(预防和彻底治疗血管瘤的知识，你不可不知道<a href="http://jb.9939.com/dis/140747/">http://jb.9939.com/dis/140747/</a>',
            '142' => '其它(心血管外科),(有其它心血管外科问题，找其它心血管外科医院、医生请到这里来<a href="http://ask.9939.com/classid/138">http://ask.9939.com/classid/138</a>',
            '144' => '乳腺增生,(预防和彻底治疗乳腺增生的知识，你不可不知道<a href="http://jb.9939.com/dis/140130/">http://jb.9939.com/dis/140130/</a>',
            '145' => '乳腺癌,(预防和彻底治疗乳腺癌的知识，你不可不知道<a href="http://jb.9939.com/dis/140127/">http://jb.9939.com/dis/140127/</a>',
            '146' => '乳腺结核,(预防和彻底治疗乳腺结核的知识，你不可不知道<a href="http://jb.9939.com/dis/140128/">http://jb.9939.com/dis/140128/</a>',
            '147' => '乳腺炎,(预防和彻底治疗乳腺炎的知识，你不可不知道<a href="http://jb.9939.com/dis/139426/">http://jb.9939.com/dis/139426/</a>',
            '148' => '其它(乳腺外科),(有其它乳腺外科问题，找其它乳腺外科医院、医生请到这里来<a href="http://jb.9939.com/sec/1680.shtml">http://jb.9939.com/sec/1680.shtml</a>',
            '150' => '痔疮,(怎么治痔疮更有效，怎么治疗痔疮不复发？<a href="http://jb.9939.com/dis/141076/">http://jb.9939.com/dis/141076/</a>',
            '151' => '肛裂,(怎么治疗肛裂更有效，怎么治疗肛裂不复发？<a href="http://jb.9939.com/dis/139119/">http://jb.9939.com/dis/139119/</a>',
            '153' => '肛瘘,(怎么治疗肛瘘更有效，怎么治疗肛瘘不复发？<a href="http://jb.9939.com/dis/139120/">http://jb.9939.com/dis/139120/</a>',
            '154' => '内痔,(怎么治疗内痔更有效，怎么治疗内痔不复发？<a href="http://jb.9939.com/dis/139881/">http://jb.9939.com/dis/139881/</a>',
            '155' => '直肠息肉,(怎么治疗直肠息肉更有效，怎么治直肠息肉不复发？<a href="http://jb.9939.com/dis/141051/">http://jb.9939.com/dis/141051/</a>',
            '156' => '结肠息肉,(怎么治疗结肠息肉更有效，怎么治疗结肠息肉不复发？<a href="http://jb.9939.com/dis/139539/">http://jb.9939.com/dis/139539/</a>',
            '157' => '肛周脓肿,(怎么治疗肛周脓肿更有效，怎么治疗肛周脓肿不复发？<a href="http://jb.9939.com/dis/139131/">http://jb.9939.com/dis/139131/</a>',
            '158' => '其它(肛肠外科),(有其它肛肠外科问题，找其它肛肠外科医院、医生到这里<a href="http://ask.9939.com/classid/149">http://ask.9939.com/classid/149</a>',
            '160' => '胆结石,(关于胆结石您需要注意的不止这些来，看看更多知识吧<a href="http://jb.9939.com/dis/138828/">http://jb.9939.com/dis/138828/</a>',
            '161' => '脂肪肝,(关于脂肪肝您需要注意的不止这些来，看看更多知识吧<a href="http://jb.9939.com/dis/141041/">http://jb.9939.com/dis/141041/</a>',
            '162' => '肝脓肿,(关于肝脓肿您需要注意的不止这些来，看看更多知识吧<a href="http://jb.9939.com/dis/139097/">http://jb.9939.com/dis/139097/</a>',
            '163' => '胆囊息肉,(关于胆囊息肉你你需要注意的不止这些，来看看更多知识吧<a href="http://jb.9939.com/dis/138833/">http://jb.9939.com/dis/138833/</a>',
            '164' => '肝损伤,(关于胆结石你需要注意的不止这些来，看看更多知识吧<a href="http://jb.9939.com/dis/139098/">http://jb.9939.com/dis/139098/</a>',
            '165' => '其它(有其它肝胆外科问题，找其它肝胆外科医院、医生请到这里<a href="http://jb.9939.com/sec/1671.shtml">http://jb.9939.com/sec/1671.shtml</a>',
            '167' => '脓肿,(找脓肿的最佳治疗方案请到这里<a href="http://jb.9939.com/dis/139960/">http://jb.9939.com/dis/139960/</a>',
            '168' => '脂肪瘤,(找脂肪瘤的最佳治疗方案请到这里<a href="http://ask.9939.com/classid/168">http://ask.9939.com/classid/168</a>',
            '169' => '慢性阑尾炎,(找慢性阑尾炎的最佳治疗方案请到这里<a href="http://jb.9939.com/dis/139802/">http://jb.9939.com/dis/139802/</a>',
            '170' => '甲状腺瘤,(找甲状腺瘤的最佳治疗方案请到这里<a href="http://ask.9939.com/classid/170">http://ask.9939.com/classid/170</a>',
            '171' => '创伤性溃疡,(找创伤性溃疡的最佳治疗方案请到这里<a href="http://jb.9939.com/dis/138768/">http://jb.9939.com/dis/138768/</a>',
            '172' => '其它(普外科),(有其它普外科问题，找其它普外科医院、医生请到这里<a href="http://ask.9939.com/classid/166">http://ask.9939.com/classid/166</a>',
            '174' => '腰肌劳损,(不得不看，腰肌劳损的偏方治疗和用药原则<a href="http://jb.9939.com/dis/140852/">http://jb.9939.com/dis/140852/</a>',
            '175' => '疼痛,(不得不看，疼痛的偏方治疗和用药原则<a href="http://ask.9939.com/classid/175">http://ask.9939.com/classid/175</a>',
            '176' => '破伤风,(不得不看，破伤风的偏方治疗和用药原则<a href="http://jb.9939.com/dis/140011/">http://jb.9939.com/dis/140011/</a>',
            '177' => '腰腿痛,(不得不看，腰腿痛的偏方治疗和用药原则<a href="http://jb.9939.com/dis/140853/">http://jb.9939.com/dis/140853/</a>',
            '178' => '头外伤,(不得不看，头外伤的偏方治疗和用药原则<a href="http://jb.9939.com/dis/140396/">http://jb.9939.com/dis/140396/</a>',
            '179' => '其它(有其它外伤科问题，找其它外伤科医院、医生请到这里<a href="http://ask.9939.com/classid/173">http://ask.9939.com/classid/173</a>',
            '181' => '脐疝,(不得不看，脐疝的治疗和用药原则<a href="http://jb.9939.com/dis/138735/">http://jb.9939.com/dis/138735/</a>',
            '182' => '胃息肉,(不得不看，胃息肉的偏方治疗和用药原则<a href="http://ask.9939.com/classid/182">http://ask.9939.com/classid/182</a>',
            '183' => '股疝,(不得不看，股疝的偏方治疗和用药原则<a href="http://jb.9939.com/dis/139208/">http://jb.9939.com/dis/139208/</a>',
            '184' => '脾破裂,(不得不看，脾破裂的偏方治疗和用药原则<a href="http://jb.9939.com/dis/140006/">http://jb.9939.com/dis/140006/</a>',
            '185' => '胃损伤,(不得不看，胃损伤的偏方治疗和用药原则<a href="http://jb.9939.com/dis/140484/">http://jb.9939.com/dis/140484/</a>',
            '186' => '肠扭转,(不得不看，肠扭转的偏方治疗和用药原则<a href="http://jb.9939.com/dis/139535/">http://jb.9939.com/dis/139535/</a>',
            '187' => '其它(胃肠外科),(有其它肠胃外科问题，找其它胃肠外科医院到这里<a href="http://ask.9939.com/classid/180">http://ask.9939.com/classid/180</a>',
            '189' => '断指再植,(找断指再植医院、医生就到9939疾病查询<a href="http://jb.9939.com/dis/138888/">http://jb.9939.com/dis/138888/</a>',
            '190' => '拇指再造,(找拇指再造医院、医生就到9939疾病查询<a href="http://jb.9939.com/dis/139877/">http://jb.9939.com/dis/139877/</a>',
            '191' => '断肢再植,(找断肢再植医院、医生就到9939疾病查询<a href="http://jb.9939.com/dis/138887/">http://jb.9939.com/dis/138887/</a>',
            '192' => '其它,(有其它气管移植问题，找其它器官移植医院到这里<a href="http://ask.9939.com/classid/180">http://ask.9939.com/classid/180</a>',
            '195' => '月经不调,(治疗月经不调，这些知识要知道<a href="http://jb.9939.com/dis/141001/">http://jb.9939.com/dis/141001/</a>',
            '196' => '阴道炎,(治疗阴道炎，这些知识必须要知道<a href="http://jb.9939.com/dis/140518/">http://jb.9939.com/dis/140518/</a>',
            '197' => '宫颈糜烂,(根治宫颈糜烂，这些知识必须要知道<a href="http://jb.9939.com/dis/257573">http://jb.9939.com/dis/257573</a>',
            '198' => '盆腔炎,(轻松治疗盆腔炎，这些知识要知道<a href="http://jb.9939.com/dis/139989/">http://jb.9939.com/dis/139989/</a>',
            '199' => '痛经,(快速根除痛经，这些方法一定要学<a href="http://jb.9939.com/dis/140390/">http://jb.9939.com/dis/140390/</a>',
            '200' => '闭经,(快速根除闭经，这些方法一定要学<a href="http://ask.9939.com/classid/200">http://ask.9939.com/classid/200</a>',
            '201' => '子宫肌瘤,(治疗子宫，这些知识要知道<a href="http://jb.9939.com/dis/141120/">http://jb.9939.com/dis/141120/</a>',
            '202' => '附件炎,(快速根除附件炎，这些方法一定要学<a href="http://jb.9939.com/dis/139050/">http://jb.9939.com/dis/139050/</a>',
            '203' => '外阴瘙痒,(治疗外阴瘙痒，这些知识要知道<a href="http://jb.9939.com/dis/140431/">http://jb.9939.com/dis/140431/</a>',
            '204' => '不孕症,(治疗不孕症，这些知识要知道<a href="http://jb.9939.com/dis/138696/">http://jb.9939.com/dis/138696/</a>',
            '205' => '外阴炎,(治疗外阴炎，这些知识要知道<a href="http://jb.9939.com/dis/140432/">http://jb.9939.com/dis/140432/</a>',
            '206' => '细菌性阴道炎,(治疗细菌性阴道炎，这些知识要知道<a href="http://jb.9939.com/dis/140518/">http://jb.9939.com/dis/140518/</a>',
            '207' => '其它(妇科),(有其它妇科问题，找其它妇科医院、医生亲到这里来<a href="http://jb.9939.com/sec/1683.shtml">http://jb.9939.com/sec/1683.shtml</a>',
            '209' => '流产,(流产一定要警惕的一些事项<a href="http://jb.9939.com/dis/139731/">http://jb.9939.com/dis/139731/</a>',
            '210' => '宫外孕,(宫外孕一定要警惕的一些事项<a href="http://jb.9939.com/dis/139190/">http://jb.9939.com/dis/139190/</a>',
            '211' => '早孕反应,(早孕反应一定要警惕的一些事项<a href="http://jb.9939.com/dis/141015/">http://jb.9939.com/dis/141015/</a>',
            '212' => '早产,(早产一定要警惕的一些事项<a href="http://jb.9939.com/dis/141011/">http://jb.9939.com/dis/141011/</a>',
            '213' => '羊水过多,(羊水过多一定要警惕的一些事项<a href="http://jb.9939.com/dis/140844/">http://jb.9939.com/dis/140844/</a>',
            '214' => '产后出血,(产后出血一定要警惕的一些事项<a href="http://jb.9939.com/dis/138709/">http://jb.9939.com/dis/138709/</a>',
            '215' => '难产,(难产一定要警惕的一些事项<a href="http://jb.9939.com/dis/139893/">http://jb.9939.com/dis/139893/</a>',
            '216' => '破水,(破水一定要警惕的一些事项<a href="http://jb.9939.com/dis/140012/">http://jb.9939.com/dis/140012/</a>',
            '217' => '子宫破裂,(子宫破裂一定要警惕的一些事项<a href="http://jb.9939.com/dis/141123/">http://jb.9939.com/dis/141123/</a>',
            '218' => '其它(产科),(有其它产科问题，找其它产科医院、医生亲到这里来<a href="http://ask.9939.com/classid/208">http://ask.9939.com/classid/208</a>',
            '219' => '避孕流产,(轻松搞定避孕流产的经典小知识<a href="http://ask.9939.com/classid/209">http://ask.9939.com/classid/209</a>',
            '222' => '早泄,(轻松搞定早泄的经典小知识<a href="http://jb.9939.com/dis/141014/">http://jb.9939.com/dis/141014/</a>',
            '223' => '阳痿,(轻松搞定阳痿的经典小知识<a href="http://jb.9939.com/dis/140848/">http://jb.9939.com/dis/140848/</a>',
            '224' => '包皮龟头炎,(轻松搞定包皮龟头炎的经典小知识<a href="http://jb.9939.com/dis/138627/">http://jb.9939.com/dis/138627/</a>',
            '225' => '遗精,(轻松搞定遗精的经典小知识<a href="http://jb.9939.com/dis/140891/">http://jb.9939.com/dis/140891/</a>',
            '226' => '小阴茎,(轻松搞定小阴茎的经典小知识<a href="http://ask.9939.com/classid/226">http://ask.9939.com/classid/226</a>',
            '227' => '附睾炎,(轻松搞定附睾炎的经典小知识<a href="http://ask.9939.com/classid/227">http://ask.9939.com/classid/227</a>',
            '228' => '男性不育症,(轻松搞定男性不育症的经典小知识<a href="http://jb.9939.com/dis/139885/">http://jb.9939.com/dis/139885/</a>',
            '229' => '隐睾，(轻松搞定隐睾的经典小知识<a href="http://ask.9939.com/classid/229">http://ask.9939.com/classid/229</a>',
            '230' => '包皮过长,(轻松搞定包皮过长的经典小知识<a href="http://jb.9939.com/dis/138627/">http://jb.9939.com/dis/138627/</a>',
            '231' => '其它(男性科),(有其它男性科问题，找其它男性科医院、医生亲到这里来<a href="http://ask.9939.com/classid/221">http://ask.9939.com/classid/221</a>',
            '233' => '前列腺炎,(治疗前列腺炎必知的一些方法<a href="http://jb.9939.com/dis/140040/">http://jb.9939.com/dis/140040/</a>',
            '234' => '前列腺增生,(治疗前列增生必知的一些方法<a href="http://jb.9939.com/dis/140041/">http://jb.9939.com/dis/140041/</a>',
            '235' => '其它(前列腺科),(有其它前列腺科问题，找其它前列腺科医院、医生亲到这里来<a href="http://ask.9939.com/classid/232">http://ask.9939.com/classid/232</a>',
            '238' => '小儿手足口病,(治疗小儿手足口病必知的一些方法<a href="http://ask.9939.com/classid/238">http://ask.9939.com/classid/238</a>',
            '239' => '小儿感冒,(治疗小儿感冒必知的一些方法<a href="http://jb.9939.com/dis/140626/">http://jb.9939.com/dis/140626/</a>',
            '240' => '婴幼儿腹泻,(治疗婴幼儿腹泻必知的一些方法<a href="http://jb.9939.com/dis/140944/">http://jb.9939.com/dis/140944/</a>',
            '241' => '疝气,(治疗疝气必知的一些方法<a href="http://jb.9939.com/dis/140175/">http://jb.9939.com/dis/140175/</a>',
            '242' => '小儿高热,(治疗小儿高热必知的一些方法<a href="http://jb.9939.com/dis/140627/">http://jb.9939.com/dis/140627/</a>',
            '243' => '小儿肺炎,(治疗小儿肺炎必知的一些方法<a href="http://jb.9939.com/dis/140624/">http://jb.9939.com/dis/140624/</a>',
            '244' => '黄疸,(治疗黄疸必知的一些方法<a href="http://jb.9939.com/dis/140701/">http://jb.9939.com/dis/140701/</a>',
            '245' => '早产儿,(治疗早产儿必知的一些方法<a href="http://jb.9939.com/dis/141012/">http://jb.9939.com/dis/141012/</a>',
            '246' => '小儿脑瘫,(治疗小儿脑瘫必知的一些方法<a href="http://jb.9939.com/dis/140643/">http://jb.9939.com/dis/140643/</a>',
            '247' => '其它(小儿内科),(有其它小儿内科问题，找其它小儿内科医院、医生亲到这里来<a href="http://ask.9939.com/classid/237">http://ask.9939.com/classid/237</a>',
            '249' => '多动症,(找治疗小儿多动症的医院就在这里<a href="http://jb.9939.com/dis/140622/">http://jb.9939.com/dis/140622/</a>',
            '250' => '孤独症,(找治疗小儿孤独症的医院就在这里<a href="http://jb.9939.com/dis/138931/">http://jb.9939.com/dis/138931/</a>',
            '251' => '惊厥,(找治疗小儿惊厥的医院就在这里<a href="http://jb.9939.com/dis/140639/">http://jb.9939.com/dis/140639/</a>',
            '252' => '儿童睡眠障碍,(找治疗儿童睡眠障碍的医院就在这里<a href="http://jb.9939.com/dis/138937/">http://jb.9939.com/dis/138937/</a>',
            '253' => '儿童学习障碍,(找治疗儿童学习障碍的的医院就在这里<a href="http://ask.9939.com/classid/253">http://ask.9939.com/classid/253</a>',
            '254' => '儿童精神分裂,(找治疗儿童精神分裂的医院就在这里<a href="http://jb.9939.com/dis/138933/">http://jb.9939.com/dis/138933/</a>',
            '255' => '其它(小儿心理),(有其它小儿心理问题，找其它小儿心理医院、医生亲到这里来<a href="http://ask.9939.com/classid/255">http://ask.9939.com/classid/255</a>',
            '256' => '小儿外科,(有其它小儿外科问题，找其它外科医院、医生亲到这里来<a href="http://ask.9939.com/classid/256">http://ask.9939.com/classid/256</a>',
            '258' => '小儿肥胖,(找治疗小儿肥胖症的医院就在这里<a href="http://jb.9939.com/dis/140623/">http://jb.9939.com/dis/140623/</a>',
            '259' => '厌食症,(找治疗小儿厌食症的医院就在这里<a href="http://jb.9939.com/dis/140653/">http://jb.9939.com/dis/140653/</a>',
            '260' => '锌缺乏病,(找治疗小儿锌缺乏病的医院就在这里<a href="http://jb.9939.com/dis/140688/">http://jb.9939.com/dis/140688/</a>',
            '261' => '维生素D缺乏,(点击这里浏览更多维生素D缺乏病精彩知识<a href="http://jb.9939.com/dis/140458/">http://jb.9939.com/dis/140458/</a>',
            '262' => '维生素A缺乏,(点击这里浏览更多维生素A缺乏病精彩知识<a href="http://jb.9939.com/dis/140452/">http://jb.9939.com/dis/140452/</a>',
            '263' => '其它(小儿营养不良),(有其它小儿营养不良问题，找其它小儿营养不良医院、医生亲到这里来<a href="http://jb.9939.com/dis/140654/">http://jb.9939.com/dis/140654/</a>',
            '265' => '肺炎,(点击这里浏览更多关于小儿肺炎的精彩知识<a href="http://jb.9939.com/dis/140624/">http://jb.9939.com/dis/140624/</a>',
            '266' => '呕吐,(点击这里浏览更多关于小儿呕吐的精彩知识<a href="http://jb.9939.com/dis/140706/">http://jb.9939.com/dis/140706/</a>',
            '267' => '新生儿败血症,(点击这里浏览更多关于新生儿败血症的精彩知识<a href="http://jb.9939.com/dis/140691/">http://jb.9939.com/dis/140691/</a>',
            '268' => '新生儿溶血病,(点击这里浏览更多关于新生儿溶血症的精彩知识<a href="http://jb.9939.com/dis/140709/">http://jb.9939.com/dis/140709/</a>',
            '269' => '新生儿窒息,(点击这里浏览更多关于新生儿窒息的精彩知识<a href="http://jb.9939.com/dis/140712/">http://jb.9939.com/dis/140712/</a>',
            '270' => '新生儿泪囊炎,(点击这里浏览更多关于新生儿泪囊炎的精彩知识<a href="http://jb.9939.com/dis/140703/">http://jb.9939.com/dis/140703/</a>',
            '271' => '其它(新生儿),(有其它新生儿问题，找其它新生儿医院、医生亲到这里来<a href="http://ask.9939.com/classid/264">http://ask.9939.com/classid/264</a>',
            '273' => '不孕,(解决妇女不孕的一些超级好方法集锦<a href="http://jb.9939.com/dis/138696/">http://jb.9939.com/dis/138696/</a>',
            '274' => '不育,(解决妇女不育的一些超级好方法集锦<a href="http://ask.9939.com/classid/274">http://ask.9939.com/classid/274</a>',
            '275' => '其它(不孕不育),(有其它不孕不育问题，找其它不孕不育医院、医生亲到这里来<a href="http://ask.9939.com/classid/272">http://ask.9939.com/classid/272</a>',
            '278' => '白内障,(更多关于白内障的治疗知识绝对不容错过<a href="http://jb.9939.com/dis/138607/">http://jb.9939.com/dis/138607/</a>',
            '279' => '弱视,(更多关于弱势的治疗知识绝对不容错过<a href="http://jb.9939.com/dis/140145/">http://jb.9939.com/dis/140145/</a>',
            '280' => '沙眼,(更多关于沙眼的治疗知识绝对不容错过<a href="http://jb.9939.com/dis/140172/">http://jb.9939.com/dis/140172/</a>',
            '281' => '近视眼,(更多关于近视眼的治疗知识绝对不容错过<a href="http://jb.9939.com/dis/139563/">http://jb.9939.com/dis/139563/</a>',
            '282' => '青光眼,(更多关于青光眼的治疗知识绝对不容错过<a href="http://jb.9939.com/dis/140062/">http://jb.9939.com/dis/140062/</a>',
            '283' => '其它(眼科),(有其它眼科问题，找其它眼科医院、医生亲到这里来<a href="http://ask.9939.com/classid/277">http://ask.9939.com/classid/277</a>',
            '285' => '鼻炎,(轻轻松松解决鼻炎问题的方法<a href="http://jb.9939.com/dis/139254/">http://jb.9939.com/dis/139254/</a>',
            '286' => '鼻出血,(轻轻松松解决鼻出血的方法<a href="http://ask.9939.com/classid/284">http://ask.9939.com/classid/284</a>',
            '287' => '鼻息肉,(轻轻松松解决鼻息肉的方法<a href="http://ask.9939.com/classid/287">http://ask.9939.com/classid/287</a>',
            '288' => '扁桃体炎,(轻轻松松解决扁桃体炎的方法<a href="http://jb.9939.com/dis/139372/">http://jb.9939.com/dis/139372/</a>',
            '289' => '中耳炎,(轻轻松松解决中耳炎的方法<a href="http://jb.9939.com/dis/139407/">http://jb.9939.com/dis/139407/</a>',
            '290' => '其它,(有其它耳科问题，找其它耳科医院、医生亲到这里来<a href="http://ask.9939.com/classid/290">http://ask.9939.com/classid/290</a>',
            '292' => '口腔溃疡,(患了口腔溃疡怎么办？这些方法能帮你解决<a href="http://jb.9939.com/dis/139648/">http://jb.9939.com/dis/139648/</a>',
            '293' => '龋齿,(患了龋齿怎么办？这些方法能帮你解决<a href="http://jb.9939.com/dis/140075/">http://jb.9939.com/dis/140075/</a>',
            '294' => '牙周病,(患了牙周病怎么办？这些方法能帮你解决<a href="http://jb.9939.com/dis/140778/">http://jb.9939.com/dis/140778/</a>',
            '295' => '磨损,(患了磨损怎么办？这些方法能帮你解决<a href="http://jb.9939.com/dis/139873/">http://jb.9939.com/dis/139873/</a>',
            '296' => '唇裂,(患了唇裂怎么办？这些方法能帮你解决<a href="http://jb.9939.com/dis/138781/">http://jb.9939.com/dis/138781/</a>',
            '297' => '舌病,(患了舌病怎么办？这些方法能帮你解决<a href="http://jb.9939.com/dis/140199/">http://jb.9939.com/dis/140199/</a>',
            '298' => '其它(口腔科),(有其它口腔科问题，找其它口腔科医院、医生亲到这里来<a href="http://ask.9939.com/classid/291">http://ask.9939.com/classid/291</a>',
            '300' => '男性生殖,(男性生殖知识大放送，精彩不容错过<a href="http://ask.9939.com/classid/300">http://ask.9939.com/classid/300</a>',
            '301' => '隆胸,(关于隆胸你都知道多少快来看看吧<a href="http://ask.9939.com/classid/300">http://ask.9939.com/classid/300</a>',
            '303' => '激光永久脱毛术,(关于激光永久脱毛术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/303">http://ask.9939.com/classid/303</a>',
            '304' => '皮肤移植术,(关于皮肤移植术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/304">http://ask.9939.com/classid/304</a>',
            '305' => '去纹身术,(关于去纹身术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/305">http://ask.9939.com/classid/305</a>',
            '306' => '其它(皮肤),(有其它皮肤问题，找其它皮肤医院、医生亲到这里来<a href="http://ask.9939.com/classid/302">http://ask.9939.com/classid/302</a>',
            '307' => '头部,(关于头部整形术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/307">http://ask.9939.com/classid/307</a>',
            '308' => '眼部,(关于眼部整形术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/308">http://ask.9939.com/classid/308</a>',
            '309' => '胸部,(关于胸部整形术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/309">http://ask.9939.com/classid/309</a>',
            '310' => '口腔,(关于口腔整形术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/310">http://ask.9939.com/classid/310</a>',
            '311' => '鼻部,(关于鼻部整形术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/311">http://ask.9939.com/classid/311</a>',
            '312' => '女性生殖,(关于女性生殖你都知道多少快来看看吧<a href="http://ask.9939.com/classid/312">http://ask.9939.com/classid/312</a>',
            '313' => '四肢,(关于四肢整形你都知道多少快来看看吧<a href="http://ask.9939.com/classid/313">http://ask.9939.com/classid/313</a>',
            '314' => '眉部,(关于眉部你都知道多少快来看看吧<a href="http://ask.9939.com/classid/314">http://ask.9939.com/classid/314</a>',
            '316' => '疤痕矫正,(关于疤痕矫正你都知道多少快来看看吧<a href="http://ask.9939.com/classid/316">http://ask.9939.com/classid/316</a>',
            '317' => '痣切除术,(关于痣切除术你都知道多少快来看看吧<a href="http://ask.9939.com/classid/317">http://ask.9939.com/classid/317</a>',
            '318' => '祛斑,(关于祛斑你都知道多少快来看看吧<a href="http://ask.9939.com/classid/318">http://ask.9939.com/classid/318</a>',
            '319' => '其它(全身),(有其它全身问题，找其它全身医院、医生亲到这里来<a href="http://ask.9939.com/classid/319">http://ask.9939.com/classid/319</a>',
            '320' => '耳部,(关于耳部整形你都知道多少快来看看吧<a href="http://ask.9939.com/classid/320">http://ask.9939.com/classid/320</a>',
            '321' => '腰腹部,(关于腰腹部整形你都知道多少快来看看吧<a href="http://ask.9939.com/classid/321">http://ask.9939.com/classid/321</a>',
            '322' => '颈部,(关于颈部整形你都知道多少快来看看吧<a href="http://ask.9939.com/classid/322">http://ask.9939.com/classid/322</a>',
            '323' => '臀部,(关于臀部整形你都知道多少快来看看吧<a href="http://ask.9939.com/classid/323">http://ask.9939.com/classid/323</a>',
            '326' => '乙肝,(治疗乙肝你不知道的新方法新知识<a href="http://jb.9939.com/dis/140896/">http://jb.9939.com/dis/140896/</a>',
            '327' => '肝癌,(治疗肝癌你不知道的新方法新知识<a href="http://jb.9939.com/dis/140979/">http://jb.9939.com/dis/140979/</a>',
            '328' => '肝性脑病,(紧急应对肝性脑病方法总汇<a href="http://ask.9939.com/classid/328">http://ask.9939.com/classid/328</a>',
            '329' => '新生儿肝炎,(关于新生儿肝炎你知道多少<a href="http://ask.9939.com/classid/329">http://ask.9939.com/classid/329</a>',
            '330' => '其它(肝病科),(有其它肝病问题，找其它肝病科医院、医生亲到这里来<a href="http://ask.9939.com/classid/325">http://ask.9939.com/classid/325</a>',
            '332' => '淋病,(以下方法为您轻松解决淋病带来的烦恼<a href="http://jb.9939.com/dis/139724/">http://jb.9939.com/dis/139724/</a>',
            '333' => '梅毒,(以下方法为您轻松解决梅毒带来的烦恼<a href="http://jb.9939.com/dis/139841/">http://jb.9939.com/dis/139841/</a>',
            '334' => '尖锐湿疣,(以下方法为您轻松解决尖锐湿疣带来的烦恼<a href="http://jb.9939.com/dis/139500/">http://jb.9939.com/dis/139500/</a>',
            '335' => '性交疼痛,(以下方法为您轻松解决性交疼痛带来的烦恼<a href="http://jb.9939.com/dis/140718/">http://jb.9939.com/dis/140718/</a>',
            '336' => '生殖器疱疹,(以下方法为您轻松解决生殖器疱疹带来的烦恼<a href="http://jb.9939.com/dis/140265/">http://jb.9939.com/dis/140265/</a>',
            '337' => '黄褐斑,(以下方法为您轻松解决黄褐斑带来的烦恼<a href="http://jb.9939.com/dis/139330/">http://jb.9939.com/dis/139330/</a>',
            '338' => '其它(性病科),(有其它性病科问题，找其它性病科医院、医生亲到这里来<a href="http://jb.9939.com/sec/1695.shtml">http://jb.9939.com/sec/1695.shtml</a>',
            '340' => '湿疹,(以下方法为您轻松解决湿疹带来的烦恼<a href="http://jb.9939.com/dis/140269/">http://jb.9939.com/dis/140269/</a>',
            '341' => '白癜风,(以下方法为您轻松解决白癜风带来的烦恼<a href="http://jb.9939.com/dis/138604/">http://jb.9939.com/dis/138604/</a>',
            '342' => '荨麻疹,(以下方法为您轻松解决荨麻疹带来的烦恼<a href="http://jb.9939.com/dis/140499/">http://jb.9939.com/dis/140499/</a>',
            '343' => '皮肤过敏,(以下方法为您轻松解决皮肤过敏带来的烦恼<a href="http://jb.9939.com/dis/139994/">http://jb.9939.com/dis/139994/</a>',
            '344' => '红斑,(以下方法为您轻松解决红斑带来的烦恼<a href="http://jb.9939.com/dis/139287/">http://jb.9939.com/dis/139287/</a>',
            '345' => '牛皮癣,(以下方法为您轻松解决牛皮癣带来的烦恼<a href="http://jb.9939.com/dis/139954">http://jb.9939.com/dis/139954</a>',
            '346' => '痤疮,(以下方法为您轻松解决痤疮带来的烦恼<a href="http://jb.9939.com/dis/138785/">http://jb.9939.com/dis/138785/</a>',
            '347' => '脚气,(以下方法为您轻松解决脚气带来的烦恼<a href="http://jb.9939.com/dis/139525/">http://jb.9939.com/dis/139525/</a>',
            '348' => '银屑病,(以下方法为您轻松解决银屑病带来的烦恼<a href="http://jb.9939.com/dis/140928/">http://jb.9939.com/dis/140928/</a>',
            '349' => '其它(皮肤科),(有其它皮肤科问题，找其它皮肤科医院、医生亲到这里来<a href="http://jb.9939.com/sec/1694.shtml">http://jb.9939.com/sec/1694.shtml</a>',
            '351' => '艾滋病,(艾滋病预防知识汇总，看看就不会再恐艾<a href="http://jb.9939.com/dis/138595/">http://jb.9939.com/dis/138595/</a>',
            '352' => '麻疹,(得了麻疹不要怕，以下方法能治疗<a href="http://jb.9939.com/dis/139767/">http://jb.9939.com/dis/139767/</a>',
            '353' => '狂犬病,(预防狂犬病必须知道的几点<a href="http://jb.9939.com/dis/139656/">http://jb.9939.com/dis/139656/</a>',
            '354' => '水痘,(预防水痘必须知道的几点<a href="http://jb.9939.com/dis/140332/">http://jb.9939.com/dis/140332/</a>',
            '355' => '病毒性肝炎,(病毒性肝炎不可不知道的护理方法<a href="http://jb.9939.com/dis/138869/">http://jb.9939.com/dis/138869/</a>',
            '356' => '风疹,(了解以下知识帮你轻松治疗风疹<a href="http://jb.9939.com/dis/139043/">http://jb.9939.com/dis/139043/</a>',
            '357' => '伤寒,(了解以下知识帮你轻松治疗伤寒<a href="http://jb.9939.com/dis/140177/">http://jb.9939.com/dis/140177/</a>',
            '358' => '天花,(了解以下知识帮你彻底治疗天花<a href="http://jb.9939.com/dis/140380/">http://jb.9939.com/dis/140380/</a>',
            '359' => '丝虫病,(了解以下知识帮你彻底治疗丝虫病<a href="http://jb.9939.com/dis/140336/">http://jb.9939.com/dis/140336/</a>',
            '360' => '流行性感冒,(了解以下知识帮你彻底治疗流行性感冒<a href="http://ask.9939.com/classid/360">http://ask.9939.com/classid/360</a>',
            '361' => '甲型肝炎,(了解以下知识帮你彻底治疗甲型肝炎<a href="http://jb.9939.com/dis/139483/">http://jb.9939.com/dis/139483/</a>',
            '362' => '鼠疫,(了解以下知识帮你彻底预防鼠疫<a href="http://jb.9939.com/dis/140330/">http://jb.9939.com/dis/140330/</a>',
            '363' => '其它(传染科),(有其它传染科问题，找其它传染科医院、医生亲到这里来<a href="http://jb.9939.com/sec/1692.shtml">http://jb.9939.com/sec/1692.shtml</a>',
            '365' => '阴虱病,(掌握以下治疗方法饮食病没有了<a href="http://jb.9939.com/dis/140927/">http://jb.9939.com/dis/140927/</a>',
            '366' => '肝吸虫病,(掌握以下治疗方法肝吸虫病没有了<a href="http://ask.9939.com/classid/366">http://ask.9939.com/classid/366</a>',
            '367' => '阴道毛滴虫,(掌握以下治疗方法阴道毛滴虫病没有了<a href="http://jb.9939.com/dis/140921/">http://jb.9939.com/dis/140921/</a>',
            '368' => '血吸虫病,(掌握以下治疗方法血吸虫病没有了<a href="http://jb.9939.com/dis/140759/">http://jb.9939.com/dis/140759/</a>',
            '369' => '疟疾,(掌握以下治疗方法疟疾没有了<a href="http://jb.9939.com/dis/139963/">http://jb.9939.com/dis/139963/</a>',
            '370' => '其它(寄生虫),(有其它寄生虫问题，找其它寄生虫医院、医生亲到这里来<a href="http://ask.9939.com/classid/364">http://ask.9939.com/classid/364</a>',
            '386' => '焦虑症,(掌握以下方法轻轻松松治疗焦虑症<a href="http://jb.9939.com/dis/139520/">http://jb.9939.com/dis/139520/</a>',
            '387' => '强迫症,(掌握以下方法轻轻松松治疗强迫症<a href="http://jb.9939.com/dis/140054/">http://jb.9939.com/dis/140054/</a>',
            '388' => '恐怖症,(掌握以下方法轻轻松松治疗恐怖症<a href="http://jb.9939.com/dis/139639/">http://jb.9939.com/dis/139639/</a>',
            '389' => '贪食症,(掌握以下方法轻轻松松治疗贪食症<a href="http://ask.9939.com/classid/389">http://ask.9939.com/classid/389</a>',
            '390' => '厌食症,(掌握以下方法轻轻松松治疗厌食症<a href="http://ask.9939.com/classid/390">http://ask.9939.com/classid/390</a>',
            '391' => '癔症,(掌握以下方法轻轻松松治疗癔症<a href="http://ask.9939.com/classid/391">http://ask.9939.com/classid/391</a>',
            '392' => '抑郁症,(掌握以下方法轻轻松松治疗抑郁症<a href="http://jb.9939.com/dis/140910/">http://jb.9939.com/dis/140910/</a>',
            '393' => '神经衰弱,(掌握以下方法轻轻松松治疗神经衰弱<a href="http://jb.9939.com/dis/140214/">http://jb.9939.com/dis/140214/</a>',
            '394' => '洁癖症,(掌握以下方法轻轻松松治疗洁癖症<a href="http://ask.9939.com/classid/394">http://ask.9939.com/classid/394</a>',
            '395' => '其它(神经症),(有其它神经症问题，找其它神经症医院、医生亲到这里来<a href="http://ask.9939.com/classid/395">http://ask.9939.com/classid/395</a>',
            '397' => '性障碍,(掌握以下方法轻轻松松治疗性障碍<a href="http://jb.9939.com/dis/140725/">http://jb.9939.com/dis/140725/</a>',
            '398' => '性欲减退,(掌握以下方法轻轻松松治疗性欲减退<a href="http://jb.9939.com/dis/140722/">http://jb.9939.com/dis/140722/</a>',
            '399' => '性变态,(掌握以下方法轻轻松松治疗性变态<a href="http://jb.9939.com/dis/140715/">http://jb.9939.com/dis/140715/</a>',
            '400' => '恋物癖,(掌握以下方法轻轻松松治疗恋物癖<a href="http://ask.9939.com/classid/400">http://ask.9939.com/classid/400</a>',
            '401' => '性厌恶,(掌握以下方法轻轻松松治疗性厌恶<a href="http://jb.9939.com/dis/140721/">http://jb.9939.com/dis/140721/</a>',
            '402' => '露阴癖,(掌握以下方法轻轻松松治疗露阴癖<a href="http://jb.9939.com/dis/139741/">http://jb.9939.com/dis/139741/</a>',
            '403' => '其它(性障碍),(有其它性障碍问题，找其它性障碍医院、医生亲到这里来<a href="http://ask.9939.com/classid/403">http://ask.9939.com/classid/403</a>',
            '405' => '精神分裂症,(得了精神分裂，你必须知道以下几点<a href="http://jb.9939.com/dis/139574/">http://jb.9939.com/dis/139574/</a>',
            '406' => '躁狂症,(得了狂躁症，你必须知道以下几点<a href="http://jb.9939.com/dis/141018/">http://jb.9939.com/dis/141018/</a>',
            '407' => '其它障碍(精神病及障碍),(有其它精神病及障碍问题，找其它精神病及障碍医院、医生亲到这里来<a href="http://ask.9939.com/classid/407">http://ask.9939.com/classid/407</a>',
            '409' => '情感障碍,(得了情感障碍，你必须知道以下几点<a href="http://jb.9939.com/dis/140066/">http://jb.9939.com/dis/140066/</a>',
            '410' => '记忆障碍,(得了记忆障碍，你必须知道以下几点<a href="http://jb.9939.com/dis/139473/">http://jb.9939.com/dis/139473/</a>',
            '411' => '失眠症,(得了失眠症，你必须知道以下几点<a href="http://jb.9939.com/dis/140267/">http://jb.9939.com/dis/140267/</a>',
            '412' => '自闭症,(得了自闭症，你必须知道以下几点<a href="http://jb.9939.com/dis/141131/">http://jb.9939.com/dis/141131/</a>',
            '413' => '强迫性障碍,(得了强迫性障碍，你必须知道以下几点<a href="http://jb.9939.com/dis/140053/">http://jb.9939.com/dis/140053/</a>',
            '414' => '嗜睡症,(得了嗜睡症，你必须知道以下几点<a href="http://jb.9939.com/dis/140310/">http://jb.9939.com/dis/140310/</a>',
            '415' => '磨牙,(得了磨牙症，你必须知道以下几点<a href="http://ask.9939.com/classid/415">http://ask.9939.com/classid/415</a>',
            '416' => '抽动障碍,(得了抽动障碍，你必须知道以下几点<a href="http://jb.9939.com/dis/138751/">http://jb.9939.com/dis/138751/</a>',
            '417' => '遗忘,(得了遗忘症，你必须知道以下几点<a href="http://jb.9939.com/dis/140893/">http://jb.9939.com/dis/140893/</a>',
            '418' => '其它(其它障碍),(有其它障碍问题，找其它障碍医院、医生亲到这里来<a href="http://ask.9939.com/classid/418">http://ask.9939.com/classid/418</a>',
            '420' => '适应能力差,(了解以下知识后你还会适应能力差吗<a href="http://ask.9939.com/classid/420">http://ask.9939.com/classid/420</a>',
            '421' => '反应迟钝,(了解以下知识后你还会反应迟钝吗<a href="http://ask.9939.com/classid/421">http://ask.9939.com/classid/421</a>',
            '422' => '工作效率低,(了解以下知识后你还会工作效率低吗<a href="http://ask.9939.com/classid/422">http://ask.9939.com/classid/422</a>',
            '423' => '身体疲乏,(了解以下知识后你还会身体疲乏吗<a href="http://ask.9939.com/classid/423">http://ask.9939.com/classid/423</a>',
            '424' => '办公室综合症,(了解以下知识后你还会得办公室综合症吗<a href="http://ask.9939.com/classid/424">http://ask.9939.com/classid/424</a>',
            '425' => '其它(职场心理),(有其它职场心理问题，找其它职场心理医院、医生亲到这里来<a href="http://ask.9939.com/classid/425">http://ask.9939.com/classid/425</a>',
            '427' => '沟通障碍,(解决沟通障碍，你必须知道这些知识<a href="http://ask.9939.com/classid/427">http://ask.9939.com/classid/427</a>',
            '429' => '中医内科,(更多中医内科问题，更多中医内科知识尽在这里<a href="http://ask.9939.com/classid/429">http://ask.9939.com/classid/429</a>',
            '430' => '中医外科,(更多中医外科问题，更多中医外科知识尽在这里<a href="http://ask.9939.com/classid/430">http://ask.9939.com/classid/430</a>',
            '431' => '中医妇科,(更多中医妇科问题，更多中医妇科知识尽在这里<a href="http://ask.9939.com/classid/431">http://ask.9939.com/classid/431</a>',
            '432' => '中医儿科,(更多中医儿科问题，更多中医儿科知识尽在这里<a href="http://ask.9939.com/classid/432">http://ask.9939.com/classid/432</a>',
            '433' => '中医保健科,(更多中医保健科问题，更多中医保健科知识尽在这里<a href="http://ask.9939.com/classid/433">http://ask.9939.com/classid/433</a>',
            '434' => '针灸保健科,(更多针灸保健科问题，更多针灸保健科知识尽在这里<a href="http://ask.9939.com/classid/434">http://ask.9939.com/classid/434</a>',
            '435' => '中医骨伤,(更多中医骨伤问题，更多中医骨伤知识尽在这里<a href="http://ask.9939.com/classid/435">http://ask.9939.com/classid/435</a>',
            '437' => '中药,(更多中药问题，更多中药知识尽在这里<a href="http://ask.9939.com/classid/437">http://ask.9939.com/classid/437</a>',
            '438' => '西药,(更多西药问题，更多西药知识尽在这里<a href="http://ask.9939.com/classid/438">http://ask.9939.com/classid/438</a>',
            '439' => '中成药,(更多中成药问题，更多中成药知识尽在这里<a href="http://ask.9939.com/classid/439">http://ask.9939.com/classid/439</a>',
            '440' => '保健品,(更多保健品问题，更多保健品知识尽在这里<a href="http://ask.9939.com/classid/440">http://ask.9939.com/classid/440</a>',
            '441' => '药物反应,(更多药物反应问题，更多药物反应知识尽在这里<a href="http://ask.9939.com/classid/441">http://ask.9939.com/classid/441</a>',
            '442' => '计生用品,(找计生用品，更多计生用品知识请浏览这里<a href="http://ask.9939.com/classid/442">http://ask.9939.com/classid/442</a>',
            '445' => '发热,(理解更多发热知识，从此不为发热担忧<a href="http://ask.9939.com/classid/445">http://ask.9939.com/classid/445</a>',
            '446' => '疲劳,(理解更多疲劳知识，从此不为疲劳担忧<a href="http://ask.9939.com/classid/446">http://ask.9939.com/classid/446</a>',
            '447' => '水肿,(理解更多水肿知识，从此不为水肿担忧<a href="http://jb.9939.com/dis/140294/">http://jb.9939.com/dis/140294/</a>',
            '448' => '淋巴结肿大,(理解更多淋巴结肿大知识，从此不为淋巴结肿大担忧<a href="http://jb.9939.com/dis/139721/">http://jb.9939.com/dis/139721/</a>',
            '449' => '关节疼痛,(理解更多关节疼痛知识，从此不为关节疼痛大担忧<a href="http://ask.9939.com/classid/449">http://ask.9939.com/classid/449</a>',
            '450' => '头痛,(头痛了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/450">http://ask.9939.com/classid/450</a>',
            '451' => '口臭,(口臭了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/451">http://ask.9939.com/classid/451</a>',
            '452' => '消瘦,(消瘦了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/452">http://ask.9939.com/classid/452</a>',
            '453' => '贫血,(贫血了怎么办以下方法供你参考<a href="http://jb.9939.com/dis/140009/">http://jb.9939.com/dis/140009/</a>',
            '454' => '抽筋,(抽筋了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/454">http://ask.9939.com/classid/454</a>',
            '455' => '胃胀气胀,(胃胀气胀了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/455">http://ask.9939.com/classid/455</a>',
            '456' => '便血,(便血气胀了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/456">http://ask.9939.com/classid/456</a>',
            '457' => '胸痛,(胸痛气胀了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/457">http://ask.9939.com/classid/457</a>',
            '458' => '恶心欲呕吐,(恶心欲呕吐了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/458">http://ask.9939.com/classid/458</a>',
            '459' => '便秘,(便秘了怎么办以下方法供你参考<a href="http://jb.9939.com/dis/138673/">http://jb.9939.com/dis/138673/</a>',
            '460' => '眩晕,(眩晕了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/460">http://ask.9939.com/classid/460</a>',
            '461' => '瘫痪,(瘫痪了怎么办以下方法供你参考<a href="http://ask.9939.com/classid/461">http://ask.9939.com/classid/461</a>',
            '462' => '其它症状(全身症状),(有其它症状问题，找其它症状医院、医生亲到这里来<a href="http://ask.9939.com/classid/462">http://ask.9939.com/classid/462</a>',
            '464' => '咳嗽,(让你从容应对咳嗽的知识宝典<a href="http://ask.9939.com/classid/464">http://ask.9939.com/classid/464</a>',
            '465' => '耳鸣,(让你从容应对耳鸣的知识宝典<a href="http://ask.9939.com/classid/465">http://ask.9939.com/classid/465</a>',
            '466' => '甲状腺肿,(让你从容应对甲状腺肿的知识宝典<a href="http://ask.9939.com/classid/466">http://ask.9939.com/classid/466</a>',
            '467' => '颈肩痛,(让你从容应对颈肩痛的知识宝典<a href="http://ask.9939.com/classid/467">http://ask.9939.com/classid/467</a>',
            '468' => '眼痛,(让你从容应对眼瞳的知识宝典<a href="http://ask.9939.com/classid/468">http://ask.9939.com/classid/468</a>',
            '469' => '牙齿异常,(让你从容应对牙齿异常的知识宝典<a href="http://ask.9939.com/classid/469">http://ask.9939.com/classid/469</a>',
            '470' => '视力障碍,(让你从容应对视力障碍的知识宝典<a href="http://ask.9939.com/classid/470">http://ask.9939.com/classid/470</a>',
            '471' => '鼻出血,(让你从容应对鼻出血的知识宝典<a href="http://jb.9939.com/dis/138640/">http://jb.9939.com/dis/138640/</a>',
            '472' => '其它(头颈部),(有其它头颈部问题，找其它头颈部医院、医生亲到这里来<a href="http://ask.9939.com/classid/463">http://ask.9939.com/classid/463</a>',
            '474' => '心悸,(治疗心悸的超好方法，看看你知道吗<a href="http://ask.9939.com/classid/474">http://ask.9939.com/classid/474</a>',
            '475' => '高血压,(治疗高血压的超好方法，看看你知道吗<a href="http://jb.9939.com/dis/139141/">http://jb.9939.com/dis/139141/</a>',
            '476' => '低血压,(治疗低血压的超好方法，看看你知道吗<a href="http://ask.9939.com/classid/476">http://ask.9939.com/classid/476</a>',
            '477' => '心率失常,(治疗心率失常的超好方法，看看你知道吗<a href="http://ask.9939.com/classid/477">http://ask.9939.com/classid/477</a>',
            '478' => '心力衰竭,(治疗心力衰竭的超好方法，看看你知道吗<a href="http://ask.9939.com/classid/478">http://ask.9939.com/classid/478</a>',
            '479' => '男性乳房发育症,(治疗男性乳房发育症的超好方法，看看你知道吗<a href="http://ask.9939.com/classid/479">http://ask.9939.com/classid/479</a>',
            '480' => '乳汁分泌减少,(治疗乳汁分泌减少超好方法，看看你知道吗<a href="http://ask.9939.com/classid/480">http://ask.9939.com/classid/480</a>',
            '481' => '其它(胸部症状),(有其它胸部症状问题，找其它胸部症状医院、医生亲到这里来<a href="http://ask.9939.com/classid/473">http://ask.9939.com/classid/473</a>',
            '483' => '腹泻,(做好以下几点腹泻不再来<a href="http://jb.9939.com/dis/139078/">http://jb.9939.com/dis/139078/</a>',
            '484' => '腹痛,(做好以下几点腹痛不再来<a href="http://jb.9939.com/dis/139077/">http://jb.9939.com/dis/139077/</a>',
            '485' => '肝肿大,(做好以下几点肝肿大不再有<a href="http://ask.9939.com/classid/485">http://ask.9939.com/classid/485</a>',
            '486' => '腹部肿块,(做好以下几点腹部肿块不再有<a href="http://ask.9939.com/classid/486">http://ask.9939.com/classid/486</a>',
            '487' => '其它(腹部症状),(有其它腹部症状问题，找其它腹部症状医院、医生亲到这里来<a href="http://ask.9939.com/classid/487">http://ask.9939.com/classid/487</a>',
            '489' => '多尿,(你知道多尿是有什么引起的吗？快来了解一下吧<a href="http://ask.9939.com/classid/489">http://ask.9939.com/classid/489</a>',
            '490' => '少尿,(你知道少尿是有什么引起的吗？快来了解一下吧<a href="http://ask.9939.com/classid/490">http://ask.9939.com/classid/490</a>',
            '491' => '血尿,(你知道血尿是有什么引起的吗？快来了解一下吧<a href="http://jb.9939.com/dis/140754/">http://jb.9939.com/dis/140754/</a>',
            '492' => '肾病综合症,(你知道肾病综合症是有什么引起的吗？快来了解一下吧<a href="http://ask.9939.com/classid/492">http://ask.9939.com/classid/492</a>',
            '493' => '蛋白尿,(你知道蛋白尿是有什么引起的吗？快来了解一下吧<a href="http://ask.9939.com/classid/493">http://ask.9939.com/classid/493</a>',
            '494' => '其它(腰部症状),(有其它腰部症状问题，找其它要不症状医院、医生亲到这里来<a href="http://ask.9939.com/classid/494">http://ask.9939.com/classid/494</a>',
            '496' => '囊肿,(专家提示解决囊肿需要以下知识<a href="http://ask.9939.com/classid/496">http://ask.9939.com/classid/496</a>',
            '497' => '瘙痒,(专家提示解决瘙痒需要以下知识<a href="http://jb.9939.com/dis/140162/">http://jb.9939.com/dis/140162/</a>',
            '498' => '溃疡,(专家提示解决溃疡需要以下知识<a href="http://jb.9939.com/dis/139648/">http://jb.9939.com/dis/139648/</a>',
            '499' => '疱疹,(专家提示解决疱疹需要以下知识<a href="http://ask.9939.com/classid/499">http://ask.9939.com/classid/499</a>',
            '500' => '结节,(专家提示解决结节需要以下知识<a href="http://jb.9939.com/dis/139551/">http://jb.9939.com/dis/139551/</a>',
            '501' => '出汗异常,(专家提示解决出汗异常需要以下知识<a href="http://ask.9939.com/classid/501">http://ask.9939.com/classid/501</a>',
            '502' => '其它(皮肤症状),(有其它皮肤症状问题，找其它皮肤症状医院、医生亲到这里来<a href="http://ask.9939.com/classid/502">http://ask.9939.com/classid/502</a>',
            '504' => '骨质疏松,(解决骨质疏松的绝对好方法你试过吗<a href="http://jb.9939.com/dis/139243/">http://jb.9939.com/dis/139243/</a>',
            '505' => '肌无力,(解决肌无力的绝对好方法你试过吗<a href="http://ask.9939.com/classid/505">http://ask.9939.com/classid/505</a>',
            '506' => '肌肉萎缩,(解决肌肉萎缩的绝对好方法你试过吗<a href="http://ask.9939.com/classid/506">http://ask.9939.com/classid/506</a>',
            '507' => '其它(四肢症状),(有其它四肢症状问题，找其它四肢症状医院、医生亲到这里来<a href="http://ask.9939.com/classid/507">http://ask.9939.com/classid/507</a>',
            '509' => '性早熟,(性早熟了怎么办，了解以下知识不再烦<a href="http://jb.9939.com/dis/140724/">http://jb.9939.com/dis/140724/</a>',
            '510' => '痛经,(痛经了怎么办，了解以下知识不再烦<a href="http://jb.9939.com/dis/140390/">http://jb.9939.com/dis/140390/</a>',
            '511' => '白带,(白带多了怎么办，了解以下知识不再烦<a href="http://ask.9939.com/classid/511">http://ask.9939.com/classid/511</a>',
            '512' => '阴道出血,(阴道出血了怎么办，了解以下知识不再烦<a href="http://ask.9939.com/classid/512">http://ask.9939.com/classid/512</a>',
            '513' => '闭经,(闭经了怎么办，了解以下知识不再烦<a href="http://ask.9939.com/classid/513">http://ask.9939.com/classid/513</a>',
            '514' => '男子性功能障碍,(男子性功能障碍了怎么办，了解以下知识不再烦<a href="http://ask.9939.com/classid/514">http://ask.9939.com/classid/514</a>',
            '515' => '女性不孕,(女性不孕了怎么办，了解以下知识不再烦<a href="http://ask.9939.com/classid/515">http://ask.9939.com/classid/515</a>',
            '516' => '男性不育,(男性不育了怎么办，了解以下知识不再烦<a href="http://jb.9939.com/dis/139885/">http://jb.9939.com/dis/139885/</a>',
            '517' => '其它(生殖部位),(有其它生殖部位问题，找其它生殖部位医院、医生亲到这里来<a href="http://ask.9939.com/classid/517">http://ask.9939.com/classid/517</a>',
            '372' => '综合治疗,(更多综合治疗知识请到这里<a href="http://ask.9939.com/classid/372">http://ask.9939.com/classid/372</a>',
            '373' => '手术治疗,(更多手术治疗知识请到这里<a href="http://ask.9939.com/classid/373">http://ask.9939.com/classid/373</a>',
            '374' => '放疗,(更多放疗知识请到这里<a href="http://ask.9939.com/classid/374">http://ask.9939.com/classid/374</a>',
            '375' => '化疗,(更多化疗知识请到这里<a href="http://ask.9939.com/classid/375">http://ask.9939.com/classid/375</a>',
            '376' => '中医治疗,(更多中医治疗的知识请到这里<a href="http://ask.9939.com/classid/376">http://ask.9939.com/classid/376</a>',
            '377' => '偏方疗法,(更多偏方治疗肿瘤的知识请到这里<a href="http://ask.9939.com/classid/377">http://ask.9939.com/classid/377</a>',
            '378' => '肝癌,(更多肝癌预防和治疗知识请到这里<a href="http://ask.9939.com/classid/378">http://ask.9939.com/classid/378</a>',
            '379' => '肺癌,(更多肺癌预防和治疗知识请到这里<a href="http://jb.9939.com/dis/139000/">http://jb.9939.com/dis/139000/</a>',
            '381' => '胰腺癌,(更多胰腺癌预防和治疗知识请到这里<a href="http://jb.9939.com/dis/140875/">http://jb.9939.com/dis/140875/</a>',
            '382' => '直肠癌,(更多直肠癌预防和治疗知识请到这里<a href="http://jb.9939.com/dis/141045/">http://jb.9939.com/dis/141045/</a>',
            '383' => '其它,(有其它肿瘤问题，找其它肿瘤医院、医生亲到这里来<a href="http://ask.9939.com/classid/383">http://ask.9939.com/classid/383</a>',
        );
    }

}

?>