<?php

class QModels_Base_Table extends Zend_Db_Table {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function init() {
        $this->db_v2_write = $this->factory(Zend_Registry::get('db_v2_write'));
        $this->db_v2_read = $this->factory(Zend_Registry::get('db_v2_read'));

        $this->db_v2sns_write = $this->factory(Zend_Registry::get('db_v2sns_write'));
        $this->db_v2sns_read = $this->factory(Zend_Registry::get('db_v2sns_read'));

        $this->db_dzjb_write = $this->factory(Zend_Registry::get('db_dzjb_write'));
        $this->db_dzjb_read = $this->factory(Zend_Registry::get('db_dzjb_read'));

        $this->db_lady_write = $this->factory(Zend_Registry::get('db_lady_write'));
        $this->db_lady_read = $this->factory(Zend_Registry::get('db_lady_read'));

        $this->db_tongji_write = $this->factory(Zend_Registry::get('db_tongji_write'));
        $this->db_tongji_read = $this->factory(Zend_Registry::get('db_tongji_read'));

        //新增 gaoqing 2016-04-26
        $this->db_v2jb_write = $this->factory(Zend_Registry::get('db_v2jb_write'));
        $this->db_v2jb_read = $this->factory(Zend_Registry::get('db_v2jb_read'));
    }

    private function factory($config) {
        return Zend_Db::factory('PDO_MYSQL', $config);
    }
}
