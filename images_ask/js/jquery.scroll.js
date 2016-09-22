(function($){
	$.fn.extend({
		"jScroll":function(o){
			o = $.extend({
				showContent:  "",
				direction: "top",
				scrollDistance:252,
				scrollCount:3,
				timePerNap:  3000,
				timePerMove: 400
			},o);
			
			var timer = null;
			var curInde;
			
			function slide(tindex){
		      if("top"==o.direction){
	          $(o.showContent).animate({
				top:0-o.scrollDistance*tindex
                },o.timePerMove); 
			  }
			  if("left"==o.direction){
				$(o.showContent).animate({
				left:0-o.scrollDistance*tindex
                },o.timePerMove);  
			  }
			  curInde=tindex;
            }
			
            function autoSlide(){
              curInde++;
              if(curInde>o.scrollCount-1){
				clearTimeout(timer);
				curInde=0;
                timer=setTimeout(autoSlide,o.timePerNap);
              }else{
			  clearTimeout(timer);
              slide(curInde);
              timer = setTimeout(autoSlide,o.timePerNap);
			  }
            }
           $(document).ready(function(){
									  autoSlide();
									  })
		}
	});
})(jQuery);