<?php

/**
 * 
 * 搜索索引名称   
 * index_9939_com_v2sns_keywords_all
 * index_9939_com_v2_keywords_all
 * index_9939_com_v2_art
 * index_9939_com_dzjb_art
 * index_9939_com_dzjb_disease
 * index_9939_com_jb_art //新疾病库
 * index_9939_com_jb_disease //新疾病库
 * index_9939_com_jb_symptom //新疾病库
 * index_wd_ask
 * index_wd_ask_history_1
 * index_wd_ask_history_2
 * index_wd_ask_history_3
 * index_wd_ask_history_4
 * index_wd_ask_history_5
 * index_wd_ask_history_6
 * 
 */
class Search extends SearchTrait {

    private static $map_indexer_func = array(
        'index_9939_com_v2_keywords_all' => 'search_words_all_data',
        'index_9939_com_v2_art' => 'search_zx_article_data',
        'index_9939_com_dzjb_art' => 'search_dzjb_article_data',
        'index_9939_com_jb_art' => 'search_jb_article_data',
        'index_9939_com_jb_disease' => 'search_disease_data',
        'index_9939_com_jb_symptom' => 'search_symptom_data',
        'index_9939_com_jb_art,index_9939_com_v2_art' => 'search_article_data',
        'index_9939_com_jb_disease,index_9939_com_jb_symptom' => 'search_disease_symptom_merge_data',
        'index_wd_ask,index_wd_ask_history_1,index_wd_ask_history_2,index_wd_ask_history_3,index_wd_ask_history_4,index_wd_ask_history_5,index_wd_ask_history_6,index_wd_ask_history_7' => 'search_ask_data',
    );

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * typeid 1:问答词 2:资讯词
     */

