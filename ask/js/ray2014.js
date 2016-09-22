function bq(obj1,obj2){
	$("."+obj1+" ul li").click(function(){
		$(this).addClass("on").siblings().removeClass("on");
		//$("."+obj2+" dl:eq("+$(this).index()+")").show().siblings().hide();
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

