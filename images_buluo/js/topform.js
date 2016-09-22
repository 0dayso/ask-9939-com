	$('.top-tip li:first-child').click(function() {
		if ($(this).hasClass('g-login')) {
			$('.t-login').removeClass('k-display-none').css('left',$(this).position().left+30+'px').find("input[type='text']:first-child").focus();
			return false;
		}
	})
	$(document).click(function(e){
		var p=e.target;
		while(p!==this){
			if (p===$('.t-login')[0]) return true;
			//alert(p.nodeName)
			p=p.parentNode;
		}
		$('.t-login').addClass('k-display-none')
		})