<template>
    <div id="user_seller_header" class="header">
    	<div class="headertop border bottom">
                        <div class="btn back"  @click="goBack">返回</div>
                        <div class="btn save"></div>
                 </div>
         <div class="userinfow">
            <div class="avatar">
                <div class="img"  v-bind:style="{backgroundImage:'url('+userinfo.head_pic+')'}"></div>
            </div>
            <div class="userInfo" >
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
                 <i class="arrowsetting"></i>
                <div class="settings">个人资料</div>
             </router-link>
            </div>
        </div>
        <div class="userSetting useraddress address">
            <div class="returnaddress">退货地址</div>
            <div><span></span></div>
            <div style="float:right"> <i class="arrow"></i></div>
        </div>
        <div class="label">资质信息</div>
        <div class="userInfobox qualify right">
                  <router-link to="/usersellindex/mumbercheck">
                    <div class="userInfoitem">
                                <div class="title">实名认证</div>
                                <div  v-if="userinfo.is_authentication == '0'">
                                  <div class="info unverify"></div>
                                 </div>
                                 <div v-else>
                                     <div class="info checked"></div>
                                 </div>
                                <div class="arrowbiaozhi"> <i class="tianbaoweipai icon-arrowright"></i></div>
                     </div>
                 </router-link>
            </div>
            <div class="label">其他信息</div>
    </div>
</template>
<script>
    var config = require('../../../../config')

    export default {
        data(){
            return {
                userinfo:{
                    nickname:WEIPAT.userinfo.nickname,
                    head_pic_init:WEIPAT.userinfo.headimgurl
                },
              userinfoinit:{
                                  head_pic_init:WEIPAT.userinfo.headimgurl
                              },
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
           if(typeof(json.data.result)!="undefined"){
                        that.userinfo = json.data.result;
            }

            }).catch(function(ex) {
            console.log(ex);
            });
            },
            methods:{
            goBack:function(){
                    this.$router.go(-1)


            }



            }
    }
</script>
<style scoped>
 @import '../../../assets/css/user_center_seller_iconfont.css'
</style>
<style scoped>

#user_seller_header .header{
   width: 100%;
        background-color: #FFF;
        border-top: 10px solid #EFEFF4;
        margin-bottom: 10px;
        position: relative;
        cursor: pointer;
}
 #user_seller_header .headertop .border{
position:relative;
}
 #user_seller_header .userinfo{
background-color:#fff;
 position:relative;
 }

 #user_seller_header .headertop .border.bottom:after{
bottom:0;
top:auto;
}
 #user_seller_header .headertop .border:after{
width: 100%;
height: 1px;
position: absolute;
content: '';
top: 0;
left: 0;
transform: scaleY(0.5);
background-color: #D9D9D9;
-webkit-transform: scaleY(0.5);
}
 #user_seller_header .headertop .back{
left:4%;
text-align:left;
}
 #user_seller_header .headertop .btn.save{
text-align:right;
color:#13AE24;
right:4%;
}
 #user_seller_header .headertop .btn {
height:100%;
position:absolute;
overflow:hidden;
cursor:pointer;
}

  #user_seller_header  .headertop {
        height: 43px;
        background-color: #FFF;
        font-size: 16px;
        line-height: 43px;
        position: relative;
    }


   #user_seller_header .userinfow .avatar {
        float: left;
        width: 66px;
        height: 66px;
        padding: 1px;
        margin: 10px 15px;
        border-radius: 35px;
        border: 1px solid #D5D4D4;
    }
  #user_seller_header  .userinfow .avatar a {
        display: block;
    }
   #user_seller_header .userinfow .avatar .img {
        width: 60px;
        margin: 1px;
        height: 60px;
        display: block;
        border-radius: 32px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
    }

  #user_seller_header   .userinfow {
        font-size: 12px;
        background-color:#fff;
        height: 90px;
        line-height: 90px;
          position:relative;
                background-color:#fff;
        margin:20px 0 20px 0;
    }
  #user_seller_header   .userInfo{
        font-size: 12px;
        height: 90px;
        padding-top:15px;
        line-height: 90px;
         margin: 0 50px 0 100px;
  }
  #user_seller_header   .userInfo .name {
        width: 100%;
        height: 16px;
        line-height: 16px;
        margin-bottom: 10px;

        color: #000;
        overflow: hidden;
        font-size: 16px;
    }

   #user_seller_header  .userInfo .name a {
        float: left;
        display: block;
    }

  #user_seller_header   .userInfo .name .sellerLevel,
   #user_seller_header  .userInfo .name .buyerLevel {
        margin: 0 2px 0 5px;
    }

  #user_seller_header   .userInfo .name .individual,
  #user_seller_header   .userInfo .name .business {
        height: 15px;
        line-height: 15px;
        display: flex;
        display: -webkit-flex;
        float: left;
        font-size: 33px;
    }

   #user_seller_header  .userInfo .name .onlyBail {
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

  #user_seller_header   .userInfo .name .individual i {
        color: #FFB123;
        font-weight: normal;
    }

   #user_seller_header  .userInfo .name .business i {
        color: #169ADA;
        font-weight: normal;
    }

  #user_seller_header   .userInfo .name .verify {
        float: left;
        height: 15px;
        border-radius: 1px;
        background-repeat: no-repeat;
        background-position: center center;
        display: block;
    }

   #user_seller_header  .userInfo .name .verify.individual {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_individual.png);
    }

   #user_seller_header  .userInfo .name .verify.business {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_business.png);
    }

   #user_seller_header  .userInfo .name .verify.bail {
        width: 19px;
        background-size: 19px;
        background-image: url(/res/img/bail.png);
    }

  #user_seller_header   .userInfo .name .verify.individual.bail {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_individual_bail.png);
    }

   #user_seller_header  .userInfo .name .verify.business.bail {
        width: 26px;
        background-size: 26px;
        background-image: url(/res/img/verify_business_bail.png);
    }

   #user_seller_header  .userInfo .vline {
        float: left;
        height: 12px;
        width: 1px;
        line-height: 12px;
        border-left: 1px solid #EEE;
    }

  #user_seller_header   .userInfo .scores,
   #user_seller_header  .userInfo .fans {
        float: left;
        color: #888;
        height: 12px;
        line-height: 12px;
    }

   #user_seller_header  .userInfo .scores {
        padding-right: 4px;
    }

   #user_seller_header  .userInfo .fans {
        padding-left: 4px;
    }

   #user_seller_header   .userSetting {
