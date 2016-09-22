<?php

/**
 * 健康经验 模型层
 */
class Experience extends QModels_Ask_Table {

    function init() {
        parent::init();
    }

    /**
     * 获取 (常见疾病;生活保健;两性健康;整形美容) 分类里面的一条数据
     * @author caoxingdi
     * @date 2016-07-25
     * @return array 
     */
    public function getPlateBy($plateid = 0) {
        $sql = "SELECT ep.id,title,username,addtime,plateid,image,content from 9939_ep_experience ep ,9939_ep_experience_content eps where ep.id = eps.id and ep.plateid=$plateid order BY ep.id desc limit 0,1 ";
        return $this->_db->fetchRow($sql);
    }

    /**
     * 根据分类id，获取ask最新问答
     * @param type $classid 分类id
     * @return array
     */
    public function getaskList($classid = '') {
        $array = array();
        if (!empty($classid)) {
            $sql = "select id,title,content,ctime from wd_ask where classid =$classid and  examine = 1  order by id DESC limit 0,2";
            $array = $this->_db->fetchAll($sql);
        }
        return $array;
    }

    /**
     * 
     * @param type $offset 开始条数
     * @param type $size 每页条数
     * @return array
     */
    public function getArticleList($offset = 0, $size = 10, $isGetContent = true) {
        $sql = "select ex.id,title,username,addtime,plateid,image,content from 9939_ep_experience ex ,9939_ep_experience_content exs where ex.id = exs.id  order by addtime desc limit $offset,$size";

        if (!$isGetContent) {
            $sql = "select id,title,username,addtime,plateid from 9939_ep_experience order by addtime desc limit $offset,$size";
        }
        $array = $this->_db->fetchAll($sql);
        return $array;
    }

