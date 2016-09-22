<?php

/**
 * 创建ask经验结构化数据
 */
class CreateExpStruct {

    public function createXml() {
        $exp_obj = new Experience();
        $top_art_list = $exp_obj->GetExpMaxId('id desc', 0, 1);
        $max_article_id = $top_art_list['id'];
        $structdata_path = 'structdatas';
        $maps_path = $structdata_path . DIRECTORY_SEPARATOR . 'expasksitemaps';
        $save_sitemap_dir = APP_ROOT . DIRECTORY_SEPARATOR . $maps_path;
        if (!file_exists($save_sitemap_dir)) {
            mkdir($save_sitemap_dir, 0777, true);
        }
        $data_max_articleid = 0;
        $max_file_index = 0;
        $data_max_articleid_file = $save_sitemap_dir . DIRECTORY_SEPARATOR . 'data_max_expid.php';
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
//            $this->createIndex();
            echo '无新数据,不需要更新sitemap';
        } else {
            $max_diff = 2000; //每个文件放的记录数
            $start_article_id = $data_max_articleid;
            $file_index = $max_file_index + 1;
            $return_info = array();
            if(($max_article_id-$start_article_id)<$max_diff){
                echo '数据太少,请稍后再试';
                return;
            }
            $max_article_id = $start_article_id + $max_diff;
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
                    if ($end_article_id < ($start_article_id + $max_diff)) {
                        $end_article_id = $start_article_id + $max_diff;
                    }
                }
                $node = array();
                foreach ($art_list as $k => $v) {
                    $node_1 = array();
                    $node_2 = array();
                    $node_3 = array();

                    $list = $exp_obj->parseContent($v['content'], $v['source']);
                    $node['url'] = sprintf("http://ask.9939.com/exp/%d.html", $v['addtime'] . $v['id']);
                    $node['title'] = "\xEF\xBB\xBF" . $v['title'];
                    $node['articleTitle'] = $v['title'];  //主题字段
                    $node['publishDate'] = date('Y-m-d H:i:s', $v['addtime']);
                    $node['viewCount'] = mt_rand(3303, 19675); //网页浏览次数
                    //根据分类id，查询当前分类
                    $category = $exp_obj->getCategoryByCatid($v['catid']);
                    $node['category'] = $category['name'];
                    $detail2 = $this->screening($list['desc']);
                    $node['desc'] = $detail2;
                    foreach ($list['content'] as $d => $val) {
                        $detail = $this->screening($val);
                        if (!empty($detail)) {
                            $node_3['detail'] = $detail;
                            $node_2[$d]['substep'] = $node_3;
                        }
                    }
                    $detail1 = $this->screening($list['desc']);
                    $node_1['induction'] = $detail1;
                    $node_1['substeps'] = $node_2;
                    $node_1_1['step'] = $node_1;
                    $node['steps'] = $node_1_1;
                    $data_2['item'] = $node;
                    $data_1[] = $data_2;
                }
                $data_list['webName'] = '久久问医';
                $data_list['hostName'] = 'ask.9939.com';
                $data_list['datalist'] = $data_1;
                $data[] = $data_list;
                if (count($data_list['datalist']) > 0) {
                    $filename = sprintf('expsitemap%d.xml', $file_index);
                    $root_name = 'document';
                    $return_save_info = QLib_Xml_Client::createxmlfile($data, $filename, $save_sitemap_dir, $root_name, array(), true);
//                print_r($return_save_info);exit;
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
                $sitemap_index_filename = sprintf('expask%s.xml', 'indexfile');
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

    private function createIndex() {
        $exp_obj = new Experience();
        $top_art_list = $exp_obj->GetExpMaxId('id desc', 0, 1);
        $max_article_id = $top_art_list['id'];
        $structdata_path = 'structdatas';
        $maps_path = $structdata_path . DIRECTORY_SEPARATOR . 'expasksitemaps';
        $save_sitemap_dir = APP_ROOT . DIRECTORY_SEPARATOR . $maps_path;
        if (!file_exists($save_sitemap_dir)) {
            mkdir($save_sitemap_dir, 0777, true);
        }
        $data_max_articleid = 0;
        $max_file_index = 0;
        $data_max_articleid_file = $save_sitemap_dir . DIRECTORY_SEPARATOR . 'data_max_expid.php';
        if (is_file($data_max_articleid_file)) {
            $content = file_get_contents($data_max_articleid_file);
            $data = unserialize($content);
            $data_max_articleid = $data['max_articleid']; //同缓存文件中读取上次的最大文章ID
            $max_file_index = $data['max_file_index'];
        }

        $max_diff = 2000; //每个文件放的记录数
        $start_article_id = $data_max_articleid;
        $file_index = $max_file_index + 1;
        $return_info = array();
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
            $sitemap_index_filename = sprintf('expask%s.xml', 'indexfile');
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

    private function getCreateData($start_article_id = 0, $max_article_id = 0, $max_diff = 10000, &$art_list = array()) {
        $total_record = count($art_list);
        if ($total_record <= $max_diff && $start_article_id <= $max_article_id) {
            Zend_Loader::loadClass('Experience', MODELS_PATH);
            $exp_obj = new Experience();
            $end_article_id = $start_article_id + $max_diff;
            $where = "e.id>'{$start_article_id}' and e.id<='{$end_article_id}' ";
            $offset = 0;
            $answers = '';
            $count = $max_diff - $total_record;
            if ($count > 0) {
                $tmp_art_list = $exp_obj->getExpList($where, 'id asc', $count, $offset);
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

    private function screening($content) {
        $content = strip_tags($content); //过滤所有的html页面
        $content = str_replace("&nbsp;", "", $content); //过滤空格
        $content = str_replace(PHP_EOL, '', $content); //过滤换行
        $search = array(" ", "　", "", "", "", "匚", "", "\n", "\r", "\t"); //过滤空格
        $replace = array("", "", "", "", "");
        return str_replace($search, $replace, $content);
    }

}
