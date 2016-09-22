<?php
@extract($_REQUEST);
require 'config.php';
require 'func_huodong.php';
$db = DBconnect(1);

if(!$_COOKIE['member_uID'])
echo '<script>alert("请您登录！");location.href="/";</script>';
$idLv = $_COOKIE['idLv'];
$gifts = @include('gifts.php');
$gift = array(
			1=>'恭喜您获得一等奖！',
			2=>'恭喜您获得二等奖！',
			3=>'恭喜您获得三等奖！',
			4=>'恭喜您获得幸运奖！',
			5=>'恭喜您获得参与奖！',
			6=>'恭喜您获得参与奖！',
			7=>'恭喜您获得参与奖！'
		);
session_start();
$_SESSION["aa"]="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>抽奖成功</title>
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
            <?php
              echo '<p><img src="images/cjcg.jpg" /><span class="zhtemp_span">您获得了'.$gift[$idLv].' 奖品为'.$gifts[$idLv].'！</span></p>
              <p class="zhla-2p">请及时到您的<a href="http://home.9939.com/user/do/do/edit/">个人空间</a>填写你的详细信息，我们会根据你填写的而信息及时打电话与您核对，并将及时把奖品邮寄给您！如有以为请加qq群：83841869讨论!</p>';
              ?>
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
    <?php include 'footer.html';?>
    </div>
  </div>
</body>
</html>