    /**
     * 得到相关经验
     * @author gaoqing
     * @date 2016-07-25
     * @param int $disid 疾病id
     * @param int $count 所需个数
     * @return array 相关经验集
     */
    public function getRelExps($disid, $count = 4) {
        $relExps = array();

        if (!empty($disid)) {

            $sql = ' SELECT * FROM 9939_ep_experience WHERE diseaseid = ? LIMIT 0, ' . $count;
            $stat = $this->_db->prepare($sql);
            $stat->execute(array($disid));
            $temp = $stat->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($temp)) {
                $relExps = $temp;
            }
        }
        return $relExps;
    }

    /**
     * 获取疾病信息（根据主键ID）
     * @author gaoqing
     * @date 2016-07-25
     * @param int $disid 疾病id
     * @param int $catid 分类id
     * @return array 疾病信息
     */
    public function getDiseaseByDisid($disid, $catid = 0) {
        $disease = array();

        if (!empty($disid)) {
            $sql = ' SELECT * FROM 9939_ep_disease WHERE id = ? ';
            $stat = $this->_db->prepare($sql);
            $stat->execute(array($disid));
            $temp = $stat->fetch(PDO::FETCH_ASSOC);
            if (isset($temp) && !empty($temp)) {
                $disease = $temp;
            }
            if (!empty($catid)) {
                $category = $this->getCategoryByCatid($catid);
                if (!empty($category)) {
                    $plate = array('常见疾病', '生活保健', '两性健康', '整形美容');
                    $category['plate_name'] = $plate[$category['plateid']];
                    $disease['cat'] = $category;
                }
            }
        }
        return $disease;
    }

    /**
     * 获取分类信息（根据主键ID）
     * @author gaoqing
     * @date 2016-07-25
     * @param int $catid 分类id
     * @return array 分类信息
     */
    public function getCategoryByCatid($catid) {
        $category = array();

        if (!empty($catid)) {
            $sql = ' SELECT * FROM 9939_ep_category WHERE id = ? ';
            $stat = $this->_db->prepare($sql);
            $stat->execute(array($catid));
            $temp = $stat->fetch(PDO::FETCH_ASSOC);
            if (!empty($temp)) {
                $category = $temp;
            }
        }
        return $category;
    }

    /**
     * 得到当前经验的相关问答
     * @author gaoqing
     * @date 2016-07-26
     * @param int $classid 科室id
     * @param int $size 查询数量
     * @return array 当前经验的相关问答集
     */
    public function getRelAsks($classid, $size) {
        $relAsks = array();

        $ask = new Ask();
        $asks = $ask->getList(' classid = ' . $classid, 'id DESC', $size);
        if (!empty($asks)) {
            $askIds = array();
            foreach ($asks as $ask) {
                $askIds[] = $ask['id'];
            }
            $answers = array();
            $answer = new Answer();
            $askid_str = implode(',', $askIds);
            $temp = $answer->getList(' askid in (' . $askid_str . ')');
            foreach ($temp as $ans) {
                if (!isset($answers[$ans['askid']])) {
                    $ans['content'] = QLib_Utils_String::cutString($ans['content'], 33);
                    $answers[$ans['askid']] = $ans;
                }
            }
            $relAsks['ask'] = $asks;
            $relAsks['answer'] = $answers;
        }
        return $relAsks;
    }

    /**
     * 获取plateid下的所有的category
     * @date 2016-07-25
     */
    public function getCategoriesByPlateid($plateid) {
        $category = array();
        if (is_numeric($plateid)) {
            $db = $this->_db;
            $sql = ' SELECT * FROM 9939_ep_category WHERE plateid = ? ';
            $stat = $db->prepare($sql);
            $stat->execute(array($plateid));
            $category = $stat->fetchAll(PDO::FETCH_ASSOC);
        }
        return $category;
    }

    /**
     * catid 获取 disease
     */
    public function getDiseaseByCatid($catid = '') {
        $disease = array();
        if (!empty($catid)) {
            $db = $this->_db;
            $sql = ' SELECT * FROM 9939_ep_disease WHERE catid = ? ';
            $stat = $db->prepare($sql);
            $stat->execute(array($catid));
            $disease = $stat->fetchAll(PDO::FETCH_ASSOC);
        }
        return $disease;
    }

    /**
     * 得到经验信息（通过主键）
     * @author gaoqing
     * @date 2016-07-25
     * @param int $expid 经验id
     * @param boolean $isgetContent 是否获取 content 内容
     * @param boolean $isParseContent 是否解析 content 内容（将 content 中的【概述】、【内容】、【注意事项】拆开到数组中）
     * @return array 经验信息
     */
    public function getExperience($expid, $isgetContent = false, $isParseContent = false) {
        $experience = array();
        if (!empty($expid)) {

            $db = $this->_db;
            $sql = ' SELECT * FROM 9939_ep_experience WHERE id = ? ';

            if ($isgetContent) {
                $sql = ' SELECT ep.*, ec.image, ec.content FROM 9939_ep_experience ep, 9939_ep_experience_content ec WHERE ep.id = ec.id AND ep.id = ? ';
            }
            $stat = $db->prepare($sql);
            $stat->execute(array($expid));
            $temp = $stat->fetch(PDO::FETCH_ASSOC);
            if (!empty($temp)) {
                $experience = $temp;

                //解析 content 内容
                if ($isParseContent) {
                    $experience['content'] = $this->parseContent($temp['content'], $temp['source']);
                }
            }
        }
        return $experience;
    }

    /**
     * 解析content 中的内容到数组中
     * @author gaoqing
     * @date 2016-07-25
     * @param string $content 待解析内容
     * @param int $source 来源（0: 39网；1: 120网）
     * @return array 解析后的 content 数据集
     */
    public function parseContent($content, $source) {
        //39
        if ($source == 0) {
            return $this->parse39Content($content);
        }
        //120
        if ($source == 1) {
            return $this->parse120Content($content);
        }
    }

    private function parse39Content($initContent) {
        $match = array();
        $pattern = '/\<dl\s*(class\s*\=\s*"steps")?\s*\>[\s\S]*?\<\/dl\>/';

        preg_match_all($pattern, $initContent, $match);

        $content = array('desc' => '', 'content' => array(), 'tip' => '');
        if (isset($match[0]) && !empty($match[0])) {
            $matchContent = $match[0];
            //概述：
            if (!empty($matchContent[0])) {
                $content['desc'] = $this->mfilter($matchContent[0], true, '/概述/', '');
            }
            //内容：
            if (isset($matchContent[1]) && !empty($matchContent[1])) {
                $content['content'] = $this->handleMainContent($matchContent[1]);
            }
            //注意事项：
            if (isset($matchContent[2]) && !empty($matchContent[2])) {
                $content['tip'] = $this->mfilter($matchContent[2], true, '/注意事项/', '');
            }
        }
        return $content;
    }

    /**
     * 获取经验列表  根据 plateid | catid | diseaseid
     * @param type $idtype
     * @param type $id
     * @param type $offset
     * @param type $size
     * @return type
     */
    public function getListByCon($idtype = 'plateid', $id = 0, $offset = 0, $size = 12) {
        $list = array();
        $sql = "SELECT id,title,username,userid,addtime,diseaseid,catid,plateid FROM 9939_ep_experience WHERE {$idtype} = {$id} order by addtime desc limit {$offset} , {$size}";
        $list = $this->_db->fetchAll($sql);
        return $list;
    }

    public function getContentList($ids = '') {
        $content = array();
        $sql = "select id,content from 9939_ep_experience_content where id in ({$ids})";
        $res = $this->_db->fetchAll($sql);
        return $res;
    }

    public function getCountByCon($idtype = 'plateid', $id = 0) {
        $sql = "select count(id) count from 9939_ep_experience where {$idtype} = {$id}";
        $res = $this->_db->fetchAll($sql);
        return $res['0']['count'];
    }

    /**
     * 疾病id获取疾病信息 in查询
     * @param type $diseaseids
     * @return type
     */
    public function getDislistByDisid($diseaseids = '') {
        $sql = "select id,name,catid from 9939_ep_disease where id in ({$diseaseids})";
        $res = $this->_db->fetchAll($sql);
        return $res;
    }

    private function parse120Content($initContent) {
        $content = array('desc' => '', 'content' => array(), 'tip' => '');
        //概述：
        $descMatch = $this->matchDatas('/\<p\s*class\=\s*\"w_articlep1\"\>[\s\S]*?\<\/p\>/', $initContent);
        if (isset($descMatch[0]) && !empty($descMatch[0])) {
            $content['desc'] = $this->mfilter($descMatch[0][0]);
        }
        //概述：
        $contentMatch = $this->matchDatas('/\<p\s*class\=\s*\"w_articlep2\"\>[\s\S]*?\<\/p\>/', $initContent);
        if (isset($contentMatch[0]) && !empty($contentMatch[0])) {
            $mcontent = array();
            foreach ($contentMatch[0] as $cval) {
                $mcontent[] = $this->mfilter($cval, true, '/\d{1,2}/', '');
            }
            $content['content'] = $mcontent;
        }
        //注意事项：
        $tipMatch = $this->matchDatas('/\<p\s*class\=\s*\".*w_articlep3\"\>[\s\S]*?\<\/p\>/', $initContent);
        if (isset($tipMatch[0]) && !empty($tipMatch[0])) {
            $content['tip'] = $this->mfilter($tipMatch[0][0]);
        }
        return $content;
    }

    private function handleMainContent($mainContent) {
        $mcontent = array();

        $mpattern = '/\<dd\>[\s\S]*?\<\/dd\>/';
        $mmatch = array();
        preg_match_all($mpattern, $mainContent, $mmatch);

        if (isset($mmatch[0]) && !empty($mmatch[0])) {
            foreach ($mmatch[0] as $cval) {
                $mcontent[] = $this->mfilter($cval, true, '/\d{1,2}/', '');
            }
        }
        return $mcontent;
    }

    private function mfilter($subject, $isfilter = false, $pattern = '//', $replacement = '') {
        $filtered = strip_tags($subject);
        if ($isfilter) {
            $filtered = preg_replace($pattern, $replacement, $filtered);
        }
        return trim($filtered);
    }

    private function matchDatas($pattern, $subject) {
        $match = array();
        preg_match_all($pattern, $subject, $match);
        return $match;
    }

    /**
     * 获取经验列表的最大id
     * @param type $order 排序
     * @return array
     */
    public function GetExpMaxId($order = 'id asc', $offset = 0, $size = 1) {
        $sql = 'SELECT * from 9939_ep_experience order by ' . $order;
        if ($size > 0 && $offset >= 0) {
            $sql.=" limit {$offset},{$size}";
        }
        return $this->_db->fetchRow($sql);
    }

    public function getExpList($where = array(), $orderBy = '', $count = 1000, $offset = 0) {
        $sql = "select e.id,e.title,e.username,e.addtime,e.diseaseid,e.source,e.catid,e.plateid,e.remark,ex.content from 9939_ep_experience e ,9939_ep_experience_content ex where e.id = ex.id and $where limit $offset,$count;";
        return $this->_db->fetchAll($sql);
    }

    public function getDiseaseExpList($classlevel3 = '0', $offset = 0, $count = 10) {
        $sql = "select e.id,e.title,e.diseaseid,e.addtime from 9939_ep_disease d ,9939_ep_experience e where d.id = e.diseaseid and d.diseaseid = $classlevel3 order by e.id desc limit $offset,$count";
        return $this->_db->fetchAll($sql);
    }

}
