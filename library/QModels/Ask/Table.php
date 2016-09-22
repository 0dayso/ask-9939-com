<?php

class QModels_Ask_Table extends QModels_Base_Table {
    protected $map_table = array(
            'wd_ask_history_1' => array(0, 502014, 'wd_ask_history_1_answer'),
            'wd_ask_history_2' => array(502015, 1007110, 'wd_ask_history_2_answer'),
            'wd_ask_history_3' => array(1007111, 1517111, 'wd_ask_history_3_answer'),
            'wd_ask_history_4' => array(1517112, 2042111, 'wd_ask_history_4_answer'),
            'wd_ask_history_5' => array(2042112, 3372111, 'wd_ask_history_5_answer'),
            'wd_ask_history_6' => array(3372112, 4702068, 'wd_ask_history_6_answer'),
            'wd_ask_history_7' => array(4702069, 5702233, 'wd_ask_history_7_answer'),
            'wd_ask' => array(5702234, 100000000, 'wd_answer')
    );
    
    public $dateMap = array(
            'wd_ask_history_1' => array(1222819204, 1231516800),
            'wd_ask_history_2' => array(1231516920, 1240243187),
            'wd_ask_history_3' => array(1240329599, 1248969599),
            'wd_ask_history_4' => array(1248969613, 1257868799),
            'wd_ask_history_5' => array(1257891446, 1284042006),
            'wd_ask_history_6' => array(1284048000, 1451567207),
            'wd_ask_history_7' => array(1451577600, 1462015335)
    );

    public function __construct() {
        parent::__construct();
    }

    public function init() {
        parent::init();
        $this->_db = $this->db_v2sns_read;
    }
    public function select($withFromPart = false) { 
        $this->_db = $this->db_v2sns_read;
        return parent::select($withFromPart);
    }

    public function fetchRow($where = null, $order = null) {
        $this->_db = $this->db_v2sns_read;
        return parent::fetchRow($where, $order);
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $this->_db = $this->db_v2sns_read;
        $ret = parent::fetchAll($where, $order, $count, $offset);
        return $ret;
    }


    public function insert(array $data) {
        $this->_db = $this->db_v2sns_write;
        return parent::insert($data);
    }

    public function update(array $data, $where){
         $this->_db = $this->db_v2sns_write;
        return parent::update($data, $where);
    }

    public function delete($where) {
        $this->_db = $this->db_v2sns_write;
        return parent::delete($where);
    }


}
