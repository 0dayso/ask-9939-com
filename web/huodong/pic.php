<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect();
require 'func_huodong.php';
$ideadid = $_REQUEST['ideaid'];
if(!$ideaid){
	header("/");
}
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
      
      <div class=" g-e6m0s4 tMarginLg l-fix">
        <div class="col-m">
          <div class="m-wrap">
            <div class="mod zhmod-01">
              <span class="top">
                <span class="tl">
                </span>
              </span>
              <div class="inner zhview l-fix">
              <?php  	
               $r = getDetail($ideaid);
               $r = $r[0];
               $type = $r[type]==1 ? '文字':'图片';
                echo '<div class="zhworks-view">';
                  if($r[type]==2){
                	echo '<img src="attachment/chuangyi/'.$r[pic].'" />';
                  }elseif($r[type]==1){
                  	echo getSubstr($r[desc],0,500);
                  }
                  
                echo '</div>';
                 if($r[type]==2){
                	 echo '<div><br/><p>图片描述：'.$r[desc].'</p></div>';
                  }
                echo '<div class="zhworks-info">
                ';
                
                  echo '<p>作品类型：'.$type.'</p>
                  <p>标题：'.$r[title].'</p>
                  <p>作者：'.$r[nickname].'</p>
                  <p>投票数：'.$r[tpnum].'</p>
                </div>
                <a href="toupiao.php?ideaid='.$r[ideaid].'&path=pic.php" class="zhsubmit">投我一票</a>
              </div>
              ';
                ?>
              <span class="bottom">
                <span class="bl">
                </span>
              </span>
            </div> 
          </div>
        </div>
        <?php @include('huodong_right.html')?>
        <?php @include('huodong_left_col-e.html')?>
      <div class="g-line tMarginLg">
        <div class="mod zhmod-01">
            <span class="top">
              <span class="tl">
              </span>
            </span>
            <div class="inner zhmedia">
              <div class="hd">
                <h3>合作媒体</h3>
              </div>
              <div class="bd">
                inner
              </div>
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
 <?php @include('footer.html');?>
  </div>
</div>
</body>
</html>
