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
class Listask extends QModels_Ask_Table {

    protected $_name = "wd_ask";

    /**
     * 
     * @param string $where
     * @param string $order
     * @param int $count
     * @param int $offset
     * @param string $index
     * @return array
     */
    public function List_Ask($where, $order = null, $count = null, $offset = 0, $index = '') {
        /* 2010-4-12 LinYuan  */
        /* 2010-11-25 LiYunxing add $index */

        //-----------筛选掉 【未审核】的问答 （@author: gaoqing -------- @date: 2015-08-10）-----------//
        if (isset($where) && !empty($where)) {
            if (stripos($where, 'examine') === false) {
                $where .= " and examine = 1 ";
            }
        } else {
            $where = " examine = 1 ";
        }

        if (!empty($index)) {
            $index = strtolower($index);
            if (stripos($index, 'force') === false) {
                $index = "FORCE INDEX($index)";
            }
        }
        $fields = 'userid,id,point,class_level1,class_level2,class_level3,classid,ctime,term,title,status,answernum,bestanswer ';
        $tbname = 'wd_ask';
        $sql = $this->createSql($fields, $tbname, $where, $index, $order, $offset, $count);
//		die($sql);
        $result = $this->_db->fetchAll($sql);
        if (empty($result)) {
            $tbname = 'wd_ask_history_7';
            $sql = $this->createSql($fields, $tbname, $where, $index, $order, $offset, $count);
            $result = $this->_db->fetchAll($sql);
        }
        return $result;
    }

    public function numRows($where) {

        if (isset($where) && !empty($where)) {
            if (stripos($where, 'examine') === false) {
                $where .= " and examine = 1 ";
            }
        } else {
            $where = " examine = 1 ";
        }
        $fields = 'count(1) as num ';
        $tbname = 'wd_ask';
        $strsql = $this->createSql($fields, $tbname, $where);
        $result = $this->_db->fetchAll($strsql);
        if ($result[0]['num'] == 0) {
            $tbname = 'wd_ask_history_7';
            $sql = $this->createSql($fields, $tbname, $where);
            $result = $this->_db->fetchAll($sql);
        }
        return $result[0]['num'];
    }

    private function createSql($fields, $tbname, $where, $index = '', $order = null, $offset = 0, $count = 0) {
        $sql = "select {$fields} ";
        $sql .="from {$tbname} {$index} ";
        if (!empty($where)) {
            $sql .=" where $where";
        }
        if (!empty($order)) {
            $sql .=" order by  $order ";
        }
        
        if($offset==null){
            $offset = 0;
        }
        if(!empty($count)){
             $sql .=" limit $offset,$count";
        }
        return $sql;
    }

    /**
     * 
     * @param type $sclassid
     * @return type
     */
    public function GetDetail($sclassid) {
        $where = 'id=' . $sclassid;

        $row = $this->_db->fetchAll("select * from wd_keshi where $where");
        //$row = $this->_db->fetchAll("SELECT * FROM `list` where $where");
        return $row[0];
    }

