// JavaScript Document

var queen=(function(){
	var list=[]
	return {
		status:true,
		add:function(o){
			list.push(o);
		},
		start:function(){
			if(this.status){
				//console.log(list);
				var tmp=list.shift();
				queen.status=false;
				tmp();
			}
			if(list.length){
				setTimeout(arguments.callee,30);
			}
		},
		urlcache:{}
	}
})();
function makeLinks(href,text){
	var tmp=document.createElement('a');
	tmp.href=href;
	tmp.appendChild(document.createTextNode(text));
	return tmp;
}
function getPages(url,ele){
	var status,html='';
	var pele=jQuery(ele).parents().filter(function(){
		if (this.tagName.toLowerCase()=='ol') return this;
	});
	var jTab=jQuery(pele).find('.j-tab-01');
	if(!queen.urlcache[url]){
		queen.urlcache[url]=true;
		queen.add(function(){
			var u=url;
			(function(url){
				var status='';
				jQuery.ajax({
					url : url+'&src=JQ',
					dataType:"json",
					cache:false,
					success:function(data){
						queen.urlcache[url]=data;
						if(data.status==0){
							status=1;
						}else if(data.status==1){
							status=2;
						}else if(data.status==2){
							status=3;
						}else if(data.status==3){
							status=0;
						}
						jTab.html('');
						var j=0;
						jTab[0].insertRow(j);
						jTab[0].rows[j].insertCell(0);
						jTab[0].rows[j].cells[0].width="10%";
						jTab[0].rows[j].cells[0].appendChild(document.createTextNode('科室'));
						jTab[0].rows[j].insertCell(1);
						jTab[0].rows[j].cells[1].width="47%";
						jTab[0].rows[j].cells[1].appendChild(document.createTextNode('标题'));
						jTab[0].rows[j].insertCell(2);
						jTab[0].rows[j].cells[2].width="14%";
						jTab[0].rows[j].cells[2].appendChild(document.createTextNode('查看/回复数'));
						jTab[0].rows[j].insertCell(3);
						jTab[0].rows[j].cells[3].width="10%";
						jTab[0].rows[j].cells[3].appendChild(document.createTextNode('状态'));
						jTab[0].rows[j].insertCell(4);
						jTab[0].rows[j].cells[4].width="9%";
						jTab[0].rows[j].cells[4].appendChild(document.createTextNode('悬赏分'));
						jTab[0].rows[j].insertCell(5);
						jTab[0].rows[j].cells[0].width="10%";
						jTab[0].rows[j++].cells[5].appendChild(document.createTextNode('时间'));
						jQuery('#page_'+status+'a').html(data.pagehtml);
						jQuery('#page_'+status+'b').html(data.pagehtml);
						jQuery.each(data.aList,function(i){
							jTab[0].insertRow(j);
							jTab[0].rows[j].insertCell(0);
							jTab[0].rows[j].cells[0].appendChild(makeLinks("/classid/"+data.aList[i].classid,data.aList[i].name));
							jTab[0].rows[j].insertCell(1);
							jTab[0].rows[j].cells[1].appendChild(makeLinks("/?id="+data.aList[i].id,data.aList[i].title));
							jTab[0].rows[j].insertCell(2);
							jTab[0].rows[j].cells[2].appendChild(document.createTextNode(data.aList[i].browsenum+'/'+data.aList[i].answernum));
							jTab[0].rows[j].insertCell(3);
							jTab[0].rows[j].cells[3].appendChild(document.createTextNode(data.aList[i].status));
							jTab[0].rows[j].insertCell(4);
							jTab[0].rows[j].cells[4].appendChild(document.createTextNode(data.aList[i].point));
							jTab[0].rows[j].insertCell(5);
							jTab[0].rows[j++].cells[5].appendChild(document.createTextNode(data.aList[i].ctime));
						});
						queen.status=true;
					}
				});
			})(u);
		});
		queen.start();
	}else{
		var data=queen.urlcache[url];
		if (typeof data == 'object') {
			setTimeout(function(){
				if(data.status==0){
					status=1;
				}else if(data.status==1){
					status=2;
				}else if(data.status==2){
					status=3;
				}else if(data.status==3){
					status=0;
				}
				var j=0;
				jTab[0].insertRow(j);
				jTab[0].rows[j].insertCell(0);
				jTab[0].rows[j].cells[0].width="10%";
				jTab[0].rows[j].cells[0].appendChild(document.createTextNode('科室'));
				jTab[0].rows[j].insertCell(1);
				jTab[0].rows[j].cells[1].width="47%";
				jTab[0].rows[j].cells[1].appendChild(document.createTextNode('标题'));
				jTab[0].rows[j].insertCell(2);
				jTab[0].rows[j].cells[2].width="14%";
				jTab[0].rows[j].cells[2].appendChild(document.createTextNode('查看/回复数'));
				jTab[0].rows[j].insertCell(3);
				jTab[0].rows[j].cells[3].width="10%";
				jTab[0].rows[j].cells[3].appendChild(document.createTextNode('状态'));
				jTab[0].rows[j].insertCell(4);
				jTab[0].rows[j].cells[4].width="9%";
				jTab[0].rows[j].cells[4].appendChild(document.createTextNode('悬赏分'));
				jTab[0].rows[j].insertCell(5);
				jTab[0].rows[j].cells[0].width="10%";
				jTab[0].rows[j++].cells[5].appendChild(document.createTextNode('时间'));
				jQuery('#page_'+status+'a').html(data.pagehtml);
				jQuery('#page_'+status+'b').html(data.pagehtml);
				jQuery.each(data.aList,function(i){
					jTab[0].insertRow(j);
					jTab[0].rows[j].insertCell(0);
					jTab[0].rows[j].cells[0].appendChild(makeLinks("/classid/"+data.aList[i].classid,data.aList[i].name));
					jTab[0].rows[j].insertCell(1);
					jTab[0].rows[j].cells[1].appendChild(makeLinks("/?id="+data.aList[i].id,data.aList[i].title));
					jTab[0].rows[j].insertCell(2);
					jTab[0].rows[j].cells[2].appendChild(document.createTextNode(data.aList[i].browsenum+'/'+data.aList[i].answernum));
					jTab[0].rows[j].insertCell(3);
					jTab[0].rows[j].cells[3].appendChild(document.createTextNode(data.aList[i].status));
					jTab[0].rows[j].insertCell(4);
					jTab[0].rows[j].cells[4].appendChild(document.createTextNode(data.aList[i].point));
					jTab[0].rows[j].insertCell(5);
					jTab[0].rows[j++].cells[5].appendChild(document.createTextNode(data.aList[i].ctime));
				});
			},1)
		};
	}
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
function checkData() {
	var title = $("#questionTitle").val();
	var content = $("#questionContent").val();
	if(title=="") {
		alert("请输入提问标题！");
		$("#questionTitle").focus();
		return false;
	} else if(content=="") {
		alert("请输入提问内容！");
		$("#questionContent").focus();
		return false;
	} else if(title.length<5||title.length>50) {
		alert("标题字数应在 5-50 之间！");
		$("#questionTitle").focus();
		return false;
	} else if(content.length<10||content.length>500) {
		alert("内容字数应在 10-500 之间！");
		$("#questionContent").focus();
		return false;
	} 
}

//document.write('<script type="text/javascript" src="/images_ask/js/dialog.js"></script>');

var b=new compoDialog();
b.add(maskLayerManger.displaymaskLayer("#949494",0.5));
function changeLabel(obj, __index, __index_1) {
	if(obj.checked) {
		$('#label_' + __index).css('display', '');
		$('#label_' + __index_1).css('display', 'none');
	}
}
function ask(__id) {
	var uid = getcookie('member_uID'); 
	if(uid!=""&&uid!=null) {
		if($("#questionTitle").val()=="") {
			alert("请输入提问标题！");
			$("#questionTitle").focus();
			return false;
		} else if($("#questionContent").val()=="") {
			alert("请输入提问内容！");
			$("#questionContent").focus();
			return false;
		}
	} else {
		var item = $('input[name=register][checked]').val();
		if(item==0) {
			if($("#loginEmail").val()=="") {
				alert("请输入登录邮箱!");
				$("#loginEmail").focus();
				return false;
			} else if($("#loginPass").val()=="") {
				alert("请输入密码!");
				$("#loginPass").focus();
				return false;
			} else if($("#questionTitle").val()=="") {
				alert("请输入提问标题！");
				$("#questionTitle").focus();
				return false;
			} else if($("#questionContent").val()=="") {
				alert("请输入提问内容！");
				$("#questionContent").focus();
				return false;
			}
		} else {
			if($("#regEmail").val()=="") {
				alert("请输入注册邮箱!");
				$("#regEmail").focus();
				return false;
			} else if($("#questionTitle").val()=="") {
				alert("请输入提问标题！");
				$("#questionTitle").focus();
				return false;
			} else if($("#questionContent").val()=="") {
				alert("请输入提问内容！");
				$("#questionContent").focus();
				return false;
			}
		}
	}
	var __d = $('#'+ __id + ' input[type!=radio],input:checked,textarea ');
	var __str = '';
	for(i=0; i<__d.length; i++) {
		if(__d[i].name != '') {
			__str = __str + __d[i].name + '=' + encodeURIComponent(__d[i].value) + '&';
		}
	}
	$.post('/ask/do/do/save/?' + __str, function(data) {
		if(data=='error-100') { //帐号重复
			alert('帐号重复！提问失败！！！');
		} else if (data=='100') {
			alert('帐号或密码错误！！！');
		} else if (data==0) {
			alert('提问失败！');
		} else if (data) {		//成功
			$.get('/ask/loadhtml/html/ok/id/' + data, function(msg) {
				b.add(boxDialogManger.displayboxDialog(msg));
				b.show('<div class="mod tmod-01"><h2 class="h2">问题提交成功</h2><span class="close"><img src="/images_ask/images/ticon_2.png" style="cursor:pointer" onclick="b.hide()"></span><div class="t-content"><span class="top"><span class="tl"></span><span class="tr"></span></span><div class="inner"></div><span class="bottom"><span class="bl"></span>&nbsp;<span class="br"></span></span></div></div>');
			});
		} else {				//失败
			alert('提问失败！！！');
		}
	});
	return false;
}