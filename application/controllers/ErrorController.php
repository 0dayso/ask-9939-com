<?php
class ErrorController extends Zend_Controller_Action
{
    public function init()
	{
		$this->view = Zend_Registry::get("view");
		parent::init();
	}
    public function errorAction()
    {
        header('HTTP/1.0 404 Not Found');
		echo $this->view->render("404.phtml");
    }
}
?>