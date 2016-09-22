<?php

/**
 * 创建ask结构化数据
 */
class CreateAskStruct {

    public function createXml() {
        $ask_obj = new Ask();
        $top_art_list = $ask_obj->GetAskList('examine in (1) AND answernum >=2', 'id desc', 1, 0);
        $max_article_id = $top_art_list[0]['id'];
        $structdata_path = 'structdatas';
        $maps_path = $structdata_path . DIRECTORY_SEPARATOR . 'asksitemaps';
        $save_sitemap_dir = APP_ROOT . DIRECTORY_SEPARATOR . $maps_path;
        if (!file_exists($save_sitemap_dir)) {
            mkdir($save_sitemap_dir, 0777, true);
        }
        $data_max_articleid = 0;
        $max_file_index = 0;
        $data_max_articleid_file = $save_sitemap_dir . DIRECTORY_SEPARATOR . 'data_max_askid.php';
        if (is_file($data_max_articleid_file)) {
            $content = file_get_contents($data_max_articleid_file);
            $data = unserialize($content);
            $data_max_articleid = $data['max_articleid']; //同缓存文件中读取上次的最大文章ID
            $max_file_index = $data['max_file_index'];
        }
        if ($data_max_articleid >= $max_article_id) {
            if($data_max_articleid>$max_article_id){
                $arr = array('max_articleid' => $max_article_id, 'max_file_index' => $max_file_index, 'addtime' => time());
                $content = serialize($arr);
                file_put_contents($data_max_articleid_file, $content);
            }
            echo '无新数据,不需要更新sitemap';
        } else {
            $max_diff = 1000; //每个文件放的记录数
            $start_article_id = $data_max_articleid;
            $file_index = $max_file_index + 1;
            $return_info = array();
            if(($max_article_id-$start_article_id)<1000){
                echo '数据太少,请稍后再试';
                return;
            }
            $max_article_id = $start_article_id + $max_diff;
            $keshi = new Keshi();
            $classid = $keshi->cache_keshi();
            while ($start_article_id < $max_article_id) {
                if ($start_article_id >= $max_article_id) {
                    break;
                }
                $end_article_id = $start_article_id + $max_diff;
                $art_list = array();
                $this->getCreateData($start_article_id, $max_article_id, $max_diff, $art_list);
                $data = array();
                $total_record = count($art_list);
                if ($total_record > 0) {
                    $end_article_id = $art_list[$total_record - 1]['id'];
                }
                $node = array();
                foreach ($art_list as $k => $v) {
                    $ans_index = 0;
                    $node_1 = array();
                    $node_2 = array();
                    $node_3 = array();
                    $all_valid_ans_list = array();
                    foreach ($v['answer'] as $d => $answer_list) {
                        if (stripos($answer_list['content'], 'http://') === false) {
                            $content = $this->pregReplace($answer_list['content']);
                            if (!empty($content) && mb_strlen($content) > 20) {
                                $node_3[$ans_index]['normalAnswer'] = $content;
                                $all_valid_ans_list[$ans_index]['content'] = $content;
                                $all_valid_ans_list[$ans_index]['id'] = $answer_list['id'];
                                $ans_index++;
                            }
                        }
                    }
                    if (mb_strlen($v['title'], 'utf8') > 4 && count($node_3) >= 2 && ((stripos($v['title'], '24小时专家在线') === false) && (stripos($v['title'], '提交成功') === false) || (stripos($v['title'], '保健咨询') === false) || (stripos($v['title'], '医生你好') === false))) {
                        //                foreach($v['answer'] as $kk =>$val){
                        //                    $content = $this->pregReplace($val['content']);
                        //                    $node_3[$kk]['normalAnswer'] = $content;//回答内容
                        //                }
                        $node_2['url'] = sprintf("http://ask.9939.com/id/%d", $v['id']);
                        $node_2['title'] = "\xEF\xBB\xBF" . $v['title'];
                        $contents = $this->pregReplace($v['content']);
                        if (empty($contents)) {
                            $contents = $node_2['title'];
                        }
                        $node_2['question'] = $contents;
                        if (!empty($v['help'])) {
                            $node_2['quDesc'] = $v['help'];
                        }
                        foreach ($all_valid_ans_list as $kk1 => $val1) {
                            if ($val1['id'] == $v['bestanswer']) {
                                $content3 = $this->pregReplace($val1['content']);
                                if (!empty($content3)) {
                                    $node_2['bestAnswer'] = $content3; //最佳答案
                                }
                            }
                        }

                        $node_2['bestAnswer'] = empty($node_2['bestAnswer']) ? $node_3[0]['normalAnswer'] : $node_2['bestAnswer'];
                        $node_2['isAcceptAns'] = $v['status'];
                        $node_2['normalAnswers'] = $node_3;
                        $node_2['publishDate'] = date('Y-m-d H:i:s', $v['ctime']); //问题发布时间
                        foreach ($v['answer'] as $kk2 => $val2) {
                            $node_2['lastComment'] = date('Y-m-d H:i:s', $val2['addtime']); //最新回答时间
                        }
                        $node_2['answers'] = count($node_3); //$v['answernum']; //回答条数
                        $node_2['category'] = $classid[$v['classid']]['name']; //问题所在分类
                        $node_2['channel1'] = $classid[$v['class_level1']]['name']; //一级目录
                        
                        $node_2['channel2'] = $v['class_level2'] > 0 ? $classid[$v['class_level2']]['name'] : ''; //二级目录

                        $node_2['channel3'] = $v['class_level3'] > 0 ? $classid['dis_'.$v['class_level3']]['name'] : ''; //三级目录
                        
                        if (!empty($v['tag'])) {
                            $node_2['tags'] = $v['tag']; //关键字
                        }
                        $node_1['item'] = $node_2;
                        $data_list[] = $node_1;
                    }
                }
                $node['webName'] = '久久问医';
                $node['hostName'] = 'ask.9939.com';
                $node['datalist'] = $data_list;
                $data[] = $node;
                if (count($node['datalist']) > 0) {
                    $filename = sprintf('sitemap%d.xml', $file_index);
                    $root_name = 'document';
                    $return_save_info = QLib_Xml_Client::createxmlfile($data, $filename, $save_sitemap_dir, $root_name, array(), TRUE);

                    if ($return_save_info) {
                        $return_info[] = sprintf('<a href="http://ask.9939.com/%s/%s" target="_blank">%s</a>', $maps_path, $filename, $filename);
                    }
                    $file_index++;
                }
                $start_article_id = $end_article_id;
            }
            $max_article_id = $start_article_id;
            if (count($return_info) >= 0) {
                $sitemap_files = scandir($save_sitemap_dir);
                $sitemap_index_data = array();
                foreach ($sitemap_files as $k => $v) {
                    if (!in_array($v, array(".", ".."))) {
                        $r_real_path = realpath($save_sitemap_dir . '/' . $v);
                        if (is_file($r_real_path) && stripos($v, '.xml')) {
                            $xml_url = sprintf('http://ask.9939.com/%s/%s', $maps_path, $v);
                            $node = array();
                            $node['loc'] = $xml_url;
                            $node['lastmod'] = date('Y-m-d H:i:s', fileatime($r_real_path));
                            $node_parent = array('sitemap' => $node);
                            $sitemap_index_data[] = $node_parent;
                        }
                    }
                }

                $save_sitemapindex_path = dirname($save_sitemap_dir);
                $sitemap_index_filename = sprintf('ask%s.xml', 'indexfile');
                $root_name = 'sitemapindex';
                $root_attr = array('xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9/');
                $return_save_info = QLib_Xml_Client::createxmlfile($sitemap_index_data, $sitemap_index_filename, $save_sitemapindex_path, $root_name, $root_attr);

                if ($return_save_info) {
                    $return_info[] = sprintf('<a href="http://ask.9939.com/%s/%s" target="_blank">%s</a>', $structdata_path, $sitemap_index_filename, $sitemap_index_filename);
                    $msg = implode("\n<br />", $return_info);
                    echo $msg;
                    echo '<br />';
                    echo '生成成功';
                    $arr = array('max_articleid' => $max_article_id, 'max_file_index' => $file_index - 1, 'addtime' => time());
                    $content = serialize($arr);
                    file_put_contents($data_max_articleid_file, $content);
                }
            }
        }
    }

