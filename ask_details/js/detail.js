$(function () {
	//经验分享
   	$('.tnav li.exs').hover(function(){
		$('.excom').removeClass('disn').addClass('shay');
	},function(){
		$('.excom').removeClass('shay').addClass('disn');		
		
	});
	
	
	$('.excom').hover(function(){
		$('.tnav li.exs').addClass('cus');	
	},function(){
		$('.tnav li.exs').removeClass('cus');	
		$('.excom').removeClass('shay').addClass('disn');	
	});
	//右侧定位弹出
	$('.rightbar dd.hel_01').hover(function(){
		$('.rightbar dt').show();	
	},function(){
		$('.rightbar dt').hide();		
	});
	$('.rightbar dt').hover(function(){
		$(this).show();
		$('.rightbar dd.hel_01').addClass('hecur');
	},function(){
		$(this).hide();	
		$('.rightbar dd.hel_01').removeClass('hecur');
	});
	//首页经验分享去除li右边边距
	$('.regula').find('li:last-child').css('margin-right','0');
	//分享
	$('.shacon div').hover(function(){
		$(this).parent().find('ul').show();	
	});
	$('.shacon ul,.shacon').mouseleave(function(){
		$('.shacon ul').hide();	
	});
	//经验分享tanchu 
	$('.lacol a').click(function(){
		$('.lacol a').removeClass('atc');
		$(this).addClass('atc');
		var ind=$(this).index();
		$('.lacol a').find('div').removeClass('shay').addClass('disn');
		$(this).find('div').removeClass('disn').addClass('shay');
		$('.alinf .necol').removeClass('shay').addClass('disn');
		$('.alinf .necol').eq(ind).removeClass('disn').addClass('shay');	
	});
	$('.lacol .atc').click(function(){
		$('.alinf .necol').removeClass('shay').addClass('disn');	
	});
	$('.a_prev').find('li:odd').css('margin-right','0');
	$('.excom').find('a:last').css('border-bottom','none');
	//固定定位
	var hei=$(document).height()-950;
	
	$(window).scroll(function() {
		if(($(this).scrollTop()>335)&&($(this).scrollTop()<hei)){
			$('#float').addClass('float');	
		}
		else{
			$('#float').removeClass('float');		
		}	
	});
	
	$(window).scroll(function() {
		if(($(this).scrollTop()>250)&&($(this).scrollTop()<hei)){
			$('#float2').addClass('float');	
		}
		else{
			$('#float2').removeClass('float');		
		}	
	});
	//我的分享弹出
	$('.tips a').click(function(){
		$('.tips,.outbra').removeClass('shay').addClass('disn');	
	});
});
