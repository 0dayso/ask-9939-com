$(function() {
	    $(".nlwo").find("div.secname ul").find("li").click(function(){
			$(this).addClass("selected").siblings().removeClass("selected");
            var index2 = $(this).index();
			$(this).parents(".nlwo").find(".lkod>div").eq(index2).show().siblings().hide();
		});
});	
function bq(obj1,obj2){
	$("."+obj1+" ul li").click(function(){
		$(this).addClass("on").siblings().removeClass("on");
		$("."+obj2+" dl").hide()
		$("."+obj2+" dl:eq("+$(this).index()+")").show()
	})
}
$(function () {
    var al_a = $('.mebox_a a');
    al_a.mousemove(function () {
        var data_id = $(this).attr("data-id");
        var div_chg_obj = $("#div_"+data_id);
        div_chg_obj.find(".meline").hide().eq($(this).index()).show();
        $(this).addClass('active').siblings().removeClass('active');
    });

});
$(function(){
	bq("hr_rig01","hr_left01");
})
function notice(idname, info) {
	//参数接收处理
	var dcolor = arguments[2] ? arguments[2] : 'grey';
	var scolor = arguments[3] ? arguments[3] : 'black';
	var getval = arguments[4] ? arguments[4] : '';
	//初始样式和值
	if (getval) {
		$('#' + idname).val(getval).css('color', scolor);
	} else {
		$('#' + idname).val(info).css('color', dcolor);
	}
	//获取焦点和失去焦点时的样式和值
	$('#' + idname).focus(function() {
		if ($(this).val() == info) {
			$(this).val('');
			$(this).css('color', scolor);
		}
	}).blur(function() {
		if ($(this).val() == '') {
			$(this).val(info);
			$(this).css('color', dcolor);
		}
	}).parents('form').submit(function() {
		if ($.trim($('#' + idname).val()) == info) {
			$('#' + idname).val('');
		}
		$('#' + idname).focus().css('color', scolor);
	});
}   
