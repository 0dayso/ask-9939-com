<?php

/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Cache_Client
 * @package QLib.Cache.Client
 */
class QLib_Cache_Client {
    
    /**
     * 全站通用生成缓存
     * @param type $dir 缓存目录
     * @param string $filename 要缓存的文件名
     * @param type $data 要缓存的数组
     * @param type $time 缓存的时间 单位 小时
     */
    public static function setCache($dir, $filename, $data, $time) {
        $file_save_dir = APP_CACHE_PATH . '/' . $dir;
        $filename = $file_save_dir . "/" . $filename;
        if (!file_exists($file_save_dir)) {
            mkdir($file_save_dir, 0777, true);
        } else {
            chmod($file_save_dir, 0777);
        }
        if($time==0){
            @unlink($filename);
        }else{
            $arr = array('content' => $data, 'addtime' => time(), 'savetime' => $time);
            $content = serialize($arr);
            file_put_contents($filename, $content);
        }
    }
    
    /**
     * 全站通用加载缓存
     * @param type $dir 缓存目录
     * @param string $filename 缓存文件名
     * @param type $iHour 缓存时间 单位 小时
     * @return boolean
     */
    public static function getCache($dir, $filename, $iHour = 1) {
        $filename = APP_CACHE_PATH . '/' . $dir . "/" . $filename;
        $iTime = $iHour * 3600;
        if (is_file($filename)) {
            $content = file_get_contents($filename);
            $arr = unserialize($content);
            //判断缓存时间
            $haveTime = time() - $arr['addtime'];
            if (($haveTime / $iTime) > $arr['savetime']) {
                return false;
            } else {
                return $arr['content'];
            }
        }
        return false;
    }
    
    /**
     * 生成缓存
     * @param type $dir 缓存目录
     * @param string $filename 要缓存的文件名
     * @param type $data 要缓存的数组
     * @param type $time 缓存的时间 单位 小时
     */
    public static function setUserCache($dir, $filename, $data, $time) {
        $filename = APP_CACHE_PATH . '/' . $dir . "/" . $filename;
        if($time==0){
            @unlink($filename);
        }else{
            $arr = array('content' => $data, 'addtime' => time(), 'savetime' => $time);
            $content = serialize($arr);
            file_put_contents($filename, $content);
        }
    }
    
    /**
     * 加载缓存
     * @param type $dir 缓存目录
     * @param string $filename 缓存文件名
     * @param type $iHour 缓存时间 单位 小时
     * @return boolean
     */
    public static function getUserCache($dir, $filename, $iHour = 1) {
        $filename = APP_CACHE_PATH . '/' . $dir . "/" . $filename;
        $iTime = $iHour * 3600;

        if (is_file($filename)) {
            $content = file_get_contents($filename);
            $arr = unserialize($content);

//		return $arr['content'];
////
//		exit;
            //判断缓存时间
            $haveTime = time() - $arr['addtime'];
            if (($haveTime / $iTime) > $arr['savetime']) { //永久缓存
                return false;
            } else {
                return $arr['content'];
            }
//            if ($haveTime > $iTime) {
//                return false;
//            } else {
//                return $arr['content'];
//            }
        }
        return false;
    }

    /**
     * 生成缓存
     * @param type $dir 缓存目录  pages
     * @param string $key 要缓存的文件名 control|action|filename-params 如:hotwords|search|default-2
     * @param type $data 要缓存的数组
     * @param type $time 缓存的时间 单位 小时
     */
    public static function setPageCache($dir, $key, $data, $time) {
        $page_info = explode('|', $key);
        $page_save_file_path =$page_info[0].DIRECTORY_SEPARATOR.$page_info[1];
        $file_save_dir = APP_CACHE_PATH . '/' . $dir . "/" . $page_save_file_path;
        if (!file_exists($file_save_dir)) {
            mkdir($file_save_dir, 0777, true);
        } else {
            chmod($file_save_dir, 0777);
        }
        $filename = $file_save_dir.DIRECTORY_SEPARATOR.$page_info[2];
        if($time==0){
            @unlink($filename);
        }else{
            $add_time = strtotime(date('Y-m-d',time()));
            $arr = array('content' => $data, 'addtime' => $add_time, 'savetime' => $time);
            $content = serialize($arr);
            file_put_contents($filename, $content);
        }
    }
    
    
    /**
     * 加载缓存
     * @param type $dir 缓存目录   pages
     * @param string $key  要缓存的文件名  control|action|filename-params 如:hotwords|search|default-2
     * @param type $iHour 缓存时间 单位 小时
     * @return boolean
     */
    public static function getPageCache($dir, $key, $iHour = 1) {
//        return false;
        $page_save_file_path = str_replace('|', DIRECTORY_SEPARATOR, $key);
        $filename = APP_CACHE_PATH . '/' . $dir . "/" . $page_save_file_path;
        $iTime = $iHour * 3600;
        if (is_file($filename)) {
            $content = file_get_contents($filename);
            $arr = unserialize($content);
            //判断缓存时间
            $haveTime = time() - $arr['addtime'];
            if ($haveTime > $iTime) {
                return false;
            } else {
                return $arr['content'];
            }
        }
        return false;
    }
    
    
    
    /**
     * 生成缓存
     * @param type $dir 缓存目录  pages
     * @param string $fname 要缓存的文件名 
     * @param type $data 要缓存的数组
     * @param type $expired 缓存的时间 单位 秒 0时，缓存永不过期
     */
    public static function setPageJsonCache($dir, $fname, $data, $expired=0) {
        $file_save_dir = APP_CACHE_PATH . '/' . $dir ;
        if (!file_exists($file_save_dir)) {
            mkdir($file_save_dir, 0777, true);
        } else {
            chmod($file_save_dir, 0777);
        }
        $filename = $file_save_dir.DIRECTORY_SEPARATOR.$fname;
//        $add_time = strtotime(date('Y-m-d',time()));
        $add_time=time();
        $arr = array('content' => $data, 'addtime' => $add_time, 'expired' => $expired);
        $json_data = json_encode($arr);
        file_put_contents($filename, $json_data);
    }
    
    
    /**
     * 加载缓存
     * @param type $dir 缓存目录   pages
     * @param string  $fname 要缓存的文件名 
     * @return boolean
     */
    public static function getPageJsonCache($dir, $fname) {
//        return false;
        $filename = APP_CACHE_PATH . '/' . $dir . "/" . $fname;
        if (is_file($filename)) {
            $content = file_get_contents($filename);
            $arr = json_decode($content);
            $expired = $arr['expired']==0?time():$arr['expired'];
            //判断缓存时间
            $haveTime = time() - $arr['addtime'];
            if ($haveTime > $expired) {
                return false;
            } else {
                return $arr['content'];
            }
        }
        return false;
    }

    

}
