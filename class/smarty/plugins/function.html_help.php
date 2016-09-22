<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_image} function plugin
 *
 * Type:     function<br>
 * Name:     html_image<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  format HTML tags for the image<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - border = border width (optional, default 0)
 *         - height = image height (optional, default actual height)
 *         - image =image width (optional, default actual width)
 *         - basedir = base directory for absolute paths, default
 *                     is environment variable DOCUMENT_ROOT
 *
 * Examples: {html_image file="images/masthead.gif"}
 * Output:   <img src="images/masthead.gif" border=0 width=400 height=23>
 * @author  DingLei <dinglei@28.com>
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_help($params, &$smarty)
{
    foreach($params as $_key => $_val) {
        switch($_key) {
           case 'basedir':
                $$_key = $_val;
                break;
            case 'alt':
                $$_key = smarty_function_escape_special_chars($_val);
                break;

            case 'link':
            case 'href':
                $prefix = '<a href="' . $_val . '">';
                $suffix = '</a>';
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_help: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    return $prefix . '<img src="images/tip.png" alt="'.$alt.'" border="0" '.$extra.' target="_blank" />' . $suffix;
}

/* vim: set expandtab: */

?>
