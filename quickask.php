<?php
session_start();
$_SESSION['token']['token'] = md5(time().'ohgod');
?>

<link href="/ask/css/lw-askProcess.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
<?php 
 //科室的数组
foreach($this->CATEGORY as &$v){
    unset($v['description']);
}
foreach($this->KESHIGROUP as &$v){
    foreach($v as &$v1){
        unset($v1['description']);
    }
}
if($this->FUSHUKESHI){
    $fushuarr=$this->FUSHUKESHI[1];
}
?>
var keshiarrkey=eval('(<?=json_encode($this->CATEGORY)?>)');
var keshiarr=eval('(<?=json_encode($this->KESHIGROUP)?>)');
var fushuarr=eval('(<?=json_encode($fushuarr)?>)');
var keshixz=new Array();
var keshiid=0;
$(function(){
    var strcjks="";
    var stryjks="";
    
    $.each(fushuarr,function(id,item){
        strcjks+="<li><a href='javascript:;' id='99_"+item.kid+"' onclick='ksxz("+item.kid+",99);'>"+item.name+"</a></li>"
    }); 
    $.each(keshiarr[0],function(id,item){
        stryjks+="<li><a href='javascript:;' id='1_"+item.id+"' onclick='ksxz("+item.id+",1);'>"+item.name+"</a></li>"
    }); 
    if($("#catid").val()!="")
    if(keshiarrkey[$("#catid").val()]!=null){
        $("#z_span").html(keshiarrkey[$("#catid").val()].name+"（点击可修改）");
    }else{
        $("#z_span").html("请选择科室列表");
        $("#catid").val("");
    }
    $("#cjksul").html(strcjks);
    $("#yjksul").html(stryjks);
    $("#z_span").click(function(){
        keshixz=new Array();
        $("#cjksul").html(strcjks);
        $("#yjksul").html(stryjks);
        shuaxin($("#catid").val(),99)
        $(".keshixuanze").show();
    });
    $(".guanbi").click(function(){
        $(".keshixuanze").hide();
    });
    $("#queding").click(function(){
        $(".keshixuanze").hide();
        if(keshiarrkey[keshiid]!=null){
            $("#z_span").html(keshiarrkey[keshiid].name+"（点击可修改）");
            $("#catid").val(keshiid);
        }
    });
});
function ksxz(id,jb){
    keshiid=id;
    if(jb==1){
        $("#ksdiv"+3).hide();
        keshixz[3]=null;
        keshixz[2]=null;
    }
    if(jb==2){
        keshixz[3]=null;
    }
    if(jb==99){
        keshixz[3]=null;
        keshixz[2]=null;
    }
    if(jb<3){
        ksshengcheng(id,jb+1);
    }
    shuaxin(id,jb);
}
function shuaxin(id,jb){
    if(id==0)id="null";
    var keshi=keshiarrkey[id];
    if(keshi==null)return false;
    ksxuanzhong(keshi.class_level1,1);
    if(jb==99){
        ksshengcheng(keshi.class_level1,2);
    }
    ksxuanzhong(keshi.class_level2,2);
    if(jb==99){
        ksshengcheng(keshi.class_level2,3);
    }
    ksxuanzhong(keshi.class_level3,3);
    ksxuanzhong(id,99);
    ksxuanzhong(id,jb);
    var str="";
    if(keshiarrkey[keshi.class_level1]!=null){
        str+='<div class="keshi12" >'+keshiarrkey[keshi.class_level1].name;
    }
    if(keshiarrkey[keshi.class_level2]!=null){
        str+='>></div><div class="keshi12" >'+keshiarrkey[keshi.class_level2].name;
    }
    if(keshiarrkey[keshi.class_level3]!=null){
        str+='>></div><div class="keshi12" >'+keshiarrkey[keshi.class_level3].name;
    }
    str+='</div>';
    $("#tishi").html(str);
}

