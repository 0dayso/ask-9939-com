
var curInde = 0;
var timePe = 5000; 
var timePeMov=400;
var contentWra=".zhslide-thumb-wrap";
var changePositio=252;

var timer = null;


$(document).ready(function(){
	
   autoSlide2();
   $(".zhsilde-num li").bind('click',function(){
	clearTimeout(timer);										 
    curInde = $(".zhsilde-num li").index(this);
    slide2(curInde);
	timer = setTimeout(autoSlide2,timePe);
  });
});

function slide2(tindex){
			 
	$(contentWra).animate({
                    left:0-changePositio*tindex
                   },timePeMov); 
				   curInde=tindex;
  $(".zhsilde-num li").eq(tindex).addClass("on").siblings().removeClass("on");
}

function autoSlide2(){
  curInde++;
  if(curInde>2){
    curInde=0;
  }
  slide2(curInde);
  timer = setTimeout(autoSlide2,timePe);
}