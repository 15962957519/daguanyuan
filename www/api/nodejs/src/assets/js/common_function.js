/**
 * Created by ericssonon on 2016/12/22.
 */

// 验证手机号
export function isPhoneNo(phone) {
    var pattern = /^1[34578]\d{9}$/;
    return pattern.test(phone);
}

export function isArray(obj) {
    return Object.prototype.toString.call(obj) === "[object Array]"
}

// Checks if an object has a property.
export function has(obj, propName) {
    return Object.prototype.hasOwnProperty.call(obj, propName)
}

export function rmoney(s) {
    s = s.toString();
    return parseFloat(s.replace(/[^\d\.-]/g, ""));
}

var config = require('../../../config')

export function userLikeProduct_function(good_id, token, e) {

    return data;
}

export function isEmptyObject(e) {
    var t;
    for (t in e)
        return !1;
    return !0
}

export function getuserinfo(obj) {
    var that = this;
    axios.get(decodeURIComponent(config.dev.env.default_domain_api) + '/product/getuserall', {
        params: {
            token: storeWithExpiration.get('token'),
            u_id: obj.u_id
        }
    }).then(function (response) {
        if (response.status == '200') {
            return response.data;
        }
    }).then(function (json) {
        if (typeof(json.user_info) != 'undefined') {
            obj.product = json.product;
            obj.userinfo = json.user_info;
        }
    }).catch(function (ex) {
        console.log(ex);
    });

}
export function getuserfocus(obj) {
    var that = this;
    axios.get(decodeURIComponent(config.dev.env.default_domain_api) + '/user/userinfofocusall', {
        params: {
            token: storeWithExpiration.get('token'),
            page: 1
        }
    }).then(function (response) {
        if (response.status == '200') {
            return response.data;
        }
    }).then(function (json) {
        if (typeof(json.data.data) != 'undefined') {
            obj.lists = json.data.data;
        }
    }).catch(function (ex) {
        console.log(ex);
    });
}

export function weixinlogin(a, b, ne) {
    //先检查是存否在token
    var flagislogin = window.storeWithExpiration.get('token') || '';
    if (!flagislogin && typeof flagislogin != "undefined" && flagislogin != 0 || flagislogin == null || '' == flagislogin || flagislogin.replace(/(^s*)|(s*$)/g, "").length == 0) {
        var code = '';
        code = is_code_state();
        if (code.replace(/(^s*)|(s*$)/g, "").length > 0) {
            //api到后端取数据
            var state = GetQueryString('state');
            //  var code_flag = store.get('code')
            var userinfo = getLogin(code, ne, a, b, state)
            return false;
        }
        window.storeWithExpiration.set('urkl', a);
        var appid = "wxdabbc66245346dda";
        var redirect_uri = encodeURIComponent(urldomain);
        var scope = "snsapi_base";//snsapi_userinfo
        var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" + appid + "&redirect_uri=" + redirect_uri + "&response_type=code&scope=" + scope + "&state=123&connect_redirect=1#wechat_redirect";
        location.href = url;
        return false;
    }
    //解析json
    //  WEIPAT.userinfo.nickname =json.userong.nickname,
        WEIPAT.userinfo.isSubscribe = 1,
        WEIPAT.userinfo.isLogin = 1,
        WEIPAT.userinfo.token = flagislogin;
    return false;
}

// check js
export function is_weixn() {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == "micromessenger") {
        return true;
    } else {
        return false;
    }
}
// check js
export function is_code_state() {
    var code = GetQueryString('code') || '';
    if (code.replace(/(^s*)|(s*$)/g, "").length > 0) {
        var code_flag = store.set('code', code)
        return code;
    } else {
        return '';
    }
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return (r[2]);
    return null;
}

