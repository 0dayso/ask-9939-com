<?php
/**
  *##############################################
  * @FILE_NAME :censor.php
  *############################################## 
	*
	* @author : xzx
	* @MailAddr : xzx747@126.com
	* @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
	* @PHP Version :  Ver 5.21
	* @Apache  Version : Ver 2.20
	* @MYSQL Version : Ver 5.0
	* @Version : Ver Wen Sep 16 09:00 CST 2009
	* @DATE : Wen Sep 16 09:00 CST 2009
  *
  *==============================================
  * @Desc :  词语屏蔽模块
  *==============================================
  */

class Censor extends QModels_Ask_Table
{	
	/**
	* @Desc 词语替换
	* @param $msg
	* @return bool
	*/
	public function replace($str="")
	{	
		global $_SGLOBAL;
		$tmp_censor_array = $_SGLOBAL['censor'];
		
		foreach ($tmp_censor_array as $k=>$v){
			$str = str_replace($v,"*",$str);
		}
		return $str;		
	}	
}
?>