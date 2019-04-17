<style scoped>
 @import '../../assets/css/user_center_seller_center01.css';
</style>
<style scoped>
    #myassets_draw .icon {
        position:relative;
        min-width: 1em;
        font-size: 30px;
        -webkit-transition: font-size 0.25s ease-out 0s;
        -moz-transition: font-size 0.25s ease-out 0s;
        transition: font-size 0.25s ease-out 0s;
    }
@font-face {
    font-family: "usercenter";
    src: url('//w.tianbaoweipai.com/static/font/usercenter.eot?t=1484042365502'); /* IE9*/
    src: url('//w.tianbaoweipai.com/static/font/usercenter.eot?t=1484042365502#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('//w.tianbaoweipai.com/static/font/usercenter.woff?t=1484042365502') format('woff'), /* chrome, firefox */ url('//w.tianbaoweipai.com/static/font/usercenter.ttf?t=1484042365502') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/ url('//w.tianbaoweipai.com/static/font/usercenter.svg?t=1484042365502#usercenter') format('svg'); /* iOS 4.1- */
}


.iconfont {
    font-family: "usercenter";
    font-size: 32px;
    color: inherit;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
#myassets_draw{
    width:100%;
    display: table;
}
#myassets_draw  .withdrawMain{
    width:100%;

}
    #myassets_draw .withdrawMain .tip{
    width: 94%;
    margin: 0 auto;
    height: 24px;
    line-height: 24px;
    font-size: 12px;
    color: #BFBEC4;
    text-align: left;
}
    #myassets_draw .withdrawMain button{
        width: 94%;
        margin: 0 3%;
        border: none;
        border-radius: 4px;
        height: 40px;
        line-height: 40px;
        font-size: 18px;
        color: #FFF;
        margin-top: 20px;
        outline: none;

    }
    #myassets_draw .withdrawMain .notReady{
        background-color: #0BB20C;

    }
#myassets_draw .withdrawItem {
    height: 48px;
    background-color: #FFF;
    line-height: 48px;
    font-size: 14px;
}
    #myassets_draw .withdrawItem .title {
    float:left;
        width:30%;
        text-align: center;
}  #myassets_draw .withdrawItem .numInput {
           float: left;
           width: 70%;
           height: 48px;
           color: #CCC;
           box-sizing: border-box;
}
#myassets_draw  .withdrawMain:before{
    content: "";  /*:before和:after必带技能，重要性为满5颗星*/
    display: block;
    width:0;
    height:20px;
}
</style>
<template>
    <div id="myassets_draw">
        <div class="withdrawMain">
            <div class="withdrawItem">
                <div class="title">提现金额：</div>
                <div class="numInput money" data-restcount="1">
                    <span placeholder="请输入提现金额">请输入提现金额</span>
                </div>
            </div>
            <div class="tip">本次提现最大金额：￥0.00，当前还可提现： 1次</div>
            <button class="nextBtn wx notReady">微信提现</button>
        </div>
    </div>
</template>
<script>
import { MessageBox,Navbar, TabItem,Indicator, Actionsheet ,Toast } from 'mint-ui';
import { getuserfocus ,isEmptyObject } from '../../assets/js/common_function.js';
import {mapState } from 'vuex';
var config = require('../../../config')
    export default {
        data(){
            return {
                userinfo:[]
            }
        },
          watch: {
            // 如果 question 发生改变，这个函数就会运行
          },
        mounted: function() {
                document.title = "提现到微信"
            },
            methods:{
                assets(e){
                    var	that =this;
                    axios.get('/user/userinfoall', {
                        params: {
                            token: storeWithExpiration.get('token')
                        }
                    })
                        .then(function(response) {

                            if(response.status=='200'){
                                return response.data;
                            }
                        }).then(function(json) {
                        that.userinfo = json.data.result;
                    }).catch(function(ex) {
                        console.log(ex);
                    });
                }
            },
             computed: mapState({

                  })
    }
</script>

