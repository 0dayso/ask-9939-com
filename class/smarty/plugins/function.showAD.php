<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_radios} function plugin
 *
 * File:       function.html_radios.php<br>
 * Type:       function<br>
 * Name:       html_radios<br>
 * Date:       24.Feb.2003<br>
 * Purpose:    Prints out a list of radio input types<br>
 * Input:<br>
 *           - name       (optional) - string default "radio"
 *           - values     (required) - array
 *           - options    (optional) - associative array
 *           - checked    (optional) - array default not set
 *           - separator  (optional) - ie <br> or &nbsp;
 *           - output     (optional) - the output next to each radio button
 *           - assign     (optional) - assign the output as an array to this variable
 * Examples:
 * <pre>
 * {html_radios values=$ids output=$names}
 * {html_radios values=$ids name='box' separator='<br>' output=$names}
 * {html_radios values=$ids checked=$checked separator='<br>' output=$names}
 * </pre>
 * @link http://smarty.php.net/manual/en/language.function.html.radios.php {html_radios}
 *      (Smarty online manual)
 * @author     Christopher Kvarme <christopher.kvarme@flashjab.com>
 * @author credits to Monte Ohrt <monte@ispi.net>
 * @version    1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_showAD($aParams, &$smarty)
{
	//需要的条件$aList,$i,$iWidth,$iHeight	
	
	$aList = $aParams['aList'];
	$i= $aParams['i'];	
	$j = $i-1;
	//print_r($aList[$j]);
	
	$iWidth = $aList[$j]['width'];
	$iHeight = $aList[$j]["height"];
	$sPic = substr(__AD_PIC,2).$aList[$j]['pic'];
    $sTitle = $aList[$j]['adTitle'];	
    $sUrl = $aList[$j]['adWebPage'];
        	
    switch ($aList[$j]['type'])
    {
    	case 0 : // 文字		
    		$sTitle = ($aList[$j]['isRed']) ? "<font color=red>$sTitle</font>" : $sTitle;
    		//$sTitle = str_sub($sTitle,30);
    		$sStr = "<a href='$sUrl' class='af12px aline' title='$sTitle' target='_blank'>".$sTitle."</a>";
    		break;
    	case 1 : // 图片    		
    		$sStr = "<a href='$sUrl' target='_blank'  title='$sTitle'><img src='".$sPic."' border=0 alt='{$sTitle}' width='$iWidth' height='$iHeight'></a>";
    		break;
    	case 2: // flash
    		$sStr = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='$iWidth' height='{$iHeight}'>
  <param name='movie' value='".$sPic."'>
  <param name='quality' value='high'>
  <embed src='$sPic' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='$iWidth' height='{$iHeight}'></embed>
</object>";
    		break;
    	case 3 : // 视频
    		$sStr = "";
    }   	  	        
	echo $sStr;
}
?>