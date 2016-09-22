// JavaScript Document
$(function(){
	var hot_wenti = $('#hot_wenti');
	var hot_as = $('#hot_wenti a');
	var wenti_list = $('.wenti_list');
	hot_as.mousemove(function(){
		$(this).addClass('active').siblings().removeClass('active');
		wenti_list.hide().eq($(this).index()).show();
	});
});