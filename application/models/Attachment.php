<?php
/**
  *##############################################
  * @FILE_NAME :Attachment.php
  *##############################################
  *
  * @author :   矫雷
  * @MailAddr : kxgsy163@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   图片模型详细模块
  *==============================================
  */



Zend_Loader::loadClass('Zend_File_Transfer_Adapter_Http');

class Attachment extends QModels_Ask_Table
{
	protected $_name ="attachment";
	protected $primary = 'aid';
	
	
	
	/**
	* 添加图片附件
	*
	* @param 图片附件信息 array
	* @return 插入ID int
	*/
	public function add($info = array(), $param = array()) {
		$info = $this->getParam($info, $param);
		$info = $this->trimCol($info);
		$this->insert($info);
	}

	/**
	* 编辑文章
	*
	* @param 图片附件ID int
	* @param 图片附件信息 array
	* @return  int
	* 未使用
	*/
	public function edit ($info = array(), $param = array()) {
		exit('参数错误！Attachment:edit()');
		$info = $this->getParam($info, $param);
		$tmp_articleid = intval($param['articleid']); //文章ID
		$where = "articleid = $tmp_articleid";
		unset($param['articleid']);
		//echo "<hr>";
		//print_r($param);
		//echo 'edit';exit;
		//print_r($param); exit;
		//$this->update($param, $where);
		//return $tmp_articleid;
	}
	
	/**
	* 
	* 取得附件表信息:用于图谱模型
	* $where 条件
	*/
	public function listInfo($where = 1) {
		$order = $this->primary .' ASC ';
		$result = $this->fetchAll($where, $order);
		return $result->toArray();
	}
	
	
	/**
	* 
	* 附件删除：同时删除文件
	* @param $aid 字符串
	*/
	public function del($dir='', $aid = 0) {
		$where = $this->primary .' IN ('. $aid .') ';
		$listInfo = $this->listInfo($where);
		foreach($listInfo as $k => $v) {
			unlink(APP_ROOT.$dir.$v['filepath']);                                                            //删除文件
		}
		$this->_db->delete($this->_name, $where);
	}
	
	
	
	
	
	
	
	/**
	*
	* 取得入库信息
	* @param  $info:基本信息
	* @param  $param:图片信息
	* renturn array
	*/
	private function getParam($info = array(), $param = array()) {
		$info['filename'] = $param['name'];
		$info['filepath'] = $param['filename'];
		$info['filetype'] = $param['type'];
		$info['filesize'] = $param['size'];
		$info['fileext'] = substr($param['name'], (strpos($param['name'], '.') + 1));
		$info['isimage'] = 1;
		$info['uploadtime'] = time();
		return $info;
	}
	
	
	
	/**
	* 去除param数组中键值为非列名单元
	*/
	private function trimCol($param) {
		foreach ($param as $k => &$v) {
			if(!in_array($k, $this->_getCols())){
				unset($param[$k]);
			}
		}
		return $param;
	}
	
	
	
	
	
	/**
	 * @desc 上传图片 2009-07-14
	 * 
	 *
	 */
	public function uploadPic($dir="",$ext=""){		
		$dateDir = date('Ym').'/';
		$fileDir = APP_ROOT.$dir.$dateDir;
		
		$mask_int = umask();
		umask(0);
		if (!is_dir($fileDir)) mkdir($fileDir, 0777, true);
		umask($mask_int);
		
		$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->setDestination($fileDir);
		$adapter->addValidator('FilesSize', false, array('min' => '1kB', 'max' => '2MB'));
		$adapter->addValidator('Extension', false, 'jpg,png,gif,doc');
		
		
		$uplod_Filename = strtolower($adapter->getFileName());		
		//echo $uplod_Filename; exit;		
		//print_r($adapter);exit;
		$pos = strrpos($uplod_Filename, '.');
		if ($pos) {					
			$fileNamelast = substr(strrchr($uplod_Filename, '.'), 1);			
		}			
		
		$fileName = time().$ext.'.'.$fileNamelast;//新文件名				
		
		$adapter->addFilter('Rename', $fileDir.$fileName);
		
		//echo $fileDir.$fileName; exit;
		
		//print_r($adapter);exit;
		
		if ($adapter->receive()){		
			chmod($fileDir.$fileName, 0777);			
			return $dateDir.$fileName;	
			
		}
		else {
			echo 'error';
			return false;					
		}	
	}
}
?>