//到后端获取用户信息
function getLogin(code, next, to, from, state) {
    var dddtmpurl = '';
    var patt = '';
    axios.get('/user/getAccess_token?code=' + code + '&state=' + state)
        .then(function (json) {
            return json.data;
        }).then(function (json) {
        if (json.statue_code == '2000') {
            if (typeof json.user != undefined) {
                WEIPAT.userinfo.nickname = json.userong.nickname,
                WEIPAT.userinfo.isSubscribe = json.userong.subscribe,
                WEIPAT.userinfo.isLogin = 1,
                WEIPAT.userinfo.headimgurl = json.userong.head_pic;
                WEIPAT.userinfo.token = json.token;
                window.storeWithExpiration.set('token', json.token, 3600000);
                //  next();
                dddtmpurl = window.storeWithExpiration.get('urkl') || '';
                window.storeWithExpiration.set('urkl', '');
                //http://w.tianbaoweipai.com/?code=031KoaVk1lDPok0nFVSk1EMsVk1KoaVp&state=123
                patt = /\?code=(.*)&state=(.*)/;
                if (patt.test(dddtmpurl)) {
                    //   next('/');
                    window.location.replace(windows.default_domain_web)
                    window.location.href = '';
                    return false;
                } else {
                   // next(dddtmpurl);
                    var stateObject = {};
                    var title = "天宝微拍";
                    var newUrl = "/";
                  //  history.pushState(stateObject,title,newUrl);
                    next(dddtmpurl);
                    return false;
                }
            }
        } else if (json.statue_code == '4006') {
            //用户没有关注，让用户关注
            var appid = "wxdabbc66245346dda";
            var redirect_uri = encodeURIComponent(urldomain);
            var scope = "snsapi_userinfo";//snsapi_userinfo
            var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" + appid + "&redirect_uri=" + redirect_uri + "&response_type=code&scope=" + scope + "&state=snsapi_userinfo&connect_redirect=1#wechat_redirect";
            setTimeout("window.location=('" + url + "')", 300);
            //  location.href = url;
            return false;
            window.opener = null;
            window.open('', '_self');
            window.close();
            return false;
        }
        window.storeWithExpiration.set('token', '');
        dddtmpurl = window.storeWithExpiration.get('urkl') || '';
        patt = /\?code=(.*)&state=(.*)/;
        if (patt.test(dddtmpurl)) {
            next('/');
            return false;
        } else {
            window.location.href = "/";
        }
        return false;
    }).catch(function (error) {
        alert(error)
    });
    return false;

}

function urlrefer() {
    var ref = '';
    if (document.documentURI.length > 0) {
        ref = document.documentURI;
    }
    return ref;
}


export function commonsharejs(title, linkgurl, img) {
    title =  config.build.env.FARENAME + title + config.build.env.SALING;

    console.log(title)
    wx.onMenuShareTimeline({
        title: title,
        link: linkgurl,
        imgUrl: img,
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    })
    wx.onMenuShareAppMessage({
        title: title, // 分享标题
        desc: title, // 分享描述
        link: linkgurl, // 分享链接
        imgUrl: img, // 分享图标
        type: 'link', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareQQ({
        title: title, // 分享标题
        desc: title, // 分享描述
        link: linkgurl, // 分享链接
        imgUrl: img, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });

    wx.onMenuShareWeibo({
        title: title, // 分享标题
        desc: title, // 分享描述
        link: linkgurl, // 分享链接
        imgUrl: img, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareQZone({
        title: title, // 分享标题
        desc: title, // 分享描述
        link: linkgurl, // 分享链接
        imgUrl: img, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
}


export function weixincommonjsdk() {

    if ($.os.android) {
        var domain = location.href.split('#')[0];
        axios.get('/makesign?url=' + encodeURIComponent(domain)).then(function (response) {
            window.wx && wx.config({
                debug: false,
                appId: "wxdabbc66245346dda",
                timestamp: String(response.data.timestamp),
                nonceStr: response.data.noncestr,
                signature: response.data.signature,
                jsApiList: [
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'onMenuShareQQ',
                    'showAllNonBaseMenuItem',
                    'onMenuShareWeibo',
                    'onMenuShareQZone',
                    "showOptionMenu",
                    "closeWindow",
                    "getLocation",
                    "previewImage",
                    "openAddress"
                ]
            });

            wx.ready(function () {
                wx.checkJsApi({
                    jsApiList: ['getLocation', 'onMenuShareTimeline', 'previewImage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                    success: function (res) {
                        // 以键值对的形式返回，可用的api值true，不可用为false

                    }
                });
            });
        }).then(function () {

        }).catch(function (error) {
            console.log(error);
        });
    }
}

export function weixin() {
    var that = this;
    // Optionally the request above could also be done as
    axios.get('/makesign', {
        params: {
            url: encodeURIComponent(location.href.split('#')[0])
        }
    }).then(function (response) {
        window.wx && wx.config({
            debug: false,
            appId: "wxdabbc66245346dda",
            timestamp: String(response.data.timestamp),
            nonceStr: response.data.noncestr,
            signature: response.data.signature,
            jsApiList: [
                'chooseImage',
                'onMenuShareTimeline',
                'showAllNonBaseMenuItem',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                "showOptionMenu",
                "closeWindow",
                "uploadImage",
                "previewImage"
            ]
        });
    }).then(function () {
        //   that.uploadflag=false;
    }).catch(function (error) {
        console.log(error);
    });

}

