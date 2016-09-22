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
Zend_Loader::loadClass('Ask', MODELS_PATH);
Zend_Loader::loadClass('Diseasekeywords', MODELS_PATH);
Zend_Loader::loadClass('Experience', MODELS_PATH);
Zend_Loader::loadClass('Answer', MODELS_PATH);
Zend_Loader::loadClass('Keshi', MODELS_PATH);
Zend_Loader::loadClass('Member', MODELS_PATH);
Zend_Loader::loadClass('MemberDetail', MODELS_PATH);
Zend_Loader::loadClass('MemberDetailDoctor', MODELS_PATH);
//Zend_Loader::loadClass('Credit',MODELS_PATH);	#加载积分类
Zend_Loader::loadClass('Askadd', MODELS_PATH);
Zend_Loader::loadClass('Create',MODELS_PATH);
Zend_Loader::loadClass('Hotwords',MODELS_PATH);

function json_to_array($search) {//如果是嵌套的JSON用此
    $arr = array();
    foreach ($search as $k => $w) {
        if (is_object($w))
            $arr[$k] = json_to_array($w); //判断类型是不是object
        else
            $arr[$k] = $w;
    }
    return $arr;
}

class AskController extends Zend_Controller_Action {

    /**
     * Enter description here ...
     * @var Zend_View
     */
    private $ViewObj;
    private $AskObj = '';
    private $AnswerObj = '';
    private $MemberObj = '';
    private $memberDetailObj;
    private $member_detail_1 = 'MemberDetail';
    private $member_detail_2 = 'MemberDetailDoctor';
    private $Credit_obj = '';
    private $keshi_obj;
    private $CreateObj;
    private $HotwordsObj;
    private $DiseasekeywordsObj;
    private $ExperienceObj;

    public function init() {
        $this->mobile_redirect();
        $this->ViewObj = Zend_Registry::get('view');
        $this->AskObj = new Ask();
        $this->DiseasekeywordsObj = new Diseasekeywords();
        $this->ExperienceObj = new Experience();
        $this->AnswerObj = new Answer();
        $this->MemberObj = new Member();
        $this->memberDetailObj = new MemberDetail();
        $this->MemberDetailDoctor = new MemberDetailDoctor();
        $this->AnswerObj = new Answer();
        $this->disease_obj = new Disease();
        $this->CreateObj = new Create();
        $this->HotwordsObj = new Hotwords();
        //加载搜索类
        Zend_Loader::loadClass('GetSearchData', MODELS_PATH);
        $this->GetSearchData_obj = new GetSearchData();

//		$this->Credit_obj = new Credit();		#积分类
        $this->keshi_obj = new Keshi();
        parent::init();
    }

    private function mobile_redirect() {
        header('Cache-Control:no-cache,no-store,max-age=0,must-revalidate');
        header("Pragma: no-cache"); 
        $action_name = strtolower($this->getRequest()->getActionName());
        $flag = QLib_Utils_Function::ismobile();
        $redirect_url = "http://wapask.9939.com";
        switch ($action_name) {
            case "shows": {
                    $ask_id = $this->_getParam('id', 0);
                    $redirect_url = "http://wapask.9939.com/id/{$ask_id}.html";
                    break;
                }
        }
        if ($flag === true) {
            header("Location:$redirect_url", true, 302);
            exit;
        }
    }

    public function indexAction() {
        header('Location:http://ask.9939.com/Asking/index/', true, 301);
        exit;
    }

    public function diseasesAction() {
        $id = $this->getRequest()->getParam('id');
        $CATEGORY = $this->keshi_obj->cache_keshi();
        $keshi = $this->keshi_obj->get_keshi();

        $dis = array();
        $kk = 0;
        foreach ($keshi[$id]['child'] as $k => $v) {
            $n = 0;
            $kk = $v['id'];
            $dis[$kk] = array();
            foreach ($CATEGORY as $key => $val) {
                if ($val['pID'] == $v['id']) {
                    $dis[$kk][$n]['id'] = $val['id'];
                    $dis[$kk][$n]['name'] = $val['name'];
                    $n++;
                }
            }
            if (empty($dis[$kk])) {
                $dis[$kk][0]['id'] = '';
                $dis[$kk][0]['name'] = '';
            }
//		  		$kk++;
        }
        echo json_encode($dis);
    }

    /**
     * 问答投诉
     * @author 林原
     * @date 2010-10-14
     */
    public function tsAction() {
        //获取投诉类型 1,问题，2 回答
        $type = intval($this->_getParam("type"));
        if ($type != 1)
            $type = 2;

        //获取被投诉的用户名
        $uname = $this->_getParam("uname");
        if (!$uname)
            $uname = '匿名';

        //获取被投诉的用户id
        $uid = intval($this->_getParam("uid"));

        //内容id
        $id = intval($this->_getParam("id"));
        if ($id <= 0)
            exit;

        $this->ViewObj->type = $type; //类型
        $this->ViewObj->uname = $uname; //名字
        $this->ViewObj->uid = $uid;     //用户id
        $this->ViewObj->id = $id;   //内容id
        echo $this->ViewObj->render("ask_tousu.phtml");
    }

    public function loadcatAction() {
        $id = $this->_getParam('id');

        $id = intval($id);
        #if(!$id) $id='1653';
        $tmp_keshi_obj = new Keshi();
        $where = $tmp_keshi_obj->getValue('pid') . '=\'' . $id . '\'';
        $tmp_keshi_list_array = $tmp_keshi_obj->getList($where);

        $tmp_keshi_option_str = '';
        foreach ($tmp_keshi_list_array as $k => $v) {

            $tmp_keshi_option_str .= '<option value="' . $v[$tmp_keshi_obj->getValue('primary')] . '" class="' . $v[$tmp_keshi_obj->getValue('pid')] . '">' . $v[$tmp_keshi_obj->getValue('name')] . '</option>';
        }
        echo $tmp_keshi_option_str;
    }

    public function loadfuAction() {         //2011-7-27 科室
        $id = $this->_getParam('id');

        $id = intval($id);
        $tmp_keshi_obj = new Keshi();
        $where = $tmp_keshi_obj->getValue('pid') . '=' . $id;
        $tmp_keshi_array = $tmp_keshi_obj->getList($where);
        foreach ($tmp_keshi_array as $k => $v) {
            if ($v[$tmp_keshi_obj->getValue('primary')]) {
                $tmp_keshi_str .='<li onclick="category_load(' . $v[$tmp_keshi_obj->getValue('primary')] . ')">' . $v[$tmp_keshi_obj->getValue('name')] . '</li>';
            } else {
                $tmp_keshi_str .='<li onclick="category_load(' . $v[$tmp_keshi_obj->getValue('primary')] . ')"><span style="color:#ff0000;">' . $v[$tmp_keshi_obj->getValue('name')] . '</span></li>';
            }
        }
        echo $tmp_keshi_str;
    }

