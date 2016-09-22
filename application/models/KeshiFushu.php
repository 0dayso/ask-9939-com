<?php
/**
  *##############################################
  * @FILE_NAME :KeshiFushu.php
  *##############################################
  *
  * @author : 魏鹏
   * @MailAddr : 123109769@qq.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   附属分类表
  *==============================================
  */

class KeshiFushu extends QModels_Ask_Table
{
	protected $_name = 'wd_keshi_fushu';

    /**
	* 添加附属分类
	*
	* @param 附属分类内容 array
	* @return 插入ID int
	*/
	public function add($param) {
	    $db = $this->getAdapter();
		$param = $this->trimCol($param);
		unset($param[$this->primary]);
        $db->insert($this->_name,$param);
		return $db->lastInsertId();
	}
    //查询附属分类列表id 
    public function List_AllZ($where='1', $order='', $count=-1, $offset=-1) {
		$sSQL = "select a.id,b.id as kid,a.keshiid,b.name,a.description,a.type,a.listorder,a.ctime from `$this->_name` as a,`wd_keshi` as b ";
        if($where!=""){
            $sSQL.=" where a.keshiid=b.id AND ".$where." ";
        }else{
            $sSQL.=" where a.keshiid=b.id ";
        }
        if($order!="")
            $sSQL.=" order by ".$order." ";  
        if($count!=-1&&$offset!=-1){
            $sSQL.=" limit $offset,$count";
        }
        $result = $this->_db->fetchAll($sSQL);
		return $result;	
	}
    //更改附属分类
    public function updatefushu($kid,$str){
        $str1=$this->List_keshiidstr($kid);
        $shuz=explode(",",$str);
        $shuz1=explode(",",$str1);
        foreach($shuz1 as $v){
            if($v!=""){
                if(!in_array($v,$shuz)){
                    $this->_db->delete($this->_name, 'keshiid = '.$kid.' AND pid='.$v);
                }
            }
        }
        foreach($shuz as $v){
            if($v!=""){
                if(!in_array($v,$shuz1)){
                    $data['pid'] = $v;
                    $data['keshiid'] = $kid;
                    $data['ctime'] = time();
                    if (($nbid=$this->add($data)) > 0) {
                        $ndata=array(
                            'listorder'=>$nbid
                        );
                        $this->update($ndata," id= $nbid ");
                        
                    }
                }
            }
        }
        
    }
    //返回科室所对应的附属分类
    public function List_keshiidstr($kid){
        $sSQL = "select id,pid from `$this->_name` ";
        $sSQL.=" where keshiid='$kid' ";
        $result = $this->_db->fetchAll($sSQL);
        $str="";
        foreach($result as $v){
            if($str!="")$str.=",";
            $str.=$v['pid'];
        }
		return $str;	
    }
    //查询分类按级别
    public function List_pid($pid){
        $sSQL = "select id,keshiid,name,description,type,listorder,ctime from `$this->_name` ";
        $sSQL.=" where pid=$pid ";
        $sSQL.=" order by listorder asc ";  
        $result = $this->_db->fetchAll($sSQL);
		return $result;	
    }
    //查询附属分类列表id 
    public function List_All($where='1', $order='', $count=-1, $offset=-1) {
		$sSQL = "select id,keshiid,name,description,type,listorder,ctime from `$this->_name` ";
        if($where!="")
            $sSQL.=" where ".$where." ";
        if($order!="")
            $sSQL.=" order by ".$order." ";  
        if($count!=-1&&$offset!=-1){
            $sSQL.=" limit $offset,$count";
        }
        $result = $this->_db->fetchAll($sSQL);
		return $result;	
	}
    public function delete($id){
        return $this->_db->delete($this->_name, 'id = '.$id);
    }
    public function update($data,$where){
        return $this->_db->update($this->_name, $data,$where);
    }
    /**
	* 查询单个附属分类
	*
	* @param 条件
	* @return 关注点栏目信息 array
	*/
	public function getById($id) {
		$result=$this->_db->fetchRow("select * from `$this->_name` where id=$id");
		return $result;
	}
    
 
    //查询数量
    public function GetCount($where="1") {
		$where = !$where ? "1" : $where;
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `$this->_name` as a where ".$where);
		return $result[0]['num'];
	}
	private function trimCol($param) {
		foreach ($param as $k => &$v) {
			if(!in_array($k, $this->_getCols())){
				unset($param[$k]);
			}
		}
		return $param;
	}


}
?>