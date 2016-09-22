<?php

/**
 * ##############################################
 * @FILE_NAME :RegisterController.php
 * ##############################################
 *
 * @author : ljf
 * @MailAddr :licaption@163.com
 * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
 * @PHP Version :  Ver 5.21
 * @Apache  Version : Ver 2.20
 * @MYSQL Version : Ver 5.0
 * @Version : Ver Tue Sep 15 13:53 CST 2009
 * @DATE : Tue Sep 15 13:53 CST 2009
 *
 * ==============================================
 * @Desc :  注册控制器
 * ==============================================
 */
class RegisterController extends Zend_Controller_Action {

    public function init() {
        $this->view = Zend_Registry::get("view");
        Zend_Loader::loadClass('Register', MODELS_PATH);
        $this->Register = new Register();
        Zend_Loader::loadClass('Doctor', MODELS_PATH);
        $this->Doctor_obj = new Doctor();
        Zend_Loader::loadClass('Hospital', MODELS_PATH);
        $this->Hospital_obj = new Hospital();
        //$this->db_jb=$GLOBALS['$db_tj_obj'];
        Zend_Loader::loadClass("Member", MODELS_PATH);
        $this->member_obj = new Member();
        Zend_Loader::loadClass("MemberDetailDoctor", MODELS_PATH);
        $this->details_obj = new MemberDetailDoctor();
    }

    public function indexAction() {
        //过时方法，跳转到http://www.9939.com/register/
        header("location: " . WEB_URL . "register");
        exit;
        $backUrl = $this->_getParam('backurl'); //获取跳转到的url 林原 2010-08-24
        $this->view->backurl = $backUrl; //跳转url 林原 2010-08-24
        echo $this->view->render("register.phtml");
    }

    //验证数据合法性
    private function check($data) {
        if ($data['username'] == "")
            return "请填写用户名";
        if (!preg_match("/^[\w]+$/", $data['username']))
            return "用户名只能是下划线，数字，字母";

        if (strlen($data['username']) < 4 || strlen($data['username']) > 16)
            return "用户名长度必须是4到16个字符";
        if ($data['email'] != "") {
            if (!preg_match("/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/", $data['email']))
                return "E－mail格式不正确";
        }
        if ($data['nickname'] == "")
            return "请填写昵称";
        if (!preg_match("/^[\w|\x80-\xff]+$/", $data['nickname']))
            return "昵称只能是下划线，数字，字母，中文";
        if ($data['password'] == "")
            return "请填写密码";
        if ($data['password'] != $data['password1'])
            return "俩次输入密码不一致";
        if (strlen($data['password']) < 6)
            return "密码长度不小于6个字符";


        return true;
    }

