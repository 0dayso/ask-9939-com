<?php
/**
  *##############################################
  * @FILE_NAME :html.php
  *##############################################
  *
  * @author : liubo
  * @MailAddr : funkfan@sina.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Tue Jun 23 01:23:51 GMT 2009
  * @DATE : Tue Jun 23 01:23:51 GMT 2009
  *
  *==============================================
  * @Desc :生成html模块
  *==============================================
  */
class Html extends QModels_Article_Table
{
    
    public function __construct() {
        parent::__construct();
    }

    public function init() {
        parent::init();
        $this->db_www =  $this->db_v2_read;
        $this->_db = $this->db_v2_read;
    }
	public function get_category( $catid ){
		try{
		$sql = "SELECT * FROM `Category` WHERE catid=".$catid;
		$result = $this->_db->fetchAll($sql);
		return $result;
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}
	
	public function get_root_category(){
		try{
		$sql = "SELECT * FROM `Category` WHERE parentid=0";
		$result = $this->_db->fetchAll($sql);
		return $result;
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}		
	}

	public function get_channel(){
		$sql = "SELECT channelid,channelname FROM `Channel`";
		$result = $this->_db->fetchAll($sql);
		return $result;		
	}
	
	public function get_article(){
		$sql = "SELECT articleid, catid, title FROM `Article`";
		$result = $this->_db->fetchAll($sql);
		return $result;		
	}	

	public function getArticleByCategoryID($categoryID, $startNum, $endNum){
		$sql = "SELECT ". 
				"* ".
				" FROM `article` ".
				" WHERE `catid` = ". $categoryID .
				" AND status = 20  order by `articleid` desc ".
				" limit " . $startNum . ", " . $endNum . " ";
		//$refArticleArr = $this->db_www_obj->fetchAll($sql);
		return $this->db_www->fetchAll($sql);
		//return $refArticleArr;
	}
	
	
	public function get_template($catid, $type){
		try{
		$sql = "SELECT * FROM `template` where catid like '%,".$catid.",%' and type=".$type;
		echo $sql."\n";
		$result = $this->_db->fetchRow($sql);
		return $result;			
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}			
	}
	
}
?>