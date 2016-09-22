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
<script>
	function checkform(){
		var type = '';
		var a = document.getElementsByName('info[type]');
		for(var i=0;i<a.length;i++){
			if(a[i].checked){
				type = i;
			}
		}
		var title = jQuery('#title').val();
		var desc = jQuery('#desc').val();
		var file = jQuery('#file').val();
		if(type==0){
			if(title.length==0||desc.length==0||file.length==0){
				alert('请填写完整信息。');
				return false;
			}
		}else if(type==1){
			if(title.length==0||desc.length==0){
				alert('请填写完整信息。');
				return false;
			}
		}
		jQuery('#upform').submit();
	}
</script>
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
              <div class="inner zhatt-area">
                <div class="zhflow">
                <h4><img src="/9939/images_chuangyi/cslc.jpg" /></h4>
                <p> 
1.注册成为网站会员，留下真实联系方式。<br />
2.参与活动，在登陆状态上传自己的作品。
                </p> 
                </div>
                <div class="zhupload l-fix tmarginTemp">
                  <form id="upform" action="upload.php" method="POST" enctype="multipart/form-data">
                  	<?php
                  	if(!$_COOKIE['member_uID'])
                    	echo '<p id="friendly"><span href="###" style="color:#FF0000;">友情提示：请先登录,才能参与活动</span></p>';
					?>
                    <p><label>作品标题：</label><input type="text" id="title" name="info[title]"/></p>
                    <p><label>作品类型：</label>
                    <input type="radio" id="radio_pic" name="info[type]" class="zhinput-radio" value="2" checked  onclick="jQuery('#file_area').show();jQuery('#textname').html('图片介绍');"/>图片
                    <input type="radio" id="radio_word" name="info[type]" class="zhinput-radio" value="1" onclick="jQuery('#file_area').hide();jQuery('#textname').html('文字作品');"/>文字</p>
                    <div id="file_area">
                    <p><label>图片作品：</label><input type="file" id="file" name="idea"/></p>
                    </div>
                    <p class="zhwhite"><span id="textname">图片简介</span>  限500字以内</p>
                    <p>
                      <textarea id="desc" name="info[desc]"></textarea>
                    </p>
                    <p><a href="###" class="zhsubmit" onclick="return checkform();">立即上传</a></p>
                  </form>
                </div>
                <div class="zhrequest tmarginTemp">
                <h4>作品要求：</h4>
                <table>
                  <tr>
                    <td valign="top">1.图片作品：</td>
                    <td>格式 gif, jpg, jpeg, png, bmp <br />
              大小30k内<br />
   
              尺寸200*200</td>
                  </tr>
                </table>
                2.文字作品：500字以内
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
