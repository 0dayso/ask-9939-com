// JavaScript Document
$(function(){
$(document).jTab({
  menutag:".hd li",
  action:"mouseover",
  activeClassName:"on"
  });
$(".zhtag-more").hide();
$(".zhsidebar .zhmod01 h4").bind("mouseover", function(){
  $(this).find(".zhtag-more").show();
});
$(".zhsidebar .zhmod01 h4").bind("mouseout", function(){
  $(this).find(".zhtag-more").hide();
});

$(".ques-broad .bd").textSlider({
 line:1,
 speed:500,
 timer:3000
 });
});
//搜索用
function mksearch(id,i)
{
	var kw = $("#"+id).val();
	var type = 10; 
	if(i==1){
		window.location.href="<?=SO_URL?>index/index/kw/"+kw+"/type/"+type;
	}else if(i==2){
		window.location.href="<?=ASK_URL?>Asking/index/kw/"+kw;
	}
	return false;
}