<?php
define("AMFPHP_BASE", realpath(dirname(dirname(dirname(__FILE__)))) . "/");
require_once(AMFPHP_BASE . "shared/app/BasicGateway.php");
require_once(AMFPHP_BASE . "shared/util/MessageBody.php");
require_once(AMFPHP_BASE . "shared/util/functions.php");
require_once(AMFPHP_BASE . "json/app/Actions.php");


class Gateway extends BasicGateway
{
	function createBody()
	{
		$GLOBALS['amfphp']['encoding'] = 'json';
		$body = & new MessageBody();
		
		$uri = setUri();
		$elements = explode('/json.php/', $uri);
		
		$args = $elements[1];
		$rawArgs = explode('/', $args);
		
		if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
		{
			$rawArgs[] = $GLOBALS['HTTP_RAW_POST_DATA'];
		}
		
		$body->setValue($rawArgs);
		return $body;
	}
	
	/**
	 * Create the chain of actions
	 */
	function registerActionChain()
	{
		$this->actions['deserialization'] = 'deserializationAction';
		$this->actions['classLoader'] = 'classLoaderAction';
		$this->actions['security'] = 'securityAction';
		$this->actions['exec'] = 'executionAction';
		$this->actions['serialization'] = 'serializationAction';
	}
}
?>