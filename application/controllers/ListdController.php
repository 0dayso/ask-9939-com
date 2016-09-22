<?php
/**
   *==============================================
   * @Desc :  问答医师团列表控制器
   *==============================================
   */

class ListdController extends Zend_Controller_Action {
	public function init() {
		$this->viewObj = Zend_Registry::get("view");
		Zend_Loader::loadClass('Listd',MODELS_PATH); 
		$this->Listd = new Listd();
	}

	public function indexAction() {
		//医生团成员列表
		$class = $this->getRequest()->getParam('class');
		#echo  $class;
		$area = $this->getRequest()->getParam('area');
		
		$team = $this->Listd->mem_list($class,$area);
		$this->viewObj->team = $team;
		//医生排行
		$doc_ph  = $this->Listd->doc_paihang();
		$this->viewObj->doc_ph = $doc_ph;
		echo $this->viewObj->render("listd.phtml");
	}

}
?>
