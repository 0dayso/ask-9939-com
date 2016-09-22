<?php
class QLib_Utils_CacheHelper {
    
    /**
     * 
     * @param type $options
     * @return Q_Cache_Adapter_Redis
     */
    public static function Q($options=array('section'=>'servers','domain'=>'9939.com')){
        $options['class']=__CLASS__;
        return Q_Cache::factory($options, 'Redis');
    }
}