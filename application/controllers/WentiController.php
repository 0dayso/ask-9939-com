<?php

class WentiController extends Zend_Controller_Action {

    private $askObj;
    private $keshiObj;

    public function init() {
        $this->askObj = new Ask();
        $this->view = Zend_Registry::get("view");
        parent::init();
    }

    /**
     * 日期列表页
     */
    public function listAction() {
        $template = "wenti/list.phtml"; //页面模板
        $page = intval($this->getRequest()->getParam('page', 1)); //当前页码
        $perPageNum = 78; //每页显示数量
        //生成指定区间内的日期
        $dateMapCache = $this->askObj->dateMap;
        $minDate = date('Y-m-d', $dateMapCache['wd_ask_history_1'][0]);
        $maxDate = date('Y-m-d', strtotime('-1 day'));

        $dateMap = array();
        $dateRange = $this->createYmdRange($minDate, $maxDate);
        $i = 0;
        foreach ($dateRange as $key => $val) {
            $dateMap[$val] = array(
                'url' => $this->detailPageUrl(array('page' => 1, 'params' => array('date' => $val))),
            );
            $i++;
        }
        krsort($dateMap);

        //根据页码获取当前页显示的日期数据
        $dateMapPage = array_slice($dateMap, $page * $perPageNum - $perPageNum, $perPageNum);

        //分页功能
        $totalNum = count($dateMap);
        $totalPage = ceil($totalNum / $perPageNum);
        $pagedata = array(
            'curr' => $page,
            'totalnum' => $totalNum,
            'totalpage' => $totalPage,
        );

        $urlOpt = array('urlfn' => 'listPageUrl', 'params' => '');
        $pagehtml = $this->displayPaging($urlOpt, $page, $totalNum, $totalPage);

        //页面参数
        $pageParams = array(
            'title' => '问题汇总列表第' . $page . '页_久久问医',
            'keywords' => '健康问答,在线问答,问答',
            'description' => '久久问医为您提供各类疾病方面的问题列表，主要包括各类疾病疾病、预防、保健、治疗、诊断、用药等方面的问题及在线医生解答.',
            'datemap' => $dateMapPage,
            'pagehtml' => $pagehtml,
        );
        //页面传值
        foreach ($pageParams as $k => $v) {
            $this->view->$k = $v;
        }
        echo $this->view->render($template);
    }

