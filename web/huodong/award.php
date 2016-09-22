<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect(1);
require 'func_huodong.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>奖品设置</title>
<link href="style/r.css" type="text/css" rel="stylesheet" />
<link href="style/b.css" type="text/css" rel="stylesheet" />
<link href="style/g.css" type="text/css" rel="stylesheet" />
<link href="style/m.css" type="text/css" rel="stylesheet" />
<link href="style/activity.css" type="text/css" rel="stylesheet" />
<link href="style/activitySub.css" type="text/css" rel="stylesheet" />
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
          	<div class="t subBg"><h1>奖品设置</h1><span>说明：以下奖品均以实物为准，最终解释说明权归9939健康网所有！</span></div>
          	<div class="c lw-askProcessL">
          		<div class="bd bPaddingLg bMarginLg">
          			<div class="lw-picBlock l-fix">
          				<img src="images/subPage/temp_1.jpg" alt=""/>
          				<p class="text">特等奖1名 5999元笔记本电脑</p>
          			</div>
          			<div class="lw-picBlock l-fix">
          				<img src="images/subPage/temp_2.jpg" alt=""/>
          				<p class="text">一等奖2名  1180元 尊贵礼盒套装</p>
          			</div>
					<div class="lw-picBlock l-fix">
          				<img src="images/subPage/temp_3.jpg" alt=""/>
          				<p class="text">二等奖3名 190元 超力康益维软胶</p>
          			</div>
					<div class="lw-picBlock l-fix">
          				<img src="images/subPage/temp_4.jpg" alt=""/>
          				<p class="text">三等奖4名 78元金溢康钙D软胶囊</p>
          			</div>
					<div class="lw-picBlock l-fix">
          				<img src="images/subPage/temp_5.jpg" alt=""/>
          				<p class="text">参与奖15名 46元牙宝露口腔护理液</p>
          			</div>
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
    <?php include 'footer.html'; ?>
    </div>
  </div>
</body>
</html>
<div style="display:none"><script src="  http://s23.cnzz.com/stat.php?id=1898354&web_id=1898354" language="JavaScript" ></script></div>