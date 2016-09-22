<?php

/**
 * ==============================================
 * @Desc :  列表控制器
 * ==============================================
 */
class ListController extends Zend_Controller_Action {

    private $sTime = '';
    private $_my_params = array();
    private static $arr_ask_status = array(
        '3' => '全部问题',
        '0' => '待解决问题',
        '1' => '已解决问题',
        '2' => '悬赏问题',
        '4'=>'待解决问题'//零回复问题变成待解决问题
    );
    public function init() {
        $this->mobile_redirect();
        Zend_Loader::loadClass('Ask', MODELS_PATH);
        Zend_Loader::loadClass('Listask', MODELS_PATH);
        Zend_Loader::loadClass('Member', MODELS_PATH);
        Zend_Loader::loadClass('Keshi', MODELS_PATH);
        Zend_Loader::loadClass('Create',MODELS_PATH);
        Zend_Loader::loadClass('Hotwords',MODELS_PATH);
        Zend_Loader::loadClass('Diseasekeywords', MODELS_PATH);
        $this->_my_params = $this->getRequest()->getParams();
        $this->AskObj = new Ask();
        $this->DB_list = new Listask();
        $this->Member_obj = new Member();
        $this->keshi_obj = new Keshi();
        $this->disease_obj = new Disease();
        $this->CreateObj = new Create();
        $this->HotwordsObj = new Hotwords();
        $this->DisKeywordsObj = new Diseasekeywords();
        $this->viewObj = Zend_Registry::get("view");
        $this->tmp_cookie_array = $this->Member_obj->getCookie();
    }
    
