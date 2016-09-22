//用于ajax输出搜索结果
function searchdata(){
	var kw = $("#ask_title").val();
	if(kw!=""){
		$.get('/Ajaxsearch/index/kw/'+encodeURIComponent(kw),
		function(data){
			//alert(data)
			$('#search_data').html('');
			$('#search_data').append(data);
			var marr = data.match(/\/id\/(\d+)/g);
			idstr = '';
			for(i=0;i<marr.length;i++){
				var tmpid = marr[i].substring(4);
				if(tmpid!=''){
					idstr += tmpid + ',';
				}
			}
			idstr = idstr.substring(0,idstr.length-1);
			if(idstr!=""){
				$.get('/Ajaxsearch/keshilist/askid/'+idstr,
				function(data){
					//alert(data);
					$('#auto').html('');
					$('#auto').append(data);
					var alistkeshi = document.getElementsByName('listkeshi');
					alistkeshi[0].checked = 'true';
					getKeshiList(alistkeshi[0]);
				});
				//$('#load_catid_container').css('display','block');
			}
		});
	}else{
		$("#ask_title").val('请输入您的提问标题');
	}
}

function getKeshiList(t){
	$('#load_catid').html('');
	$('#load_catid_other').html('');
	var stype = t.value;
	var atype = stype.split('_');
	$('#catid').val(atype[atype.length-1]);
}