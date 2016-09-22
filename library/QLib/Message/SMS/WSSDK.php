<?php

/*
* 漫道短信 webservice
* 
* 
* 运营商:北京创世漫道科技有限公司 
* http://self.zucp.net/main.htm 
*/
class QLib_Message_SMS_WSSDK {
    private $_params = array();
    public function __construct($config) {
        $this->init($config);
    }
    
    public function init($config){
        foreach($config as $k=>$v){
            $this->{$k} = $v;
        }
        $this->_params = array(
            'sn'=>$this->sn, ////替换成您自己的序列号
            'pwd'=>$this->getpwd(), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
            'mobile'=>'',//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
            'content'=>'',//短信内容
            'ext'=>'',		
            'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
            'msgfmt'=>'',
            'rrid'=>''
        );
    }
    
    public function mobile($mobile){
        $this->_params['mobile'] = $mobile;
        return $this;
    }
    public function times($times){
        if($times>0){
            $times=$times+5*60;//增加五分钟延迟
            $stime =  date('Y-m-d H:i:s',$times);
            $this->_params['stime'] = $stime;
        }
        return $this;
    }

    //设置发送的内容
    public function content($content) {
        $this->_params['content'] =$content;// iconv( "gb2312", "UTF-8//IGNORE" ,$content);
        return $this;
    }
    
    private function check(){
        return true;
    }

    //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
    private function getpwd() {
        $str_pwd = $this->sn . $this->pass;
        return strtoupper(md5($str_pwd));
    }

    //
    /*
     * 返回格式数组：array('flag'=>'0','msg'=>'','code'=>'');
     */
    public function send() {
        $ret = array('flag'=>'0','msg'=>'','code'=>'');
        $check_flag = $this->check();
        if(!$check_flag){
            $ret['flag']=0;
            $ret['msg']='发送失败';
            $ret['code']='';
            return ret;
        }
        
        $flag = 0;
        //构造要post的字符串 
        foreach ($this->_params as $key => $value) {
            if ($flag != 0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key . "=";
            $params.= urlencode($value);
            $flag = 1;
        }
        $length = strlen($params);
        //创建socket连接 
        $fp = fsockopen($this->ip, $this->port, $errno, $errstr, 10) or exit($errstr . "--->" . $errno);
        //构造post请求的头 
        $header = "POST /webservice.asmx/mdsmssend HTTP/1.1\r\n";
        $header .= "Host:{$this->ip}\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . $length . "\r\n";
        $header .= "Connection: Close\r\n\r\n";
        //添加post的字符串 
        $header .= $params . "\r\n";
        //发送post的数据 
        fputs($fp, $header);
        $inheader = 1;
        while (!feof($fp)) {
            $line = fgets($fp, 1024); //去除请求包的头只显示页面的返回数据 
            if ($inheader && ($line == "\n" || $line == "\r\n")) {
                $inheader = 0;
            }
            if ($inheader == 0) {
                
            }
        }
        $line = str_replace("<string xmlns=\"http://tempuri.org/\">", "", $line);
        $line = str_replace("</string>", "", $line);
        $result = explode("-", $line);
        if (count($result) > 1){
            $ret['flag']=0;
            $ret['msg']='发送失败';
            $ret['code']=$line;
            
        }else{
            $ret['flag']=1;
            $ret['msg']='发送成功';
            $ret['code']=$line;
        }
        return $ret;
    }

}
