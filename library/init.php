<?php

function QLibCallBack($class) {
    return call_user_func(array('Zend_Loader', 'loadClass'), $class, LIBRARY_PATH);
}

function LibCallBack($class) {
    return call_user_func(array('Zend_Loader', 'loadClass'), $class, ZEND_PATH);
}

function ModelCallBack($class) {
    return call_user_func(array('Zend_Loader', 'loadClass'), $class, MODELS_PATH);
}
require_once ZEND_PATH . '/Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true)->suppressNotFoundWarnings(true);
$autoloader->pushAutoloader('QLibCallBack', array('QModels_', 'QConfigs_', 'QLib_'));
$autoloader->pushAutoloader('ModelCallBack', '');
$autoloader->pushAutoloader('LibCallBack', array('Q_', 'Zend_', 'ZendX_'));
$autoloader->registerNamespace(array('QModels_', 'QConfigs_', 'QLib_', 'Q_'));


