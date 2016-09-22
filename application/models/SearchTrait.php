<?php

/**
 * 
 * 搜索索引名称
 * index_9939_com_v2_keywords   
 * index_9939_com_v2_keywords_all
 * index_9939_com_v2_art    tmp_source_id=1 代表咨询文章
 * index_9939_com_dzjb_art  tmp_source_id=2 代表疾病文章
 * index_9939_com_dzjb_disease
 * index_9939_com_jb_art //新疾病库 疾病文章  tmp_source_id=2 代表疾病文章
 * index_9939_com_jb_disease //新疾病库 疾病 tmp_source_id =1 代表疾病
 * index_9939_com_jb_symptom //新疾病库 症状 tmp_source_id =2 代表症状
 * index_wd_ask
 * index_wd_ask_history_1
 * index_wd_ask_history_2
 * index_wd_ask_history_3
 * index_wd_ask_history_4
 * index_wd_ask_history_5
 * index_wd_ask_history_6
 * 
 */
class SearchTrait {
    

    /**
     * 
     * @param type $sphinx_data
     * @return type
     */
    public static function search_words_all_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $arr_ids = array();
            $wd_obj = new KeyWords();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $arr_ids[] = $k;
            }
            $ret_list = $wd_obj->List_ByIds($arr_ids);
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * 查询疾病文章
     */

    public static function search_dzjb_article_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $art_obj = new Article();
            $arr_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $arr_ids[] = $k;
            }
            $disease_list = $art_obj->List_DiseaseArticleByIds($arr_ids);
            if ($disease_list) {
                foreach ($disease_list as $k => $v) {
                    $v['tmp_source_id'] = 2;
                    $date_path = date('Y/md',$v['inputtime']);
                    $article_path = sprintf("%s/%s/%d.shtml",'article',$date_path,$v['id']);
                    $v['url'] = sprintf('%s/%s', Yii::getAlias('@jb_domain'), $article_path);
                    $v['wap_url']=sprintf('%s/%s', Yii::getAlias('@mjb_domain'), $article_path);
                    $ret_list[] = $v;
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $ret['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * 查询疾病文章
     */

    public static function search_jb_article_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $art_obj =  new Article();
            $arr_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $arr_ids[] = $k;
            }
            $disease_list = $art_obj->List_DiseaseArticleByIds($arr_ids);
            if ($disease_list) {
                foreach ($disease_list as $k => $v) {
                    $v['tmp_source_id'] = 2;
                    $date_path = date('Y/md',$v['inputtime']);
                    $article_path = sprintf("%s/%s/%d.shtml",'article',$date_path,$v['id']);
                    $v['url'] = sprintf('%s/%s', Yii::getAlias('@jb_domain'), $article_path);
                    $v['wap_url']=sprintf('%s/%s', Yii::getAlias('@mjb_domain'), $article_path);
                    $ret_list[] = $v;
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * 查询资讯文章
     */

    public static function search_zx_article_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $art_obj = new Article();
            $arr_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $arr_ids[] = $k;
            }
            $disease_list = $art_obj->List_ArticleByIds($arr_ids);
            if ($disease_list) {
                foreach ($disease_list as $k => $v) {
                    $v['tmp_source_id'] = 1;
                    $v['url'] = $v['url'];
                    $article_path = sprintf("%s/%d.shtml",'article',$v['id']);
                    $v['wap_url']=sprintf('%s/%s', Yii::getAlias('@wap'), $article_path);
                    $ret_list[] = $v;
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }
    
    public static function search_article_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $art_obj = new Article();
            $zx_art_ids = array();
            $jb_art_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $attr = $v['attrs'];
                if ($attr['tmp_source_id'] == 1) {
                    $zx_art_ids[] = $k;
                } else {
                    $jb_art_ids[] = $k;
                }
            }
            if (count($zx_art_ids) > 0) {
                $disease_list = $art_obj->List_ArticleByIds($zx_art_ids);
                if ($disease_list) {
                    foreach ($disease_list as $k => $v) {
                        $v['tmp_source_id'] = 1;
                        $v['url'] = $v['url'];
                        $article_path = sprintf("%s/%d.shtml",'article',$v['id']);
                        $v['wap_url']=sprintf('%s/%s', Yii::getAlias('@wap'), $article_path);
                        $ret_list[] = $v;
                    }
                }
            }
            if (count($jb_art_ids) > 0) {
                $diseaseArticle = new Article();
                $disease_list = $diseaseArticle->List_DiseaseArticleByIds($jb_art_ids);
                if ($disease_list) {
                    foreach ($disease_list as $k => $v) {
                        $v['tmp_source_id'] = 2;
                        $date_path = date('Y/md',$v['inputtime']);
                        $article_path = sprintf("%s/%s/%d.shtml",'article',$date_path,$v['id']);
                        $v['url'] = sprintf('%s/%s', Yii::getAlias('@jb_domain'), $article_path);
                        $v['wap_url']=sprintf('%s/%s', Yii::getAlias('@mjb_domain'), $article_path);
                        $ret_list[] = $v;
                    }
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /*
     * 查询词相关问答
     */

    public static function search_ask_data($sphinx_data) {
        $total = 0;
        $ask_res_list = array();
        if (!empty($sphinx_data['matches'])) {
            $ret_list = array();
            $ask_obj = new Ask();
            $answer_obj = new Answer();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $r = $ask_obj->list_one($k);
                if (count($r) > 0) {
                    $r['cntime'] = self::formatAskTime($r['ctime']);
                    $ret_list[] = $r;
                }
            }
            if (!empty($ret_list)) {
                foreach ($ret_list as $key => $value) {
                    $ask_res_list[$key]['ask'] = $value;
                    $answer_list = $answer_obj->getbyaskid($value['id']);
                    $len = count($answer_list);
                    if ($len > 0) {
                        $ask_res_list[$key]['answer'] = $answer_list[0];
                    }
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ask_res_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /**
     * 
     * @param type $sphinx_data sphinx查询结果
     * @return type
     */
    public static function search_disease_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $arr_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $arr_ids[] = $k;
            }
            $disease_list = Disease::List_ByIds($arr_ids);
            if ($disease_list) {
                foreach ($disease_list as $k => $v) {
                    $v['tmp_source_id'] = 1;
                    $v['source_flag'] = 1;
                    $v['url'] = sprintf('%s/%s/', Yii::getAlias('@jb_domain'), $v['pinyin_initial']);
                    $v['wap_url'] = sprintf('%s/%s/', Yii::getAlias('@mjb_domain'), $v['pinyin_initial']);
                    $ret_list[] = $v;
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }

    /**
     * 根据关键词查相关症状
     * @param type $wd
     * @param type $offset
     * @param type $size
     * @param type $explainflag  1:采用第三方分词 0:不采用第三方分词
     * @param array $conditions
     * * $conditions = array(
			'column_id' => array(1)
		);
     * @return array
     */
    public static function search_symptom_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $arr_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $arr_ids[] = $k;
            }
            $symptom_list = Symptom::List_ByIds($arr_ids);
            if ($symptom_list) {
                foreach ($symptom_list as $k => $v) {
                    $v['tmp_source_id'] = 2;
                    $v['source_flag'] = 2;
                    $v['url'] = sprintf('%s/zhengzhuang/%s/', Yii::getAlias('@jb_domain'), $v['pinyin_initial']);
                    $v['wap_url'] = sprintf('%s/zhengzhuang/%s/', Yii::getAlias('@mjb_domain'), $v['pinyin_initial']);
                    $ret_list[] = $v;
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }
    
    /**
     * 
     * @param type $wd
     * @param type $offset
     * @param type $size
     * @param type $explainflag
     * @param array $conditions
     * @return type
     */
    public static function search_disease_symptom_merge_data($sphinx_data) {
        $total = 0;
        $ret_list = array();
        if (!empty($sphinx_data['matches'])) {
            $arr_ids = array();
            foreach ($sphinx_data['matches'] as $k => $v) {
                $attr = $v['attrs'];
                if ($attr['tmp_source_id'] == 1) {
                    $arr_ids[] = sprintf("'%s%d'",'disease',$k) ;
                } else {
                    $arr_ids[] = sprintf("'%s%d'",'symptom',$k) ;
                }
            }
            $symptom_list = DiseaseSymptomMerge::List_ByIds($arr_ids);
            if ($symptom_list) {
                foreach ($symptom_list as $k => $v) {
                    $v['tmp_source_id'] = $v['source_flag'] ;
                    if($v['source_flag']==1){
                        $v['url'] = sprintf('%s/%s/', Yii::getAlias('@jb_domain'), $v['pinyin_initial']);
                        $v['wap_url'] = sprintf('%s/%s/', Yii::getAlias('@mjb_domain'), $v['pinyin_initial']);
                    }else{
                       $v['url'] = sprintf('%s/zhengzhuang/%s/', Yii::getAlias('@jb_domain'), $v['pinyin_initial']); 
                       $v['wap_url'] = sprintf('%s/zhengzhuang/%s/', Yii::getAlias('@mjb_domain'), $v['pinyin_initial']);
                       
                    }
                    $ret_list[] = $v;
                }
            }
            $total = $sphinx_data['total'];
        }
        $explain_words = $sphinx_data['explain_words'];
        return array('list' => $ret_list, 'total' => $total, 'explain_words' => $explain_words);
    }
    
    private static function formatAskTime($ctime) {
        $max_diff = 24 * 60 * 60;
        $one_hour_diff = 1 * 60 * 60;
//        $one_minu_diff = 60*60;
        $curr_time = time();
        $diff_time = $curr_time - $ctime;
        if ($diff_time > $max_diff) {
            return date('Y-m-d', $ctime);
        } else if ($diff_time > $one_hour_diff) {
            return sprintf('%d小时前', ceil($diff_time / 3600));
        } else {
            return sprintf('%d分钟前', ceil($diff_time / 60));
        }
    }

}
