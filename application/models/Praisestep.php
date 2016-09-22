<?php


class Praisestep extends QModels_Ask_Table
{
	public $_name ="praisestep_cache";

		/**
	* 添加文章
	*
	* @param 文章信息 array
	* @return 插入ID int
	*/
	public function add($param) {
		
		$param['addtime'] or ( $param['addtime']=time());
		
		//去除主键
		unset($param[$this->primary]);
		//去除param数组中键值为非列的值
		//$param = $this->trimCol($param);
		//var_dump($param);
		return $this->insert($param);
	}
    	/**
	* 添加未登录顶踩ip
	*
	* @param 顶踩信息 array
	* @return 插入ID int
	*/
	public function add_ip($param) {
		$param['addtime'] or ( $param['addtime']=time());
        $sql = "insert into `praisestep_ip`(`id`, `addtime`, `mark`, `ip`, `tid`)values(null,".$param['addtime'].",".$param['mark'].",'".$param['ip']."','".$param['tid']."')";
		$this->_db->query($sql);
        return true;
	}
	/**
	* 删除一小时之前的
	*/
    public function del_time($stime) {
		$this->_db->query("delete from `praisestep_ip` where addtime<$stime");
        return true;
	}
    //查询一条数据
    public function GetCount($where) {
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `praisestep_ip` where ".$where); 
		return $result[0]['num'];
	}
 }

?>