<?php
/**
   * 活动
   * @author 林原 2010-09-10
   */
Zend_Loader::loadClass('Ask',MODELS_PATH);
Zend_Loader::loadClass('MemberDetail',MODELS_PATH);  
Zend_Loader::loadClass('Lottery',MODELS_PATH);   
Zend_Loader::loadClass('Product',MODELS_PATH);
Zend_Loader::loadClass('DuiHuan',MODELS_PATH);

class HuodongController extends Zend_Controller_Action {
	
	public $view;  
	private $Member_obj;  //会员
	private $ask_obj; //问题
	private $pro_obj;  //产品
	private $lottery_obj;  //抽奖
	private $duihuan_obj; //兑换
	
	public function init() {
		$this->view = Zend_Registry::get("view");		

		//加载会员类  读取用户的cookie
		Zend_Loader::loadClass('Member',MODELS_PATH);
		$this->Member_obj = new Member();
		
		$tmp_cookie_array = $this->Member_obj->getCookie();
		$this->tmp_uid = intval($tmp_cookie_array['uid']);
		$this->nickname = $tmp_cookie_array['nickname'];
		$this->username = $tmp_cookie_array['username'];
		
		$this->memberDetail_obj = new MemberDetail();
		
		$this->ask_obj = new Ask();
		$this->lottery_obj = new Lottery();
		$this->pro_obj = new Product();
		$this->duihuan_obj = new DuiHuan();
	}
	
	public function indexAction() {
		if($this->nickname) {
			$this->view->nickname = $this->nickname;
		}
		echo $this->view->render("huodong/zadan.phtml");
	}
	
	/**
	 * 所有获奖用户 
	 *
	*/
	public function huojiangAction() {
		if($this->nickname) {
			$this->view->nickname = $this->nickname;
		}
		
		//分页 
		$page = intval($this->_getParam("page"));
		if($page==0) $page = 1;  //页
		$pageSize = 40; //每页50
		
		//获取内容
              include APP_DATA_PATH.'/huojiang.php';
              if($huojiangList) {
              	  $huojiangArr = array_chunk($huojiangList,$pageSize);
              	  $pageCount = count($huojiangArr); //总页数
              }
              if(!$pageCount) $pageCount = 1;
              if($page>=$pageCount) $page = $pageCount;
              
              $this->view->pageCount = $pageCount;
              $this->view->page = $page;
              $this->view->result = $huojiangArr[$page-1];
		
		echo $this->view->render("huodong/huojiang.phtml");
	}
	
	/**
	 * 砸金蛋
	 */
	public function zaAction() {
		$url = '#'; //要跳转到url
		$content = ''; //提示内容
		$status = 2; //状态，0 未登录，1 错误，2 正确
		$linktitle = ''; //链接标题
		$title = '';//提示标题
		//$content = "获得100积分！ \n已累积：10000积分  \n您可以兑换一本图书了！";
		//$content = "获得100积分！ \n已累积：10000积分 ";
		//$arr = array('url'=>$url,'title'=>$title,'content'=>$content,'status'=>$status,'linktitle'=>$linktitle);
		//echo json_encode($arr);
		//exit;
		
		$pointArr = array(5=>20,10=>50,50=>100);
		$za = false;
		
		//判断用户是否登录
		if (!$this->nickname) {
			$status = 0;
		} else {
			//判断是否提问过
			$where = "userid=".$this->tmp_uid;
			$order = "id DESC";
			$askInfo = $this->ask_obj->fetchRow($where,$order);
			$current_num = 1;  //砸蛋次数 每到50 就从 1 开始
			$point = 0;  //砸蛋获得的积分
			$totalPoint = 0; //总积分
			$lastTotalPoint = 0; //上次总积分
			
			if($askInfo) {
				$lotteryInfo = $this->lottery_obj->fetchRow("uid=".$this->tmp_uid,"ltId DESC");
				//判断是否砸过金蛋
				if($lotteryInfo) {
					//判断提问时间是否大于砸蛋时间
					if($lotteryInfo->ltTime>$askInfo->ctime) {
						$title = '对不起';
						$content = '每提问一次才能砸一次,请先提问！';
						$url = 'http://ask.9939.com/asking/index/?backurl=http%3A%2F%2Fask.9939.com%2Fhuodong%2Findex';
						$linktitle = '去提问';
						$status = 1;
					} else {
						//获取当前次数
						$current_num = $lotteryInfo->ltNum+1;
						if($current_num>50) $current_num = 1;
						$lastTotalPoint = $lotteryInfo->ltTotalPoint;
						$za = true;
					}
			    } else {
			    	$za = true;
			    }
			} else {
				$title = '对不起！';
				$content = '每提问一次能才砸一次,请先提问！';
				$url = 'http://ask.9939.com/asking/index/?backurl=http%3A%2F%2Fask.9939.com%2Fhuodong%2Findex';
				$linktitle = '去提问';
				$status = 1;
			}
		}
		if($za) {
			//计算积分
			$point = $pointArr[$current_num] ? $pointArr[$current_num] : 10 ;
			
			//总积分
			$totalPoint = $lastTotalPoint + $point;
			
			$data = array('uid'=>$this->tmp_uid,'username'=>$this->username,'ltPoint'=>$point,'ltNum'=>$current_num,'ltTime'=>time(),'ltTotalPoint'=>$totalPoint);
			$result = $this->lottery_obj->insert($data);
			if($result) {
				$title = '恭喜你！获得'.$point.'积分！';
				$content = "已累积：".$totalPoint." 积分";
				$url = 'http://ask.9939.com/asking/index/?backurl=http%3A%2F%2Fask.9939.com%2Fhuodong%2Findex';
				$linktitle = '继续提问';
				if($totalPoint>=1200) {
					$content = "已累积：".$totalPoint."积分 \n 您可以兑换一本图书了！";
					$url = 'http://ask.9939.com/huodong/duihuan';
					$linktitle = '去兑换';
				}
				$status = 2;
			} else {
				$title = '出错了！';
				$content = "砸蛋失败";
				$url = 'http://ask.9939.com/';
				$linktitle = '返回首页';
				$status = 1;
			}
		}
	/*	$status = 2;
		$title = '恭喜你！获得500积分！';
		$content = "已累积：1000 积分 \n您可以兑换一本图书了！";
		$linktitle = '去兑换>>';*/
		$arr = array('url'=>$url,'title'=>$title,'content'=>$content,'status'=>$status,'linktitle'=>$linktitle);
		echo json_encode($arr);
	}
	