height: 100%;
background-color:#fff;
position: absolute;
top: 0px;
right:4%;
overflow:hidden;
    }

   #user_seller_header  .userSetting .settings{
        float: right;
        height: 90px;
        right: 10%;
        line-height: 90px;
        display: block;
        color: #888;
        font-size: 14px;
    }

   #user_seller_header  .userSetting .arrowsetting {
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

         #user_seller_header   .arrow {
            float: right;
            font-size: 22px;
            display: block;
            width: 25px;
            height: 46px;
            color: #CFCFCF;
            background:#fff url(../../../assets/img/headerjiantou.png) no-repeat;
            background-position:50% 50%;
            background-size:contain;
            margin-left: 8px;
            line-height: 46px;
            overflow:hidden;
        }
     #user_seller_header  .userSetting .arrow {
        float: right;
        font-size: 22px;
        display: block;
        width: 25px;
        height: 46px;
        color: #CFCFCF;
        background:#fff url(../../../assets/img/headerjiantou.png) no-repeat;
        background-position:50% 50%;
        background-size:contain;
        margin-left: 8px;
        line-height: 46px;
        overflow:hidden;
    }
  #user_seller_header   .address{
    display:table;
    }

    #user_seller_header  .useraddress div{
    float:left;
    }
   #user_seller_header  .useraddress{
        width:100%;
        margin-top:20px;
        background-color:#fff;
        height:46px;
        line-height:46px;
        position:relative;
        padding:0 20px;
        margin-left:4%;
        cursor:pointer;
        font-size:16px;
        color:#999;
margin-bottom:15px;



    }
   #user_seller_header .label{
font-size:14px;
color:#888;
text-indent:4%;
   }

 #user_seller_header  .userInfobox{
    width: 100%;
    background-color: #FFF;
    border-left: none;
    border-right: none;
    margin-top: 10px;
    margin-bottom: 15px;
}
 #user_seller_header  .userInfobox .userInfoitem{
    width: 96%;
    height: 46px;
    position: relative;
    line-height: 46px;
    color: #999;
    font-size: 14px;
    margin: 0 0 0 4%;
    cursor: pointer;
 }
 #user_seller_header  .userInfobox .userInfoitem .info{
 text-align:right;
 }
  #user_seller_header  .userInfobox .userInfoitem .info.unverify:before{
content:"未认证";
  }

   #user_seller_header  .userInfobox .userInfoitem .checked.info:after{
content:"已经认证";
  }

   #user_seller_header  .userInfobox .userInfoitem .info.notPay:before{
content:"未支付";
  }

   #user_seller_header  .userInfobox .userInfoitem .info.Pay:after{
content:"已经支付";
  }

 #user_seller_header  .userInfobox .userInfoitem .title{
float: left;
    width: 23%;
    height: 46px;
    line-height: 46px;
    color: #424242;
    font-size: 16px;
 }
 #user_seller_header  .info{
  float: left;
      width: 66%;
      height: 46px;
      line-height: 46px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      color: #888;
      word-break: break-all;
 }
#user_seller_header  .userInfobox .userInfoitem .arrowbiaozhi{
position:absolute;
height:46px;
line-height:46px;
right:4%;
top:50%;
margin-top:-23px;
color:#C8C8C8;
 }
#user_seller_header  .userInfobox .userInfoitem  .tianbaoweipai{
font-family: "usercenter" !important;
min-width: 1em;
color: inherit;
font-size: inherit;
font-style: normal;
display: inline-block;
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
}
#user_seller_header  .userInfobox .userInfoitem  .tianbaoweipai.icon-arrowright:before{
content:"\e603";
}
#user_seller_header .border:after{
width: 100%;
    height: 1px;
    position: absolute;
    content: '';
    top: 0;
    left: 0;
    transform: scaleY(0.5);
    background-color: #D9D9D9;
    -webkit-transform: scaleY(0.5);
}
</style>