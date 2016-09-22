<?php

/**
 * ==============================================
 * @Desc : 科室控制器
 * ==============================================
 */
class DepartmentsController extends Zend_Controller_Action {

    public function init() {

        Zend_Loader::loadClass('Keshi', MODELS_PATH);
        Zend_Loader::loadClass('Listask', MODELS_PATH);
        Zend_Loader::loadClass('Html', MODELS_PATH);
        Zend_Loader::loadClass('Create', MODELS_PATH);

        $this->keshi_obj = new Keshi();
        $this->list_obj = new Listask();
        $this->Html_obj = new Html();
        $this->CreateObj = new Create();
        $this->view = Zend_Registry::get("view");
    }

    public function indexAction() {
        //获取全部缓存
        $CATEGORY = $this->keshi_obj->getKeshifenliCache();
        //获取一二级科室
        $departments = $this->keshi_obj->get_keshi_redis();
        $this->view->departments = $departments;
        $this->view->category = $CATEGORY;

        //精彩回答  最新七条
        $this->view->answers = $this->answers();

        //右侧相关疾病文章 licheng 2015-11-25 start
        $cur_keshi_id = "32"; //未选中任何栏目指定默认栏目
        $jb_title_tmp = $this->keshi_obj->get_one($cur_keshi_id);
        $jb_title = QLib_Utils_String::cutString($jb_title_tmp['name'], 6, '...');
        $this->view->jb_title = $jb_title;



        //局部缓存
        $filename = 'list_jb_' . $cur_keshi_id;
        $jb_art_lists = QLib_Cache_Client::getCache('pages/part/list', $filename);
        if (!$jb_art_lists) {
            $dis_cache = $this->keshi_obj->getKeshifenliCache(array($cur_keshi_id), 1);
            shuffle($dis_cache);
            $rand_jb_name = '';
            foreach ($dis_cache as $key => $val) {
                if ($val['is_disease'] == 1) {
                    $rand_jb_name = $val['name'];
                    break;
                }
            }
            $jb_art_lists = Search::search_relarticle($rand_jb_name, 0, 9);
            QLib_Cache_Client::setCache('pages/part/list', $filename, $jb_art_lists, 24);
        }
        $this->view->rel_article = $jb_art_lists['list'];
        //右侧相关疾病文章 licheng 2015-11-25 end
        //右侧名医推荐 lc@2016-6-15
        $famousDoctors = SiteHelper::getRecommendHospital(371);
        $this->view->famousDoctors = $famousDoctors;

        //医院推荐
        $recommendHospital = SiteHelper::getRecommendHospital(32);
        $this->view->recommendHospital = $recommendHospital;

        echo $this->view->render("departments.phtml");
    }

    private function answers() {
        $answers = $this->list_obj->List_Ask("status='1'", "ctime desc", "7", "0");

        return $answers;
    }

}

?>