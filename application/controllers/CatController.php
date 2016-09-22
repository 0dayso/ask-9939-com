<?php
/**
   *##############################################
   * @FILE_NAME :CatController.php
   *##############################################
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
   *==============================================
   * @Desc :  问答分类控制器
   *==============================================
   */

class CatController extends Zend_Controller_Action {
	public function init() {	
		$this->view = Zend_Registry::get("view");
		Zend_Loader::loadClass('Cat',MODELS_PATH);
		$this->Cat = new Cat();
	}
	

	public function indexAction() {		
		//echo "1";exit;		
		echo $this->view->render("cat.phtml");		
	}
	
}
?>
