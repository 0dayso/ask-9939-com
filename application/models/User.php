<?php
 /**
  *##############################################
  * @FILE_NAME :User.php
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Fri Jun 19 17:12:03 CST 2009
  *==============================================
  * @Desc :  用户操作类
  *==============================================
  */

Zend_Loader::loadClass("Juese",MODELS_PATH);
class User extends QModels_Ask_Table 
{
	
	/**
	* @Desc 
	* @param
	* @param
	* @param
	* @return 
	*/
	
	 protected $_name="admin";
	 
	 public function checkHas($data)
 	 {
	 	$uname=trim($data['uname']);
		$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("uname = ?", $uname);
		    $row = $this->fetchRow($where);
		    if($row->userid>0)
		    {
		    	return true;
		    }  
		    else 
		    {
		    	return false;
		    }
 		}
	
	/**
	* @Desc 验证登陆
	* @param $data array
	* @return bool
	*/
	public function Checklogin($data,&$error)
	{
		$username=$data['username'];
		$pwd=$data['password'];
		$db=$this->getAdapter();
    	$where[] = $db->quoteInto("uname = ?", $username);
	    $where[] = $db->quoteInto("upwd = ?", md5($pwd));   
    	$row = $this->fetchRow($where);
    	if($row->userid)
    	{
    		$manage = new Zend_Session_Namespace('manage');
    		$juese_obj=new Juese();
    		$juese_one=$juese_obj->getOne($row->idjs);
    		$row->uquanxian=(array)unserialize($row->uquanxian);
    		$row->uquanxian=array_merge((array)$row->uquanxian,(array)$juese_one->jsquanxian);
    		
    		$row->jsmenus=unserialize($row->jsmenus);
    		$row->jscategory=unserialize($row->jscategory);
			$manage->unlock();
    		$manage->user = $row;
			$manage->lock();
    		return true;
    	}
    	else
    	{
    		$error="登录失败";
    	}
	}
	
	/**
	* @Desc 退出系统
	* @param void
	* @return bool
	*/
	public function Logout()
	{
		$manage = new Zend_Session_Namespace('manage');
		if($manage->isLocked())
		{
			$manage->unlock();
		}
		
		return $manage->unsetAll();
	}
	
