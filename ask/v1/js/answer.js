$(function() {
    $('#latak,.latak').click(function() {
        _counter = $(".dzone").length;
        var prohibit_reply = parseInt($(this).attr('data-prohibit_reply'));
        if (Cookies.get("member_uID") !== null) {
            switch (_loginStatus) {
                case 2://普通用户登陆提示注册专家
                    $("#answer_need_reg").show();
                    break;

                case 3://医生账户未通过验证
                    $("#answer_doctor_need_auth").show();
                    break;

                case 4://医生并且验证通过
                    if (_counter > 2) {
                        alert('当前问题已有2人回复，请回复别的问题。');
                        return false;
                    }
                    if(prohibit_reply==1){
                        alert('当前问题已关闭,请回复别的问题!');
                        return false;
                    }
                    $("#capain").stop(true, false).slideToggle(300);
                    return false;
                    break;
            }
        } else {
            $("#answer_need_login").show();
            return false;
        }
    });
    //不可回复的弹框
//    $(".prohibit_reply:eq(0)").click(function(){
//        alert("该问题已不再接受回复，请选择回答其他问题！");
//    });
   
    
    $(".colo2 a").click(function() {
        _id = $(this).attr('data_id');
        _username = $(this).attr('data_username');
        _uid = $(this).attr('data_uid');
        //    console.log(_uid);
        reportDialog(_id, _username, _uid, 'reportMsg');
    });


    //采纳为最佳答案
    $(".helpful").click(function() {
        ask_id = $(this).attr('ask_id');
        answer_id = $(this).attr('answer_id');
        _url = '/ask/over/askid/' + ask_id + '/id/' + answer_id;
        $.ajax({type: "GET", url: _url, data: '',
            success: function(msg) {
                switch (msg) {
                    case 'success':
//                        $(".vaosucce").fadeIn(1000);
//                        setTimeout("$('.vaosucce').hide();", 2000);
                        alert('本回答已采纳为正确答案。');
                        window.location.href = "/id/" + ask_id;
//                        setTimeout(
//                            function(){
//                                $(".vaosucce").hide();
//                                window.location.href="/id/"+ask_id;
//                            },2000);
//                        
                        return false;
                        break;

                    default:
                        alert(msg);
                        break;
                }
            }
        });
    });

    //关闭弹窗
    //$('.xlerr a,.ts_sub').click(function() {
    //    $('.bounce,.qbwrt').hide();
    //});
    $('.xlerr a').click(function() {
        $('.bounce,.qbwrt').hide();
    });

    $('.mebxle a').click(function() {
        $('.l-mod').hide();
    });
    $('#dzBx em').click(function() {
        $('.vaosucce').show();
    })


    $('#answer_content').focus(function() {
        if ($(this).val() == '请根据患者提问的内容，给予专业详尽的指导意见。（最多输入500字）') {
            $(this).val('');
        }
        $(this).attr({"style": "color:#333"});
    }).blur(function() {
        if ($(this).val() == '') {
            $(this).val('请根据患者提问的内容，给予专业详尽的指导意见。（最多输入500字）');
        }
        $(this).attr({"style": "color:#999"});
    });

    $('#answer_suggest').focus(function() {
        if ($(this).val() == '请给出具体的运动，饮食，康复等方面的指导。（最多输入500字）') {
            $(this).val('');
        }
        $(this).attr({"style": "color:#333"});
    }).blur(function() {
        if ($(this).val() == '') {
            $(this).val('请给出具体的运动，饮食，康复等方面的指导。（最多输入500字）');
        }
        $(this).attr({"style": "color:#999"});
    });


    //回答框字数统计
    $('#answer_content').bind('focus keyup input paste', function() {
        var len = $(this).val().length;
        if (len > 500) {
            $(this).val($(this).val().substring(0, 500));
            alert('病情分析不能超过500字');
        }
        $('#contentNum').text($(this).val().length);
    });

    $('#answer_suggest').bind('focus keyup input paste', function() {
        var len = $(this).val().length;
        if (len > 500) {
            $(this).val($(this).val().substring(0, 500));
            alert('意见建议不能超过500字');
        }
        $('#suggestNum').text($(this).val().length);
    });

    $("#answer_submit").bind('click', function() {
        checkAnswerData();
    });

    $("#form_answer").bind('keydown', function() {
        ctrlEnter(event, 'answer');
    });

    $(".aaaaa").Pinglun({
        url: "/Praisestep/praise",
        name: "testpraise",
        loginUrl: "/user", //未登录用户跳转地址
        hasDing: "current", //顶过的盒子className
        userId: "member_uID", //用户登录了 以什么名字存在cookie里
        answerId: "answerid", //顶过的文章id  
        answerUser: "answeruser", //发布人的id
        ding: ".dzBx",
        num: ".zP1"
    });
});



