$(function () {
    $('.heaex a').hover(function () {
        var ind = $(this).index();
        $('.heaex a').removeClass('cure');
        $(this).addClass('cure');
        $('.ouinf .oubor').removeClass('shay').addClass('disn');
        $('.ouinf .oubor').eq(ind).removeClass('disn').addClass('shay');
    });
    $('.alla a').hover(function () {
        var indc = $(this).index();
        $(this).parent().find('a').removeClass('curb');
        $(this).parent().find('a b').removeClass('shay').addClass('disn');
        $(this).addClass('curb');
        $(this).find('b').removeClass('disn').addClass('shay');

        $(this).parent().next('.asin').find('.neinfo').removeClass('shay').addClass('disn');
        $(this).parent().next('.asin').find('.neinfo').eq(indc).removeClass('disn').addClass('shay');
        $(this).parent().next('.asin').find('.neinfo').eq(indc).find('a').removeClass('cusb');
        $(this).parent().next('.asin').find('.neinfo').eq(indc).find('a').eq(0).addClass('cusb');
        $(this).parent().next().next().find('.pseur').removeClass('shay').addClass('disn');
        $(this).parent().next().next().find('.pseur').find('.thrinf').removeClass('shay').addClass('disn');
        $(this).parent().next().next().find('.pseur').eq(indc).removeClass('disn').addClass('shay');
        $(this).parent().next().next().find('.pseur').eq(indc).find('.thrinf').eq(0).removeClass('disn').addClass('shay');
    });
    $('.neinfo a').hover(function () {
        var indb = $(this).index();
        var ind2 = $(this).parent('.neinfo').index();

        $(this).parent().find('a').removeClass('cusb');


        $(this).addClass('cusb');
        $(this).parents('.asin').next('.outba').find('.pseur,.thrinf').removeClass('shay').addClass('disn');
        $(this).parents('.asin').next('.outba').find('.pseur').eq(ind2).removeClass('disn').addClass('shay');
        $(this).parents('.asin').next('.outba').find('.pseur').eq(ind2).find('.thrinf').eq(indb).removeClass('disn').addClass('shay');
    });

});