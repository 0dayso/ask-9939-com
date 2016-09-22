<?php
/* 
 * 列表页热门话题
 * @author 魏鹏
 */
define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录
@include(ROOT."data/data_adsplace_134.php");   //引入科室，广告位关系缓存文件
if(is_array($_ADSGLOBAL['134'])){

    foreach($_ADSGLOBAL['134'] as $v){
        if($v['type']=="文字链"){
            $text='<li>
            <a href="'.$v['linkurl'].'" target="_blank"><font size="2" color="#505152">'.$v['adsname'].'</font></a></li>';
            echo $text;
        }else if($v['type']=="外部调用"){
            $text=urldecode(urldecode($v['text']));
            $text=preg_replace(array("/\\\'/","/\\\/","/&quot;/","/\"\"/"),array("'","","","\""),$text);///服务器带自动加\的替换方式
            echo $text;
        }
    }
}
?>