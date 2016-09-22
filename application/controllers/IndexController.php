<?php
/**
   *##############################################
   * @FILE_NAME :IndexController.php
   *##############################################
   *
   * @author : ljf
   * @MailAddr :licaption@163.com
   * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
   * @PHP Version :  Ver 5.21
   * @Apache  Version : Ver 2.20
   * @MYSQL Version : Ver 5.0
   * @Version : Ver Tue Sep 15 13:53 CST 2009
   * @DATE : Tue Sep 15 13:53 CST 2009
   *
   *==============================================
   * @Desc :  问答首页控制器
   *==============================================
   */
Zend_Loader::loadClass('Ask',MODELS_PATH);
class IndexController extends Zend_Controller_Action
{
    private $AskObj = '';
	public function init()
	{
		$this->view = Zend_Registry::get("view");
        $this->AskObj = new Ask();
        $this->keshi_obj = new Keshi();
		parent::init();
	}
	
    public function indexAction()
    {
    	echo "<script>location.href='index.shtml';</script>";
    	//echo $this->view->render("index.phtml");
	}
    //现线人数
    public function keshinumAction(){
        @require(APP_DATA_PATH. '/cache_keshinum.php');
        $CATEGORY = $this->keshi_obj->get_keshi_redis();
        $time1=date("H",time());
        foreach($KESHINUM['key'] as $k=>$v){
            if($time1>=$v[0]&&$time1<$v[1]){
                break;
            }
        }
        $data=$KESHINUM['value'][$k];
        $rebound=array();
        foreach($CATEGORY as $v){
            $ren=rand($data[0][0],$data[0][1]);
            $rebound[$v['id']]=$ren;
        }
        echo "var keshirenshu=eval('(".json_encode($rebound).")');";
    }
    //头部搜索旁边的今日已解决问题个数，以及久久问答已收录问题总数
    public function topasknumAction(){
        $ask_num = $this->AskObj->getask_num();
        $lenth=strlen($ask_num);
        for($i=$lenth; $i<9; $i++){
            $ask_num="0".$ask_num;
            $lenth=$i;
        }
        $arry_ask_num=str_split($ask_num);
        foreach($arry_ask_num as $k=>$v){
            $askid_num.="<b>".$v."</b>";
        }
        $iStartTime = date("Y-m-d");
        $nums=file_get_contents(APP_DATA_PATH. '/askIndexHeader'.$iStartTime.".txt");
        echo "document.write('<p class=ask-resolved>今日已解决 <span>{$nums}</span> 个</p><p class=ask-record><span>久久问答已收录问题</span><span class=number>{$askid_num}</span></p>')";
    }
    //健康网首页
    public function zxasknumAction(){
        $ask_num = $this->AskObj->getask_num();
        $lenth=strlen($ask_num);
        for($i=$lenth; $i<9; $i++){
            $ask_num="0".$ask_num;
            $lenth=$i;
        }
        $arry_ask_num=str_split($ask_num);
        foreach($arry_ask_num as $k=>$v){
            $askid_num.="<b>".$v."</b>";
        }
        echo "document.write('{$askid_num}')";
    }
    //现线人数
    public function onnumAction(){
        @require(APP_DATA_PATH. '/cache_onnum.php');
        $time1=date("H",time());
        foreach($ONNUM['key'] as $k=>$v){
            if($time1>=$v[0]&&$time1<$v[1]){
                break;
            }
        }
        $data=$ONNUM['value'][$k];
        $ren=rand($data[0][0],$data[0][1]);
        $tie=rand($data[1][0],$data[1][1]);
        echo "document.write('<p>当前在线用户：{$ren}人</p><p>问答贴：{$tie}贴</p>')";
    }
    //用户登陆管理
    public function userAction(){
		if($_COOKIE['member_uID']){
			$str = '<p class="welcome" onmouseover="showlogin('.$_COOKIE['member_uID'].')">您好，'
					.'<font color="Red">'.$_COOKIE['member_nickname'].'</font> 欢迎进入久久健康社区！</p>	'
					.'<ul class="top-tip">'
					.'<li class="g-logined"><a href="'.HOME_9939_URL.'user/index/">我的空间</a></li>'
					.'<li><span onclick="return logout();"  style="cursor:pointer;">退出</span></li>'	
					.'<li class="help"><a href="'.ASK_URL.'rule.shtml" target="_blank">帮助</a></li>	</ul>';
		}else if($_COOKIE['sina_member_nickname']){
		    $str = '<p class="welcome" onmouseover="showlogin('.$_COOKIE['member_uID'].')">您好，'
				    .'<font color="Red">'.$_COOKIE['sina_member_nickname'].'</font> 欢迎进入久久健康社区！</p>'
				    .'<ul class="top-tip">'	
				    .'<li class="g-logined"><a href="'.HOME_9939_URL.'user/index">我的空间</a></li>'
				    .'<li><span onclick="return logout();"  style="cursor:pointer;">退出</span></li>'
				    .'<li class="help"><a href="'.ASK_URL.'rule.shtml" target="_blank">帮助</a></li></ul>';
		}else{
			$str = '<p class="welcome">您好 欢迎进入久久健康社区！</p>'
	    			.'<ul class="top-tip">'
	    			.'<li class="g-login"><span id="vvvlogin" style="cursor:pointer;">登录</span></li>'
	    			.'<li><a href="'.WEB_URL.'register">注册</a></li>'
	    			.'<li class="help"><a href="'.ASK_URL.'rule.shtml" target="_blank">帮助</a></li>'
					.'<li><a href="javascript:void(0);" onclick="toQzoneLogin()"><img src="http://home.9939.com/images/qq_login.png"></a></li>'
	    			.'</ul>';
		}
		echo $str;
    }
}
?>
