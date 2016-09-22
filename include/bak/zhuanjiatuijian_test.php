<?php
/* 
 * 专家推荐
 * @author 林原
 * @date 2010-7-1
 */

//获取科室id
$keshiid = intval($this->classid) ?  intval($this->classid) : intval($this->info['classid']);

//获取广告位id
define("ROOT",substr(dirname(__FILE__), 0, -7));	//文件的主目录

require(ROOT."data/adsplace_pos_keshi.php");   //引入科室，广告位关系缓存文件
if($_ADSP_POS_KESHI[$keshiid]) {
    $adsplaceId = $_ADSP_POS_KESHI[$keshiid][0]; //获取广告位id
} else {
    @require(ROOT."Keshi_cache.php"); //引入科室缓存文件
    $keshiInfo = $CATEGORY[$keshiid];  //获取科室信息
    $parentKeshiArr = explode(',',$keshiInfo['arrparentid']);  //父科室id
    //一级一级往上查找
    for($i=count($parentKeshiArr)-1;$i>=0;$i--) {
        if($_ADSP_POS_KESHI[$parentKeshiArr[$i]]) {
            $adsplaceId = $_ADSP_POS_KESHI[$parentKeshiArr[$i]][0]; //获取广告位id
        }
    }
}


echo $adsplaceId; exit;

//显示广告
if($adsplaceId) {
    if(file_exists(ROOT."data/data_adsplace_".$adsplaceId.'.php')) {
         require(ROOT."data/data_adsplace_".$adsplaceId.'.php'); //引入广告缓存文件

         //输出广告
         foreach($_ADSGLOBAL[$adsplaceId] as $k=>$v){
              echo '<div class="zhmod-03">
                        <div class="header-left fl">
                          <a href="'.$v[linkurl].'"><img src="http://home.9939.com'.$v[imageurl].'" width="84" height="114"/></a>
                        </div>
                        <div class="info-right fl">
                          '.$v[introduce].'
                          <p class="askme"><a href="'.$v[linkurl].'">向我提问</a></p>
                        </div>
                      </div>';
        }

    }

}

?>