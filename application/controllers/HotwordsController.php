<?php

/**
 * ##############################################
 * @FILE_NAME :HotwordsController.php
 * ##############################################
 *
 * ==============================================
 * @Desc :  搜索热词控制器
 * ==============================================
 */
class HotwordsController extends Q_Controller_Action {

    public function init() {
        $this->mobile_redirect();
        $this->view = Zend_Registry::get('view');

        $this->view->controllername = $this->getRequest()->getControllerName();
        $this->view->actionname = $this->getRequest()->getActionName();

        $this->ask_obj = new Ask();
        $this->list_ask_obj = new Listask();
        $this->answer_obj = new Answer();
        $this->wd_obj = new KeyWords();
        $this->CreateObj = new Create();
        $domainname = 'ask';
        $this->view->domainname = $domainname;
        $this->view->domainurl = 'http://' . $domainname . '.9939.com/hot/';
        $this->view->base_include_path = 'http://www.9939.com/9939/res/hot/' . $domainname;
        $this->view->searchurl = 'http://' . $domainname . '.9939.com/hot/';
        $this->view->letterurl = 'http://' . $domainname . '.9939.com/hot/';
    }

    private function mobile_redirect() {
        header('Cache-Control:no-cache,no-store,max-age=0,must-revalidate');
        header("Pragma: no-cache");
        $action_name = strtolower($this->getRequest()->getActionName());
        if (!method_exists($this, $action_name . 'Action')) {
            $this->error();
            exit;
        }
        $flag = QLib_Utils_Function::ismobile();
        $redirect_url = "http://wapask.9939.com/hot/";
        switch ($action_name) {
            case "index": {
                    $redirect_url = "http://wapask.9939.com/hot/";
                    break;
                }
            case "search": {
                    $kw = $this->_getParam('wd', '');
                    $redirect_url = "http://wapask.9939.com/hot/{$kw}/";
                    break;
                }
            case "letter": {
                    $kw = $this->_getParam('wd', '');
                    $redirect_url = "http://wapask.9939.com/hot/";
                    break;
                }
        }
        if ($flag === true) {
            header("Location:$redirect_url", true, 302);
            exit;
        }

        $uristr = $_SERVER['REQUEST_URI'];
        if ($pos = strpos($uristr, '?')) {
            $uristr = substr($uristr, 0, $pos);
        }
        if (($last_char = substr($uristr, -1) ) != '/') {
            $redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $uristr . '/';
            if ($this->_hasParam('page')) {
                $page = $this->_getParam('page', 1);
                $redirect_url.='?page=' . $page; //http_build_query($paramsArr);
            }
            header("Location:$redirect_url", true, 301);
            exit;
        }
    }

    public function indexAction() {
        $page = $this->_getParam('page', 1);
        $cache_key = sprintf('%s|%s|%s-%d', $this->view->controllername, $this->view->actionname, 'default', $page);
        $data = QLib_Cache_Client::getPageCache('pages', $cache_key, 24);
        if ($data) {
            echo $data;
            exit;
        }

        $letter_list = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($letter_list);
        $return_list = array();
        $filter_array = $this->getFilterArray();
        $cache_rand_words = KeyWords::getCacheRandWords(100, $filter_array);
        for ($i = 0; $i < $len; $i++) {
            $wd = strtoupper($letter_list{$i});
            $ret = $cache_rand_words[$wd];
            if (count($ret) > 0) {
                $return_list[$wd] = array_splice($ret, 0, 15);
            }
        }
        $this->view->list = $return_list;
        $this->loadletterlist();

        $this->view->cache_page = $page;
        /* 页面meta及标题 */
        $this->view->metaTitle = '字母检索' . '_久久问医';
        $this->view->metaKeywords = '字母检索';
        $this->view->metaDescription = '字母检索';
        echo $this->view->render('/hot/index.phtml');
    }

    public function letterAction() {
        $wd = $this->_getParam('wd', '');
        $this->view->letter = empty($wd) ? 'A' : strtoupper($wd);
        if (!in_array($this->view->letter, range("A", "Z"))) {
            $this->error();
        }
        $page = $this->_getParam('page', 1);
        $cache_key = sprintf('%s|%s|%s-%d', $this->view->controllername, $this->view->actionname, $this->view->letter, $page);
        $data = QLib_Cache_Client::getPageCache('pages', $cache_key, 24);
        if ($data) {
            echo $data;
            exit;
        }

        //每页数量
        $size = 204;
        $dis_page_num = 7;
        $paging = $this->helpPaging('pager')->setSize($size)->setPageSetSize($dis_page_num);
        $return_info = $this->getwordslist($wd, $paging->getNewCurrent(), $size);

        $this->view->total = $total = $return_info['total'];
        $this->view->paging = $paging->setTotal($total);
        $this->view->list = $return_info['list'];
        $this->loadletterlist();

        $this->view->cache_page = $page;

        /* 页面meta及标题 */
        $this->view->metaTitle = $this->view->letter . '_久久问医';
        $this->view->metaKeywords = $this->view->letter;
        $this->view->metaDescription = $this->view->letter;
        echo $this->view->render('/hot/letter.phtml');
    }

