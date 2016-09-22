<?php
/**
  *##############################################
  * @FILE_NAME :Pic.php
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
  * @Desc :   文章模块
  *==============================================
  */

class Pic extends QModels_Ask_Table
{
	protected $_name ="member_pic";
	private $primary = 'picid';
	private $postip = 'postip';
	private $userid = 'uid';
	private $filename = 'filename';
	private $title = 'title';
	private $uptime = 'dateline';
	private $friend = 'friend';

	
	
	
	
	/**
	* 
	* 返回成员变量值
	*/
	public function getValue($var='') {
		if(!$var) return '';
		if(!in_array($var, array_keys(get_object_vars($this)))) return '';
		return $this->$var;
	}
	
	
	/**
	* 添加文章
	*
	* @param 文章信息 array
	* @return 插入ID int
	*/
	public function add_Pic($param) {
		
		$param['dateline'] = time();
		
		//去除主键
		unset($param[$this->primary]);
		//去除param数组中键值为非列的值
		$param = $this->trimCol($param);
		return $this->insert($param);
	}

	/**
	* 编辑文章
	*
	* @param 文章ID int
	* @param 文章信息 array
	* @return  int
	*/
	public function edit_Pic ($param) {
		$tmp_id = intval($param[$this->primary]); //文章ID
		
		
		$where = $this->primary .'=\''. $tmp_id .'\'';
		$where .= ' AND '. $this->userid .'=\''. $param[$this->userid] .'\'';
		
		$param['dateline'] = time();
		
		//去除主键
		unset($param[$this->primary]);
		//print_r($param); exit;
		//去除param数组中键值为非列的值
		$param = $this->trimCol($param);
		$param = $this->trimValueIsNull($param);
		return $this->update($param, $where);
	}

	/**
	* 查看图片
	*
	* @param 条件
	* @return 图片信息 array
	*/
	public function list_Pic($where='', $order='', $count='', $offset='') {
		#echo $where, $order, $count, $offset;
		$result = $this->fetchAll($where, $order, $count, $offset);
		return $result->toArray();
	}
	
	/**
	* 查看图片
	*
	* @param 条件
	* @return图片信息 array
	*/
	public function get_one_Pic($id='') {
		if(!$id)return '';
		
		$where = $this->primary .'='. intval($id);
		$sql = 'SELECT `'. implode('`,`', $this->_getCols()) .'` FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchRow($sql); //获取一行
		return $result;
	}
	
	
	public function getUrlById($id='') {
		if(!$id)return '';
		$result = $this->get_one_Pic($id);
		#return $result['thumb'] ? str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$result['filepath'].'thumb_'.$result['filename']) : str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$result['filepath'].$result['filename']);
		return str_replace(APP_ROOT, '', APP_PIC_ROOT.'/'.$result['filepath'].$result['filename']);
		#return $result[$this->filename];
	}
	
	/**
	* 删除图片
	*
	* @param $param:array
	* @return bool
	*/
	public function del_Pic($param = array()) {
		$picid = $param['picid'];
		if(is_array($picid)) {
			$where = $this->primary .' IN ('. implode(',', $picid) .')';
		} else {
			$where = $this->primary .'='. intval($picid);
		}
		$where .= ' AND '. $this->userid .'=\''. $param[$this->userid] .'\'';
		#$result = $this->_db->delete($this->_name, $where);    //系统假删除： 遗弃
		$result = $this->delete($where);
		return $result;
	}
	
	
	
	/**
	* 删除图片
	*
	* @param $param:array
	* @return bool
	*/
	public function del($picid = '') {
		if(is_array($picid)) {
			$where = $this->primary .' IN ('. implode(',', $picid) .')';
		} else {
			$where = $this->primary .'='. intval($picid);
		}
		#$result = $this->_db->delete($this->_name, $where);    //系统假删除： 遗弃
		
		$result = $this->delete($where);
		return $result;
	}
	
	/**
	 × 加载默认类对象成员
	 */
	private function newObj() {
		
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
	
	
	
	//获取数据总和
	public function numRows($where=1) {
		$sql = 'SELECT count('. $this->primary .') as count FROM `'. $this->_name .'` WHERE '. $where;
		$result = $this->_db->fetchRow($sql); 
		return $result['count'];
	}
	
	
	/**
	* 
	* 修改排序值
	* @param $param array
	* @param $articleid:ID值
	*/
	public function listorder($param, $articleid) {
		$where = $this->primary .'='. $articleid;
		$param = $this->trimCol($param);
		$this->update($param, $where);
	}
	
	
	
	
	
	/**
	 * @desc 上传图片 2009-07-14
	 * 
	 *
	 */
	public function uploadPic($dir='', $ext=""){		
		$fileDir = APP_PIC_ROOT.'/'.trim($dir, '/') .'/';
		
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
			return $fileName;	
			
		}
		else {
			echo 'error';
			return false;					
		}	
	}
	
	
	
	
	public function thumb($file='', $width=130, $height=130) {
		if(!$file) return 0;
		$filename = APP_PIC_ROOT . '/'. trim($file, '/');
		
		$ext = strtolower(substr($file, -strpos(strrev($file), '.')));
		$ext = strtolower($ext);
		$ext = ($ext=='jpg') ? 'jpeg' : $ext;
		if($ext=='gif') {
			return 0;
		}
		$filename_new = substr($filename, 0, -strpos(strrev($filename), '/')) . 'thumb_'.substr($file, -strpos(strrev($filename), '/'));
		$info = getimagesize($filename);
		$from_w = $info[0];
		$from_h = $info[1];
		$to_w = $width;
		$to_h = $height;
		#echo $from_w, $from_h;exit;
		
		$w_w = $from_w/$to_w;
		$h_h = $from_h/$to_h;
		if($w_w>$h_h) {
			$w = $to_w;
			$h = $from_h / $w_w;
			$x = 0;
			$y = ($to_h - $h) / 2;
		} else {
			$h = $to_h;
			$w = $from_w / $h_h;
			$x = ($to_w - $w) / 2;
			$y = 0;
		}
		$func = 'imagecreatefrom'. $ext;
		if(function_exists($func)) {
			ob_start();
			$from = $func($filename);
			$to = imagecreatetruecolor($width, $height);
			imagecolorallocate($to, 255, 255, 255);
			imagecopyresampled($to, $from,$x, $y, 0, 0, $w, $h, $from_w, $from_h);
			$func = 'image'. $ext;
			$func($to, $filename_new, '100');
			imagedestroy($to);
			imagedestroy($from);
			$data = ob_get_contents();
			ob_end_clean();
			if($data) {
				return 0;
			}
			return 1;
		} else {
			return 0;
		}
	}

	
	
	
	
	
	
	
	private function trimValueIsNull($param=array()) {
		if(!$param)return '';
		foreach ($param as $k => $v) {
			if(!$v) {
				unset($param[$k]);
			}
		}
		return $param;
	}
}



?>