    public function fuAction() {        //2011-7-27常见疾病的上级分类
        $html = $info = $list = array();
        //获取url传递的参数id，即当前点击的分类id
        $id = $this->_getParam('id');
        $id = intval($id);
        $cat = new Keshi();
        //查找当前分类的信息，存储在$info中
        $where = $cat->getValue('primary') . ' = ' . $id;
        $info = $cat->getList($where);
        //获取当前分类的父id，存储于$id中
        $id = $pid = $info[0]['pID'];
        //循环获取当前分类的父分类信息
        while ($id > 0) {
            $where = $cat->getValue('primary') . ' = ' . $id;
            $info = $cat->getList($where);
            $id = $info[0]['pID'];
            //获取父分类下的所有分类
            $where = 'pId = ' . $id;
            $list = $cat->getList($where);
            if (!$list)
                break;
            //每个分类放在一个ul里面
            $html[] = '<ul>';
            foreach ($list as $k => $v) {

                if ($pid == $v['id']) {
                    $html[] = '<li><span style="color:#ff0000;">' . $v['name'] . '<span></li>';
                } else {
                    $html[] = '<li>' . $v['name'] . '</li>';
                }
            }
            $pid = $id;
            $html[] = '</ul>';
        }
        echo implode('', array_reverse($html));
    }

    public function loaddiseaesAction() {      //2011-7-18 查询那些是常见疾病
        //echo "11";exit;
        $tmp_keshi_obj = new Keshi();
        $where = $tmp_keshi_obj->getValue('common_diseases') . '=1';
        $common_diseaes_data = $tmp_keshi_obj->getList($where);
        //print_r($common_diseaes_data);
        echo json_encode($common_diseaes_data);
    }

    public function doAction() {
        $tmp_act = $this->getRequest()->getParam('do');
        $say_ok_array = array('looklist');
        if (!in_array($tmp_act, $say_ok_array)) {
            
        }

        if (in_array($tmp_act, get_class_methods(get_class($this)))) {
            $this->{strtolower($tmp_act)}();
        } else {
            Zend_Adver_Js::helpJsRedirect("/user", 1, '错误的访问地址');
        }
    }

    /**
     * @desc 快速提问
     * @author  xzxin 
     * @date 2010-09-17
     * 
     */
    public function quickAction() {
        //exit("ok");
        /* 防止重复提交 */

        $sessionask = substr(md5('ask'), 5, 5) . time() . substr(md5('9939.com'), 10, 5);
        setcookie('ask_wktime', $sessionask, time() + 600, '/', APP_DOMAIN);
        $aToken = new Zend_Session_Namespace('token');
        $aToken->unlock();
        $sToken = md5(time() . $id);
        $aToken->token = $sToken;
        $aToken->lock();
        $this->ViewObj->token = $sToken;
        /* 防止重复提交 */

        //分类id 林原 2010-09-25
        $classid = intval($this->_getParam("classid"));
        if (!$classid)
            $classid = 537;
        $this->ViewObj->classid = $classid;

        echo $this->ViewObj->render('/quick_ask_v2.phtml');
    }

