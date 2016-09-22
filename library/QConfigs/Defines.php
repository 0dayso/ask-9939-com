<?php
class QConfigs_Defines {
	public static function setVaribles($env = 'rls') {
        defined("Q_WWW_PATH") or  define("Q_WWW_PATH", APP_ROOT);
        defined("APP_MB_ROOT") or define("APP_MB_ROOT",APP_ROOT.'/tpl');//模板路经
        defined("APP_DATA_PATH") or define("APP_DATA_PATH",APP_ROOT."/data");//缓存文件路径
        defined("APP_CACHE_PATH") or define("APP_CACHE_PATH",APP_ROOT."/cache");//缓存文件路径
        defined("APP_PIC_ROOT") or define("APP_PIC_ROOT",APP_ROOT.'/upload/pic');//上传文件路经
        defined("HOME_9939_URL") or define("HOME_9939_URL",'http://home.9939.com/');
        defined("ASK_URL") or define("ASK_URL", 'http://ask.9939.com/');
        defined("APP_CACHE_PREFIX") or define("APP_CACHE_PREFIX","ASK-9939-COM_");//缓存前缀

        // xzxin 2010-01-30 
        defined("WEB_URL") or define("WEB_URL", 'http://www.9939.com/');
        defined("DOCTOR_URL") or define("DOCTOR_URL", 'http://doctor.9939.com/');
        defined("SO_URL") or define("SO_URL", 'http://so.9939.com/');
        defined("JB_URL") or define("JB_URL", 'http://jb.9939.com/');
        defined("HOME_USER_DEFAULT_PIC") or define('HOME_USER_DEFAULT_PIC',HOME_9939_URL.'/images/default.jpg');

        //问答生成公共页面路径
        define("APP_PUBLIC_PATH",APP_ROOT."/public/");
        //问答调取缓存动态文件路径
        define("APP_INCLUDE_PATH",APP_ROOT."/include/");
        define("APP_TIME_INTERVAL",60*60*12);		//cookie时间间隔  （12 hour）
        define("APP_DOMAIN",".9939.com");		//cookie应用的域名
        
        define('Q_CONFIG_PATH', APP_ROOT.'/config/');
        switch ($env) {
			case 'local' :
            {
                define('RELEASE_ENV', 'local');
                define("APP_CONFIG_FILE", APP_ROOT . "/config/app_local.ini");
				break;
            }
			case 'rls' :
            default:
            {
                define('RELEASE_ENV', 'rls');
                define("APP_CONFIG_FILE", APP_ROOT . "/config/app.ini");
				break; 
            }
		}
        
        //数据库配置
        $db_config = new Zend_Config_Ini(APP_CONFIG_FILE);
        $db_v2_write = $db_config->db_v2_write;
        $db_v2_read =$db_config->db_v2_read;
        $db_v2sns_write =$db_config->db_v2sns_write;
        $db_v2sns_read =$db_config->db_v2sns_read; 
        $db_dzjb_write =$db_config->db_dzjb_write;
        $db_dzjb_read =$db_config->db_dzjb_read;
        $db_lady_write =$db_config->db_lady_write;
        $db_lady_read =$db_config->db_lady_read;
        $db_tongji_write =$db_config->db_tongji_write; 
        $db_tongji_read =$db_config->db_tongji_read;
        $db_v2jb_write =$db_config->db_v2jb_write;
        $db_v2jb_read =$db_config->db_v2jb_read;
        $common_config=$db_config->common;
        
        Zend_Registry::set("db_v2jb_write", $db_v2jb_write->db->toArray());
        Zend_Registry::set("db_v2jb_read", $db_v2jb_read->db->toArray());

        Zend_Registry::set("db_v2_write", $db_v2_write->db->toArray());
        Zend_Registry::set("db_v2_read", $db_v2_read->db->toArray());
        Zend_Registry::set("db_v2sns_write", $db_v2sns_write->db->toArray());
        Zend_Registry::set("db_v2sns_read",$db_v2sns_read->db->toArray());
        Zend_Registry::set("db_dzjb_write",$db_dzjb_write->db->toArray());
        Zend_Registry::set("db_dzjb_read",$db_dzjb_read->db->toArray());
        Zend_Registry::set("db_lady_write",$db_lady_write->db->toArray());
        Zend_Registry::set("db_lady_read",$db_lady_read->db->toArray());
        Zend_Registry::set("db_tongji_write",$db_tongji_write->db->toArray());
        Zend_Registry::set("db_tongji_read",$db_tongji_read->db->toArray());
        Zend_Registry::set("common_config",$common_config->common->toArray());
        
        $GLOBALS['db_jb_obj'] = Zend_Db::factory('PDO_MYSQL',Zend_Registry::get("db_dzjb_read"));
        $GLOBALS['db_www_obj'] = Zend_Db::factory('PDO_MYSQL',Zend_Registry::get("db_v2_read"));
        $db_default = Zend_Db::factory('PDO_MYSQL',Zend_Registry::get("db_v2sns_write"));
        Zend_Db_Table::setDefaultAdapter($db_default);
	}
}
