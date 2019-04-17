<template>
    <div id="header" class="header">
        <div class="avatar" >
            <div class="img"  v-bind:style="{backgroundImage:'url('+userinfo.head_pic+')'}"></div>
        </div>
        <div class="userInfo" >
                <div  class="verifyState1andheader">
                                      <div  v-if="userinfo.level == '1'">
                                          <div class="verifyState1 membercolorv1"></div>
                                     </div>
                                     <div v-else-if="userinfo.level == '2'">
                                          <div class="verifyState1 membercolorv2"></div>
                                     </div>
                                     <div v-else-if="userinfo.level == '3'">
                                          <div class="verifyState1 membercolorv3"></div>
                                     </div>
                                     <div v-else-if="userinfo.level == '4'">
                                        <div class="verifyState1 membercolorv4"></div>
                                     </div>
                                     <div v-else-if="userinfo.level == '5'">
                                         <div class="verifyState1 membercolorv5"></div>
                                     </div>
                                     <div v-else>
                                        <div class="verifyState1 membercolorv1"></div>
                                     </div>
                                    <template v-if="userinfo.is_authentication==1">
                                       <span>已认证</span>
                                    </template>
                                     <template v-else>
                                      <span>未认证</span>
                                    </template>
                  </div>
            <div class="name">
                <a class="verified" v-cloak >{{userinfo.nickname}}</a>
                <div class="level buyerLevel lv1"></div>
            </div>
                <router-link  v-cloak class="scores" tag="div" to="/usercenter/foucsme">粉丝{{userinfo.fans_count}} </router-link>
                <div class="vline"></div>
                <router-link  v-cloak class="fans" tag="div" to="/usercenter/myfoucs">关注{{userinfo.collect_count}} </router-link>
        </div>
        <div class="userSetting  icon_lists">
            <router-link to="/usercenter/setting">
            <i class="arrow"></i>
            <div class="settings">个人资料</div>
            </router-link>
            </div>
    </div>
</template>
<script>
    var config = require('../../../../config')

    export default {
        data(){
         var  that =this;
            return {
                userinfo:{
                    nickname:WEIPAT.userinfo.nickname,
                    head_pic_init:WEIPAT.userinfo.headimgurl
                },
              userinfoinit:{
                                  head_pic_init:WEIPAT.userinfo.headimgurl
                            }
            }
        },
        mounted: function() {
         var  that =this;
            var header = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
         axios.get( '/user/userinfoall', {
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
            },
            methods:{
            },
            computed:{

            }
    }
</script>

<style scoped>
    .header {
        width: 100%;
        background: #FFF;
        border-top: 10px solid #EFEFF4;
        margin-bottom: 10px;
        position: relative;
        height: 90px;
        cursor: pointer;
    }
    .header .avatar {
        float: left;
        width: 66px;
        height: 66px;
        padding: 1px;
        margin: 10px 15px;
        border-radius: 35px;
        border: 1px solid #D5D4D4;
    }
    .header .avatar a {
        display: block;
    }
    .header .avatar .img {
        width: 60px;
        margin: 1px;
        height: 60px;
        display: block;
        border-radius: 32px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
    }

    .header .userInfo {
        font-size: 12px;
        height: 90px;
        line-height: 90px;
        margin: 0 50px 0 100px;
    }

    .header .userInfo .name {
        margin-top: 26px;
        height: 16px;
        line-height: 16px;
        margin-bottom: 10px;
        color: #000;
        overflow: hidden;
        font-size: 16px;
    }

    .header .userInfo .name a {
        float: left;
        display: block;
    }

    .header .userInfo .name .sellerLevel,
    .header .userInfo .name .buyerLevel {
        margin: 0 2px 0 5px;
    }

    .header .userInfo .name .individual,
    .header .userInfo .name .business {
        height: 15px;
        line-height: 15px;
        display: flex;
        display: -webkit-flex;
        float: left;
        font-size: 33px;
    }

    .header .userInfo .name .onlyBail {
        height: 20px;
        line-height: 20px;
        float: left;
        font-size: 10px;
        font-weight: normal;
        color: #169ADA;
        display: flex;
        display: -webkit-flex;
        margin-left: -2px;
        margin-top: 1px;
    }

    .header .userInfo .name .individual i {
        color: #FFB123;
        font-weight: normal;
    }

    .header .userInfo .name .business i {
        color: #169ADA;
        font-weight: normal;
    }

    .header .userInfo .name .verify {
        float: left;
        height: 15px;
        border-radius: 1px;
        background-repeat: no-repeat;
        background-position: center center;
        display: block;
    }

    .header .userInfo .name .verify.individual {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_individual.png);
    }

    .header .userInfo .name .verify.business {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_business.png);
    }

    .header .userInfo .name .verify.bail {
        width: 19px;
        background-size: 19px;
        background-image: url(/res/img/bail.png);
    }

    .header .userInfo .name .verify.individual.bail {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_individual_bail.png);
    }

    .header .userInfo .name .verify.business.bail {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_business_bail.png);
    }

    .header .userInfo .vline {
        float: left;
        height: 12px;
        width: 1px;
        line-height: 12px;
        border-left: 1px solid #EEE;
    }

    .header .userInfo .scores,
    .header .userInfo .fans {
        float: left;
        color: #888;
        height: 12px;
        line-height: 12px;
    }

    .header .userInfo .scores {
        padding-right: 4px;
    }

    .header .userInfo .fans {
        padding-left: 4px;
    }

    .header .userSetting {
        height: 100%;
        position: absolute;
        top: 0px;
        right:4%;
         overflow:hidden;
    }

    .header .userSetting .settings{
        float: right;
        height: 90px;
        right: 10%;
        line-height: 90px;
        display: block;
        color: #888;
        font-size: 14px;
    }

    .header .userSetting .arrow {
        float: right;
        font-size: 22px;
        display: block;
        width: 25px;
        height: 90px;
        color: #CFCFCF;
        background:#fff url(../../../assets/img/headerjiantou.png) no-repeat;
        background-position:50% 50%;
        background-size:contain;
        margin-left: 8px;
        line-height: 90px;
        overflow:hidden;
    }
.header  .verifyState1andheader{
line-height:30px;
float:left;
width:48px;
margin:0 auto;
text-align:left;
margin-right:8px;
color:#888;
}
.header  .verifyState1{
float:left;
background: red;
width: 30px;
margin-right: 10px;
height: 30px;
}
.header  .verifyState1.membercolorv1{
    color:#c7c7cc;
    background-color: #ccc;
    background-image:url(/static/img/nomember.png);
    background-repeat:no-repeat;
    background-size:cover;
    background-position:center;
}
.header .verifyState1.membercolorv5{
    background-image:url(/static/img/diamond.png);
    background-repeat:no-repeat;
    background-size:cover;
    background-color:#FFF;
    background-position:center;
}
.header .verifyState1.membercolorv4{
    background-image:url(/static/img/gold_medal.png);
    background-repeat:no-repeat;
    background-size:cover;
    background-color:#FFF;
    background-position:center;
}
.header  .verifyState1.membercolorv3{
    background-image:url(/static/img/silver.png);
    background-repeat:no-repeat;
    background-size:cover;
    background-color:#FFF;
    background-position:center;
}
.header .verifyState1.membercolorv2{
    background-image:url(/static/img/bronze.png);
    background-repeat:no-repeat;
    background-size:cover;
    background-color:#FFF;
    background-position:center;
}
</style>