    /**
     * 问答热搜词页
     */
    public function searchAction() {
        $wd = $this->_getParam('wd', '');
        $page = $this->_getParam('page', 1);
        $cache_key = sprintf('%s|%s|%s-%d', $this->view->controllername, $this->view->actionname, $wd, $page);
        $data = QLib_Cache_Client::getPageCache('pages', $cache_key, 24);
        if ($data) {
            echo $data;
            exit;
        }
        $wd_name = '';
        if (!empty($wd)) {
            $wd_list = $this->wd_obj->getKeywordName($wd);
            if (!$wd_list || empty($wd_list)) {
                $this->error();
            }
            $wd_name = $wd_list['keywords'];
            //每页数量
            $size = 13;
            $dis_page_num = 7;
            $paging = $this->helpPaging('pager')->setSize($size)->setPageSetSize($dis_page_num);
            $return_rel_info = $this->search_article_ask($wd_name,$paging->getNewCurrent(),$size);
            
            $rel_article = $return_rel_info[0];//相关文章
            $this->view->rel_art_list = $rel_article['list'];
            
            $return_info = $return_rel_info[1];//相关问答
            $this->view->total = $total = $return_info['total'];
            $this->view->paging = $paging->setTotal($total);
            $this->view->searchList = $return_info['list'];
            $this->view->explain_words = $return_info['explain_words'];
            
           
        }
        $this->view->cache_page = $page;
        $this->view->keywords = $wd_name;
        $this->view->pinyinKeywords = $wd;
        $this->view->letter = strtoupper($wd{0});
        $this->view->adnum = '32'; //
        $this->loadletterlist();


        /* 相关热词 */
        $rel_keywords_len = 10;
        $relateWords = $this->relate_words($this->view->keywords, 0, 20);
        $this->view->relateDiseaseWords = empty($relateWords) ? array() : array_slice($relateWords['list'], 0, $rel_keywords_len);

        //随机关键词
        $randwords = $this->rand_words();
        $this->view->randWords = $randwords; // $zimuArr;//底部字母关键词

        $cache_hot_question = QLib_Cache_Client::getCache('pages/hotwords/rel', 'hotquestion');
        if ($cache_hot_question) {
            $this->view->hotQuestion = $cache_hot_question;
        } else {
            /* 右侧热门问答 */
            $hotQuestionNum = 8; //热门文章数量
            $where = ' examine=1 and answernum>=3';
            $order = ' id DESC';
            $hotQuestion = $this->list_ask_obj->List_Ask($where, $order, $hotQuestionNum, 0, ' index_examine_answernum_id');
            QLib_Cache_Client::setCache('pages/hotwords/rel', 'hotquestion', $hotQuestion, 2);
            $this->view->hotQuestion = $hotQuestion;
        }

        //热门科室、热门部位
        $hotDepPart = new HotDepPart();
        $this->view->hotDepPart = $hotDepPart->getCommonDisDep(7);

        /* 页面meta及标题 */
        $this->view->metaTitle = $wd_name . '_久久问医';

        //右侧名医推荐 lc@2016-6-15
        //科室id => 后台广告位id
        $famousDoctors = $this->CreateObj->getAds(286);
        $this->view->famousDoctors = $famousDoctors;

        $this->view->metaKeywords = "{$wd_name}咨询,{$wd_name}相关问题，{$wd_name}精彩问答";
        $this->view->metaDescription = "久久问医为您解答关于{$wd_name}相关问题，{$wd_name}精彩问答.{$wd_name}咨询就上久久问医!";
        echo $this->view->render('/hot/search.phtml');
    }
    
    //批量获取文章及问答
    private function search_article_ask($cn_key_name,$offset,$size){
        $rel_conditon = $this->rand_condition();
//        $rd_art_condition = $this->rand_article_condition();
        $explain_ext_config = array(
            'is_ext_words'=>1
        );
        $queries = array(
            array('word'=>$cn_key_name,'indexer'=>'index_9939_com_jb_art','offset'=>0,'size'=>8,'condition'=>array(),'explain_ext_config'=>$explain_ext_config),
            array('word'=>$cn_key_name,'indexer'=>'index_wd_ask,index_wd_ask_history_1,index_wd_ask_history_2,index_wd_ask_history_3,index_wd_ask_history_4,index_wd_ask_history_5,index_wd_ask_history_6,index_wd_ask_history_7','offset'=>$offset,'size'=>$size,'condition'=>$rel_conditon,'explain_ext_config'=>$explain_ext_config),
        );
        $result = array();
        $ret = QLib_Utils_SearchHelper::batchSearch($queries);
        foreach($ret as $kk=>$ret){
            $indexer_name = $ret['indexer'];
            $sphinx_result = Search::parse_search_data($ret,$indexer_name);
            $result[]=$sphinx_result;
        }
        return $result;
    }