    public static function search_words_byinitial($wd, $offset, $size, array $condition = array()) {
        $total = 0;
        $ret_list = array();
        if (!empty($wd)) {
            $wd_obj = new KeyWords();
            $where_arr = array();
            $where_arr[] = sprintf("%s='%s'", 'pinyin_initial', $wd);
            foreach ($condition as $k => $v) {
                if (isset($v['args']) && is_array($v['args'])) {
                    $column_id = $v['args'][0];
                    $search_ids = implode(',', $v['args'][1]);
                    $where_arr[] = " $column_id in ($search_ids)";
                }
            }
            $where = implode(' and ', $where_arr);
            $search_result = $wd_obj->list_forpaging($where, 'id desc', $size, $offset);
            $ret_list = $search_result['list'];
            $total = $search_result['total'];
        }
        return array('list' => $ret_list, 'total' => $total);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * typeid 1:问答词 2:资讯词
     */

    public static function search_words_all($wd, $offset, $size, array $condition = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_v2_keywords_all';
            $search_result = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $condition, $explainflag, $explain_ext_config);
            return self::parse_search_data($search_result, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * 查询老疾病文章
     */

    public static function search_dzjb_article($wd, $offset, $size, array $condition = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_dzjb_art';
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $condition, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * 查询疾病文章
     */

    public static function search_jb_article($wd, $offset, $size, array $conditions = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_jb_art';
            $conditions = self::fill_filter_condition($conditions);
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * 查询词相关文章；
     */

    public static function search_article($wd, $offset, $size, array $conditions = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_jb_art,index_9939_com_v2_art';
            $conditions = self::fill_filter_condition($conditions);
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * 查询资讯文章
     */

    public static function search_zx_article($wd, $offset, $size, array $conditions = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_v2_art';
            $conditions = self::fill_filter_condition($conditions);
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * 查询词相关文章；优先疾病文章,然后资讯文章
     */

    public static function search_relarticle($wd, $offset, $size, array $conditions = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $return_list = self::search_jb_article($wd, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            $ret_list = $return_list['list'];
            $total = $return_list['total'];
            $diff_num = $size - count($ret_list);
            if ($diff_num > 0) {
                $return_art_list = self::search_zx_article($wd, $offset, $diff_num, $conditions, $explainflag, $explain_ext_config);
                $art_list = $return_art_list['list'];
                $art_total = $return_art_list['total'];
                if ($art_total > 0) {
                    foreach ($art_list as $k => $v) {
                        $v['url'] = $v['url'];
                        $ret_list[] = $v;
                    }
                }
                $total+=$art_total;
            }
            $explain_words = $return_list['explain_words'];
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * 查询词相关问答
     */

    public static function search_ask($wd, $offset, $size, array $conditions = array(), $explainflag = 1, $explain_ext_config = array()) {
        $total = 0;
        $ask_res_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_wd_ask,index_wd_ask_history_1,index_wd_ask_history_2,index_wd_ask_history_3,index_wd_ask_history_4,index_wd_ask_history_5,index_wd_ask_history_6,index_wd_ask_history_7';
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ask_res_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /**
     * 
     * @param type $wd
     * @param type $offset
     * @param type $size
     * @param type $explainflag  1:采用第三方分词 0:不采用第三方分词
     * @param array $conditions
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * @return array
     */
    public static function search_disease($wd, $offset, $size, $explainflag = 0, array $conditions = array(), $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_jb_disease';
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /**
     * 根据关键词查相关症状
     * @param type $wd
     * @param type $offset
     * @param type $size
     * @param type $explainflag  1:采用第三方分词 0:不采用第三方分词
     * @param array $conditions
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * @return array
     */
    public static function search_symptom($wd, $offset, $size, $explainflag = 0, array $conditions = array(), $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_jb_symptom';
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /**
     * 
     * @param type $wd
     * @param type $offset
     * @param type $size
     * @param type $explainflag
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * @return type
     */
    public static function search_disease_symptom_merge($wd, $offset, $size, $explainflag = 0, array $conditions = array(), $explain_ext_config = array()) {
        $total = 0;
        $ret_list = array();
        $explain_words = array($wd);
        if (!empty($wd)) {
            $indexer_name = 'index_9939_com_jb_disease,index_9939_com_jb_symptom';
            $ret = QLib_Utils_SearchHelper::Search($wd, $indexer_name, $offset, $size, $conditions, $explainflag, $explain_ext_config);
            return self::parse_search_data($ret, $indexer_name);
        }
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array(column_id,array(1))
      )
      );
     * 
     * typeid 1:问答词 2:资讯词
     */

    public static function search_disease_symptom_words_byinitial($wd, $offset, $size, array $condition = array()) {
        $total = 0;
        $ret_list = array();
        if (!empty($wd)) {
            $wd_obj = new DiseaseSymptomMerge();
            $where_arr = array();
            $where_arr[] = sprintf("%s='%s'", 'capital', $wd);
            foreach ($condition as $k => $v) {
                if (isset($v['args']) && is_array($v['args'])) {
                    $column_id = $v['args'][0];
                    $search_ids = implode(',', $v['args'][1]);
                    $where_arr[] = " $column_id in ($search_ids)";
                }
            }
            $where = implode(' and ', $where_arr);
            $search_result = $wd_obj->list_forpaging($where, 'unique_key desc', $size, $offset);
            $ret_list = $search_result['list'];
            $total = $search_result['total'];
        }
        return array('list' => $ret_list, 'total' => $total);
    }

    /**
     * 
     * @param type $sphinxdata sphinx搜索结果
     * @param type $indexer_name
     * @return type
     */
    public static function parse_search_data($sphinxdata, $indexer_name) {
        $sphinx_result_fn = self::$map_indexer_func[$indexer_name];
        return self::$sphinx_result_fn($sphinxdata);

//        $indexer_name = strtolower(trim($indexer_name));
//        $sphinx_result_fn = isset(self::$map_indexer_func[$indexer_name])?self::$map_indexer_func[$indexer_name]:'';
//        if(!empty($sphinx_result_fn)){
//            return self::$sphinx_result_fn($sphinxdata);
//        }
//        return array('list'=>array(),'total'=>0, 'explain_words' => $sphinxdata['explain_words']);
    }

    /**
     * 加过滤条件查询日期条件
     * @return type
     */
    private static function fill_filter_condition($conditons = array()) {
        $curr_time = time();
        $min = 1420041600;
        $max = $curr_time;
        $condition = array(
            array(
                'filter' => 'filter_range',
                'args' => array('createtime', $min, $max)
            )
        );
        if (count($conditons) > 0) {
            $condition = array_merge($conditons, $condition);
        }
        return $condition;
    }

}
