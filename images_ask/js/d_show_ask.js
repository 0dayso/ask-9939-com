// JavaScript Document
function copyToClipBoard(){
		var clipBoardContent="";
		clipBoardContent+=document.title;
		clipBoardContent+="\n";
		clipBoardContent+=this.location.href; //获取地址
		if(window.clipboardData) {   
			window.clipboardData.setData("Text",clipBoardContent);
		} else if (window.netscape) {   
			 try {   
		            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");   
		    } catch (e) {   
		            alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");   
		        } 
		       var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);   
		       if (!clip)   {
		       	   alert("浏览器不允许！");
		          return;   
		       }
    		       var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);   
    			if (!trans)   {
    				 alert("浏览器不允许！");
        			return;   
        		}
    			trans.addDataFlavor('text/unicode');   
    			var str = new Object();   
    			var len = new Object();   
    			var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);   
    			str.data = clipBoardContent;   
    			trans.setTransferData("text/unicode",str,clipBoardContent.length*2);   
    			var clipid = Components.interfaces.nsIClipboard;   
    			if (!clip)   {
    				 alert("浏览器不允许！");
        			return false;   
        		}
    			clip.setData(trans,null,clipid.kGlobalClipboard);   
		}
		alert("网址己复制,可以转发给你的朋友了！");
		return;
} 

//搜索用
function mksearch(id,i)
{
	var kw = $("#"+id).val();
	var type = 10; 
	if(i==1){
		if(kw=='输入提问内容，点击“我要提问”' || kw==''){
			alert('请输入您要搜索的内容!');
			$("#"+id).val('');
			$('#searchtext').focus();
			return false;
		}
		window.location.href="http://so.9939.com/index/index/kw/"+kw+"/type/"+type;
	}else if(i==2){
		window.location.href="http://ask.9939.com/asking/index/kw/"+kw;
	}
	return false;
}

(function($){
$.fn.extend({
        Scroll:function(opt,callback){
                //参数初始化
                if(!opt) var opt={};
                var _btnUp = $("#"+ opt.up);//Shawphy:向上按钮
                var _btnDown = $("#"+ opt.down);//Shawphy:向下按钮
                var timerID;
                var _this=this.eq(0).find("ul:first");
                var     lineH=_this.find("li:first").height(), //获取行高
                        line=opt.line?parseInt(opt.line,10):parseInt(this.height()/lineH,10), //每次滚动的行数，默认为一屏，即父容器高度
                        speed=opt.speed?parseInt(opt.speed,10):500; //卷动速度，数值越大，速度越慢（毫秒）
                        timer=opt.timer //?parseInt(opt.timer,10):3000; //滚动的时间间隔（毫秒）
                if(line==0) line=1;
                var upHeight=0-line*lineH;
                //滚动函数
                var scrollUp=function(){
                        _btnUp.unbind("click",scrollUp); //Shawphy:取消向上按钮的函数绑定
                        _this.animate({
                                marginTop:upHeight
                        },speed,function(){
                                for(i=1;i<=line;i++){
                                        _this.find("li:first").appendTo(_this);
                                }
                                _this.css({marginTop:0});
                                _btnUp.bind("click",scrollUp); //Shawphy:绑定向上按钮的点击事件
                        });

                }
                //Shawphy:向下翻页函数
                var scrollDown=function(){
                        _btnDown.unbind("click",scrollDown);
                        for(i=1;i<=line;i++){
                                _this.find("li:last").show().prependTo(_this);
                        }
                        _this.css({marginTop:upHeight});
                        _this.animate({
                                marginTop:0
                        },speed,function(){
                                _btnDown.bind("click",scrollDown);
                        });
                }
               //Shawphy:自动播放
                var autoPlay = function(){
                        if(timer)timerID = window.setInterval(scrollUp,timer);
                };
                var autoStop = function(){
                        if(timer)window.clearInterval(timerID);
                };
                 //鼠标事件绑定
                _this.hover(autoStop,autoPlay).mouseout();
                _btnUp.css("cursor","pointer").click( scrollUp ).hover(autoStop,autoPlay);//Shawphy:向上向下鼠标事件绑定
                _btnDown.css("cursor","pointer").click( scrollDown ).hover(autoStop,autoPlay);
        }      
})
})(jQuery);
$(document).ready(function(){
        $("#scrollDiv").Scroll({line:1,speed:300,timer:2000,up:"btn1",down:"btn2"});
});

function showZ(n){
	    document.getElementById("j-box0"+n).style.display="block";
	    document.getElementById("j-box0"+n).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.cssText="z-index:9999;";

	  }
	   function showBuchong(obj){
		    document.getElementById("j-box01").style.display="block";
	  }
	  function hideZ(n){
	    document.getElementById("j-box0"+n).style.display="none";
	    document.getElementById("j-box0"+n).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.cssText="z-index:1;";
	  }
function changeLabel(obj, __index, __index_1) {
		if(obj.checked) {
			$('#label_' + __index).css('display', '');
			$('#label_' + __index_1).css('display', 'none');
		}
	}
