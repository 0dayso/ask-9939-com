<?php

/**
 * ##############################################
 * @FILE_NAME :list.php
 * ##############################################
 *
 * @author : hua
 * @MailAddr : dreamcastzh@163.com
 * @copyright : Copyright (c) 2009 ��������(http://www.78.cn)
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
class Diseasekeywords extends QModels_Ask_Table {

    protected $_name = "wd_keywords";

  

    /**
     * 拼音获取关键词
     * @param type $bid
     * @return type
     */
    public function getKeywordsByInitial($pinyin_initial) {
            $sql = 'select * from wd_keywords where pinyin_initial="' . $pinyin_initial.'"';
            $result = $this->_db->fetchAll($sql);
            return $result[0];
    }
    
    /**
     * 疾病下的所有关键词
     * @param type $diseaseid
     * @return type
     */
    public function getListByDisease($diseaseid) {
            $sql = 'select * from wd_keywords where disease_id=' . $diseaseid;
            $result = $this->_db->fetchAll($sql);
            return $result;
    }
    
    /**
     * 科室下 每个疾病取一个关键词
     */
    public function getListByClassid($param) {
        $con = '';
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $con = 'd.' . $key . '=' . $value;
            }
        }
        $sql = 'SELECT k.id,k.disease_id,k.name,k.pinyin_initial from wd_keywords k RIGHT JOIN wd_disease d on k.disease_id=d.id where ' . $con . ' GROUP BY disease_id';
        $result = $this->_db->fetchAll($sql);
        return $result;
    }

}
