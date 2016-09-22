<?php
/**
 *##############################################
 * @FILE_NAME :SurveyController.php
 *##############################################
 *
 * @author : 魏鹏
 * @MailAddr : 123109769@qq.com
 * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
 * @PHP Version :  Ver 5.21
 * @Apache  Version : Ver 2.20
 * @MYSQL Version : Ver 5.0
 * @Version : Ver Wen July 08 15:23 CST 2009
 * @DATE : Tue July 08 15:23 CST 2009
 *
 *==============================================
 * @Desc :  提问调查控制器
 *==============================================
 */
Zend_Loader::loadClass("SurveyItem", MODELS_PATH);
Zend_Loader::loadClass("SurveyText", MODELS_PATH);
class SurveyController extends Zend_Controller_Action
{

    private $ViewObj;
    private $surveyItem;
    private $surveyText;
    
    public function init()
    {
        parent::init();
        $this->surveyItem = new SurveyItem();
        $this->surveyText = new SurveyText();
        $this->keshi_obj = new Keshi();
        $this->ViewObj = Zend_Registry::get('view');
    }
    //显示调查页面
    public function indexAction(){
        $this->ViewObj->item = $this->surveyItem->List_Item();
        $CATEGORY = $this->keshi_obj->cache_keshi();
        $FUSHUKESHI = $this->keshi_obj->cache_keshi_fushu();
        $KESHIGROUP = $this->keshi_obj->cache_keshi_group();
        $this->ViewObj->CATEGORY=$CATEGORY;
        $this->ViewObj->FUSHUKESHI=$FUSHUKESHI;
        $this->ViewObj->KESHIGROUP=$KESHIGROUP;
        echo $this->ViewObj->render('survey_index.phtml');
    }
    //添加调查内容
    public function insAction(){
        $data['id'] = $this->getRequest()->getParam('survery');
        $data['content'] = trim($this->getRequest()->getParam('content'));
        if(($error = $this->check($data))===true){
            if($data['id'] == -1){
                $data['ctime'] = time();
                unset($data['id']);
                $this->surveyText->add($data);
            }else{
                $this->surveyItem->updatenum($data['id']);
            }
        }else{
            echo json_encode(array("error" => $error));
            exit;
        }
        echo json_encode(array("error" => "1"));
    }
    //验证数据合法性
    private function check($data)
    {
        if ($data['id'] == ""||$data['id'] == "undefined")
            return "请选择您遇到的问题";
        if ($data['id'] != -1&&$data['id']<=0)
            return "调查项目id不合法";
        if($data['id'] == -1&&($data['content'])=="")
            return "请填写您遇到的问题";
        if (mb_strlen($data['content'],"UTF-8") >200)
            return "调查内容长度不能大于200";
        return true;
    }
    //更改调查项目
    private function updateitme(){
        $data['content'] = $this->getRequest()->getParam('content');
        $id = $this->getRequest()->getParam('id');
        $page = (int)$this->getRequest()->getParam('page');
        if(!$id){
            Zend_Adver_Js::helpJsRedirect("/manage/survey/itemlist?page=$page", 0, "错误更改id不存在");
        }
        if (($this->ViewObj->error = $this->check($data)) === true) {
            if ($this->surveyItem->update($data," id= $id ") > 0) {
                $error = "操作成功";
            } else {
                $error = "操作失败,请稍候在试";
            }
            Zend_Adver_Js::helpJsRedirect("/manage/survey/itemlist?page=$page", 0, $error);
        }else{
            $this->ViewObj->data = $data;
            $this->ViewObj->data['id']=$id;
            $_GET['page']=$page;
        }
    }
    //添加调查项目
    private function insertitme(){
        $data['content'] = $this->getRequest()->getParam('content');
        $data['ctime'] = time();
        $page = (int)$this->getRequest()->getParam('page');
        if (($this->ViewObj->error = $this->check($data)) === true) {
            if ($this->surveyItem->add($data) > 0) {
                $error = "操作成功";
            } else {
                $error = "操作失败,请稍候在试";
            }
            Zend_Adver_Js::helpJsRedirect("/manage/survey/itemlist?page=$page", 0, $error);
        }else{
            $this->ViewObj->data = $data;
            $_GET['page']=$page;
        }
    }
    
}
?>