    private function mobile_redirect(){
        header('Cache-Control:no-cache,no-store,max-age=0,must-revalidate');
        header("Pragma: no-cache"); 
        $action_name = strtolower($this->getRequest()->getActionName());
        $flag =  QLib_Utils_Function::ismobile();
        $tmp_redirect_url = "http://wapask.9939.com";
        switch($action_name){
            case "index":{
                $sclassid = $this->_getParam('classid',0);
                $tmp_redirect_url = "http://wapask.9939.com/classid/{$sclassid}.html";
                break;
            }
        }
        
        if($flag===true){
            header("Location:$tmp_redirect_url", true, 302);
            exit;
        }
    }
    public function indexAction() {
        //exit("维护中！"); //2010-09-10
        try {
            //去掉参数验证 wangpuqiang@20150907
            /*
              $arrgdcs=array('classid'=>"int",'status'=>"string",'minid'=>"int",'total_pages'=>"int",'page'=>"int",'fpage'=>"int");
              $arrParam=$this->getRequest()->getParams();
              foreach($arrParam as $k=>$v){
              if($arrgdcs[$k]=="int"){
              if(!preg_match("/^[0-9]*$/",$v)){
              echo $this->viewObj->render('/error.phtml');
              exit;
              }
              }
              } */
            $sclassid = intval($this->getRequest()->getParam('classid',0));
            $aDetail = array();
            if ($sclassid != 0) {
                $aDetail = $this->keshi_obj->get_one($sclassid);
                if (!isset($aDetail)) {
                    $this->error();
                }
            }

            $page = (int) $this->getRequest()->getParam('page');
            $page = $page <= 0 ? 1 : $page;
            
            $status = $this->getRequest()->getParam('status');
            $status = ($status == '' || $status == 'all') ? 3 : $status;
            
            $arr_temp_page_url = array();
            array_push($arr_temp_page_url, $sclassid);
            array_push($arr_temp_page_url, $status == 3 ? 'all' : $status);
            $tmp_page_url = '/classid/' . implode('-', $arr_temp_page_url);
            $this->viewObj->page_base_url = $tmp_page_url;
            
            $total_pages = $this->getRequest()->getParam('total_pages',0);
            $minid = $this->getRequest()->getParam('minid',0);
            
            $cache_sclassid = ($sclassid == 0) ? array() : array($sclassid);
            $cache_ks = $this->keshi_obj->getKeshifenliCache($cache_sclassid, 1);
            $this->viewObj->ks_cache = $cache_ks;
           
            
            
            $ks_nav = $this->keshi_obj->get_keshi_nav($sclassid);
            $this->viewObj->ks_nav = $ks_nav;
            
            $aAsk_info = $this->pages($page, $sclassid, $status, '', $total_pages, $minid);
            
            
            $aAsk_list = $aAsk_info['aList'];
            
            $this->viewObj->total_pages = $aAsk_info['total_pages'];
            $this->viewObj->page_now = $aAsk_info['page_now'];
            $this->viewObj->ask_list = $aAsk_list;
            $this->viewObj->classid = $sclassid;
            //科室部分
            $class_level1 = $cache_ks[$sclassid]['class_level1'];
            
            $this->viewObj->dzjb = $this->keshi_obj->get_keshi_redis();
           
            //所属一级科室序号(推荐位置使用)
            $this->viewObj->order = $this->keshi_obj->a_department($sclassid);

            try {
                $common_config = Zend_Registry::get("common_config");
            } catch (Exception $e) {
                
            }
            
            //title,description,keywords
            $title = $keywords = $description = '';
            if (!empty($aDetail['name'])) {
                $cn_status = isset(self::$arr_ask_status[$status]) ? self::$arr_ask_status[$status] : '';
                $curr_ks_name = $aDetail['name'];
                $title = "{$curr_ks_name}疾病,常见病_{$curr_ks_name}{$cn_status}_医生在线免费咨询_第{$aAsk_info['page_now']}页_久久问医";
                $keywords = "{$curr_ks_name}疾病,{$curr_ks_name}常见病,{$curr_ks_name}医生在线";
                $description = "久久问医{$curr_ks_name}疾病问答为您提供:{$curr_ks_name}疾病,{$curr_ks_name}医生在线免费咨询等问题,关于{$curr_ks_name}的问题,就上久久问医.";
            } else {
                $title = "全部问题_问题大全_医生在线免费咨询_第{$aAsk_info['page_now']}页_久久问医";
                $keywords = "全部问题,问题大全,医生在线,免费咨询";
                $description = "久久问医为您提供各种健康疾病方面的问题,主要包括各种疾病症状、预防、保健、治疗、诊断、用药等方面的问题及医生在线免费咨询.健康问题就上久久问医.";
            }

            $this->viewObj->title = $title;
            $this->viewObj->keywords = $keywords;
            $this->viewObj->description = $description;
            
            
            //右侧相关疾病文章 licheng 2015-11-25 start
            $cur_keshi_id = $sclassid;//$this->getRequest()->getParam('classid');
            if($cur_keshi_id=="0"){
                $cur_keshi_id = "32";//未选中任何栏目指定默认栏目
            }
            $jb_title_tmp = $this->keshi_obj->get_one($cur_keshi_id);
            
            $jb_title = QLib_Utils_String::cutString($jb_title_tmp['name'], 6, '...');
            $this->viewObj->jb_title = $jb_title;
           
            //局部缓存
            $filename = 'list_jb_'.$cur_keshi_id;
            $jb_art_lists = QLib_Cache_Client::getCache('pages/part/list', $filename);
            if (!$jb_art_lists) {
                $dis_cache = $this->keshi_obj->getKeshifenliCache(array($cur_keshi_id), 1);
                shuffle($dis_cache);
                $rand_jb_name = '';
                foreach ($dis_cache as $key => $val) {
                    if ($val['is_disease'] == 1) {
                        $rand_jb_name = $val['name'];
                        break;
                    }
                }
                $jb_art_lists = Search::search_relarticle($rand_jb_name, 0, 9);
                QLib_Cache_Client::setCache('pages/part/list', $filename, $jb_art_lists, 24);
            }
            $this->viewObj->rel_article = $jb_art_lists['list'];
            //右侧相关疾病文章 licheng 2015-11-25 end
            

            //底部热词 licheng 2015-11-25 start
            $searchurl = 'http://ask.9939.com/hot/';
            $letterurl = 'http://ask.9939.com/hot/';
            $letter_list = 'abcdefghijklmnopqrstuvwxyz';
            $len = strlen($letter_list);
            $return_list = array();
            for($i=0;$i<$len;$i++){
             $l = strtoupper($letter_list{$i});
             $return_list[$l]  = array(
                                     'url'=>  sprintf('%s%s/',$letterurl,$l),
                                     'selected'=>($this->viewObj->letter==$l)?1:0
                                 );
            }
            $this->viewObj->letter = 'A';//默认当前字母
            $this->viewObj->letter_list = $return_list;
            $this->viewObj->searchurl = $searchurl;
            
            //随机关键词 局部缓存
            $filename = 'list_letter_'.$sclassid;
            $randwords = QLib_Cache_Client::getCache('pages/part/list_letter', $filename);
            if(!$randwords){
                $randwords = $this->HotwordsObj->rand_words();
                QLib_Cache_Client::setCache('pages/part/list_letter', $filename, $randwords, 24);
            }
            $this->viewObj->randWords =  $randwords;// $zimuArr;//底部字母关键词
            //底部常见疾病
            $hotDepPart = new HotDepPart();
            $this->viewObj->hotDepPart = $hotDepPart->getCommonDisDep(7);
            //底部热词 end
            
            $this->viewObj->status = $status;
            
            //右侧名医推荐 lc@2016-6-15
            $famousDoctors = SiteHelper::getRecommendDoc($this->viewObj->order);
            $this->viewObj->famousDoctors = $famousDoctors;
            
            //医院推荐
            $recommendHospital =SiteHelper::getRecommendHospital($this->viewObj->order);
            $this->viewObj->recommendHospital = $recommendHospital;
            
            $keshi = $this->keshi_obj->get_one($sclassid);
            if ($keshi['class_level3'] != 0) {
                $res = $this->DisKeywordsObj->getListByDisease($sclassid);
            } else {
                $param = array();
                if ($keshi['pID'] == 0) {
                    $param = array(
                        'class_level1' => $sclassid,
                    );
                } else {
                    $param = array(
                        'class_level2' => $sclassid,
                    );
                }
                $res = $this->DisKeywordsObj->getListByClassid($param);
            }
            $this->viewObj->listwords = array_slice($res, 0, 10);
            
            echo $this->viewObj->render("list_all_new.phtml");
            
        } catch (exception $e) {
            
            echo $e->getMessage();
        }
    }
    
    
    public function timeAction() {
        Zend_Loader::loadClass('Keshi', MODELS_PATH);
        $tmp_keshi_obj = new Keshi();


        $sTime = $this->_getParam('stime');
        $eTime = $this->_getParam('etime');
        $count = 15;
        $tmp_page_var = 'page';
        $tmp_page_obj = Zend_Registry::get('PageClass');
        $tmp_page_now = $this->_getParam($tmp_page_var); //当前页码
        $tmp_page_url = '/classid/time/'; //URL
        if ($sTime && $eTime) {
            $tmp_page_url .= 'stime/' . $sTime . '/etime/' . $eTime . '/';
        }
        $tmp_page_obj->setpublic($this->_my_params, $tmp_page_var);
        $tmp = $this->getTimeLimit();
        $m = 0;
        $where = '1 ';
        if ($this->_getParam('classid')) {
            $tmp_keshi_array = $tmp_keshi_obj->getList(" pID='" . $this->_getParam('classid') .
                    "'");
            if (is_array($tmp_keshi_array)) {
                foreach ($tmp_keshi_array as $k => $v) {
                    $tmp_arrchildid[] = $v['id'];
                }
                $where .= 'AND classid IN (' . implode(',', $tmp_arrchildid) . ')';
            }
        }
        foreach ($tmp as $k => $v) {
            $tmp[$k]['url'] = '/classid/time/stime/' . $v['stime'] . '/etime/' . $v['etime'] .
                    '/classid/' . $this->_getParam('classid');
        }


        foreach ($tmp as $k => $v) {
            if ((date("Ym", $sTime) >= date("Ym", $v['stime']) && date("Ym", $sTime) < date
                            ("Ym", $v['etime'])) || empty($sTime)) {
                $tmp[$k]['class'] = 'class="hover"';
                $where .= ' AND ctime BETWEEN ' . intval($v['stime']) . ' AND ' . intval($v['etime']);
                $num = $this->AskObj->numRows($where);
                $tmp_page_obj->set($num, $tmp_page_now, $tmp_page_url, $count);
                $this->viewObj->pagehtml = $tmp_page_obj->output(1); //返回分页控制HTML
                break;
            }
            $m++;
        }

        #print_r($tmp);
        $this->viewObj->timeArray = $tmp;

        $order = ' ctime DESC';
        #echo $where;
        $tmp_page_now or ( $tmp_page_now = 1);
        $offset = ($tmp_page_now - 1) * $count;
        $result = $this->DB_list->List_Ask($where, $order, $count, $offset);
        foreach ($result as $k => &$v) {
            $tmp = $this->Member_obj->getInfo($v['userid']);
            $v['userurl'] = 'http://home.9939.com/user/?uid=' . $v['userid'];
            $v['username'] = $tmp['nickname'] ? $tmp['nickname'] : $tmp['username'];
            $v['content'] = mb_substr($v['content'], 0, 350, 'utf8');
            $v['url'] = 'http://ask.9939.com/id/' . $v['id'];
            $v['lostTime'] = $this->getLostTime($v['ctime'], $v['term']);
        }
        $this->viewObj->ask_array = $result;


        ######################  科室  #######################
        $where = 'pId=0 AND id>50';
        $tmp = $tmp_keshi_obj->getList($where);
        foreach ($tmp as $k => $v) {
            $tmp[$k]['url'] = $tmp_page_url . 'classid/' . $v['id'];
        }
        $this->viewObj->keshi = $tmp;
        ######################  科室  #######################


        echo $this->viewObj->render("list_by_time.phtml");
    }

