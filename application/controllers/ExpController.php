<?php

/**
 * 健康经验 控制器
 */
class ExpController extends Zend_Controller_Action{

    public $view = null;
   
    //0: 常见疾病; 1: 生活保健; 2:  两性健康; 3:  整形美容)
    private $plates = array(
        0 => array('name' => '常见疾病', 'url' => '/expcat/0/'),
        1 => array('name' => '生活保健', 'url' => '/expcat/1/'),
        2 => array('name' => '两性健康', 'url' => '/expcat/2/'),
        3 => array('name' => '整形美容', 'url' => '/expcat/3/'),
    );
    
    public  function init(){
        parent::init();
        $this->view = Zend_Registry::get('view');
    }
    
     public function indexAction(){
        $this->fillSuffix();
        $this->view->plates = $this->plates;
        echo $this->view->render('exp/index.phtml');
    }

    /**
     * 内容页
     * @author gaoqing
     * @date 2016-07-22
     * @return string 视图
     */
    public function contentAction(){
        $template = 'exp/content.phtml';
        $uri = $_SERVER['REQUEST_URI'];
        $p = strpos($uri, '.html');
        if (empty($uri) || empty($p)){
            $this->error();
            exit;
        }
        $request = $this->getRequest();
 
        $addtime = $request->getParam('addtime', '0');
        $id = $request->getParam('id', '0');

        if (!empty($id)){

            //1、查询当前文章的内容
            $experience = new Experience();
            $exp = $experience->getExperience($id, true, true);
            if(empty($exp)){
                $this->error();
                exit();
            }
            if (!empty($exp)){
                $exp['addtime_init'] = $exp['addtime'];
                $exp['addtime'] = date('Y-m-d', $exp['addtime']);

                //2、查询疾病信息
                $disease = $experience->getDiseaseByDisid($exp['diseaseid'], $exp['catid']);

                //3、相关经验
                $relExps = $experience->getRelExps($exp['diseaseid'], 4);

                //4、相关问题
                $relAsks = $experience->getRelAsks($disease['diseaseid'], 4);

                //5、最新经验
                $latestExps = $experience->getArticleList(0, 8, false);

                $this->view->assign("exp", $exp);
                $this->view->assign("disease", $disease);
                $this->view->assign("relExps", $relExps);
                $this->view->assign("relAsks", $relAsks);
                $this->view->assign("latestExps", $latestExps);

                echo $this->view->render($template);
            }
        }
    }
    
