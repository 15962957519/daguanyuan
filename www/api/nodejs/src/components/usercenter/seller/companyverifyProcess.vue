<template>
<div class="verifyProcessMain">
        <div class="title">企业实名认证流程</div>
        <div class="verifyContent">
            <div class="process">
                                    <div class="step">
                        <div class="num">1                                                                <div class="pass" style="background-image: none"></div>
                                                            </div>
                        <div class="stepContent">填写真实姓名、联系电话、身份证号并上传身份证正面、反面和手持身份证的清晰照。</div>
                    </div>
                                    <!--<div class="step">-->
                        <!--<div class="num">2                                                                <div class="pass" style="background-image: none"></div>-->
                                                            <!--</div>-->
                        <!--<div class="stepContent">设置店铺名称、店铺Logo、及店铺介绍。</div>-->
                    <!--</div>-->
                                    <div class="step">
                        <div class="num">2                                                        <div class="pass" style="background-image: none"></div>
                                                    </div>
                        <div class="stepContent">提交实名认证申请后，工作人员将在3个工作日内与您联系核对信息。</div>
                    </div>
                            </div>
        </div>
        <div class="button">
              <router-link to="/usersellindex/company_individual">
                <button>我要认证</button>
             </router-link>
            <div class="agreement"> 点击即表示同意：<router-link to="/usersellindex/user_privacy">《实名认证服务协议》</router-link>  </div>
        </div>
    </div>
</template>
<script>
    var config = require('../../../../config')
    export default {
        mounted: function() {
            },
            methods:{

            },
            computed:{

            }
    }
</script>
<style scoped>
 @import '../../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
 .verifyProcessMain .title {
        width: 100%;
        font-size: 20px;
        line-height: 80px;
        text-align: center;
        color: #6A6A6A;
    }

    .verifyProcessMain .verifyContent {
        display: table;
        width: 100%;
        height: auto;
        font-size: 13px;
        color: #888888;
    }

    .verifyProcessMain .verifyContent .process {
        width: 88%;
        margin: 15px 4% 0 8%;
    }

    .verifyProcessMain .verifyContent .process .step {
        position: relative;
        height: 80px;
        line-height: 20px;
        padding-left: 25px;
        border-left: 2px solid #169ADA;
    }

    .verifyProcessMain .verifyContent .process .step:last-child {
        border-left: 2px solid #EFEFF4;
    }

    .verifyProcessMain .verifyContent .process .step .num {
        position: absolute;
        width: 32px;
        height: 32px;
        left: -17px;
        top: -7px;
        border-radius: 16px;
        background-color: #169ADA;
        font-size: 17px;
        color: #FFFFFF;
        line-height: 32px;
        text-align: center;

    }

    .verifyProcessMain .verifyContent .process .step .num .pass {
        width: 18px;
        height: 18px;
        margin-left: 23px;
        margin-top: -17px;
        background-repeat: no-repeat;
        background-size: 18px;
    }

    .verifyProcessMain.re .verifyContent .process .step .num .pass {
        display: block;
    }

    .verifyProcessMain .button button {
        height: 40px;
        width: 90%;
        margin: 10px 5% 0 5%;
        background-color: #03bf06;
        border: 0;
        border-radius: 3px;
        color: #FFFFFF;
        text-align: center;
        line-height: 40px;
        font-size: 16px;
    }

    .verifyProcessMain .button .agreement {
        width: 90%;
        padding: 5px 5% 20px 5%;
        font-size: 12px;
        line-height: 16px;
        text-align: center;
        color: #888888;
    }

    .verifyProcessMain .button .agreement a {
        color: #576B95;
        text-decoration: none;
    }

    .verifyProcessMain.re .button .agreement {
        display: none;
    }
</style>