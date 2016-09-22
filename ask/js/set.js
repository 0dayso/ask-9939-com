var Cookies = {};
Cookies.set = function(name, value) {
    var argv = arguments;
    var argc = arguments.length;
    var expires = (argc > 2) ? argv[2] : null;
    var path = (argc > 3) ? argv[3] : '/';
    var domain = (argc > 4) ? argv[4] : null;
    var secure = (argc > 5) ? argv[5] : false;
    document.cookie = name + "=" + escape(value) +
            ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
            ((path == null) ? "" : ("; path=" + path)) +
            ((domain == null) ? "" : ("; domain=" + domain)) +
            ((secure == true) ? "; secure" : "");
};

Cookies.get = function(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    var j = 0;
    while (i < clen) {
        j = i + alen;
        if (document.cookie.substring(i, j) == arg)
            return Cookies.getCookieVal(j);
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0)
            break;
    }
    return null;
};

Cookies.clear = function(name) {
    if (Cookies.get(name)) {
        document.cookie = name + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
};

Cookies.getCookieVal = function(offset) {
    var endstr = document.cookie.indexOf(";", offset);
    if (endstr == -1) {
        endstr = document.cookie.length;
    }
    return unescape(document.cookie.substring(offset, endstr));
};

(function($) {
    //Pinglun
    var Pinglun = function(obj) {
        this.config = $.extend({
            url: "",
            loginUrl: "",
            box: null,
            hasDing: "",
            userId: "",
            ding: "",
            name: "",
            cai: "",
            answerId: "",
            answerUser: ""
        }, obj);
        this.init();
    }
    Pinglun.prototype.init = function() {
        this.aBind();
    };
    //判断是否登录
    Pinglun.prototype.login = function() {
        //未登录
        if (!Cookies.get(this.config.userId)) {
            alert("请您先登陆！");
            $("#vvvlogin").trigger("click");
            window.moveTo(0, 0)
        }
    };
    //dom绑定
    Pinglun.prototype.aBind = function() {
        var that = this;
        var a = this.config.box.find(that.config.ding);
        var b = this.config.box.find(that.config.cai);
//        console.log(that.config.ding);
        function oBind(o) {
            o.bind("click", function(event) {
                if (!Cookies.get(that.config.ding)) {
                    /* alert("请您先登陆！");
                     $("#vvvlogin").trigger("click");
                     window.scroll(0,0);     */
                    if (that.hasDing(o)) {
                        answerId = o.parent().attr(that.config.answerId);
                        console.log(answerId);
                        _succ = $("#vaosucce"+ answerId);
                        _succ.fadeIn(1000);
                        setTimeout("_succ.hide();", 2000);
                        that.descript(o);
                    } else {
                        alert('您已经评价过，感谢您的支持！');
//                        a.unbind("click")

                    }
                } else if (Cookies.get(that.config.userId) != that.config.answeruser) {
                    if (that.hasDing(o)) {
                        answerId = o.parent().attr(that.config.answerId);
                        console.log(answerId);
                        _succ = $("#vaosucce"+ answerId);
                        _succ.fadeIn(1000);
                        setTimeout("_succ.hide();", 2000);
                        that.descript(o);
                    } else {
                        alert('您已经评价过，感谢您的支持！');
//                        a.unbind("click")

                    }
                    //   return false;
                } else {
                    alert("您不能给自己点赞！");
                    return false;
                }
            });
        }
        oBind(a);
        oBind(b);
    };

    //判断顶踩
    Pinglun.prototype.hasDing = function(o) {
        var that = this;
        var obox = o.parent();
        if (!Cookies.get(that.config.name + obox.attr(that.config.answerId))) {
            return true
        } else {
            return false
        }
    };

    //写入记录
    Pinglun.prototype.descript = function(o) {
        var that = this;
        //操作dom
        var obox = o.parent();
        var a = this.config.box.find(that.config.ding);
        var b = this.config.box.find(that.config.cai);
        var ai = parseInt(a.find("em").html(), 10);
        var bi = parseInt(b.find("em").html(), 10);
        var hasClass = false;
        if (("." + o.attr("class")) == that.config.cai) {
            hasClass = true;
            b.find("em").html(bi + 1);
        } else {
            a.find("em").html(ai + 1);
        }
        a.parent().addClass(that.config.hasDing);
        //写入cookie
        Cookies.set(that.config.name + obox.attr(that.config.answerId), "yes");
        //传给后台
        $.get(that.config.url, {
            article: obox.attr(that.config.answerId),
            answerUser: obox.attr(that.config.answerUser),
            ding: !hasClass ? 1 : 0,
            cai: hasClass ? 1 : 0,
            userId: Cookies.get(that.config.userId)
        }, function(msg) {
        });
    };

    $.fn.Pinglun = function(config) {
        return this.each(function() {
            var oConfig = $.extend({
                box: $(this)
            }, config)
            new Pinglun(oConfig);
        });
    }
})(jQuery);
/**
 * 用法 : 引用set.js
 * 然后 <script>
    $(".dzBx").Pinglun({
       url : "",
       name 
       loginUrl : "http://",//未登录用户跳转地址
       hasDing : "current",//顶过的盒子className
       userId : "member_uID",//用户登录了 以什么名字存在cookie里
       answerId : "",//顶过的文章id  
       answerUser : "", //发布人的user
       ding : "a.zP1",
       cai : "a.zP2"
    })
 </script>
 */