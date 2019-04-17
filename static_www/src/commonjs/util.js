import axios from 'axios'
import wx from 'weixin-js-sdk'
import weipaiconfig from '@/../config/dev.env'


import {store} from '@/store/store.js'  // 引入store 对象

import Setwepai from '@/commonjs/set'

//登录微信授权页面
function getToken() {
    var appid = weipaiconfig.weixing.appID;
    var urldomain = window.location.protocol + '//' + window.location.host+'/#/login';
    var redirect_uri = encodeURIComponent(urldomain);
    var scope = "snsapi_base"; //snsapi_userinfo
    // var scope = "snsapi_userinfo"; //snsapi_userinfo
    var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" + appid + "&redirect_uri=" + redirect_uri + "&response_type=code&scope=" + scope + "&state=123&connect_redirect=1#wechat_redirect";
    location.href = url;
    return
}




var weipai = new Object();



// check js
weipai.is_weixn = function () {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == "micromessenger") {
        return true;
    } else {
        return false;
    }
}



// check js
function is_code_state() {
    var code = GetQueryString('code');
    if (code) {
        // var code_flag = store.set('code', code)
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


var md5 = require('md5');



//微信公共调用sdk
if (weipai.is_weixn()) {
    weipai.login=function(next,callback,fromurlrouter) {
        //直接跳转到登录输入手机号码登录 简单明了
        var flagislogin = window.storeWithExpiration.get('token') || '';
        if(!flagislogin){
            var flag =    window.storeWithExpiration.get('fromurlrouter') || '';
            if( flag.length == 0){
                window.storeWithExpiration.set('fromurlrouter',fromurlrouter,50);
                //登录之前存入之前的路由信息
            }
            getToken();
        }else{
            var flag =    window.storeWithExpiration.get('fromurlrouter')||'';
            window.storeWithExpiration.set('fromurlrouter','',0);
            callback();
            if(!flag){
                next()
            }else{
                try{
                    var jsonstr =  JSON.parse(flag);
                    next(jsonstr.path);
                }catch(e){
                    next()
                }
            }
        }
        return true;
    }

    //到后端获取用户信息
        weipai.getLogin =function (code) {
            axios.defaults.baseURL = weipaiconfig.default_domain_api;
            //  var params = new URLSearchParams();
            // params.append('code', code);
            axios.get('user/getAccess_token?code=' + code + '&state=123').then(function (json) {
                return json.data;
            }).then(function (json) {
                if (json.statue_code == '2000') {
                    if (json.user) {
                        window.storeWithExpiration.set('token', json.token, 1);
                       // next(to)
                        //跳转到网站首页
                       window.location.href=weipaiconfig.default_domain;
                    }
                }
            }).catch(function (error) {
                alert(error)
            });
            return;
        }


    weipai.commonsharejs = function (title, linkgurl, img, content) {
        // title = config.build.env.FARENAME + title + config.build.env.SALING;
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
            title: title, // 分享标题  onMenuShareAppMessage
            desc: content, // 分享描述
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
            desc: content, // 分享描述
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
            desc: content, // 分享描述
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
            desc: content, // 分享描述
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
}else{
    weipai.login=function(next,callback,fromurlrouter){
        //直接跳转到登录输入手机号码登录 简单明了
        next();
    }

    // weipai.getLogin = function(code){
    //         alert("pc没有开放");
    // }
}

    weipai.isEmptyObject = function (obj) {
        var name;
        for (name in obj) {
            return false;
        }
        return true;
    }

// check js
weipai.is_code_state=function() {
    var code = GetQueryString('code') || '';
    if (code.replace(/(^s*)|(s*$)/g, "").length > 0) {
        var code_flag =    window.storeWithExpiration.set('code', code, 1);
        return code;
    } else {
        return '';
    }
}


weipai.GetQueryString=function(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return (r[2]);
    return null;
}

weipai.checkmobile=function(mobile) {
    var reg = new RegExp(/^1[\d]{10}$/, "i");
    return  reg.test(mobile);
}

weipai.Util = {
// arrayMax: 返回数组中的最大值
    // 将Math.max()与扩展运算符 (...) 结合使用以获取数组中的最大值。
    arrayMax: arr => Math.max(...arr),



    /************************************************************************
     * 字符串类
     ************************************************************************/

    // 清除字符串左侧或右侧的任意空格
    trim: function(str){
        var str = str || '';
        return str.replace(/^\s+|\s+$/g, "") || "";
    },

    // 清除左空格
    ltrim: function(str){
        return str.replace(/^\s+/, "");
    },

    // 清除右空格
    rtrim: function(val){
        return val.replace(/\s+$/, "");
    },

    // 截取给定长度的字符串
    truncate: function(str, len){
        if(str.length >len){
            str = str.substring(0, len);
        }
        return str;
    },

    // 只返回字符串a-z字符
    onlyLetters: function(str){
        return str.toLowerCase().replace(/[^a-z]/g, "");
    },

    // 只返回字符串中a-z和数字
    onlyLettersNums: function(str){
        return str.toLowerCase().replace(/[^a-z,0-9]/g, "");
    },

    // anagrams: 返回字符串的所有异序字符串
    // 使用递归。对于给定字符串中的每个字母, 为其其余字母创建所有部分字谜。使用Array.map()将字母与每个部分变位词组合在一起, 然后将Array.reduce()组合在一个数组中的所有字谜。基本情况为字符串length等于2或1
    anagrams: str => {
        _anagrams = _str => {
            if(_str.length <= 2){
                return _str.length === 2 ? [_str, _str[1] + _str[0]] : [_str];
            }
            return _str.split("").reduce((acc, letter, i) => acc.concat(_anagrams(_str.slice(0, i) + _str.slice(i + 1)).map(val => letter + val)), []);
        }
        s = str;
        return _anagrams(s);
    },

    // capitalize: 将字符串的第一个字母大写
    // 使用 destructuring 和toUpperCase()可将第一个字母、...rest用于获取第一个字母之后的字符数组, 然后是Array.join('')以使其成为字符串。省略lowerRest参数以保持字符串的其余部分不变, 或将其设置为true以转换为小写
    capitalize: ([first, ...rest], lowerRest = false) => first.toUpperCase() + (lowerRest ? rest.join("").toLowerCase() : rest.join("")),

    // capitalizeEveryWord: 将字符串中每个单词的首字母大写
    // 使用replace()匹配每个单词和toUpperCase()的第一个字符以将其大写
    capitalizeEveryWord: str => str.replace(/\b[a-z]/g, char => char.toUpperCase()),

    // escapeRegExp: 转义要在正则表达式中使用的字符串
    // 使用replace()可转义特殊字符
    escapeRegExp: str => str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"),

    // fromCamelCase: 从驼峰表示法转换为字符串形式
    // 使用replace()可删除下划线、连字符和空格, 并将单词转换为匹配。省略第二个参数以使用默认分隔符_
    fromCamelCase: (str, separator = "_") => str.replace(/([a-z\d])([A-Z])/g, '$1' + separator + '$2').replace(/([A-Z]+)([A-Z][a-z\d]+)/g, '$1' + separator + '$2').toLowerCase(),

    // reverseString: 反转字符串
    // 使用数组 destructuring 和Array.reverse()可反转字符串中字符的顺序。使用join('')组合字符以获取字符串
    reverseString: str => [...str].reverse().join(""),

    // sortCharactersInString: 按字母顺序对字符串中的字符进行排序
    // 使用split('')、Array.sort()利用localeCompare()重新组合使用join('').
    sortCharactersInString: str => str.split("").sort((a, b) => a.localeCompare(b)).join(""),

    // toCamelCase: 字符串转换为驼峰模式
    // 使用replace()可删除下划线、连字符和空格, 并将单词转换为驼峰模式
    toCamelCase: str => str.replace(/^([A-Z])|[\s-_]+(\w)/g, (match, p1, p2, offset) => p2 ? p2.toUpperCase() : p1.toLowerCase()),

    // truncateString: 将字符串截断为指定长度
    // 确定字符串的length是否大于num。将截断的字符串返回到所需的长度, 并将...追加到末尾或原始字符串
    truncateString: (str, num) => str.length > num ? str.slice(0, num > 3 ? num -3 : num) + "..." : str,

    currentURL: () => window.location.href,




}

var env = require('@/../config/dev.env');

var Base64 = require('js-base64').Base64;


var   $waterurl ='logo/weipai.png?x-oss-process=image/resize,P_20';
$waterurl =  Base64.encode($waterurl);
$waterurl = $waterurl.replace(/\+/, "-");
$waterurl = $waterurl.replace(/\//, "_");
$waterurl = weipai.Util.rtrim($waterurl);
//获取图片真实的路径

/**
 * 获取带水印的图片 直接使用非授权的
 * http://bucket.<endpoint>/object?x-oss-process=image/action,parame_value/action,parame_value/...
 * @param OBJ $request 参数解释
 * @return  json
 */
weipai.getCurlofimgUsenoAuth=function($objectname,$width,$height,$waterflag){
    try {
        if (typeof($width) == "undefined") {
            //$width =600;  //600-800  Math.random()*(max - min) + min
            $width = Math.ceil(Math.random()*200) + 600;
        }
        if (typeof($height) == "undefined") {
            $height=600;
        }
        if (typeof($waterflag) == "undefined") {
            $waterflag=false;
        }
        if($waterflag==true){
            var   $waterurl_t ='image/resize,m_fill,h_'+$width+',w_'+$height+'/sharpen,100/watermark,image_'+$waterurl+',t_90,g_se,x_10,y_35,color_808080,image/bright,10';
            var   $thhpimgurl = env.cdntianbao+'/'+$objectname+'?x-oss-process='+$waterurl_t;
        }else{
            var   $waterurl_t ='image/resize,m_fill,h_'+$width+',w_'+$height+'/sharpen,100,t_90,g_se,x_10,y_35,color_808080,image/bright,10';
            var   $thhpimgurl = env.cdntianbao+'/'+$objectname+'?x-oss-process='+$waterurl_t;
        }
        return  $thhpimgurl;
    } catch(e){
        console.log(e)
        return  '';
    }
}



//预览图片
weipai.priviewimges=function(data,e,goods_id){
     var currenturl = e.target.style.backgroundImage ||  e.target.src;

    if(currenturl.length>5){
        //currenturl =currenturl.slice(5,currenturl.length-1);
        let target =e.target;
        let targetindex =e.target.getAttribute('data-noimg');
        //var index =   $(target).index();
        var dataimgs =[];
        var curlimg = '';
        for(var i=0;i<data.length;i++){
            curlimg = weipai.getCurlofimgUsenoAuth((data[i]).img,1200,1200,true);
            if(i==targetindex){
                currenturl =curlimg;
            }
            dataimgs.push(curlimg);
        }
        wx &&  wx.previewImage({
            current: currenturl, // 当前显示图片的http链接
            urls:dataimgs // 需要预览的图片http链接列表
        });


        console.log(wx)
        //点击增加浏览
        var dd =new FormData();
        var _that=this;
        dd.append('token',storeWithExpiration.get('token'))
        dd.append('goods_id',goods_id)
        axios.post('/product/addclick',dd)
            .then(function (response) {
                if(response.status==200){
                    return response.data;
                }
            }).then(function(json) {
        }).catch(function (error) {


        })
    }
}


export default  weipai;