    public function souAction() {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        set_time_limit(0);
        $cache = new Cache();
        $cache->createDisease();
    }

    public function error() {
        header('HTTP/1.1 ' . '404');
        echo $this->view->render('404.phtml');
        exit;
    }

    private function loadletterlist() {
        $letter_list = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($letter_list);
        $return_list = array();
        for ($i = 0; $i < $len; $i++) {
            $l = strtoupper($letter_list{$i});
            $return_list[$l] = array(
                'url' => sprintf('%s%s/', $this->view->letterurl, $l),
                'selected' => ($this->view->letter == $l) ? 1 : 0
            );
        }
        $this->view->letter_list = $return_list;
    }

    //随机热词
    private function rand_words() {
        $letter_list = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($letter_list);
        $return_list = array();
        $max_kw_length = 100;
        $max_dis_length = 30;
        $filter_array = $this->getFilterArray();
        $cache_rand_words = KeyWords::getCacheRandWords($max_kw_length, $filter_array);
        for ($i = 0; $i < $len; $i++) {
            $wd = strtoupper($letter_list{$i});
            $ret = $cache_rand_words[$wd];
            if (count($ret) > 0) {
                $rand_num = count($ret) > $max_dis_length ? 30 : count($ret);
                $rand_keys = array_rand($ret, $rand_num);
                if (is_array($rand_keys)) {
                    foreach ($rand_keys as $k) {
                        $return_list[$wd][] = $ret[$k];
                    }
                } else {
                    $return_list[$wd][] = $ret[0];
                }
            } else {
                $return_list[$wd] = array();
            }
        }
        return $return_list;
    }

    private function getwordslist($wd, $offset, $size) {
        $filter_array = $this->getFilterArray();
        return Search::search_words_byinitial($wd, $offset, $size, $filter_array);
    }

    public function relate_words($wd, $offset, $size) {
        $explain_ext_config = array(
            'is_ext_words'=>1
        );
        $explainflag = 1;
        $filter_array = $this->getFilterArray();
        return Search::search_words_all($wd, $offset, $size, $filter_array,$explainflag,$explain_ext_config);
    }

    private function search_relarticle($wd, $offset, $size) {
        $condition = $this->rand_article_condition();
        return Search::search_relarticle($wd, $offset, $size, $condition);
    }
    
    private function rand_article_condition(){
        //        $condition = $this->rand_condition();
        $min = 1420041600;
        $max = time();
        $condition = array(
            array(
                'filter' => 'filter_range',
                'args' => array('createtime', $min, $max)
            )
        );
        return $condition;
    }

    private function search_ask($wd, $offset, $size) {
        $condition = $this->rand_condition();
        return Search::search_ask($wd, $offset, $size, $condition);
    }

    private function getFilterArray() {
//        return array('typeid' => array(2,3));
//        return array(
//                array(
//                    'filter'=>'filter',
//                    'args'=>array('typeid',array(0,1))
//                )
//        );

        return array(
        );
    }

    //随机相关文章与相关问答的查询日期条件
    private function rand_condition() {
//        $condi_create_time=array(
//            array(0,1262188800),//0-2009.12.31
//            array(1262188800,1293724800),//2009.12.31-2010.12.31
//            array(1293724800,1419955200),//2010.12.31-2014.12.31
//            array(1419955200,1451491200),//2014.12.31-2015.12.31
//            array(1451491200,1483113600),//2015.12.31-2016.12.31
//        );
        $curr_time = time();
        $condi_create_time = array(
            array(0, 1293724800), //0-2010.12.31
            array(1293724800, 1419955200), //2010.12.31-2014.12.31
            array(1419955200, 1451491200), //2014.12.31-2015.12.31
            array(1451491200, $curr_time), //2015.12.31-2016.12.31
        );

        $len = count($condi_create_time);
        $rd = mt_rand(0, $len - 1);
        $min = $condi_create_time[$rd][0];
        $max = ($rd == $len - 1) ? time() : $condi_create_time[$rd][1];
        return array(
            array(
                'filter' => 'filter_range',
                'args' => array('createtime', $min, $max)
            )
        );
    }

}
