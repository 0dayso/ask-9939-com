<?php
class Hotwords extends QModels_Article_Table
{
    //随机热词
    public function rand_words(){
        ini_set('memory_limit', '512M');
        $letter_list = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($letter_list);
        $return_list = array();
        $max_kw_length = 100;
        $max_dis_length =30; 
        $filter_array = $this->getFilterArray();
        $cache_rand_words = KeyWords::getCacheRandWords($max_kw_length, $filter_array);
        for ($i = 0; $i < $len; $i++) {
            $wd = strtoupper($letter_list{$i});
            $ret = $cache_rand_words[$wd];
            if(count($ret)>0){
                $rand_num = count($ret)>$max_dis_length?30:count($ret);
                $rand_keys = array_rand($ret,$rand_num);
                if(is_array($rand_keys)){
                    foreach($rand_keys as $k){
                        $return_list[$wd][] = $ret[$k];
                    }
                }else{
                     $return_list[$wd][] = $ret[0];
                }
            }else{
                $return_list[$wd]=array();
            }
        }
        return $return_list;
    }
    
    public function getFilterArray(){
        return array(
                array(
                    'filter'=>'filter',
                    'args'=>array('typeid',array(0,1))
                )
        );
        
    }
}