jQuery.fn.selectMode=function(o,b,c) {
	$(o).click(function(){
		$(b).show();
	})
	$(b+' li').each(function() {
			$(this).click(function() {
				$(o).html($(this).find('a').html());
				$(c).val($(this).attr('v'));
				$(b).hide();
				return false;
				})
	})
	$(document).click(function(e) {
		var p=e.target;
		while(p!==this){
			if (p===$(b)[0]||p===$(o)[0]) return true;
			p=p.parentNode;
		}
		$(b).hide();
	})
}	