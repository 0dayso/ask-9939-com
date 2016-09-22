$(function(){
		$('<iframe class="temp-iframe" width="380" height="200" frameBorder="0" style="position:absolute;top:0;left:0;"></iframe>').hide().insertBefore(".t-login");
		$('.top-tip li:first-child').click(function() {
		if ($('.temp-iframe')) {$('.temp-iframe').show().css('left',$(this).position().left+30+'px')};
		if($(this).hasClass('g-login')){			$('.t-login').removeClass('k-display-none').css('left',$(this).position().left+30+'px').find("input[type='text']:first-child").focus(); 
			return false;
		} 
	})
	$(document).click(function(e){
		
		var p=e.target;
		//if(p!==this){
			while(typeof(p) != 'undefined' && p != null && p != this){
			if (p===$('.t-login')[0]||p.className=='g-login') return true;
			p=p.parentNode;
		}
		if ($('.temp-iframe')) {$('.temp-iframe').hide()};
		$('.t-login').addClass('k-display-none')
		})
	$('.t-login .close').click(function() {
		
		if ($('.t-login').hasClass('k-display-none')) return;
			$('.t-login').addClass('k-display-none');
		if ($('.temp-iframe')) {$('.temp-iframe').hide()};
		})
});
var tLogin=function(o) {
	$('.t-login').removeClass('k-display-none').css('left',$(o).position().left+30+'px').find("input[type='text']:first-child").focus();
			return false;
}; 