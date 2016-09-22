<?php

/**
 * 字符串处理
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name String
 * @version 
 * @package QLib.Utils.String
 * @author peter.zyliu peter.zyliu@gmail.com
 * @since 1.0
 */
// // require_once 'QLib/Utils/StringHelper.php';
class QLib_Utils_String {
	
	/**
	 * QLib_Utils_StringHelper
	 *
	 * @return QLib_Utils_StringHelper
	 */
	public static function String() {
		return new QLib_Utils_StringHelper();
	}
    //向页面输出对象的内容
	public static function debugObjcet($object)
    {
        echo "<!--";
        echo "debugObjcet";
        print_r($object);
        echo "-->";
    } 
    //生成指定长度的随机字符串
    public static function randomKeys($length)
    {
        $str = '';
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }
	/**
	 * 截取字符串（1个汉字长度计为1；1个字母长度计为0.5）
	 */
	public static function cutString($sourcestr,$cutlength,$addpoint=1)
	{
		$returnstr='';
		$i=0;
		$n=0;
		$str_length=strlen($sourcestr);//字符串的字节数
		while (($n<$cutlength) and ($i<=$str_length))
		{
			$temp_str=substr($sourcestr,$i,1);
			$ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
			if ($ascnum>=224)    //如果ASCII位高与224，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
				$i=$i+3;            //实际Byte计为3
				$n++;            //字串长度计1
			}
			elseif ($ascnum>=192) //如果ASCII位高与192，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
				$i=$i+2;            //实际Byte计为2
				$n++;            //字串长度计1
			}
			elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,1);
				$i=$i+1;            //实际的Byte数仍计1个
				$n++;            //但考虑整体美观，大写字母计成一个高位字符
			}
			else                //其他情况下，包括小写字母和半角标点符号，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,1);
				$i=$i+1;            //实际的Byte数计1个
				$n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
			}
		}
		if ($str_length>$i&&$addpoint){
			$returnstr = $returnstr . "...";//超过长度时在尾处加上省略号
		}
		return $returnstr;
	}
}