    private function getCreateData($start_article_id = 0, $max_article_id = 0, $max_diff = 10000, &$art_list = array()) {
        $total_record = count($art_list);
        if ($total_record <= $max_diff && $start_article_id <= $max_article_id) {
            Zend_Loader::loadClass('Ask', MODELS_PATH);
            $ask_obj = new Ask();
            $end_article_id = $start_article_id + $max_diff;
            $where = "id>'{$start_article_id}' and id<'{$end_article_id}' and answernum >=2 ";
            $offset = 0;
            $answers = '';
            $count = $max_diff - $total_record;
            if ($count > 0) {
                $tbname = $this->getAskTableName($start_article_id);
                $ask_obj->setName($tbname);
                if ($tbname != 'wd_ask') {
                    $answers = $tbname . '_answer';
                } else {
                    $answers = 'wd_answer';
                }
                $tmp_art_list = $ask_obj->GetAskList_2($where, 'id asc', $count, $offset, $answers);
                if ($tmp_art_list) {
                    $art_list = array_merge($art_list, $tmp_art_list);
                    $total_record = count($art_list);
                    $start_article_id = $art_list[$total_record - 1]['id'];
                } else {
                    $start_article_id = $end_article_id;
                    $total_record = count($art_list);
                }
                $this->getCreateData($start_article_id, $max_article_id, $max_diff, $art_list);
            }
        }
    }