    /**
     * 
     * @param type $arrchildid
     * @param type $status
     * @return type
     */
    public function GetCount($arrchildid, $status) {
        if ($arrchildid != 0) {
            if ($status != 2) {
                $result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where classid in($arrchildid) and status=$status");
            } else {
                $result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where classid in($arrchildid) and point!=0");
            }
        } else {
            if ($status != 3) {
                $result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask` where status=$status");
            } else {
                $result = $this->_db->fetchAll("SELECT count(1) as count FROM `wd_ask`");
            }
        }
        return $result[0]['count'];
    }

    /**
     * 
     * @param type $array
     * @param type $space
     * @return type
     */
    public function aEmpty($array, $space = 1) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $v = $this->aempty($v, $space);
                if (empty($v))
                    unset($array["$k"]);
            } else {
                if ($space)
                    $v = trim($v);
                if (empty($v))
                    unset($array["$k"]);
            }
        }
        return $array;
    }

    /**
     * 
     * @param type $where
     * @param type $order
     * @param type $count
     * @param type $offset
     * @return type
     */
    public function GetList($where = '', $order = '', $count = '', $offset = '') {
        $result = $this->fetchAll($where, $order, $count, $offset);
        //print_r($result);
        return $result->toArray();
    }

    /**
     * 
     * @param type $bid
     * @return type
     */
    public function getlistName($bid) {
        if ($bid) {
            $sql = "select * from list where listid=" . $bid;
            $result = $this->_db->fetchAll($sql);
            return $result[0];
        }
    }

    public function getvalidity($s = 0, $d = 0) { {
            $total = $s + $d * 24 * 60 * 60;
            $validity = $total - time();
            $day = intval($validity / (3600 * 24));
            $y = round(($validity % (3600 * 24)) / 3600);
            return $day . '��' . $y . 'Сʱ';
        }
    }

    public function getKeshi($classid) {
        $aClasses = $this->_db->fetchAll("select id,name from wd_keshi where pID=$classid");
        //$result[0] = preg_replace("~^0,~is","",$result[0]);

        /* if($result[0]<>''){
          echo $sChildids = implode(",",$result[0]);
          return 1;
          $aClasses = $this->_db->fetchAll("select name,id from wd_keshi where id in($sChildids)");
          } */
        return $aClasses;
    }

    /**
     * 
     * @param type $where
     * @param type $where2
     * @return string
     */
    public function getCat_ask($where = '1', $where2 = "pID =0 AND ") {//wpg ���Ӳ�ѯ����
        $r = $this->_db->fetchAll("select id,name,pID from wd_keshi where $where2 $where order by listorder asc");
        $temp = array();
        if ($r) {
            foreach ($r as $k => $v) {
                //$v[url] = '/list.php?classid='.$v[id];
                $sql = "select id,name,pID from wd_keshi where pID=" . $v[id] . " order by listorder asc";
                $s = $this->_db->fetchAll($sql);
                foreach ($s as $kk => &$vv) {
                    $vv[url] = '/classid/' . $vv[id];
                    $temp[$v['id']]['count'] = mt_rand(10000, 100000);
                }
                $temp[$v['id']]['child'] = $s;
                $temp[$v['id']]['name'] = $v['name'];
                $temp[$v['id']]['url'] = '/classid/' . $v[id];
            }
            return $temp;
        }
    }

    public function getkeshi_ljf($pid = 0) {
        //echo "select id,name from wd_keshi where pID=$pid and id>30";exit;
        $r = $this->_db->fetchAll("select id,name from wd_keshi where pID=$pid and id>30");
        return $r;
    }

    public function getdaohang($id = 0) {
        //echo $id;
        if ($id == 0)
            return '';
        include(APP_ROOT . '/Keshi_cache.php');
        //$str = ' > <a href="http://ask.9939.com/order/index/classid/'.$v['id'].'">'.$CATEGORY[$id]['name'].' > </a>';
        if ($CATEGORY[$id]['arrparentid']) {
            $ks = explode(',', $CATEGORY[$id]['arrparentid']);
            //print_r($ks);
            foreach ($ks as $k => $v) {
                if ($k != 0)
                    $str .= ' > <a href="http://ask.9939.com/order/index/classid/' . $v . '">' . $CATEGORY[$v]['name'] . '</a>';
            }
        }
        $str .= ' > <a href="http://ask.9939.com/order/index/classid/' . $id . '">' . $CATEGORY[$id]['name'] . '</a>';
        return $str;
    }

    /**
     * 
     * @param type $uid
     * @param type $uType
     * @return type
     */
    public function getgzd($uid = 0, $uType = 1) {
        include(APP_ROOT . '/Keshi_cache.php');
        $table = 'member_detail_' . $uType;
        $r = $this->_db->fetchRow("select dis from $table where uid=$uid");
        if ($r) {
            $rs = explode(',', $r['dis']);
            //print_r($rs);
            foreach ($rs as $k => $v) {
                $temp['id'] = "$v";
                $temp['name'] = $CATEGORY[$v]['name'];
                $result[] = $temp;
            }
            return $result;
        }
    }

    /**
     * 
     * @param type $uid
     * @param type $uType
     * @return type
     */
    public function getusergzd($uid = 0, $uType = 1) {
        //include(APP_ROOT.'/Keshi_cache.php');
        $table = 'member_detail_' . $uType;
        //echo "select dis from $table where uid=$uid";exit;
        $r = $this->_db->fetchRow("select dis from $table where uid=$uid");
        return $r['dis'];
    }

    /**
     * 
     * @param type $uid
     * @param type $gzd
     * @param type $uType
     * @return boolean
     */
    public function updategzd($uid = 0, $gzd = '', $uType = 1) {
        //include(APP_ROOT.'/Keshi_cache.php');
        $table = 'member_detail_' . $uType;
        $this->_db->query("update $table set dis='$gzd' where uid=$uid");
        return true;
    }

    public function getcountnew($cid) {
        include(APP_ROOT . '/Keshi_cache.php');
        if ($cid) {
            $sArrChildid = $CATEGORY[$cid][arrchildid];
            $sql = "select count(1) as num from wd_ask where classid in($sArrChildid)";
            //exit($sql);
            $r = $this->_db->fetchRow($sql);
            return $r[num];
        }
    }

}
