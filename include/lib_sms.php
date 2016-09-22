<?php
/**
 * @desc 短信接口
 * @author xzxin
 * @date 2011-10-18
 */
function sendsms($sMobile,$sMsg){	
	header("Content-type:text/html;charset=utf-8");
	//$sMobile ="15101600139,13810002962,13581718868,13601212933,13001113330,13146161584,18605640039,18601124217";
	$flag = 0;
	//要post的数据
	$sMsg = iconv("UTF-8", "GB2312", $sMsg);
	$argv = array(
	    'sn' => 'SDK-BBX-010-10350',
	    'pwd' => '109279',
	    'mobile' => $sMobile,
	    'content' => $sMsg,
	);

	//构造要post的字符串 
	foreach ($argv as $key => $value) {
	    if ($flag != 0) {
	        $params .= "&";
	        $flag = 1;
	    }
	    $params.= $key . "=";
	    $params.= urlencode($value);
	    $flag = 1;
	}
	$length = strlen($params);
	//exit('ok1');
	//创建socket连接
	//$fp = fsockopen("sdk2.entinfo.cn", 80, $errno, $errstr, 10) or exit($errstr . "--->" . $errno);
	
	$fp = fsockopen("sdk2.entinfo.cn", 80, $errno, $errstr, 10);
	
	if($errstr && $errno){
		echo $errstr . "--->" . $errno."服务器没响应！短信发送失败！";
		return "短信发送失败！";
	}	
	
	//构造post请求的头
	$header = "POST /z_send.aspx HTTP/1.1\r\n";
	$header .= "Host:sdk2.entinfo.cn\r\n";
	$header .= "Referer:/mobile/sendpost.php\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . $length . "\r\n";
	$header .= "Connection: Close\r\n\r\n";
	//添加post的字符串
	$header .= $params . "\r\n";
	//发送post的数据
	fputs($fp, $header);
	$inheader = 1;
	while (!feof($fp)) {
	    $line = fgets($fp, 1024); //去除请求包的头只显示页面的返回数据
	    if ($inheader && ($line == "\n" || $line == "\r\n")) {
	        $inheader = 0;
	    }
	    if ($inheader == 0) {
	        // echo $line;
	    }
	}
	if ($line == 1) {    
		//file_put_contents("log/sms.txt", "短信发送成功 请查收 返回值:{$line} 时间:".date("H:i:s", time())."\n", FILE_APPEND );
		return "您好，我们已经把新密码发送到您的手机上了，请查收手机短信！".date("H:i:s", time())."\n";
	} else {	
		return "短信发送失败,请根据返回值查看相关错误问题 返回值:{$line}时间：".date("H:i:s", time())."\n";
	}
	fclose($fp);
}


/**
 * @desc 随机密码
 * @author xzxin
 * @date 2011-10-18
 *
 * @return String
 */
function randpwd($iLen=6){ 
	$srcstr='0123456789'; 
	mt_srand(); 
	$strs=''; 
	for($i=0;$i<$iLen;$i++){ 
		$strs.=$srcstr[mt_rand(0,9)]; 
	} 	
	return $strs;
}

/**
 * @desc 短信接口
 * @author xzxin
 * @date 2011-03-03
 */
function sendsms_old($sMobile,$sMsg){
	//短信平台登录
	$sUser = "jjsjwl";
	$sPwd = strtolower(md5("jjsjwl"));
	$sIp = $_SERVER['REMOTE_ADDR'];
	
	$oSoap = new SoapClient("http://116.213.72.20/SMS_BlueWings/SMS_BlueWingsInfo.asmx?wsdl");
	$oUserHash = $oSoap->__Call('GetUserLogin',array(array("strUserName"=>$sUser,"strUserPass"=>$sPwd,"strUserIP"=>$sIp)));
	$sUserHash = $oUserHash->GetUserLoginResult;
	//echo "Hash字符串:".$sUserHash."<hr>";	
	//发送短信前准备
	$oStep1 = $oSoap->__Call('SetMessageBegin',array(array("strUserHash"=>$sUserHash,'strCount'=>1)));
	print_r($oStep1);
	
	if($oStep1->SetMessageBeginResult == 0){ //发送短信前准备成功	
		//echo "发送短信前准备成功！<hr>";	
		$oStep2 = $oSoap->__Call('SetMessage',array(array("strUserHash"=>$sUserHash,"strTarPhone"=>$sMobile,"strMessage"=>$sMsg)));	
		//print_r($oStep2);
		if($oStep2->SetMessageResult == 1){ //短信发送成功
			 //echo "短信发送成功！<hr>";
			 $oStep3 = $oSoap->__Call('SetMessageEnd',array(array("strUserHash"=>$sUserHash,"strPDate"=>"")));		
			// print_r($oStep3);	 
			 if($oStep3->SetMessageEndResult == 0){	//发送短信过程结束成功
			 	return 31;
			 	//echo "发送短信过程结束成功！<hr>";	
			 }
			 else{		
			 	return 32;
			 	//echo "发送短信过程结束失败！<hr>";
			 }
		}
		else{
			return 2;
			//echo "短信发送失败！";
		}
	}
	else {
		return 1;
		//echo "发送短信前准备失败！";
	}
}

function getIP() {
	if (!empty($_SERVER["HTTP_CDN_SRC_IP"])) return $_SERVER["HTTP_CDN_SRC_IP"];
	elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
	else return !empty($_SERVER["HTTP_CLIENT_IP"]) ? $_SERVER["HTTP_CLIENT_IP"] : $_SERVER["REMOTE_ADDR"];
}
?>