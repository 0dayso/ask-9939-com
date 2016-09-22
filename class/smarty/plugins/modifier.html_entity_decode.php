<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty string_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     string_format<br>
 * Purpose:  format strings via sprintf
 * @link http://smarty.php.net/manual/en/language.modifier.string.format.php
 *          string_format (Smarty online manual)
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_html_entity_decode($string)
{
    return html_entity_decode($string);
	//return sprintf($format, $string);
}

/* vim: set expandtab: */

?>
