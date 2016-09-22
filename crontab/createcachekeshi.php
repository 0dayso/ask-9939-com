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


$keshi_obj = new Keshi();
$keshi_obj->createCacheKeshiFenli('redis_keshi_only_cache');
$keshi_obj->createCacheKeshiFenli();
//$ret = $keshi_obj->getKeshifenliCache();
//var_export($ret);