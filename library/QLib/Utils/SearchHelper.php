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
class QLib_Utils_SearchHelper {

    private static $sphinx_filter_map = array(
        'filter' => 'SetFilter',
        'filter_range' => 'SetFilterRange',
        'filter_floatrange' => 'SetFilterFloatRange'
    );

    public static function Q() {
        return Q_Search::factory();
    }

    /**
     * 
     * @param type $queries  
     * $queries = array(
     *  'words1'=>array(
     *      'word'=>'糖尿病',
     *      'indexer'=>'*',//必须有
     *      'offset'=>0,
     *      'size'=>10,
     *      'condition'=>array(array('filter'=>'filter_range','args'=>array('column_id',values))),
     *      'explainflag'=>1,  //$explainflag 1:采用第三方分词 0:不采用第三方分词
     *      'explain_ext_config'=>array() //分词的扩展配置
     *  ),
     * 'words2'=>array(
     *      'word'=>'糖尿病',
     *      'indexer'=>'*',//必须有
     *      'offset'=>0,
     *      'size'=>10,
     *      'condition'=>array(array('filter'=>'filter_range','args'=>array('column_id',values))),
     *      'explainflag'=>1,  //$explainflag 1:采用第三方分词 0:不采用第三方分词
     *      'explain_ext_config'=>array() //分词的扩展配置
     *  )
     * );
     * @return type
     */
    public static function batchSearch($queries = array()) {
        $search = self::Q();
        $explain_words = array();
        $index_arr = array();
        foreach ($queries as $k => $v) {
            $kw = isset($v['word']) ? $v['word'] : '';
            if (!empty($kw)) {
                $keywords = $kw;
                $indexer = isset($v['indexer']) ? $v['indexer'] : '*';
                $offset = isset($v['offset']) ? $v['offset'] : 0;
                $size = isset($v['size']) ? $v['size'] : 1;
                $explainflag = isset($v['explainflag']) ? $v['explainflag'] : 1;
                $condition = isset($v['condition']) ? $v['condition'] : array();
                $explain_ext_config = isset($v['explain_ext_config']) ? $v['explain_ext_config'] : array();
                $offset_limit = $offset + $size;
                $total_offset = $offset_limit > 1000 ? 1000 : $offset_limit;
                $search->SetLimits($offset, $size, $total_offset, 1000);
                $search->SetMatchMode(SPH_MATCH_EXTENDED2); //匹配模式   1:SPH_MATCH_ANY 6:SPH_MATCH_EXTENDED2
                $search->SetRankingMode(SPH_RANK_SPH04);
                $search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC,@id DESC"); //排序方式   @param int $sort_mode 排序方式 4:SPH_SORT_EXTENDED
                $search->SetFieldWeights(array('title' => 1000, 'name' => 1000));
                if ($explainflag === 1) {
                    $search->SetMatchMode(SPH_MATCH_EXTENDED2);
                    $keywords = self::explainWords($keywords, $explain_ext_config);
                }
                if (count($condition) > 0) {
                    foreach ($condition as $k => $v) {
                        if (is_array($v) && (isset($v['filter']) && isset(self::$sphinx_filter_map[$v['filter']]))) {
                            $filter_method = self::$sphinx_filter_map[$v['filter']];
                            $param_arr = $v['args'];
                            call_user_func_array(array($search, $filter_method), $param_arr);
                        }
                    }
                }
                $words = isset($keywords['kw']) ? $keywords['kw'] : $keywords;
                $search->AddQuery($words, $indexer);
                $search->ResetFilters();
                $explain_words[] = isset($keywords['reg_words']) ? $keywords['reg_words'] : $keywords;
                $index_arr[] = $indexer;
            }
        }
        $result = $search->RunQueries();
        $ret = array();
        if (!empty($result)) {
            foreach ($result as $k => $v) {
                $v['explain_words'] = $explain_words[$k];
                $v['indexer'] = $index_arr[$k];
                if (!empty($v['matches'])) {
                    $v['total'] = $v['total_found'] > 1000 ? 1000 : $v['total_found'];
                }
                $ret[] = $v;
            }
        }
        return $ret;
    }

