<?php
/*
* 专家推荐
* @author 林原
* @date 2010-7-1
*/

$arr_tuijian_docs = array(
    '1138686'=>array(
        'name'=>'哈岩',
        'linkurl'=>'http://tj.166tj.cn/TongBao/chatadp.php?plat_id=8&channel_id=20326&adp=pc&servername=hayan@9939.com',
        'imageurl'=>'http://home.9939.com/upload/pic/201507/1138686_avatar_middle.jpg',
        'introduce'=>'<p>姓名：哈岩</p>
<p>科室：癫痫科</p>
<p>擅长：擅长儿童癫痫、青少年癫痫、女性癫痫、老年人癫痫等各种癫痫的诊治及其并发症的处理</p>'
    ),
    '1135222'=>array(
        'name'=>'罗世芳',
        'linkurl'=>'http://webservice.zoosnet.net/LR/Chatpre.aspx?id=LZA57311681&e=9939&r=9939&p=9939',
        'imageurl'=>'http://home.9939.com/upload/pic/201507/1135222_avatar_middle.jpg',
        'introduce'=>'<p>姓名：罗世芳</p>
<p>科室：不孕不育妇科</p>
<p>擅长：妇科不孕不育，卵巢疾病</p>'
    )
);
foreach($arr_tuijian_docs as $k=>$v){
        @preg_match_all("/<p>(.*?)<\/p>/isu",$v['introduce'],$introducearr);
        if(count($introducearr[1])==3){
            $jianjie=mb_substr($introducearr[1][2],3,mb_strlen($introducearr[1][2],"utf-8"),"utf-8");
            $v[introduce]="";
            $v[introduce].="<p>".$introducearr[1][0]."</p>";
            $v[introduce].="<p>".$introducearr[1][1]."</p>";
            $leng=35;
            if(mb_strlen($introducearr[1][2])<=$leng){
                $v[introduce].="<p><a href='javascript:;'' onclick='return false;' title='$jianjie' style='text-decoration:none;color: #333333;'>".$introducearr[1][2]."</a></p>";
            }else{
                $v[introduce].="<p><a href='javascript:;' onclick=''return false;' title='$jianjie' style='text-decoration:none;color: #333333;'>".mb_substr($introducearr[1][2],0,$leng,"utf-8")."...</a></p>";
            }

        }
        echo '<div class="zhmod-03">
            <div class="header-left fl" style=" border:#FFFFFF solid 1px;"> <a href="' . $v[linkurl] . '" target="_blank" title="' .
                  $jianjie . '"><img src="' . $v[imageurl] .
                  '" width="84" height="80"/> </a>
              <p class="askme" style="margin:5px;"><a href="' . $v[linkurl] .
                  '" target="_blank">向他咨询</a></p>
            </div>
            <div class="info-right fl">
              ' . $v[introduce] . '
            </div>
          </div>';

}
exit;

//获取科室id
$keshiid = intval($this->classid) ? intval($this->classid) : intval($this->info['classid']);
//获取广告位id
define("ROOT", substr(dirname(__file__), 0, -7)); //文件的主目录

require (ROOT . "data/adsplace_pos_keshi.php"); //引入科室，广告位关系缓存文件
if ($_ADSP_POS_KESHI[$keshiid]) {
    $adsplaceId = $_ADSP_POS_KESHI[$keshiid][0]; //获取广告位id
} else {
    @require (ROOT . "Keshi_cache.php"); //引入科室缓存文件
    $keshiInfo = $CATEGORY[$keshiid]; //获取科室信息
    $parentKeshiArr = explode(',', $keshiInfo['arrparentid']); //父科室id
    //一级一级往上查找
    for ($i = count($parentKeshiArr) - 1; $i >= 0; $i--) {
        if ($_ADSP_POS_KESHI[$parentKeshiArr[$i]]) {
            $adsplaceId = $_ADSP_POS_KESHI[$parentKeshiArr[$i]][0]; //获取广告位id
        }
    }
}

//显示广告
if ($adsplaceId) {
    if (file_exists(ROOT . "data/data_adsplace_" . $adsplaceId . '.php')) {
        include (ROOT . "data/data_adsplace_" . $adsplaceId . '.php'); //引入广告缓存文件
        if(is_array($_ADSGLOBAL[$adsplaceId])){
            //输出广告
            foreach ($_ADSGLOBAL[$adsplaceId] as $k => $v) {
                @preg_match_all("/<p>(.*?)<\/p>/isu",$v[introduce],$introducearr);
                if(count($introducearr[1])==3){
                    $jianjie=mb_substr($introducearr[1][2],3,mb_strlen($introducearr[1][2],"utf-8"),"utf-8");
                    $v[introduce]="";
                    $v[introduce].="<p>".$introducearr[1][0]."</p>";
                    $v[introduce].="<p>".$introducearr[1][1]."</p>";
                    $leng=35;
                    if(mb_strlen($introducearr[1][2])<=$leng){
                        $v[introduce].="<p><a href='javascript:;'' onclick='return false;' title='$jianjie' style='text-decoration:none;color: #333333;'>".$introducearr[1][2]."</a></p>";
                    }else{
                        $v[introduce].="<p><a href='javascript:;' onclick=''return false;' title='$jianjie' style='text-decoration:none;color: #333333;'>".mb_substr($introducearr[1][2],0,$leng,"utf-8")."...</a></p>";
                    }
                    
                }
                echo '<div class="zhmod-03">
              <div class="header-left fl" style=" border:#FFFFFF solid 1px;"> <a href="' . $v[linkurl] . '" target="_blank" title="' .
                    $jianjie . '"><img src="http://home.9939.com' . $v[imageurl] .
                    '" width="84" height="80"/> </a>
                <p class="askme" style="margin:5px;"><a href="' . $v[linkurl] .
                    '" target="_blank">向他咨询</a></p>
              </div>
              <div class="info-right fl">
                ' . $v[introduce] . '
              </div>
            </div>';
               // echo '<div class="zhmod-03">
//                            <div class="header-left fl">
//                              <a href="' . $v[linkurl] . '" target="_blank" title="' .
//                    $jianjie . '"><img src="http://home.9939.com' . $v[imageurl] .
//                    '" width="84" height="114"/></a>
//                            </div>
//                            <div class="info-right fl">
//                              ' . $v[introduce] . '
//                              <p class="askme"><a href="' . $v[linkurl] .
//                    '" target="_blank">向我提问</a></p>
//                            </div>
//                          </div>';
            }
        }

    }

}

?>