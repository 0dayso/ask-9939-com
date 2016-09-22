<?php
/**
  *##############################################
  * @FILE_NAME :Toupiao.php
  *##############################################
  *
  * @author :   张泽华
  * @MailAddr : zhang-zehua@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   投票主题
  *==============================================
  */
class Vote extends QModels_Ask_Table {

    protected $_primary = 'id';
	protected $_name="Toupiao";

    /**
	* 查看投票列表
	* @param 条件
	* @return 投票信息 array
	*/
	public function List_Toupiao($where, $order, $count, $offset) {
            //echo $order;exit;
		$result = $this->fetchAll($where, $order, $count, $offset);
		//print_r($result);
		return $result->toArray();
	}

    	/**
	* 统计记录数
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetCount($where="1") {
		$where = ($where == "") ? "1" : $where;
//		echo "SELECT count(*) as num FROM `$this->_name` where ".$where;exit;
		$result = $this->_db->fetchAll("SELECT count(*) as num FROM `$this->_name` where ".$where);
		return $result[0]['num'];
	}

    /**
	* 查看投票选项列表
	* @param 条件
	* @return 投票选项信息 array
	*/
	public function List_themes($where="1") {
        $where = ($where == "") ? "1" : $where;
        $result = $this->_db->fetchAll("SELECT  *  FROM `Toupiao_themes` where ".$where);
		//$result = $this->fetchAll("SELECT count(*) as num FROM `Toupiao_themes` where ".$where, $order, $count, $offset);
		//print_r($result);exit;
		return $result;
	}

    /*
    **提交投票信息
    **数据库点击加一
    */
    public function Hits($postarr,$nid) {
       $db = $this->getAdapter();
		$where  = $db->quoteInto(' tid = ?',$nid);
        try{
        $sql = "update Toupiao_themes set   ".$postarr." where tid=$nid";
        $update = $this->_db->query($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
		if($update){
			return true;
		}
    }


    /**
	* 查询用户记录
	*
	* @param 条件
	* @return 记录数 int
	*/
	public function GetUser($username,$pwd) {
        $sql="select count(*) as num  FROM  `member` where `username`='".$username."' and `password`='".$pwd."'";
        $result = $this->_db->fetchAll($sql);
		return $result[0]['num'];
	}

        public function getVote()
        {
            
            $sql = "
                SELECT Toupiao.id,
                    Toupiao.listOrder,
                    Toupiao.title,
                    sum(Toupiao_themes.hits) AS total_hits
                FROM Toupiao
                LEFT JOIN Toupiao_themes ON(
                    Toupiao_themes.pid=Toupiao.id
                )
                GROUP BY id
                ORDER BY Toupiao.listOrder ASC
            ";
            
             return $this->_db->fetchAll($sql);
        }

        public function getVoteOptions()
        {
            $result = $this->_db->fetchAll("SELECT * FROM Toupiao_themes");
            $resultTree = array();
            for ($i=0, $count=count($result); $i<$count; $i++) {
                $resultTree[$result[$i]["pid"]] [$result[$i]["tid"]] = $result[$i];
            }
            return $resultTree;
        }

}



  ?>