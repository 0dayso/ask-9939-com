<?php
/**
  *##############################################
  * @FILE_NAME :Cache.php
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
  * @Desc :   #缓存
  *==============================================
  */

class Cache extends QModels_Ask_Table
{
    
	public function createAllAsk() {
		ECHO ' #ht-sns-9939下';
		ECHO ' #移动至社区后台';
	}
	
	public function createAllUser() {
		ECHO '	#ht-sns-9939下';
		ECHO '#移动至社区后台';
	}
	
    
    public function createDisease(){
//        $sql = 'select * from 9939_dzjb where type in (1,2,5)';
        $sql = 'select * from 9939_dzjb where type in (1)';
        $aDis = $this->db_dzjb_read->fetchAll($sql);
//        require  APP_DATA_PATH.'/cache_disease.php';
//        $aDis = $_CACHE_DISEASE;
        $data = array();
        foreach ($aDis as $k => $v) {
            $disease_id = $v['contentid'];
            $title = $v['title'];
            $type=$v['type'];
            $url = "http://jb.9939.com/dis/{$disease_id}/";
            $res = array();
            $res['contentid'] = $disease_id;
            $res['title']=$title;
            $res['url']=$url;
            $res['type']=$type;
//            $res['pinyin']=$v['pinyin'];
//            $res['pinyin_initial']=$v['pinyin_initial'];
            $pinyin =  QLib_Utils_Spell::Pinyin($title);
            $res['pinyin']= $pinyin;
            $res['pinyin_initial']= isset($pinyin[0]) ? strtoupper($pinyin[0]) : '';
            $data[]=$res;
        }
        $_cache_data = var_export($data,true);
        $file_save_path = APP_DATA_PATH.'/cache_disease_20160309.php';
        if(file_put_contents($file_save_path, '<?php $_CACHE_DISEASE = '. $_cache_data .'; ?>')) {
            #echo 'ok';
        } else {
            #echo 'not';
        }
    }
	
	public function editUser($param=array()) {
		#print_r($param);
		if(!$param) return false;
		#echo 'hello';
		$file = '/home/web/htsns-9939-com/data/cache_user.php';
		if(file_exists($file)) {
			@require($file);
			if($_CACHE_USER) {
				$_CACHE_USER[$param['userid']] = $param;
				$data = $this->getData($_CACHE_USER);
				if(file_put_contents($file, '<?php $_CACHE_USER = '. $data .'; ?>')) {
					#echo 'ok';
				} else {
					#echo 'not';
				}
			} else {
					#echo 'not open file';
			}
		}
	}
	
	private function getData($tmp=array()) {
		if(!$tmp) return '';
		ob_start();
		var_export($tmp);
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}
	
	
}



?>