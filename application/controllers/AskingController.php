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
Zend_Loader::loadClass('Answer', MODELS_PATH);
Zend_Loader::loadClass('Keshi', MODELS_PATH);
Zend_Loader::loadClass('Member', MODELS_PATH);
Zend_Loader::loadClass('MemberDetail', MODELS_PATH);
Zend_Loader::loadClass('MemberDetailDoctor', MODELS_PATH);
//Zend_Loader::loadClass('Credit',MODELS_PATH);	#加载积分类
Zend_Loader::loadClass('Askadd', MODELS_PATH);
Zend_Loader::loadClass('Create', MODELS_PATH);

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

class AskingController extends Zend_Controller_Action {

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
    private $CreateObj = '';
    private $keshi_obj;

    public function init() {
        $this->ViewObj = Zend_Registry::get('view');
        $this->AskObj = new Ask();
        $this->AnswerObj = new Answer();
        $this->MemberObj = new Member();
        $this->memberDetailObj = new MemberDetail();
        $this->MemberDetailDoctor = new MemberDetailDoctor();
        $this->AnswerObj = new Answer();
        $this->CreateObj = new Create();
        //加载搜索类
        Zend_Loader::loadClass('GetSearchData', MODELS_PATH);
        $this->GetSearchData_obj = new GetSearchData();

//		$this->Credit_obj = new Credit();		#积分类
        $this->keshi_obj = new Keshi();

        parent::init();
    }

