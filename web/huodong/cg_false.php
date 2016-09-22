<?php
@extract($_REQUEST);
require 'config.php';
require 'func_huodong.php';
$db = DBconnect(1);
$e = $_REQUEST['e'];
$e = unserialize($e);
if(!$_COOKIE['member_uID'])
echo '<script>alert("请您登录！");location.href="/";</script>';
elseif(!$_COOKIE['passed'])
echo '<script>alert("请您提问！");location.href="/";</script>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>闯关失败</title>
<link href="style/r.css" type="text/css" rel="stylesheet" />
<link href="style/b.css" type="text/css" rel="stylesheet" />
<link href="style/g.css" type="text/css" rel="stylesheet" />
<link href="style/m.css" type="text/css" rel="stylesheet" />
<link href="style/activity.css" type="text/css" rel="stylesheet" />
</head>

<body>
  <div id="doc" class="w950">
    <div id="hd">
    <?php include 'header.html';?>
    </div>
    <div id="bd">
      <div class="mod emod-02">
        <span class="top">
          <span class="tl">
          </span>
          <span class="tr">
          </span>
        </span>
        <div class="inner">
          <?php @include('nav.html');?>
          <div class="zhlala">
            <div class="zhla-inner">
             
            <p class="zhtemp_p" style="color:#000000;font-weight:bold;">很遗憾，你选择的问题有<?=$e?>条错误，如果今天是第一次闯关，你还有两次机会，请继续提问闯关。</p>
			<p class="zhla-link">
			<a href="ask.php">
				<img src="images/zlyc.gif"/>
			</a>
			</p>
            </div>
          </div>
        </div>
        <span class="bottom">
          <span class="bl">
          </span>
          <span class="br">
          </span>
        </span>
      </div>
    </div>
    <div id="ft">
    ft
    </div>
  </div>
</body>
</html>
