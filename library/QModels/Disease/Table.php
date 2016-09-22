<?php

/**
 * 新疾病库的基础类（9939_com_v2jb）
 * @author gaoqing
 */
class QModels_Disease_Table extends QModels_Base_Table {

    public function __construct() {
        parent::__construct();
    }

    public function init() {
        parent::init();
        $this->_db = $this->db_v2jb_read;
    }
    public function select($withFromPart = false) { 
        $this->_db = $this->db_v2jb_read;
        return parent::select($withFromPart);
    }

    public function fetchRow($where = null, $order = null) {
        $this->_db = $this->db_v2jb_read;
        return parent::fetchRow($where, $order);
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $this->_db = $this->db_v2jb_read;
        $ret = parent::fetchAll($where, $order, $count, $offset);
        return $ret;
    }


    public function insert(array $data) {
        $this->_db = $this->db_v2jb_write;
        return parent::insert($data);
    }

    public function update(array $data, $where){
         $this->_db = $this->db_v2jb_write;
        return parent::update($data, $where);
    }

    public function delete($where) {
        $this->_db = $this->db_v2jb_write;
        return parent::delete($where);
    }


}
