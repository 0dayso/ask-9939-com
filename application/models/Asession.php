<?php
/**
 * action:    session访问操作类
 * copyright: 中视在线
 * author:    王忠波
 * time:      2009/06/19
 * 1.注释请使用phpdocument的标准规范
 */
class Asession
{
	public function Get_Manage_Session()
	{
		$adver_manage= new Zend_Session_Namespace("manage");
		if($adver_manage->isLocked()==false)
		{
			$adver_manage->lock();
		}
		
		$session=$adver_manage->user;
		if(is_object($session))
		{
			return $session->toArray();
		}
	}

	
	/**
	*	取出数据提交令牌
	*	@return 令牌
	*/
	public function Get_Token_Session()
	{
		$adver_token= new Zend_Session_Namespace("token");
		if($adver_token->isLocked()==false)
		{
			$adver_token->lock();
		}
		
		return $adver_token->token;
	}

	/**
	*	取出数据提交令牌2
	*   @author 林原 2010-09-25
	*	@return 令牌
	*/
	public function Get_Token_Session2()
	{
		$adver_token= new Zend_Session_Namespace("token");
		if($adver_token->isLocked()==false)
		{
			$adver_token->lock();
		}
		
		return $adver_token->token2;
	}
}
?>