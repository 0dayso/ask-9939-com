<?php
/**
  *##############################################
  * @FILE_NAME :AdminMenu
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Mon Jun 22 14:27:43 CST 2009
  *==============================================
  * @Desc :  系统菜单
  *==============================================
  */
class AdminMenu
{
	private $menu;
	public function __construct()
	{
		$this->LoadMenu();
	}

	public function LoadMenu()
	{
		$i=0;
		$menu[$i][]="管理首页";
		//$menu[$i][]="<a href='/manage/Message/man' target='mainFrame'>站内信息管理</a>";
		//$menu[$i][]="<a href='/manage/login/exit' target='_top'>退出系统</a>";
		$i++;
		
		$menu[$i][] = '基本设置';
		//$menu[$i][]="<a href='/manage/Setting/config' target='mainFrame'>站点设置</a>";	
		$menu[$i][]="<a href='/manage/Setting/usergroup' target='mainFrame'>用户组</a>";
		$menu[$i][]="<a href='/manage/Setting/creditrule' target='mainFrame'>积分规则</a>";	
		$menu[$i][]="<a href='/manage/Setting/censor' target='mainFrame'>词语屏蔽</a>";		
		$i++;	
		
		$menu[$i][] = '日志管理';
		$menu[$i][]="<a href='/manage/blog' target='mainFrame'>日志列表</a>";		
		$menu[$i][]="<a href='/manage/category' target='mainFrame'>分类列表</a>";	
		$menu[$i][]="<a href='/manage/category/add' target='mainFrame'>添加分类</a>";	
		$i++;	
		
		$menu[$i][]="相册管理";		
		#$menu[$i][]="<a href='/manage/album' target='mainFrame'>相册列表</a>";
		$menu[$i][]="<a href='/manage/pic/' target='mainFrame'>图片列表</a>";			
		$i++;		
		
		$menu[$i][] = '会员管理';
		$menu[$i][]="<a href='/manage/usermanage' target='mainFrame'>会员列表</a>";		
		$menu[$i][]="<a href='/manage/feed' target='mainFrame'>会员动态</a>";	
		//$menu[$i][]="<a href='/manage/usergroup' target='mainFrame'>用户组列表</a>";
		//$menu[$i][]="<a href='/manage/userpri' target='mainFrame'>用户权限列表</a>";		
		$i++;
		
		
		$menu[$i][] = '关注管理';
		$menu[$i][]="<a href='/manage/attentioncat' target='mainFrame'>关注栏目管理</a>";		
		$menu[$i][]="<a href='/manage/attention' target='mainFrame'>关注点管理</a>";	
		//$menu[$i][]="<a href='/manage/usergroup' target='mainFrame'>用户组列表</a>";
		//$menu[$i][]="<a href='/manage/userpri' target='mainFrame'>用户权限列表</a>";		
		$i++;
		
		$menu[$i][]="部落管理";
		$menu[$i][]="<a href='/manage/buluo' target='mainFrame'>部落列表</a>";		
		$menu[$i][]="<a href='/manage/thread' target='mainFrame'>话题列表</a>";		
		$menu[$i][]="<a href='/manage/threadpost' target='mainFrame'>话题回帖列表</a>";
		$menu[$i][]="<a href='/manage/buluocat/add' target='mainFrame'>添加部落栏目</a>";
		$menu[$i][]="<a href='/manage/buluocat/' target='mainFrame'>部落栏目列表</a>";
		$i++;
		
		$menu[$i][] = '广告推荐';
		$menu[$i][]="<a href='/manage/adsplace' target='mainFrame'>广告位列表</a>";
		$menu[$i][]="<a href='/manage/adsplace/add' target='mainFrame'>添加广告位</a>";
		$menu[$i][]="<a href='/manage/ads' target='mainFrame'>广告列表</a>";
		$menu[$i][]="<a href='/manage/ads/add' target='mainFrame'>添加广告</a>";	
		$menu[$i][]="<a href='/manage/position' target='mainFrame'>推荐位列表</a>";		
		$menu[$i][]="<a href='/manage/position/add' target='mainFrame'>添加推荐位</a>";
		$i++;	
		
		$menu[$i][] = '公告管理';
		$menu[$i][]="<a href='/manage/Notice/' target='mainFrame'>公告列表</a>";		
		$menu[$i][]="<a href='/manage/Notice/add' target='mainFrame'>发布公告</a>";			
		$i++;	
		
		$menu[$i][] = '评论举报';
		$menu[$i][]="<a href='/manage/Comment/' target='mainFrame'>评论</a>";				
		$menu[$i][]="<a href='/manage/Report/' target='mainFrame'>举报</a>";		
		$i++;
		
		$menu[$i][] = '帐号权限管理';		
		$menu[$i][]="<a href='/manage/manage/man' target='mainFrame'>管理员管理</a>";
		$menu[$i][]="<a href='/manage/quanxian/man' target='mainFrame'>用户权限</a>";
		$menu[$i][]="<a href='/manage/juese/man' target='mainFrame'>用户角色</a>";
		$i++;	

		$this->menu=$menu;
	}

	public function GetMainMenu()
	{
		$ret=array();
		foreach($this->menu as $key=>$val)
		{
			$ret[]="<a href='/manage/admin/loadmenu/menu_id/".$key."' target='leftFrame'>".$val[0]."</a>";
		}

		return $ret;
	}

	public function ParseMenuUrl($string)
	{
		preg_match("/href='(.+?)'/is",$string,$matches); 
		$url=$matches[1];
		$str=explode("/",$url);
		$arr['module']=trim($str[1]);
		$arr['controller']=ucfirst(trim($str[2]));
		if($str[3]=="")	$arr['action']="index";
		else  $arr['action']=trim($str[3]);
		
		return $arr;
	}
	
	public function GetSubMenu($key)
	{		
		$menu=$this->menu[$key];
		unset($menu['modules']);
		return $menu;
	}
	
}
?>