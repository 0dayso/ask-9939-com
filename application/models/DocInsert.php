<?php

class DocInsert extends QModels_Ask_Table{
    
    public function init() {
        try {
            $db_write_config = new Zend_Config_Ini(APP_CONFIG_FILE, 'db_write');
            $this->db = Zend_Db::factory('PDO_MYSQL', $db_write_config->db);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function select(){
        $sql="SELECT nickname,username from member where doc_from='1'";
        $res=$this->db->query($sql)->fetchAll();
        return $res;
    }

    public function save($cols_mem,$vals_mem,$cols_det,$vals_det,$username){
        
       
//        $sql = "INSERT INTO member(".$cols_mem.") VALUES(".$vals_mem.")";
//        $res = $this->db->query($sql);
//        
//        $sql_uid="select uid from member where username='".$username."'";
//        $res = $this->db->query($sql_uid)->fetchAll();
//        
//        $vals_det="'".$res['0']['uid']."',".$vals_det;
//        $sql2 = "INSERT INTO member_detail_2(".$cols_det.") VALUES(".$vals_det.")";      
//        $res1 = $this->db->query($sql2);

        $this->db->beginTransaction();
        try {
            
            $sql = "INSERT INTO member(" . $cols_mem . ") VALUES(" . $vals_mem . ")";
            $res = $this->db->query($sql);

            $sql_uid = "select uid,nickname,username,doc_from from member where username='" . $username . "'";
            $res_uid = $this->db->query($sql_uid)->fetchAll();

            $vals_det = "'" . $res_uid['0']['uid'] . "'," . $vals_det;
            $sql2 = "INSERT INTO member_detail_2(" . $cols_det . ") VALUES(" . $vals_det . ")";
            $res2 = $this->db->query($sql2);
            $this->db->commit();
            $res_uid['0']['exist']="add";
            return $res_uid['0'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return '添加失败！';//$this->db->getMessage();
        }

    }
   
}
