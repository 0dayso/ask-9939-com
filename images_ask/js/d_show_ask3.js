// JavaScript Document

	var b=new compoDialog();
	b.add(maskLayerManger.displaymaskLayer("#949494",0.5));
	var c=new compoDialog();
	c.add(maskLayerManger.displaymaskLayer("#949494",0.5));
	function showHidden(obj) {
		$.get($(obj).attr('href'), function(data) {
			b.add(boxDialogManger.displayboxDialog(data));
			b.show();
		})
		return false;
	}
	function showHiddenHome(__act, obj) {
		$.get('/ask/loadHome/?url=' + __act, function(data){
			b.add(boxDialogManger.displayboxDialog(data));	
			b.show();
			uid = $(obj).attr('class');
			$('#frm')[0].action = "/ask/home/?url=friend/add/fid/"+uid;
			$('#uimg').attr('src', $('#'+uid).attr('src'));
			return false;
		});
		return false;
	}
	function hidden() {
		b.hide();
	}
	function cannelShow() {
		b.hide();
	}
	function changeLabel(obj, __index, __index_1) {
		if(obj.checked) {
			$('#label_' + __index).css('display', '');
			$('#label_' + __index_1).css('display', 'none');
		}
	}
	function ask(__id,obj) {
		$(obj).attr('disabled', 'disabled');
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
					$(obj).attr('disabled', '');
				} else if (data=='100') {
					alert('帐号或密码错误！！！');
					$(obj).attr('disabled', 'disabled');
				} else if (data) {		//成功
					$.get('/ask/loadhtml/html/ok/id/' + data, function(msg) {
						//alert('/ask/loadhtml/html/ok/id/' + data);
						c.add(boxDialogManger.displayboxDialog(msg));
						c.show('<div class="mod tmod-01"><h2 class="h2">会员提交成功</h2><div class="t-content"><span class="top"><span class="tl"></span><span class="tr"></span></span><div class="inner"></div><span class="bottom"><span class="bl"></span>&nbsp;<span class="br"></span></span></div></div>');
						$(obj).attr('disabled', '');
					});
				} else {				//失败
					alert('提问失败！！！');
					$(obj).attr('disabled', 'disabled');
				}
			});
			
		return false;
	} 

//举报用
var xxk =new compoDialog();
function closedv(){
	xxk.hide();
}
function add(o,i){
	if(i==1){
		var url = '/Commentindex';
	}else if(i==2){
		var url = '/Reportindex';
	}
	xxk.add(maskLayerManger.displaymaskLayer("#949494",0.2));
	xxk.add(boxDialogManger.displayboxDialog('<form action="'+url+'" method="get"><div class="t-innerWrap"><input type="hidden" id="xid" name="id" value=""><input type="hidden" id="xidtype" name="idtype" value=""><input type="hidden" id="xuid" name="uid" value=""><input type="hidden" id="xcid" name="cid" value=""><textarea name="message" rows="8" cols="40"></textarea><br><span><input type="submit" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id="qx" value="取消" onclick="closedv()"></span></div></form>'));
	xxk.show()
	if(o.href){
		var hr     = o.href.split('&');
		var id     = hr[0].split('=')[1];
		var idtype = hr[1].split('=')[1];
		if(i==1){
			var uid    = hr[2].split('=')[1];
			var cid    = hr[3].split('=')[1];
			if($('#xuid')){
				$("#xuid").val(uid);
			}
			if($('#xcid')){
				$("#xcid").val(cid);
			}
		}
		if($('#xid')){
			$("#xid").val(id);
		}
		if($('#xidtype')){
			$("#xidtype").val(idtype);
		}
	}
}
function getTop(e){
	var offset=e.offsetTop;
	if(e.offsetParent!=null) offset+=getTop(e.offsetParent);
	return offset;
}
function getLeft(e){
	var offset=e.offsetLeft;
	if(e.offsetParent!=null) offset+=getLeft(e.offsetParent);
	return offset;
}