function ksshengcheng(id,jb){
    if(id==0)id="null";
    if(keshiarr[id]!=null){
        var str="";
        $.each(keshiarr[id],function(id,item){
            str+="<li><a href='javascript:;' id='"+jb+"_"+item.id+"' onclick='ksxz("+item.id+","+jb+");'>"+item.name+"</a></li>"
        }); 
        $("#ksul"+jb).html(str);
        $("#ksdiv"+jb).show();
    }else{
        jb=jb-1;
        if(jb==1){
            $("#ksdiv"+2).hide();
            $("#ksdiv"+3).hide();
            keshixz[2]=null;
            keshixz[3]=null;
        }
        if(jb==2){
            $("#ksdiv"+3).hide();
            keshixz[3]=null;
        }
    }
}
function ksxuanzhong(id,jb){
    try{
        if(keshixz[jb]!=null&&keshixz[jb]!=id){
            var name=$("#"+jb+"_"+keshixz[jb]).children('font').html();
            $("#"+jb+"_"+keshixz[jb]).html(name);
        }
        if($("#"+jb+"_"+id).children('font').html()==null){
            var name=$("#"+jb+"_"+id).html();
            $("#"+jb+"_"+id).html("<font color=\"#CC6600\">"+name+"</font>");
            keshixz[jb]=id;
        }
    }catch(e){
    }
}
</script>
<div class="mod zhmod-04 tMarginLg"> 
  <span class="top"> <span class="tl"></span> </span>
  <div class="inner">
    <div class="hd" >
      <h3 id="kuaisutiwen">快速提问</h3>
    </div>
    <div class="bd" >
      <form id="ask_formzong" name="" method="POST" action="/askt/do/do/save/">
        <input type="hidden" value="<?=$_COOKIE['member_uID']?>" />
		<p id="p_showlogin" <?php if($_COOKIE['member_uID']) { echo 'style="display:none"'; }?>>
          <label><br />
          <input type="radio" style="width:10px; border:none;" checked name="register" id="register" value="0"  onclick="changeLabel(this, 3, 4);" />
          </select>
          <b>我是会员</b> </label>
          <span>（会员登录提问，将获得5积分）</span>
          <label>
          <input type="radio" style="width:10px; border:none;" name="register" id="register"  value="1" onclick="changeLabel(this, 4, 3);" />
          <b>我要注册</b> </label>
          <br />
          <br />
          <span id="label_3"> 邮箱：
          <input type="text" name="username" id="username" style="width:200px;" />
          &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
          密码：
          <input type="password" name="pwd"  id="pwd" style="width:200px;" />
          </span> <br />
          <span id="label_4" style="display:none;"> 邮箱：
          <input type="text" name="mail" style="width:260px;" />
          </span> </p>
        <p> <span id="load_catid"></span><span id="load_catid_other"></span>
          <input type="hidden" name="info[classid]" id="catid" value="<?=$this->classid?>" />
        </p>
        <p>
          <label for="">提问标题：</label>
          <input type="text" name="info[title]" id="ask_title" />
        </p>
        <p>
          <label for="">选择科室：</label>
          <div style="z-index:1000; margin-left:72px;*margin-left:76px;margin-top:-18px;" id="z_wselect"><span style="z-index: 100; border:#3399FF solid 1px; background-image:url(/ask/images/xuanze.jpg); width:160px;" class="z_span" id="z_span">请选择科室列表</span></div>
        <div class="keshixuanze" style=" position: absolute;z-index:99999;display: none;height:auto !important;">
          <div class="keshi12" style=" background:url(/ask/images/ewq1.jpg) 0 top repeat-x;height:39px;overflow:hidden; width:750px;">
            <div style=" margin-left:10px; height:39px; line-height:30px; font-size:14px; font-weight:bolder; color:#1f376d; float:left;">您选择的科室为：</div>
            <div id="tishi">
            </div>
            <div class="guanbi" style="background:url(/ask/images/qweqw.jpg) 0 9px no-repeat;"> </div>
          </div>
          <div class="keshi1">
            <label>一级科室</label>
            <br/>
            <div class="keshi1">
              <ul id="yjksul">
              </ul>
            </div>
          </div>
          <div class="keshi1" id="ksdiv2" style="display: none;">
            <label>二级科室</label>
            <br/>
            <div class="keshi1">
              <ul id="ksul2">
              </ul>
            </div>
          </div>
          <div class="keshi1"  id="ksdiv3" style="display: none;">
            <label>三级科室</label>
            <br/>
            <div class="keshi1">
              <ul id="ksul3">
              </ul>
            </div>
          </div>
          <div class="keshi2" style="margin-top: 25px;" >
            <label>常见科室</label>
            <br/>
            <div class="keshi1">
              <ul id="cjksul">
              </ul>
            </div>
          </div>
          <div class="tijiao"> <a href="javascript:;" onclick="return false;" id="queding"><img src="/ask/images/queding.jpg" /></a> </div>
        </div>
          
        </p>
        <p>
          <label for="">提问内容：</label>
          <textarea name="info[content]" style="width:510px; height:90px;" id="ask_content"></textarea>
        </p>
        <input type="hidden" value="<?php echo $_SESSION['token']['token']; ?>" name="token"/>
		<p><label>验证码:</label><input type="text" onfocus="yzmt();" maxlength="4" style="width: 55px;" name="verifyt" id="verifyt"/>
        <img id="verifyyzmt" onclick="shuaxint()" style="width:70px;height:30px;cursor:pointer; display: none; "  align="absmiddle" /></p>
		<p align="center" id="anniu">
        <input type="button" class="submit-zh" value="提交" onclick="return checkData();"/>
        </p>
      </form>
      <hr color="#FFFFFF" />
      <div style="text-align:center">温馨提示：如果您的提问越详细，您得到的回答就会越准确！</div>
    </div>
  </div>