    private function getAskTableName($id) {
        $map_table = array(
                'wd_ask_history_1' => array(0, 502014, 'wd_ask_history_1_answer'),
                'wd_ask_history_2' => array(502015, 1007110, 'wd_ask_history_2_answer'),
                'wd_ask_history_3' => array(1007111, 1517111, 'wd_ask_history_3_answer'),
                'wd_ask_history_4' => array(1517112, 2042111, 'wd_ask_history_4_answer'),
                'wd_ask_history_5' => array(2042112, 3372111, 'wd_ask_history_5_answer'),
                'wd_ask_history_6' => array(3372112, 4702068, 'wd_ask_history_6_answer'),
                'wd_ask_history_7' => array(4702069, 5702233, 'wd_ask_history_7_answer'),
                'wd_ask' => array(5702234, 100000000, 'wd_answer')
        );
        $tbname = 'wd_ask';
        foreach ($map_table as $k => $v) {
            if ($id >= $v[0] && $id <= $v[1]) {
                $tbname = $k;
                break;
            }
        }
        return $tbname;
    }

    public function pregReplace($content = '') {
        $content = preg_replace("'(<(/)?(a|img)(\S*?)?[^>]*>)'i", "", $content);
        $content = preg_replace("'(\r\n)'i", "", $content);
        $content = preg_replace("'(style=\"(.)*?\")'i", "", $content);
        $content = preg_replace("'(align=\"(.)*?\")'i", "", $content);
        $content = preg_replace("'(&(.)*?;)'i", "", $content);

        $content = strip_tags($content, '<p>');
        $content = str_replace("&nbsp;", "", $content);
        $content = str_replace("久久健康网", "", $content);
        $content = str_replace("www.9939.com", "", $content);
        $content = str_replace("baojian.9939.com", "", $content);
        $content = str_replace("独家专稿，欢迎分享，转载请注明出处。", "", $content);
        $content = str_replace("投稿及合作请联系：", "", $content);
        $content = str_replace("010-63733891-0838", "", $content);
        $content = str_replace("&#13;", "", $content);

        $content = str_replace("（）", "", $content);
        $content = str_replace("()", "", $content);
        $content = preg_replace("'(<p>\s+?</p>)'i", "", $content);

        $content = str_replace("&alpha;", "", $content);
        $content = str_replace("&quot;", "", $content);
        $content = str_replace("&ldquo;", "", $content);
        $content = str_replace("&rdquo;", "", $content);
        $content = str_replace("&lsquo;", "", $content);
        $content = str_replace("&rsquo;", "", $content);
        $content = str_replace("&hellip;", "", $content);
        $content = str_replace("&middot;", "", $content);
        $content = str_replace("&mdash;", "", $content);
        $content = str_replace("&amp;", "", $content);
        $content = str_replace("&rarr;", "", $content);
        $content = str_replace("ESC;", "", $content);
        $content = str_replace("esc;", "", $content);
        $content = str_replace(" ", "", $content);
        return str_replace('>', '}}}', str_replace('<', '{{{', $content));
    }

    public function getlistAction() {
        ini_set('memory_limit', '256M');
        $maps_path = 'statistics';
        $save_sitemap_dir = APP_ROOT . DIRECTORY_SEPARATOR . $maps_path . '/11';
        $sitemap_files = scandir($save_sitemap_dir);
        $data = 0;
        foreach ($sitemap_files as $k => $v) {
            if (!in_array($v, array(".", ".."))) {
                $r_real_path = realpath($save_sitemap_dir . '/' . $v);
                if (is_file($r_real_path) && stripos($v, '.xml')) {
                    $xml_url = sprintf($save_sitemap_dir . '/%s', $v);
                    $xml = simplexml_load_file($xml_url);
                    $json_xml = json_encode($xml);
                    $dejson_xml = json_decode($json_xml, true);
                    $xmlcount = count($dejson_xml['webName']);
                    $data = $data + $xmlcount;
                }
            }
        }
        print_r($data);
        exit;
    }

}
