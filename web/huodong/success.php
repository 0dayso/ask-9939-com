<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect();
require 'func_huodong.php';
$ideaid = $_REQUEST['ideaid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/9939/style/r.css" type="text/css" rel="stylesheet" />
<link href="/9939/style/b.css" type="text/css" rel="stylesheet" />
<link href="/9939/style/g.css" type="text/css" rel="stylesheet" />
<link href="/9939/style/m.css" type="text/css" rel="stylesheet" />
<link href="/9939/style_nav/nav_chuangyi.css" type="text/css" rel="stylesheet" />
<script src="/js/jquery.js"></script>
</head>

<body>
<div  id="doc" class="w950">
  <div id="hd">
   <?php @include('header.html');?>
  </div>
  <div id="bd">
    <?php @include('nav.html');?>
      
      <div class="g-line tMarginLg">
        <div class="mod zhmod-03">
          <span class="top">
            <span class="tl">
            </span>
          </span>
          <div class="inner zhsuccess">
            <img src="/9939/images_chuangyi/zhsuc.jpg" />
            <a href="pic.php?ideaid=<?=$ideaid?>" class="a1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
            <a href="tp.php" class="a2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
            <a href="/" class="a3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
            <a href="http://home.9939.com/user/do/do/edit" class="a4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
          </div>
          <span class="bottom">
            <span class="bl">
            </span>
          </span>
        </div>
      </div>
      
      
    </div>
  </div>
  <div id="ft">
  ft
  </div>
</div>
</body>
</html>