    public function indexAction() {
        try {
            $arrgdcs = array('hsid' => "int", 'ksid' => "int", 'doc_id' => "int", 'jyid' => "int", 'uid' => "int");
            $arrParam = $this->getRequest()->getParams();
            foreach ($arrParam as $k => $v) {
                if ($arrgdcs[$k] == "int") {
                    if (!preg_match("/^[0-9]*$/", $v)) {
                        $this->error();
                        exit;
                    }
                } else if ($k == "kw") {
                    if (preg_match("/<|>|\"/", $v)) {
                        $this->error();
                        exit;
                    }
                } else if ($k == "backurl") {
                    if (preg_match("/<|>|\"/", $v)) {
                        $this->error();
                        exit;
                    }
                }
            }

//            phpinfo();exit;
            //右侧名医推荐 lc@2016-6-13
            $famousDoctors = $this->CreateObj->getAds(271);
//            var_dump($famousDoctors);exit;
            $this->ViewObj->famousDoctors = $famousDoctors;


            //通过cookie传递上一个页面传过来的值 cookie过期时间2分钟
            $qas_info_cookie_key = md5("ask_9939_qas_info");
            $qas_info = base64_decode($_COOKIE[$qas_info_cookie_key]);
            $info = unserialize($qas_info);
            $isdata = is_array($info) ? "1" : "0";
            $this->ViewObj->isdata = $isdata;
            $this->ViewObj->datas = $info;
            //错误提示信息
            $ret_err_coo = "error_return";
            $ret_err = base64_decode($_COOKIE[$ret_err_coo]);
            $ret_error = unserialize($ret_err);
            $this->ViewObj->err = $ret_error;



            //医生信息
            $uid = $this->_getParam("uid");
            $uid = $uid != '' ? $uid : $info['answerUid'];

            if ($uid) {
                $doc_info1 = $this->MemberObj->get_one_by_id($uid);
                $doc_info2 = $this->MemberDetailDoctor->getList('uid=' . $uid);
                $doc['uid'] = $doc_info1['uid'];
                $doc['nickname'] = $doc_info1['nickname'];
                $doc['pic'] = $doc_info1['pic'];
                $doc['best'] = $doc_info2[0]['best_dis'];
            } else {
                $doc['uid'] = '';
            }

            $this->ViewObj->doc = $doc;

            //选择科室信息
//                        $KESHIGROUP = $this->keshi_obj->cache_keshi_group();
//		  	$keshi=$this->keshi_obj->get_keshi();
//		  	$diseases=$CATEGORY;
//		  	$this->ViewObj->keshi=$keshi;
//		  	$this->ViewObj->diseases=$diseases;
            //取得医院的id及科室的id
            $hsid = $this->_getParam("hsid");
            $ksid = $this->_getParam("ksid");
            $doc_id = $this->_getParam("doc_id");
            $jyid = $this->_getParam("jyid");
            $sessionask = substr(md5('ask'), 5, 5) . time() . substr(md5('9939.com'), 10, 5);
            setcookie('ask_wktime', $sessionask, time() + 600, '/', APP_DOMAIN);
            $tmp_kw = trim($this->_getParam("kw"));
            if ($tmp_kw == '输入提问内容，点击“快速提问”' || $tmp_kw == '输入提问内容，点击“我要提问”')
                $tmp_kw = '';
            $tmp_kw = $tmp_kw ? $tmp_kw : "请输入您的提问标题";
            $tmp_kw = str_replace(" ", "%20", $tmp_kw);
            $tmp_askcontent = $this->_getParam("askcontent");
            $tmp_content = trim($this->_getParam("content"));
            //获取搜索内容
            //$tmp_xml = @file_get_contents("http://211.167.92.198:8180/complex/select/?q=title:$tmp_kw*%20AND%20type:7&wt=json&start=0&rows=10&indent=on");
            //print_r($tmp_xml);exit;
            if ($tmp_xml) {
                $search = json_decode($tmp_xml, true);
                $search = json_to_array($search);
                //print_r($search);exit;
                if ($search) {
                    $tmp_list = "";
                    for ($i = 0; $i < 10; $i++) {
                        @$id = $search['response']['docs'][$i]['id'];
                        @$art_title = $search['response']['docs'][$i]['title'][0];
                        @$art_content = $search['response']['docs'][$i]['content'][0];
                        $tmp_list .= "<li><a href='/id/$id' target='_blank'>$art_title</a>$art_content</li>";
                    }
                }
            }

            $aToken = new Zend_Session_Namespace('token');
            $aToken->unlock();
            $sToken = md5(time() . $tmp_kw);
            $aToken->token = $sToken;
            $aToken->lock();


            //后退的url 林原 2010-09-15
            $backUrl = $this->_getParam("backurl");
            if ($backUrl)
                $this->ViewObj->backurl = urlencode($backUrl);

            $this->ViewObj->answerUid = $this->_getParam("uid") > 0 ? $this->_getParam("uid") : 0;
            $this->ViewObj->token = $sToken;
            $this->ViewObj->list = $tmp_list;
            $this->ViewObj->num = $tmp_num;
            $this->ViewObj->kw = str_replace("%20", " ", $tmp_kw);
            $this->ViewObj->askcontent = $tmp_askcontent;
            $this->ViewObj->content = $tmp_content;
            $this->ViewObj->user_info = $this->MemberObj->getCookie();

            //设置医院及科室的id
            $this->ViewObj->hsid = $hsid;
            $this->ViewObj->ksid = $ksid;
            $this->ViewObj->doc_id = $doc_id;
            $this->ViewObj->jyid = $jyid;

            echo $this->ViewObj->render('/question_disease.phtml');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function diseasesAction() {
        $id = $this->getRequest()->getParam('id');
        $cache_keshi = $this->keshi_obj->cache_keshi();
        $keshi = $this->keshi_obj->get_keshi();

        $dis = array();
        $kk = 0;
        foreach ($keshi[$id]['child'] as $k => $v) {
            $n = 0;
            $kk = $v['id'];
            $dis[".$kk."] = array();
            foreach ($cache_keshi as $key => $val) {
                if ($val['pID'] == $v['id']) {
                    $dis[".$kk."][$n]['id'] = $val['id'];
                    $dis[".$kk."][$n]['name'] = $val['name'];
                    $n++;
                }
            }
            if (empty($dis[".$kk."])) {
                $dis[".$kk."][0]['id'] = '';
                $dis[".$kk."][0]['name'] = '';
            }
//		  		$kk++;
        }
        echo json_encode($dis);
    }

    private function error() {
        header('HTTP/1.0 404 Not Foun');
        echo $this->ViewObj->render("404.phtml");
        exit;
    }

}

?>
