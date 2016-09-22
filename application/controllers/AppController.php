<?php
/**
 *##############################################
 * @FILE_NAME :AppController.php
 *##############################################
 *
 * @author : 赵楠
 * @MailAddr : 360807702@qq.com
 * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
 * @PHP Version :  Ver 5.21
 * @Apache  Version : Ver 2.20
 * @MYSQL Version : Ver 5.0
 * @Version : Ver Thu Jun 18 18:00:29 CST 2009
 * @DATE : Thu Jun 18 18:00:29 CST 2009
 *
 *==============================================
 * @Desc : 手机APP管理
 *==============================================
 */

Zend_Loader::loadClass("Keshi",MODELS_PATH);
Zend_Loader::loadClass('Ask',MODELS_PATH);
Zend_Loader::loadClass('Answer',MODELS_PATH);
Zend_Loader::loadClass("KeshiFushu",MODELS_PATH);
Zend_Loader::loadClass("App",MODELS_PATH);
class AppController extends Zend_Controller_Action {
	private $AskObj		= '';
	private $AnswerObj	= '';
    private $DB_App;
	private $keshiFushu;
    
    public function init() {
		$this->DB_App = new app();
        
        parent::init();
    }
    
    public function indexAction(){
    	//die('123456');
    }
    /**
     * 
     *科室列表
     * 	请求参数
     * 		无
	 * 	返回参数
	 * 		id					科室编号
     * 		name				科室名称
     * 		child				是否有子集			0:无,1:有
     * 		arrchildid			子集科室编号
     * 		description			科室简介
     * 		keywords			科室关键词
     * 
     */
    public function getkeshilistAction() {
		//http://ask.9939.com/app/getkeshilist
    	//$id				= (int)trim($this->getRequest()->getParam());   	
    	$result			= $this->DB_App->getKeshi();
    	//$this->Ptr($result);exit;
    	echo $this->zh2($result);
    }

    /**
     * 
     *科室问答列表
     * 	请求参数
     * 		sectionId			科室编号
	 *		lastId				标记，分页ID，返回小于此问题编号之后 10个问题
	 * 	返回参数
	 * 		userId				用户编号
	 * 		doctorId			医生编号
	 * 		groupId				问题编号
	 * 		doctorName			医生姓名
	 * 		doctorImageUrl		医生头像
	 * 		hospitalName		医生所在医院
	 * 		dateTime			最后回复时间
	 * 		diagnoseViewList	提问和回答的内容，数组形式，有个键值区分提问还是回答
	 * 			id					编号
	 * 			type				类型[提问:question,回应:answer]
	 * 			contentType			内容类型[文本:text]
	 * 			contentText			问题内容
	 * 			datetime			时间
     *		注释：diagnoseViewList内是数组集，并非单个问题或回答，是问题与回答的集合
     */
	public function getkeshiaskAction(){
		//http://ask.9939.com/app/getkeshiask?sectionId=32&lastId=4351871
    	$classid		= (int)trim($this->getRequest()->getParam('sectionId'));
    	$lastId			= (int)trim($this->getRequest()->getParam('lastId'));
		$data			= array();
		//获取科室问题
		$result			= $this->DB_App->getKeshiList($classid,$lastId);
		//$this->Ptr($result);exit;
		if(!empty($result)){
			foreach($result as $key => $val){
				$answerInfo						= $this->DB_App->getAnswerSort($val['id']);
				//$this->Ptr($answerInfo);exit;
				$doctorInfo						= $this->DB_App->getdoctorId($answerInfo['userid']);
				$data[$key]['userId']			= $val['userid'];
				$data[$key]['doctorId']			= $doctorInfo['uid'];
				$data[$key]['groupId']			= $val['id'];
				$data[$key]['doctorName']		= $doctorInfo['truename'];
				$data[$key]['doctorImageUrl']	= $doctorInfo['pic'];
				$data[$key]['hospitalName']		= $doctorInfo['doc_hos'];
				$data[$key]['dateTime']			= $doctorInfo['lastpost'];
				$data[$key]['diagnoseViewList'] = array(
					array(
						'id'						=> $val['id'],
						'type'						=> 'question',
						'contentType'				=> 'text',
						'contentText'				=> $val['content'],
						'datetime'					=> $val['ctime']
					),
					array(
						'id'						=> $answerInfo['id'],
						'type'						=> 'answer',
						'contentType'				=> 'text',
						'contentText'				=> $answerInfo['content'],
						'datetime'					=> $answerInfo['addtime']
					)
				);
			}
		}

		//根据问题获取用户，医生信息
		//获取回答
		//组装

    	//$this->Ptr($data);
    	echo $this->zh2($data);
	}

