//公共函数测试1


function fixSupport() {
    if ($.os.android) {
        var bodyWidth = document.body.clientWidth;
        var bodyHeight = document.documentElement.clientHeight;
    } else {
        bodyWidth = $(window).width();
        bodyHeight = $(window).height();
    }
    var dHeight = document.documentElement.offsetHeight;
    if (dHeight <= bodyHeight) {
        $('#contentbox').css('minHeight', bodyHeight - 60 - 0);
    }
    $(".supportBanner").css("visibility", "visible");
}
$(function () {
    fixSupport();

    $(document).off("wptSupport_view:fix").on("wptSupport_view:fix", function () {
        fixSupport();
    });
});


//自定义函数
//跳转
function wptRedirect(url, time) {
    time = typeof time == 'undefined' ? ($.os.android ? 200 : 0) : time;

    if (time > 0) {
        setTimeout(function () {
            location.href = url;
        }, time);
    } else {
        location.href = url;
    }
}


