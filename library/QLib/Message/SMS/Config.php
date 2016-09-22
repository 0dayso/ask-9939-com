<?php

class QLib_Message_SMS_Config {
    
    /*
     * 漫道短信 webservice
     * 
     * 
     * 运营商:北京创世漫道科技有限公司 
     * http://self.zucp.net/main.htm 
     */
    public static $SMS_WSSDK_CONFIG = array(
        'sn'=>'SDK-BBX-010-10350',
        'pass'=>'109279',
        'ip'=>'sdk.entinfo.cn',
        'port'=>'8061'
    );
    
    /*
     * 漫道短信 httpservice
     * 
     * 
     * 运营商:北京创世漫道科技有限公司 
     * http://self.zucp.net/main.htm 
     */
    public static $SMS_HSSDK_CONFIG = array(
        'userid'=>'51',
        'account'=>'SDK09103',
        'password'=>'301900',
        'ip'=>'211.149.197.95',//sdk9.mdjc.net.cn
        'port'=>'8888'
        
    );
    
    /*
     * 
     * 
     * 
     * 运营商:名商通SMS
     * http://www.139000.com/
     */
    public static $SMS_MSTSDK_CONFIG = array(
        'name'=>'renhaibo',
        'pwd'=>'haibo9939',
        'ip'=>'www.139000.com',
        'port'=>''
    );
}