    /**
     * 
     *常见疾病
     * 	请求参数
     * 		无
	 * 	返回参数
	 * 		id					科室编号
     * 		name				名称
     * 		description			描述
     */
	public function getchangjianjbAction(){
		//http://ask.9939.com/app/getchangjianjb
		$data			= $this->DB_App->getChangJianJB();
		//$this->Ptr($data);
		echo $this->zh2($data);
	}    
    
    /**
     * 
     *获取问题信息
     *	请求参数
     *		askid				问题编号
     *	返回参数
     *		ask					array
     *			id					问题编号
     *			classid				当前所属科室编号
     *			class_level1 		一级所属科室编号
     *			class_level2 		二级所属科室编号
     *			class_level3 		三级所属科室编号
     *			title				标题
     *			content				内容
     *			ctime				发表时间
     *			userid				用户编号
     *			isReal				是否真实
     *			age					年龄
     *			sexnn				性别[1:男 2:女]
     *		answer				array
     *			id					回复编号
     *			askid				问题编号
     *			userid				用户编号
     *			content				内容
     *			addtime				回复时间
     */
    public function getaskAction(){
    	//http://ask.9939.com/app/getask?askid=4288216
    	$askid			= (int)trim($this->getRequest()->getParam('askid'));
    	$data			= array();
    	$data['ask']	= $this->DB_App->getAskInfo($askid);
    	$data['answer']	= $this->DB_App->getAnswerList($askid);
    	//$this->Ptr($data);
    	echo $this->zh3($data);
    }

	/**
     * 
     *个人问诊详细
     * 	请求参数
     * 		userid				用户编号
     * 		page				页码
     * 		num					每页显示条数
	 * 	返回参数
	 * 		id					问题编号
	 * 		classid				所属科室编号
	 * 		class_level1		一级科室编号
	 * 		class_level2		二级科室编号
	 * 		class_level3		三级科室编号
	 * 		title				标题
	 * 		content				内容
	 * 		citme				添加时间
	 * 		userid				用户编号
	 * 		age					年龄
	 * 		sexnn				性别
     * 
     */
	public function getuseraskAction(){
    	//http://ask.9939.com/app/getuserask?userid=384153&page=1&num=10
    	$userid			= (int)trim($this->getRequest()->getParam('userid'));
    	$page			= (int)trim($this->getRequest()->getParam('page'));
    	$num			= (int)trim($this->getRequest()->getParam('num'));
    	if(!$page){
    		$page		= 1;
    	}
    	if(!$num){
    		$num		= 10;
    	}

    	$limit			= $num * ($page - 1);
    	$result			= $this->DB_App->getUserAskList($userid,$limit,$num);
    	//$this->Ptr($result);
    	echo $this->zh2($result);
		//echo json_encode($result);
    }
    
	/**
     * 
     *用户提问总数
     * 	请求参数
     * 		userid				用户编号
	 * 	返回参数
	 * 		num					提问个数
     * 
     */    
    public function getuseraskcountAction(){
    	//http://ask.9939.com/app/getuseraskcount?userid=384153
    	$userid			= (int)trim($this->getRequest()->getParam('userid'));
    	$result			= $this->DB_App->getUserAskCount($userid);
    	//$this->Ptr($result);
    	echo $this->zh($result);
    }
    
	/**
     * 
     *判断用户名是否存在
     * 	请求参数
     * 		username			用户名称
	 * 	返回参数
	 * 		num					个数[0:不存在 ,非0:存在]
     * 
     */
    public function isuserAction(){
    	//http://ask.9939.com/app/isuser?username=liangshuang@9939.com
    	$username		= trim($this->getRequest()->getParam('username'));
    	$result			= $this->DB_App->existsUsername($username);
    	//$this->Ptr($result);
    	echo $this->zh($result);
    }
    
