<?php
/**
  *##############################################
  * @FILE_NAME :
  *##############################################
  * @author : kerry
  * @mail : 6302743@qq.com
  * @copyright : Copyright (c) 2009 中视在线
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : 1.0
  * @DATE : Mon Jul 06 14:17:01 CST 2009
  *==============================================
  * @Desc :  留言数据分析操作类
  *==============================================
  */
class Fkanaylize extends QModels_Ask_Table
{

	public function getanaylist($day,$timeindex,$idgg)
	{
		try{
			$db=$this->getAdapter();
			$select = $db->select();
			$select->from('Fkanaylize', '*');
			$select->where('day = ?', $day);
			$select->where('timeindex = ?', $timeindex);
			$select->where('idgg = ?', $idgg);
			$sql=$select->__toString();
			$list=$db->fetchAll($sql);
			return $list;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function getanaylistbytime($time,$idgg)
	{
		$date=date("Y-m-d",$time);
		$paiqi_config=new Zend_Config_Ini(APP_CONFIG_FILE, 'adver_paiqi');
		$arr=$paiqi_config->toArray();
		$tmp_arr=$arr['paiqi'];
		$i=0;
		foreach($tmp_arr as $val)
		{
			$time_arr=explode("-",$val);
			$starttime=$date." ".$time_arr[0];
			$endtime=$date." ".$time_arr[1];
			$starttime=strtotime($starttime);
			$endtime=strtotime($endtime);
			if($time>=$starttime && $time<=$endtime)
			{
				$index=$i;
				break;
			}

			$i++;
		}

		return $this->getanaylist($date,$index,$idgg);
	}


	/*
	根据广告按时间段求总数
	@array $data=array('start'=>"开始时间int",'end'=>"结束时间int")
	@int $idgg 广告编号
	*/
	public function getTotalByIdgg($data,$idgg)
	{
		if($data['start']&&$data['end']&&$idgg)
		{
			$data['start']=date("Y-m-d",$data['start']);
			$data['end']=date("Y-m-d",$data['end']);
			$sql="select count(*) as gtotal from Fkanaylize where day between '".$data['start']."' and '".$data['start']."' and idgg=".intval($idgg);
			$ret=$this->_db->fetchAll($sql);

			$total=intval($ret[0]['gtotal']);

			return $total;
		}
	}

	/*
	根据广告位按时间段求总数
	@array $data=array('start'=>"开始时间int",'end'=>"结束时间int")
	@int $idggw 广告位
	*/
	public function getTotalByIdggw($data,$idggw)
	{
		if($data['start']&&$data['end']&&$idggw)
		{
			$data['start']=date("Y-m-d",$data['start']);
			$data['end']=date("Y-m-d",$data['end']);
			$sql="select count(*) as gtotal from Fkanaylize where day between '".$data['start']."' and '".$data['start']."' and idggw=".intval($idggw);
			$ret=$this->_db->fetchAll($sql);

			$total=intval($ret[0]['gtotal']);

			return $total;
		}
	}

	/*
	*根据广告位按时间段求
	*@array $data=array('start'=>"开始时间int",'end'=>"结束时间int")
	*AUTHoR:zzh
	*/
	public function getGarrByIdggw($data)
	{
		if($data['starttime']&&$data['endtime'])
		{
			if($data['idbm']>0){
				$where = " AND idbm=".$data['idbm'];
			}
			if($data['idggw']){
				$where .= " and idggw IN ('".$data['idggw']."') ";
				$tmp_group = "idggw";
			}
			if($data['idgg']){
				$where .= " and idgg IN ('".$data['idgg']."') ";
				$tmp_group = "idgg";
			}
			$data['start']=date("Y-m-d",$data['starttime']);
			$data['end']=date("Y-m-d",$data['endtime']);
			$sql="select idggw,idgg,sum(gcount) as gcountx,sum(dcount) as dcountx
			 from Fkanaylize 
			 where day between '".$data['start']."' and '".$data['end']."' ".$where
			."group by ".$tmp_group;
			$ret=$this->_db->fetchAll($sql);

			foreach ((array) $ret as $v){
				$tmp_arr[$v[$tmp_group]]['gcount'] = $v['gcountx'];
				$tmp_arr[$v[$tmp_group]]['dcount'] = $v['dcountx'];
			}
			return $tmp_arr;
		}
	}

	/**
	 * 根据部门id获取yidggw（此表中是idzy）
	 *
	 * @param int $idbm
	 */
	public function getyidggw($idbm)
	{
		if(!$idbm) return ;
		$sql = "SELECT A.idzy,B.ggwname
		FROM Fkanaylize AS A
		LEFT JOIN Guanggaowei AS B
		ON A.idzy=B.idggw
		WHERE A.idbm=".$idbm;
		$ret=$this->_db->fetchAll($sql);
		foreach ((array) $ret as $v)
		{
			$tmp_idarr[$v['idzy']] = $v['ggwname'];
		}
		return $tmp_idarr;
	}

	/**
  * 获取留言数据
  *
  * @param array $data
  */
	public function GetgbookList($data)
	{
		if(count($data['keyname'])<1 || $data['idzy']=="") return ;

		$knameArr=$data['keyname'];

		foreach ((array) $knameArr as $k=>$v){
			$selectStrArr[$v]=" sum(A.".$v.") AS ".$v."x  ";
			$orDerArr[]=$v."x";
		}

		$selectStr=@implode(",",$selectStrArr);
		$orDerStr=@implode(" DESC ,",$orDerArr);


		if($data['idbm']>0){
			$where_and = " AND A.idbm=".$data['idbm'];
		}

		if($data['idzy']){
			$where_and .= " AND A.idzy IN ('".$data['idzy']."')";
		}

		if($data['starttime'] && $data['endtime']){
			$where_and .= " AND (A.day BETWEEN '".$data['starttime']."' AND '".$data['endtime']."') ";
		}

		if($data['group']){
			$tmp_group = ' GROUP BY '.$data['group'];
			$OrDerStr  =  ' A.ctime ';
		}else{
			$tmp_group = ' GROUP BY A.ctime,A.timetype ';
		}

		if($data['leftjoin']){
			$tmp_leftjoin = ' ';
		}

		$select_sql = "SELECT
					A.idzy,A.day,A.timetype, ".$selectStr." 
					FROM `Tjbyurl` AS A
					".$tmp_leftjoin."
					WHERE 1 ".$where_and." 
					".$tmp_group."
		            ORDER BY ".$orDerStr."  DESC";

		$result = $this->_db->fetchAll($select_sql);
		//print_r($result);
		foreach ((array) $result as $v){

			//饼图用
			$tmp_data['btdata'][$v['timetype']][$data['keyname']['gcount']]  += $v[$data['keyname']['gcount'].'x'];
			$tmp_data['btdata'][$v['timetype']][$data['keyname']['dcount']]  += $v[$data['keyname']['dcount'].'x']; 
			unset($tmp_data['btdata'][$v['timetype']]['']);

			//统计列表用
			$tmp_data['list'][$v['ctime']]['allnum'][$data['keyname']['gcount']] += $v[$data['keyname']['gcount'].'x'];
			$tmp_data['list'][$v['ctime']]['allnum'][$data['keyname']['dcount']] += $v[$data['keyname']['dcount'].'x']; 
			$tmp_data['list'][$v['ctime']]['alllist'][$v['timetype']][$data['keyname']['gcount']] = $v[$data['keyname']['gcount'].'x'];
			$tmp_data['list'][$v['ctime']]['alllist'][$v['timetype']][$data['keyname']['dcount']] = $v[$data['keyname']['dcount'].'x']; 
			unset($tmp_data['list'][$v['ctime']]['allnum']['']);
			unset($tmp_data['list'][$v['ctime']]['alllist'][$v['timetype']]['']);
			 
		}
		return $tmp_data;
	}
}
?>