    //注册ajax调用
    public function registerajaxAction() {
        if ($this->validatePost()) {
            $info = $this->_info;     //用户信息
            $flg = $info['uType'];
            if (!$flg) {//如果是医生注册第二步传第一步的类型属性
                $flg = $this->getRequest()->getParam('flg');
            }
            $src = $this->getRequest()->getParam('src');
            if ($flg == 1) {//大众会员
                if (($error = $this->check($info)) === true) {
                    $member = $this->member_obj->get_one("email='{$info['username']}' or username='{$info['username']}'");

                    if ($member) {
                        if ($src == 'app') {
                            echo json_encode(array("error" => 1, "msg" => "用户名存在请更改"));
                        } else {
                            echo json_encode(array("error" => "用户名存在请更改"));
                        }
                        exit;
                    }
                    if ($info['email'] != "") {
                        $member = $this->member_obj->get_one("email='{$info['email']}' or username='{$info['email']}'");
                        if ($member) {
                            if ($src == 'app') {
                                echo json_encode(array("error" => 1, "msg" => "E-mail存在请更改"));
                            } else {
                                echo json_encode(array("error" => "E-mail存在请更改"));
                                exit;
                            }
                        }
                    }
                } else {
                    if ($src == 'app') {
                        echo json_encode(array("error" => 1, "msg" => $error));
                    } else {
                        echo json_encode(array("error" => $error));
                    }
                    exit;
                }

                $password = $info['password'];
                $addInfo = $info;
                $addInfo['password'] = md5($password);
                $addDetial['dis'] = $this->_info['dis'];
                $addDetial['uType'] = $flg;
                unset($addInfo['dis']);
                unset($addInfo['password1']);

                $uid = $this->member_obj->add_one($addInfo);     //插入基本信息
                ////////连接商成
//                if(class_exists("SoapClient",false)){
//                    $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php','uri'=>'go9939','encoding'=>'utf8'));
//                    $wlhtml=$client->__call('addUser', array(APP_SOAP_GO9939_KEY,$info['username'] ,$info['password']));
//                }
                ///////连接ucenter
                if (@include_once(APP_ROOT . "/uc_client/uc_config.php")) {
                    include_once(APP_ROOT . "/uc_client/client.php");
                    uc_user_register($uid, $info['username'], $info['password'], $info['username']);
                    $wlhtml.=uc_user_synlogin($uid);
                }
                $addDetial['uid'] = $uid;
                if ($addDetial['dis'] <> '') {
                    $addDetial['dis'] = $addDetial['dis'];
                } else {
                    unset($addDetial['dis']);
                }
                $this->details_obj->add_one($addDetial); //插入详细信息
                $this->view->info = $info;
                if ($src == 'app') {
                    echo json_encode(array("error" => 0, "msg" => $uid));
                } else {
                    echo json_encode(array("error" => "1", "html" => $wlhtml . $this->view->render('register_succeedajax.phtml')));
                }
                exit;
            } else {//医生会员
                $info['truename'] = $this->getRequest()->getParam('trueName');
                $info['doc_hos'] = $this->getRequest()->getParam('hos');
                $info['doc_keshi'] = $this->getRequest()->getParam('keshi');
                if ($info['truename']) {//如果有数据提交
                    $firstInfo = $this->getRequest()->getParam('firstInfo');
                    $firstInfo = str_replace('\"', '"', $firstInfo);
                    $firstInfo = unserialize(trim($firstInfo));
                    if (($error = $this->check($firstInfo)) === true) {
                        $member = $this->member_obj->get_one("email='{$firstInfo['username']}' or username='{$firstInfo['username']}'");
                        if ($member) {
                            if ($src == 'app') {
                                echo json_encode(array("error" => 1, "msg" => "用户名存在请更改"));
                            } else {
                                echo json_encode(array("error" => "用户名存在请更改"));
                            }
                            exit;
                        }
                        if ($firstInfo['email'] != "") {
                            $member = $this->member_obj->get_one("email='{$firstInfo['email']}' or username='{$firstInfo['email']}'");
                            if ($member) {
                                if ($src == 'app') {
                                    echo json_encode(array("error" => 1, "msg" => "E-mail存在请更改"));
                                } else {
                                    echo json_encode(array("error" => "E-mail存在请更改"));
                                }
                                exit;
                            }
                        }
                    } else {
                        if ($src == 'app') {
                            echo json_encode(array("error" => 1, "msg" => $error));
                        } else {
                            echo json_encode(array("error" => $error));
                        }
                        exit;
                    }
                    $info['username'] = $firstInfo['username'];
                    $info['email'] = $firstInfo['email'];
                    $info['nickname'] = $firstInfo['nickname'];
                    $info['uType'] = $firstInfo['uType'];
                    $info['password'] = md5($firstInfo['password']);
                    $info['dateline'] = $firstInfo['dateline'];
                    $info['dis'] = $firstInfo['dis'];
                    $addInfo = $info;
                    $addDetial['truename'] = $info['truename'];
                    $addDetial['doc_hos'] = $info['doc_hos'];
                    $addDetial['doc_keshi'] = $info['doc_keshi'];
                    $addDetial['dis'] = $info['dis'];
                    $addDetial['uType'] = $info['uType'];
                    unset($addInfo['truename']);
                    unset($addInfo['doc_hos']);
                    unset($addInfo['doc_keshi']);
                    unset($addInfo['dis']);
                    unset($addInfo['password1']);

                    $uid = $this->member_obj->add_one($addInfo); //插入用户基本信息
                    ////////连接商成
//                    if(class_exists("SoapClient",false)){
//                        $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php','uri'=>'go9939','encoding'=>'utf8'));
//                        $wlhtml=($client->__call('addUser', array(APP_SOAP_GO9939_KEY,$firstInfo['username'] ,$firstInfo['password'])));
//                    }
                    ///////连接ucenter
                    if (@include_once(APP_ROOT . "/uc_client/uc_config.php")) {
                        include_once(APP_ROOT . "/uc_client/client.php");
                        uc_user_register($uid, $firstInfo['username'], $firstInfo['password'], $firstInfo['username']);
                        $wlhtml.=uc_user_synlogin($uid);
                    }
                    $addDetial['uid'] = $uid;
                    if (!empty($addDetial['dis'])) {
                        $addDetial['dis'] = explode(",", $addDetial['dis']);
                        foreach ($addDetial['dis'] as $v) {
                            $dis .= "$v,";
                        }
                    }


                    $addDetial['dis'] = rtrim($addDetial['dis'], ",");
                    $this->details_obj->add_one($addDetial); //插入详细信息

                    $hospital = $this->Hospital_obj->get_one_get("name_hospital='" . trim($info['doc_hos']) . "'");

                    $hospital_id = $hospital['hospital_id'];
                    if ($hospital_id) {
                        $where = "where jiayuanID=0 and status='99' and name_doctor='" . $info['truename'] . "' and hospital='" . $hospital_id . "'";
                    } else {
                        $where = "where jiayuanID=0 and status='99' and name_doctor='" . $info['truename'] . "'";
                    }
                    $exist = $this->Doctor_obj->get_doctor($where); //获取医生的信息
                    //提取科室
                    if ($info['doc_keshi']) {
                        $sectionArr = $this->Doctor_obj->getSectionId($info['doc_keshi']);
                        if (!empty($sectionArr))
                            $section_id = $sectionArr[0]['id'];
                    }
                    //验证该科室是否适于该医院
                    $hos_section = null;
                    if (!empty($section_id) && !empty($hospital_id)) {
                        $hos_section = $this->Doctor_obj->getSectionHos($section_id, $hospital_id);
                    }
                    if (!empty($hos_section)) {
                        $this->view->keshi = $info['doc_keshi'];
                        $this->view->doctor = $exist;
                        $this->view->hospital = $hospital;
                        if (!empty($exist)) {
                            foreach ($exist AS $k => $v) {
                                $reninfo[$k]['doctor_id'] = $v['doctor_id'];
                                $reninfo[$k]['hospital'] = $hospital_id;
                                if ($uid) {
                                    $reninfo[$k]['jiayuanID'] = $uid;
                                }
                                $reninfo[$k]['name_doctor'] = $info['truename'];
                                $reninfo[$k]['username'] = $info['username'];
                            }
                        }
                    }

                    if (empty($exist)) {
                        if ($uid) {
                            $reninfo['jiayuanID'] = $uid;
                        }
                        $reninfo['hospital'] = $hospital_id;
                        $reninfo['name_doctor'] = $info['truename'];
                        $reninfo['username'] = $info['username'];
                    }
                    $this->view->hosSection = $hos_section;
                    $this->view->secondInfo = $reninfo;
                    $this->view->tzurl = "register/docregisterajax/";
                    if ($src == 'app') {
                        echo json_encode(array("error" => 0, "msg" => $uid));
                    } else {
                        echo json_encode(array("error" => "1", "html" => $wlhtml . $this->view->render('register_renlingajax.phtml')));
                    }
                    exit;
                } else {
                    if (($error = $this->check($info)) === true) {
                        $member = $this->member_obj->get_one("email='{$info['username']}' or username='{$info['username']}'");
                        if ($member) {
                            if ($src == 'app') {
                                echo json_encode(array("error" => 1, "msg" => "用户名存在请更改"));
                            } else {
                                echo json_encode(array("error" => "用户名存在请更改"));
                            }
                            exit;
                        }
                        if ($info['email'] != "") {
                            $member = $this->member_obj->get_one("email='{$info['email']}' or username='{$info['email']}'");
                            if ($member) {
                                if ($src == 'app') {
                                    echo json_encode(array("error" => 1, "msg" => "E-mail存在请更改"));
                                } else {
                                    echo json_encode(array("error" => "E-mail存在请更改"));
                                }
                                exit;
                            }
                        }
                    } else {
                        if ($src == 'app') {
                            echo json_encode(array("error" => 1, "msg" => $error));
                        } else {
                            echo json_encode(array("error" => $error));
                        }
                        exit;
                    }
                    $this->view->firstInfo = $info;
                    $this->view->tzurl = "register/registerajax/flg/2";
                    if ($src == 'app') {
                        echo json_encode(array("error" => 0, "msg" => $uid));
                    } else {
                        echo json_encode(array("error" => "1", "html" => $wlhtml . $this->view->render('register_2ajax.phtml')));
                    }
                    exit;
                }
            }
        } else {
            if ($src == 'app') {
                echo json_encode(array("error" => 1, "msg" => $this->_message));
            } else {
                echo json_encode(array("error" => $this->_message));
            }
            exit;
        }
    }