    /**
     * 日期详情页
     */
    public function detailAction() {
        try {
            $template = "wenti/detail.phtml"; //页面模板
            $this->keshiObj = new Keshi();

            $year = $this->getRequest()->getParam('year');
            $month = $this->getRequest()->getParam('month');
            $day = $this->getRequest()->getParam('day');
            $page = intval($this->getRequest()->getParam('page', 1));

            $begin_time = mktime(0, 0, 1, $month, $day, $year);
            $end_time = mktime(23, 59, 59, $month, $day, $year);
            $condition = "ctime >= {$begin_time} AND ctime < {$end_time} and examine=1";

            $perPageNum = 16;
            $curr_page = $page;
            $offset = $curr_page * $perPageNum - $perPageNum;
            $offset = $offset > 0 ? $offset : 0;
            $table_date = date('Y-m-d H:i:s',$end_time);//当天最后一个时间,来判断表,防止当天没数据 $year . '-' . $month . '-' . $day;
            $return_ask_info = $this->askObj->listHistoryByDate($table_date, $condition, 'ctime DESC', $perPageNum, $offset, true);
            $list_result = $return_ask_info['list'];
            $total_num = $return_ask_info['total'];
            $total_page = ceil($total_num / $perPageNum);

            $format_date = sprintf("%s-%s-%s", $year, strlen($month) < 2 ? '0' . $month : $month, strlen($day) < 2 ? '0' . $day : $day);
            $urlOpt = array('urlfn' => 'detailPageUrl', 'params' => array('date' => $format_date));
            $pagehtml = $this->displayPaging($urlOpt, $curr_page, $total_num, $total_page);

            $list = array();
            foreach ($list_result as $k => $v) {
                $list[$k] = $v;

                $classid = $v['classid'];
                if ($v['class_level3'] > 0) {
                    //如果有class_level3,则当前问题归类到疾病
                    $classid = 'dis_' . $classid;
                }

                $navigation = $this->getClassByAsks($classid);
                $list[$k]['keshiname'] = $navigation[0];
            }

            //页面参数
            $pageParams = array(
                'title' => $year . '年' . $month . '月' . $day . '日' . '问题汇总列表第' . $page . '页_久久问医',
                'keywords' => '健康问答,在线问答,问答',
                'description' => '久久问医为您提供各类疾病方面的问题列表，主要包括各类疾病疾病、预防、保健、治疗、诊断、用药等方面的问题及在线医生解答.',
                'list' => $list,
                'pagehtml' => $pagehtml,
            );

            //页面传值
            foreach ($pageParams as $k => $v) {
                $this->view->$k = $v;
            }

            echo $this->view->render($template);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 
     * 获取问题栏目
     */
    private function getClassByAsks($classid) {
        $this->keshiObj = new Keshi();
        $CATEGORY = $this->keshiObj->getKeshifenliCache(array($classid), 1);
        $keshicid = 0;
        $array = array();
        while (true) {
            if (empty($classid))
                break;
            if ($CATEGORY[$classid]) {
                if ($keshicid !== 0) {
                    $arrs[] = $classid;
//                    $array[] = '<a href="/classid/' . $classid . '" target="_blank" title="' . $CATEGORY[$classid]['name'] . '">' . $CATEGORY[$classid]['name'] . '</a>';
                    $array[] = $CATEGORY[$classid]['name'];
                } else {
                    $is_disease = isset($CATEGORY[$classid]['is_disease']) ? $CATEGORY[$classid]['is_disease'] : 1;
                    $jb_url = $is_disease == 1 ? "/disease/" . $CATEGORY[$classid]['id'] : "/classid/$classid";
//                    $array[] = '<a href="' . $jb_url . '" title="' . $CATEGORY[$classid]['name'] . '" target="_blank">' . $CATEGORY[$classid]['name'] . '</a> ';
                    $array[] = isset($CATEGORY[$classid]['is_disease']) ? array($CATEGORY[$classid]['name'], $CATEGORY[$classid]['is_disease'], $CATEGORY[$classid]['id']) : array($CATEGORY[$classid]['name'], 1, $classid);
                }
                $classid = $CATEGORY[$classid]['pID'];
                $keshicid = 1;
            } else {
                break;
            }
        }
//        if ($array) {
//            $array = implode(' > ', array_reverse($array));
//        }
        return $array;
    }

    /**
     * 生成分页链接
     * @param type $pageNum
     * @return type
     */
    private function listPageUrl($params) {
        $pageNum = $params['page'];
        $url = sprintf("%s%d%s", "http://ask.9939.com/wenti/", $pageNum, '.html');
        return $url;
    }

    /**
     * 生成分页链接
     * @param type $pageNum
     * @return type
     */
    private function detailPageUrl($params) {
        $date = $params['params']['date'];
        $pageNum = $params['page'];
        $url = sprintf("%s%s/%d%s", "http://ask.9939.com/wenti/", $date, $pageNum, ".html");
        return $url;
    }

    /**
     * 列表页分页代码
     * @param type $urlfn
     * @param type $currPage
     * @param type $totalNum
     * @param type $totalPage
     * @param type $showPage
     * @return string
     */
    private function displayPaging($urlOpt, $currPage, $totalNum, $totalPage, $showPage = 5) {
        if($totalNum==0){
            return array('html' => '', 'totalnum' => $totalNum, 'totalpage' => $totalPage);
        }
        if ($totalPage < $currPage) {
            $this->error();
            return array('html' => '', 'totalnum' => $totalNum, 'totalpage' => $totalPage);
        }
        $urlParams = $urlOpt['params'];
        $urlfn = $urlOpt['urlfn'];
        $beginUrl = $this->$urlfn(array('page' => 1, 'params' => $urlParams));
        if ($currPage == 1) {
            $prevPage = 1;
        } else {
            $prevPage = $currPage - 1;
        }
        $prevUrl = $this->$urlfn(array('page' => $prevPage, 'params' => $urlParams));

        if ($currPage == $totalPage) {
            $nextPage = $totalPage;
        } else {
            $nextPage = $currPage + 1;
        }
        $nextUrl = $this->$urlfn(array('page' => $nextPage, 'params' => $urlParams));
        $lastUrl = $this->$urlfn(array('page' => $totalPage, 'params' => $urlParams));

        $start = min(floor(max(($currPage - floor($showPage / 2)), 1)), max(($totalPage - $showPage + 1), 1));
        $end = min(($start + $showPage - 1), $totalPage);

        $html = '<a href="' . $beginUrl . '" title="首页" target="_self" class="lpage">首页</a>';
        $html .= '<a href="' . $prevUrl . '" title="上一页" target="_self" class="lpaget">&lt;&lt;</a>';
        for ($i = $start; $i <= $end; $i++) {
            $pageurl = $this->$urlfn(array('page' => $i, 'params' => $urlParams));
            if ($currPage == $i) {
                $html .='<a href="' . $pageurl . '" class="now" target="_self" >' . $i . '</a>';    //输出页数
            } else {
                $html .='<a href="' . $pageurl . '"  target="_self" >' . $i . '</a>';    //输出页数
            }
        }
        $html .= '<a class="spot">...</a>';
        $html .= '<a href="' . $lastUrl . '" target="_self">' . $totalPage . '</a>';
        $html .= '<a href="' . $nextUrl . '" target="_self" title="下一页" class="lpaget">&gt;&gt;</a>';
        $html .= '<a href="' . $lastUrl . '" target="_self" title="末页" class="lpage">末页</a>';
        $html .= '<a class="spot">共 ' . $totalPage . ' 页， ' . $totalNum . '条</a>';
        return array('html' => $html, 'totalnum' => $totalNum, 'totalpage' => $totalPage);
    }

    private function map_func_callback($time) {
        return date('Y-m-d', $time);
    }

    /**
     * 生成指定区间内的日期
     * @param type $ymdStart
     * @param type $ymdEnd
     * @param type $range
     * @return type
     */
    private function createYmdRange($ymdStart, $ymdEnd = true, $range = 86400) {
        if ($ymdEnd === true)
            $ymdEnd = date('Y-m-d');

        $call_back = array($this, 'map_func_callback');
        return array_map($call_back, range(strtotime($ymdStart), strtotime($ymdEnd), $range));
    }
    
    public function error() {
        header('HTTP/1.1 ' . '404');
        echo $this->view->render('404.phtml');
        exit;
    }

}
