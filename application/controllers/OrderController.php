<?php
/**
   *##############################################
   * @FILE_NAME :OrderController.php
   *##############################################
   *
   * @author : 李军锋
   * @MailAddr : 
   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Thu Jun 18 18:00:29 CST 2009
   * @DATE : Thu Jun 18 18:00:29 CST 2009
   *
   *==============================================
   * @Desc : 科室定制
   *==============================================
   */
class OrderController extends Zend_Controller_Action
{
	private $MemberObj = '';
	private $Credit_obj = '';
	private $uid=0;
	private $utype=1;
	private $tmp_user_cookie='';
	
	public function init() {	
    	$this->ViewObj = Zend_Registry::get('view');	
    	
    	Zend_Loader::loadClass('Member',MODELS_PATH);				
    	$this->MemberObj = new Member(); 	
    	
    	Zend_Loader::loadClass('Listask',MODELS_PATH);
		$this->DB_list = new Listask();
		
		$this->tmp_user_cookie = $this->MemberObj->getCookie();
		
		$this->uid = $this->tmp_user_cookie['uid'];
		$this->utype = $this->tmp_user_cookie['uType'];
		parent::init();
	}

	
	
	public function indexAction() {
		if(!$this->uid) {
			echo $this->ViewObj->render('/login.phtml');
		}
		else{
			require(APP_ROOT.'/Keshi_cache.php');
			$this->ViewObj->ks_cache = $CATEGORY;	

			$classid = $this->_getParam('classid'); //全部科室 查询
			if(isset($classid)) $this->ViewObj->classid = 'true';
			$classid = $classid ? $classid : 0;
			
			$daohang = $this->DB_list->getdaohang($classid);//导航
			$this->ViewObj->daohang = $daohang;
			
			$dzjb = $this->DB_list->getkeshi_ljf($classid);//大众疾病 科室 id>30
			$this->ViewObj->dzjb = $dzjb;												

			$gzd = $this->DB_list->getgzd($this->uid,$this->utype);//关注科室数组						
			$this->ViewObj->gzd = $gzd;		
			
			
			$where = " 1";	
						
			$keshi_str = $this->DB_list->getusergzd($this->uid,$this->utype);//取用户的关注点
			if($keshi_str) $where .= " and classid in($keshi_str)";		
			
			$classid_sql = '';
			if($classid){
				$classid_sql = "/classid/$classid";
				$where = "classid in (".$CATEGORY[$classid]['arrchildid'].")";//全部科室 按课时查询
			}
			
			$id = $this->_getParam('id');//点击定制的某个科室时 查询用的id
			if($id){
				$classid_sql = "/id/$id";
				$where = "classid in (".$CATEGORY[$id]['arrchildid'].")";// 点击单个定制科室 
			}
			
			$this->ViewObj->classid_sql = $classid_sql;	
			
			$status = $this->_getParam('s');	
			if(!isset($status)) $status = 4;
			$this->ViewObj->status = $status;
			
			if($status==0) $where .= " and status = $status";//待解决问题生成
			if($status==1) $where .= " and status = $status";//已解决问题生成
			if($status==2) $where .= " and point != 0";///悬赏问题生成
			if($status==4) $where .= " and answernum=0 ";//零回复问题生成;	$status==3 //全部问题					
			
			$p = $this->_getParam('p');
			if(!$p) $p = 1;
			$this->ViewObj->p = $p;
			$ofset = ($p-1)*30;		

			$this->ViewObj->rs = $this->DB_list->List_Ask($where,'id desc',30,$ofset);	
			
			echo $this->ViewObj->render('/list_order.phtml');
		}

	}
	
	public function cengAction(){
		$id = $this->_getParam('id');
		include(APP_ROOT.'/Keshi_cache.php');	
		//组织选择的科室级别关系	
		if($id){
			$arrp = $CATEGORY[$id]['arrparentid'];
			$arrp .= ",$id";
			$arrp = explode(',',$arrp);
			sort($arrp);
			if($arrp){				
				foreach ($arrp as $k=>$v){
					if($v){
						$name .= '<a href="#" onclick="show('.$v.')">'.$CATEGORY[$v]['name'].'</a> > ';
					}
				}
				$name = substr($name,0,-3);
			}				
		}
		//echo $name;exit;
		if(!$name) $name = '尚未选择科室';
		
		//子科室列表
		$keshi = $this->DB_list->getkeshi_ljf($id);//科室
		if($keshi){
			foreach($keshi as $k=>$v){
				$ks .= "<li><a href='#' id='keshi".$k."' onclick=show('".$v['id']."');>".$v['name']."</a></li>";
			}
		}
		if($id == $CATEGORY[$id]['arrchildid']) $over = '您已经选择到底层分类，确认请按“完成”按钮！';
		$arr = array('name'=>$name,'ks'=>$ks,'over'=>$over);
		echo json_encode($arr);
	}
	
	public function loginAction(){
		$this->_info['username'] = trim($this->_getParam('username'));
		$this->_info['password'] = trim($this->_getParam('password'));
		$this->MemberObj->checklogin($this->_info,$msg);
		if($msg=='登录成功！'){
			echo "<script>alert('登陆成功！');location.href='/order/';</script>";
			//echo $this->ViewObj->render('/keshidz.phtml');
		}
		else echo "<script>alert('登陆失败！');location.href='/order/';</script>";;
	}	
	
	public function addAction(){
		include(APP_ROOT.'/Keshi_cache.php');
		$id = intval($this->_getParam('id'));//新科室id
		$fid = intval($this->_getParam('fid'));//序号
		echo '<input type="hidden" name="keshi[]" id="keshi_'.$fid.'" value="'.$id.'" /><a href="#">'.$CATEGORY[$id]['name'].'</a><span>（<a href="#">修改</a>|<a href="#">删除</a>）</span>';
	}
	
	// 删除关注点
	public function delAction(){
		$fid = intval($this->_getParam('fid'));
		echo '<input type="hidden" value="0" id="keshi_'.$fid.'" name="keshi[]"/><a onclick="add('.$fid.')" href="#">点击添加</a><span>（添加关注点）</span>';		
	}
	
	public function postAction(){
		$arr = $this->_getParam('keshi');
		//print_r($arr);exit;
		foreach($arr as $k=>$v){
			$str .= $v.',';
		}
		$str = substr($str,0,-1);
		//exit($str);
		$this->DB_list->updategzd($this->uid,$str,$this->utype);
		header("location:http://ask.9939.com/order/index/ ");
	}
	
	
	
}
?>