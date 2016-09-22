<?php

/*
* 漫道短信 httpservice
* 
* 
* 运营商:北京创世漫道科技有限公司 
* http://self.zucp.net/main.htm 
*/
class QLib_Message_SMS_HSSDK {
    private $_params = array();
    public function __construct($config) {
        $this->init($config);
    }
    
    public function init($config){
        foreach($config as $k=>$v){
            $this->{$k} = $v;
        }
        $this->_params = array(
            'userid'=>$this->userid, ////替换成您自己的序列号
            'account'=>$this->account, //
            'password'=>$this->password,
            'content'=>'',//短信内容
            'mobile'=>'',//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号	
            'sendtime'=>'',//定时时间 格式为2011-6-29 11:09:21
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
    
    private function gethost(){
        return "http://".$this->ip.":".$this->port;
    }
    
    private function check(){
        return true;
    }

    //
    /*
     * 返回格式数组：array('flag'=>'0','msg'=>'','code'=>'');
     * 
        QLib_Message_SMS_Client::send(15810309771, '您正在注册账户，校验码475818，请于30分钟内输入，工作人员不会索取，请勿泄漏。');
        exit;
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
        $host = $this->gethost();
        $url=$host.'/sms.aspx?action=send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($this->_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $result = curl_exec($ch);
        
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