    //生成验证码
    public function verifyAction() {
        $imgWidth = 70;
        $imgHeight = 30;
        $authimg = imagecreate($imgWidth, $imgHeight);
        $bgColor = ImageColorAllocate($authimg, 255, 255, 255);
        $fontfile = APP_ROOT . "/heiti.ttf";
        $white = imagecolorallocate($authimg, 234, 185, 95);
        imagearc($authimg, 150, 8, 20, 20, 75, 170, $white);
        imagearc($authimg, 180, 7, 50, 30, 75, 175, $white);
        imageline($authimg, 20, 20, 180, 30, $white);
        imageline($authimg, 20, 18, 170, 50, $white);
        imageline($authimg, 25, 50, 80, 50, $white);
        $noise_num = 800;
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
            imageline($authimg, mt_rand(0, $imgWidth), mt_rand(0, $imgHeight), mt_rand(0, $imgWidth), mt_rand(0, $imgHeight), $line_color);
        }
//        $randnum = rand(0, mb_strlen($str,"utf-8"));
//        //if ($randnum % 3)
//    //        $randnum += 1;
//        $str = mb_substr($str,$randnum,4,"utf-8");
        $str = $this->getVerifyCode();
        $session = new Zend_Session_Namespace("verify");
        $session->unlock();
        $session->verify = md5(strtoupper($str));
        $session->lock();
        ImageTTFText($authimg, 20, 0, 8, 25, $font_color, $fontfile, $str);
        ImagePNG($authimg);
        ImageDestroy($authimg);
    }

    private function getVerifyCode($length = 4) {
        $letters = 'bcdfghjklmnpqrstvwxyz';
        $vowels = 'aeiou';
        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            if ($i % 2 && mt_rand(0, 10) > 2 || !($i % 2) && mt_rand(0, 10) > 9)
                $code.=$vowels[mt_rand(0, 4)];
            else
                $code.=$letters[mt_rand(0, 20)];
        }

        return $code;
    }

    public function showsAction() {
//        $starttime = microtime(true);
        if ($this->_getParam("id")) { // 传递了ID参数，为老链接形式
            $id = intval($this->getRequest()->getParam('id'));
        } else { // 新链接形式  nanke-23/234234.html
            $keshiName = $this->_getParam(1);
            $keshiIndex = $this->_getParam(2);
            $id = $this->_getParam(3);
            echo "keshi name: $keshiName<br />";
            echo "keshi index: $keshiIndex<br />";
            echo "ask id: $askId<br />";
        }
        $s = $this->getRequest()->getParam('s');
        /* 防止重复提交 */
        $aToken = new Zend_Session_Namespace('token');
        $aToken->unlock();
        $sToken = md5(time() . $id);
        $aToken->token2 = $sToken;
        $aToken->lock();
        $this->ViewObj->token = $sToken;
        /* 防止重复提交 */

        $this->ViewObj->s = $s;
        if (!$id)
            return;
        $tmp_ask_info = $this->AskObj->get_one($id);
        
        $sphinx_search_words = $tmp_ask_info['title'];
        $tmp_ask_info['title'] = $this->replaceWordToChar($tmp_ask_info['title']);
        $tmp_ask_info['content'] = $this->replaceWordToChar($tmp_ask_info['content']);
        $tmp_ask_info['help'] = $this->replaceWordToChar($tmp_ask_info['help']);
        /* 更新浏览次数  */
        $this->ViewObj->curentuserid = $tmp_ask_info['userid'];
        if (!$tmp_ask_info) {
            //Zend_Adver_Js::helpJsRedirect("/", 1, '该问题已经不存在了!');
            //最新提问			
            $where = " status=1 ";
            $order = " etime desc";
            $tmp = array(
                'li_1' => '37', //公告
                'li_2' => '58', //常见病
                'li_3' => '59', //亚健康
                'li_4' => '60', //生活类
            );
            foreach ($tmp as $k => $v) {
                $tmp_ads_array = $this->getAds($v);
                $this->ViewObj->$k = $tmp_ads_array;
            }
            $this->error();
            exit;
        }

        //获取分类名
        $classid = $tmp_ask_info['classid'];
        if($tmp_ask_info['class_level3']>0){
            //如果有class_level3,则当前问题归类到疾病
            $classid = 'dis_'.$classid;
        }
        $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($classid), 1);
        $tmp_ask_info['classname'] = $CATEGORY[$classid]['name'];

        $navigation  = $this->getClassByAsks($classid);
        $tmp_ask_info['class_html'] = $navigation;
        
        $this->ViewObj->info = $tmp_ask_info;
        //新浪微薄
        $weibo_url = ASK_URL . "id/" . $tmp_ask_info['id'];
        $weibo_title = $tmp_ask_info['title'] . "（久久健康网）";
        $weibo_url_len = strlen($weibo_url);
        $weibo_title_len = strlen($weibo_title);
        $weibo_content_len = 140 - $weibo_url_len - $weibo_title_len / 2;
        $this->ViewObj->weibo_title = $weibo_title;
        $this->ViewObj->weibo_content = $this->AskObj->getSubstr($tmp_ask_info['content'], 0, $weibo_content_len);

        $tmp_user_cookie = $this->MemberObj->getInfo($tmp_ask_info['userid']); #提问者用户

        $tmp_user_cookie['linkurl'] = $tmp_ask_info['hiddenname'] ? '#' : HOME_9939_URL . 'user/?uid=' . $tmp_user_cookie['uid'];
        if (strpos($tmp_user_cookie['pic'], 'default.jpg')) {
            if ($tmp_user_cookie['uType'] == 1) {
                $tmp_user_cookie['pic'] = ASK_URL . '/images_ask/images/niming_dz.png';
            } else
                $tmp_user_cookie['pic'] = ASK_URL . '/images_ask/images/niming_ys.png';
        } else
            $tmp_user_cookie['pic'] = HOME_9939_URL . $tmp_user_cookie['pic'];

        $tmp_user_cookie['nickname'] = $tmp_ask_info['hiddenname'] ? '匿名用户' : $tmp_user_cookie['nickname'];
        $this->ViewObj->user_info = $tmp_user_cookie;
        
        $this->newObj($tmp_user_cookie['uType']);
        $this->ViewObj->user_info_detail = $this->memberDetailObj->get_one($tmp_ask_info['userid']);

        $where = 'askid=' . $tmp_ask_info['id'];
        $order = ' addtime asc';
        $this->AnswerObj->settbname($tmp_ask_info['id']);
        $tmp_answer_array = $this->AnswerObj->getList($where, $order);

        $tmp_good_answer = array(); #最佳答案

        $haveNormalCount = 0; //普通会员回答数量
        $haveDoctorCount = 0; //医生会员回答数量
        //此处获取回答用户信息
        $userid = array();
        foreach ($tmp_answer_array as $v) {
            $userid[] = $v['userid'];
        }
        $starttime = microtime(true);
        foreach ($userid as $v) {
            $usersInfoTmp[] = $this->MemberObj->getInfo($v);
        }
        $endtime = microtime(true);
        $userInfo = array();
        if(!empty($usersInfoTmp)){
            foreach ($usersInfoTmp as $v) {
                $userInfo[$v['uid']] = $v;
            }
        }
        $md = new MemberDetail();
        $md_doctor = new MemberDetailDoctor();
        foreach ($tmp_answer_array as $k => $v) {
            $tmp_answer_array[$k]['user_info'] = $v['user_info'] = $userInfo[$v['userid']];  #回复用户
            //获取回复用户详细信息
            if ($v['user_info']['uType'] == 1) { //普通会员
                $haveNormalCount ++;
                if (!preg_match("/(<a[^>]*9939[^>]*>)[^<]*[\x{4e00}-\x{9fa5}]+[^<]*(<\s*\/a\s*>)/u", $v['content'])) {
                    $v['content'] = preg_replace("/([\x{4e00}-\x{9fa5}]+)(<a[^>]*9939[^>]*>)[^<]*(<\s*\/a\s*>)/u", "$2<font color='#0000ff'>$1</font>$3", $v['content']);
                }
                $tmp_answer_array[$k]['user_detail_info'] = $md->get_one($v['userid']);
            } else {   //医生会员
                $tmp_answer_array[$k]['user_detail_info'] = $md_doctor->get_one($v['userid']);
                $haveDoctorCount ++;
            }
            $tmp_answer_array[$k]['user_info']['pic'] = $tmp_answer_array[$k]['user_info']['pic'] ? HOME_9939_URL . $tmp_answer_array[$k]['user_info']['pic'] : HOME_USER_DEFAULT_PIC;
            

            if ($tmp_ask_info['bestanswer'] && ($tmp_ask_info['bestanswer'] == $v['id'])) {
                $v['user_info']['pic'] = $v['user_info']['pic'] ? HOME_9939_URL . $v['user_info']['pic'] : HOME_USER_DEFAULT_PIC;
                $tmp_good_answer = $v;
                if ($v['uType'] == 1)
                    $haveNormalCount--;
                else
                    $haveDoctorCount--;
                unset($tmp_answer_array[$k]);
            }
        }
        if ($haveNormalCount > 0)
            $this->ViewObj->haveNormalAnswer = true; //存在医生会员的回答
        if ($haveDoctorCount > 0)
            $this->ViewObj->haveDoctorAnswer = true; //存在普通会员的回答
        foreach ($tmp_answer_array as $k => $v) {
            $answeridnum[] = $this->AnswerObj->praisestep_cache($tmp_answer_array[$k]['id']);
        }
        if (!empty($answeridnum[0])) {
            foreach ($answeridnum as $kn => $vn) {
                foreach ($vn as $key => $var) {
                    foreach ($tmp_answer_array as $k => $v) {
                        if ($v['id'] == $var['tid']) {
                            $tmp_answer_array[$k]['praise'] = $tmp_answer_array[$k]['praise'] + $var['praise'];
                            $tmp_answer_array[$k]['step'] = $tmp_answer_array[$k]['step'] + $var['step'];
                        }
                    }
                }
            }
        }
        if(isset($tmp_good_answer['id'])){
            $answerid[] = $this->AnswerObj->praisestep_cache($tmp_good_answer['id']);
            if (!empty($answerid[0])) {
                foreach ($answerid as $kn => $vn) {
                    foreach ($vn as $key => $var) {
                        if ($tmp_good_answer['id'] == $var['tid']) {
                            $tmp_good_answer['praise'] = $tmp_good_answer['praise'] + $var['praise'];
                            $tmp_good_answer['step'] = $tmp_good_answer['step'] + $var['step'];
                        }
                    }
                }
            }
        }
        
        $tmp_doc_answer = array();
        $tmp_user_answer = array();
               
        foreach ($tmp_answer_array as $k => $v) {
            $role_type=$v['user_info']['uType'];
            
            $content = $v['content'];
            $content=  strip_tags($content,'<p>');
            $content = str_replace('病情分析:', '', $content);
            $content = str_replace('指导意见:', '', $content);
            
            $v['content'] = $content;
            
            $suggest =  $v['suggest'];
            $suggest=  strip_tags($suggest,'<p>');
            $suggest = str_replace('病情分析:', '', $suggest);
            $suggest = str_replace('指导意见:', '', $suggest);
            $v['suggest'] = $suggest;
            
            switch ($role_type){
                case 1:{
                    $tmp_user_answer[] = $v;
                    break;
                }
                case 2:{
                    $tmp_doc_answer[] = $v;
                    break;
                }
            }
        }
        
        //网友关注
        $where = 'classid = '.$tmp_ask_info['classid'];
        $follow = $this->AskObj->List_Ask($where, 'id desc', 30, 0,'classid');
        foreach($follow as $k=>$val){
            if($k<10){
                $friend_follow[] = $val;
            }else if($k<20){
                $friend_follow_1[] = $val;
            }else{
                $friend_follow_2[] = $val;
            }
        }
        $this->ViewObj->friend_follow = $friend_follow; //网友关注 1-10条
        $this->ViewObj->friend_follow_1 = $friend_follow_1;//网友关注 11-20条
        $this->ViewObj->friend_follow_2 = $friend_follow_2;//网友关注 21-30条
        //相关热词
        $classlevel3 = $tmp_ask_info['class_level3'];
        if($tmp_ask_info['class_level3']=='0'){
            $keshilist =$this->keshi_obj->getKeshifenliCache(array($tmp_ask_info['classid']),1);
            foreach ($keshilist as $k=>$val1){
               if($val1['class_level3']!=0){
                   $classlevel3 = $val1['class_level3'];
                   break;
               }
            }
        }
