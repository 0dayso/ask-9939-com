<?php

/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Images_Client
 * @version version (2009-10-26 下午04:13:33)
 * @package QLib.Images.Client
 * @author   tds@qteam.cn
 * @since   1.0
 */
class QLib_Images_Client {

    /**
     * 获取组合后的图片
     *
     * @param String $imageUrl
     * @return String
     */
    private static function getImageUrl($imageUrl,$type='source') {
        $url = self::getDomainStr();
        if(strpos($imageUrl, 'uploadfile')===false){
            return $url . '/uploadfile/' . $imageUrl;
        }
        return $url . '/' . $imageUrl;
    }

    public static function getDomainStr() {
        return 'http://www.9939.com';
    }

    public static function Images($image, $type = 'article') {
        return self::getImageUrl($image, $type);
    }

}
