<?php
@extract($_REQUEST);
require 'config.php';
require 'func_huodong.php';
$db = DBconnect(1);
$gift = @include('gifts.php');
if($_COOKIE['member_uID']&&$_COOKIE['passed']){
	$row = getRecordSet("SELECT * FROM `hd_ask_chg` WHERE `uid`='".$_COOKIE['member_uID']."'");
	if($row[0][chgnum]>=3){
		echo '<script>alert("您的闯关次数已达到3次，谢谢您的参与！");location.href="index.php";</script>';
	}
}elseif(!$_COOKIE['member_uID']){
	echo '<script>alert("请先登录！");location.href="index.php";</script>';
}elseif(!$_COOKIE['passed']){
	echo '<script>alert("请先提问！");location.href="index.php";</script>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>抽奖</title>
<link href="style/r.css" type="text/css" rel="stylesheet" />
<link href="style/b.css" type="text/css" rel="stylesheet" />
<link href="style/g.css" type="text/css" rel="stylesheet" />
<link href="style/m.css" type="text/css" rel="stylesheet" />
<link href="style/activity.css" type="text/css" rel="stylesheet" />
<link href="style/activitySub.css" type="text/css" rel="stylesheet" />
<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
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
          <div class="lw-subMod">
          	<div class="t subBg"><h1>抽&nbsp;&nbsp;&nbsp;&nbsp;奖</h1><span>说明：以下奖品均以实物为准，最终解释说明权归9939健康网所有！</span></div>
          	<div class="c lw-askProcessL">
          		<div class="bd zhflash_bd bPaddingLg bMarginLg">
       			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="529" height="529">
                      <param name="movie" value="draw/draw.swf" />
                      <param name="quality" value="high" />
                      <embed src="draw/draw.swf" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="529" height="529"></embed>
       			  </object>
                
                  <table>
                    <tbody>
                      <tr>
                        <th width="50%">获奖姓名</th>
                        <th>奖品</th>
                      </tr>
                      <?php
                      $row = getRecordSet("SELECT * FROM `hd_ask_gift` ORDER BY `gid` DESC LIMIT 0,10");
                      foreach($row as $k=>$v){
                      	$uname = $v[nickname] ?  $v[nickname] : $v[username];
                      	echo ' <tr>
                        <td>'.$uname.'</td>
                        <td>'.$gift[$v[giftid]].'</td>
                      	</tr>';
                      }
                      ?>
                    </tbody>
                  </table>
          		</div>
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
<div style="display:none"><script src="  http://s23.cnzz.com/stat.php?id=1898354&web_id=1898354" language="JavaScript" ></script></div>