    /**
     * 经验分享
     */
    public function categoryAction() {
        $this->fillSuffix();
        $uri = $_SERVER['REQUEST_URI'];
        $template = 'exp/category.phtml';
//        $md5URL = md5($uri);
        $cate = $this->_getParam('cate', 'expcat');
        $id = $this->_getParam('id', '0');
        $page = $this->_getParam('page', '1');
        
        $plates = $this->plates; 
        
        $exp_obj = new Experience();
        $plateid = $id;
        $catid = '';
        $catshow = '全部';
        $curname = '';
        $diseaseid = '';
        $error = 0;
        if($cate == 'explist'){
            $category = $exp_obj->getCategoryByCatid($id);
            $error = !empty($category) ? 0 : 1;
            $plateid = $category['plateid'];
            $catid = $id;
            $catshow = $category['name'];
            $curname = $category['name'];
        }elseif($cate == 'expdis'){
            $disease = $exp_obj->getDiseaseByDisid($id);
            $error = !empty($disease) ? 0 : 1;
            $category = $exp_obj->getCategoryByCatid($disease['catid']);
            $plateid = $disease['plateid'];
            $catid = $disease['catid'];
            $diseaseid = $id;
            $catshow = $category['name'];
            $curname= $disease['name'];
        }else{
            $error = in_array($id, range(0, 3)) ? 0 : 1;
        }
        if($error == 1){
            $this->error();
            exit;
        }
        $curname = $curname ? $curname : $plates[$id]['name'];
        
        //plateid->科室
        $categories = array();
        $categories = $exp_obj->getCategoriesByPlateid($plateid);
        //catid->疾病
        $disease = array();
        if($cate !== 'expcat'){
            $disease = $exp_obj->getDiseaseByCatid($catid);
        }
        
        //列表数据部分
        $idtype=''; 
        switch ($cate) {
            case 'explist':
                $idtype = 'catid';
                break;
            case 'expdis':
                $idtype = 'diseaseid';
                break;
            default:
                $idtype = 'plateid';
                break;
        }
        $size=11;
        $count = $exp_obj->getCountByCon($idtype, $id);//总数
        $paging_obj = new QLib_Paging();
        $pagetemplate = LIBRARY_PATH."/QLib/View/Templet/Paging/exp.phtml";
        $paging_obj = $paging_obj
                ->setTemplate($pagetemplate)
                ->setSize($size)
                ->setPageSetSize(7)
                ->setTotal($count)
                ->setUrlFormat('/' . $cate . '/'.$id.'-%d'. '/')
                ->setCurrent($page);
        
//        $totalpage = ceil($count/$size);
//        $page = min($page,$totalpage);
        $offset = $paging_obj->getOffset();
        $list = $exp_obj->getListByCon($idtype, $id, $offset, $size);
        $expids = '';
        $diseaseids = '';
        foreach ($list as $k => $v) {
            $expids .= $v['id'].',';
            $diseaseids .=$v['diseaseid'].',';
            $list[$k]['url'] = '/exp/'.$v['addtime'].$v['id'].'html';
        }
        $expids = trim($expids, ',');
        $diseaseids = trim($diseaseids, ',');
        $listcontent = $exp_obj->getContentList($expids);
        $call_fun = array($this,'filterHtmlCallBack');
        $listcontent = array_map($call_fun, $listcontent);
        $listcontent = self::setColumntoKey($listcontent, 'id');
        
        $listdisease = $exp_obj->getDislistByDisid($diseaseids);
        $listdisease = self::setColumntoKey($listdisease, 'id');
        
//        $pre = ($page > 1) ? ($page - 1) : 1;
//        $next = ($page == $totalpage) ? $totalpage : ($page + 1);
//        $pageurl = array(
//            'pre' => sprintf('/%s/%d-%d/', $cate,$id,$pre),
//            'next' => sprintf('/%s/%d-%d/', $cate,$id,$next),
//            'page' => $page,
//            'totalpage' => $totalpage,
//        );
        
        $latest_experience = $exp_obj->getArticleList(0,8,false);
        $current = array(
            'cate' => $cate,
            'plateid' => $plateid,
            'catid' => $catid,
            'diseaseid' => $diseaseid,
            'plateshow'=>$plates[$plateid]['name'],
            'catshow' => $catshow,
            'curname'=>$curname,
            'uri'=>$uri,
        );
        $this->view->current=$current; //当前分类
        $this->view->plates=$plates; //4模块
        $this->view->categories=$categories; //科室部分
        $this->view->disease=$disease; //疾病部分
        $this->view->list=$list; //列表部分
        $this->view->listcontent=$listcontent; //列表部分
        $this->view->listdisease=$listdisease; //列表部分
        $this->view->pageurl=$paging_obj; //页码部分
        $this->view->latest_experience=$latest_experience; //右侧最新经验
        echo $this->view->render($template);
    }
    /**
     * 我要分享
     */
    public function shareAction() {
        $this->fillSuffix();
        $uri = $_SERVER['REQUEST_URI'];
        $template = 'exp/share.phtml';
        echo $this->view->render($template);
    }

    /**
     * 我的经验
     */
    public function mysharingAction() {
        $this->fillSuffix();
        $uri = $_SERVER['REQUEST_URI'];
        $template = 'exp/mysharing.phtml';
        echo $this->view->render($template);
    }

    public function error(){
        header('HTTP/1.1 ' . '404');
        echo $this->view->render('404.phtml');
        exit;
    }
    public function filterHtmlCallBack($v){
        $v['content'] = strip_tags($v['content']);
        return $v;
    }
    
    private static function setColumntoKey($array = array(), $column = 'id') {
        $newarr = array();
        foreach ($array as $k => $v) {
            $newarr[$v[$column]] = $v;
        }
        return $newarr;
    }

        private function fillSuffix() {
        $uristr = $_SERVER['REQUEST_URI'];
        $str_params = '';
        if ($pos = strpos($uristr, '?')) {
            $str_params = substr($uristr, $pos);
            $uristr = substr($uristr, 0, $pos);
        }
        if (($last_char = substr($uristr, -1) ) != '/' && (stripos($uristr, '.shtml') === false)) {
            $redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $uristr . '/';
            $redirect_url.= $str_params;
            header("Location:$redirect_url", true, 301);
            exit;
        }
    }

}