$("#input1").click(function(){alert("aa");$("#input1").value="";});
$(function(){
	$(document).jTab({
	 menutag:".j-mod .hd li"
	});
	$(document).jTab({
	 menutag:".zhmod-02 .hd li"
	});
	$(".user_info .pic").bind("mouseover",function(){
	  $(this).children().eq(0).show();
	});
	$(".user_info .pic").bind("mouseout",function(){
	  $(this).children().eq(0).hide();
	});
	$(".zhlocation_cc").textSlider({
		line:1,
		 speed:500,
		 timer:3000
		});
	});
	
function select_kc(num,km,kc)
{
	for(i=1; i<=7; i++)
	{
		if(num == i)
		{
			obj = document.getElementById(km + "_" + i);
			obj.className = "lion";
			obj = document.getElementById(kc + "_" + i);
			obj.className = "ct_on";
		}
		else
		{
			obj = document.getElementById(km + "_" + i);
			obj.className = "";
			obj = document.getElementById(kc + "_" + i);
			obj.className = "ct_off";
		}
	}
}

function isKeyTrigger(e,keyCode){   
	    var argv = isKeyTrigger.arguments;   
	    var argc = isKeyTrigger.arguments.length;   
	    var bCtrl = false;   
	    if(argc > 2){   
	        bCtrl = argv[2];   
	    }   
	    var bAlt = false;   
	    if(argc > 3){   
	        bAlt = argv[3];   
	    }   
	    var nav4 = window.Event ? true : false;   
	    if(typeof e == 'undefined') {   
	        e = event;   
	    }   
	    if(bCtrl &&   
	        !((typeof e.ctrlKey != 'undefined') ?    
	        e.ctrlKey :   
	        e.modifiers & Event.CONTROL_MASK > 0)) {   
	        return false;   
	    }   
	    if( bAlt &&   
	        !((typeof e.altKey != 'undefined') ?    
	            e.altKey : e.modifiers & Event.ALT_MASK > 0)){   
	        return false;   
	    }   
	    var whichCode = 0;   
	    if (nav4) whichCode = e.which;   
	    else if (e.type == "keypress" || e.type == "keydown") whichCode = e.keyCode;   
	    else whichCode = e.button;          
	    return (whichCode == keyCode);   
}    
function ctrlEnter(e,formname){   
    var ie = navigator.appName == "Microsoft Internet Explorer" ? true : false;   
    if(ie){   
        if(event.ctrlKey && event.keyCode == 13) {   
        	if(formname == 'answer') {
        		if(checkAnswerData()) {
        			document.getElementById("form_answer").submit();
        		}
        	} else if(formname == 'ask') {
        		if(checkData()) {
        			document.getElementById("form_ask").submit();
        		}
        	}
        }   
    }  else {   
            if(isKeyTrigger(e,13,true)) {
	            	if(formname == 'answer') {
	            		if(checkAnswerData()) {
        				document.getElementById("form_answer").submit();
        			}
	        	} else if(formname == 'ask') {
	        		if(checkData()) {
        				document.getElementById("form_ask").submit();
        			}
	        	}
            }
     }   
} 

function checkAnswerData() {
			var content = $("#answer_content").val();
		if(content=="") {
			alert('请输入回答内容!');
			$("#answer_content").focus();
			return false;
		}
		$("#answer_submit").attr("disabled","disabled");
		return true;
	}	
