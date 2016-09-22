<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
set_time_limit(0);
header("Content-type:text/html;charset=utf-8"); 
//去除原有的重定向,通过路由配置来解决 wangpuqiang @2014.03.31
$uri = $_SERVER['REQUEST_URI'];
if(strpos($uri,'classid')||strpos($uri,'list')){
    $arr_regs = array('@/list/index?/?$@','@/list/index/classid/(\d+)?/?$@','@/classid/(.+)/@','@/classid/(\d+)?(.html)$@','@/list/(\d+)(-)?(\d+)?\2?(\d+)?(\.html)?$@');
    foreach($arr_regs as $v){
        $m_count =  preg_match($v, $uri, $matches);
        if($m_count>0){
            $url_param = isset($matches[1])?$matches[1]:0;
            header('Location:http://ask.9939.com/classid/'.$url_param, true, 302);
            break;
        }
    }
} else {
    $m_count =  preg_match('@/ask/show/id/(.+)/?@', $uri, $matches);
    if($m_count>0){
        header('Location:http://ask.9939.com/id/'.$matches[1], true, 302);
    }
}

date_default_timezone_set ('Asia/Shanghai');
define("APP_ROOT",dirname(__FILE__));
define("MODELS_PATH",APP_ROOT."/application/models");
define("CONTROLLERS_PATH",APP_ROOT."/application/controllers");
defined("LIBRARY_PATH") or define('LIBRARY_PATH',APP_ROOT."/library");
defined("ZEND_PATH") or define("ZEND_PATH", dirname(dirname(APP_ROOT.'/../')).'/QFramework2.0');


set_include_path(LIBRARY_PATH . PATH_SEPARATOR . MODELS_PATH . PATH_SEPARATOR . ZEND_PATH . PATH_SEPARATOR .get_include_path());
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true)->suppressNotFoundWarnings(true);
$autoloader->registerNamespace(array('QModels', 'QConfigs', 'QLib', 'Q'));
QConfigs_Defines::setVaribles("local");

require_once("data/data_usergroup.php"); 
require_once("data/data_creditrule.php"); 
require_once("data/data_censorvalue.php"); 
require_once("data/data_cookie.php"); 
//公用函数类
$common_tool=new Zend_Adver_Tool();
Zend_Registry::set("tool",$common_tool);
$PageClass=new Zend_Adver_Pageclass();
Zend_Registry::set("PageClass",$PageClass);

$front = Zend_Controller_Front::getInstance();
$front->setControllerDirectory(array(
	'default' => APP_ROOT.'/application/controllers',
    'test'    => APP_ROOT.'/application/controllers/test',
	'manage'    => APP_ROOT.'/application/controllers/manage'
	));

//$front->setParam("noErrorHandler",true);
$front->setParam("noViewRenderer",true)
      ->throwExceptions(true);
//视图助手
$view = new Zend_View();
$view->setScriptPath(APP_ROOT.'/application/views/scripts');
$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');

$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($view)->setViewSuffix('php');
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
Zend_Registry::set("view",$view);    
// session
Zend_Session::setOptions(array('cookie_domain' => '.9939.com'));

// xzin 2009-11-24
$router = $front->getRouter();   
//实现如http://ask.9939.com/id/2215343类型的url   

$router->addRoute('ask',    
    new Zend_Controller_Router_Route_Regex('id/(\d+)(\.(s)?html)?$',    
        array(   
            'controller'=>'ask',   
            'action'=>'shows'                         
        ),
        array(
            1=>'id'
        )   
    )   
);  



//WangPuQiang @ 2015-03-24
$router->addRoute('list_regex_all_params',    
    new Zend_Controller_Router_Route_Regex('classid/(\d+)(-)?(all)?\2?(\d+)?(\.(s)?html)?$',    
        array(   
            'controller'=>'list',   
            'action'=>'index'
        ),
        array(
            1=>'classid',
            3=>'status',
            4=>'page'
        )
    )   
);

$router->addRoute('list_regex_two_params',    
    new Zend_Controller_Router_Route_Regex('classid/(\d+)(-)?(\d+)?\2?(\d+)?(\.html)?$',    
        array(   
            'controller'=>'list',   
            'action'=>'index'
        ),
        array(
            1=>'classid',
            3=>'status',
            4=>'page'
        )
    )   
);

$router->addRoute('departments',    
    new Zend_Controller_Router_Route('cat.shtml',    
        array(   
            'controller'=>'departments',   
            'action'=>'index'                         
        )   
    )   
);


