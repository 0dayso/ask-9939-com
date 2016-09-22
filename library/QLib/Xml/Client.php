<?php

/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Xml_Client
 * @version version (2009-10-26 下午04:13:33)
 * @package QLib.Xml.Client
 * @author   tds@qteam.cn
 * @since   1.0
 */
class QLib_Xml_Client {
    
    /**
     * 
     * @param array $data
     * @param string $filename
     * @param string $save_path
     * @param string $root_name
     * @return boolean
     */
    public static function createxmlfile($data,$filename='',$save_path='',$root_name='Root',$root_attr = array(),$is_add_cdata=false){
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement($root_name);
        if(count($root_attr)>0){
            foreach($root_attr as $k=>$v){
                $root->setAttribute($k, $v);
            }
        }
        $dom->appendChild($root);
        foreach($data as $k=>$v){
            self::createnode($dom, $root, $v,$is_add_cdata);
        }
        
        $save_file_name = empty($filename)? sprintf("%d.xml",time()):$filename;
        $base_save_path = $save_path;
        if(!file_exists($base_save_path)){
            mkdir($base_save_path,0777,true);
        }
        $save_path = $base_save_path.DIRECTORY_SEPARATOR.$save_file_name;
        $flag = $dom->save($save_path);
        if($flag){
            return array('save_path'=>$save_path,'file_name'=>$save_file_name);
        }else{
            return false;
        }
    }
    
    private static function createnode($dom,$item_article,$data,$is_add_cdata){
        foreach($data as $k=>$v){
            if(is_numeric($k)){
                self::createnode($dom,$item_article,$v,$is_add_cdata);
            }else{
                $child_node = $dom->createElement($k);
                $item_article->appendChild($child_node);
                if(is_array($v)){
                    self::createnode($dom,$child_node,$v,$is_add_cdata);
                }else{
                    if($is_add_cdata==true){
                        $child_node_text_value =$dom->createCDATASection($v);
                        $child_node->appendChild($child_node_text_value);
                    }else{
                        $child_node_text_value =$dom->createTextNode($v);
                        $child_node->appendChild($child_node_text_value);
                    }
                }
            }
        }
    }

}