/*
 * @returns {String}
 */
function getTimer() {
    var now = new Date();
    var nowStr = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
    return nowStr;
}

function jump(askid) {
    window.location.href("/id/" + askid);
}

/*
 * 
 * @returns {Boolean}
 */
function checkAnswerData() {
    var content = $("#answer_content").val() == '请根据患者提问的内容，给予专业详尽的指导意见。（最多输入500字）' ? '' : $("#answer_content").val();
    var suggest = $("#answer_suggest").val() == '请给出具体的运动，饮食，康复等方面的指导。（最多输入500字）' ? '' : $("#answer_suggest").val();
    var askid = $("#answer_askid").val();
    var point = $("#answer_point").val();
    if (content == "") {
        alert('请输入回答内容!');
        $("#answer_content").focus();
        return false;
    }
    if (suggest == "") {
        alert('请输入意见建议!');
        $("#suggest_suggest").focus();
        return false;
    }
//    $("#answer_submit").attr("disabled", "disabled");
    var myDate = new Date();
    nowTime = myDate.getTime() / 1000;
//    console.log(nowTime);
    _lastAnswer = Cookies.get("last_answer" + askid);
//    console.log(_lastAnswer);
    if (nowTime - _lastAnswer < 5 * 60) {
        alert('5分钟内禁止重复回答！');
        return false;
    }


    _data = "answer[content]=" + content + "&answer[suggest]=" + suggest + "&answer[askid]=" + askid + "&answer[point]=" + point;
    $.ajax({
        type: "POST",
        url: "/answer/",
        data: _data,
        success: function(msg) {
            switch (msg) {
                case 'success':
                    $("#answer_success").show();
                    setTimeout(function() {
                        window.location.href = "/id/" + askid;
                    }, parseInt(3000));
//                    setTimeout("window.location.reload()", 3000);
                    return false;
                    break;

                default:
                    alert(msg);
                    break;
            }
        }
    });
    return false;
}


/*
 * 
 * @param {type} e
 * @param {type} keyCode
 * @returns {Boolean}
 */
function isKeyTrigger(e, keyCode) {
    var argv = isKeyTrigger.arguments;
    var argc = isKeyTrigger.arguments.length;
    var bCtrl = false;
    if (argc > 2) {
        bCtrl = argv[2];
    }
    var bAlt = false;
    if (argc > 3) {
        bAlt = argv[3];
    }
    var nav4 = window.Event ? true : false;
    if (typeof e == 'undefined') {
        e = event;
    }
    if (bCtrl &&
            !((typeof e.ctrlKey != 'undefined') ?
                    e.ctrlKey :
                    e.modifiers & Event.CONTROL_MASK > 0)) {
        return false;
    }
    if (bAlt &&
            !((typeof e.altKey != 'undefined') ?
                    e.altKey : e.modifiers & Event.ALT_MASK > 0)) {
        return false;
    }
    var whichCode = 0;
    if (nav4)
        whichCode = e.which;
    else if (e.type == "keypress" || e.type == "keydown")
        whichCode = e.keyCode;
    else
        whichCode = e.button;
    return (whichCode == keyCode);
}

/*
 * 
 * @param {type} e
 * @param {type} formname
 * @returns {undefined}
 */
function ctrlEnter(e, formname) {
    return false;
    var ie = navigator.appName == "Microsoft Internet Explorer" ? true : false;
    if (ie) {
        if (event.ctrlKey && event.keyCode == 13) {
            checkAnswerData();
        }
    } else {
        if (isKeyTrigger(e, 13, true)) {
            checkAnswerData();
        }
    }
}


//
//function reportMsg(objId) {
//    var content = $(objId).find('#tousu_content');
////    alert(content.value);
////    return false;
//    if (content.value == "") {
//        alert("请输入投诉内容!");
//        content.focus();
////        $(obj).show();
//        return false;
//    }else{
//        //  AJAX action
////        $(obj).hide();
//    }
//    return true;
//
//}

/**
 * TODO
 * @param {type} user_name
 * @param {type} user_url
 * @param {type} user_id
 * @param {type} answer_id
 * @returns {String}
 */
