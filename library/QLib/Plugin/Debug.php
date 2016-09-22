<?php

class QLib_Plugin_Debug extends Zend_Controller_Plugin_Abstract {
	
	private $startTime = 0;
	private $endTime = 0;
	/**
	 * Zend_Controller_Front 向注册的 路由器 发送请求前被调用。
	 * @see Zend_Controller_Plugin_Abstract::routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $resquest) {
		$this->startTime = microtime(true);
	}
	/**
	 * 在 路由器 完成请求的路由后被调用。
	 * @see Zend_Controller_Plugin_Abstract::routeShutdown()
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		//print " routeShutdown ";
	}
	/**
	 * 在 Zend_Controller_Front 进入其分发循环（dispatch loop）前被调用。
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopStartup()
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		//print " dispatchLoopStartup ";
	}
	
	/**
	 * 在动作由 分发器 分发前被调用。该回调方法允许代理或者过滤行为。
	 * 通过修改请求和重设分发标志位（利用 Zend_Controller_Request_Abstract::setDispatched(false) ）当前动作可以跳过或者被替换。
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		//print " preDispatch ";
	}
	
	/**
	 * 在 Zend_Controller_Front 推出其分发循环后调用。
	 * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 */
	public function dispatchLoopShutdown() {
		//print " dispatchLoopShutdown ";
	}
	
	/**
	 * 在动作由 分发器 分发后被调用。该回调方法允许代理或者过滤行为。
	 * 通过修改请求和重设分发标志位（利用 Zend_Controller_Request_Abstract::setDispatched(false) ）可以指定新动作进行分发。
	 * @see Zend_Controller_Plugin_Abstract::postDispatch()
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		$debug = $request->getParam('_qdebug');
		$qip = $request->getParam('_qip');
		if ($qip == 'debugserver') {
			print "<!--";
			print isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '?';
			print "-->";
		}
		if (empty($debug)) {
			return;
		}
		$response = $this->getResponse();
		$this->endTime = microtime(true);
		$startTime = $this->startTime;
		$endTime = $this->endTime;
		$allTime = ($this->endTime - $this->startTime);
		$moduleName = $request->getModuleName();
		$controllerName = $request->getControllerName();
		$actionName = $request->getActionName();
		$view_path = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewFile = $view_path->getViewScript();
		$view = $view_path->view;
		$vars = $view->getVars();
		if ($debug == 1) {
			$logger = new Zend_Log();
			$writer = new Zend_Log_Writer_Firebug();
			$logger->addWriter($writer);
			Zend_Registry::set('logger', $logger);
			$logger = Zend_Registry::get('logger');
			$debugData = array(
				'开始时间' => $startTime, 
				'结束时间' => $endTime, 
				'花费时间' => $allTime, 
				'使用模块' => $moduleName, 
				'控制器' => $controllerName, 
				'方法' => $actionName, 
				'视图文件' => $viewFile, 
				'请求参数' => $request->getParams(), 
				'视图变量' => $vars
			);
			$logger->log($debugData, Zend_Log::DEBUG);
		}
		elseif ($debug == 2) {
			$viewVars = Zend_Debug::dump($vars, null, false);
			$params = Zend_Debug::dump($request->getParams(), null, false);
			$str = <<<DEBUG
        <style>
        table.debug__ {background-color:#888}
        .debug__ td{background-color:#fff;padding:5px 9px;}
        </style>
        <table border="0" cellpadding="1" cellspacing="1" class="debug__">
        <tr><td colspan="2" align="center">调试信息</td></tr>
        <tr><td>开始时间</td><td>{$startTime}</td></tr>
        <tr><td>结束时间</td><td>{$endTime}</td></tr>
        <tr><td>花费时间</td><td>{$allTime}</td></tr>
        <tr><td>使用模块</td><td>{$moduleName}</td></tr>
        <tr><td>控制器</td><td>{$controllerName}</td></tr>
        <tr><td>方法</td><td>{$actionName}</td></tr>
        <tr><td>视图文件</td><td>{$viewFile}</td></tr>
        <tr><td>请求参数</td><td>{$params}</td></tr>
        <tr><td>视图变量</td><td>{$viewVars}</td></tr>
        </table>
DEBUG;
			$response->appendBody($str); #$str 注入内容
		}
	}
}