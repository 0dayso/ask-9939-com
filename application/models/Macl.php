<?php
 /**
  *##############################################
  * @FILE_NAME : Macl.php
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Thu Jul 16 14:08:06 CST 2009
  *==============================================
  * @Desc :  权限控制插件
  *==============================================
  */
Zend_Loader::loadClass("Asession",MODELS_PATH);
Zend_Loader::loadClass("Quanxian",MODELS_PATH);
class Macl extends Zend_Controller_Plugin_Abstract
{
	private $user;
	private $qx;
	public function __construct()
	{
		 $user=new Asession();
		 $this->user=$user->Get_Manage_Session();
		 $this->qx=new Quanxian();
	}
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		
		$array=$request->getParams();
		$controller=strtolower(trim($array['controller']));
		$action=strtolower(trim($array['action']));
		//print_r($array);exit;
		if($controller=="login")
		{
			//非登录状态访问
			if($action=="exit")
			{
				if(!$this->user['idu'])
				{
					Zend_Adver_Js::GoToTop('/manage/','');
				}
			}
		}
		elseif($controller=='index'){}
		else
		{
			if(!$this->user['idu'])
			{
				//flash 调用
				if($array['frmflex']==1)
				{
					$xml="<?xml version=\"1.0\" encoding=\"utf-8\" ?> <droot err=\"1\" ><emsg>请先登录</emsg>
					</droot>";
					echo $xml;
					exit;
				}
				else
				{
					Zend_Adver_Js::GoToTop('/manage/','');
				}
			}
			else
			{
				//角色验证验证代码
				//start
				if($this->user['idu'])
				{ 
					
					$data['controller']=$controller;
					$data['action']=$action;
					$qxid=$this->qx->getQxId($data);
					if(in_array($qxid,$this->user['uquanxian'])==false)
					{
						if($array['frmflex']==1)
						{
							$xml="<?xml version=\"1.0\" encoding=\"utf-8\" ?> <droot err=\"1\" ><emsg>您无权访问该内容</emsg>
							</droot>";
							echo $xml;
						}
						else
						{
							Zend_Adver_Js::Goback('您无权访问该内容');
						}
						
						exit;	
					}
				}
				//end
			}
		}
	}
}
?>