$router->addRoute('disease',    
    new Zend_Controller_Router_Route_Regex('disease/(\d+)(-)?(\d+)?(\.(s)?html)?$',    
        array(   
            'controller'=>'disease',   
            'action'=>'index'
        ),
        array(
            1=>'classid',
            3=>'page'
        )
    )   
);

$router->addRoute('disease_top',    
    new Zend_Controller_Router_Route_Regex('top/(\w+)(-)?(\d+)?$',    
        array(   
            'controller'=>'disease',   
            'action'=>'top'
        ),
        array(
            1=>'classid',
            3=>'page'
        )
    )   
);

$router->addRoute('search_index',
    new Zend_Controller_Router_Route_Regex('hot',
        array(  
            'controller'=>'hotwords',   
            'action'=>'index'
        )
    )
);

$router->addRoute('search_keywords_list_regex',    
    new Zend_Controller_Router_Route_Regex('hot/(\w)',    
        array(  
            'controller'=>'hotwords',   
            'action'=>'letter'
        ),
        array(
            1=>'wd'
        ),
        'hot/%s'
    )   
);


$router->addRoute('search_keywords_wd_regex',    
    new Zend_Controller_Router_Route_Regex('hot/(\w{2,})$',    
        array(  
            'controller'=>'hotwords',   
            'action'=>'search'
        ),
        array(
            1=>'wd'
        ),
        'hot/%s'
    )   
);

$router->addRoute('search_sou',
    new Zend_Controller_Router_Route_Regex('hot/kw',
        array(  
            'controller'=>'hotwords',   
            'action'=>'sou'
        )
    )
);

/********* 2015-12-14 新增：End ************/

/* * ** 健康经验 Start ** */

/** 首页 */
$router->addRoute(
        'jingyan_index', new Zend_Controller_Router_Route(
        'jingyan/', array(
    'controller' => 'exp',
    'action' => 'index'
        )
        )
);
/** 栏目页 */
$router->addRoute('jingyan_category', new Zend_Controller_Router_Route_Regex('(expcat|explist|expdis)/(\d+)-?(\d+)?', array(
    'controller' => 'exp',
    'action' => 'category'
        ), array(
    1 => 'cate',
    2 => 'id',
    3 => 'page',
        )
        )
);

/** 内容页 */
$router->addRoute(
        'exp_content', new Zend_Controller_Router_Route_Regex(
        'exp/(\d{10})(\d+).html', array(
    'controller' => 'exp',
    'action' => 'content'
        ), array(
    1 => 'addtime',
    2 => 'id'
        )
        )
);


//结构化数据
$router->addRoute('sitemap_indez',
    new Zend_Controller_Router_Route_Regex('create-ask-struct',
        array(  
            'controller'=>'sitemap',   
            'action'=>'index'
        )
    )
);

$router->addRoute('expsitemap_index',
    new Zend_Controller_Router_Route_Regex('create-exp-struct',
        array(  
            'controller'=>'expsitemap',   
            'action'=>'index'
        )
    )
);

/** 我要分享 */
$router->addRoute(
        'exp_share',
        new Zend_Controller_Router_Route_Regex(
            'jingyan/shareing/share',
            array(
                'controller' => 'exp',
                'action' => 'share'
            )
        )
);

/** 我的经验 */
$router->addRoute(
        'exp_mysharing',
        new Zend_Controller_Router_Route_Regex(
            'jingyan/shareing/meshare',
            array(
                'controller' => 'exp',
                'action' => 'mysharing'
            )
        )
);
/* * ** 健康经验 End ** */



//问题汇总-日期列表页 lc@2016-8-26
$router->addRoute(
    'wenti_list',
    new Zend_Controller_Router_Route_Regex(
        'wenti/(\d+).html',
        array(
            'controller' => 'wenti',
            'action' => 'list'
        ), array(
            1 => 'page',
        )
    )
);

//问题汇总-问题列表页 lc@2016-8-26
$router->addRoute(
    'wenti_detail',
    new Zend_Controller_Router_Route_Regex(
        'wenti/(\d{4})-(\d{2})-(\d{2})/(\d+).html',
        array(
            'controller' => 'wenti',
            'action' => 'detail'
        ), array(
            1 => 'year',
            2 => 'month',
            3 => 'day',
            4 => 'page',
        )
    )
);

$profile = new Zend_Db_Profiler();
$profile->setEnabled(TRUE);
$profile->getLastQueryProfile();

$front->dispatch();