	/**
     * 
     *提出一个文本的问题
     * 	请求参数
     * 		userid				用户编号
     * 		title				标题
     * 		content				内容
     * 		classid				所属科室
	 * 	返回参数
	 * 		num					问题编号
     * 
     */
    public function addtextaskAction(){
		//http://ask.9939.com/app/addtextask?uid=384153&title=111111aaaa&content=aaaaaaaaaa&classid=56
		    	
    	$uid			= (int)trim($this->getRequest()->getParam('uid'));
		$title			= trim($this->getRequest()->getParam('title'));
    	$content		= trim($this->getRequest()->getParam('content'));
    	$classid		= (int)trim($this->getRequest()->getParam('classid'));
    	if(!$uid){
    		$data			= array("error" => "用户ID不能为空");
		    echo $this->zh($data);
			exit;    		
    	}
		if(strlen($title)<2) {		#提问标题
		    $data			= array("error" => "标题长度必须大于2");
		    echo $this->zh($data);
			exit;
		}
		if(!preg_replace("/\s/", '', $content)) {		#问题描述
		    $data			= array("error" => "请填写问题描述");
		    echo $this->zh($data);
			exit;
		}
		if(!$classid) {			#科室
		    $data			= array("error" => "请选择科室");
			echo $this->zh($data);
			exit;
		}

		if($title == '请输入您的问题标题'|| $title == '填写你的问题,立即为你解答') {
			$title = mb_substr($content,0,10,'utf-8').'...';
		}
		
		if($uid){
		    $result			= $this->DB_App->existsUserAsk($uid,$title);
		    if(!empty($result)){
            	$data		= array("error" => "请不要连续提问相同问题");
            	echo $this->zh($data);
            	exit;
		    }
		}else{
			$data			= array('error' => "请先登录");
			echo $this->zh($data);
		}
		
		$param['userid'] = $uid;
		$param['status'] = 0;
		
		if($_SERVER['HTTP_CDN_SRC_IP']){
			$param['ip'] = $_SERVER['HTTP_CDN_SRC_IP'];
		}elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
			$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$param['ip'] = $_SERVER['REMOTE_ADDR'];
        }
       
		$keshiTable = new Keshi();
        $keshi = $keshiTable->fetchAll("id={$classid}");
		if (isset($keshi[0])) {
			$param['class_level1'] = $keshi[0]['class_level1'];
			$param['class_level2'] = $keshi[0]['class_level2'];
			$param['class_level3'] = $keshi[0]['class_level3'];
		}
		$param['ctime'] = time();
		$param['title'] = $title;
		$param['content'] = $content;
		
