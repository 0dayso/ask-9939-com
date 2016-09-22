<?php

/**
 * ==============================================
 * @Desc :  列表控制器
 * ==============================================
 */
class DiseaseController extends Zend_Controller_Action {

    private $sTime = '';
    private static $arr_ask_status = array(
        '3' => '全部问题',
        '0' => '待解决问题',
        '1' => '已解决问题',
        '2' => '悬赏问题',
        '4' => '零回复问题',
        '5' => '已回复问题',
    );

    public function init() {
        $this->mobile_redirect();
        Zend_Loader::loadClass('Ask', MODELS_PATH);
        Zend_Loader::loadClass('Disease', MODELS_PATH);
        Zend_Loader::loadClass('Member', MODELS_PATH);
        Zend_Loader::loadClass('Keshi', MODELS_PATH);
//        Zend_Loader::loadClass('Html',MODELS_PATH);
        Zend_Loader::loadClass('Create', MODELS_PATH);
        Zend_Loader::loadClass('Hotwords', MODELS_PATH);
        Zend_Loader::loadClass('Diseasekeywords', MODELS_PATH);
        $this->AskObj = new Ask();
        $this->DB_list = new Disease();
        $this->Member_obj = new Member();
        $this->keshi_obj = new Keshi();
        $this->CreateObj = new Create();
        $this->HotwordsObj = new Hotwords();
        $this->DisKeywordsObj = new Diseasekeywords();
        $this->viewObj = Zend_Registry::get("view");
        $this->tmp_cookie_array = $this->Member_obj->getCookie();
    }

    private function mobile_redirect() {
        header('Cache-Control:no-cache,no-store,max-age=0,must-revalidate');
        header("Pragma: no-cache");
        $action_name = strtolower($this->getRequest()->getActionName());
        $flag = QLib_Utils_Function::ismobile();
        $redirect_url = "http://wapask.9939.com";
        switch ($action_name) {
            case "index": {
                    $sclassid = $this->_getParam('classid', 0);
                    $redirect_url = "http://wapask.9939.com/disease/{$sclassid}.html";
                    break;
                }
        }
        if ($flag === true) {
            header("Location:$redirect_url", true, 302);
            exit;
        }
    }

    public function error() {
        header('HTTP/1.1 ' . '404');
        echo $this->viewObj->render('404.phtml');
        exit;
    }

