<?php

/**
 * ##############################################
 * @FILE_NAME :Member.php
 * ##############################################
 *
 * @author : xzx
 * @MailAddr : xzx747@126.com
 * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
 * @PHP Version :  Ver 5.21
 * @Apache  Version : Ver 2.20
 * @MYSQL Version : Ver 5.0
 * @Version : Ver Mon Sep 14 09:34 CST 2009
 * @DATE : Mon Sep 14 09:34 CST 2009
 *
 * ==============================================
 * @Desc :  会员模块
 * ==============================================
 */
class Member extends QModels_Ask_Table {

    protected $_primary = 'uid';
    protected $_name = "member";
    private $primary = 'uid';
    private $pwd = 'password';

    /**
     * @Desc 验证登录
     * @param $data array
     * @return bool
     */
    public function checklogin($data, &$msg = '', $uid = true) {//如果填写了$uid直接登陆
        //print_r($data);exit;
        $db = $this->getAdapter();
        //var_dump($db);
        // $where = "(".$db->quoteInto("username = ?", $username). $db->quoteInto(' or username_old = ? ',$username).")";
        if ($uid === true) {
            $username = $data['username'];
            $pwd = $data['password'];
            $where = "(" . $db->quoteInto("username = ?", $username) . $db->quoteInto(' or email = ? ', $username) . ")";
        } else {
            $where = "(" . $db->quoteInto("uid = ?", $uid) . ")";
        }
        //echo $username;

        $row = $this->fetchRow($where);

        //print_r($row);exit;
        if ($row->uid && ($uid !== true || $row->password == md5($pwd) || $row->password == md5(md5($pwd)))) {
            // 更新最近登录IP，登录时间
            $arr['ip'] = $_SERVER['REMOTE_ADDR'];
            $arr['lastlogin'] = time();
            $this->Edit($arr, $row->uid);

            $this->ssetcookie('member_uID', $row->uid);
            $this->ssetcookie('member_username', $row->username);
            $this->ssetcookie('member_uType', $row->uType);
            $_COOKIE['member_uType'] = $row->uType;
            $this->ssetcookie('member_nickname', $row->nickname);
            $this->ssetcookie('member_pic', str_replace(APP_ROOT, "", APP_PIC_ROOT) . "/" . $row->pic);
            $this->ssetcookie('member_credit', $row->credit);
            $this->ssetcookie('member_experience', $row->experience);
            $this->ssetcookie('member_ip', $arr['ip']);

            //print_r($row->uType); exit;
            $this->ssetcookie('member_groupinfo', "", $row->uType, $row->credit);
            //print_r($row); exit;
            //$msg = "登录成功！";
            return $row->uid;
        }
    }

    /**
     * 生成cookie值
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function ssetcookie($cookie_name, $cookie_value, $uType = "", $credit = "") {
        if ($cookie_name <> "member_groupinfo") {
            setcookie($cookie_name, $cookie_value, time() + APP_TIME_INTERVAL, "/", APP_DOMAIN);
        } else {
            // 方法一：直接读取表数据
            /**
              Zend_Loader::loadClass('Usergroup',MODELS_PATH);
              $this->Usergroup_obj = new Usergroup();
              $usergroup_array = $this->Usergroup_obj->GetList("creditlower<='".$credit."' AND uType='".$uType."'");
              $iCount = count($usergroup_array) -1;
              setcookie('member_grouptitle',$usergroup_array[$iCount]['grouptitle'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
              setcookie('member_groupname',$usergroup_array[$iCount]['groupname'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
              setcookie('member_groupicon',$usergroup_array[$iCount]['icon'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
             * */
            // 方法二：读取缓存文件的$_SGLOBAL['usergroup']
            global $_SGLOBAL;
            $tmp_usergroup_array = $_SGLOBAL['usergroup'];
            //print_r($tmp_usergroup_array);exit;
            $usergroup_array = array();