//        $related_hot_words =$this->DiseasekeywordsObj->getListByDisease($classlevel3);
        
        if ($tmp_ask_info['class_level3'] != 0) {
            $res = $this->DiseasekeywordsObj->getListByDisease($tmp_ask_info['class_level3']);
        } else {
            $param = array();
            if ($tmp_ask_info['class_level2'] == 0) {
                $param = array(
                    'class_level1' => $tmp_ask_info['class_level1'],
                );
            } else {
                $param = array(
                    'class_level2' => $tmp_ask_info['class_level2'],
                );
            }
            $res = $this->DiseasekeywordsObj->getListByClassid($param);
            
        }
        $hots = array();
        $i = 0;
        foreach ($res as $k => $v) {
            if (!empty($v['name'])) {
                $hots[] = $v;
                $i++;
            }
            if ($i >= 10) {
                break;
            }
        }
        $this->ViewObj->related_hot_words = array_slice($hots, 0.10);
        //相关经验
        $related_jingyan = $this->ExperienceObj->getDiseaseExpList($classlevel3,0,7);
        $this->ViewObj->related_jingyan = $related_jingyan;
        
        $this->ViewObj->answerList = $tmp_answer_array;
        $this->ViewObj->doc_answerList = $tmp_doc_answer;
        $this->ViewObj->user_answerList = $tmp_user_answer;
        $this->ViewObj->goodAnswer = $tmp_good_answer; //最佳答案
        $this->ViewObj->askid = $id;
        $this->ViewObj->AskObj = $this->AskObj;
        $this->ViewObj->keshiid = $tmp_ask_info['classid'];
        $tmp_user_isloogin = $this->MemberObj->getCookie();
        $tmp_user_isVip = $this->MemberObj->GetValue($tmp_user_isloogin['uid'], 'isVip');
        $this->ViewObj->user_isloogin_info = $tmp_user_isloogin; #当前登录用户
        //判断当前提问者与登录用户是否一致，问题是否通过审核
        //针对用户发垃圾信息的情况
        //examine 1已审核 0未审核
        if( $tmp_ask_info['examine'] == '0' ){
            if ( $tmp_user_isloogin['uid'] !== $tmp_ask_info['userid'] ) {
                if((int)$tmp_user_isVip !== 1){
                    $this->error();
                    exit;
                }
            }
        }

        $userStatus = array(); //用户状态及信息