    public function indexAction() {
//        ini_set('display_errors',1);
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
//set_time_limit(0);
//        exit("维护中！"); //2010-09-10
        try {
            if ($this->getRequest()->getParam('classid') !== null) {
                $sclassid = $this->getRequest()->getParam('classid');
            } else {
                $sclassid = 0;
            }
            if ($sclassid == 0) {
                $this->error();
                exit;
            }
            $sclassid_cache = 'dis_' . $sclassid;
            $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($sclassid_cache), 1);
            if ($CATEGORY[$sclassid_cache]['class_level3'] == 0) {
                $this->error();
                exit;
            }

            $page = (int) $this->getRequest()->getParam('page');
            $page = $page <= 0 ? 1 : $page;
            $status = $this->getRequest()->getParam('status', 5);

            $minid = $this->getRequest()->getParam('minid');
            $minid = $minid == NULL ? 0 : $minid;

            if ($sclassid != 0) {
                if (!$CATEGORY[$sclassid_cache]) {
                    $this->error();
                    exit;
                }
                $aDetail = $CATEGORY[$sclassid_cache];
                $pDetail = $CATEGORY[$aDetail['pID']];
            }

            //科室部分

            $this->viewObj->ks_cache = $CATEGORY;
            $this->viewObj->info = array();
            $this->viewObj->info['class_isid'] = $this->viewObj->ks_cache[$sclassid]['class_level1'] . ' ' . $this->viewObj->ks_cache[$sclassid]['class_level2'];
            $this->viewObj->order = $this->keshi_obj->a_department($sclassid_cache);
            $fpage = 1;
            $where = "classid=" . $sclassid . ' and examine = 1 and answernum>0';
            $sql = "select count(1) as count from wd_ask as a where $where";
            $db = $this->DB_list->getDefaultAdapter();
            $result = $db->fetchAll($sql);
            $total_pages = $result[0]['count'];

            $aAsk_list = $this->pagesnew($page, $sclassid, $status, '', $total_pages, $minid, $fpage);
            $rs[$k] = $aAsk_list;
            
            $paging_obj = new QLib_Paging();
            $pagetemplate = LIBRARY_PATH . "/QLib/View/Templet/Paging/disease.phtml";
            $paging_obj = $paging_obj
                    ->setTemplate($pagetemplate)
                    ->setSize(8)
                    ->setPageSetSize(7)
                    ->setTotal($total_pages)
                    ->setUrlFormat('/disease/' . $sclassid . '-%d' . '/')
                    ->setCurrent($page);

            $this->viewObj->totalRecord = $this->DB_list->GetCount($sclassid, ' examine = 1 and answernum>0');
            $this->viewObj->rs = $rs;
            $this->viewObj->classid = $sclassid_cache;
            $this->viewObj->detail = $aDetail;
            $this->viewObj->pdetail = $pDetail;
            $this->viewObj->pageUrl = $paging_obj;


            try {
                $common_config = Zend_Registry::get("common_config");
            } catch (Exception $e) {
                
            }

            //title,description,keywords
            $cn_status = isset(self::$arr_ask_status[$status]) ? self::$arr_ask_status[$status] : '';

            $name = trim($aDetail['name']);
            $tail = ($page == 1) ? '' : "({$page})";
            $this->viewObj->title = $name . "_病因_症状_治疗方法_医生在线咨询" . $tail . "_久久问医";
            $this->viewObj->keywords = "{$name},{$name}的病因,{$name}的症状,{$name}的治疗方法";
            $this->viewObj->description = "久久问医医生在线解答{$name}相关问题,如{$name}的病因,{$name}的症状表现,{$name}的治疗方法及预防保健.解决{$name}方面的常见问题就上久久问医";

            $this->viewObj->ksname = $curr_ks_name;
            //右侧相关疾病文章 licheng 2015-11-25 start
            $jb_title = QLib_Utils_String::cutString($aDetail['name'], 6, '...');
            $this->viewObj->jb_title = $jb_title;


            //局部缓存
            $filename = 'disease_jb_' . $sclassid;
            $jb_art_lists = QLib_Cache_Client::getCache('pages/part/disease', $filename);
            if (!$jb_art_lists) {
                $jb_art_lists = Search::search_relarticle($aDetail['name'], 0, 9);
                QLib_Cache_Client::setCache('pages/part/disease', $filename, $jb_art_lists, 24);
            }
            $this->viewObj->rel_article = $jb_art_lists['list'];
            //右侧相关疾病文章 licheng 2015-11-25 end

            $listwords = $this->DisKeywordsObj->getListByDisease($sclassid);
            if ($listwords) {
                $this->viewObj->listwords = $listwords;
            }

            //底部热词 licheng 2015-11-25 start
            $searchurl = 'http://ask.9939.com/hot/';
            $letterurl = 'http://ask.9939.com/hot/';
            $letter_list = 'abcdefghijklmnopqrstuvwxyz';
            $len = strlen($letter_list);
            $return_list = array();
            for ($i = 0; $i < $len; $i++) {
                $l = strtoupper($letter_list{$i});
                $return_list[$l] = array(
                    'url' => sprintf('%s%s/', $letterurl, $l),
                    'selected' => ($this->viewObj->letter == $l) ? 1 : 0
                );
            }
            $this->viewObj->letter = 'A'; //默认当前字母
            $this->viewObj->letter_list = $return_list;
            $this->viewObj->searchurl = $searchurl;
            //随机关键词 局部缓存
            $filename = 'disease_letter_' . $sclassid;
            $randwords = QLib_Cache_Client::getCache('pages/part/disease_letter', $filename);
            if (!$randwords) {
                $randwords = $this->HotwordsObj->rand_words();
                QLib_Cache_Client::setCache('pages/part/disease_letter', $filename, $randwords, 24);
            }
            $this->viewObj->randWords = $randwords; // $zimuArr;//底部字母关键词
            //底部常见疾病
            $hotDepPart = new HotDepPart();
            $this->viewObj->hotDepPart = $hotDepPart->getCommonDisDep(7);

            //底部热词 end

            $this->viewObj->status = $status;

            //右侧名医推荐 lc@2016-6-15
            $famousDoctors = SiteHelper::getRecommendDoc($this->viewObj->order);
            $this->viewObj->famousDoctors = $famousDoctors;

            //医院推荐
            $recommendHospital = SiteHelper::getRecommendHospital($this->viewObj->order);
            $this->viewObj->recommendHospital = $recommendHospital;

            echo $this->viewObj->render("disease_list.phtml");
        } catch (exception $e) {
            echo $e->getMessage();
        }
    }

    public function topAction() {
//        ini_set('display_errors', 1);
//        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
//        set_time_limit(0);
//        exit("维护中！"); //2010-09-10
        try {
            $uri = $_SERVER['REQUEST_URI'];
            if ($uri[strlen($uri) - 1] != '/') {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/';
                $this->_redirect($url);
                exit;
            }

            if ($this->getRequest()->getParam('classid') !== null) {
                $pinyin = $this->getRequest()->getParam('classid');
            } else {
                $this->error();
            }
            $keywords = $this->DisKeywordsObj->getKeywordsByInitial($pinyin);
            if (!$keywords) {
                $this->error();
            }
            $sclassid = $keywords['disease_id'];
            $classid_cache = 'dis_' . $sclassid;
            $curword = $keywords['name'];
            $listwords = $this->DisKeywordsObj->getListByDisease($sclassid);
            /* 防止重复提交 */
            $aToken = new Zend_Session_Namespace('token');
            $aToken->unlock();
            $sToken = md5(time() . $sclassid);
            $aToken->token = $sToken;
            $aToken->lock();
            $this->viewObj->token = $sToken;
            /* 防止重复提交 */
            $sessionask = substr(md5('ask'), 5, 5) . time() . substr(md5('9939.com'), 10, 5);
            setcookie('ask_wktime', $sessionask, time() + 600, '/', APP_DOMAIN);

            $page = (int) $this->getRequest()->getParam('page');
            $page = $page <= 0 ? 1 : $page;
            $frompage = (int) $this->getRequest()->getParam('fpage');
            $frompage = $frompage <= 0 ? 1 : $frompage;
            $src = $this->getRequest()->getParam('src');
            $status = $this->getRequest()->getParam('status', 5);

            $order = $this->getRequest()->getParam('order');
            $s = $this->getRequest()->getParam('s');
            $total_pages = $this->getRequest()->getParam('total_pages');

            $minid = $this->getRequest()->getParam('minid');
            $minid = $minid == NULL ? 0 : $minid;

            $CATEGORY = array();
            if ($sclassid != 0) {
                $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($classid_cache), 1);
                if (!$CATEGORY[$classid_cache]) {
                    $this->error();
//                    $this->_redirect('http://ask.9939.com/404.html');
                    exit;
                }
                $aDetail = $CATEGORY[$classid_cache];
                $pDetail = $CATEGORY[$aDetail['pID']];
                //$aKeshi = $this->DB_list->getKeshi($sclassid);
            }

            //科室部分

            $this->viewObj->ks_cache = $CATEGORY;
            $this->viewObj->info = array();
            $this->viewObj->order = $this->keshi_obj->a_department($sclassid_cache);
            $fpage = 1;
            $where = "classid=" . $sclassid . ' and examine = 1 and answernum>0';
            $sql = "select count(1) as count from wd_ask as a where $where";
            $db = $this->DB_list->getDefaultAdapter();
            $result = $db->fetchAll($sql);
            $total_pages = $result[0]['count'];
            $aAsk_list = $this->pagesnewtop($page, $sclassid, $status, $keywords, $total_pages, $minid, $fpage);
            $rs[$k] = $aAsk_list;

//            $this->viewObj->totalRecord = $this->DB_list->GetCount($sclassid, ' examine = 1 and answernum>0');

            $this->viewObj->curword = $curword;
            $this->viewObj->listwords = $listwords;
            $this->viewObj->rs = $rs;
            $this->viewObj->classid = $classid_cache;
//            print_r($this->viewObj->classid);
//            exit;
            $this->viewObj->detail = $aDetail;
            $this->viewObj->pdetail = $pDetail;


            try {
                $common_config = Zend_Registry::get("common_config");
            } catch (Exception $e) {
                
            }

            //title,description,keywords
            $cn_status = isset(self::$arr_ask_status[$status]) ? self::$arr_ask_status[$status] : '';

            $arr_title = array();
            $curr_ks_name = empty($aDetail['name']) ? "各类" : $aDetail['name'];
            $title = $curr_ks_name == '各类' ? "" : $curr_ks_name;
            if (!empty($title)) {
                array_push($arr_title, $title);
            }
            array_push($arr_title, '疾病列表');
            array_push($arr_title, '久久问医');
            if (isset($common_config['title'])) {
//                array_push($arr_title, $common_config['title']);
            }
            $this->viewObj->title = $curword . '_久久问医';
            $this->viewObj->keywords = $curword;
            $this->viewObj->description = '久久问医为您提供关于' . $curword . '方面的相关问题及关于' . $curword . '的在线医生解答.';

            $this->viewObj->ksname = $curr_ks_name;
            //右侧相关疾病文章 licheng 2015-11-25 start
            $jb_title = QLib_Utils_String::cutString($aDetail['name'], 6, '...');
            $this->viewObj->jb_title = $jb_title;


            //局部缓存
            $filename = 'disease_jb_' . $sclassid;
            $jb_art_lists = QLib_Cache_Client::getCache('pages/part/disease', $filename);
            if (!$jb_art_lists) {
                $jb_art_lists = Search::search_relarticle($aDetail['name'], 0, 9);
                QLib_Cache_Client::setCache('pages/part/disease', $filename, $jb_art_lists, 24);
            }
            $this->viewObj->rel_article = $jb_art_lists['list'];
            //右侧相关疾病文章 licheng 2015-11-25 end
            //底部热词 licheng 2015-11-25 start
            $searchurl = 'http://ask.9939.com/hot/';
            $letterurl = 'http://ask.9939.com/hot/';
            $letter_list = 'abcdefghijklmnopqrstuvwxyz';
            $len = strlen($letter_list);
            $return_list = array();
            for ($i = 0; $i < $len; $i++) {
                $l = strtoupper($letter_list{$i});
                $return_list[$l] = array(
                    'url' => sprintf('%s%s/', $letterurl, $l),
                    'selected' => ($this->viewObj->letter == $l) ? 1 : 0
                );
            }
            $this->viewObj->letter = 'A'; //默认当前字母
            $this->viewObj->letter_list = $return_list;
            $this->viewObj->searchurl = $searchurl;
            //随机关键词 局部缓存
            $filename = 'disease_letter_' . $sclassid;
            $randwords = QLib_Cache_Client::getCache('pages/part/disease_letter', $filename);
            if (!$randwords) {
                $randwords = $this->HotwordsObj->rand_words();
                QLib_Cache_Client::setCache('pages/part/disease_letter', $filename, $randwords, 24);
            }
            $this->viewObj->randWords = $randwords; // $zimuArr;//底部字母关键词
            //底部常见疾病
            $hotDepPart = new HotDepPart();
            $this->viewObj->hotDepPart = $hotDepPart->getCommonDisDep(7);

            //底部热词 end

            $this->viewObj->status = $status;

            
            //右侧名医推荐 lc@2016-6-15
            $famousDoctors = SiteHelper::getRecommendDoc($this->viewObj->order);
            $this->viewObj->famousDoctors = $famousDoctors;

            //医院推荐
            $recommendHospital = SiteHelper::getRecommendHospital($this->viewObj->order);
            $this->viewObj->recommendHospital = $recommendHospital;

            echo $this->viewObj->render("disease_top.phtml");
        } catch (exception $e) {
            echo $e->getMessage();
        }
    }

    //列表页问题列表部分生成静态文件
    public function asklistAction($data, $ptotal = 0, $page = 1, $status = '', $classid) {
        //生成列表静态列表start
        $pathtmp = 'liststatic/l_' . $classid . '/s_' . $status;
        $patharr = split('/', $pathtmp);
        $listhtml_path = '';
        foreach ($patharr as $v) {
            $listhtml_path .= '/' . $v;
            if (is_dir(APP_DATA_PATH . $listhtml_path)) {
                continue;
            } else {
                @mkdir(APP_DATA_PATH . $listhtml_path, 0777);
            }
        }
        $listhtml_name = APP_DATA_PATH . $listhtml_path . '/p_' . ($ptotal - $page + 1) . '.php';
        if ($page > 1 && !file_exists($listhtml_name)) {
            $listcontent = "<?php\n " . '$PageData = ' . var_export($data, TRUE) . ";\n?>";
            @file_put_contents($listhtml_name, $listcontent);
            @chmod($listhtml_name, 0777);
            unset($listcontent);
        }
        //生成列表静态列表end
    }

    private function getasklistAction($ptotal = 0, $page = 1, $status = '', $classid = 0) {
        if ($ptotal <= 0) {
            return '';
        }
        $page = $page < 1 ? 1 : $page;
        $filepath = APP_DATA_PATH . '/liststatic/l_' . $classid . '/s_' . $status . '/p_' . ($ptotal - $page + 1) . '.php';
        if (file_exists($filepath)) {
            include($filepath);
            return $PageData;
        } else {
            return array();
        }
    }

    private function pagesnew($page = 1, $sclassid = 0, $status = 5, $path = '', $total_pages = '', $minid = 0, $fpage = 1) {
        $order = 'id DESC';
        $count = 8; //每页条数
        $tmp_page_var = 'page'; //分页变量
        $tmp_page_now = $this->_getParam($tmp_page_var); //当前页码

        $this->viewObj->status = $status;
        if ($total_pages) {
            $num = $total_pages;
            $first_page = 1;
        } else {
            $num = 0;
            $cacheName = 'ask_disease_' . $sclassid . '_' . $status;
            $data = QLib_Cache_Client::getCache('ask_disease', $cacheName);
            if ($data) {
                $num = $data;
            }
        }
        $this->viewObj->aAsk = array();
//        echo $ptotal;exit;
        if ($page > 1 && $num) {
            $ptotal = ceil($num / $count);
            $start = $tmp_page_now * $count;
            //前100页的数据，从数据库读取
            if ($start > 100 * $count) {
                $this->viewObj->aAsk = $this->getasklistAction($ptotal, $tmp_page_now, $status, $sclassid);
            }

            if (count($this->viewObj->aAsk) > 0) {
                $minid = $this->viewObj->aAsk[count($this->viewObj->aAsk) - 1]['id'];

            }
        }
//        var_dump($this->_my_params);
//        exit;
        $this->_my_params = array();
        if (count($this->viewObj->aAsk) == 0) {
            $w = '';
            $index = "";
            $CATEGORY = array();
            if ($sclassid != 0) {
                $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($sclassid), 1);
                $this->viewObj->sclassid = $sclassid;
                $index = 'FORCE INDEX(';
                $category = $CATEGORY[$sclassid];
                if ($category['class_level1']) {
                    $w = " class_level1={$category['class_level1']} ";
                    $index .= "class_level1";
                    if ($category['class_level2']) {
                        $w .= " AND class_level2={$category['class_level2']}";
                        $index .= "_level2";
                        if ($category['class_level3']) {
                            $w .= " AND class_level3={$category['class_level3']}";
                            $index .= "_level3";
                        }
                    }
                    $index .= '_id)';
                } else {
                    $index .= 'classid)';
                    $w = " classid={$sclassid} ";
                }
            }
            $w = empty($w) ? 1 : $w;
            $where = "";
            if ($w != 1) {
                $where = $w;
            }
            $wherearr = array();
            if ($status < 2) { // 小于2的时候表示解决状态
                $wherearr[] = " status=$status ";
            } elseif ($status == 2) { // 悬赏问题
                $wherearr[] = " point > 0  ";
            } elseif ($status == 4) { // 0回复问题
                $wherearr[] = "  answernum = 0   ";
            } elseif ($status == 5) { // 有回复问题
                $wherearr[] = " examine=1 and  answernum>0  ";
            }

            if (count($wherearr) > 0) {
                $str_where_arr = implode(' and ', $wherearr);
                $where.=' and ' . $str_where_arr;
            }

            $subpage = $page - $fpage;
            $start = 0;
            $start = $page >= 1 ? ($page - 1) * $count : 0;

            $this->viewObj->aAsk = $this->DB_list->List_Ask($where, $order, $count, $start, $index);

            foreach ($this->viewObj->aAsk as $k => $v) {
//                print_r($v);exit;
                $this->viewObj->aAsk[$k]['classname'] = $CATEGORY[$v['classid']]['name'];
                $this->viewObj->aAsk[$k]['statustxt'] = $v['status'] == 5 ? '已解决' : '未解决';
                $userInfo = $this->Member_obj->getInfo($v[userid]); //取用户详细信息
                $this->viewObj->aAsk[$k]['ctime'] = date("Y-m-d H:i:s", $v['ctime']);
                $this->viewObj->aAsk[$k]['hiddenname'] = $v['hiddenname'];
                $this->viewObj->aAsk[$k]['username'] = $userInfo['username'];
                $this->viewObj->aAsk[$k]['endtime'] = $this->DB_list->getvalidity($v['ctime'], $v['term']);
                $minid = $v['id'];
            }
           
            $total_where = '';
            if ($where != 1) {
                $total_where = $where;
            }
            $num = $this->DB_list->numRows($total_where);
            
            if ($page > 1) {
                $back = $this->asklistAction($this->viewObj->aAsk, $ptotal, $tmp_page_now, $status, $sclassid);
            } else {
                $total_pages = $this->DB_list->numRows($total_where);
//                $totalarr = array('content' => $total_pages, 'addtime' => time(), 'savetime' => 86400);
//                @file_put_contents(APP_ROOT . '/cache/ask_disease_' . $sclassid . '_' . $status, serialize($totalarr));
                $cacheName = 'ask_disease_' . $sclassid . '_' . $status;
                QLib_Cache_Client::setCache('ask_disease', $cacheName, $total_pages, 24);
            }
        }
        return array('aList' => $this->viewObj->aAsk, 'status' => $status, 'minid' => $minid);
    }

    private function pagesnewtop($page = 1, $sclassid = 0, $status = 5, $keywords = '', $total_pages = '', $minid = 0, $fpage = 1) {

        $pinyin_initial = $keywords['pinyin_initial'];
        $words = $keywords['name'];

//        $order = 'id DESC';
        $count = 8; //每页条数

        $tmp_page_obj = Zend_Registry::get('PageClass');
        $tmp_page_var = 'page'; //分页变量
        $tmp_page_now = $this->_getParam($tmp_page_var); //当前页码

        $tmp_page_url = '/disease/' . $pinyin_initial . '/'; //URL
        $arr_temp_page_url = array();
        array_push($arr_temp_page_url, $pinyin_initial);

        $show_type = 6;
        $this->viewObj->status = $status;

        $this->viewObj->aAsk = array();

        $this->_my_params = array();
        if (count($this->viewObj->aAsk) == 0) {
            $CATEGORY = $this->keshi_obj->getKeshifenliCache(array($sclassid), 1);
            $tmp_page_obj->setpublic($this->_my_params, $tmp_page_var, $show_type);

            $offset = $page * $count;
            $arr = array(
                array(
                    'filter' => 'filter',
                    'args' => array('classid', array($sclassid))
                )
            );
            $res = Search::search_ask($words, $offset, $count, $arr); //获取问答
            $explain_words = $res['explain_words'];
            $asks = array();
            foreach ($res['list'] as $k => $v) {
                $userInfo = $this->Member_obj->getInfo($v['ask']['userid']); //取用户详细信息 

                $v['ask']['bestAnswerDetail'] = array(
                    'bestAnswerUID' => $v['answer']['id'],
                    'bestAnswer' => $v['answer']['content'],
                    'userid' => $v['answer']['userid']
                );
                $v['ask']['nickname'] = $userInfo['nickname'] ? $userInfo['nickname'] : $userinfo['username'];
                $asks[$k] = $v['ask'];
            }
            $this->viewObj->aAsk = $asks;
            foreach ($this->viewObj->aAsk as $k => $v) {
                $this->viewObj->aAsk[$k]['classname'] = $CATEGORY[$v['classid']]['name'];
                $this->viewObj->aAsk[$k]['statustxt'] = $v['status'] == 5 ? '已解决' : '未解决';
                $userInfo = $this->Member_obj->getInfo($v['bestAnswerDetail']['userid']); //取用户详细信息 uid,username,nickname,pic
                $this->viewObj->aAsk[$k]['bestAnswerDetail']['uid'] = $userInfo['uid'];
                $this->viewObj->aAsk[$k]['bestAnswerDetail']['username'] = $userInfo['username'];
                $this->viewObj->aAsk[$k]['bestAnswerDetail']['nickname'] = $userInfo['nickname'];
                $this->viewObj->aAsk[$k]['bestAnswerDetail']['pic'] = $userInfo['pic'];
                $this->viewObj->aAsk[$k]['ctime'] = date("Y-m-d H:i:s", $v['ctime']);
                $this->viewObj->aAsk[$k]['hiddenname'] = $v['hiddenname'];
                $this->viewObj->aAsk[$k]['username'] = $userInfo['username'];
                $this->viewObj->aAsk[$k]['endtime'] = $this->DB_list->getvalidity($v['ctime'], $v['term']);
                $minid = $v['id'];
            }
//            print_r($this->viewObj->aAsk);
            $tmp_page_url = '/top/' . implode('-', $arr_temp_page_url);
            $num = $res['total'];
            $this->viewObj->totalRecord = $num;
            //$num 总数  $tmp_page_now 当前页     $tmp_page_url, $count 每页条数 $first_page, $status
            $tmp_page_obj->set($num, $tmp_page_now, $tmp_page_url, $count, $first_page, $status);
            $this->viewObj->pagehtml = $tmp_page_obj->output(1); //返回分页控制HTML
        }
        return array('pagehtml' => $this->viewObj->pagehtml, 'aList' => $this->viewObj->aAsk, 'explain_words' => $explain_words, 'status' => $status, 'minid' => $minid);
    }

    /*
     * 问答按时间查询
     * @author: kxgsy163@163.com
     * time: 2009-11-23
     */

    private function getTimeLimit() {
        $iNums = 5;
        $iMonths = 3;
        $sTime = strtotime("-15 months") > mktime(0, 0, 1, 10, 31, 2008) ? strtotime("-15 months - " .
                        (date('j') - 1) . " days") : mktime(0, 0, 1, 10, 1, 2008);
        $this->sTime = $sTime = mktime(0, 0, 1, date("m", $sTime), date("d", $sTime), date('Y', $sTime));
        $atime = array();
        for ($i = 1; $i <= $iNums; $i++) {
            $tmp['stime'] = strtotime("+" . ($i - 1) * $iMonths . " months", $sTime);
            $eTime = strtotime("+" . $i * $iMonths . " months -2 seconds", $sTime);
            $tmp['etime'] = $eTime > time() ? time() : $eTime;

            $atime[] = $tmp;
        }
        #print_r($atime);
        return $atime;
    }

    private function getLostTime($cTime = 0, $term = 0) {
        $over = '该问题已过期！';
        if (!$cTime)
            return $over;
        $term or ( $term = 10);
        $now = time();
        if (strtotime("- $term days") >= $cTime) {
            return $over;
        } else {
            $time = $cTime + $term * 24 * 3600 - $now;
            $iDay = floor($time / 3600 / 24);
            $str = '离问题结束还有 ' . $iDay . '天' . floor(($time - $iDay * 24 * 3600) / 3600) .
                    '小时';
            return $str;
        }
    }

}

?>
