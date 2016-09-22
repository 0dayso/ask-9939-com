// JavaScript Document
$(document).ready(function(){
	$dragBln = false;
	$(".moul").touchSlider({
		flexible : true,
		speed : 200,
		btn_prev : $("#btn_prev"),
		btn_next : $("#btn_next"),
		paging : $(".inock  a"),
		counter : function (e){
			$(".inock a").removeClass("on").eq(e.current-1).addClass("on");
		}
	});
	$(".moul").bind("mousedown", function() {
		$dragBln = false;
	});
	$(".moul").bind("dragstart", function() {
		$dragBln = true;
	});
	$(".moul a").click(function(){
		if($dragBln) {
			return false;
		}
	});
	timer = setInterval(function(){
		$("#btn_next").click();
	}, 7000);
	$("#lc_focus_1").hover(function(){
		clearInterval(timer);
	},function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		},7000);
	});
	$(".moul").bind("touchstart",function(){
		clearInterval(timer);
	}).bind("touchend", function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		}, 7000);
	});
});
