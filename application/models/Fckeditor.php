<?php
/**
  *##############################################
  * @FILE_NAME :Fckeditor.php
  *##############################################
  *
  * @author : ljf
  * @MailAddr : licaption@163.com
  * @copyright : Copyright (c) 2009 中视在线(http://www.78.cn)
  * @PHP Version :  Ver 5.21
  * @Apache  Version : Ver 2.20
  * @MYSQL Version : Ver 5.0
  * @Version : Ver Thu Jun 18 18:00:29 CST 2009
  * @DATE : Thu Jun 29 14:31:29 CST 2009
  *
  *==============================================
  * @Desc :   编辑器模块
  *==============================================
  */
	class Fckeditor{
		public function fckAction($name='content',$content='')
		{
			@include(APP_ROOT. "/editor/Editor.php");   //加载编辑器
	    	$editor = new Editor();
	    	$editor->setArray(array('name'=>$name, 'value'=>$content));  
	    	return $editor->getEditor();
		}		
	}
	