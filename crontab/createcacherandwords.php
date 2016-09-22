<?php
/**
 * insertSdaData.php
 * @author 黄云龙 2010-10-26
 * 10827 -- 10840
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
header("Content-type:text/html;charset=utf-8");
$basepath = dirname(dirname(__FILE__));
$config_path = $basepath . DIRECTORY_SEPARATOR . "web/api/config.php";
//var_dump($config_path);exit;
require $config_path;
//$condition = array(
//                array(
//                    'filter'=>'filter',
//                    'args'=>array('typeid',array(0,1))
//                )
//        );
$condition = array();
KeyWords::createCacheRandWords($condition);