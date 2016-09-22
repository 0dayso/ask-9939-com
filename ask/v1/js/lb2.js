// JavaScript Document
$(document).ready(function(){
	$dragBln = false;
	$(".moul2").touchSlider({
		flexible : true,
		speed : 200,
		btn_prev : $("#btn_prev1"),
		btn_next : $("#btn_next1"),
		paging : $(".inock2  a"),
		counter : function (e){
			$(".inock2 a").removeClass("on").eq(e.current-1).addClass("on");
		}
	});
	$(".moul2").bind("mousedown", function() {
		$dragBln = false;
	});
	$(".moul2").bind("dragstart", function() {
		$dragBln = true;
	});
	$(".moul2 a").click(function(){
		if($dragBln) {
			return false;
		}
	});
	timer = setInterval(function(){
		$("#btn_next1").click();
	}, 8000);
	$("#lc_focus_2").hover(function(){
		clearInterval(timer);
	},function(){
		timer = setInterval(function(){
			$("#btn_next1").click();
		},8000);
	});
	$(".moul2").bind("touchstart",function(){
		clearInterval(timer);
	}).bind("touchend", function(){
		timer = setInterval(function(){
			$("#btn_next1").click();
		}, 8000);
	});
});
