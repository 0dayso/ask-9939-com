
var curIndex = 0;
var timePer = 5000; 
var timePerMove=400;
var contentWrap=".zhexpert-wrap";
var changePosition=145;

var timer = null;


$(document).ready(function(){
	
   autoSlide();

});

function slide(tindex){
			 
	$(contentWrap).animate({
                    top:0-changePosition*tindex
                   },timePerMove); 
				   curIndex=tindex;
  $(".zhsilde-num li").eq(tindex).addClass("on").siblings().removeClass("on");
}

function autoSlide(){
  curIndex++;
  if(curIndex>1){
    curIndex=0;
  }
  slide(curIndex);
  timer = setTimeout(autoSlide,timePer);
}