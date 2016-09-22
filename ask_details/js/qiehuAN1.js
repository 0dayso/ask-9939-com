//JavaScript Document
$(function(){
	var hot_wenti = $('wd_pd_spa');
	var hot_as = $('#wd_pd_spa li');
	var wenti_list = $('.wd_qh');
	hot_as.mousemove(function(){
		$(this).addClass('active').siblings().removeClass('active');
		wenti_list.hide().eq($(this).index()).show();
	});
});