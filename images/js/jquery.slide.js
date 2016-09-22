              
			  
			  var curPage = 0;                                //当前显示页
			  var changePosition = 660;                      //一次移动距离
			  var timePerMove = 500;                        //移动花费时间（毫秒）
			  var curPosition;                             //当前位置
			  var navWrap = ".group-recommend .hd span";  //导航容器
			  var contentWrap = ".group-rec-innerwrap";  //内容容器
			  
			  $(document).ready(function(){
				$(navWrap).children().each(function(){
				  $(this).bind('focus',function(){
					  $(this).blur();						
				  });  									
				});						 
				$(navWrap+' .pre').bind('click',function(){
				  move("left");
				});
				$(navWrap+' .next').bind('click',function(){
				  move("right");
				});
				$(navWrap).children().slice(1,4).each(function (i){
	              $(this).bind('click',function() {
		            move(i);
	              });
                }); 
			  });
			  
			  function move(direction){
			    curPosition = parseInt($(contentWrap).css("left"))
			    if("right"==direction){
				  if(curPage<2){
			      $(contentWrap).animate({
                    left:curPosition-changePosition , opacity: 1
                   }, { duration:timePerMove});
				   curPage++; 
				   setPage();
				 }
			    }
				else if("left"==direction){
				  if(curPage>0){
				    $(contentWrap).animate({
                      left:curPosition+changePosition , opacity: 1
                      }, { duration:timePerMove}); 
				    curPage--;
				    setPage();
				  }
				}
				else{
				  $(contentWrap).animate({
                    left:0-changePosition*direction , opacity: 1
                   }, { duration:timePerMove}); 
				   curPage=direction;
				   setPage();
				}
			  }
			  
			  function setPage(){
			    $(".page-zh").eq(curPage).addClass("page-on").siblings().removeClass("page-on");
				if(0==curPage){
				  $(navWrap+' .pre').css({cursor : 'default' , 'background-position' : '0 -242px' });
				  $(navWrap+' .next').css({cursor : 'pointer' , 'background-position' : '-26px -281px' });
				}else if(2==curPage){
				  $(navWrap+' .pre').css({cursor : 'pointer' , 'background-position' : '0 -281px' });
				  $(navWrap+' .next').css({cursor : 'default' , 'background-position' : '-26px -242px' });
				}else{
				  $(navWrap+' .pre').css({cursor : 'pointer' , 'background-position' : '0 -281px' });
				  $(navWrap+' .next').css({cursor : 'pointer' , 'background-position' : '-26px -281px' });
				}
			  }