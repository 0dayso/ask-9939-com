<?php

/**
 * ##############################################
 * @FILE_NAME :Ask.php
 * ##############################################
 *
 * @author :   矫雷
 * @MailAddr : kxgsy163@163.com
 * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
 * @PHP Version :  Ver 5.21
 * @Apache  Version : Ver 2.20
 * @MYSQL Version : Ver 5.0
 * @Version : Ver Thu Jun 18 18:00:29 CST 2009
 * @DATE : Thu Jun 29 14:31:29 CST 2009
 *
 * ==============================================
 * @Desc :  
 * ==============================================
 */
class Answer extends QModels_Ask_Table {

    public $_name = "wd_answer";
    private $primary = 'id';

    public function tablename($name) {
        if ($name) {
            $this->_name = $name;
        } else {
            $this->_name = 'wd_answer';
        }
    }

    public function settbname($ask_id) {
        $sqltable = 'select * from wd_ask_tablespace where minid <= ' . $ask_id . ' and maxid >= ' . $ask_id;
        $tableinfo = $this->_db->fetchRow($sqltable);
        if ($tableinfo) {
            $this->tablename($tableinfo['tablename_answer']);
        }
    }
    
    public function getbyaskid($ask_id){
        $where = 'askid='. $ask_id;
        $order = ' addtime asc';  
        $this->settbname($ask_id);
        $tmp_answer_array = $this->getList($where, $order);
        return $tmp_answer_array;
    }

    public function getList($where = '1', $order = '', $count = '', $offset = '') {
        $result = $this->fetchAll($where, $order, $count, $offset);
        return $result->toArray();
    }

    //获取好评缓存数据
    public function praisestep_cache($id) {
        $result = $this->_db->fetchAll("select * from `praisestep_cache` where tid='$id' and mark='1'");
        if ($result)
            return $result;
        else
            return false;
    }

    /**
     * 添加新回答
     * @param type $param
     * @return type
     */
    public function addAnswer($param) {
        $this->settbname($param['askid']);
        return $this->add($param);
    }

    /**
     * 添加文章
     *
     * @param 文章信息 array
     * @return 插入ID int
     */
    public function add($param) {

        $param['addtime'] or ( $param['addtime'] = time());

        //去除主键
        unset($param[$this->primary]);
        //去除param数组中键值为非列的值
        $param = $this->trimCol($param);
        //var_dump($param);
        return $this->insert($param);
    }

    public function edit($param = array()) {
        $tmp_id = intval($param[$this->primary]);
        $where = $this->primary . '=\'' . $tmp_id . '\'';

        //去除主键
        unset($param[$this->primary]);
        //print_r($param); exit;
        //去除param数组中键值为非列的值
        $param = $this->trimCol($param);
        $param = $this->trimValueIsNull($param);
        return $this->update($param, $where);
    }

    public function get_one($id = '') {
        if (!$id)
            return;
        $where = $this->primary . '=' . intval($id);
        $sql = 'SELECT `' . implode('`,`', $this->_getCols()) . '` FROM `' . $this->_name . '` WHERE ' . $where;
        $result = $this->_db->fetchRow($sql); //获取一行
        return $result;
    }

    public function numRows($where = 1) {
        $result = $this->_db->fetchAll("SELECT count(1) as num FROM `" . $this->_name . "` where " . $where);
        return $result[0]['num'];
    }

    /**
     * 去除param数组中键值为非列名单元
     */
    private function trimCol($param) {
        foreach ($param as $k => &$v) {
            if (!in_array($k, $this->_getCols())) {
                unset($param[$k]);
            }
        }
        return $param;
    }

    private function trimValueIsNull($param = array()) {
        if (!$param)
            return '';
        foreach ($param as $k => $v) {
            if (!$v) {
                unset($param[$k]);
            }
        }
        return $param;
    }
    
    
    public function getAnswerNum($where=1){
//        echo "SELECT * as num FROM `wd_ask_answernum` where " . $where;
//        exit;
        $result = $this->_db->fetchAll("SELECT * FROM `wd_ask_answernum` where " . $where);
        return $result[0]['answernum'];
    }
}
