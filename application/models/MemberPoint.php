<?php
/* 
 * 会员积分处理类
 * @author 林原 2010-10-12
 */

Zend_Loader::loadClass("Member",MODELS_PATH);
Zend_Loader::loadClass("Credit",MODELS_PATH);

class MemberPoint {
    private $member_obj;  //会员对象
    private $memberId;  //会员id
    private $member_info; //会员信息
    private $history_obj; //积分历史记录对象

    public $errorMsg; //错误消息

    /**
     * 构造函数
     * @param int $memberId 会员id
     */
    public function __construct($memberId) {
         $this->member_obj = new Member();
         $this->history_obj = new Credit();
         $this->memberId = $memberId;
         $this->member_info = $this->member_obj->fetchRow("uid=".$this->memberId);
    }

    /**
     * 计算会员积分并修改
     * @param array $rule_arr 要计算的积分规则数组 数组格式：array('get'=>array('ask_pub','ask_common_reply'),'pay'=>array('ask_pub'))
     * @return bool
     */
    public function CountPoint($rule_arr) {
        //引入积分规则缓存文件
        //计算积分,会员经验值
        //判断积分是否不足
        //修改会员积分，会员经验值
        //添加到会员积分历史表中
        //重新设置cookie里面的积分，和经验值

        $pointCachePath = APP_DATA_PATH.'/data_creditrule.php';
        if(file_exists($pointCachePath)) {
            include $pointCachePath;
        } else {
            $this->errorMsg= '积分规则缓存文件无法找到！';
            return false;
        }

     if($_SGLOBAL['creditrule']) {
            $pointRule = $_SGLOBAL['creditrule'];
        }

        //计算积分,经验值
        $point = 0;  //积分
        $experience = 0; //经验值
        if($rule_arr['get']) {  //计算要加的积分,经验值
            if($pointRule['get']) $getRule = $pointRule['get'];
            foreach($rule_arr['get'] as $val) {
                if($getRule[$val]) $point += $getRule[$val];
            }
            $experience += $point;
        }
        
        if($rule_arr['pay']) {  //计算要减少的积分
            if($pointRule['pay']) $payRule = $pointRule['pay'];
            foreach($rule_arr['pay'] as $val) {
                if($payRule[$val]) $point -= $payRule[$val];
            }
        }

        //获取会员原始积分,经验值和昵称
        if($member_info) {
            $member_point = $this->member_info->credit;  //会员积分
            $nickname = $this->member_info->nickname;  //会员昵称
            $experience_now = $this->member_info->experience; //会员经验值
        }

        //判断积分
        if($point<0 && $member_point<abs($point)) {
            $this->errorMsg = "积分不足！";
            return false;
        }

        //计算最终积分
        $point = $member_info+$point;  
        //最终经验值
        $experience += $experience_now;

        //修改会员的积分
        $this->member_obj->update(array('credit'=>$point,'experience'=>$experience),"uid=".$this->memberId);

        //添加积分历史记录
        foreach($rule_arr as $val) {
            foreach($val as $k=>$v) {
                $arr = array();
                $arr['uid'] = $this->memberId;
		$arr['nickname'] = $this->member_info->nickname;
		$arr['creditmode'] = $k;
		$arr['optype'] = $v;
		$arr['credit'] = $pointRule[$k][$v];
		$arr['name'] = $pointRule[$k.'Name'][$v];
		$arr['dateline'] = time();
                $this->history_obj->Add($arr); //添加到历史记录
            }
        }

        //重新设置cookie
        $this->Member_obj->ssetcookie('member_credit',$point);
        $this->Member_obj->ssetcookie('member_experience',$experience);
        $this->Member_obj->ssetcookie('member_groupinfo',"",$this->member_info->uType,$point);

        return true;

    }

}

?>
