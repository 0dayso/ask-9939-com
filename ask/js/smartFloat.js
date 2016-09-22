$.fn.smartFloat = function (opt) {
        var defaults = {
            height      : 'auto',
            position    : 'fixed',
            top         : '0px'
        };
        function checkScrollToBottom(flag,offsetBottom) {
            if(flag){
                var scrollTop = $( window ).scrollTop();
                var scrollHeight = $( document ).height();
                var windowHeight = $( window ).height();
                offsetBottom = parseInt(offsetBottom);
                if( scrollTop + windowHeight >= scrollHeight) {
                    return true;
                }
                return false;
            }
            return false;
        }
        opt = $.extend({}, defaults, opt || {});
        var position = function (element) {
            var top = element.position().top, pos = element.css("position"),w = element.width(),h = element.height();
            var _bottom = typeof(opt['bottom'])!='undefined'?opt['bottom']:'0px';
            var flag = typeof(opt['bottom'])!='undefined';
            var _top = typeof(opt['top'])!='undefined'?opt['top']:'0px'; 
            $(window).scroll(function () {
                var scrolls  = $(this).scrollTop();
                var windowHeight = $( window ).height();
                if (scrolls  > top && !checkScrollToBottom(flag,_bottom)) {
                    if (window.XMLHttpRequest) {
                        element.css(opt);
                    } else {
                        element.css({
                            top: scrolls 
                        });
                    }
                } else {
                    if(checkScrollToBottom(flag,_bottom)){
                        var p_top = ( parseFloat(windowHeight) - parseFloat(_bottom)-parseFloat(h)-parseFloat(_top))+'px';
                        var tmp_pos = element.css("position");
                        element.css({
                            width   : w,
                            position: tmp_pos,
                            top     : p_top
                        });
                    }else{
                        element.css({
                            width   : w,
                            position: pos,
                            top: top
                        });
                    }
                }
            });
        };
        return $(this).each(function () {
            position($(this));
        });
};