</div>
<script type="text/javascript">

function checkData() {
	var title = $("#ask_title").val();
	var content = $("#ask_content").val();
	var cid = $("#catid").val();
	if(cid==""||cid==0) {
		alert("请选择科室");
		return false;
	} else if(title=="") {
		alert("请输入提问标题！");
		$("#ask_title").focus();
		return false;
	} else if(content == "") {
		alert("请输入提问内容！");
		$("#ask_content").focus();
		return false;
	} else if(title.length<5 || title.length>50) {
		alert("标题字数要在 5-50 之间");
		$("#ask_title").focus();
		return false;
	} else if(content.length<10 || content.length>500) {
		alert("内容字数要在 10-500 之间");
		$("#ask_content").focus();
		return false;
	}
    var button=$("#anniu").html();
    
    $("#anniu").html("正在验证数据...");
    $.ajax({
	type: "POST",
	url:  "/askt/do/do/savecheckkuaisu",
	dataType:'json',
	data: "pwd="+$("#pwd").val()+"&username="+$("#username").val()+"&register="+$("input[name='register']:checked").val()+"&info[title]="+$("#ask_title").val()+"&info[content]="+$("#ask_content").val()+"&info[classid]="+$("#catid").val()+"&rank="+Date(),
	success: function(msg){
	   if(msg.error==1){
            document.getElementById("ask_formzong").submit();
	   }else{
          alert(msg.error);
	       $("#anniu").html(button);
	   }
	}});
    return false;
	if($("#verifyt").val()==""){
            alert('请填写验证码');	
			return false;
        }
		$('#textarea_hidden_editor').val(b.getValue());
		return true;
}
function shuaxint(){
    if(!yzmkq)return;
    var str="/askt/verify?"+(new Date()).getTime();
    $("#verifyyzmt").attr("src",str);
}
var yzmkq=false;
function yzmt(){
    if(yzmkq)return;
    yzmkq=true;
    var str="/askt/verify?"+(new Date()).getTime();
    $("#verifyyzmt").attr("src",str);
    $("#verifyyzmt").show();
}
//category_load(0);
</script>   