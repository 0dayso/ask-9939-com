<?php
/**
 * 验证 数据是否合法
 * @author 林原
 */
class CheckData {
	
	/**
	 * 判断字符串是否是安全的
	 */
	static function isSafeStr($str) {//$key 1普通用户 2医生用户 3是付费医生
		//判断是否里面有网址
		if(preg_match('/\.com|\.cn|\.mobi|\.co|\.net|\.so|\.org|\.gov|\.tel|\.tv|\.biz|\.cc|\.hk|\.name|\.info|\.asia|\.me/', $str)) {
		 
			return false;
		}
	   //电话 qq号
		if(preg_match('/(\(\d{3,4}\)|\d{3,4}-)?\d{6,8}/', $str)) {
	
			return false;
		}
		//判断是否里面有中文
		if(!preg_match('/([\x81-\xfe][\x40-\xfe])/', $str)) {
			return false;
		}
        Zend_Loader::loadClass('Member',MODELS_PATH);
        $MemberObj = new Member();
        $tmp_user_cookie = $MemberObj->getCookie();
        $where = ' uid=\''.$tmp_user_cookie['uid'].'\'';


		$tmp_user_cookie = $MemberObj->get_one($where);
		//读取缓存
		require_once APP_DATA_PATH.'/data_censorvalue.php';
		global $_SGLOBAL;
        
        if($tmp_user_cookie['uType']=="2"&&$tmp_user_cookie['isVip']=="1"){
            $key=3;
        }else if($tmp_user_cookie['uType']=="2"){
            $key=2;
        }else{
            $key=1;
        }
       
		foreach($_SGLOBAL['censorvalue'][$key] as $val) {
			if($val<>""){
				if(strpos($str,$val)!==false) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 判断ip
	 */
	static function isSafeIp($ip='') {
		if($ip == '') {
			if($_SERVER['HTTP_CDN_SRC_IP'])
				$ip = $_SERVER['HTTP_CDN_SRC_IP'];
			elseif($_SERVER['HTTP_X_FORWARDED_FOR'])
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else
				$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		if(file_exists(APP_DATA_PATH.'/data_ipfilter.php')) {
			include APP_DATA_PATH.'/data_ipfilter.php';
		}
		if($ip_array) {
			foreach ($ip_array as $key => $val) {
				$arr = explode('-',$val);
				$startip = $arr[0];
				if($arr[1]) $endip = $arr[1];
				else $endip = $arr[0];
				$startip = ip2long(trim($startip));
				$endip = ip2long(trim($endip));
				$ip = ip2long($ip);
				if($ip>=$startip && $ip <= $endip) {
					return false;
				}
			}
		}
		return true;
		
	}
	
}
?>
