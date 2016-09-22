<?php

/**
 * ##############################################
 * @FILE_NAME :Keshi.php
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
class Keshi extends QModels_Ask_Table {

    protected $_name = "wd_keshi";
    private $primary = 'id';
    private $pid = 'pID';
    private $name = 'name';

    public function getList($where = '', $order = 'listorder asc', $count = '', $offset = '') {
        $result = $this->fetchAll($where, $order, $count, $offset);
        return $result->toArray();
    }

    /**
     * 
     * 返回成员变量值
     */
    public function getValue($var = '') {
        if (!$var)
            return '';
        if (!in_array($var, array_keys(get_object_vars($this))))
            return '';
        return $this->$var;
    }

    public function get_one($id = '') {
        $res = $this->getKeshifenliCache(array($id));
        return $res[$id];
    }

    /**
     * 
     * PHP文件中获取一级、二级科室
     */
    public function get_keshi() {
        $KESHIGROUP = $this->cache_keshi_group();
        //科室部分
        $get_keshi = array();
        //一级科室去除常见疾病、不孕不育、症状标签537,524,272,443
        //$arr=array('537');
        //一级科室 家居、生活、亚健康、药品单独提出,做为”其他“分类
        $qt = array('3', '15', '22', '436', '524', '272', '443', '537');
        foreach ($KESHIGROUP['0'] as $k => $v) {
            // if(in_array($v['id'],$arr)) continue;
            if (in_array($v['id'], $qt)) {
                $get_keshi['15']['name'] = "其他";
                $qt_url = '/classid/' . $v['id'];

                $v['url'] = $qt_url;
                $get_keshi['15']['child'][] = $v;
                $get_keshi['15']['keywords'] = "";
                $get_keshi['15']['url'] = "";
                $get_keshi['15']['id'] = '15';
                $get_keshi['15']['url'] = '/classid/15';
                continue;
            }
            $s = $KESHIGROUP[$v['id']];
            if (is_array($s)) {
                foreach ($s as $kk => &$vv) {
                    $vv['url'] = '/classid/' . $vv['id'];
                }
            }
            $get_keshi[$v['id']]['child'] = $s;
            $get_keshi[$v['id']]['name'] = $v['name'];
            $get_keshi[$v['id']]['keywords'] = $v['keywords'];
            $get_keshi[$v['id']]['url'] = '/classid/' . $v['id'];
            $get_keshi[$v['id']]['id'] = $v['id'];
        }

        $a = $get_keshi['15'];
        unset($get_keshi['15']);
        $get_keshi['15'] = $a;

        return $get_keshi;
    }

    /*
     * 根据所选科室判断所属的一级科室的id(其他对应的id为15)
     */

    public function a_department($classid) {
        $classid = ($classid == 0) ? 32 : $classid;
        $qt = array('3', '15', '22', '436', '524', '272', '443', '537');
        if (in_array($classid, $qt)) {
            return '15';
        }
        $cate = $this->get_one($classid);
        if ($classid == $cate['id']) {
            if ($cate['pID'] != '0') {
                return $this->a_department($cate['pID']);
            } else {
                return $cate['id'];
            }
        }
    }

    /*
     * 根据科室id获取上级科室信息
     *
     */

    public function get_keshi_nav($keshiid) {
        $ret_arr = array();
        $yjks = $this->a_department($keshiid);
        while (true) {
            if ($keshiid > 0) {
                $jb_url = "/classid/$keshiid";
                $item = $this->get_one($keshiid);
                $item['url'] = $jb_url;
                $ret_arr[] = $item;
                $keshiid = $item['pID'];
                if ($keshiid == 0)
                    break;
            }else {
                break;
            }
        }
        if ($yjks == '15') {
            $jb_url = "/classid/15";
            $item = $this->get_one(15);
            $item['url'] = $jb_url;
            $item['name'] = "其他";
            $ret_arr[] = $item;
        }

        $result = array_reverse($ret_arr, true);
        return $result;
    }

    /*
     * 获取科室分组
     */

    public function cache_keshi_group() {
        @include(APP_DATA_PATH . '/cache_wd_keshi_group_fenli_all.php');
        return $KESHIGROUP;
    }

    /*
     * 获取科室附属缓存
     */

    public function cache_keshi_fushu() {
        @include(APP_DATA_PATH . '/cache_wd_keshi_fushu.php');
        return $FUSHUKESHI;
    }

    /*
     * 获取科室缓存
     */

    public function cache_keshi() {
        ini_set('memory_limit', '512M');
        require (APP_DATA_PATH . '/cache_wd_keshi_fenli_all.php');
        return $CATEGORY;
    }

    /**
     * 获取科室部分缓存（默认全部）
     * @param type $class_level1
     * @return type
     */
    public function get_keshi_redis($class_level1 = array()) {
        $return_list = array();
        $key = 'redis_keshi_only_cache';
        $key_cache_category = APP_CACHE_PREFIX . $key;
        $redis = QLib_Utils_CacheHelper::Q();
        $return_redis_list = $redis->hMget($key_cache_category, $class_level1);

        if (!empty($return_redis_list)) {
            $return_parent_catids = array();
            foreach ($return_redis_list as $v) {
                if (!empty($v)) {
                    $info = json_decode($v, true);
                    $classid = $info['id'];
                    $level1[$classid] = $info;
                }
            }
        }
        if (empty($return_list)) {
            $return_list = $this->createCacheKeshiFenli($key);
        }
        return $return_list;
    }

    /**
     * 科室缓存
     * @param type $catids 获取所有的传空数组
     * @$is_get_tree_flag int; 0不获取父\子集合,1获取父\子集合
     * @$is_get_disease_flag int; 是否获取疾病 1,
     * @return array
     */
    public function getKeshifenliCache($classidids = array(), $is_get_tree_flag = 0) {
        $return_list = array();
        $key = 'redis_keshi_fenli_cache';
        $key_cache_category = APP_CACHE_PREFIX . $key;
        $redis = QLib_Utils_CacheHelper::Q();
        $return_redis_list = $redis->hMget($key_cache_category, $classidids);
        if (!empty($return_redis_list)) {
            $return_parent_catids = array();
            foreach ($return_redis_list as $k=>$v) {
                if (!empty($v)) {
                    $info = json_decode($v, true);
                    $classid = $k;//$info['id'];
                    $return_list[$classid] = $info;
                    if($info['class_level1']!=0){
                        $return_parent_catids[] = $info['class_level1'];
                    }
                    if($info['class_level2']!=0){
                       $return_parent_catids[] = $info['class_level2'];
                    }
                    if ($info['child'] == 1) {
//                        $return_parent_catids[] = $info['arrchildid'];
                        
                        if(isset($info['kes_arrchildid'])){
                            $return_parent_catids[] = $info['kes_arrchildid'];
                        }
                        $return_parent_catids[] = $info['dis_arrchildid'];
                    }
                }
            }
            if ($is_get_tree_flag === 1) {
                $catids = implode(',', $return_parent_catids);
                $arr_catids = explode(',', $catids);

                $return_redis_list = $redis->hMget($key_cache_category, $arr_catids);
                if (!empty($return_redis_list)) {
                    foreach ($return_redis_list as $k=>$v) {
                        if (!empty($v)) {
                            $info = json_decode($v, true);
                            $classid = $k;//$info['id'];
                            $return_list[$classid] = $info;
                        }
                    }
                }
            }
        }
        if (empty($return_list)) {
            $return_redis_list = $redis->hMget($key_cache_category, array());
            if (empty($return_redis_list)) {
                $return_list = $this->createCacheKeshiFenli();
            }
        }
        return $return_list;
    }

    /**
     * 生成redis缓存
     * @param type $expired
     * @return type
     */
    public function createCacheKeshiFenli($key = '') {
        $redis = QLib_Utils_CacheHelper::Q();
        $redis_expired = 24 * 3600; //缓存24小时

        if ($key == 'redis_keshi_only_cache') {
            $key_cache_keshi = APP_CACHE_PREFIX . $key;
            $hash_only_keshi = $this->get_keshi();
            foreach ($hash_only_keshi as $k => $v) {
                $hash_cache_keshi[$k] = json_encode($v);
            }
            $redis->hMset($key_cache_keshi, $hash_cache_keshi, $redis_expired);
            return $hash_only_keshi;
        } else {
            $return_list = $this->cache_keshi();
            $hash_cache_keshi = array();
            $level1 = array();
            foreach ($return_list as $k => $v) {
                $hash_cache_keshi[$k] = json_encode($v);
            }
            $key = 'redis_keshi_fenli_cache';
            $key_cache_keshi = APP_CACHE_PREFIX . $key;
            $redis->hMset($key_cache_keshi, $hash_cache_keshi, $redis_expired);
            return $return_list;
        }
    }

}
