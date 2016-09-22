// JavaScript Document
$(document).ready(function(){
  $('.mainleve').each(function(){
  	$(this).mouseenter(function(){
  		$(this).find("i").addClass("current");
  		$(this).find("ul").slideDown();
  	})
  	$(this).mouseleave(function(){
  		$(this).find("i").removeClass("current");
  		$(this).find("ul").slideUp("fast");
  	})
  })
});
