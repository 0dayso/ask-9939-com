<?php

/*
 * 
 * 
 * 
 * 运营商:名商通SMS
 * http://www.139000.com/
 */

class QLib_Message_SMS_MSTSDK {

    private $_params = array();

    public function __construct($config) {
        $this->init($config);
    }

    public function init($config) {
        foreach ($config as $k => $v) {
            $this->{$k} = $v;
        }
        $this->_params = array(
            'name' => $this->name, ////用户名
            'pwd' => $this->pwd, //密码
            'dst' => '', //手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于100个手机号
            'msg' => '', //短信内容
            'time' => '',
        );
    }

    public function mobile($mobile) {
        $this->_params['dst'] = $mobile;
        return $this;
    }

    public function times($times) {
        if ($times > 0) {
            $times = $times + 5 * 60; //增加五分钟延迟
            $stime = date('YmdHis', $times);
            $this->_params['time'] = $stime;
        }
        return $this;
    }

    //设置发送的内容
    public function content($content) {
        $this->_params['msg'] = QLib_Utils_String::String()->utfToGbk($content);// iconv( "UTF-8", "gb2312//IGNORE" ,$content);
        
        return $this;
    }

    private function check() {
        return true;
    }

    private function gethost() {
        if (!empty($this->port)) {
            return "http://" . $this->ip . ":" . $this->port;
        }
        return "http://" . $this->ip;
    }

    //
    /*
     * 返回格式数组：array('flag'=>'0','msg'=>'','code'=>'');
     */
    public function send() {
        $ret = array('flag' => '0', 'msg' => '', 'code' => '','return_info'=>'');
        $check_flag = $this->check();
        if (!$check_flag) {
            $ret['flag'] = 0;
            $ret['msg'] = '发送失败';
            $ret['code'] = '';
            return ret;
        }
        $host = $this->gethost();
        $url = $host . '/send/gsend.asp';
        $post_data = http_build_query($this->_params);
        $post_data =  QLib_Utils_String::String()->utfToGbk($post_data); //iconv( "UTF-8", "gb2312//IGNORE" ,$post_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $result = curl_exec($ch);
        $result = QLib_Utils_String::String()->gbkToUtf8($result);// iconv( "gb2312", "UTF-8//IGNORE" ,$result);
        parse_str($result,$return_arr);
        
        if (intval($return_arr['num'])==0) {
            $ret['flag'] = 0;
            $ret['msg'] = '发送失败';
            $ret['code'] = $result;
        } else {
            $ret['flag'] = $return_arr['num'];
            $ret['msg'] = '发送成功';
            $ret['code'] = '';
            $ret['return_info']=$return_arr;
        }
        return $ret;
    }

}