function quick_ask(){
	if($("#quick_title").val()==""){
		alert("请输入标题");
		$("#quick_title").focus();
		return false;
	} else if($("#quick_content").val()=="") {
		alert("请输入内容");
		$("#quick_content").focus();
		return false;
	} else if($("#quick_content").val()=="输入您的问题") {
		alert("请输入内容");
		$("#quick_content").focus();
		return false;
	} else if($("#quick_title").val().length<5 || $("#quick_title").val().length>50) {
		alert("标题字数要在：5-50  之间");
		$("#quick_title").focus();
		return false;
	} else if($("#quick_content").val().length<10 || $("#quick_content").val().length>500) {
		alert("内容字数要在：10-500 之间");
		$("#quick_content").focus();
		return false;
	}
}
function checkData(){
	if($("#ask_title").val()==""){
		alert("请输入标题");
		$("#ask_title").focus();
		return false;
	} else if($("#ask_content").val()=="") {
		alert("请输入内容");
		$("#ask_content").focus();
		return false;
	} /*else if($("#ans_content").val()=="输入您的问题") {
		alert("请输入内容");
		$("#ans_content").focus();
		return false;
	} */else if($("#ask_title").val().length<5 || $("#ask_title").val().length>50) {
		alert("标题字数要在：5-50  之间");
		$("#ask_title").focus();
		return false;
	} else if($("#ask_content").val().length<10 || $("#ask_content").val().length>500) {
		alert("内容字数要在：10-500 之间");
		$("#ask_content").focus();
		return false;
	}
}
function askadd(askid) {

//	var askid = 3395350;
	
	if($("#askadd_content").val()=="") {
		alert("请输入内容!");
		$("#askadd_content").focus();
		return false;
	}
	 $.ajax({ 
		   type: "POST", 
		   url: "/ask/askadd", 
		   data: "askid="+askid+"&content="+$("#askadd_content").val(), 
		   success: function(msg){ 
		     if(msg=='empty') {
			     alert('内容不能为空！');
			     return false;
			  } else if(msg=='notfound') {
				  alert('该问题不存在或已被删除！');
				   return false;
			  } else if(msg=='isclose') {
				  alert('问题已经结束！');
				   return false;
			  } else if(msg=='nopower') {
				  alert('你没有权限修改！');
				   return false;
			  } else if(msg=='notsafe') {
				  	alert('内容含有非法词语！');
				   return false;
			  } else if(msg=='ok') {
				  $("#askadd_content").val('');

				  $.ajax({ 
					   type: "POST", 
					   url: "/ask/getaskadd", 
					   cache: false,
					   data: "askid="+askid, 
					   success: function(msg){ 
					  	$("#askaddlist").html(msg);
					   	hideZ(1);
					   } 
					 });
				  /*$.get("/ask/getaskadd/askid/"+askid+"?t="+Math.random, function(data){ 
					   $("#askaddlist").html(data);
					   hideZ(1);
					 });*/

				  
			  } else {
				  alert('添加失败！');
				   return false;
			  } 
		   } 
		 });
	return false;
}
function askTousu(){
	var tousuItem = $("input[name=tousuItem1]:checked").val();
	var askid = 3395350;
	var type = 1;
	var content = $("#tousuContent1").val();
	if(content=="") {
		alert("请输入投诉内容!");
		$("#tousuContent1").focus();
		return false;
	}
	$.ajax({ 
		   type: "POST", 
		   url: "/ask/tousu", 
		   data: "id="+askid+"&content="+content+"&item="+tousuItem+"&type=1", 
		   success: function(msg){ 
		     if(msg=='ok'){
			     alert("投诉成功!");
			     $("#tousuContent1").val('');
			     hideZ(5);
			  } else if(msg=="nologin"){
				  alert("请先登录！");
			  } else {
				  alert("投诉失败！");
			  }
		     
		   } 
		 });
	
	return false;
}
function tousu(uname,contentid,obj) {
	$("#tousu_div").show();
	$("#tousu_div").css("top",$(obj).offset().top+$(obj).height()+2);
	$("#tousu_div").css("left",$(obj).offset().left-50);
	$("#tousuren").html(uname);
	$("#tousuContent2").val('');
	$("#tousuId").val(contentid);
	return false;
}
function tousuSubmit(){
	var tousuItem = $("input[name=tousuItem2]:checked").val();
	var askid = $("#tousuId").val();
	var content = $("#tousuContent2").val();
	if(content=="") {
		alert("请输入投诉内容!");
		$("#tousuContent2").focus();
		return false;
	}
	$.ajax({ 
		   type: "POST", 
		   url: "/ask/tousu", 
		   data: "id="+askid+"&content="+content+"&item="+tousuItem+"&type=2", 
		   success: function(msg){ 
		     if(msg=='ok'){
			     alert("投诉成功!");
			     $("#tousuContent2").val('');
			     $("#tousu_div").hide();
			  } else if(msg=="nologin"){
				  alert("请先登录！");
			  } else {
				  alert("投诉失败！");
			  }
		     
		   } 
		 });
	return false;
}
function updateAnswer(aid,obj){
	$("#updateAns_div").show();
	$("#updateAns_div").css("top",$(obj).offset().top+$(obj).height()+2);
	$("#updateAns_div").css("left",$(obj).offset().left-50);
	var content = $("#answer_content_"+aid).html();
	content = content.replace(/\s+/,'');
        content = content.replace(/<.+?>/ig,"\n");
        $("#updateAns_content").html("");
	$("#updateAns_content").val(content);
	$("#ansId").val(aid);
	return false;
}
function updateAnswerSubmit(){
	var ansId = $("#ansId").val();
	var content = $("#updateAns_content").val();
	if(content=="") {
		alert("请输入内容!");
		$("#updateAns_content").focus();
		return false;
	}
	$.ajax({ 
		   type: "POST", 
		   url: "/ask/editanswer", 
		   data: "id="+ansId+"&content="+content, 
		   success: function(msg){ 
		     if(msg=='ok'){
			     alert("修改成功!");
			     location.reload();
			  } else if(msg=="nologin"){
				  alert("请先登录！");
			  } else if(msg=="nopower"){
				  alert("你没有权限修改！");
			  } else if(msg=='notsafe') {
				  alert('内容含有非法词语！');
			  } else {
				  alert("修改失败！");
			  }
		   } 
		 });
	return false;
}
function getcookie(name)
		{
			var arg = name + "=";
			var alen = arg.length;
			var clen = document.cookie.length;
			var i = 0;
			while(i < clen)
			{
				var j = i + alen;
				if(document.cookie.substring(i, j) == arg) return getcookieval(j);
				i = document.cookie.indexOf(" ", i) + 1;
				if(i == 0) break;
			}
			return null;
	}
function getcookieval(offset)
{
	var endstr = document.cookie.indexOf (";", offset);
	if(endstr == -1)
	endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}