	/**
	* @Desc 验证用户是否已经登录
	* @param void
	* @return bool
	*/
	public function CheckIsLogin()
	{
		try {
			Zend_Loader::loadClass("Asession",MODELS_PATH);
			$sess_obj=new Asession();
			$sess=$sess_obj->Get_Manage_Session();
			if(intval($sess['userid'])>0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	
	public function add($data,&$error)
	 {
	 	$filters = array(
		    '*'=>'StringTrim','StripTags'); 
		$validators = array(
		    'uname'   =>  array('Alnum','NotEmpty'),
		    'nickname'=> 'NotEmpty',
		    'utel'    => 'Digits',
		    'uemail'  => 'EmailAddress',
		    'uip'     => 'Ip',
		    'upwd'    => array(
		  				   'NotEmpty',
		    			   array('Identical',$data['upwd1'])),
		    'idjs'    =>'Digits',
		    'idbm'    =>'Digits'
		);
		
		//更新数据没写密码
		//去掉密码规则
		if(intval($data['userid'])>0)
		{
			if($data['upwd']=="")
			{
				unset($validators['upwd']);
				unset($data['upwd']);
			}
		}
		
		try {
			
			$input = new Zend_Filter_Input($filters, $validators,$data);
			
			if ($input->isValid("uname")==false) {
			  	$error="用户名错误";
			  	//return false;
			}
			elseif($input->isValid('nickname')==false)
			{
				$error="昵称不能为空";
				//return false;
			}
			elseif($input->isValid('utel')==false)
			{
				$error="联系电话错误";
				//return false;
			}
			elseif($input->isValid('uemail')==false)
			{
				$error="Email错误";
				//return false;
			}
			elseif($input->isValid('upwd')==false)
			{
				if($data['userid']==0)
				{
					$error="用户密码错误";
					//return false;
				}
			}
			elseif($input->isValid('idjs')==false)
			{
				$error="请选择角色";
				//return false;
			}
			
			if($error) return false;
			else
			{
					$upload = new Zend_File_Transfer_Adapter_Http();
					$path=APP_ROOT."/upload/upic";

					$upload->setDestination($path,'upic');
					
				
					//设置文件大小
					$upload->addValidator('FilesSize',
											true,
											array('min' => 100,
											'max' => 10000000,
											'bytestring' => true));
					
					//设置文件类型
					$upload->addValidator('Extension',
											true,
											array('extension1' => 'jpg,gif,bmp,JPG,GIF,BMP,jpeg,JPEG,png,PNG',
											'case' => true));
					//获取上传的文件名
				
					
					
					if($upload->receive('upic'))
					{
						if($upload->isValid('upic'))
						{
							$error="上传文件类型错误";
							return false;
						}
						else
						{
							$data['upic']=$upload->getFileName('upic');
							$data['upic']=str_replace(APP_ROOT,'',$data['upic']);
						}
					}
				
				$data['jscategory']=serialize($data['jscategory']);
 				$data['jsmenus']=serialize($data['jsmenus']);
 		
				$ret=$this->_getCols();
				$arr=array();
				foreach($ret as $val)
				{
					if($data[$val]<>"")
					{
						$arr[$val]=$data[$val];
					}
				}
				
				$arr['ustate']=1;
				$arr['utime']=time();
			
				
				//$arr['uquanxian']=serialize($arr['uquanxian']);
				if(intval($arr['userid'])==0)
				{
					if($this->checkHas($data))
		 			{
		 				$error="用户已存在";
		 				return false;
					}
					
					$arr['upwd']=md5($arr['upwd']);
					
					return $this->insert($arr);
				}
				else
				{
					if($arr['upwd']<>"") $arr['upwd']=md5($arr['upwd']);
					
					$db=$this->getAdapter();
		    		$where[] = $db->quoteInto("userid = ?", $arr['userid']);
		    		unset($arr['userid']);
		    		
					return $this->update($arr,$where);
				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
 	}
 	
 	/**
 	* @Desc 得到所有用户列表
 	* @param void
 	* @return array
 	*/
 	public function GetAll($data=array())
 	{
 		$idbm=intval($data['idbm']);
 		$idjs=intval($data['idjs']);
 		if($idbm>0) $sqlwhere=" and idbm=".intval($idbm);
 		if($idjs>0) $sqlwhere=" and idjs=".intval($idjs);
 		$db=$this->getAdapter();
 		$sql="SELECT userid,uname,nickname From admin where 1 ".$sqlwhere;
 		$ret=$db->fetchAll($sql);
 		$array=array();
 		foreach($ret as $val)
 		{
 			$array[$val['userid']]=$val['nickname'];
 		}
 		
 		return $array;
 	}
 	
	/**
	* 获取用户昵称
	*
	* @param 用户id int
	* @return 用户昵称 str
	*/
	public function get_UserName($id) {
		if (!$id = (int)$id) return null;
		$row = $this->fetchRow('userid='.$id);
		return $row->nickname;
	}
 	
 	
 	public function getList($data=array())
	{
	 	$page=intval($data['page']);
	 	if($data['pagesize']>0&&$data['page']>0) $offset=($page-1)*$data['pagesize'];
	 	else 
	 	{
	 		$offset=0;
	 	}
	 		
	 	$username=trim($data['uname']);
	 	$idjs=intval($data['idjs']);
	 	//$idbm=intval($data['idbm']);
	 	
	 	$db=$this->getAdapter();
	 	$sql="select A.* ,C.jsname from admin A left join admin_role C on A.idjs=C.idjs where 1";
	 	$sql_c="select count(*) as num from admin A left join admin_role C on A.idjs=C.idjs where 1";
	 	if($username<>"") $where=" and A.uname like '%".$username."%'";
	 	if($idjs>0) $where.=" and A.idjs=".$idjs;
	 	//if($idbm>0) $where.=" and A.idbm=".$idbm;
	 	$sql.=$where;
	 	$sql_c.=$where;
		
	 	//echo $sql;exit;
	 	$total=$db->fetchRow($sql_c);
	 	$total=$total['num'];
	 	$ret['total']=$total;
	 	$sql.=" limit ".$data['pagesize']." offset ".$offset;	
		$list=$db->fetchAll($sql);
		$ret['list']=$list;
		
		
	 	return $ret;
	 }
	 
	 
	public function getOne($id)
	{
		if(intval($id)==0) return false;
	 	$rows=$this->find($id);
	 	$row=$rows->current();
	 	
	 	if($row->userid>0)
	 	{
	 		$row->uquanxian=unserialize($row->uquanxian);
	 		$row->jscategory=unserialize($row->jscategory);
 			$row->jsmenus=unserialize($row->jsmenus);
 			
	 		if(count($row->uquanxian)==0) $row->uquanxian=array();
	 		if(count($row->jscategory)==0) $row->jscategory=array();
 			if(count($row->jsmenus)==0) $row->jsmenus=array();
 			
	 		return $row;
	 	}
	 	else
	 	{
	 		return null;
	 	}
	 }
}
?>