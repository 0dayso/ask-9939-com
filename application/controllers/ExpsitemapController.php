<?php

class ExpsitemapController extends Zend_Controller_Action {

//经验问答结构化数据
    public function indexAction() {
        $struct_obj = new CreateExpStruct();
        $struct_obj->createXml();
    }

}
