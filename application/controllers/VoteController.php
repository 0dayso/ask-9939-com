<?php
/**
  *##############################################
  * @FILE_NAME :voteController.php
  *##############################################
  *
  * @author :   jzg
  * @MailAddr : zhang-zehua@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Orc 26 14:31:29 CST 2010
  *
  *==============================================
  * @Desc :   问卷调查
  *==============================================
  */
   Zend_Loader::loadClass('Credit',MODELS_PATH);	#加载积分类
class voteController extends Zend_Controller_Action {

    /**
     * 
     * @var Vote
     */

    private $modVote = null;
    public function init()
	{
		$this->view = Zend_Registry::get("view");
                Zend_Loader::loadClass('Vote',MODELS_PATH);
		$this->modVote = new Vote();
                $this->Credit_obj = new Credit();		#积分类
		parent::init();
	}

    public function indexAction() {
                $this->view->voteList = $this->modVote->getVote();
                $this->view->optionList = $this->modVote->getVoteOptions();
		$this->view->page_title = "问卷调查";
		$this->view->url = "/vote";
        	echo $this->view->render('huodong/vote.phtml');
    }

    //问卷调查提交
    public function asksubmitAction() {
        $username=trim($this->_getParam('username'));
        $pwd=md5(trim($this->_getParam("userpassword")));
        if (empty($username) || empty($pwd)) {
            Zend_Adver_Js::Goback("用户名或密码不能为空！");
            exit;
        }
        $number=$this->modVote->GetUser($username,$pwd);
            //$backUrl= $number ? "":ASK_URL."/register?backurl=".ASK_URL."/vote";
        
        if (!empty($number)) {
            $json["status"] = $number;
            $json["url"]="/vote";
            $choice=array();
            $choice=$this->_getParam("choice");
            if($choice)
            {
                foreach($choice as $v)
                { 
                    $args = 'hits = hits+1';
                    $result = $this->modVote->Hits($args,$v);
                }
                $str = $this->Credit_obj->updatespacestatus("get","vote"); 	#积分
            }else{
                $json["status"] = "3";
            }
        }
        else {
            $json["status"] = "2";
            $json["url"] = "/register?backurl=/vote";
        }
        echo json_encode($json);
    }
}





?>