function reportDialog(askid, username, uid, divId) {
    $("#" + divId).html('');
    var str = '';
    str = '                            <div class="wrtit">';
    str += '                                <div class="ts_compl"><img src="/ask/v1/images/ts_writ.jpg">';
    str += '                                    <div class="xlerr" onclick="$(\'#' + divId + '\').hide();"><a><img src="/ask/v1/images/xlerr.png"></a></div>';
    str += '                                </div>';
    str += '                            </div>';
    str += '                        <div class="pr18">';
    str += '                                <form onsubmit="return reportMsg(\'' + divId + '\')">';
    str += '                                    <div class="boun1 fix"> <span class="fl">被投诉人：</span>';
    str += '                                        <label class="r_lable"><a href="http://home.9939.com/user/?uid=' + uid + '">' + username + '</a></label>';
    str += '                                    </div>';
    str += '                                    <div class="tstim mT16"> ';
    str += '                                        <span class="fl">投诉时间：</span>';
    str += '                                        <label class="cor999 timer">' + getTimer() + '</label>';
    str += '                                    </div>';
    str += '                                    <div class="tslx mT16"> ';
    str += '                                    <span>投诉类型：</span>';
    str += '                                        <div class="typen mT10 clearfix">';
    str += '                                            <label><input type="radio" name="tousuItem" value="1" >谩骂诽谤</label>';
    str += '                                            <label><input type="radio" name="tousuItem" value="2">色情淫秽</label>';
    str += '                                            <label class="w87"><input type="radio" name="tousuItem" value="3">无意义灌水</label>';
    str += '                                            <label class="w140"><input type="radio" name="tousuItem" value="4">政治敏感性内容</label>';
    str += '                                        </div>';
    str += '                                        <div class="typen mT10 clearfix">';
    str += '                                            <label><input type="radio" name="tousuItem" value="6">暴力犯罪</label>';
    str += '                                            <label><input type="radio" name="tousuItem" value="5">广告</label>';
    str += '                                            <label class="w87"><input type="radio" name="tousuItem" value="7">无满意答案</label>';
    str += '                                            <label class="w140"><input type="radio" name="tousuItem" value="8">医生回答内容肤浅应付</label>';
    str += '                                        </div>';
    str += '                                    </div>';
    str += '                                    <div class="mT17"><span>投诉说明：</span>';
    str += '                                        <textarea class="comthat mT10" name="tousu_content" id="tousu_content" style="width: 372px; height: 48px;" class="css_data_report"></textarea>';
    str += '                                    </div>';
    str += '                                    <input type="hidden" name="type" id="type" value="2" datafield="type" class="css_data_report" />';//1问题 2回答
    str += '                                    <input type="hidden" name="id" id="id" value="' + askid + '"  datafield="type" class="css_data_report" />';//答案id
    str += '                                    <div class="ts_sub mT10">';
    str += '                                        <input type="submit" value="提交" class="bton"/>';
    str += '                                    </div>';
    str += '                                </form>';
    str += '                            </div>';
    $("#" + divId).html(str).show();
}

function reportMsg(divId) {
    var _id = $('#id').val();
    var _content = $('#tousu_content').val();
    var _item = $('input:radio[name="tousuItem"]:checked').val();
    var _type = $('#type').val();

    if (_item == null) {
        alert("请选择投诉类型!");
        return false;
    }

    if (_content == '') {
        alert("请输入投诉内容!");
        $("#tousu_content").focus();
        return false;
    }

    _data = "id=" + _id + "&content=" + _content + "&item=" + _item + "&type=" + _type;
//    var _data = [];
//    _data['id'] = _id;
//    _data['content'] = _content;
//    _data['item'] = _item;
//    _data['type'] = _type;
//    var jsonStr = {};
//    for(var i=0;i<=_data.length;i++){
//        jsonStr[i] = _data[i];
//    }
//    
//    str = JSON.stringify(jsonStr);
//    alert(str);
//    return false;
    $.ajax({
        type: "POST",
        url: "/ask/tousu/",
        data: _data,
        success: function(msg) {
            switch (msg) {
                case 'ok':
                    alert("成功投诉此用户！");
                    $("#" + divId).html('').hide();
                    break;

                case 'nologin':
                    alert("请先登陆！");
                    $("#" + divId).html('').hide();
                    $("#answer_need_login").show();
                    break;

                default:
                    alert(msg);
                    break;
            }
        }
    });

    return false;
}
