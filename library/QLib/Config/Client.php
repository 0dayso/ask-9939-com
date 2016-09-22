<?php

/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Config_Client
 * @package QLib.Images.Client
 * @author   tds@qteam.cn
 * @since   1.0
 */
class QLib_Config_Client {
    
    public  static function gettemplateconfig(){
        return QConfigs_Site_Config::$site_template_map;
    }
    
    public static function findtemplateconfig($catids){
        $return_arr_config = array();
        $arr_config = self::gettemplateconfig();
        $arr_cat_ids = $catids;
        if(!is_array($arr_cat_ids)){
            $arr_cat_ids =  explode(',', $arr_cat_ids);
        }
        //资讯栏目id：1836,1979,2711,2094,2464,2266, 2388,2791,1808,11470,2280,1947,11430,9552,9687,9366,9456,
        //疾病专题id：10936,9782,11161,11135,9785,11091,10820,11305,9788,10819,9783,10797,11743,10896,11037,10249,11066,9790,11277,11364,9784,10236,11290,9789,9786,9787,9780,11003,11169,11334,10916,10976
        foreach ($arr_cat_ids as $v){
            if(isset($arr_config[$v])){
                $return_arr_config = $arr_config[$v];
                break;
            }
        }
        return $return_arr_config;
    }
}
