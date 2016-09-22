<?php

class Yii {
    
    //网站模板配置
    public static $alias_map = array(
        'oldjb' => 'http://oldjb.9939.com',
        'main' => 'http://www.9939.com',
        'ask' => 'http://ask.9939.com',
        'wap' => 'http://m.9939.com',
        'wapask' => 'http://wapask.9939.com',
        'mjb_domain' => 'http://m.jb.9939.com',
        'community' => 'http://home.9939.com',
        'jb_domain' => 'http://jb.9939.com',
        'fileserver' => 'http://jb.9939.com',
        'frontdomain' => 'http://jb.9939.com',
    );

    public static function getAlias($alias) {
        $aliasname = str_replace('@', '', $alias);
        return isset(self::$alias_map[$aliasname]) ? self::$alias_map[$aliasname] : '';
    }

}
