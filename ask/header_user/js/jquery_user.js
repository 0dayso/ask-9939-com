/**
 * @author feiwen
 */
(function($){
	$.fn.textSlider = function(settings){    
        settings = jQuery.extend({
        	speed : "normal",
			line : 2,
			timer : 1000
    	}, settings);
		return this.each(function() {
			$.fn.textSlider.scllor( $( this ), settings );
    	});
    }; 
	$.fn.textSlider.scllor = function($this, settings){
		//alert($this.html());
		var ul = $( "ul:eq(0)", $this );
		var timerID;
		var li = ul.children();
		var _btnUp=$(".up:eq(0)", $this)
		var _btnDown=$(".down:eq(0)", $this)
		var liHight=$(li[0]).height();
		var upHeight=0-settings.line*liHight;//滚动的高度；
		var scrollUp=function(){
			_btnUp.unbind("click",scrollUp);
			ul.animate({marginTop:upHeight},settings.speed,function(){
				for(i=0;i<settings.line;i++){
                	 //$(li[i]).appendTo(ul);
					 ul.find("li:first").appendTo(ul);
					// alert(ul.html());
                }
               	ul.css({marginTop:0});
                _btnUp.bind("click",scrollUp); //Shawphy:绑定向上按钮的点击事件
			});	
		};
		var scrollDown=function(){
			_btnDown.unbind("click",scrollDown);
			ul.css({marginTop:upHeight});
			for(i=0;i<settings.line;i++){
				ul.find("li:last").prependTo(ul);
            }
			ul.animate({marginTop:0},settings.speed,function(){
                _btnDown.bind("click",scrollDown); //Shawphy:绑定向上按钮的点击事件
			});	
		};
		var autoPlay=function(){
			timerID = window.setInterval(scrollUp,settings.timer);
			//alert(settings.timer);
		};
		var autoStop = function(){
            window.clearInterval(timerID);
        };
		//事件绑定
		ul.hover(autoStop,autoPlay).mouseout();
		_btnUp.css("cursor","pointer").click( scrollUp );
		_btnUp.hover(autoStop,autoPlay);
		_btnDown.css("cursor","pointer").click( scrollDown );
		_btnDown.hover(autoStop,autoPlay)
	};
})(jQuery);


$(function(){
		$('<iframe class="temp-iframe" width="380" height="200" frameBorder="0" style="position:absolute;top:0;left:0;"></iframe>').hide().insertBefore(".t-login");
		$('.top-tip li:first-child').click(function() {
		if ($('.temp-iframe')) {$('.temp-iframe').show().css('left',$(this).position().left+30+'px')};
		if($(this).hasClass('g-login')){			$('.t-login').removeClass('k-display-none').css('left',$(this).position().left+30+'px').find("input[type='text']:first-child").focus();
			return false;
		}
	})
	$(document).click(function(e){
		try{
		var p=e.target;
		while(p!==this){
			if (p===$('.t-login')[0]||p.className=='g-login') return true;
			p=p.parentNode;
		}
		if ($('.temp-iframe')) {$('.temp-iframe').hide()};
		$('.t-login').addClass('k-display-none')
         }catch(e){
            
        }
})
	$('.t-login .close').click(function() {
		try{
		if ($('.t-login').hasClass('k-display-none')) return;
			$('.t-login').addClass('k-display-none');
		if ($('.temp-iframe')) {$('.temp-iframe').hide()};
        }catch(e){
            
        }
		})
       
});
var tLogin=function(o) {
	$('.t-login').removeClass('k-display-none').css('left',$(o).position().left+30+'px').find("input[type='text']:first-child").focus();
			return false;
}; 