    /**
     * 根据关键字全文搜索
     * @param string $keyword 关键词
     * @param string $indexer 搜索索引
     * @param int $offset
     * @param int $size
     * @param int $match_mode 匹配模式   1:SPH_MATCH_ANY 6:SPH_MATCH_EXTENDED2
     * @param int $sort_mode 排序方式   @param int $sort_mode 排序方式 4:SPH_SORT_EXTENDED
     * $conditions = array(
      array(
      'filter'=>'filter_range',
      'args'=>array($attribute,values)
      )
      );
     * 
     * $explainflag 1:采用第三方分词 0:不采用第三方分词
     */
    public static function Search($keyword, $indexer, $offset, $size, array $conditions = array(), $explainflag = 1, $explain_ext_config = array()) {
        $search = self::Q();
        $offset_limit = $offset + $size;
        $total_offset = $offset_limit > 1000 ? 1000 : $offset_limit;
        $search->SetLimits($offset, $size, $total_offset, 1000);
        $search->SetMatchMode(SPH_MATCH_EXTENDED2);
        $search->SetRankingMode(SPH_RANK_SPH04);
        $search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC");
        $search->SetFieldWeights(array('title' => 1));
        foreach ($conditions as $k => $v) {
            if (is_array($v) && (isset($v['filter']) && isset(self::$sphinx_filter_map[$v['filter']]))) {
                $filter_method = self::$sphinx_filter_map[$v['filter']];
                $param_arr = $v['args'];
                call_user_func_array(array($search, $filter_method), $param_arr);
            }
        }
        if ($explainflag === 1) {
            $search->SetMatchMode(SPH_MATCH_EXTENDED2);
            $keyword = self::explainWords($keyword, $explain_ext_config);
        }
        $words = isset($keyword['kw']) ? $keyword['kw'] : $keyword;
        $ret = $search->query($words, $indexer);
        $ret['explain_words'] = isset($keyword['reg_words']) ? $keyword['reg_words'] : $keyword;
        $ret['kw'] = $words;
        if (!empty($ret['matches'])) {
            $ret['total'] = $ret['total_found'] > 1000 ? 1000 : $ret['total_found'];
        }
        return $ret;
    }

    /**
     * 获取分词结果
     * @param string $words
     * @param array $explain_ext_config
     * @return string
     */
    public static function explainWords($words, $explain_ext_config = array()) {
        $explain_result = self::scws($words);
        $explode_wds = array();
        $reg_explode_wds = array();

        $reg_explode_wds[] = sprintf('(%s)', $words);
        $sort_explain_result = self::sortExplainWords($explain_result, $words);
        $o_keywords = array();
        $ext_keywords = array();
        foreach ($sort_explain_result as $k => $v) {
            $reg_explode_wds[] = sprintf('(%s)', $v);
            $o_keywords[] = sprintf('%s', $v);
            $ext_keywords[] = sprintf('("%s"/%d)', $v, mb_strlen($v) / 3);
        }
        $ret = implode('', $o_keywords);

        $explode_wds[] = sprintf('("%s"/%d)', $words, mb_strlen($words) / 3); // sprintf('(^%s)', $words);
        $explode_wds[] = sprintf('("%s"/%d)', $ret, mb_strlen($ret) / 3);

        if (!empty($explain_ext_config)) {
            if ($explain_ext_config['is_ext_words'] === 1) {
                $ext_words = implode('|', $ext_keywords);
                $explode_wds[] = $ext_words;
            }
        }

        $search_words = implode('|', $explode_wds);
        $reg_search_words = implode('|', $reg_explode_wds);
//        var_dump($search_words);exit;
        return array('kw' => $search_words, 'reg_words' => $reg_search_words);
    }

    private static function mb_str_split($str) {
        return preg_split('/(?<!^)(?!$)/u', $str);
    }

    private static function sortExplainWords($explain_result, $source_words) {
        $explain_wds = array();
        foreach ($explain_result as $k => $v) {
            $pos = stripos($source_words, $v['word']);
            $explain_wds[$pos] = $v['word'];
        }
        ksort($explain_wds);
        $explain_sort_words = array_values($explain_wds);
        return $explain_sort_words;
    }

    /**
     * 利用scws 分词
     * @param string $words
     * @return array
     */
    public static function scws($words) {
        $so = scws_new();
        $so->set_charset('utf-8');
        //默认词库  
        $so->set_dict(ini_get('scws.default.fpath') . '/dict.utf8.xdb');

//        $extra_path = ini_get('scws.default.fpath') . '/dict_extra.txt';
//        if(file_exists($extra_path)){
//            $so->add_dict($extra_path,SCWS_XDICT_TXT);
//        }
        //默认规则 
        $so->set_rule(ini_get('scws.default.fpath') . '/rules.utf8.ini');

        //设定分词返回结果时是否去除一些特殊的标点符号 
        $so->set_ignore(true);

        //设定分词返回结果时是否复式分割，如“中国人”返回“中国＋人＋中国人”三个词。 
        // 按位异或的 1 | 2 | 4 | 8 分别表示: 短词 | 二元 | 主要单字 | 所有单字 
        //1,2,4,8 分别对应常量 SCWS_MULTI_SHORT? SCWS_MULTI_DUALITY SCWS_MULTI_ZMAIN SCWS_MULTI_ZALL 
        $so->set_multi(7);

        //设定是否将闲散文字自动以二字分词法聚合 
        $so->set_duality(false);

        // 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
        $so->send_text($words);

        //按照词的属性取前2个短词
//        $result = $so->get_tops(10, "XB,XZ,XS,nt,nz,an,ng,vn,i,j,n,v");
        $result = $so->get_tops(10, "XB,XZ,XS,nt,nz,an,ng,vn,i,j,n");
        $so->close();
        return $result;
    }

}