    // Register
    public function registerAction() {
        //过时方法，跳转到http://www.9939.com/register/
        header("location: " . WEB_URL . "register");
        exit;
        if ($this->validatePost()) {
            $info = $this->_info;     //用户信息
            $flg = $info['uType'];
            if (!$flg) {//如果是医生注册第二步传第一步的类型属性
                $flg = $this->getRequest()->getParam('flg');
            }

            if ($info['username'] !== NULL) {
                $email = $info['username'];
                $check_email = $this->member_obj->getEmail($email);
                if ($check_email) {
                    Zend_Adver_Js::helpJsRedirect('/register/?uType=' . $flg, 0, 'Email已经存在！请重新输入Email！');
                }
            }
            if ($flg == 1) {//大众会员
                $password = $info['password'];
                $addInfo = $info;
                $addInfo['password'] = md5($password);
                //if($this->_info_detail['dis']){echo 'zhang';}else{echo 'hai';}//exit;
                //print_r($this->_info['dis']);
                $addDetial['dis'] = $this->_info['dis'];
                $addDetial['uType'] = $flg;
                unset($addInfo['dis']);
                $uid = $this->member_obj->add_one($addInfo);     //插入基本信息
                ////////连接商成
//                if(class_exists("SoapClient",false)){
//                    $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php','uri'=>'go9939','encoding'=>'utf8'));
//                    echo $client->__call('addUser', array(APP_SOAP_GO9939_KEY,$info['username'] ,$info['password']));
//                }
                ///////连接ucenter
                if (@include_once(APP_ROOT . "/uc_client/uc_config.php")) {
                    include_once(APP_ROOT . "/uc_client/client.php");
                    uc_user_register($uid, $info['username'], $info['password'], $info['username']);
                    echo uc_user_synlogin($uid);
                }
                $addDetial['uid'] = $uid;
                if ($addDetial['dis'] <> '') {
                    $addDetial['dis'] = $addDetial['dis'];
                } else {
                    unset($addDetial['dis']);
                }
                //print_r($addDetial).'<br>';
                $this->details_obj->add_one($addDetial); //插入详细信息
                $this->view->info = $info;
                echo $this->view->render('register_succeed.phtml');
            } else {//医生会员
                $info['truename'] = $this->getRequest()->getParam('trueName');
                $info['doc_hos'] = $this->getRequest()->getParam('hos');
                $info['doc_keshi'] = $this->getRequest()->getParam('keshi');
                if ($info['truename']) {//如果有数据提交
                    $firstInfo = $this->getRequest()->getParam('firstInfo');
                    $firstInfo = str_replace('\"', '"', $firstInfo);
                    $firstInfo = unserialize(trim($firstInfo));
                    $email = $firstInfo['username'];
                    $check_email = $this->member_obj->getEmail($email);
                    if ($check_email) {
                        Zend_Adver_Js::helpJsRedirect('/register/?uType=' . $flg, 0, 'Email已经存在！请重新输入Email！');
                    }
                    $info['username'] = $firstInfo['username'];
                    $info['nickname'] = $firstInfo['nickname'];
                    $info['uType'] = $firstInfo['uType'];
                    $info['password'] = md5($firstInfo['password']);
                    $info['dateline'] = $firstInfo['dateline'];
                    $info['dis'] = $firstInfo['dis'];
                    $addInfo = $info;
                    $addDetial['truename'] = $info['truename'];
                    $addDetial['doc_hos'] = $info['doc_hos'];
                    $addDetial['doc_keshi'] = $info['doc_keshi'];
                    $addDetial['dis'] = $info['dis'];
                    $addDetial['uType'] = $info['uType'];
                    unset($addInfo['truename']);
                    unset($addInfo['doc_hos']);
                    unset($addInfo['doc_keshi']);
                    unset($addInfo['dis']);

                    $uid = $this->member_obj->add_one($addInfo); //插入用户基本信息
                    ////////连接商成
                    if (class_exists("SoapClient", false)) {
                        $client = new SoapClient(null, array('location' => 'http://www.go9939.com/wbs2/9939_server.php', 'uri' => 'go9939', 'encoding' => 'utf8'));
                        echo ($client->__call('addUser', array(APP_SOAP_GO9939_KEY, $firstInfo['username'], $firstInfo['password'])));
                    }
                    ///////连接ucenter
                    if (@include_once(APP_ROOT . "/uc_client/uc_config.php")) {
                        include_once(APP_ROOT . "/uc_client/client.php");
                        uc_user_register($uid, $firstInfo['username'], $firstInfo['password'], $firstInfo['username']);
                        echo uc_user_synlogin($uid);
                    }
                    $addDetial['uid'] = $uid;
                    if (!empty($addDetial['dis'])) {
                        $addDetial['dis'] = explode(",", $addDetial['dis']);
                        foreach ($addDetial['dis'] as $v) {
                            $dis .= "$v,";
                        }
                    }


                    $addDetial['dis'] = rtrim($addDetial['dis'], ",");
                    $this->details_obj->add_one($addDetial); //插入详细信息
                    $hospital = $this->Hospital_obj->get_one_get("name_hospital='" . trim($info['doc_hos']) . "'");
                    $hospital_id = $hospital['hospital_id'];
                    if ($hospital_id) {
                        $where = "where jiayuanID=0 and status='99' and name_doctor='" . $info['truename'] . "' and hospital='" . $hospital_id . "'";
                    } else {
                        $where = "where jiayuanID=0 and status='99' and name_doctor='" . $info['truename'] . "'";
                    }
                    $exist = $this->Doctor_obj->get_doctor($where); //获取医生的信息
                    //提取科室
                    if ($info['doc_keshi']) {
                        $sectionArr = $this->Doctor_obj->getSectionId($info['doc_keshi']);
                        if (!empty($sectionArr))
                            $section_id = $sectionArr[0]['id'];
                    }
                    //验证该科室是否适于该医院
                    $hos_section = null;
                    if (!empty($section_id) && !empty($hospital_id)) {
                        $hos_section = $this->Doctor_obj->getSectionHos($section_id, $hospital_id);
                    }
                    if (!empty($hos_section)) {
                        $this->view->keshi = $info['doc_keshi'];
                        $this->view->doctor = $exist;
                        $this->view->hospital = $hospital;
                        if (!empty($exist)) {
                            foreach ($exist AS $k => $v) {
                                $reninfo[$k]['doctor_id'] = $v['doctor_id'];
                                $reninfo[$k]['hospital'] = $hospital_id;
                                if ($uid) {
                                    $reninfo[$k]['jiayuanID'] = $uid;
                                }
                                $reninfo[$k]['name_doctor'] = $info['truename'];
                                $reninfo[$k]['username'] = $info['username'];
                            }
                        }
                    }
                    if (empty($exist)) {
                        if ($uid) {
                            $reninfo['jiayuanID'] = $uid;
                        }
                        $reninfo['hospital'] = $hospital_id;
                        $reninfo['name_doctor'] = $info['truename'];
                        $reninfo['username'] = $info['username'];
                    }
                    $this->view->hosSection = $hos_section;
                    $this->view->secondInfo = $reninfo;
                    $this->view->tzurl = "/register/docregister/";
                    echo $this->view->render('register_renling.phtml');
                    exit;
                } else {
                    $this->view->firstInfo = $info;
                    $this->view->tzurl = "/register/register/flg/2";
                    echo $this->view->render('register_2.phtml');
                }
            }
        } else {
            Zend_Adver_Js::helpJsRedirect('/register', 0, $this->_message);
        }
    }

