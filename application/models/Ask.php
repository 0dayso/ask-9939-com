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
class Ask extends Listask {

    protected $_name = "wd_ask";
    private $primary = 'id';

    public function init() {
        try {
            parent::init();
            $this->db_www = $this->db_v2_read;
            $this->db_jb = $this->db_dzjb_read;
            $this->db_v2jb = $this->db_v2jb_read;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function setName($tbname) {
        $this->_name = $tbname;
    }

    /**
     * 
     * @param type $where  'classid = 44'
     * @param type $order
     * @param type $count
     * @param type $offset
     * @return type
     */
    public function getList($where = '', $order = '', $count = '', $offset = '') {
        return $this->List_Ask_default($where, $order, $count, $offset);
    }

    /**
     * 查看文章
     *
     * @param 条件
     * @return 文章信息 array
     */
    public function List_Ask_default($where = '1', $order = '', $count = '', $offset = '') {
        $this->setName("wd_ask");
        $result = $this->fetchAll($where, $order, $count, $offset);
        return $result->toArray();
    }

    /**
     * 根据日期条件筛选使用不同的表查询数据
     * lc@2016-8-29
     * @param type $date
     * @param type $where
     * @param type $order
     * @param type $count
     * @param type $offset
     * @return type
     */
    public function listHistoryByDate($date, $where = '1',  $order = '', $count = '', $offset = '',$return_count_flag = false) {
        $time = strtotime($date);
        $dateMap = $this->dateMap;
        $asktable = 'wd_ask';
        foreach ($dateMap as $k => $v) {
            if ($time >= $v[0] && $time <= $v[1]) {
                $asktable = $k;
            }
        }
        $this->setName($asktable);
        $result = $this->fetchAll($where, $order, $count, $offset);
        $list =  $result->toArray();
        $total_record = 0;
        if($return_count_flag===true){
            $total_record = $this->numRows($where);
        }
        return array('list'=>$list,'total'=>$total_record);
    }

    /**
     * 添加文章
     *
     * @param 文章信息 array
     * @return 插入ID int
     */
    public function add($param) {

        $param['ctime'] = time();
        //去除主键
        unset($param[$this->primary]);
        //去除param数组中键值为非列的值
        $param = $this->trimCol($param);
        return $this->insert($param);
        //$this->_name='wd_120answer';
        //var_dump($param);
        //$sqlinsert = 'insert into wd_120answer (title, content, depart1, depart2, depart3, age, sexnn) values ("'.$param['title'].'", "'.$param['content'].'", "'.$param['class_level1'].'", "'.$param['class_level2'].'", "'.$param['class_level3'].'", "'.$param['age'].'", "'.$param['sexnn'].'")';
        //直接放到抓取表里
        /* $indata['title'] = $param['title'];
          $indata['content'] = $param['content'];
          $indata['depart1'] = $param['class_level1'];
          $indata['depart2'] = $param['class_level2'];
          $indata['depart3'] = $param['class_level3'];
          $indata['age'] = $param['age'];
          $indata['sexnn'] = $param['sexnn'];
          $indata['broadcast'] = $param['broadcast'];
          $indata['point'] = $param['point'];
          $indata['classid'] = $param['classid'];
          $indata['answerUid'] = $param['answerUid'];
          $indata['userid'] = $param['userid'];
          $indata['status'] = $param['status'];
          $indata['ip'] = $param['ip'];
          $indata['ctime'] = $param['ctime'];
          return $this->_db->insert('wd_120answer', $indata); */

        //return $this->_db->insert('wd_120answer',$param);
    }

    public function edit($param = array()) {
        $tmp_id = intval($param[$this->primary]);
        $where = $this->primary . '=\'' . $tmp_id . '\'';
        //echo $where;exit;
        //去除主键
        unset($param[$this->primary]);
        //print_r($param); exit;
        //去除param数组中键值为非列的值
        $param = $this->trimCol($param);
        $param = $this->trimValueIsNull($param);
        return $this->update($param, $where);
    }

    public function ismobiles($id, $ismobile) {
        if (!$id)
            return '';
        $sql = "UPDATE wd_ask SET ismobile=" . $ismobile . " WHERE id={$id}";
        $this->_db->query($sql);
    }

    public function ismails($id, $ismail) {
        if (!$id)
            return '';
        $sql = "UPDATE wd_ask SET ismail=" . $ismail . " WHERE id={$id}";
        $this->_db->query($sql);
    }

    public function getask_num() {
        $sql = 'SELECT count(1) as ask_id FROM wd_ask';
        $aField = $this->_db->fetchRow($sql); //获取一行	
        return $aField['ask_id'];
    }

    public function editAnswerNum($id = 0) {
        #echo $id;
        if (!$id)
            return '';

        // xzxin 2010-05-31
        $aField = array();
        $aField = $this->_db->fetchRow("select answernum from wd_ask_answernum  where askid=" . $id); //获取一行	
        if (!$aField) {
            //echo "<!--xzxin 实时读取-->";
            //获取回答数
            $sql = 'SELECT count(1) as answernum FROM `wd_answer` WHERE askid=' . $id;
            $aField = $this->_db->fetchRow($sql); //获取一行	
            $aField['answernum'] = intval($aField['answernum']);
            $aField['askid'] = $id;
            $this->_db->insert("wd_ask_answernum", $aField);
        } else {
            $iAnswernum = $aField['answernum'] + 1;
            $sql = "UPDATE wd_ask_answernum SET answernum=" . $iAnswernum . " WHERE askid='$id'";
            #echo $sql;
            $this->_db->query($sql);
            //	echo "<!--xzxin 读取中间表-->";
            $sql = "UPDATE wd_ask SET answernewnum=answernewnum+1,answernum={$iAnswernum} WHERE id={$id}";
            $this->_db->query($sql);

            //lc@2016-6-29
            //如果当前回答条数大于等于2条，设置当前问题为禁止回答状态
            if ($iAnswernum >= 2) {
                $sql = "UPDATE wd_ask SET prohibit_reply=1 WHERE id={$id}";
                $this->_db->query($sql);
            }
        }
    }

    public function list_one($id = '') {
        if (!$id)
            return;
        $id = intval($id);
        foreach ($this->map_table as $k => $v) {
            if ($id >= $v[0] && $id <= $v[1]) {
                $this->setName($k);
                break;
            }
        }
        $where = $this->primary . '=' . intval($id);
//        var_dump($where);exit;
        $sql = 'SELECT `' . implode('`,`', $this->_getCols()) . '` FROM `' . $this->_name . '` WHERE ' . $where;
//                echo $sql;
//                exit;
//        var_dump($sql);exit;
        $result = $this->_db->fetchRow($sql); //获取一行
        return $result;
    }

    public function get_one($id = '') {
        if (!$id)
            return;
        $id = intval($id);
        $tabalname_answer = 'wd_answer';
        foreach ($this->map_table as $k => $v) {
            if ($id >= $v[0] && $id <= $v[1]) {
                $tabalname_answer = $v[2];
                break;
            }
        }

        $result = $this->list_one($id); //获取一行
        if (!$result)
            return "";

        // xzxin 2010-05-31
        $aField = array();
        $aField = $this->_db->fetchRow("select answernum from wd_ask_answernum  where askid=" . $id); //获取一行	
        if (!$aField) {
            //echo "<!--xzxin 实时读取-->";
            //获取回答数
            $sql = 'SELECT count(*) as answernum FROM `' . $tabalname_answer . '` WHERE askid=' . $id;
            $aField = $this->_db->fetchRow($sql); //获取一行	
            $aField['askid'] = $id;
            $this->_db->insert("wd_ask_answernum", $aField);
        } else {
            //	echo "<!--xzxin 读取中间表-->";
        }

        $result['answernum'] = intval($aField['answernum']);
        return $result;
    }

    public function get_one_new($id = '') {
        if (!$id)
            return;
        $id = intval($id);
        $where = $this->primary . '=' . intval($id);
        $sqltable = 'select * from wd_ask_tablespace where minid <= ' . $id . ' and maxid >= ' . $id;
        echo $sqltable;
        exit;
        $tableinfo = $this->_db->fetchRow($sqltable);
        if ($tableinfo) {
            $tabalname_ask = $tableinfo['tablename'];
            $tabalname_answer = $tableinfo['tablename_answer'];
        } else {
            $tabalname_ask = $this->_name;
            $tabalname_answer = 'wd_answer';
        }
        $sql = 'SELECT `' . implode('`,`', $this->_getCols()) . '` FROM `' . $tabalname_ask . '` WHERE ' . $where;
        $result = $this->_db->fetchRow($sql); //获取一行
        if (!$result)
            return "";

        // xzxin 2010-05-31
        $aField = array();
        $aField = $this->_db->fetchRow("select answernum from wd_ask_answernum  where askid=" . $id); //获取一行	
        if (!$aField) {
            //echo "<!--xzxin 实时读取-->";
            //获取回答数
            $sql = 'SELECT count(*) as answernum FROM `' . $tabalname_answer . '` WHERE askid=' . $id;
//			$sql = 'SELECT count(*) as answernum FROM `'.$tabalname_answer.'` WHERE askid='.$id;		
            $aField = $this->_db->fetchRow($sql); //获取一行	
            $aField['askid'] = $id;
            $this->_db->insert("wd_ask_answernum", $aField);
        } else {
            //	echo "<!--xzxin 读取中间表-->";
        }

        $result['answernum'] = intval($aField['answernum']);
        return $result;
    }

    public function get_one_test($id = '') {
        if (!$id)
            return;
        $where = $this->primary . '=' . intval($id);
        $sql = 'SELECT `' . implode('`,`', $this->_getCols()) . '` FROM `' . $this->_name . '` WHERE ' . $where;

        //echo $sql;//exit;
        $result = $this->_db->fetchRow($sql); //获取一行

        if (!$result)
            return "";

        // xzxin 2010-05-31
        $aField = array();
        $aField = $this->_db->fetchRow("select answernum from wd_ask_answernum  where askid=" . $id); //获取一行	
        if (!$aField) {
            //echo "<!--xzxin 实时读取-->";
            //获取回答数
            $sql = 'SELECT count(*) as answernum FROM `wd_answer` WHERE askid=' . $id;
            $aField = $this->_db->fetchRow($sql); //获取一行	
            $aField['askid'] = $id;
            $this->_db->insert("wd_ask_answernum", $aField);
        } else {
            //	echo "<!--xzxin 读取中间表-->";
        }

        $result['answernum'] = intval($aField['answernum']);
        return $result;
    }

    public function numRows($where) {
        $strsql = "SELECT count(1) as num FROM `" . $this->_name . "`";
        if (isset($where) && !empty($where)) {
            $strsql.=' where ' . $where;
        }
        $result = $this->_db->fetchAll($strsql);

        return $result[0]['num'];
    }

    public function newask($where) {
        $result = $this->_db->fetchAll("SELECT id,title FROM `" . $this->_name . "`  " . $where);
        return $result;
    }

    //格式化where
//    private function formatwhere($where=1){
//        
//    }

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

    /**
     * 部落浏览次数计数
     */
    public function Add_viewnum($id) {
        exit; // xzxin 2010-07-21
        $row = $this->_db->fetchRow("select * from wd_ask_count where askid = $id");
        if (!$row[askid]) {
            $this->_db->query("insert into wd_ask_count (`askid`,`hits_time`,`hits`,`hits_day`,`hits_week`,`hits_month`) value ('$id','" . time() . "','1','1','1','1')");
        } else {
            $day_now = date("Y-m-d", time());
            $day_last = date("Y-m-d", $row[hits_time]);
            $week_now = date("Y-m", time()) . '-' . ceil(date("d", time()) / 7);
            $week_last = date("Y-m", time()) . '-' . ceil(date("d", $row[hits_time]) / 7);
            $month_now = date("Y", time()) . '-' . ceil(date("m", time()) / 7);
            $month_last = date("Y", time()) . '-' . ceil(date("m", $row[hits_time]) / 7);
            if ($day_now == $day_last) {
                $result = 'hits_day=hits_day+1,hits_week=hits_week+1,hits_month=hits_month+1';
            } elseif ($week_now == $week_last) {
                $result = 'hits_week=hits_week+1,hits_month=hits_month+1';
            } elseif ($month_now == $month_last) {
                $result = 'hits_week = 1,hits_month = hits_month+1';
            } else {
                $result = 'hits_month = 1 , hits_week = 1';
            }
            $rows_affected = $this->_db->query("update wd_ask_count set $result,hits=hits+1 where askid=$id");
        }
    }

    //获取相关文章
    public function getCorArt($keywords, $count, $title = '') {
        #return '';
        $tmp_kw_array = explode(' ', trim($keywords));
        #print_r($tmp_kw_array);
        $tmp_kw = $tmp_kw_array[0] ? $tmp_kw_array[0] : $title;
        unset($a);
        $html = array();
        for ($i = 1; $i <= 2; $i++) {
            (strlen($tmp_kw) < 7) or ( $tmp_kw = $this->getSubstr($tmp_kw, 0, 4));
            $tmp_xml = file_get_contents('http://211.167.92.198:9080/complex/search?kw=' . $tmp_kw . '&page=' . $i . '&tid=3');
            preg_match_all("|<record>.*?<\/record>|is", $tmp_xml, $tmp_a);
            #echo $tmp_kw;
            if (count($tmp_a[0])) {
                foreach ($tmp_a[0] as $sKey => $sVal) {
                    if (count($html) >= $count)
                        break;
                    preg_match("|.*<id>\D*(\d*).*</id>.*<title>\s*<!\[CDATA\[(.*)\]\]></title>.*<info>.*?(\d{10}).*(http\S*).*|is", $sVal, $tmp_b);
                    if (count($tmp_b) < 5)
                        continue;
                    $tmp_title = iconv('gb2312', 'utf-8', $tmp_b[2]);
                    $tmp_url = iconv('gb2312', 'utf-8', $tmp_b[4]);
                    if (!$tmp_title)
                        continue;
                    #$a[$tmp_b[3]]['title'] = strip_tags($tmp_title);
                    #$a[$tmp_b[3]]['url'] = $tmp_url;
                    $html[] = '<li><a href="' . $tmp_url . '">' . $this->getSubstr(strip_tags($tmp_title), 0, 20) . '</a></li>';
                }
            }
        }
        return implode(' ', $html);
        ##################  以下废弃  ###########################
        return '';
        try {
            $aArt = $this->db_www->fetchAll("select * from Article where status=20 AND  keywords like ('%$keywords%') order by articleid desc limit 0,$count");
            foreach ($aArt as $k => $v) {
                $URL = $v[url];
                $TITLE = $this->getSubstr($v[title], 0, $words);
                $html .= '<li><a href="' . $URL . '">' . $TITLE . '</a></li>';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $html;
    }

    //获取相关疾病
    public function getCorDis($kid, $count) {
        try {
            $r = $this->_db->fetchAll("select name from wd_keshi where id=$kid");
            $rs = $this->db_jb->fetchAll("select id from 9939_section_category where name='" . $r[0][name] . "'");
            $aDis = $this->db_jb->fetchAll("select * from 9939_dzjb a ,9939_disease_content b where b.keshi like ('%" . $rs[0][id] . "%') and a.contentid=b.contentid order by a.contentid desc limit 0,$count");
            foreach ($aDis as $k => $v) {
                $URL = "http://jb.9939.com/dis/{$v[contentid]}/";
                $TITLE = $this->getSubstr($v[title], 0, $words);
                $html .= '<li><a href="' . $URL . '">' . $TITLE . '</a></li>';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $html;
    }

    /* 字符串截取函数
     * $str 字符串
     * $beginStr 开始取的位置
     * $length  要取的长度,个数
     * $isHaveBland 统计是否包含空格符,0 是不统计空格,1统计空格
     * $codingLength   utf-8 $codingLength=3,gb2312 $codingLength=2;
     */

    public function getSubstr($str, $beginStr = -1, $length = -1, $isHaveBlank = 1, $codingLength = 3) {
        $len = strlen($str);
        //$str=str_replace("&nbsp;"," ",$str);//过滤掉html空格
        if ($length == -1)
            $length = -$beginStr - 1;
        $i = 0;
        $strCount = 0;
        $subStr = "";
        while ($i < $len) {
            if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", substr($str, $i, 1))) {
                if ($strCount >= $beginStr) {
                    if (strlen(mb_substr($str, $strCount, 1, 'utf8')) == 2) {
                        $subStr .= substr($str, $i, $codingLength - 1);
                    } else {
                        $subStr .= substr($str, $i, $codingLength);
                    }
                    //echo $i.$subStr.'<br>';
                }
                //过滤特殊字符·····
                if (strlen(mb_substr($str, $strCount, 1, 'utf8')) == 2) {
                    $i += $codingLength - 1;
                } else {
                    $i += $codingLength;
                }
                $strCount++;
            } elseif (substr($str, $i, 6) == "&nbsp;") {// 处理空格
                if ($strCount >= $beginStr) {
                    $subStr .= substr($str, $i, 6);
                }
                if ($isHaveBlank == 1) {//统计空格
                    $strCount++;
                }
                $i+=6;
            } else {
                if ($strCount >= $beginStr) {
                    $subStr .= substr($str, $i, 1);
                }
                if ($isHaveBlank == 1) {//统计空格
                    $strCount++;
                } else {//不统计空格
                    if (substr($str, $i, 1) != " ") {
                        $strCount++;
                    }
                }
                $i+=1;
            }

            if ($strCount == $length + $beginStr) {
                break;
            }
        }
        if ($beginStr == -1) {
            return $strCount;
        }
        //$subStr=str_replace(" ","&nbsp;",$subStr);//还原html空格
        return $subStr;
    }

    /**
     * 根据手机号，查询手机验证码
     * @param varchar $mobile
     * @return type
     */
    public function mobileCode($mobile = '') {
        return $this->_db->fetchRow("SELECT * FROM wd_ask_mobile_code where type=1 and mobile = " . $mobile . " order by updatetime desc");
    }

    public function updateMember($uid = '', $data = array()) {
        $table = "member";  // 更新的数据表
        $db = $this->getAdapter();

        $where = $db->quoteInto("uid = ?", $uid); // where语句
        // 更新表数据,返回更新的行数
        return $db->update($table, $data, $where);
    }

    /**
     * 分诊
     * @param type $askinfo 问答的信息
     */
    public function triage($askinfo) {
        $wd_keshi_ret_default = array('class_level1' => '15', 'class_level2' => '0', 'class_level3' => '0');

        $content = $askinfo['content'];

        $explain_result = QLib_Utils_SearchHelper::scws($content);

        $callback = array($this, 'filter');
        //获取过滤数据
        $explain_result = array_filter($explain_result, $callback);
//        print_r($explain_result);exit;
        //如果无法从用户输入的内容里分出有意义的词，返回默认科室
        if (!isset($explain_result[0])) {
            return $wd_keshi_ret_default;
        }

        $all = array();
        foreach ($explain_result as $k => $v) {
            $all[$v['attr']][] = $v;
        }

        $wd_keshi_ret['class_level3'] = '0'; //默认三级疾病为0
        //
        //Ⅰ
        //如果有疾病词
        if (isset($all['XB'][0])) {
            //第一个病的词的名称
            $disease_name = $all['XB'][0]['word'];

            //通过疾病名查疾病id
            $disease_sql = "select id from 9939_disease where name='{$disease_name}'";
            $disease_ret = $this->db_v2jb->fetchRow($disease_sql);
            $disease_id = $disease_ret['id']; //9725
            //先直接查疾病表是否有该疾病
            //如果有直接返回查询结果
            //如果没有，通过从症状表查
            //通过查wd_disease表得到disease_id
            $disease_name_sql = "select id,name from 9939_disease where id={$disease_id}";
            $disease_name_ret = $this->db_v2jb->fetchRow($disease_name_sql);
            $disease_name = $disease_name_ret['name'];

            $class_level3_sql = "select class_level1,class_level2,id from wd_disease where name='{$disease_name}'";
            $class_level3 = $this->_db->fetchRow($class_level3_sql);
            if (isset($class_level3['id'])) {
                $wd_keshi_ret = $class_level3;
                $wd_keshi_ret['class_level3'] = $class_level3['id'];
                unset($wd_keshi_ret['id']);
                return $wd_keshi_ret;
            } else {
                //通过疾病id查科室
                $disease_condition = 'disease' . $disease_id;
                $department_sql = "select depart.name from 9939_department depart,9939_depart_rel_merge merge where merge.source_flag=1 and merge.unique_key='{$disease_condition}' and merge.departmentid=depart.id";
                $department_ret = $this->db_v2jb->fetchRow($department_sql);
                $department_name = $department_ret['name'];

                //通过科室名称进入wd_keshi查询最终的
                $wd_keshi_sql = "select class_level1,class_level2 from wd_keshi where name='{$department_name}'";
                $wd_keshi_ret = $this->_db->fetchRow($wd_keshi_sql);
                //如果查不到数据放到默认科室里
                if (!isset($wd_keshi_ret['class_level1'])) {
                    $wd_keshi_ret = $wd_keshi_ret_default;
                } else {
                    if (!$wd_keshi_ret['class_level3']) {
                        $class_level3_sql = "select id from wd_disease where id='{$wd_keshi_ret['class_level3']}'";
                        $class_level3 = $this->_db->fetchRow($class_level3_sql);
                    }
                }
                return $wd_keshi_ret;
            }
        }

        //Ⅱ
        //如果没有疾病词，症状词，只有部位词
        if (!isset($all['XZ'][0]) && isset($all['XS'][0])) {
            //第一个部位的词的名称
            $condition_part_name = $all['XS'][0]['word'];

            //通过部位名查部位id
            $condition_part_sql = "select id from 9939_part where name='{$condition_part_name}'";
            $condition_part_ret = $this->db_v2jb->fetchRow($condition_part_sql);
            $condition_part_id = $condition_part_ret['id']; //39
            //通过部位查找所有的疾病
            $part_disease_sql = "SELECT unique_key FROM 9939_part_rel_merge WHERE source_flag=1 AND partid={$condition_part_id}";
            $part_disease_ret = $this->db_v2jb->fetchRow($part_disease_sql);
            $disease_id = str_replace('disease', '', $part_disease_ret['unique_key']); //7211
            //先直接查疾病表是否有该疾病
            //如果有直接返回查询结果
            //如果没有，通过从症状表查
            //通过查wd_disease表得到disease_id
            $disease_name_sql = "select id,name from 9939_disease where id={$disease_id}";
            $disease_name_ret = $this->db_v2jb->fetchRow($disease_name_sql);
            $disease_name = $disease_name_ret['name'];

            $class_level3_sql = "select class_level1,class_level2,id from wd_disease where name='{$disease_name}'";
            $class_level3 = $this->_db->fetchRow($class_level3_sql);
            if (isset($class_level3['id'])) {
                $wd_keshi_ret = $class_level3;
                $wd_keshi_ret['class_level3'] = $class_level3['id'];
                unset($wd_keshi_ret['id']);
                return $wd_keshi_ret;
            } else {
                //通过疾病id查科室
                $disease_condition = 'disease' . $disease_id;
                $department_sql = "select depart.name from 9939_department depart,9939_depart_rel_merge merge where merge.source_flag=1 and merge.unique_key='{$disease_condition}' and merge.departmentid=depart.id";
                $department_ret = $this->db_v2jb->fetchRow($department_sql);
                $department_name = $department_ret['name'];

                //通过科室名称进入wd_keshi查询最终的
                $wd_keshi_sql = "select class_level1,class_level2 from wd_keshi where name='{$department_name}'";
                $wd_keshi_ret = $this->_db->fetchRow($wd_keshi_sql);
                //如果查不到数据放到默认科室里
                if (!isset($wd_keshi_ret['class_level1'])) {
                    $wd_keshi_ret = $wd_keshi_ret_default;
                }
                return $wd_keshi_ret;
            }
        }

        //Ⅲ
        //如果没有疾病词，部位词，只有症状词
        if (!isset($all['XS'][0]) && isset($all['XZ'][0])) {
            //症状的词的名称
            $symtpom_name = $all['XZ'][0]['word'];


            //通过症状名查症状id
            $symtpom_sql = "select id from 9939_symptom where name='{$symtpom_name}'";
            $symtpom_ret = $this->db_v2jb->fetchRow($symtpom_sql);
            $symtpom_id = $symtpom_ret['id']; //6521
            //通过症状id查关联疾病,取第一个病
//            $rel_disease_sql = "SELECT diseaseid FROM 9939_disease_symptom_rel WHERE symptomid ={$symtpom_id}";
            $rel_disease_sql = "SELECT 9939_disease.id,9939_disease.`name`,9939_disease_symptom_rel.diseaseid FROM 9939_disease LEFT JOIN 9939_disease_symptom_rel ON 9939_disease_symptom_rel.diseaseid=9939_disease.id WHERE symptomid = {$symtpom_id}";
            $rel_disease_ret = $this->db_v2jb->fetchRow($rel_disease_sql); //获取所有症状关联的疾病id
            $rel_disease_id = $rel_disease_ret['diseaseid']; //取第一个病
            $rel_disease_name = $rel_disease_ret['name']; //取第一个病
            //先直接查疾病表是否有该疾病
            //如果有直接返回查询结果
            //如果没有，通过从症状表查
            //通过查wd_disease表得到disease_id
            $class_level3_sql = "select class_level1,class_level2,id from wd_disease where name='{$rel_disease_name}'";
            $class_level3 = $this->_db->fetchRow($class_level3_sql);

            if (isset($class_level3['id'])) {
                $wd_keshi_ret = $class_level3;
                $wd_keshi_ret['class_level3'] = $class_level3['id'];
                unset($wd_keshi_ret['id']);
                return $wd_keshi_ret;
            } else {
                //通过疾病id查科室
                $disease_condition = 'disease' . $rel_disease_id;
                $department_sql = "select depart.name from 9939_department depart,9939_depart_rel_merge merge where merge.source_flag=1 and merge.unique_key='{$disease_condition}' and merge.departmentid=depart.id";
                $department_ret = $this->db_v2jb->fetchRow($department_sql);

                $department_name = $department_ret['name'];

                //通过科室名称进入wd_keshi查询最终的
                $wd_keshi_sql = "select class_level1,class_level2 from wd_keshi where name='{$department_name}'";
                $wd_keshi_ret = $this->_db->fetchRow($wd_keshi_sql);

                //如果查不到数据放到默认科室里
                if (!isset($wd_keshi_ret['class_level1'])) {
                    $wd_keshi_ret = $wd_keshi_ret_default;
                }
                return $wd_keshi_ret;
            }
        }
        //Ⅳ
        //如果没有疾病词，有症状词和部位词
        if (isset($all['XZ'][0]) && isset($all['XS'][0])) {

            //**************1、根据症状取出所有的疾病**************
            foreach ($all['XZ'] as $k => $v) {
                //症状的词的名称
                $symtpom_name = $v['word'];

                //通过症状名查症状id
                $symtpom_sql = "select id from 9939_symptom where name='{$symtpom_name}'";
                $symtpom_ret = $this->db_v2jb->fetchRow($symtpom_sql);
                $symtpom_id = $symtpom_ret['id']; //6521
                //通过症状id查关联疾病,取第一个病
                $rel_disease_sql = "SELECT diseaseid FROM 9939_disease_symptom_rel WHERE symptomid ={$symtpom_id}";
                $rel_disease_ret = $this->db_v2jb->fetchAll($rel_disease_sql); //获取所有症状关联的疾病id
                if (isset($rel_disease_ret[0])) {
                    break;
                }
            }
            foreach ($rel_disease_ret as $k => $v) {
                $rel_disease_id_arr[] = $v['diseaseid'];
            }


            //**************2、根据部位取出所有的疾病**************
            //第一个部位的名称
            $condition_part_name = $all['XS'][0]['word'];

            //通过部位名查部位id
            $condition_part_sql = "select id from 9939_part where name='{$condition_part_name}'";
            $condition_part_ret = $this->db_v2jb->fetchRow($condition_part_sql);

            //3、如果根据部位查不到疾病，设置疾病id为根据症状查出来的第一个疾病
            if (!$condition_part_ret) {
                $last_disease_id = $rel_disease_id_arr[0];
            } else {
                $condition_part_id = $condition_part_ret['id']; //67
                //3.1、通过部位查找所有的疾病
                $part_disease_sql = "SELECT unique_key FROM 9939_part_rel_merge WHERE source_flag=1 AND partid={$condition_part_id}";
                $part_disease_ret = $this->db_v2jb->fetchAll($part_disease_sql);
                foreach ($part_disease_ret as $k => $v) {
                    $part_disease_id_arr[] = str_replace('disease', '', $v['unique_key']);
                }

                //**************3.2、取二者的交集的第一个疾病作为最终疾病**************
                $last_disease_id_arr = array_intersect($rel_disease_id_arr, $part_disease_id_arr);
                //如果没有交集时：
                if (empty($last_disease_id_arr)) {
                    if (!empty($rel_disease_id_arr)) {
                        $last_disease_id_arr[0] = $rel_disease_id_arr[0];
                    } else if (!empty($part_disease_id_arr)) {
                        $last_disease_id_arr[0] = $part_disease_id_arr[0];
                    }
                }
                sort($last_disease_id_arr);
                $last_disease_id = $last_disease_id_arr[0];
            }


            //先直接查疾病表是否有该疾病
            //如果有直接返回查询结果
            //如果没有，通过从症状表查
            //通过查wd_disease表得到disease_id
            $disease_name_sql = "select id,name from 9939_disease where id={$last_disease_id}";
            $disease_name_ret = $this->db_v2jb->fetchRow($disease_name_sql);
            $disease_name = $disease_name_ret['name'];

            $class_level3_sql = "select class_level1,class_level2,id from wd_disease where name='{$disease_name}'";
            $class_level3 = $this->_db->fetchRow($class_level3_sql);
            if (isset($class_level3['id'])) {
                $wd_keshi_ret = $class_level3;
                $wd_keshi_ret['class_level3'] = $class_level3['id'];
                unset($wd_keshi_ret['id']);
                return $wd_keshi_ret;
            } else {
                //通过疾病id查科室
                $disease_condition = 'disease' . $last_disease_id;
                $department_sql = "select depart.name from 9939_department depart,9939_depart_rel_merge merge where merge.source_flag=1 and merge.unique_key='{$disease_condition}' and merge.departmentid=depart.id";

                $department_ret = $this->db_v2jb->fetchRow($department_sql);
                $department_name = $department_ret['name'];

                //通过科室名称进入wd_keshi查询最终的
                $wd_keshi_sql = "select class_level1,class_level2 from wd_keshi where name='{$department_name}'";
                $wd_keshi_ret = $this->_db->fetchRow($wd_keshi_sql);

                //如果查不到数据放到默认科室里
                if (!isset($wd_keshi_ret['class_level1'])) {
                    $wd_keshi_ret = $wd_keshi_ret_default;
                }

                return $wd_keshi_ret;
            }
        }
        //未考虑到的情形，直接返回默认科室
        return $wd_keshi_ret_default;
    }

    public function filter($v) {
        return in_array($v['attr'], array('XB', 'XZ', 'XS'));
    }

    public function addMobile($mobile, $code) {
        $db = $this->getAdapter();
        $data = array(
            'type' => 1,
            'mobile' => $mobile,
            'code' => $code,
            'updatetime' => time(),
            'addtime' => time()
        );
        $mobile = $this->_db->fetchRow("select* from wd_ask_mobile_code where mobile = " . $mobile . " and type=1");
        if (!empty($mobile)) {
            $data = array(
                'code' => $code,
                'updatetime' => time(),
            );
            $where = $db->quoteInto("id = ?", $mobile['id']);
            return $db->update("wd_ask_mobile_code", $data, $where);
        } else {
            return $this->_db->insert("wd_ask_mobile_code", $data);
        }
    }

    /**
     * 查询当前手机号，是否已经绑定用户
     * @param string $mobile 手机号
     */
    public function getMember($mobile) {
        $res = $this->_db->fetchRow("select * from member where mobile = '$mobile'");
        if ($res) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * 根据用户id，查询出当前用户信息
     * @param int $uid 用户id
     */
    public function getMember_2($uid) {
        return $this->_db->fetchRow("select * from member where uid = '$uid'");
    }

    /**
     * 查询当前用户名是否已经注册
     * @param string $username 用户名
     */
    public function getMemberOne($username, $uid) {
        return $this->_db->fetchRow("select * from member where username = '$username' and uid != '$uid'");
    }

    public function GetAskList_2($where = '1', $order = '', $count = '', $offset = '', $answer = 'wd_answer') {
        $result = $this->fetchAll($where, $order, $count, $offset); //查询所有的问题
        $result = $result->toArray();
        foreach ($result as $k => $v) {
            $result[$k]['answer'] = $this->_db->fetchAll("select id,content,addtime from $answer where askid = " . $v['id'] . " order by addtime asc"); //查问题所对应的答案
        }
        return $result;
    }
   

}
