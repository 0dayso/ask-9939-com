// JavaScript Document
$(document).ready(function () {
    $('.mainleve').each(function () {
        $(this).mouseenter(function () {
            $(this).find("i").addClass("current");
            $(this).find("ul").slideDown();
        })
        $(this).mouseleave(function () {
            $(this).find("i").removeClass("current");
            $(this).find("ul").slideUp("fast");
        })
    })

    $('.hotwords a:nth-child(6n+6)').css('margin-right', '0');
    $('.currm').mousemove(function () {
        var zimu = $(this).attr('switc');
        var className = ".lett-tab-" + zimu;
        $('.move').removeClass('move');
        $(this).addClass('move');
        var div = $(".lett-tab-con").find(className);
        $(".lett-tab-con div").addClass('curro');
        if (div) {
            div.removeClass('curro');
        }
    }).click(function () {
        return true;
    });

    /*限制条数*/
    $('.hr_left01').find('.cinfo').each(function (index, domEle) {
        if ($(domEle).height() > 66) {
            $(domEle).css('height', '64px');
            $(domEle).next().show();
        }
    });
    var bool = true;
    $('a.folen').click(function () {
        if (bool) {
            $(this).prev().css('height', 'auto');
            $(this).html('收起&nbsp;∧');
            bool = false;
        }
        else {
            $(this).prev().css('height', '64px');
            $(this).html('展开&nbsp;∨');
            bool = true;
        }
    });
    var boc = true;
    $('a.fres').click(function () {
        if (boc) {
            $('.capain').slideDown();
            boc = false;
        }
        else {
            $('.capain').slideUp();
            boc = true;
        }

    });
});