	/**
	 * 积分兑换产品列表
	 */
	public function duihuanAction() {
		if($this->nickname) {
			$this->view->nickname = $this->nickname;
		}
	
		$page = intval($this->_getParam("page"));
		if($page<=0) $page = 1;
		
		$pageSize = 12; //每页显示大小
		
		//总数
		$listCount = $this->pro_obj->getCount('proStatus=1');
		
		//总页数
		$pageCount = ceil($listCount/$pageSize);
		
		if($page>$pageCount) $page = $pageCount;
		
		$limit  = ($page-1)*$pageSize;
		
		$result = $this->pro_obj->fetchAll('proStatus=1','proId desc',$pageSize,$limit);
		if($result) $this->view->result = $result->toArray();
		$this->view->pageCount = $pageCount;
		$this->view->page = $page;
		echo $this->view->render("huodong/duihuan.phtml");
	}
	
	/**
	 * 兑换产品
	 */
	public function huanAction() {
		$status = 0; //状态 0 未登录，1成功，2，积分不足
		$havepoint = 0; //积分
		
		//判断用户是否登录
		if (!$this->nickname) {
			$status = 0;
		} else {
			//获取产品id
			$proId = intval($this->_getParam("proid"));
			//获取产品所需要积分
			$proInfo = $this->pro_obj->fetchRow("proId=$proId");  //产品信息
			if($proInfo) {
				$proPoint = $proInfo->proPoint;
				//获取用户积分
				$lotteryInfo = $this->lottery_obj->fetchRow("uid=".$this->tmp_uid,"ltId DESC");
				if($lotteryInfo) {
					$point = $lotteryInfo->ltTotalPoint;
					if($point<$proPoint) {
						$havepoint = $point;
						$status = 2;
					} else {
						$status = 1;
					}
				} else {
					$havepoint = $point;
					$status = 2;
				}
			} else {
				$status = 3;
			}
		}
		
		$arr = array('status'=>$status,'point'=>$havepoint);
		echo json_encode($arr);
	}
	
	/**
	 * 填写个人信息
	 */
	public function inputinfoAction() {
		//判断用户是否登录
		if (!$this->nickname) {
			Zend_Adver_Js::helpJsRedirect("http://ask.9939.com/huodong/duihuan",0,"对不起，请先登录！");
			exit;
		}
		
		//获取产品id
		$proId = intval($this->_getParam("proid"));
		
		//判断产品是否已经被兑换
		$result = $this->duihuan_obj->fetchRow("proId=$proId");
		if($result) {
			echo $this->view->render("/huodong/yihuan.phtml");
			exit;
		}
		
		//获取产品所需要积分
		$proInfo = $this->pro_obj->fetchRow("proId=$proId");  //产品信息
		if($proInfo) {
			$proPoint = $proInfo->proPoint;  //产品积分
		} else {
			Zend_Adver_Js::helpJsRedirect("http://ask.9939.com/huodong/duihuan",0,"产品不存在或已被删除！");
			exit;
		}
		//获取用户积分
		$lotteryInfo = $this->lottery_obj->fetchRow("uid=".$this->tmp_uid,"ltId DESC");
		if($lotteryInfo) {
			$point = $lotteryInfo->ltTotalPoint;  //用户积分
		} else {
			echo $this->view->render("/huodong/buzu.phtml");
			exit;
		}
		
		//积分不足
		if($point<$proPoint) {
			echo $this->view->render("/huodong/buzu.phtml");
			exit;
		}
		
		//填写，提交 表单
		if($this->getRequest()->isPost()) {
				$data = array();
				$data = $this->_getParam("info");
				$data['uid'] = $this->tmp_uid; //用户id
				$data['username'] = $this->username; //用户名
				$data['proId'] = $proId;  //产品id
				$data['proPoint'] = $proInfo->proPoint; //产品积分
				$data['proName'] = $proInfo->proName; //产品名称
				$data['exTime'] = time(); //时间
				$result = $this->duihuan_obj->insert($data);
				if($result) {
					//修改积分
					$res = $this->lottery_obj->update(array("ltTotalPoint"=>$point-$proPoint),"ltId=".$lotteryInfo->ltId);
					$havepoint = $point-$proPoint;
					$this->view->point = $havepoint;
					echo $this->view->render("/huodong/ok.phtml");
					exit;
				} else {
					Zend_Adver_Js::helpJsRedirect("http://ask.9939.com/huodong/duihuan",0,"兑换失败！");
					exit;
				}
		} else {
			$this->view->info = $proInfo->toArray();
			$this->view->nickname = $this->nickname;
			echo $this->view->render("/huodong/inputinfo.phtml");
		}
	}
	

}

?>