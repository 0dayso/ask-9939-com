(function($){
	$.fn.extend({
		"jSlide":function(o){
			o = $.extend({
				navContent:	  "",		//选项卡按钮标签
				showContent:  "",
				action:		"click",	//切换动作，默认click
				scrollDistance:252,
				scrollCount:3,
				activeClassName:  "on",
				timePerNap:  3000,
				timePerMove: 400
			},o);
			
			var timer = null;
			var curIndex;
			
	        $(o.navContent).bind(o.action,function(){
	          clearTimeout(timer);										 
              curIndex = $(o.navContent).index(this);
              slide(curIndex);
	          timer = setTimeout(autoSlide,o.timePerNap);
            });	
			
			function slide(tindex){
				$(o.showContent).animate({
				left:0-o.scrollDistance*tindex
                },o.timePerMove);  

			  curIndex=tindex;
			  if(o.navContent){
                $(o.navContent).eq(tindex).addClass(o.activeClassName).siblings().removeClass(o.activeClassName);
			  }
            }
			
            function autoSlide(){
              curIndex++;
              if(curIndex>o.scrollCount-1){
				clearTimeout(timer);
				curIndex=-1;
                timer=setTimeout(autoSlide,o.timePerNap);
              }else{
			  clearTimeout(timer);
              slide(curIndex);
              timer = setTimeout(autoSlide,o.timePerNap);
			  }
            }
          
		}
	});
})(jQuery);