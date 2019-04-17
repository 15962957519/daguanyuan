<style scoped>
 @import '../../assets/css/user_center_seller_iconfont.css'
</style>
<style scoped>
#myaddress .verifyBox{
    width: 100%;
    display: table;
}
#myaddress .verifyBox.operator{
    background: #FFF;
}
.verifyBox .buttonBanner button{
    border: 0;
    width: 90%;
    color: #FFF;
    height: 42px;
    font-size: 16px;
    line-height: 42px;
    text-align: center;
    border-radius: 3px;
    margin: 5px 5% 15px 5%;
    background-color: #06BC07;
}
#myaddress .verifyBox .infoItem {
    position: relative;
    height: 28px;
    width: 96%;
    margin-left: 5%;
    font-size: 16px;
    line-height: 28px;
    padding: 10px 1% 10px 0;
    box-sizing:content-box;
}

#myaddress .verifyBox .infoItem .liHead {
    width: 27%;
    float: left;
}

#myaddress .verifyBox .infoItem .liContent {
    width: 72%;
    float: left;
}

#myaddress .verifyBox .infoItem .liFoot {
    width: auto;
    overflow: hidden;
    display: inline-block;
    height: 28px;
    float: left;
}
#myaddress .verifyBox .infoItem .liFoot.checkcode{
    width: auto;
    border-radius: 3px;
    color: #fff;
    font-weight: 500;
    margin-right: 8%;
    font-size: 16px;
    text-align: center;
    background: #fe0100;
    float: right;
}


#myaddress .verifyBox .infoItem .liFoot.error:after {
    content: '';
    width: 100%;
    height: 100%;
    float: right;
    background-image: url("/res/img/verifyWarnIcon.png");
    background-repeat: no-repeat;
    background-size: 15px;
    background-position: center center;
}

#myaddress .verifyBox .infoItem .liContent .input,
#myaddress .verifyBox .infoItem .liContent .numInput {
    border: 0;
    font-size: 14px;
    height: 28px;
    float: left;
    width: 55%;
}
#myaddress .verifyBox .infoItem .liContent .input.numinputcode {
    width: 45%;
}

#myaddress .verifyBox .infoItem .liContent .numInput span {
    line-height: 28px;
    height: 28px;
    color: #A9A9A9;
}

#myaddress .verifyBox .infoItem .liContent .numInput span.hover {
    border-right: 2px solid red;
}

#myaddress.verifyBox .infoItem .liContent .numInput span.hasValue {
    color: #000;
}

#myaddress .verifyBox .tips {
    padding: 5px 5% 10px 5%;
    color: #939393;
    background-color: #EEE;
    line-height: 20px;
    font-size: 12px;
    text-align: right;
}

#myaddress .verifyBox .tips.verified {
    color: #169ADA;
}

#myaddress .verifyBox .tips.verifyError {
    color: #F94A45;
}

#myaddress .verifyBox .tips.confirm {
    position: relative;
    width: 90%;
    height: 1px;
    padding: 0;
    border: 0;
    margin: 25px 5%;
    background-color: #AAA;
}

#myaddress .verifyBox .tips.confirm div {
    position: absolute;
    width: 48%;
    left: 26%;
    margin-top: -6px;
    font-size: 12px;
    line-height: 12px;
    text-align: center;
    background-color: #EEE;
}
#myaddress  .icon-shangjiantou1.fi-size{
    font-size:16px;
}
#myaddress  .fi-size{
    font-size:16px !important;
}
#myaddress .icon-shangjiantou1:before {
    content: "\e603";
}
.addresslist span{
width:50%;
display:inline-block;
line-height:26px;
}
#myaddress {
color:#808080;
background-color:#fff;
padding-bottom:10px;
padding:0 2% 2% 2%;
}
#myaddress  .lists{
height:auto;
}
#myaddress  .lists li{
line-height:32px;
padding:0 2%;
position:relative;
}

#myaddress .addsynax{
width:32px;
height:32px;
float:left;
margin-right:10px;
background-image:url(../../assets/img/address.png);
 background-size:100%;
 background-repeat :no-repeat ;
}
#myaddress  .addnewaddress{
width:100%;
height:32px;
margin-top:10px;
line-height:32px;
font-size:14px;
color:#ffcc99;
position:relative;
}
#myaddress  .addnewaddress .arrow{
    position: absolute;
    right: 0;
    height:32px;
    line-height: 32px;
    color: #c8c8c8;
    top: 0;
    bottom: 0;
}

#myaddress  .icon-shangjiantou1.fi-size{
    font-size:16px;
}
#myaddress  .fi-size{
    font-size:16px !important;
}
#myaddress .icon-shangjiantou1:before {
    content: "\e603";
}
</style>
<template>
    <div id="myaddress">
                <div class="lists">
                    <ul>
                        <li class="border horizonBottom">
                          <div class="addresslist"><span>李启鹏</span><span>李启鹏</span></div>
                          <div>详细地址</div>
                        </li>
                    </ul>


                </div>
                <div class="addnewaddress"><div class="addsynax "></div><span>新增加收货地址</span>
                        <div class="arrow">
                            <i class="icon iconfont  icon-shangjiantou1 fi-size fi-stack"></i>
                         </div>
                    </div>


                <copyright></copyright>
    </div>
</template>
<script>
var config = require('../../../config')
import {MessageBox ,Switch,Indicator,Toast } from 'mint-ui';
var copyright  = require('../copyright.vue');
    export default {
        data(){
            return {
                 lists: []
            }
        },
          components:{
            copyright
            },
        mounted: function() {
                document.title = "我的地址"
                var obj3 = new Object();

            },
            methods:{

            },
            computed:{

            }
    }
</script>

