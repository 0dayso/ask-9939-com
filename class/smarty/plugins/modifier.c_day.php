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
 * Name:     truncate<br>
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
function smarty_modifier_c_day($sDate2)
{
	$sDate1 = date("Y-m-d");
	$s_date1_list = explode("-", $sDate1);
	$s_date2_list = explode("-", $sDate2);
	$s_d1 = mktime(0, 0, 0, $s_date1_list[1], $s_date1_list[2], $s_date1_list[0]);
	$s_d2 = mktime(0, 0, 0, $s_date2_list[1], $s_date2_list[2], $s_date2_list[0]);
	return round(($s_d1 - $s_d2) / 3600 / 24);
}

/* vim: set expandtab: */

?>
