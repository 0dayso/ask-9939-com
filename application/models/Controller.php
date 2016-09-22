<?php
class Controller
{
	protected static $_instance = null;
	private $modclist=array();
	
	public function __construct()
	{
		$managec_config=new Zend_Config_Ini(APP_CONFIG_FILE, 'managecontrollers');
		$this->modclist=$managec_config->managec->toArray();
	}
	
	public static function getInstance()
	{
		if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
	}
	
	public static function listAll()
	{
		return self::getInstance()->getList();
	}
	
	public function getList()
	{
		return $this->modclist;
	}
}
?>