(function($){
	$.fn.extend({
		"jTab":function(o){
			o = $.extend({
				menutag:	  "",		//选项卡按钮标签
				curIndex:     0,		//默认显示索引
				action:		"click",	//切换动作，默认click
				activeClassName:  "on-zh",
				boxBlock: $(o.menutag).parent().parent().next().children()
			},o);
			var num=arguments.callee.num?arguments.callee.num++:(arguments.callee.num=0,arguments.callee.num++);
			$(o.menutag+":first-child").addClass(o.activeClassName);
            //$(o.menutag).parent().parent().next().children(":not(:first-child)").hide();
            o.boxBlock.filter(":not(:first-child)").hide();
	        o.boxBlock.attr("id", function(){return idNumber(o.activeClassName+num)+ o.boxBlock.index(this)});
			$(o.menutag).bind(o.action,function(){
				var c = $(o.menutag);
                var index = c.index(this);
                var p = idNumber(o.activeClassName+num);
                show(c,index,p);
               	return false;
			});
			
			function show(controlMenu,num,prefix){
              var content= prefix + num;
              $('#'+content).siblings().hide();
              $('#'+content).show();
              controlMenu.eq(num).addClass(o.activeClassName).siblings().removeClass(o.activeClassName);
            }
 
           function idNumber(prefix){
             var idNum = prefix;
             return idNum;
           }
		}
	});
})(jQuery);

