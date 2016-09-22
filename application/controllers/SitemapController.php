<?php

class SitemapController extends Zend_Controller_Action {

    //ask问答xml
    public function indexAction() {
        $struct_obj = new CreateAskStruct();
        $struct_obj->createXml();
    }

}