            foreach ($tmp_usergroup_array as $k => $v) {
                if ($credit >= $v['creditlower'] && $uType == $v['uType']) {
                    $usergroup_array = $v;
                }
            }
            //print_r($usergroup_array);exit;
            //print_r($usergroup_array); exit;
            //echo $_COOKIE['member_grouptitle']; exit;
            setcookie('member_grouptitle', $usergroup_array['grouptitle'], time() + APP_TIME_INTERVAL, "/", APP_DOMAIN);
            setcookie('member_groupname', $usergroup_array['groupname'], time() + APP_TIME_INTERVAL, "/", APP_DOMAIN);
            setcookie('member_groupicon', "/" . $usergroup_array['icon'], time() + APP_TIME_INTERVAL, "/", APP_DOMAIN);
        }
    }

    /**
     * @Desc 获取某用户的信息
     * @param $uid int
     * @return array
     */
    public function getInfo($uid) {
        if (!$uid)
            return "";
        
        $cacheName = 'member_'.$uid;
        $data = QLib_Cache_Client::getUserCache('member', $cacheName);  //
        if($data){
            return $data;
        }else{
//            echo '2';
            $db = $this->getAdapter();
            $where[] = $db->quoteInto("uid = ?", $uid);
            $row = $this->fetchRow($where);
    //        print_r($row->uid); exit;
            if ($row->uid) {
                $usergroup_array = $this->getGroupInfo($row->uType, $row->credit);

                $arr = Array(
                    'uid' => $row->uid,
                    'username' => $row->username,
                    'uType' => $row->uType,
                    'nickname' => $row->nickname,
                    'pic' => str_replace(APP_ROOT, "", APP_PIC_ROOT) . "/" . $row->pic, // 头像
                    'credit' => $row->credit,
                    'ip' => $row->ip,
                    'experience' => $row->experience,
                    'grouptitle' => $usergroup_array['grouptitle'],
                    'groupname' => $usergroup_array['groupname'],
                    'groupicon' => "/" . $usergroup_array['icon'],
                    'isvip' => $row->isVip,
                    'totalanswer' => $row->totalanswer
                );
                QLib_Cache_Client::setUserCache('member', $cacheName, $arr, 24);
                
                return $arr;
            }
        }
        return "";
    }

    /**
     * @Desc 获取某用户的信息
     * @param $uid int
     * @return array
     */
    public function getInfoByInId($uid) {
        if (!$uid)
            return "";

        $db = $this->getAdapter();
        $where = "uid in($uid)";
        $res = $this->fetchAll($where)->toArray();
        foreach($res as $k=>$row){
            if ($row['uid']) {
                $usergroup_array = $this->getGroupInfo($row['uType'], $row['credit']);

                $arr[] = Array(
                    'uid' => $row['uid'],
                    'username' => $row['username'],
                    'uType' => $row['uType'],
                    'nickname' => $row['nickname'],
                    'pic' => str_replace(APP_ROOT, "", APP_PIC_ROOT) . "/" . $row['pic'], // 头像
                    'credit' => $row['credit'],
                    'ip' => $row['ip'],
                    'experience' => $row['experience'],
                    'grouptitle' => $usergroup_array['grouptitle'],
                    'groupname' => $usergroup_array['groupname'],
                    'groupicon' => "/" . $usergroup_array['icon'],
                    'isvip' => $row['isVip'],
                    'totalanswer' => $row['totalanswer']
                );
            }
        } 
                return $arr;
//        return '';
    }

    /**
     * @desc 获取用户组信息
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function getGroupInfo($uType = "", $credit = "") {

        global $_SGLOBAL;
        $tmp_usergroup_array = $_SGLOBAL['usergroup'];
        //print_r($tmp_usergroup_array);exit;
        $usergroup_array = array();

        foreach ($tmp_usergroup_array as $k => $v) {
            if ($credit >= $v['creditlower'] && $uType == $v['uType']) {
                $usergroup_array = $v;
            }
        }

        return $usergroup_array;
    }

    /**
     * @Desc 退出
     * @param $msg
     * @return bool
     */
    public function logout(&$msg = "") {
        setcookie('member_uID', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_username', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_uType', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_nickname', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_pic', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_credit', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_experience', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);

        setcookie('member_grouptitle', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_groupname', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        setcookie('member_groupicon', "", time() - APP_TIME_INTERVAL, "/", APP_DOMAIN);
        $msg = "成功退出！";
    }

    /**
     * @Desc :获取列表
     * @param array $data
     * @return array
     */
    public function GetList($where = "1 ") {
        $order = " ORDER BY uid asc";
        $where_sql = trim($where);
        if ($where_sql == '1') {
            $select_sql = "SELECT *
                FROM `member` " . $order;
        } else {
            /**
             * idggz
             * ggzname 广告组名称
             * idxm 项目id
             */
            $select_sql = "SELECT *
                FROM `member`
                WHERE  " . $where . $order;
            //echo $select_sql; exit;
        }
        $re = $this->_db->fetchAll($select_sql);
        return $re;
    }

    /**
     * @Desc :获取某字段的值
     *
     * @param int $uid
     * 2009-09-16
     * @return string
     */
    public function GetValue($uid, $fieldname = 'credit') {
        if (!$uid)
            return 0;
        $select_one_sql = "SELECT `$fieldname`
				FROM `member`
				WHERE uid=" . $uid;

        //echo $select_one_sql; exit;
        $re = $this->_db->fetchAll($select_one_sql);
        return $re[0][$fieldname];
    }

    /**
     * @DESC:更改一条记录
     *
     * @param array $postarr
     * @param int $idggz
     * @return bool
     */
    public function Edit($postarr, $uid) {
        $db = $this->getAdapter();
        $where = $db->quoteInto('uid = ?', $uid);
        //echo $where;
        //print_r($postarr);exit;
        $update = $this->update($postarr, $where);
        if ($update) {
            return true;
        }
    }

    //统计记录数
    public function GetCount($where = "1") {
        //echo "ss".$where; exit;
        $where = ($where == "") ? "1" : $where;
        $result = $this->_db->fetchAll("SELECT count(*) as num FROM `member_blog` where " . $where);
        return $result[0]['num'];
    }

    /**
     * 统计会员数量
     *
     * @author  林原 2010-08-24
     * @param string $where 条件
     * @return int
     */
    public function GetMemberCount($where = "1") {
        $sql = "SELECT count(*) as num FROM `member` where " . $where;
        $result = $this->_db->fetchOne($sql);
        return $result;
    }

    /**
     * 获取会员的cookie值
     *
     * @return array
     */
    public function getCookie() {
        global $_SGLOBAL;
        $tmp_cookie_array = $_SGLOBAL['cookie'];
        return $tmp_cookie_array;
    }

    /**
     * 判断会员是否登录
     *
     * @return boolean
     */
    public function isLogin() {
        global $_SGLOBAL;
        $tmp_cookie_array = $_SGLOBAL['cookie'];
        if ($tmp_cookie_array['uid'])
            return true;
        else
            return false;
    }

    /**
     * 查看会员信息
     * @author  kxgsy163@163.com
     * @param 条件
     * @return 会员信息 array
     */
    public function get_one_by_id($id) {
        $where = $this->primary . '=\'' . $id . '\'';
        $sql = 'SELECT `' . implode('`,`', $this->_getCols()) . '` FROM `' . $this->_name . '` WHERE ' . $where;
        //echo $sql;
        $result = $this->_db->fetchRow($sql); //获取一行
        $result['pic'] = str_replace(APP_ROOT, "", APP_PIC_ROOT) . "/" . $result['pic']; // 头像
        return $result;
    }

    /**
     * 编辑会员信息
     *
     * @param 会员信息 array
     * @return  int
     */
    public function editInfo($param) {

        $tmp_id = intval($param[$this->primary]); //文章ID

        $where = $this->primary . '=\'' . $tmp_id . '\'';
        $param = $this->trimCol($param);
        unset($param[$this->primary]);
        #return $this->update($param, $where);
        $this->update($param, $where);
        return 1;
    }

    /**
     *
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

    /**
     *
     * 返回成员变量值
     */
    public function getVarValue($var = '') {
        if (!$var)
            return '';
        if (!in_array($var, array_keys(get_object_vars($this))))
            return '';
        return $this->$var;
    }

    /**
     * @Desc :获取列表
     * @param array $data
     * @return array
     */
    public function userJoinDetailList($where = "1 ", $orderBy = 'a.uid asc', $limit = '', $uType = 1) {
        return false;
        $orderBy or ( $orderBy = 'a.uid asc'); # eTime:2009-11-09 kxgsy163@163.com #找人
        $order = " ORDER BY $orderBy";
        $select_sql = "SELECT a.uid, a.nickname, a.pic" . ($uType == 2 ? ', b.zhicheng' : '') . '
			FROM `member` a, `' . ($uType == 2 ? 'member_detail_2' : 'member_detail_1') . '` b
			WHERE a.uid=b.uid ' . $where . $order . $limit;
        #echo "<hr>", $select_sql, "<hr>"; #exit;

        $re = $this->_db->fetchAll($select_sql);
        return $re;
    }

    /**
     * @Desc :获取列表
     * @param array $data
     * @return array
     */
    public function userJoinDetailCount($where = "1 ", $uType = 1) {
        return false;
        $select_sql = 'SELECT count(a.uid) as count
			FROM `member` a, `' . ($uType == 2 ? 'member_detail_2' : 'member_detail_1') . '` b
			WHERE a.uid=b.uid ' . $where;
        #echo "<hr>", $select_sql, "<hr>"; exit;

        $re = $this->_db->fetchAll($select_sql);
        return $re[0]['count'];
    }

    /**
     * 查看会员信息
     * @author  kxgsy163@163.com
     * @param 条件
     * @return 会员信息 array
     */
    public function get_one($where = '') {
        if (!$where)
            return;
        $sql = 'SELECT `' . implode('`,`', $this->_getCols()) . '` FROM `' . $this->_name . '` WHERE ' . $where;
        //echo $sql;//exit;
        $result = $this->_db->fetchRow($sql); //获取一行
        return $result;
    }

    public function getcgnum($uid) {
        if ($uid) {
            $sql = "select chgnum from hd_ask_chg where uid=$uid";
            //echo $sql;
            $result = $this->_db->fetchRow($sql);
            //var_dump($result);exit;
            return intval($result['chgnum']);
        }
    }

    //添加 问答 闯关 记录 hd_ask_chg
    public function add_ask_chg($uid) {
        $r = $this->get_uid($uid);
        if ($r) {
            $sql = "update `hd_ask_chg` set asknum=asknum+1,time=" . time() . " where uid=$uid";
        } else
            $sql = "insert into `hd_ask_chg`(uid,chgnum,asknum,time) values($uid,0,1," . time() . ")";
        //echo $sql;exit;
        $this->_db->query($sql);
        return true;
    }

    public function get_uid($uid) {
        $sql = "select cig from `hd_ask_chg` where uid=$uid";
        //echo $sql;exit;
        $r = $this->_db->fetchRow($sql);
        return $r;
    }

    public function getBySql($sSql) {
        $re = $this->_db->fetchAll($sSql);
        return $re;
    }

    public function queryBySql($sSql) {
        return $this->_db->query($sSql) > 0;
    }

    /**
     * @DESC 添加会员返回会员id
     * @author frozen
     * @date 2010-10-27
     */
    public function add_one($info) {
        $this->_db->insert("member", $info);
        $last_insert_id = $this->_db->lastInsertId();
        $this->ssetcookie('member_uID', $last_insert_id);
        $this->ssetcookie('member_username', $info['username']);
        $this->ssetcookie('member_uType', $info['uType']);
        $this->ssetcookie('member_nickname', $info['nickname']);
        return $last_insert_id;
    }

    /*
     * @DESC 添加用户时检测email是否重复
     * @author jzg
     * @date 2010-12-31
     */

    public function getEmail($email) {
        $sql = "SELECT username FROM member WHERE username='{$email}'";
        return $this->_db->fetchAll($sql);
    }

}

?>