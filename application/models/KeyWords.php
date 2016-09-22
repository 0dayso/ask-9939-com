<?php

/**
 *
 * ==============================================
 * @Desc :   关键词
 * ==============================================
 */
class KeyWords extends QModels_Ask_Table {

    protected $_name = "keywords";
    protected $_primary = 'id';

    /**
     * 查看文章
     *
     * @param 条件
     * @return 文章信息 array
     */
    public function List_All($where = '1', $order = '', $count = '', $offset = '') {
        $result = $this->fetchAll($where, $order, $count, $offset);
        return $result->toArray();
    }

    public function List_ByIds($wdids = array()) {
        if (count($wdids) == 0) {
            return false;
        }
        $ids = implode(',', $wdids);
        $sql = "select id,keywords,pinyin,pinyin_initial,typeid from keywords where id in ($ids) order by id desc";
        $result = $this->db_v2sns_read->fetchAll($sql);
        return $result;
    }
    /**
     * 
     * @param string $where
     * @param string $order
     * @param int $count
     * @param int $offset
     * @return array
     * 例如:EXPLAIN
     * SELECT * FROM keywords WHERE  pinyin_initial='A' and typeid in (2,3)  and  id >=(
     * SELECT id FROM keywords where pinyin_initial='A' and typeid in (2,3)  
     * ORDER BY id asc limit 600,1
     * ) ORDER BY id asc limit 0,200;
     * 
     */
    public function list_forpaging($where = '', $order = 'id desc', $count = 0, $offset = 0) {
        $sql = 'select id,keywords,pinyin,pinyin_initial,typeid from keywords';
        if (!empty($where)) {
            $sql.=' where ' . $where;
        }
        if(!empty($order)){
            $sql.=' order by '.$order;
        }
        if($count>0){
            $sql.=" limit $offset,$count";
        }
        $result = $this->db_v2sns_read->fetchAll($sql);
        $total = $this->GetCount($where);
        return array('list'=>$result,'total'=>$total);
    }
    
    private function createQuerySql($where = '', $order = 'id desc', $count = 0, $offset = 0){
        if($offset>=5000){
            $tmp_sql = 'select id from keywords';
            if (!empty($where)) {
                $tmp_sql.=' where ' . $where;
            }
            if(!empty($order)){
                $tmp_sql.=' order by '.$order;
            }
            if($count>0){
                $tmp_sql.=" limit $offset,$count";
            }
            
            $sql = 'select a.id,keywords,pinyin,pinyin_initial,typeid from keywords ';
            
            $sql = sprint("%s a inner join (%s) b on a.id=b.id",$sql,$tmp_sql);
            return $sql;
        }else{
            $sql = 'select id,keywords,pinyin,pinyin_initial,typeid from keywords';
            if (!empty($where)) {
                $sql.=' where ' . $where;
            }
            if(!empty($order)){
                $sql.=' order by '.$order;
            }
            if($count>0){
                $sql.=" limit $offset,$count";
            }
            return $sql;
        }
    }

    public function list_one($id) {
        $where = $this->_primary . '=' . intval($id);
        $sql = 'SELECT * FROM `' . $this->_name . '` WHERE ' . $where;

        $result = $this->db_v2sns_read->fetchRow($sql); //获取一行
        return $result;
    }

    public function getKeywordName($value) {
        $where = " `pinyin` = '" . $value . "'";
        $sql = 'SELECT `keywords` FROM `' . $this->_name . '` WHERE ' . $where;
        $result = $this->db_v2sns_read->fetchRow($sql); //获取一行
        return $result;
    }

    //获取数据总和
    public function GetCount($where = '') {
        $sql = 'SELECT count(1) as num FROM `' . $this->_name . '`';
        if (!empty($where)) {
            $sql.=' WHERE ' . $where;
        }
        $result = $this->db_v2sns_read->fetchRow($sql);
        return $result['num'];
    }
    
