<?php
@extract($_REQUEST);
require 'config.php';
include('../../Category_cache.php');
$db = DBconnect();
require 'func_ljf.php';



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$CATEGORY[$catid][catname]?></title>
<meta name="keywords" content="<?=$CATEGORY[$catid][setting][meta_keywords]?>" />
<meta name="description" content="<?=$CATEGORY[$catid][setting][meta_description]?>" />
<base href="http://lucky.9939.com"></base>
<link href="/9939/style/global.css" rel="stylesheet" type="text/css" />
<link href="/9939/style/magazine.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/9939/style/lw-zz.css"/>
<link rel="stylesheet" type="text/css" href="/9939/style/g.css">
<script type="text/javascript" src="/9939/js/jquery.js"></script>
<script type="text/javascript">
<!--
function setTab(m,n){
 var tli=document.getElementById("menu"+m).getElementsByTagName("div");
 var mli=document.getElementById("main"+m).getElementsByTagName("ul");
 for(i=0;i<tli.length;i++){
  tli[i].className=i==n?"hover":"";
  mli[i].style.display=i==n?"block":"none";
 }
}
//-->
</script>
</head>

<body>
<div id="doc" class="w950">
		<div id="hd"><?php include "header.html"?></div>
</div>

<div class="lw-zzHeader tMarginLg " style="height:74px">
	<div class="w950">
		<div class="hd">
			<p class="linkList">
				<a class="linkItem" href="http://ezone.9939.com/yyys/">营养饮食</a>
				<a class="linkItem" href="http://ezone.9939.com/ssmr/">塑身美容</a>
				<a class="linkItem" href="http://ezone.9939.com/ssqg/">情感生活</a>
				<a class="linkItem" href="http://ezone.9939.com/jsyy/">健身运动</a>
				<a class="linkItem" href="http://ezone.9939.com/mmbb/">妈咪宝宝</a>
				<a class="linkItem" href="http://ezone.9939.com/zyyy/">专业医疗</a>
			</p>
			<p class="hFblock"></p>
			<p class="hFblock2"></p>
		</div>
	</div>	
</div>

