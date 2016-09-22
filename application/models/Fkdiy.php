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
  * @Desc :  留言自定义字段
  *==============================================
  */
if(!defined("FANKUI_TPL_PATH")) 
{
	define("FANKUI_TPL_PATH",APP_ROOT."/tpl/gbook");
}

Zend_Loader::loadClass("Fksearch",MODELS_PATH);
Zend_Loader::loadClass("Fkdiymeta",MODELS_PATH);
class Fkdiy extends QModels_Ask_Table
{
		private $fkdiy_meta_meta_tpl="<p>{label_name}:  <label><input type='text' name='{label_key}' id='{label_key}' size=5 /></label></p>";
		private $fkdiy_meta_meta_tpl_hidden="<input type='hidden' name='idfkdiy' value='{idfkdiy}' id='idfkdiy' />";
	
		public function checkHas($data)
 		{
	 		$qxname=trim($data['fkdiyname']);
	 		$db=$this->getAdapter();
	    	$where[] = $db->quoteInto("fkdiyname = ?", $qxname);
		    $row = $this->fetchRow($where);
		    if(!$row)
		    {
		    	return false;
		    }
		    else 
		    {
			    if($row->idfkdiy>0)
			    {
			    	return true;
			    }  
			    else 
			    {
			    	return false;
			    }
		    }
 		}
 	
	 	public function add($var,&$error=null)
	 	{
	 		$data['idfkdiy']=intval($var['idfkdiy']);
	 		$data['fkdiyname']=trim($var['fkdiyname']);
	 		$data['fkdiydesc']=trim($var['fkdiydesc']);
	 		$data['fkdiyarray']=serialize($var['fkdiyarray']);
	 		$data['fkdiystate']=intval($var['fkdiystate']);
	 		$data['idfktpl']=intval($var['idfktpl']);
	 		$filters = array(
		    '*'=>'StringTrim','StripTags'); 
			$validators = array(
		    'fkdiyname' => 'NotEmpty',
		    'fkdiyarray'=> 'NotEmpty'
			);
	 		
			try{
				
				$input = new Zend_Filter_Input($filters, $validators,$data);
				if ($input->isValid("fkdiyname")==false) {
				  	$error="只能是数字和字母组合";
				  	return false;
				}
				elseif ($input->isValid("fkdiyarray")==false) {
				  	$error="属性不能为空";
				  	return false;
				}
				
				if($data['idfkdiy']==0)
				{
					if($this->checkHas($data)==true)
			 		{
			 			$error="该记录已存在";
			 			return false;
			 		}
			 		
			 		unset($data['idfkdiy']);
	 				$ret= $this->insert($data);
	 				
	 				//生成留言本
	 				$data=array();
	 				//留言本编号
	 				$data['id']=$ret;
	 				//客户编号
	 				$data['gid']=1; 
	 				$this->fkmkfkcode($data);
				}
				else 
				{
					$db=$this->getAdapter();
			    	$where[] = $db->quoteInto("idfkdiy = ?", $data['idfkdiy']);
			    	unset($data['idfkdiy']);
		 			$ret= $this->update($data,$where);
				}
			 		
			 	return $ret;	
			}catch (Exception $e)
			{
				echo $e->getMessage();
			}
	 	}
 	
	 	public function getList($data=array())
	 	{
	 		$page=intval($data['page']);
	 		if($data['pagesize']>0&&$data['page']>0) $offset=($page-1)*$data['pagesize'];
	 		else 
	 		{
	 			$offset=0;
	 		}
	 		
	 		$fkdiyname=trim($data['fkdiyname']);
	 		$db=$this->getAdapter();
	 		$select = $db->select();
	 		$select->from('Fkdiy', '*');
	 		if($fkdiyname<>"")
	 		{
		    	$select->where('fkdiyname = ?', $fkdiyname);
	 		}
	 		
	 		$sql=$select->__toString();
	 		
	 		$total=$db->fetchAll($sql);
	 	
	 		$total=count($total);
	 		$ret['total']=$total;
	 		
	 		$select->order('idfkdiy');
	 		$select->limit($data['pagesize'],$offset);
			$sql=$select->__toString();
			$list=$db->fetchAll($sql);
			$ret['list']=$list;
	 		return $ret;
	 	}
	 	
	 	
	 	public function getOne($id)
	 	{
	 		if(intval($id)==0) return false;
	 		$rows=$this->find($id);
	 		$row=$rows->current();
	 		
	 		if($row->idfkdiy>0)
	 		{
	 			$row->fkdiyarray=unserialize($row->fkdiyarray);
	 			return $row;
	 		}
	 		else
	 		{
	 			return null;
	 		}
	 	}
	 	
