<?php
@extract($_REQUEST);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提问成功</title>
<link href="style/r.css" type="text/css" rel="stylesheet" />
<link href="style/b.css" type="text/css" rel="stylesheet" />
<link href="style/g.css" type="text/css" rel="stylesheet" />
<link href="style/m.css" type="text/css" rel="stylesheet" />
<link href="style/activity.css" type="text/css" rel="stylesheet" />

<script src="js/jquery.js"></script>

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
          <div class="zhlinks">
            <ul>
              <li><a href="http://ask.9939.com/web/huodong/index.php">活动首页</a></li>
              <li class="on"><a href="###">奖品展示</a></li>
              <li><a href="###">活动细则</a></li>
              <li><a href="###">健康商城</a></li>
            </ul>
          </div>
          <div class="zhlala">
            <div class="zhla-inner">
              <p><img src="images/twcg2.jpg" /></p>
              <p><br />
您的问题已经提交成功，系统自动将您升级为本站会员<br />
系统为您分配的用户名为<span id="u_name"></span> 密码为<span id="u_pwd"></span><br />
会员名和密码不容易记忆？为了更好参与抽奖你可以：<a href="http://home.9939.com/user/do/do/edit/" target="_blank">修改用户名和密码</a>
</p>
            <p class="zhla-link"><a href="http://ask.9939.com/web/huodong/chuangguan.php"><img src="images/mscg.gif" /></a></p>

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
<script>
$('#u_name').html('<?=$n?>');
$('#u_pwd').html('<?=$p?>');
</script>
<div style="display:none"><script src="  http://s23.cnzz.com/stat.php?id=1898354&web_id=1898354" language="JavaScript" ></script></div>