<?php

class SiteHelper {

    /**
     * 获取推荐医生
     */
    public static function getRecommendDoc($classid = 0) {
        //右侧名医推荐 lc@2016-6-15
        //科室id => 后台广告位id
        $adIdsArr = array(
            '32' => 273, //内科
            '102' => 274, //外科
            '220' => 275, //男科
            '193' => 276, //妇产科
            '523' => 277, //皮肤性病科
            '236' => 278, //儿科
            '276' => 272, //五官科
            '428' => 279, //中医科
            '324' => 280, //传染病科
            '371' => 281, //肿瘤科
            '299' => 282, //整形美容
            '525' => 283, //心理
            '15' => 284, //其他
            '537' => 284//热门标签等
        );
        $createObj = new Create();
        $adsid = isset($adIdsArr[$classid])?$adIdsArr[$classid]:$adIdsArr[32];
        return $createObj->getAds($adsid);
    }
    /*
     * 推荐医院
     */
    public static function getRecommendHospital($classid = 0) {
        //右侧名医推荐 lc@2016-6-15
        //科室id => 后台广告位id
        $adIdsArr = array(
            '32' => 287, //内科
            '102' => 287, //外科
            '220' => 287, //男科
            '193' => 287, //妇产科
            '523' => 287, //皮肤性病科
            '236' => 287, //儿科
            '276' => 287, //五官科
            '428' => 287, //中医科
            '324' => 287, //传染病科
            '371' => 287, //肿瘤科
            '299' => 287, //整形美容
            '525' => 287, //心理
            '15' => 287, //其他
            '537' => 287 //热门标签等等
        );
        $createObj = new Create();
        $adsid = isset($adIdsArr[$classid])?$adIdsArr[$classid]:$adIdsArr[32];
        return $createObj->getAds($adsid);
    }
    
     /*
     * 推荐药品
     */
    public static function getRecommendDrug($classid = 0) {
        //右侧名医推荐 lc@2016-6-15
        //科室id => 后台广告位id
        $adIdsArr = array(
            '32' => 288, //内科
            '102' => 288, //外科
            '220' => 288, //男科
            '193' => 288, //妇产科
            '523' => 288, //皮肤性病科
            '236' => 288, //儿科
            '276' => 288, //五官科
            '428' => 288, //中医科
            '324' => 288, //传染病科
            '371' => 288, //肿瘤科
            '299' => 288, //整形美容
            '525' => 288, //心理
            '15' => 288, //其他
            '537' => 288 //热门标签等等
        );
        $createObj = new Create();
        $adsid = isset($adIdsArr[$classid])?$adIdsArr[$classid]:$adIdsArr[32];
        return $createObj->getAds($adsid);
    }

}
