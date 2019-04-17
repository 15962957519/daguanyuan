<template>
    <div id="app">
        <div class="es-section login-section">
            <div class="morea banner">
                <div class="m-section1-h3">
                    <a class="a1"><img src="@/assets/images/login/nav.png"/></a><a class="a2">立即注册</a>
                </div>
                <div class="qt">
                    <img src="@/assets/images/login/tz.png"/>
                </div>
            </div>
            <div class="login-main js-login-main">
                <div class="form-group mbl">
                    <div class="controls">
                        <img src="@/assets/images/login/per.png"><input class="" id="login_username" type="text"
                                                                        placeholder="账号/邮箱/手机号"><a class="a4"><img
                            src="@/assets/images/login/jt.png"></a>
                    </div>
                </div>
                <div class="form-group mbl">
                    <div class="controls">
                        <img src="@/assets/images/login/suo.png"><input class="" id="login_username" type="text" name=""
                                                                        placeholder="请输入密码">
                        <a class="a1">获取验证码</a>
                    </div>
                </div>
                <div class="mbl">
                    <a class="mm" href="">忘记密码</a>
                </div>
                <div class="mb2">
                    <img src="@/assets/images/login/dl.jpg">
                </div>
            </div>

            <div class="link">
                <h4>第三方快捷登录</h4>
                <div class="tb">
                    <a><img src="@/assets/images/login/wx.png"></a>
                    <a><img src="@/assets/images/login/qq.png"></a>
                    <a><img src="@/assets/images/login/xl.png"></a>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
    a {
        text-decoration: none;
    }

    @media (min-width: 750px) {
        .login-section {
            width: 750px !important;
            margin: 0 auto !important;
        }
    }

    @media (min-width: 750px) {
        .mb2 {
            line-height: 54px;
        }
    }

    .login-section {
        width: 100%;
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
    }

    .banner {
        background: url(~@/assets/images/login/banner.jpg) no-repeat;
        background-size: 100% auto;
        height: 5.5rem;
    }

    .morea {
        padding-top: .8rem;
    }

    .m-section1-h3 {
        font-size: 15px;
        color: #ff862d;
        padding: 5px 10px;
    }

    .qt {
        margin: 0 auto;
        width: 27%;
        padding-top: .7rem;
    }

    .controls input {
        border: none;
        width: 57%;
        outline: none;
        font-size: .3rem;
        padding-left: .1rem;
        display: inline-block;
        float: left;
        padding-top: .2rem
    }

    input::placeholder {
        color: #c7c7c7;
        font-size: .33rem;
    }

    .controls {
        border-bottom: .01rem solid #c8c9c9;
        position: relative;
        height: 1rem;
        padding-top: .1rem;
        padding-left: .1rem;
    }

    .controls a {
        float: right;
        height: .4rem;
        color: #c7c7c7;
        font-size: .24rem;
        vertical-align: middle;
    }

    .controls .a1 {
        display: inline-block;
        width: 1.5rem;
        border-radius: .15rem;
        float: right;
        border: 0.02rem solid #6572fd;
        font-size: .12rem;
        text-align: center;
        height: .4rem;
        line-height: .3rem;
        position: absolute;
        top: .25rem;
        right: .04rem;
        color: #6572fd;
        padding-top: .05rem;
    }

    .controls img {
        display: inline-block;
        width: .51rem;
        height: .62rem;
        float: left;
    }

    .controls .a4 {
        padding-top: .15rem;
    }

    .controls .a4 img {
        width: .32rem;
        height: .42rem;
    }

    .qt img {
        width: 100%;

    }

    .m-section1-h3 .a1 {

        display: inline-block;
        font-weight: normal;
        padding-left: .33rem;
        float: left;
    }

    .a1 img {
        display: inline-block;
        width: .3rem;
        height: .4rem;
    }

    .m-section1-h3 .a2 {
        float: right;
        color: #eff4f5;
        font-size: .33rem;
        display: inline-block;

        padding-right: .2rem;
        font-weight: normal;
    }

    .mbl {
        margin-bottom: .2rem !important;
    }

    .mbl a {
        float: right;
        color: #c2c3c4;
        font-size: .24rem;
    }
    .mm{
        padding-right: .14rem;

    }


    .mb2 img {
        width: 90%;
        padding: .9rem 0 0 .3rem;
        display: inline-block;
    }

    .link {
        width: 100%;
        margin: 0.2rem auto 0;
    }



    .link h4 {
        text-align: center;
        color: #c2c3c4;
        font-size: .24rem;
        margin: 0;
        font-weight: normal;
    }

    .tb {
        padding-left: 1.5rem;
    }

    .tb a img {
        width: 1.62rem;
        height: 1.70rem;
        float: left;
        padding-top: .5rem;
    }

    .tb a img:nth-child(1) {
        padding-left: .6rem;
    }

    .fa-wechat:before, .fa-weixin:before {
        content: "\f1d7";
    }
</style>

<script>
    fnResize()
    window.onresize = function () {
        fnResize()
    }
    function fnResize() {
        var deviceWidth = document.documentElement.clientWidth || window.innerWidth
        if (deviceWidth >= 750) {
            deviceWidth = 750
        }
        if (deviceWidth <= 320) {
            deviceWidth = 320
        }
        document.documentElement.style.fontSize = (deviceWidth / 7.5) + 'px'
    }
</script>