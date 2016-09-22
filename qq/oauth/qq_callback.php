<?php 
/**
   *##############################################
   * 
   *##############################################
   *潘红晶
   *
   *==============================================
   * QQ回调文件
   *==============================================
   */
require_once("../comm/config.php");
require_once("../comm/utils.php");
function qq_callback()
{
    //debug
    //print_r($_REQUEST);
    //print_r($_SESSION);

    if($_REQUEST['state'] == $_SESSION['state']) //csrf
    {
        
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $_SESSION["appid"]. "&redirect_uri=" . urlencode($_SESSION["callback"])
            . "&client_secret=" . $_SESSION["appkey"]. "&code=" . $_REQUEST["code"];

        $response = get_url_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }

        $params = array();
        parse_str($response, $params);

        //debug
        //print_r($params);

        //set access token to session
        $_SESSION["access_token"] = $params["access_token"];

    }
    else 
    {
        echo("The state does not match. You may be a victim of CSRF.");
    }
}

function get_openid()
{
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" 
        . $_SESSION['access_token'];

    $str  = get_url_contents($graph_url);
    if (strpos($str, "callback") !== false)
    {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }

    $user = json_decode($str);
    if (isset($user->error))
    {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
    }

    //debug
    //echo("Hello " . $user->openid);

    //set openid to session
    $_SESSION["openid"] = $user->openid;
}

//QQ登录成功后的回调地址,主要保存access token
qq_callback();

//获取用户标示id
get_openid();
function get_user_info()
{
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
        . "access_token=" . $_SESSION['access_token']
        . "&oauth_consumer_key=" . $_SESSION["appid"]
        . "&openid=" . $_SESSION["openid"]
        . "&format=json";

    $info = get_url_contents($get_user_info);
    $arr = json_decode($info, true);

    return $arr;
}

//获取用户基本资料
$arr = get_user_info();
//print_r($arr);
//获取用户基本资料
$con = mysql_connect("192.168.229.47","9939_indata","3edcVFR$");
mysql_select_db('9939_com_v2sns');
$reg_date=time();
if (!$con)
  {
  die('Could not connect: ' . mysql_error("失败"));
  }
mysql_query('set names utf8');
$mark = substr(md5($arr['figureurl']),19,10);
  $username="QQ_9939"."(".$mark.")";
//$result=mysql_query("select count(*) as count from member where username='".$username."'");   
//$sta = mysql_fetch_row($result);
define("APP_TIME_INTERVAL",60*60*12);		//cookie时间间隔  （12 hour）
define("APP_DOMAIN",".9939.com");		//cookie应用的域名
function getImage($url,$filename="") {
	if(!$url) return false;
	if(!$filename) {
		$filename=".jpg" ;
	}
    $filename = time().$filename;
    $ch = curl_init(); 
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$img = curl_exec($ch);
			curl_close($ch);
	$fp2=@fopen(dirname(dirname(dirname(dirname(__FILE__))))."/htsns-9939-com/upload/pic/qq/".$filename, "a");
	fwrite($fp2, $img);
	fclose($fp2);
	return $filename;
}


//if($sta[0]){
    $resultn=mysql_query("select * from member where username='".$username."'"); 
    $uid = mysql_fetch_row($resultn);   
    if($uid[3]==$username){
            
            // 更新最近登录IP，登录时间
            $iarr['ip'] = $_SERVER['REMOTE_ADDR'];
            $iarr['lastlogin'] = time();
			
			mysql_query("update member set ip=".$iarr['ip'].", lastlogin=".$iarr['lastlogin']." where uid=".$uid[0]);
            
            setcookie('member_uID',$uid[0],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_username',$uid[3],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_uType',$uid[1],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
			$_COOKIE['member_uType'] = $uid[1];
            setcookie('member_nickname',$uid[2],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_pic',$uid[19],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_credit',$uid[6],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_experience',$uid[7],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_ip',$iarr['ip'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
		   echo '<script>window.open("http://ask.9939.com");</script>';
		   echo "<script>window.close();</script>";
    }
    else{
    $filename=getImage("".$arr['figureurl_2']."");
    $mark = substr(md5($arr['figureurl']),19,10);
      $username="QQ_9939"."(".$mark.")";
      mysql_query("INSERT INTO `member` (`uid`, `uType`, 
    
    `nickname`, `username`, `email`, `password`, `credit`, 
    
    `experience`, `domain`, `viewnum`, `notenum`, 
    
    `friendnum`, `dateline`, `updatetime`, `lastpost`, 
    
    `lastlogin`, `lastsend`, `status`, `newpm`, `pic`, 
    
    `ip`, `isVip`, `title`, `friend`, `salt`, 
    
    `checkemail`, `huodongshenhe`, `getpassword`, 
    
    `rzpassword`, `zdpassword`) VALUES (NULL, '3', '".$arr["nickname"]."', '".$username."', 
    
    '', '', '0', '0', '', '0', '0', '0', '".$reg_date."', '0', '0', 
    
    '0', '0', '1', '0', 'qq/".$filename."', '".$_SERVER['REMOTE_ADDR']."', '0', '', '', 
    
    '', NULL, '0', '', '', NULL)");

 // 更新最近登录IP，登录时间
            $iarr['ip'] = $_SERVER['REMOTE_ADDR'];
            $iarr['lastlogin'] = time();
			$resultn=mysql_query("select * from member where nickname='".$arr["nickname"]."'"); 
            $uid = mysql_fetch_row($resultn);
			mysql_query("update member set ip=".$iarr['ip'].", lastlogin=".$iarr['lastlogin']." where uid=".$uid[0]."");
            setcookie('member_uID',$uid[0],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_username',$uid[3],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_uType',$uid[1],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
			$_COOKIE['member_uType'] = $uid[1];
            setcookie('member_nickname',$uid[2],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_pic',$uid[19],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_credit',$uid[6],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_experience',$uid[7],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
            setcookie('member_ip',$iarr['ip'],time()+APP_TIME_INTERVAL,"/",APP_DOMAIN);
			echo '<script>window.open("http://ask.9939.com");</script>';
			echo "<script>window.close();</script>";

}

//}

?>
