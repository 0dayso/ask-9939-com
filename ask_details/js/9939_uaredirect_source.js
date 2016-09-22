(function(){
    var map = [];
    map['ask.9939.com']='http://wapask.9939.com/';
    var hostname = String(window.location.hostname).toLowerCase();
    var pathname = String(window.location.pathname).toLowerCase();
    pathname = pathname=="/"?'':pathname;
    var match_url = hostname+pathname;
    match_url = match_url.replace('index.shtml','');
    
    if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))){
        if(typeof(map[match_url])!="undefined"){
            window.location=map[match_url];
        }
    }
})();