    public function docregisterajaxAction() {
        $info = $this->getRequest()->getParam('secondInfo');
        $info = empty($info) ? $this->getRequest()->getParam('qrInfo') : $info;
        $info = str_replace('\"', '"', $info);
        $info = unserialize(trim($info));
        $qh = $this->getRequest()->getParam('qh');
        $dwdh = $this->getRequest()->getParam('dwdh');
        $info['inputtime'] = time();
        unset($info['username']);
        unset($info['hospital']);
        $info['rlphone'] = $qh . "-" . $dwdh . "," . $this->getRequest()->getParam('phone');
        if ($info['doctor_id']) {
            $info['updatetime'] = time();
            $this->Doctor_obj->update_one($info, $info['doctor_id']); //更新匹配到的医生记录，存入家园id和认领联系方式
        } else {
            $info['status'] = 3;
            $doctor_id = $this->Doctor_obj->add_one($info); //更新匹配到的医生记录，存入家园id和认领联系方式
        }
        $this->view->info = $info;
        echo json_encode(array("error" => "1", "html" => $wlhtml . $this->view->render('register_succeedajax.phtml')));
        exit;
    }

    public function docregisterAction() {
        //过时方法，跳转到http://www.9939.com/register/
        header("location: " . WEB_URL . "register");
        exit;
        $info = $this->getRequest()->getParam('secondInfo');
        $info = empty($info) ? $this->getRequest()->getParam('qrInfo') : $info;
        $info = str_replace('\"', '"', $info);
        $info = unserialize(trim($info));
        $qh = $this->getRequest()->getParam('qh');
        $dwdh = $this->getRequest()->getParam('dwdh');
        $info['inputtime'] = time();
        unset($info['username']);
        unset($info['hospital']);
        $info['rlphone'] = $qh . "-" . $dwdh . "," . $this->getRequest()->getParam('phone');
        if ($info['doctor_id']) {
            $info['updatetime'] = time();
            $this->Doctor_obj->update_one($info, $info['doctor_id']); //更新匹配到的医生记录，存入家园id和认领联系方式
        } else {
            $info['status'] = 3;
            $doctor_id = $this->Doctor_obj->add_one($info); //更新匹配到的医生记录，存入家园id和认领联系方式
        }
        $this->view->info = $info;
        echo $this->view->render('register_succeed.phtml');
        exit;
    }

