import wx from 'weixin-js-sdk'

import axios from 'axios'
var  wxmakething = new Object();

wxmakething.getwxmakeings = function() {
    var domain = location.href.split('#')[0];
    axios.get('/makesign?url=' + encodeURIComponent(domain)).then(function (response) {
        if (response.status == '200') {
            //console.log(response)
            return response;
        }
    }).then(function (response) {

        if (response) {
            //console.log(response)
            wx.config({
                debug: false,
                appId: response.data.appid,
                timestamp: String(response.data.timestamp),
                nonceStr: response.data.noncestr,
                signature: response.data.signature,
                jsApiList: [
                    "chooseImage",
                    "uploadImage",
                    "previewImage",
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'onMenuShareQQ',
                    'onMenuShareWeibo',
                    'onMenuShareQZone',
                    "showOptionMenu",
                    "closeWindow",
                    "getNetworkType",
                    "editAddress",
                ]
            });
            wx.ready(function () {
                wx.checkJsApi({
                    jsApiList: [
                        'getNetworkType',
                        'uploadImage',
                        'chooseImage',
                        'previewImage',
                        'editAddress',
                        'getLatestAddress',

                    ],
                    success: function (res) {

                    }
                });
                wx.error(function (res) {

                })
            });
        } else {
            alert("服务器响应异常！")
        }
    }).catch(function (error) {
    });
}


export default  wxmakething;