<?php
@extract($_REQUEST);
require 'config.php';
$db = DBconnect(1);
require 'func_ljf.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>健康知识闯关抽大奖</title>
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
          <?php include 'nav.html';?>
          <div class="zhstep l-fix">
            <img src="images/zhstep.jpg" />
            <div class="zhlink-temp"><a href="ask.php"></a></div>
          </div>
          <div class="zhtag">
            <div class="zhblock zhblock01">
              <div class="hd">
                <h3>获奖用户</h3>
              </div>
              <div class="bd">
                <table>
                  <tbody>
                    <tr>
                      <th width="60">用户名</th>
                      <th width="60">获奖等级</th>
                      <th width="150">所获奖品</th>
                    </tr>
                    <?php
                    $hj_user = get_hj_user();//取获奖会员
                    if($hj_user){
                    	/*
                    	//$narr = array('hellokitty','龙哥','fxjn');
                    	//$arr = array('龙哥 三等奖  78元金溢康钙D软胶囊','fxjn 参与奖  46元牙宝露口腔护理液');
	                   // foreach ($hj_user as $k=>$v){
	                    	if($k==mt_rand(1,10)){
	                    		echo '<tr><td>'.$arr[]
	                    	}else{
	                    		echo '<tr><td>'.$v['username'].'</td><td>'.$v['rank'].'</td><td>'.$v['name'].'</td></tr>';
	                    	}
	                    }
	                    */
                    	$arr = @include('luckylist.php');
                    	foreach($arr as $k=>$v){
                    		echo '<tr><td>'.$v[uname].'</td><td>'.$v[Lv].'</td><td>'.$v[gift].'</td></tr>';
                    	}
                    	/*
                    	echo '<tr><td>大毛</td><td>三等奖</td><td>78元金溢康钙D软胶囊</td></tr>';
                    	echo '<tr><td>咏夜颂歌</td><td>幸运奖</td><td>50积分</td></tr>';
                    	echo '<tr><td>hellen</td><td>幸运奖</td><td>50积分</td></tr>';
                    	echo '<tr><td>王涛</td><td>幸运奖</td><td>200积分</td></tr>';
                    	echo '<tr><td>夜阑珊</td><td>幸运奖</td><td>100积分</td></tr>';
                    	echo '<tr><td>dreamy</td><td>幸运奖</td><td>50积分</td></tr>';
                    	echo '<tr><td>龙哥</td><td>三等奖</td><td>78元金溢康钙D软胶囊</td></tr>';
                    	echo '<tr><td>fxjn</td><td>参与奖</td><td>46元牙宝露口腔护理液
</td></tr>';
                    	echo '<tr><td>冯波</td><td>三等奖</td><td>100积分</td></tr>';
                    	echo '<tr><td>冯波</td><td>三等奖</td><td>50积分</td></tr>';
                    	*/
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="zhblock zhblock02">
              <div class="hd">
                <h3>健康指导连连看</h3>
              </div>
              <?php include 'jkzdllk.html';?>
            </div>
            <div class="zhblock zhblock03">
              <div class="hd">
                <h3>冬季常见20大疾病</h3>
              </div>
              <div class="bd">
                <ul>
                <?php
                include('data_adsplace_58.php');
                if($_ADSGLOBAL['58']){
	                foreach ($_ADSGLOBAL['58'] as $k=>$v){
	             		echo '<li><a href="'.$v['linkurl'].'" target="_blank">'.$v['adsname'].'</a></li>';   	
	                }
                }
                ?>
                </ul>
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
                <ul>
                  <li><a href="http://www.wind360.cn/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m1.jpg" /></a></li>
                  <li><a href="http://www.funtry.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m2.jpg" /></a></li>
                  <li><a href="http://xywy.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m3.jpg" /></a></li>
                  <li><a href="http://www.ganbaobao.com.cn/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m4.jpg" /></a></li>
                  <li><a href="http://www.361health.com/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m5.jpg" /></a></li>
                  <li><a href="http://www.lady361.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m6.jpg" /></a></li>
                  <li><a href="http://www.7y7.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m7.jpg" /></a></li>
                  <li><a href="http://www.xtata.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m8.jpg" /></a></li>
                  <li><a href="http://www.onlylady.com/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m9.jpg" /></a></li>
                  <li><a href="http://www.5yi.com/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m10.jpg" /></a></li>
                  <li><a href="http://bbs.80end.cn/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m11.jpg" /></a></li>
                  <li><a href="www.jinti.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m12.jpg" /></a></li>
                  <li><a href="http://www.mz16.cn" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m13.gif" /></a></li>
                  <li><a href="http://www.quanke.net" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m14.jpg" /></a></li>
                  <li><a href="http://www.3751.cn" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/m15.gif" /></a></li>

				  <li><a href="http://www.jinti.com" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/logo-0111-1.jpg" /></a></li>
				  <li><a href="http://www.5elady.com/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/logo-0111-2.gif" /></a></li>
				  <li><a href="http://www.yaolee.net.cn/" target="_blank"><img src="http://www.9939.com/9939/images_chuangyi/logo-0111-3.gif" /></a></li>
                </ul>
              </div>
            </div>
            <span class="bottom">
              <span class="bl">
              </span>
            </span>
          </div>
      </div>
    </div>
    <div id="ft">
    <?php include 'footer.html'; ?>
    </div>
  </div>
</body>
</html>
<div style="display:none"><script src="  http://s23.cnzz.com/stat.php?id=1898354&web_id=1898354" language="JavaScript" ></script></div>