//                $tmp_user_isloogin['uid'] = 1135113;
        /*
         * 以下判断用户是否注册及登录状态
         */
        //判断用户是否登录
        if ($this->MemberObj->isLogin()) {
            //获取会员信息
            $userStatus['userInfo'] = $this->MemberObj->get_one_by_id($tmp_user_isloogin['uid']);
            $userType = $userStatus['userInfo']['uType']; //获取用户类型
            //根据会员类型 获取会员详细信息
            if ($userType == 1 || $userType == 3) {//普通用户 QQ用户
                $userStatus['statusNum'] = 2;
                $userStatus['userDetail'] = $this->memberDetailObj->get_one($tmp_user_isloogin['uid']);
            } elseif ($userType == 2) {//医生用户
//                        print_r($userStatus['userInfo']['group_status']);exit;
                if ($userStatus['userInfo']['isVip'] == 1) {//0免费会员 1付费会员 2住站医生
                    $userStatus['statusNum'] = 4;
                } else {
                    $userStatus['statusNum'] = 3;
                }
                $userStatus['userDetail'] = $this->MemberDetailDoctor->get_one($tmp_user_isloogin['uid']);
            }
        } else {
            $userStatus['statusNum'] = 1;
        }
        $this->ViewObj->userStatus = $userStatus;
        
        /* 获取问题补充列表 */
        $askadd = new Askadd();
        $this->ViewObj->askAddList = $askadd->getList("askid=$id", "addtime");

        /* 投诉选项 */
        $tousuArr = array(1 => '谩骂诽谤', '色情淫秽', '无意义灌水', '政治敏感性内容', '广告', '暴力犯罪', '无满意答案', '医生回答内容肤浅应付');
        $this->ViewObj->tousuArr = $tousuArr;
        $this->ViewObj->description = $this->getpagedescription($tmp_ask_info);

        /*
         * 获取科室
         * licheng 2015-9-9 18:36:47
         */
        Zend_Loader::loadClass('Keshi', MODELS_PATH);
        $this->Keshi_obj = new Keshi();
        //科室部分
        $ask_classid = ($tmp_ask_info['class_level3'] == 0) ? $tmp_ask_info['classid'] : 'dis_'.$tmp_ask_info['class_level3'];
        $this->ViewObj->order = $this->Keshi_obj->a_department($ask_classid);
        $this->ViewObj->shicha = $endtime - $starttime;
         
        //右侧相关疾病文章
        $jb_title = QLib_Utils_String::cutString($tmp_ask_info['title'], 6, '...');
        $this->ViewObj->jb_title = $jb_title;
        
        
        //问题相关的问答及文章
        $return_rel_info = $this->search_article_ask($sphinx_search_words);
        //相关问题
        $related_problem =$return_rel_info[1];
        $this->ViewObj->relatedProblem = $related_problem['list'];
        //局部缓存
        $filename = 'detail_jb_'.$id;
        $jb_art_lists = QLib_Cache_Client::getCache('pages/part/detail', $filename);
        if(!$jb_art_lists){
            $jb_art_lists = $return_rel_info[0];
            QLib_Cache_Client::setCache('pages/part/detail', $filename, $jb_art_lists, 24);
        }
        $this->ViewObj->rel_article = $jb_art_lists['list'];
        
       
        //底部热词 licheng 2015-11-25 start
        $searchurl = 'http://ask.9939.com/hot/';
        $letterurl = 'http://ask.9939.com/hot/';
        $letter_list = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($letter_list);
        $return_list = array();
        for($i=0;$i<$len;$i++){
         $l = strtoupper($letter_list{$i});
         $return_list[$l]  = array(
                                 'url'=>  sprintf('%s%s/',$letterurl,$l),
                                 'selected'=>($this->ViewObj->letter==$l)?1:0
                             );
        }
        $this->ViewObj->letter = 'A';//默认当前字母
        $this->ViewObj->letter_list = $return_list;
        $this->ViewObj->searchurl = $searchurl;
        //随机关键词 局部缓存
        $filename = 'detail_letter_'.$id;
        $randwords = QLib_Cache_Client::getCache('pages/part/detail_letter', $filename);
        if(!$randwords){
            $randwords = $this->HotwordsObj->rand_words();
            QLib_Cache_Client::setCache('pages/part/detail_letter', $filename, $randwords, 24);
        }
        $this->ViewObj->randWords = $randwords;// $zimuArr;//底部字母关键词

        //底部常见疾病
        $hotDepPart = new HotDepPart();
        $this->ViewObj->hotDepPart = $hotDepPart->getCommonDisDep(7);

        //底部热词 end
        $arr_title = array();
        $title = strip_tags($tmp_ask_info['title']);
        $content = strip_tags($tmp_ask_info['content']);
        if (!empty($title)) {
            array_push($arr_title, $title);
        }
        array_push($arr_title, '久久问医');
        if (isset($common_config['title'])) {
//                array_push($arr_title, $common_config['title']);
        }
        $this->ViewObj->title = implode('_', $arr_title);

        $arr_keywords = array();
        array_push($arr_keywords, $title);
        $this->ViewObj->keywords = implode(',', $arr_keywords);

        $arr_description = array();
        array_push($arr_description, $title);
        array_push($arr_description,  QLib_Utils_String::cutString($content, 20,0));
        if (isset($common_config['description'])) {
//                array_push($arr_description, $common_config['description']);
        }
        $this->ViewObj->description = implode(',', $arr_description);
        
        //右侧名医推荐 lc@2016-6-15
        $famousDoctors = SiteHelper::getRecommendDoc($this->ViewObj->order);
        $this->ViewObj->famousDoctors = $famousDoctors;

        //医院推荐
        $recommendHospital = SiteHelper::getRecommendHospital($this->ViewObj->order);
        $this->ViewObj->recommendHospital = $recommendHospital;
        
        //药品推荐
        $recommendDrug =  SiteHelper::getRecommendDrug($this->ViewObj->order);
        $this->ViewObj->recommendDrug = $recommendDrug;
        
        echo $this->ViewObj->render('/show_ask_news.phtml');
    }
    
    //批量获取文章及问答
    private function search_article_ask($cn_key_name){
        $rel_conditon = array();
        $queries = array(
            array('word'=>$cn_key_name,'indexer'=>'index_9939_com_jb_art','offset'=>0,'size'=>9,'condition'=>array()),
            array('word'=>$cn_key_name,'indexer'=>'index_wd_ask,index_wd_ask_history_1,index_wd_ask_history_2,index_wd_ask_history_3,index_wd_ask_history_4,index_wd_ask_history_5,index_wd_ask_history_6,index_wd_ask_history_7','offset'=>0,'size'=>8,'condition'=>$rel_conditon),
        );
        $result = array();
        $ret = QLib_Utils_SearchHelper::batchSearch($queries);
        foreach($ret as $kk=>$ret){
            $indexer_name = $ret['indexer'];
            $sphinx_result = Search::parse_search_data($ret,$indexer_name);
            $result[]=$sphinx_result;
        }
        return $result;
    }
    
    function getpagedescription($tmp_ask_info) {
        $title = $tmp_ask_info['title'];
        $sex = '性别：' . $tmp_ask_info['sexnn'] == 1 ? '男' : '女';
        $age = '年龄：' . $tmp_ask_info['age'];
        $content = $tmp_ask_info['content'];
        $description = $title . $sex . $age . $content;
        //sprintf('%s%s%s', $tmp_ask_info['title'],'性别：'.$tmp_ask_info['sexnn']==1?'男':'女',' 年龄：<?=$this->info['age']=$content
        return $this->AskObj->getSubstr($description, 0, 76);
    }

    /**
     * 问题补充 
     */
    function askaddAction() {
        $content = $this->_getParam('content'); //补充的内容
        if (!trim($content)) {
            echo 'empty'; //内容为空
            exit;
        }

        $id = intval($this->_getParam('askid')); // 问题的id
        if ($id <= 0) {
            echo 'iderror'; //问题id错误
            exit;
        }


        $user = $this->MemberObj->getCookie(); //获取登录用户cookie
        $ask = $this->AskObj->get_one($id); //获取问题


        if (!$ask['id']) {
            echo 'notfound'; //问题不存在
            exit;
        }

        if ($ask['bestanswer'] > 0 || $ask['status'] == 1) {
            echo 'isclose';  //已经解决
            exit;
        }

        //15天
        /* $endtimes = $ask['ctime']+(86400*15);//应该的结束时间
          if(time()>$endtimes) {
          echo 'timeout'; //时间到了
          exit;
          } */

        if ($user['uid'] != $ask['userid']) {
            echo 'nopower';  //没有权限修改
            exit;
        }

        //验证字符串是否含有非法词语
        Zend_Loader::loadClass('CheckData', MODELS_PATH);
        if (!CheckData::isSafeStr($content)) {
            echo 'notsafe';
            exit;
        }



        $askadd = new Askadd();
        //$askadd->_name = ($this->AskObj->getasktable($id)). '_add';
        $addtime = time(); //补充时间

        $param = array('askid' => $id, 'addtime' => $addtime, 'content' => $content);


        if ($askadd->add($param)) {
            echo 'ok';
        } else {
            echo 'lose';
        }
    }

    /**
     * 投诉
     */
    function tousu2Action() {

        //判断权限
        $user = $this->MemberObj->getCookie(); //获取登录用户cookie
        $uid = $user['uid'];
        if (!$user['uid']) {
            Zend_Adver_Js::Goback('请先登陆！');
            exit;
        }

        $id = intval($this->_getParam('id')); //获取要投诉的内容id
        if (!$id) {
            echo 'iderror'; //id错误
            exit;
        }

        $content = $this->_getParam('tousu_content'); //获取投诉的内容
        if (!$content) {
            Zend_Adver_Js::Goback('请填写投诉内容！');
            exit;
        }

        $item = intval($this->_getParam('tousuItem')); //投诉的选项
        if (!$item) {
            echo 'item_error'; //投诉的选项错误
            exit;
        }

        $type = intval($this->_getParam('type')); //投诉的类型 1,问题，2 回答
        if (($type != 1) && ($type != 2)) {
            echo 'type_error';
            exit;
        }
//                echo '<script>alert("投诉成功！");window.top.opener = null;window.close();</script>';exit;

        Zend_Loader::loadClass('Tousu', MODELS_PATH);
        $tousu = new Tousu();
        $now = time();
        $param = array('content_id' => $id, 'content_type' => $type, 'uid' => $uid, 'time' => $now, 'class' => $item, 'content' => $content);
        if ($tousu->add($param)) {
            if ($type == '2') {
                //付费医生用户投诉记录 //wpg
                Zend_Loader::loadClass('Answer', MODELS_PATH);
                Zend_Loader::loadClass('Member', MODELS_PATH);
                $Answer = new Answer();
                $Answerdate = $Answer->get_one($id);
                $Member = new Member();
                $Memberdata = $Member->get_one_by_id($Answerdate['userid']);
                if ($Memberdata['uType'] == "2" && $Memberdata['isVip'] == "1") {
                    Zend_Loader::loadClass('WageAnswer', MODELS_PATH);
                    $WageAnswer = new WageAnswer();
                    $WageAnswer->addWageTousu($id, $Answerdate['userid']);
                }
            }
            //////////////////////
            echo '<script>alert("投诉成功！");window.top.opener = null;window.close();</script>';
        } else {
            Zend_Adver_Js::Goback('投诉失败！');
            exit;
        }
    }

    /**
     * 投诉
     */
    function tousuAction() {

        //判断权限
        $user = $this->MemberObj->getCookie(); //获取登录用户cookie
        $uid = $user['uid'];
        if (!$user['uid']) {
            echo 'nologin'; //没登录
            exit;
        }

        $id = intval($this->_getParam('id')); //获取要投诉的内容id
        if (!$id) {
            echo 'iderror'; //id错误
            exit;
        }

        $content = $this->_getParam('content'); //获取投诉的内容
        if (!$content) {
            echo 'content_empty'; //内容为空
            exit;
        }

        $item = intval($this->_getParam('item')); //投诉的选项
        if (!$item) {
            echo 'item_error'; //投诉的选项错误
            exit;
        }

        $type = intval($this->_getParam('type')); //投诉的类型 1,问题，2 回答
        if (($type != 1) && ($type != 2)) {
            echo 'type_error';
            exit;
        }

        Zend_Loader::loadClass('Tousu', MODELS_PATH);
        $tousu = new Tousu();
        $now = time();
        $param = array('content_id' => $id, 'content_type' => $type, 'uid' => $uid, 'time' => $now, 'class' => $item, 'content' => $content);
        if ($tousu->add($param)) {
            echo 'ok'; //成功！
        } else {
            echo 'lose'; //失败
        }
    }

    /**
     * 编辑 回答
     */
    function editanswerAction() {
        $user = $this->MemberObj->getCookie(); //获取登录用户cookie
        $uid = $user['uid'];
        if (!$uid) {
            echo 'nologin'; //没登录
            exit;
        }

        $answerId = intval($this->_getParam('id')); //回复的id
        $answer = $this->AnswerObj->get_one($answerId);
        if ($answer) {
            //判断权限修改
            if ($answer['userid'] != $uid) {
                echo 'nopower'; //没权限
                exit;
            }
        } else {
            echo 'id_error';
            exit;
        }

        $content = $this->_getParam('content'); //回复内容
        if (!trim($content)) {
            echo 'content_empty';
            exit;
        }

        //验证字符串是否含有非法词语
        Zend_Loader::loadClass('CheckData', MODELS_PATH);
        if (!CheckData::isSafeStr($content)) {
            echo 'notsafe';
            exit;
        }

        $answer['content'] = $content;

        //修改
        if ($this->AnswerObj->edit($answer)) {
            echo 'ok';
        } else {
            echo 'no';
        }
    }

    /**
     * 获取问题补充列表
     */
    function getaskaddAction() {
        $askid = intval($this->_getParam('askid'));
        if (!$askid) {
            echo '';
            exit;
        }
        $askadd = new Askadd();
        $list = $askadd->getList("askid=$askid", "addtime");
        if ($list) {
            $str = "";
            $count = 0;
            foreach ($list as $val) {
                $count++;
                $str .= ' <div class="ask_added"><strong>问题补充' . $count . ':</strong>(' . date("Y-m-d H:i:s", $val['addtime']) . ')<br/>' . nl2br($val['content']) . '</div>';
            }
            echo $str;
        } else {
            echo '';
        }
    }

    /**
     * 
     * 采纳最佳答案
     */
    public function overAction() {
        $id = intval($this->_getParam('id'));
        $aid = intval($this->_getParam('askid'));
        if (!$id)
            return;
        $tmp_user_isloogin = $this->MemberObj->getCookie();
        $tmp_ask_user = $this->AskObj->get_one($aid);

        #$tmp_ask_user['userid'] = $tmp_user_isloogin['uid'] = 8; #测试使用

        if ($tmp_ask_user['userid'] != $tmp_user_isloogin['uid']) {
            Zend_Adver_Js::Goback('您的权限不够!');
        } else {
            $param = array(
                'id' => $aid,
                'bestanswer' => $id,
                'etime' => time(),
                'status' => 1
               // 'prohibit_reply' => 1 //设置采纳之后，不可再进行回复
            );
            if ($this->AskObj->edit($param)) {

                ######获得悬赏积分#####
                if ($tmp_ask_user['point'] > 0) {
                    $tmp_answer_best_array = $this->AnswerObj->get_one($id);
                    $tmp_answer_best_user_array = $this->MemberObj->get_one_by_id($tmp_answer_best_array['userid']);
                    $tmp_user_credit = $tmp_answer_best_user_array['credit'] + $tmp_ask_user['point'];
                    $arr = array('credit' => $tmp_user_credit);
                    $this->MemberObj->Edit($arr, $tmp_answer_best_array['userid']);
                }
                ######获得悬赏积分#####
//				Zend_Adver_Js::helpJsRedirect("/id/".$aid, 1, '采纳答案成功!');
                echo 'success';
                exit;
            } else {
//				Zend_Adver_Js::helpJsRedirect("/id/".$aid, 1, '采纳答案失败!');
                echo 'failed';
                exit;
            }
        }
    }

    /**
     * 
     * 加载HTML文件
     */
    public function loadhtmlAction() {
        //exit();

        $tpl = $this->_getParam('html');
        $id = $this->_getParam('id');
        $this->ViewObj->aid = $id;
        if ($tpl == 'edit_question') {
            echo $this->ViewObj->render('/question_edit.phtml');
        } else if ($tpl == 'edit_point') {
            $tmp_memeber_cookie = $this->MemberObj->getCookie();
            #print_r($tmp_memeber_cookie);
            $tmp_point_option = '';
            if ($tmp_memeber_cookie['credit'] < 20) {
                $tmp_point_option .= '<option value="0">对不起！您没有可用积分！</option>';
            } else {
                for ($i = 20; $i <= 100; $i = $i + 20) {
                    if ($i < $tmp_memeber_cookie['credit']) {
                        $tmp_point_option .= '<option value="' . $i . '">' . $i . '分</option>';
                    }
                }
            }

            $this->ViewObj->point_option = $tmp_point_option;
            echo $this->ViewObj->render('/point_edit.phtml');
        } else if ($tpl == 'ok') {
            $tmp_memeber_cookie = $this->MemberObj->getCookie();
            $aid = $this->_getParam('id');
            #echo $this->ViewObj->aid, '---', $aid;
            $tmp_memeber_cookie['pwd'] = $pwd = $this->_getParam('pwd');
            if ($this->_getParam('flag'))
                unset($tmp_memeber_cookie['pwd']);
            $this->ViewObj->user_info = $tmp_memeber_cookie;
            $this->ViewObj->doctor = $this->getAds(50);
            #print_r($this->ViewObj->doctor);
            $tmp_ask_array = $this->AskObj->get_one($aid);
            #print_r($tmp_ask_array);
            $tmp_kw = preg_replace("/\s|&nbsp;/", "20%", $tmp_ask_array['title']);
            $tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw=' . $tmp_kw . '&page=1');
            $this->GetSearchData_obj->SetXmlData($tmp_xml);
            $tmp_list = $this->GetSearchData_obj->GetList();
            if (is_array($tmp_list)) {
                sort($tmp_list);
                $tmp_list = array_splice($tmp_list, 0, 5);
                foreach ($tmp_list as $k => &$v) {
                    $v['adsname'] = nl2br($v['adsname']);
                    $v['desc'] = nl2br($v['desc']);
                    $v['url'] = '/id/' . $v['ID'];
                    $v['TITLE'] = mb_substr(strip_tags($v['TITLE']), 0, 45, 'utf8');
                }
            }
            #print_r($tmp_list);
            $this->ViewObj->ask = $tmp_list;
            echo $this->ViewObj->render('/question_ok.phtml');
        } else if ($tpl == 'okhd') {
            $tmp_memeber_cookie = $this->MemberObj->getCookie();
            $this->MemberObj->add_ask_chg($tmp_memeber_cookie['uid']);
            $tmp_memeber_cookie['pwd'] = $pwd = $this->_getParam('pwd');
            if ($this->_getParam('flag'))
                unset($tmp_memeber_cookie['pwd']);
            $username = $tmp_memeber_cookie['username'];
            setcookie('passed', 'ok', time() + 3600, '/', '.9939.com');
            session_start();
            $_SESSION['aa'] = '';
            $arr = array('name' => $username, 'pwd' => $pwd);
            echo json_encode($arr);
            exit;
        }else if ($tpl == 'xinhuifu') {
            $id = intval($this->_getParam('id'));
            Zend_Loader::loadClass('Friend', MODELS_PATH);
            $this->fri = new Friend();
            $arn = $this->fri->askreplay2($id); //问答回复
            echo json_encode(array("error" => "1", "arn" => $arn));
            exit;
        } elseif ($tpl == 'showlogin') {
            $id = intval($this->_getParam('id'));
            $so = intval($this->_getParam('so'));
            $get_callback = $this->getRequest()->getParam('jsoncallback');
            Zend_Loader::loadClass('Friend', MODELS_PATH);
            $this->fri = new Friend();
            $db = $this->fri->getAdapter();
            $member = $db->fetchRow("select * from member where uid=$id");
            $fan = $this->fri->friendask($id); //好友请求
            $tishi = "问答回复";
            if ($member['uType'] == 2) {
                $tishi = "问答回复";
                $arn = $this->fri->askreplay($id); //问答回复
            } else {
                $tishi = "有新回复";
                $arn = $this->fri->askreplay2($id); //问答回复
            }
            $fnn = $this->fri->friendnews($id); //好友消息 
            $temp1 = "<span><a href='" . HOME_9939_URL . "news/index/hyqq/1'>好友请求 " . $fan . "条</a></span><span><a href='" . HOME_9939_URL . "news/index/wdhf/1
			'>" . $tishi . " " . $arn . "条</a></span><span><a href='" . HOME_9939_URL . "news'>好友消息 " . $fnn . "条</a></span>";
            if ($_COOKIE['member_uType'] == 2) {
                $home_dir = "doctor";
            } else {
                $home_dir = "Ask";
            }
            $temp2 = "<li><span><a href='" . HOME_9939_URL . "{$home_dir}'>我的提问</a></span></li><li><span><a href='/Ask'>我要提问</a></span></li><li><span><a href='" . HOME_9939_URL . "news/index/notice/1'>站内消息</a></span></li><li><span><a href=" . HOME_9939_URL . "user/do/do/edit>完善资料</a></span></li>";

            //热点推荐
            include('/home/web/htsns-9939-com/data/data_adsplace_70.php');
            if ($_ADSGLOBAL['70']) {
                foreach ($_ADSGLOBAL['70'] as $k => $v) {
                    $rdtj .= "<li><a href=" . $v['linkurl'] . ">" . $v['adsname'] . "</a></li>";
                }
            }
            $arr = array('w_up' => $temp2, 'w_right' => $temp1, 'w_hot' => $rdtj);
            if ($so)
                echo $get_callback . "('" . json_encode($arr) . "')";
            else
                echo json_encode($arr);
            exit;
        }
        $tag_id = $this->_getParam('tag_id');
        if ($tag_id) {
            echo '<script>var __tmp_data=$("#' . $tag_id . '").html();if(!__tmp_data){__tmp_data="请在此输入补充提问内容";}$("#add_content").val(__tmp_data);</script>';
        }
    }

    public function loadhomeAction() {
        $url = $this->_getParam('url');
        #echo HOME_9939_URL . $url;
        $data = file_get_contents(HOME_9939_URL . $url);

        //添加图片的完整路径
        $data = preg_replace('/(\'|")(\/images_home)/', "\\1" . HOME_9939_URL . "\\2", $data);
        //$data = <img src="
        #$data = file_get_contents('/home/web/htsns-9939-com/application/views/scripts/home/friend_add.phtml');
        echo $data;
    }

    /*
     * 好友请求发送HOME.9939.com
     * 页面显示与home相同；调页面请到home
     * @author: kxgsy163@163.com
     */

    public function homeAction() {
        $url = $this->_getParam('url');
        $note = $this->getRequest()->getParam('note');
        $uid = $_COOKIE['member_uID'];
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                "Cookie: member_uID=$uid\r\n"
            )
        );
        $context = stream_context_create($opts);
        $data = '/note/' . $this->_getParam('note');
        $data = file_get_contents(HOME_9939_URL . $url . $data, false, $context);
        if ($data == 'error') {
            Zend_Adver_Js::Goback('好友添加失败！');
        } else {
            $data = (preg_replace("/.*?alert\((.*?)\).*/is", "\\1", preg_replace('/[\'|"]/', '', $data)));
            Zend_Adver_Js::Goback($data);
        }
    }

    private function save() {
        $param = $this->_getParam('info');
        if (strlen($param['title']) < 2) {  #提问标题
            echo 0;
            #echo 'title';
            exit;
        }
        if (!preg_replace("/\s/", '', $param['content'])) {  #问题描述
            echo 0;
            #echo 'content';
            exit;
        }

        if (!$param['classid']) {   #科室
            echo 0;
            #echo 'classid';
            exit;
        }
        //$param['title'] = iconv('utf-8', 'utf-8', $param['title']);
        //$param['title'] = iconv('utf-8', 'utf-8', $param['title']);
        $tmp_member_cookie = $this->MemberObj->getCookie();
        if (!$tmp_member_cookie['uid']) {
            #echo 'hello1';
            if ($this->_getParam('register')) { #账户注册
                #echo 'hello2';
                $username = $this->_getParam('mail');
                $username = preg_replace("/\s/", '', $username);
                if (!preg_match("/.*@.*\.\w*/", $username)) {
                    echo 0, 'email';
                }
                #echo 'hello3';
                $pwd = rand(100000, 999999);
                $tmp_new_user['username'] = $username;
                $tmp_new_user['password'] = $pwd;
                $tmp_new_user['dateline'] = time();
                $tmp_new_user['nickname'] = substr($username, 0, strpos($username, '@'));
                Zend_Loader::loadClass('Register', MODELS_PATH);
                $tmp_register_obj = new Register();
                $where = ' username=\'' . $username . '\'';
                if ($this->MemberObj->get_one($where)) {
                    echo 'error-100';
                    exit;
                }
                #echo 'hello4';
                $tmp_new_user_id = $tmp_register_obj->register_member($tmp_new_user);
                #echo '---';
                if ($tmp_new_user_id) {
                    #echo 'hello5';
                    if (!($this->MemberObj->checklogin(array('username' => $username, 'password' => $pwd)))) {
                        echo '404';
                        exit;
                    }
                    #echo 'hello66';
                } else {
                    echo '100';
                    exit;
                }
                #echo 'hello6';
            } else { #账户登录
                $username = $this->_getParam('username');
                $pwd = $this->_getParam('pwd');
                #echo 'hello7';
                if (!($this->MemberObj->checklogin(array('username' => $username, 'password' => $pwd)))) {
                    echo '404';
                    exit;
                }
                #echo 'hello8';
                $sFlag = true;
            }
            #echo 'hello9';
            #$where = ' username=\''. $username .'\'';
            $where = ' username=\'' . ($tmp_user_cookie['username'] ? $tmp_user_cookie['username'] : $username) . '\'';
            $tmp_member_cookie = $this->MemberObj->get_one($where);
            #echo 'hello10';
            #$tmp_member_cookie = $this->MemberObj->getCookie();
            #$tmp_member_cookie['uid'] = $tmp_member_cookie['uid'] ? $tmp_member_cookie['uid'] : 
        }
        $param['userid'] = $tmp_member_cookie['uid'];
        $param['status'] = 0;

        #echo 'hello ';exit;

        $id = $this->AskObj->add($param);
        setcookie('askid', $id, time() + 3600, '/', '.9939.com');
        $str = require('/home/web/ask-9939-com/askNum_manage.php');
        $str++;
        @file_put_contents('/home/web/ask-9939-com/askNum_manage.php', '<?php return ' . $str . ';?>');  #问答总数生成头部使用
        @file_put_contents('/home/web/ask-9939-com/askNum.php', "document.write('$str');");
        if ($param['point'] > 0) {  #悬赏扣除积分
            $tmp_user_credit = $tmp_member_cookie['credit'] - $param['point'];
            ($tmp_user_credit > 0) or ( $tmp_user_credit = 0);
            $this->MemberObj->Edit(array('credit' => $tmp_user_credit), $param['userid']);
            $this->MemberObj->ssetcookie('member_credit', $tmp_user_credit);
        }
        $str = $this->Credit_obj->updatespacestatus("get", "ask_pub");  #积分
        echo $id;
        echo '/pwd/' . $pwd;
        echo $sFlag ? '/flag/1' : '';
    }

    public function testAction() {
        $param['classid'] = 537;
        $classid = $param['classid'];
        $result = array();  //结果数组
        $nowCount = 0;  //当前的数量
        $needCount = 10; //需要的数量
        $arr = $this->AskObj->fetchAll("classid=$classid AND answernum>0", "id desc", 10);
        if ($arr) {
            $result = $arr->toArray();
            $nowCount = count($result);
        }
        if ($nowCount < $needCount) {  //如果不够 调取分类顶级分类下面所有的
            $keshiinfo = $this->keshi_obj->fetchRow("id=$classid");
            $classidstr = $keshiinfo->arrparentid . ',' . $keshiinfo->arrchildid;
            $classidstr = trim($classidstr, ',');
            if ($classidstr) {
                $arr2 = $this->AskObj->fetchAll("classid IN ($classidstr) AND answernum>0", "id desc", 10);
                if ($arr2) {
                    $result = array_merge($result, $arr2->toArray());
                    $nowCount = count($result);
                }
            }
        }
        if ($nowCount < $needCount) {  //如果还不够调取所有的
            $arr3 = $this->AskObj->fetchAll("answernum>0", "id desc", 10);
            if ($arr3) {
                $result = array_merge($result, $arr3->toArray());
                $nowCount = count($result);
            }
        }
        if ($nowCount > $needCount) {
            $result = array_splice($result, 10); //移除多余的
        }
        $this->ViewObj->asklist = $result;
        echo $this->ViewObj->render("/question_cross_new.phtml");
        #echo $tmp_kw = $this->_getParam('kw');
//		$tmp_kw = '一点都很郁闷';
//		$tmp_xml = file_get_contents('http://211.167.92.198:8080/ask/search?kw='.$tmp_kw.'&page=1');
//		$this->GetSearchData_obj->SetXmlData($tmp_xml);
//		$tmp_list            = $this->GetSearchData_obj->GetList();
//		print_R($tmp_list);
    }

    /**
     * 
     * 用于问题相关修改： 问题补充 追加悬赏
     */
    private function edit() {
        $param = $this->_getParam('info');
        $tmp_member_cookie = $this->MemberObj->getCookie();
        $tmp_ask_info = $this->AskObj->get_one($param['id']);
        if ($param['point']) {
            $param['point'] = $param['point'] + $tmp_ask_info['point'];
            $tmp_user_credit = $tmp_member_cookie['credit'] - $param['point'];
            ($tmp_user_credit > 0) or ( $tmp_user_credit = 0);
            $this->MemberObj->Edit(array('credit' => $tmp_user_credit), $tmp_ask_info['userid']);
            $this->MemberObj->ssetcookie('member_credit', $tmp_user_credit);
        }
        #$tmp_ask_info['userid'] = $tmp_member_cookie['uid'] = 7; 		#测试专用

        if ($tmp_ask_info['userid'] == $tmp_member_cookie['uid']) {
            $param['addtime'] = time();
            $this->AskObj->edit($param);
            Zend_Adver_Js::helpJsRedirect("/id/" . $param['id'], 1, '提交成功！!');
        } else {
            Zend_Adver_Js::helpJsRedirect("/id/" . $param['id'], 1, '对不起！权限不够！');
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
     * @author kxgsy163@163.com
     * 对象实例化:用于会员详细信息
     */
    private function newObj($type = 1) {
        $type = ($type == 1) ? 1 : 2;
        $this->memberDetailObj = new $this->{'member_detail_' . $type} ( );        #实例化会员详细信息类
    }

    /*
     * 话题底层计数
     */

    public function addvnAction() {
        $sAskid = $this->getRequest()->getParam('askid');
        $this->AskObj->Add_viewnum($sAskid);
    }

    public function getnewaskAction() {
        $where = " order by ctime desc limit 0, 10";
        $result = $this->AskObj->newask($where);
        foreach ($result as $k => $v) {
            $isShow .='<li><b></b><a href="http://ask.9939.com/id/' . $v[id] . '" target="_blank">' . $v[title] . '</a></li>';
        }
        echo "document.write('$isShow')";
    }

    private function getAds($id = 0) {
        if (!$id)
            return array();
        $dir = '/home/web/htsns-9939-com/data/';
        $filename = 'data_adsplace_' . $id . '.php';
        if (file_exists($dir . $filename)) {
            //echo $dir.$filename."<br>";
            @require($dir . $filename);
            return $_ADSGLOBAL[$id];
        }
        return array();
    }


    /**
     * 
     * 获取问题栏目
     */
    private function getClassByAsks($classid) {
        $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($classid), 1);
        $keshicid = 0;
        $array = array();
        while (true) {
            if (empty($classid))
                break;
            if ($CATEGORY[$classid]) {
                if ($keshicid !== 0) {
                    $arrs[] = $classid;
                    $array[] = '<a href="/classid/' . $classid . '" target="_blank" title="' . $CATEGORY[$classid]['name'] . '">' . $CATEGORY[$classid]['name'] . '</a>';
                } else {
                    $is_disease = isset($CATEGORY[$classid]['is_disease']) ? $CATEGORY[$classid]['is_disease'] : 1;
                    $jb_url = $is_disease == 1 ? "/disease/" . $CATEGORY[$classid]['id'] : "/classid/$classid";
                    $array[] = '<a class="a_bol" href="' . $jb_url . '" title="' . $CATEGORY[$classid]['name'] . '" target="_blank">' . $CATEGORY[$classid]['name'] . '</a> ';
                }
                $classid = $CATEGORY[$classid]['pID'];
                $keshicid = 1;
            } else {
                break;
            }
        }
        if ($array) {
            $array = implode(' > ', array_reverse($array));
        }
        return $array;
    }

    private function error() {
        header('HTTP/1.0 404 Not Foun');
        echo $this->ViewObj->render("404.phtml");
        exit;
    }
    
    private function replaceWordToChar($subject){
        if(empty($subject)){
            return $subject;
        }
        $rep_arr = array(
            '同志',
            '服务',
            '帅哥',
            '全套',
            '鸭子',
            '招聘',
            '少爷',
            '小弟',
            '按摩',
            '肌肉',
            '巨牌',
            '会所',
            '男同',
            '男同志',
            '男模',
            '模特',
            '男子会所'
        );
        $subject = str_replace($rep_arr, '**', $subject);
        return $subject;
    }
}

?>
