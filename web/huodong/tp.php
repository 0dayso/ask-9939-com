<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect();
require 'func_huodong.php';
	
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
              <div class="inner zhvote-area">
                <div class="hd">
                  <h3>投票区</h3>
                </div>
                <div class="bd">
                  <div class="zhworks-wrap">
                 <?php $rs = getAllList();
                  $pages = $rs['pages'];
                  $con = $rs['rs'];
            
                  foreach($con as $k=>$v){
                  	if($v[type]==2){
	                  echo '<div class="zhworks-item">
	                    <div class="zhpic">
	                      <img src="attachment/chuangyi/'.$v[pic].'" />
	                    </div>
	                    <p>用户名：'.$v[nickname].'</p>
	                    <p>票数：'.$v[tpnum].'</p>
	                    <p>上传日期：'.date("Y-m-d",$v[time]).'</p>
	                    <p><a href="pic.php?ideaid='.$v[ideaid].'" class="zhsubmit zhsubmit01">查看</a><a href="toupiao.php?ideaid='.$v[ideaid].'&path=tp.php" class="zhsubmit zhsubmit02">投我一票</a></p>
	                  </div>';
                  	}else{
	                   echo '<div class="zhworks-item">
                    <div class="zhpic">
                      <a href="pic.php?ideaid='.$v[ideaid].'">'.getSubstr($v[desc],0,20).'...</a>
                    </div>
                    <p>用户名：'.$v[nickname].'</p>
	                    <p>票数：'.$v[tpnum].'</p>
	                    <p>上传日期：'.date("Y-m-d",$v[time]).'</p>
                    <p><a href="pic.php?ideaid='.$v[ideaid].'" class="zhsubmit zhsubmit01">查看</a><a href="toupiao.php?ideaid='.$v[ideaid].'&path=tp.php" class="zhsubmit zhsubmit02">投我一票</a></p>
                  </div>';}
					}
                  	
                  echo '</div>
                  <div class="zhpages">
                  '.$pages.'
                  </div>';
                  ?>
                </div>
              </div>
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
