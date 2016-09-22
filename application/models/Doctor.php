<?php
/**
 *##############################################
 * @FILE_NAME :Doctor.php
 *##############################################
 * @author : frozen
 * @mail : 276896323@qq.com
 * @copyright : Copyright (c) 2009 中视在线
 * @PHP Version :  Ver 5.21
 * @Apache  Version : Ver 2.20
 * @MYSQL Version : Ver 5.0
 * @Version : 1.0
 * @DATE : Fri Jun 19 17:12:03 CST 2009
 *==============================================
 * @Desc :  用户操作类
 *==============================================
 */
class Doctor extends QModels_Ask_Table {
    public function init() {
        $this->db_jb = $GLOBALS['db_jb_obj'];
    }

    /**
     * @Desc
     * @param
     * @param
     * @param
     * @return
     */

    protected $_name="doctor";


    /**
     * @Desc 获取某用户信息
     * @param void
     * @return bool
     */
    public function get_one($doctor_id) {
        $sql="select * from doctor where doctor_id=".$doctor_id."";
        $row=$this->db_jb->fetchRow($sql);
        return $row;
    }
    /**
     * @Desc 根据条件获取某些用户信息
     * @param void
     * @return bool
     */
    public function get_doctor($where) {
        $sql="select * from doctor $where";
        $row=$this->db_jb->fetchAll($sql);
        return $row;
    }
    /**
     * @Desc 添加用户信息
     * @param void
     * @return bool
     */

    function add_one($info) {
        try {
            $effact_row=$this->db_jb->insert("doctor",$info);
            return $effact_row;
        }catch(Exception $e) {
            echo $e;
        }
    }
    /**
     * @Desc 更新某用户信息
     * @param void
     * @return bool
     */

    function update_one($info,$doctor_id='') {
        try {
            if(!$doctor_id) {
                $doctor_id=$info['doctor_id'];
            }
            $effact_row=$this->db_jb->update("doctor",$info,"doctor_id=".$doctor_id."");
            return $effact_row;
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @Desc 获取医生信息
     * @param void
     * @return bool
     */
    public function get_doctor_list($where, $order, $count, $offset) {
        $sql="select * from doctor where $where order by $order limit $offset,$count ";
        $row=$this->db_jb->fetchAll($sql);
        return $row;
    }
    /**
     * @Desc 获取医生个数
     * @param void
     * @return bool
     */
    public function get_doctor_count($where) {
        $sql="select count(*) as num from doctor where $where ";
        $row=$this->db_jb->fetchRow($sql);
        return $row['num'];
    }
    /**
     * @Desc 删除医生
     * @param void
     * @return bool
     */
    function del_one($id) {
        $sql="update doctor set del=2 where doctor_id in ($id)";
        $this->db_jb->query($sql);
    }

    //获取某个科室的id
    public function getSectionId($keShiName) {
        $sql = "select id from 9939_section_category where name='".$keShiName."'";
        $row=$this->db_jb->fetchAll($sql);
        return $row;
    }

    //验证某个科室是否属于该医院
    public function getSectionHos($sectionId,$hospitalId) {
        $sql = "select id from section_introduce where section_id='".$sectionId."' AND hospital_id='".$hospitalId."'";
        $row=$this->db_jb->fetchAll($sql);
		print_r( $row);
        return $row;
    }
}
?>