		$this->AskObj = new Ask();
		$id = $this->AskObj->add($param);
		echo json_encode(array("num" => $id));
		exit;
    }
    
	/**
     * 
     *添加回复
     * 	请求参数
     * 		userid				用户编号
     * 		askid				问题编号
     * 		content				内容
	 * 	返回参数
	 * 		num					回答编号
     * 
     */
	public function addanserAction(){
    	//http://ask.9939.com/app/addanser?uid=789004&askid=4358284&content=aaaaaaaaaa
		try {

	    	$uid			= (int)trim($this->getRequest()->getParam('uid'));
			$askid			= (int)trim($this->getRequest()->getParam('askid'));
	    	$content		= trim($this->getRequest()->getParam('content'));
			
			############不可对  自己提的问题  做回复 处理 ###################
			$this->AskObj = new Ask();
			$result = $this->AskObj->get_one($askid);
			//var_dump($result);
			if($result['userid'] == $uid) {
				$data		= array("error" => "抱歉！不可以对自己的问题回复！！");
            	echo $this->zh($data);
            	exit;
			}
			############不可对  自己提的问题  做回复 处理 ###################
			
			//验证字符串是否为空 
			if(empty($content)) {
				$data		= array("error" => "内容不能为空！");
            	echo $this->zh($data);
				exit;
			}

			//添加回复顺序 LinYuan
			$row = $this->DB_App->getAnswerSort($askid);

			if(!empty($row)) {
				$param['sort'] = $row['sort']+1;
			} else {
				$param['sort'] = 1;
			}
			
			if($_SERVER['HTTP_CDN_SRC_IP'])
				$param['ip'] = $_SERVER['HTTP_CDN_SRC_IP'];
			elseif($_SERVER['HTTP_X_FORWARDED_FOR'])
				$param['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else
				$param['ip'] = $_SERVER['REMOTE_ADDR'];
			
			$param['askid']		= $askid;
			$param['userid']	= $uid;
			$param['content']	= $content;
			$param['addtime']	= time();
						
			//$this->Ptr($param);exit;

			$this->AnswerObj	= new Answer();
			$id					= $this->AnswerObj->addAnswer($param);
			
			$this->DB_App->updateAnswerNum($askid);
			echo json_encode(array("num" => $id));
			exit;
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}
    
	//提出一个图片的问题
	public function addimgaskAction(){
		
	}
	//提出一个音频的问题
	public function addvoiceaskAction(){
		
	}

	/**
     * 
     *疾病库搜索
     * 	请求参数
     * 		kw				搜索词		[关键词请先转码urlencode][无值:返回所有]
     * 		id				疾病编号
     * 		order			排序[1:升序,2:倒序]
	 * 	返回参数
	 * 		contentid		疾病编号
	 * 		title			名称
	 * 		thumb			缩略图url
	 * 		keywords		关键词
	 * 		description		描述
	 * 		capital			首字母
	 * 		rename			别名
     * 
     */
	public function getdiseasesearchAction(){
		//kw无参数，返回全部
		//http://ask.9939.com/app/getdiseasesearch?kw=1&order=2
		$kw				= urldecode((trim($this->getRequest()->getParam('kw'))));
		$id				= (int)trim($this->getRequest()->getParam('id'));
		$order			= (int)trim($this->getRequest()->getParam('order'));
		
		$field			= "a.contentid,a.title,concat('http://www.9939.com/',a.thumb) as thumb,a.keywords,a.description,b.capital,b.`rename`";
		$where			= "where a.contentid = b.contentid and a.type = 1 and a.catid=1654";
		
		if(!empty($kw)){
			$where		.= " and a.title like '%$kw%'";
		}
		if($id){
			$where		.= " and a.contentid = '$id'";
		}
		if($order == 2){
			$order_type	= " order by a.contentid desc";
		}else{
			$order_type	= " order by a.contentid asc";
		}
		
   		$result			= $this->DB_App->getDiseaseList($field,$where,$order_type);
    	//$this->Ptr($result);
    	echo $this->zh2($result);
	}

	//添加新浪用户
	
	/**
     * 
     *新闻分类列表
     * 	请求参数
     * 		无
	 * 	返回参数
	 * 		catid			分类编号
	 * 		arrchildid		子集编号
	 * 		catname			名称
	 * 		url				链接地址
     * 
     */	
	public function getnewcategoryAction(){
		//http://ask.9939.com/app/getnewcategory
		//$pid			= trim($this->getRequest()->getParam('pid'));
		$catid			= '9456';
		$result			= $this->DB_App->getCatItem($catid);
		$this->Ptr($result);exit;
		$cat_id_str		= $result[0]['arrchildid'];
		$result			= $this->DB_App->getCatItem($cat_id_str);
				
		//$this->Ptr($result);
		echo $this->zh2($result);
	}

	/**
     * 
     *所有新闻列表
     * 	请求参数
     * 		s			分类编号
     * 		page		页码
     * 		num			每页显示条数
	 * 	返回参数
	 * 		articleid	文章编号
	 * 		catid		分类编号
	 * 		title		标题
	 * 		thumb		缩略图url
	 * 		keywords	关键词
	 * 		description	描述
	 * 		url			文章缓存地址
	 * 		userid		用户编号
	 * 		username	用户名称
	 * 		inputtime	添加时间
	 * 		updatetime	更新时间
     */
	public function getnewAction(){
		//http://ask.9939.com/app/getnew?s=9456,9457,9458,9459,9460,9461,9462,9463,9464,9465,9466,9467,9468,9469,9470,9471,9472,9473,9474,9475,9476,9477,9478,10675,10676,10677,10678,10785,10786&limit=0&num=10
		$cat_id_str		= trim($this->getRequest()->getParam('s'));
    	$page			= (int)trim($this->getRequest()->getParam('page'));
    	$num			= (int)trim($this->getRequest()->getParam('num'));
    	if(!$page){
    		$page		= 1;
    	}
    	if(!$num){
    		$num		= 30;
    	}
    	$limit			= $num * ($page - 1);
		$result			= $this->DB_App->getArticleList($cat_id_str,$limit,$num);	
		//$this->Ptr($result);
		echo $this->zh2($result);
	}
	
	/**
     * 
     *用户信息
     * 	请求参数
     * 		uid			用户编号
	 * 	返回参数
	 * 		uid			文章编号
	 * 		uType		用户类型[1:普通用户,2:医生]
	 * 		nickname	昵称
	 * 		username	用户名称
	 * 		email		邮箱
	 * 		credit		等级积分
	 * 		sale_credit	消费积分
	 * 		from		注册来源
	 * 		dateline	注册时间
	 * 		lastlogin	最后一次登录时间
	 * 		pic			电脑端头像
	 * 		wappic		手机端头像
	 * 		ip			注册IP地址
	 * 		groupname	会员等级
	 * 		gender		性别[1:男 ,2:女]
	 * 		telephone	联系电话
	 * 		address		地址
	 * 		qq			QQ号
	 * 		birthday	生日
	 * 		blood		血型[1:A,2:B,3:AB,4:O,其他:未知]
	 * 		marriage	结婚状态[1:未婚 , 非1:已婚]
	 * 		age			年龄
	 * 		kd_address	快递地址
     */
	public function getuserinfoAction(){
		//http://ask.9939.com/app/getuserinfo?uid=764625
		$uid			= (int)trim($this->getRequest()->getParam('uid'));
		$userInfo		= $this->DB_App->getUserBase($uid);
		if($userInfo['uType'] == 1){
			$userDetail			= $this->DB_App->getUserDetail_1($userInfo['uid']);
		}elseif ($userInfo['uType'] == 2){
			$userDetail			= $this->DB_App->getUserDetail_2($userInfo['uid']);
		}

		if(!empty($userDetail)){
			$userInfoDetail		= array_merge($userInfo,$userDetail);
			if($userInfo['uType'] == 1){
				$blood				= '';
				switch ($userInfoDetail['blood']){
					case 1:$blood	= 'A型';break;
					case 2:$blood	= 'B型';break;
					case 3:$blood	= 'AB型';break;
					case 4:$blood	= 'O型';break;
					default:
						$blood	= '未知';break;
				}
				$userInfoDetail['blood']	= $blood;
			}
		}else{
			$userDetail			= array(
				'age'				=> '',
				'hight'				=> '',
				'weight'			=> '',
				'blood'				=> '',
				'marriage'			=> '',
				'qq'				=> '',
				'birthday'			=> '',
				'gender'			=> 2
			);
			$userInfoDetail		= array_merge($userInfo,$userDetail);
		}
				
		//$this->Ptr($userInfoDetail);
		echo $this->zh($userInfoDetail);
	}
	
	/**
     * 
     *注册用户
     * 	请求参数
     * 		username	用户名称
     * 		password	用户密码
     * 		email		邮箱
     * 		nick		昵称
	 * 	返回参数
	 * 		code		状态码
	 * 		msg			状态名称
	 * 						0:注册失败
	 * 						1:注册成功,用户编号
	 * 						2:用户长度不够		4 - 16
	 * 						3:密码长度不够		6 - 16
	 * 						4:昵称不能为空
	 * 						5:邮箱不能为空
     */
	public function registerAction(){
		//http://ask.9939.com/app/register?username=aaabbb1&password=123123&email=111@qq.com&nick=%E5%93%88&utype=1
		$user_name				= trim($this->getRequest()->getParam('username'));
		$user_pwd				= trim($this->getRequest()->getParam('password'));
		$email					= trim($this->getRequest()->getParam('email'));
		$nick_name				= urldecode(trim($this->getRequest()->getParam('nick')));
		$utype					= (int)trim($this->getRequest()->getParam('utype'));
		$ip						= "";

		$data					= array(
			'code'					=> "",
			'msg'					=> ""
		);
		
		if($_SERVER['HTTP_CDN_SRC_IP']){
			$ip					= $_SERVER['HTTP_CDN_SRC_IP'];
		}elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
			$ip					= $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip					= $_SERVER['REMOTE_ADDR'];
        }
        		
		if(!(strlen($user_name) >= 4 && strlen($user_name)<= 16)){
			$data['code']		= 2;
			$data['msg']		= '用户名长度不够';
		}else if(!(strlen($user_pwd) >=6 && strlen($user_pwd) <= 16)){
			$data['code']		= 3;
			$data['msg']		= '密码长度不够';
		}else if(!(strlen($email) >0)){
			$data['code']		= 5;
			$data['msg']		= '邮箱不能为空';
		}else{
			$userResult			= $this->DB_App->existsUsername($user_name);

			if($userResult['uid'] > 0){
				$data['code']	= 5;
				$data['msg']	= '用户名存在';
			}else{	
				$user_data			= array(
					'uType'				=> $utype,
					'nickname'			=> empty($nick_name) ? $user_name : $nick_name,
					'username'			=> $user_name,
					'password'			=> md5($user_pwd),
					'email'				=> $email,
					'dateline'			=> time(),
					'lastlogin'			=> time(),
					'from'				=> 'app',
					'ip'				=> $ip
				);
				
				//$this->Ptr($user_data);exit;
				
				$newUserId		= $this->DB_App->addUser($user_data);
				if($newUserId > 0){
					$data['code']		= 1;
					$data['msg']		= $newUserId;
				}else{
					$data['code']		= 0;
					$data['msg']		= '注册失败';
				}
			}
		}
		echo $this->zh($data);
		exit;
	}

	
	/**
     * 
     *登录用户
     * 	请求参数
     * 		sinaid		新浪ID
     * 		nick		昵称
     * 		sexnn		性别[1:男,2:女]
     * 		address		地址
	 *		userpass	用户密码[用户类型1,2需要传参]
	 * 		uType		用户类型[1:普通用户,2:医生,3:QQ,4:新浪]
	 * 	返回参数
	 * 		code		状态码
	 * 		msg			状态名称
	 * 						0:登录失败
	 * 						1:登录成功,用户编号
     */
	public function loginAction(){
		//http://ask.9939.com/app/login?sinaid=11111111111&nick=abcd&sexnn=1&address=111bbb&utype=4
		$utype					= (int)trim($this->getRequest()->getParam('utype'));
		if($utype == 4){
			$user_name				= (int)trim($this->getRequest()->getParam('sinaid'));
			$nick_name				= trim($this->getRequest()->getParam('nick'));
			$sexnn					= (int)trim($this->getRequest()->getParam('sexnn'));
			$address				= trim($this->getRequest()->getParam('address'));

			$data				= $this->sinaAuth($user_name,$nick_name,$sexnn,$address,$utype);
			//$this->Ptr($data);
		}else{
			$user_name				= trim($this->getRequest()->getParam('username'));
			$password				= md5(trim($this->getRequest()->getParam('userpass')));
			$userResult				= $this->DB_App->authUsername($user_name,$password);
			$data					= array(
				'code'					=> "",
				'msg'					=> ""
			);
			if($userResult['uid'] > 0){
				$data['code']		= 1;
				$data['msg']		= $userResult['uid'];
			}else{	
				$data['code']		= 0;
				$data['msg']		= '登录失败';
			}
		}
		//$this->Ptr($data);
		echo $this->zh($data);
		exit;
	}
    
	//新浪认证
	private function sinaAuth($user_name = '' ,$nick_name ,$sexnn = 2,$address = ''){
		$userResult				= $this->DB_App->existsUsername($user_name);
		$ip						= "";

		$data					= array(
			'code'					=> "",
			'msg'					=> ""
		);
		
		if($_SERVER['HTTP_CDN_SRC_IP']){
			$ip					= $_SERVER['HTTP_CDN_SRC_IP'];
		}elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
			$ip					= $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip					= $_SERVER['REMOTE_ADDR'];
        }

		if($userResult['uid'] > 0){
				$data['code']		= 1;
				$data['msg']		= $userResult['uid'];
		}else{	
			$user_data			= array(
				'uType'				=> 4,
				'nickname'			=> $nick_name,
				'username'			=> $user_name,
				'password'			=> md5(time()),
				'email'				=> '',
				'dateline'			=> time(),
				'lastlogin'			=> time(),
				'from'				=> 'app',
				'ip'				=> $ip
			);
			
			//$this->Ptr($user_data);exit;
			
			$newUserId		= $this->DB_App->addUser($user_data);
			if($newUserId > 0){
				$data['code']		= 1;
				$data['msg']		= $newUserId;
			}else{
				$data['code']		= 0;
				$data['msg']		= '注册失败';
			}
		}
		return $data;
	}


    private function zh($data){
		return json_encode($data);
		/*
    	foreach( $data as $key => $value){  
	        $data[$key] = urlencode($value);  
	    }
		return urldecode(json_encode($data));
		*/
	}
	
	private function zh2($data){
		return json_encode($data);
		/*
	    foreach( $data as $key => $value){ 
	    	foreach($value as $k => $v){ 
	        	$data[$key][$k] = urlencode($v);
	    	} 
	    }
		return urldecode(json_encode($data));
		*/
	}
    
	private function zh3($data){
		return json_encode($data);
		/*
	    foreach( $data as $key => $value){ 
	    	foreach($value as $k => $v){ 
	    		foreach($v as $sk => $sv){
	        		$data[$key][$k][$sk] = urlencode($sv);
	    		}
	    	} 
	    }
		return urldecode(json_encode($data));	
		*/
	}
	
   private function Ptr($data){
    	echo '<pre>';
    	print_r($data);
    	echo '</pre>';
    }
    
	public function __call($name, $arguments){
		$data			= array(
			'error'			=> 'function not exists'
		);
		echo json_encode($data);exit;
	}    
}
?>