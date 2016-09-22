<?php

/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Search_Client
 * @version version (2009-10-26 下午04:13:33)
 * @package QLib.Images.Client
 * @author   tds@qteam.cn
 * @since   1.0
 */
class QLib_Search_Client {
    
    /**
     * 获取组合后的图片
     *
     * @param String $imageUrl
     * @return String
     */
    private static function getSearchUrl($keyworkds='',$siteid = 'baojian') {
        $url = self::getDomainStr($siteid);
        $keyworkds = urlencode($keyworkds);
        return sprintf('%s&q=%s',$url,$keyworkds);
    }

    public static function getDomainStr($siteid='') {
       $search_id = self::getSearchId($siteid);
       return sprintf('http://sousuo.9939.com/cse/search?s=%s&entry=1',$search_id);
    }
    
    public static function getSearchId($siteid=''){
        $search_id =  QConfigs_Site_Config::$site_search_map[$siteid];
        return $search_id;
    }

    public static function Search($keyworkds='',$siteid='baojian') {
        return self::getSearchUrl($keyworkds, $siteid);
    }
    
    

}