    public static function createCacheRandWords(array $condition = array(),$expired=24){
        ini_set('memory_limit', '512M');
        $return_pagenum_list = array();
        $save_root_path = 'rand_words';
        $rand_words_save_path = $save_root_path.DIRECTORY_SEPARATOR.'caches/randwords';
        //拼音首字母页码
        $cache_key_letter_pagenum = 'pagenum';
        $pagenum_expired = 2 * $expired;
        $cache_letter_pagenum_data = QLib_Cache_Client::getCache($rand_words_save_path, $cache_key_letter_pagenum, $pagenum_expired);
        if ($cache_letter_pagenum_data) {
            $return_pagenum_list = $cache_letter_pagenum_data;
        }
        
        //获取字母所对应的数据
        $letter_list = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($letter_list);
        $return_list = array();
        $max_kw_length = 5000; // $size;
        for ($i = 0; $i < $len; $i++) {
            $wd = strtoupper($letter_list{$i});
            $pagenum = isset($return_pagenum_list[$wd]) ? $return_pagenum_list[$wd] : 0;
            $pagenum = intval($pagenum) + 1;
            $tmp_offset = $pagenum * $max_kw_length;
            $return_info = Search::search_words_byinitial($wd, $tmp_offset, $max_kw_length, $condition);
            if (count($return_info['list']) == 0 && $pagenum > 0) {
                $return_info = Search::search_words_byinitial($wd, 0, $max_kw_length, $condition);
                $pagenum = 0;
            }
            $ret = $return_info['list'];
            $return_list[$wd] = $ret;
            $return_pagenum_list[$wd] = $pagenum;
        }
        self::createRandWordsForRedis($return_list);
        QLib_Cache_Client::setCache($rand_words_save_path, 'words',$return_list, $expired);
        QLib_Cache_Client::setCache($rand_words_save_path, $cache_key_letter_pagenum, $return_pagenum_list, $pagenum_expired);
        return $return_list;
    }
    
    /**
     * 
     * 获取随机关键词
     * @param type $size
     * @param array $condition 
     *  $conditions = array(
      'column_id' => array(1)
      );
     * @return type
     */
    public static function getCacheRandWords($size = 100, array $condition = array()) {
        $max_dis_length = 30;//$size
        $key_cache_words = APP_CACHE_PREFIX.'CACHE_RAND_WORDS';
        $key_cache_words_total= APP_CACHE_PREFIX.'CACHE_RAND_WORDS_TOTAL';
        
        $redis = QLib_Utils_CacheHelper::Q();
        $cache_words_total = $redis->hMget($key_cache_words_total,range('A','Z'));
        $return_list = array();
        if(!empty($cache_words_total)){
            $rand_key = array();
            foreach($cache_words_total as $k=>$v){
                if(!empty($v)){
                    $rand_num = $v>$max_dis_length?$max_dis_length:$v;
                    $rand_index_arr = array_rand(range(0,$v-1),$rand_num);
                     if (is_array($rand_index_arr)) {
                        foreach($rand_index_arr as $vv){
                            $key = sprintf('%s_%d',$k,$vv);
                            $rand_key[] = $key;
                        }
                    }else{
                        $key = sprintf('%s_%d',$k,$rand_index_arr);
                        $rand_key[] = $key;
                    }
                }
            }
            $return_redis_list = $redis->hMget($key_cache_words,$rand_key);
            if(!empty($return_redis_list)){
                foreach($return_redis_list as $v){
                    if(!empty($v)){
                        $words_info = json_decode($v, true);
                        $capital = $words_info['pinyin_initial'];
                        $return_list[$capital][] = $words_info;
                    }
                }
            }
//            var_dump($return_list);exit;
        }
        
        if(empty($return_list)){
            $expired = 24; //小时
            $return_list = self::createCacheRandWords($condition, $expired);
        }
        return $return_list;
        
    }

    /**
     * 
     * 获取随机关键词
     * @param type $size
     * @param array $condition 
     *  $conditions = array(
      'column_id' => array(1)
      );
     * @return type
     */
    public static function getRandWordsFromFiles($size = 100, array $condition = array()) {
        $expired = 24; //小时
        $save_root_path = 'rand_words';
        $cache_key = sprintf('%s|%s|%s', 'caches', 'randwords', 'words');
        //生成的缓存文件为24小时，由crontab：createcacherandwords.php控制缓存更新,此处缓存文件永不过期
        $data = QLib_Cache_Client::getPageCache($save_root_path, $cache_key, $expired*2);
        if ($data) {
            return $data;
        } else {
            $return_list = self::createCacheRandWords($condition, $expired);
            return $return_list;
        }
    }
    
    /**
     * 创建redis缓存
     * @param array $return_list
     */
    private static function createRandWordsForRedis($return_list = array()){
        //保存缓存到redis
        $redis_expired = 24*3600; //秒
        $key_cache_words = APP_CACHE_PREFIX.'CACHE_RAND_WORDS';
        $key_cache_words_total= APP_CACHE_PREFIX.'CACHE_RAND_WORDS_TOTAL';
        $redis = QLib_Utils_CacheHelper::Q();
        
        $hash_cache_words_total = array();
        $hash_cache_words = array();
        foreach($return_list as $k=>$word_child){
            $wd = $k;
            $hash_cache_words_total[$wd]=count($word_child);
            foreach($word_child as $kk=>$v){
                $key = sprintf('%s_%d',$wd,$kk);
                $hash_cache_words[$key] = json_encode($v);
            }
        }
        $redis->hMset($key_cache_words,$hash_cache_words, $redis_expired);
        $redis->hMset($key_cache_words_total,$hash_cache_words_total, $redis_expired);
    }


}