    //认领
    public function renlingAction() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->view->tzurl = "/register/renling/";
            echo $this->view->render('register_2.phtml');
            exit;
        }

        //取得真实姓名
        $info['truename'] = $this->getRequest()->getParam('trueName');
        $info['doc_hos'] = $this->getRequest()->getParam('hos');
        $info['doc_keshi'] = $this->getRequest()->getParam('keshi');

        //取得联系方式
        $qh = $this->getRequest()->getParam('qh');
        $dwdh = $this->getRequest()->getParam('dwdh');
        // 验证客户端输入，暂时略

        if (!empty($info) && !$qh && !$dwdh) { //认领填写联系方式的页面
            $hospital = $this->Hospital_obj->get_one_get("name_hospital='" . trim($info['doc_hos']) . "'");
            $hospital_id = $hospital[0]['hospital_id'];
            if ($hospital_id) {
                $where = "where jiayuanID=0 and status='99' and name_doctor='" . $info['truename'] . "' and hospital='" . $hospital_id . "'";
            } else {
                $where = "where jiayuanID=0 and status='99' and name_doctor='" . $info['truename'] . "'";
            }
            $exist = $this->Doctor_obj->get_doctor($where);

            //提取科室
            if ($info['doc_keshi']) {
                $sectionArr = $this->Doctor_obj->getSectionId($info['doc_keshi']);
                $section_id = $sectionArr[0]['id'];
            }

            //验证该科室是否适于该医院
            if (!empty($section_id) && !empty($hospital_id)) {
                $hos_section = $this->Doctor_obj->getSectionHos($section_id, $hospital_id);
            }

            if (empty($exist)) {
                Zend_Adver_Js::GoToTop('', "该医生已被认领！！");
            } elseif (!empty($hos_section) && !empty($exist)) {//认领会员
                $info['doctor_id'] = $exist[0]['doctor_id'];
                $this->view->hospital = $hospital; //所在医院
                $this->view->doctor = $exist; //某个医生
                $this->view->keshi = $info['doc_keshi']; //医生所在的科室
                $this->view->hosSection = $hos_section;
                $this->view->secondInfo = $info;
                $this->view->tzurl = "/register/renling/";
                echo $this->view->render('register_renling.phtml');
            } else {
                Zend_Adver_Js::GoToTop('', "你填写的信息必须是真实存在的！！");
            }
        }
        if ($qh && $dwdh) { //认领成功页面
            $secondInfo = $this->getRequest()->getParam('secondInfo');
            $secondInfo = str_replace('\"', '"', $secondInfo);
            $secondInfo = unserialize(trim($secondInfo));
            $info = $secondInfo;
            $addInfo = $info;
            $addDetial['truename'] = $info['truename'];
            $addDetial['doc_hos'] = $info['doc_hos'];
            $addDetial['doc_keshi'] = $info['doc_keshi'];
            $addDetial['dis'] = $info['dis'];
            $doctor_id = $info['doctor_id']; //匹配到的医生id

            unset($addInfo['truename']);
            unset($addInfo['doc_hos']);
            unset($addInfo['doc_keshi']);
            unset($addInfo['dis']);
            unset($addInfo['doctor_id']);


            $uid = $_COOKIE['member_uID'];
            $addDetial['uid'] = $uid;
            $addDetial['uType'] = $_COOKIE['member_uType'];
            if ($addDetial['dis'] <> '') {
                $addDetial['dis'] = $addDetial['dis'];
            } else {
                unset($addDetial['dis']);
            }
            $detial_arr = $this->details_obj->get_one($uid, $addDetial['uType']); //查看该用户是否填写了详细信息
            if (!empty($detial_arr)) {
                $this->details_obj->update_one($addDetial, $uid, $addDetial['uType']); //修改详细信息
            } else {
                $this->details_obj->add_one($addDetial); //插入详细信息
            }
            $addDoctor['doctor_id'] = $doctor_id;
            $addDoctor['jiayuanID'] = $uid;
            $addDoctor['rlphone'] = $qh . "-" . $dwdh . "," . $this->getRequest()->getParam('phone');
            $this->Doctor_obj->update_one($addDoctor, $doctor_id); //更新匹配到的医生记录，存入家园id和认领联系方式

            $this->view->info = $info;

            echo $this->view->render('register_succeed.phtml');
        }
    }

    //加载关注点栏目
    public function loadAction() {
        $utype = (int) $this->getRequest()->getParam('id');
        $r = $this->Register->get_gzd_cats($utype);
        exit($r);
    }

    //加载疾病具体关注点
    public function loadgzdAction() {
        $cid = (int) $this->getRequest()->getParam('id');
        $r = $this->Register->get_gzd($cid);
        exit($r);
    }

    public function validatePost() {
        $this->_info['username'] = $this->getRequest()->getParam('username');
        $this->_info['email'] = $this->getRequest()->getParam('email');
        $this->_info['uType'] = $this->getRequest()->getParam('uType');
        $this->_info['nickname'] = $this->getRequest()->getParam('nickname');
        $this->_info['password'] = $this->getRequest()->getParam('password');
        $this->_info['password1'] = $this->getRequest()->getParam('password1');
        $this->_info['dateline'] = time();
        $this->_info['dis'] = $this->getRequest()->getParam('dis');
        if (!empty($this->_info['dis'])) {
            $dis_str = '';
            foreach ($this->_info['dis'] as $k => $v) {
                $dis_str .= "$v,";
            }
            $dis_str = rtrim($dis_str, ",");
            $this->_info['dis'] = $dis_str;
        }
        return true;
    }

    //测试用，测试完后请删除 start
    /* public function test1Action(){
      echo $this->view->render('register_succeed.phtml');
      }
      public function test2Action(){
      echo $this->view->render('register_succeedajax.phtml');
      } */
    //测试用，测试完后请删除 end
}

?>