<div id="mainbody">
     <div class="magcontent">
    	<div class="subtop">您现在的位置是：<a href="http://www.9939.com">首页</a> &gt; <?php echo catpos($catid);?></div>
        <div class="tabmain clfix">
        <div class="main">
            <div class="main-tab">
              <div class="subflash_1">
                <?php echo cat_big_pic($catid);?>
                </div>
              <div class="subhot w403">
                    <div class="hd"><h2>热门文章</h2></div>
                    <ul class="bd clfix">      
                        <?php
                        $r = get_category_hot($catid,0,11);
                        foreach ($r as $k=>$v){
                        	echo '<li><a href="'.$v[url].'" target="_blank">'.$v[title].'</a></li>';
                        }
                        ?>
                    </ul>
              </div>
            </div>
            <div><?php echo get_hengfu(1921);?></div>
            <div class="subbanner clfix">
           	  <div class="w260">
               	<div class="hd noFloat"><a href="http://ezone.9939.com/z.php?catid=<?=$catid?>"><?=$CATEGORY[$catid][catname]?> 杂志介绍</a></div>
                  <div class="bd paddingLg">                      
                      <?php echo get_zazhi_intr($catid); ?>
                  </div>
                </div>
                <div class="w410">
                	<div class="hd"><a href="http://ezone.9939.com/z.php?catid=<?=$catid?>"><?=$CATEGORY[$catid][catname]?> 期刊列表</a></div>
                    <ul class="bd">                        
					<?php
					$r = get_qikan($catid); 
					foreach ($r as $k=>$v){
						echo '<li class="l_fix"><a class="lw-pic" href="'.$v[url].'"><img src="'.$v[thumb].'" width="92" height="120" title="'.$v['name'].'"/></a><span class="lw-content">';
						$rr = get_qk_content($v['posid'],0,5);
						foreach ($rr as $kk=>$vv){
							echo '<a href="'.$vv[url].'">'.$vv[title].'</a>';
						}
						echo '</span></li>';
					}                        
					?>
                    </ul>
                </div>
            </div>
            <div class="subbanner clfix">
            	<div class="w670">
            	<div class="hd"><a href="#">网友评价</a></div>
                <form id="form1" name="form1" method="post" action="" class="bd" onSubmit="return checkForm();" >
	                <input type="hidden" name="keyid" value="phpcms-ezone-posid-<?=$catid?>"/>
	                <?php $phpcms_comment_key = md5('phpcms-ezone-posid-'.$q.'fUtcexeybTvDeeBHRwiy');?>
					<input type="hidden" name="verify" value="<?=$phpcms_comment_key?>"/>
	                <h3>我来评价“<?=$CATEGORY[$catid][catname]?>”</h3>
	                <label><span class="txt">评　价：</span><textarea name="comment" id="comment" cols="45" rows="5" onfocus="reply_clearfield()" onblur="reply_restoration()"></textarea></label>
	                <label><span class="txt">验证码：</span><input type="text" name="checkcode" id="checkcode"  style="border:1px solid" size="5"/>
	                <img src="http://lucky.9939.com/checkcode.php" id="checkcode" onclick="this.src='http://lucky.9939.com/checkcode.php?id='+Math.random()*5;" style="cursor:pointer;" alt="验证码,看不清楚?请点击刷新验证码" align="absmiddle"/></label>
	                <label class="clfix"><input class="input" type="submit" name="button" id="button" value=" " /></label>
                </form>                
                </div>
            </div>
        </div>
        <div class="r260">
            <div class="brnp1">
            <div class="magtop">
                <div class="hd clfix">
                    <h2>人气排行</h2>
                    <div class="submenu0 mt5" id="menu0">
                      <div onmouseover="setTab(0,0)" class="hover">本周</div>
                      <div onmouseover="setTab(0,1)">本月</div>
                    </div>
                </div>
                <div class="top5" id="main0">
                      <ul class="block">
                          <li>
                                <ol class="top5g clfix">                             
                                <?php
                                $r = paihang($catid,'hits_week');
                                foreach ($r as $k=>$v){
                                	echo '<li><a href="'.$v[url].'"><img src="'.$v[thumb].'" width="65" height="85" /></a><strong><a href="'.$v[url].'" class="blue">['.$v[title].']</a></strong> <span>'.$v[description].'</span></li>';
                                }
                                ?>
                                </ol>
                          </li>
                      </ul>
                      <ul>
                          <li>
                            <div class="clfix">
                                <ol class="top5g clfix">
                                <?php
                                $r = paihang($catid,'hits_month');
                                foreach ($r as $k=>$v){
                                	echo '<li><a href="'.$v[url].'"><img src="'.$v[thumb].'" width="65" height="85" /></a><strong><a href="'.$v[url].'" class="blue">['.$v[title].']</a></strong> <span>'.$v[description].'</span></li>';
                                }
                                ?>
                                </ol>
                            </div>
                          </li>
                      </ul>
                </div>
                </div>
                <div class="magtop">
                <div class="hd clfix mt5"><h2>文章点击排行</h2></div>
                <div class="toppic clfix">                    
                    <?php
                        $r = paihang($catid,'hits',0,1);
                        foreach ($r as $k=>$v){
                        	echo '<div class="pic"><a href="'.$v[url].'"><img src="'.$v[thumb].'" width="69" height="89" /></a></div>
			                    <div class="text">
			                        <strong><a href="'.$v[url].'" class="orgT">['.$v[title].']</a></strong>
			                        <p>'.getSubstr($v[description],0,22).'</p>
			                    </div>';
                        }
                    ?>
                </div>
                <ul class="bd">                    
                    <?php
                    $r = paihang_wz($catid,1,12);
                    foreach ($r as $k=>$v){
                    	echo '<li>0'.($k+2).'<a href="'.$v[url].'">'.$v[title].'</a></li>';
                    }
                    ?>
                </ul>
                </div>
                <div class="vome">
                <div class="hd clfix mt5"><h2>在线投票</h2></div>
                <form id="form2" name="form2" method="get" action="/e-zine/vote.php" class="bd" onsubmit="return checkVote();" >
                  <p><?php $r = toupiao_subject(26); echo $r[title];?></p>                  
                  <?php 
                  $r = toupiao_item($r[id]);
                  foreach ($r as $k=>$v){
                  	echo '<div><label><input type="radio" name="tid" value="'.$v[tid].'" id="name1_'.$k.'" />'.$v[themes].'</label></div>';
                  }
                  ?>                  
                  <input type="submit" name="button2" id="button2" value=" " class="btn" style="cursor:pointer"/>
                </form>
                </div>
            </div>
        </div>
        </div>
    </div>

    <div class="subhd clfix"><h2>特别推荐</h2></div>
    <ul class="subbd clfix">
    		<?php
    		include("/home/web/ht-9939-com/data/data_adsplace_1922.php");
    		foreach ($_ADSGLOBAL[1922] as $k=>$v){
    			echo '<li><a href="'.$v[linkurl].'"><img src="'.$v[imageurl].'" title="'.$v[adsname].'" width="92" height="120" /></a><p><a href="'.$v[linkurl].'" >'.$v[adsname].'</a></p></li>';
    		}
    		?>
    </ul>

	<div class="w950 tMarginLg l-fix">
    	<?php include "link.html";?>
    </div>
    </div>
    <div class="w950">
		<div id="ft">
		<?php include "footer.html";?>
		</div>
	</div>
</body>
</html>

<script type="text/javascript" src="http://lucky.9939.com/data/js/comment_setting.js"></script>
<script type="text/javascript">
  <!--
    jQuery().ready(function() {
        if(setting.ischecklogin == 0 && getcookie('auth') === null)
        {
            jQuery('#comment').val("请您登陆后发表评论！");
            jQuery('#comment').attr("disabled","disabled");
            jQuery('#dosubmit').attr("disabled","disabled");
        }
        else 
        {
            jQuery('#comment').val("我也来说两句！");
			jQuery('#checkcode').val("");
        }
    });
    function reply_restoration()
    {
        if(jQuery('#comment').val() == '')
        {
            jQuery('#comment').val("我也来说两句！");
        }
    }
    function reply_clearfield()
    {
        if (jQuery('#comment').val() == "我也来说两句！") 
        {
            jQuery('#comment').val("");   
        }            
    }
    function checkForm()
    {
        if(jQuery('#comment').val() == '' || jQuery('#comment').val() == "我也来说两句！")
        {
            alert("内容不能为空");
            jQuery('#comment').focus();
            return false;
        }
        if(jQuery('#checkcode').val() == '' )
        {
            alert("验证码不能为空");
            jQuery('#checkcode').focus();
            return false;
        }
        if (jQuery('#comment').val().length > 1000) 
        {
            alert("内容太长，最多 1000 个文字");
            return false;
        }
        var c = jQuery('#comment').val();
		var cd = jQuery('#checkcode').val();
		jQuery.getJSON("http://lucky.9939.com/comment?action=wctest&keyid=phpcms-ezone-posid-<?=$catid?>&checkcode="+cd+"&comment="+c+"&callback=?",
		        function(data){
		            if(data=='true'){alert('评论成功,感谢您的参与');}
		            });
		return false;
    }
    
    function checkVote()
	{
		if(jQuery("input[type='radio']:checked").length<1){
			alert('请选择一个选项');
			return false;
		}
		return true;
	} 

  //-->
</script>
