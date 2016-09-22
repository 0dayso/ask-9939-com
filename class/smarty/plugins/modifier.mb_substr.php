<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mb_substr<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_mb_substr($string, $start = 0,$length = 80,$suffix="...")
{
	if($length>=strlen($string))
			return $string;
		$s="";
		for($i=$start;$i<$length;$i++)
		{
			if(ord($string{$i})>127) 
			{
				$s.=$string{$i}.$string{++$i};
				continue;
			}else{
				$s.=$string{$i};
				continue;
			} 
		}
		return $s.$suffix;
/*
   if($length >= mb_strlen($string,"GB2312"))
	   return $string;
   else
	{
	  $dotNum = strlen($suffix);
	 return mb_substr($string,$start,$length-$dotNum,"GB2312").$suffix;
	}
	*/
}

/* vim: set expandtab: */

?>