	 	public function del($id,&$error)
	 	{
	 		if($ret=$this->getOne($id))
	 		{
	 			$db=$this->getAdapter();
		    	$where[] = $db->quoteInto("idfkdiy = ?", $id);
		    	return $this->delete($where);
	 		}
	 		else 
	 		{
	 			$error="参数错误";
	 			return false;
	 		}
	 	}
	 	
	 	
	 	public function gettpllist()
	 	{
	 		static $ret=array();
	 		$i=0;
	 		$ret[$i]['idtpl']="1";
	 		$ret[$i]['tplname']="gbook.php";
	 		$i++;
	 		$ret[$i]['idtpl']="2";
	 		$ret[$i]['tplname']="gbook_w.php";
	 		$i++;
	 		$ret[$i]['idtpl']="3";
	 		$ret[$i]['tplname']="gbook_none.php";
	 		$i++;
	 		$ret[$i]['idtpl']="4";
	 		$ret[$i]['tplname']="gbook_style.php";	
	 		return $ret;
	 	}
	 	
	 	
	 /**
	 * @DESC 取得自定义反馈字段列表并格式化
	 * @author xzx
	 * @return Array $RetunArr //一维数组
	 */
	public function GetOptionsArr($where=1){
		$TemDataArr=$this->getList($where);
		$TemDataArr = $TemDataArr['list'];
		//print_r($TemDataArr); exit;
		$RetunArr=array();
		foreach (($TemDataArr) AS $key => $val){
			$RetunArr[$val['idfkdiy']]=$val['fkdiyname'];
		}
		
		return $RetunArr;
	}
	
	
	/**
	* @Desc 生成留言本代码
	* @param array $data
	 			   //留言本编号 $data['id'];
	 			   //客户编号 $data['gid']=1; 
	* @return string
	*/
	
	public function fkmkfkcode($data)
	{
		$id=intval($data['id']);
		$gid=intval($data['gid']);
		if($id>0)
		{			
			$obj=$this->getOne($id);

			if($obj)
			{
				$tpllist=$this->gettpllist();
				$tpl_map=array();
				foreach($tpllist as $val)
				{
					if($val['idtpl']) $tpl_map[$val['idtpl']]=$val['tplname'];
				}
				
				
				$tpl=$tpl_map[$obj->idfktpl];
			
				$file_path=FANKUI_TPL_PATH."/".$tpl;
				if(count($obj->fkdiyarray)>0)
				{	
					$fkdiymeta_obj=new Fkdiymeta();
					$str="";
					$tmp_arr=array();
					foreach($obj->fkdiyarray as $val)
					{
						$fkdiymeta_one=$fkdiymeta_obj->getbykey($val);
						$str=str_replace("{label_name}",$fkdiymeta_one->fkdiymetavalue,$this->fkdiy_meta_meta_tpl);
						$str=str_replace("{label_key}",$fkdiymeta_one->fkdiymetakey,$str);
						$tmp_arr[$val]=$str;
					}
					
					
					$str=join("\r\n",$tmp_arr);
					
					//增加fkdiy 隐藏属性	
					$fkdiy_hidden_id=str_replace("{idfkdiy}",$id,$this->fkdiy_meta_meta_tpl_hidden);
					$str.=$fkdiy_hidden_id;
				}
				
				$content=file_get_contents($file_path);	
				$content=str_replace("{diy_block}",$str,$content);
				$jspath="/Js/gbook/".$id.".js";
				$gbookjs_common_path=APP_ROOT."/Js/gbook_common.js";

				$gbookjs_common=file_get_contents($gbookjs_common_path);
				$gbookjs_common=str_replace("{adver_id}",$gid,$gbookjs_common);
				$str= Zend_Adver_Tool::htmltoJswrite($content);
				$file_path=APP_ROOT.$jspath;
				$str=$gbookjs_common."\r\n".$str;
				file_put_contents($file_path,$str);
				return  "<script src=".$jspath."></script>";
			}
		}
	}

}
?>