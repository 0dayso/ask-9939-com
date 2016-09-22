$(function(){ 
    var userAgent = navigator.userAgent.toLowerCase(); 
    if(/msie/.test( userAgent ) && !/opera/.test( userAgent )){
         $('input[placeholder], textarea[placeholder]').placeholder();
    }else{
        $('input[placeholder], textarea[placeholder]').placeholder().focusin(function(){
            var tips = $(this).attr("placeholder");
            $(this).attr("default-text",tips);
            $(this).attr("placeholder","");
        }).focusout(function(){
            var tips = $(this).attr("default-text");
            $(this).attr("placeholder",tips);
        });
    }
});