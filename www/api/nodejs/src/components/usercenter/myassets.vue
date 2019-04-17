<style scoped>
 @import '../../assets/css/user_center_seller_center01.css';
</style>
<style scoped>
    #myassets .icon {
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
#myassets{
    height:100%;
}
    #myassets .balanceMain{
        width:100%;
        color:#FFF;
        display: table;
        background-color:#fe0100;
        text-align: center;
        margin:  0 auto;
    }
#myassets .balanceMain  .balanceBanner{
width:96%;
margin-top:10px;
}
#myassets .balanceMenu .icon-shangjiantou1:before {
font-size:16px;
content: "\e603";
}
#myassets .balanceMain  .money{
     padding: 10px 0;
     width: 96%;
     margin: 0 auto;
     height: 40px;
    box-sizing:content-box;
     line-height: 40px;
     text-align: right;
     color: #FFF;
     font-size: 32px;
     font-weight: bold;
     text-align: center;
     border-bottom: solid 1px #fc8383;
     width: 96%;
}
#myassets .balanceMain  .tab dd{
    padding: 10px 0;
    float: left;
    width: 33.3%;
    color: #fff;
    font-size: 14px;
    float: left;
}
#myassets .balanceMain  .tab dd em{
    display:block;
    margin:0 auto;
}
#myassets .balanceMain  .tab dd a{
    display: block;
    margin: 0 auto;
    width: 60%;
    border: 1px solid #FFF;
    border-radius: 4px;
    color: #FFF;
}
#myassets .balanceMenu{
    display: table;
    width:100%;
    background-color: #FFF;
}
#myassets .balanceMenu .menuItem.rechargeassets{
background-position: 0 0;

}
#myassets .balanceMenu .menuItem.withdraw{
    background-position: 0 -50px;

}
#myassets .balanceMenu .menuItem .arrow{
    display: inline-block;
}
#myassets .balanceMenu .menuItem{
width:95%;
    margin-left:5px;
    height:50px;
    border-bottom: 1px solid #dcdcdc;
}
#myassets .notice{
padding:10px;
    background:#fff;
    margin-top:10px;
}
#myassets .balanceMenu .menuItemText{
height:50px;
    margin-left:40px;
    font-size:14px;
    width: 70%;
    display: inline-block;
line-height: 50px;
}
#myassets .balanceMenu  .menuItemIcon{
    float: left;
    width: 40px;
    height: 50px;
    background-image: url(/static/img/balanceMenuIcons.png?t=55);
    background-size: 100% auto;
}

</style>
<template>
    <div id="myassets">
        <div class="balanceMain">
                <div class="balanceBanner">
                    <div class="title">我的余额</div>
                </div>
                <div class="money">0.00</div>

            <div class="tab">
                <dd>
                    <span>可用余额:</span><em>0.00</em>
                </dd>
                <dd>
                    <span>冻结金额:</span><em>0.00</em>
                </dd>

                <dd>
                    <a href="javascript:;">余额明细 </a>
                </dd>

            </div>
        </div>
        <div class="balanceMenu">
            <div class="menuItem rechargeassets">
                <a href="javascript:;" target="top">
                    <div class="menuItemIcon"></div>
                    <div class="menuItemText">充值</div>
                    <div class="arrow"><i  class="icon iconfont  icon-shangjiantou1"></i></div>
                </a>
            </div>
            <div class="menuItem withdraw">
                <router-link to="/usercenter/withdraw">
                    <div class="menuItemIcon"></div>
                    <div class="menuItemText">提现</div>
                    <div  class="arrow"><i  class="icon iconfont  icon-shangjiantou1"></i></div>
                </router-link>
            </div>
        </div>
        <div  class="notice">
            <p><strong>特别提醒：</strong></p>
            <p>1.若微信充值超出限额，可选择支付宝渠道进行资金充值。</p>
            <p>2.游客提现到账周期为7个工作日，铜牌会员到账周期为3个工作日，更多到账时间请查看会员权限，发起提现后所提资金为冻结状态，不得进行支付或转移。</p>
            <p>3.若忘记需支付密码，可在客服反馈中申请恢复默认支付密码。</p>
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
                document.title = "我的资产"
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