    //列表页问题列表部分生成静态文件
    public function asklistAction($data, $ptotal = 0, $page = 1, $status = '', $classid) {
        //生成列表静态列表start
        $pathtmp = 'liststatic/l_' . $classid . '/s_' . $status;
        $patharr = explode('/', $pathtmp);
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
    
    
    public function error() {
        header('HTTP/1.1 ' . '404');
        echo $this->viewObj->render('404.phtml');
        exit;
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
    //新版翻页
    private function pages($page = 1, $sclassid=0, $status=3, $path = '', $total_pages = '', $minid = 0) {
        $order = 'id DESC';
        $page_size = 30; //每页条数
        $tmp_page_var = 'page'; //分页变量
        $tmp_page_now = $this->_getParam($tmp_page_var,1); //当前页码
        //
        //添加保存url参数值的数组 @wangpuqiang 2015.03.26
        if ($total_pages) {
            $num = $total_pages*$page_size;
        } else {
//            @include_once (APP_ROOT . '/include/count_cache.php'); //2010.6.1 xjd
//            $num = $cacheArr;
            $num= 0;
            $cacheName = 'ask_new_' . $sclassid . '_' . $status;
            $data = QLib_Cache_Client::getCache('ask_new', $cacheName);
            if($data){
                $num = $data;
            }
        }
        $return_ask_list = array();
        if ($page > 1 && $num) {
            $total_pages = ceil($num / $page_size);
            $start = $page * $page_size;
            //前100页的数据，从数据库读取
            if ($start > 100 * $page_size) {
                $return_ask_list = $this->getasklistAction($total_pages, $tmp_page_now, $status, $sclassid);
            }
            if (count($return_ask_list)>0) {
                $minid = $return_ask_list[count($return_ask_list) - 1]['id'];
            }
        }
        $cache_keshi = array();
        if (count($return_ask_list)==0) {
            $w = '';
            $index = "";
            if ($sclassid != 0) {
                $cache_keshi = $this->keshi_obj->getKeshifenliCache(array($sclassid),1);
                $category = $cache_keshi[$sclassid];
                $index = 'FORCE INDEX(';
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
            if($w!=1){
                $where = $w;
            }
            $wherearr = array();
            if ($status < 2) { // 小于2的时候表示解决状态
//                $wherearr[] =" status=$status ";
                $wherearr[] =" answernum > 0 ";
            } elseif ($status == 2) { // 悬赏问题
                $wherearr[] = " point > 0  ";
            } elseif ($status == 4) { // 0回复问题
                $wherearr[] = "  answernum = 0   ";
            } 
            
            if(count($wherearr)>0){
                if(!empty($where)){
                    array_unshift($wherearr, $where);
                }
                $str_where_arr = implode(' and ', $wherearr);
                $where = $str_where_arr;
            }
            
            if ($sclassid != 0) {
                $sArrChildid = $cache_keshi[$sclassid]['arrchildid'];
            } else{
                $sArrChildid = 0;
            }
            $total_where='';
            if ($where == 1) {
                $where = " status in (0,1) ";
            }else{
                $total_where = $where;
            }
            $start = $page >= 1 ? ($page - 1) * $page_size : 0;
            $return_ask_list = $this->DB_list->List_Ask($where, $order, $page_size, $start, $index);
            $num = $this->DB_list->numRows($total_where);
            foreach ($return_ask_list as $k => $v) {
                $jb_name = '';
                $classid = $v['classid'];
                $jb_classid = ($v['class_level3'] == 0) ? $classid : 'dis_'.$classid;
                $cache_keshi = $this->keshi_obj->getKeshifenliCache(array($jb_classid),1);
                if ($jb_classid) {
                    $jb_name = $cache_keshi[$jb_classid]['name'];
                } else if ($v['class_level3']) {
                    $jb_classid = $v['class_level3'];
                    $jb_name = $cache_keshi[$jb_classid]['name'];
                } else if ($v['class_level2']) {
                    $jb_classid = $v['class_level2'];
                    $jb_name = $cache_keshi[$v['class_level2']]['name'];
                } else if ($v['class_level1']) {
                    $jb_classid = $v['class_level1'];
                    $jb_name = $cache_keshi[$v['class_level1']]['name'];
                }
                $return_ask_list[$k]['is_disease']= $cache_keshi[$jb_classid]['is_disease'];
                $return_ask_list[$k]['jb_classid'] = $jb_classid;
                $return_ask_list[$k]['keshi_id'] = $classid;
                $return_ask_list[$k]['jb_name'] = $jb_name;
                $return_ask_list[$k]['classname'] = $jb_name;
                $return_ask_list[$k]['statustxt'] = $v['status'] == 1 ? '已解决' : '未解决';
                $return_ask_list[$k]['cntime'] = $this->formatAskTime($v['ctime']);
                $return_ask_list[$k]['total_records'] = $num;
                $minid = $v['id'];
            }
            if ($page > 1) {
                $back = $this->asklistAction($return_ask_list, $total_pages, $tmp_page_now, $status, $sclassid);
            } else {
//                $totalarr = array('content' => $num, 'addtime' => time(), 'savetime' => 86400);
//                @file_put_contents(APP_ROOT . '/cache/ask_new_' . $sclassid . '_' . $status, serialize($totalarr));
                $cacheName = 'ask_new_' . $sclassid . '_' . $status;
                QLib_Cache_Client::setCache('ask_new', $cacheName, $num, 24);
            }
        }
        $total_pages = ceil($num / $page_size);
        return array(
            'aList' => $return_ask_list, 
            'status' => $status, 
            'minid' => $minid,
            'page_size'=>$page_size,
            'total_pages'=>$total_pages,
            'page_now'=>$page
        );
    }

    private function formatAskTime($ctime){
        $max_diff = 24*60*60; 
        $one_hour_diff = 1*60*60;
        $one_minu_diff = 60*60;
        $curr_time = time();
        $diff_time = $curr_time - $ctime;
        if($diff_time>$max_diff){
            return date('Y-m-d',$ctime);
        }else if($diff_time>$one_hour_diff){
            return sprintf('%d小时前', ceil($diff_time/3600));
        }else{
            return sprintf('%d分钟前',  ceil($diff_time/60));
        }
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
