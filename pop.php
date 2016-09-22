<?php
$host_home = 'http://home.9939.com';
echo <<<EOF
<style src="$host_home/images_buluo/style/pop.css"></style>
<div class="pop" id="box" style="display: none;">
	<div class="pop_pTit">
    	<div class="pop_pLogo">久久健康网</div>
        <span class="pop_close" id="gB" onclick="popClose();"></span>
    </div>
    <p class="pop_mT25" id="pop_mT25">您的积分已经达到<b>25000</b>，完善资料升级到<b>10</b>级，享受更多<em>特权！</em></p>
    <div class="pop_mText" id="pop_mText"><span>您还需填写：</span><p>毕业院校，学习专业，服务单位，联系电话</p></div>
    <div class="pop_wsZl"><a href="$host_home/user/do/do/edit">完善资料</a></div>
</div>
<script type="text/javascript">
    function popClose(){
        var gb = document.getElementById("gB");
		var box = document.getElementById("box");	
		 box.style.display="none";
    }
    function popShow(msg){
         var gb = document.getElementById("gB");
		var box = document.getElementById("box");
        var pop_mT25 = document.getElementById("pop_mT25");
        var pop_mText = document.getElementById("pop_mText");
        var ms = msg.split('|');
       
		 box.style.display="";
         pop_mT25.innerHTML = ms[0];
         pop_mText.innerHTML = ms[1];
    }
</script>
EOF;
?>