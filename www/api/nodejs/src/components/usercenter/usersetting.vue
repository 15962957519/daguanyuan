<template>
  <div  id="contentbox" class="contentbox" data-flag="mypublish">
    		<div class="header border bottom">
                    <div class="btn back"  @click="goBack">返回</div>
                    <div class="btn save"></div>
             </div>

    <div class="userInfobox icon_lists">
        <div class="userInfoitem basic unverified overfolwhidden">
            <div class="avatar">
                <div class="img" v-bind:style="{backgroundImage:'url('+userinfo.head_pic+')'}"></div>

            </div>
            <div class="detailInfo">
                <div>{{userinfo.nickname}}</div>
                <span class="tips">(头像、昵称将同步微信)</span>
            </div>
         </div>
         <router-link to="/usercenter/signature">
        <div class="userInfoitem border" style="display:none">
            <div class="title">个人签名</div>
            <div class="info" v-cloak>{{userinfo.usersingnature}}</div>
            <div class="arrow">
                <i class="icon iconfont  icon-shangjiantou1 fi-size fi-stack-0x6 "></i>
            </div>
        </div>
       </router-link >
         <router-link to="contacts">
        <div class="userInfoitem border">
            <div class="title">联系人</div>
            <div class="info" v-cloak>{{userinfo.real_name}}</div>
            <div class="arrow">
                <i class="icon iconfont  icon-shangjiantou1 fi-size fi-stack-0x6"></i>
            </div>
        </div>
        </router-link >
         <router-link to="weixinnumber">
        <div class="userInfoitem border">
            <div class="title">微信号</div>
            <div class="info" v-cloak>{{userinfo.weixinnumber}}</div>
            <div class="arrow">
                <i class="icon iconfont  icon-shangjiantou1 fi-size fi-stack-0x6"></i>
            </div>
            </div>
         </router-link >
        <router-link to="mobile">
        <div class="userInfoitem border telephone">
            <div class="title">手机号码</div>
            <div class="info" v-cloak>{{userinfo.mobile}}</div>
            <div class="arrow">
                <i class="icon iconfont  icon-shangjiantou1 fi-size fi-stack-0x6"></i>
            </div>
        </div>
            </router-link >
        <router-link  class="userInfoitem border address receipt" tag="div"  to="/usercenter/address">
            <div class="title">收货地址</div>
            <div class="info"><span></span></div>
            <div class="arrow">
                 <i class="icon iconfont  icon-shangjiantou1 fi-size fi-stack-0x6"></i>
            </div>
        </router-link>
    </div>
      <!--  导航-->
      <menus></menus>
  </div>
</template>
<script>
	var config = require('../../../config')
	var alert  = require('../alert.vue');
	var menu  = require('../menu.vue');

	module.exports = {
		data: function() {
                return {
                userinfo: [{'user_name': '', 'head_pic': ''}],
                   userinfoinit:{
                                                  head_pic_init:WEIPAT.userinfo.headimgurl
                                },
            }
		},
		components:{
			'menus':menu
		},
		methods:{
		  goBack: function () {
                                    this.$router.back();
                                  }

		},
		mounted:function(){
			  document.title = "用户中心"


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

	}

</script>
<style scoped>
 @import '../../assets/css/commonm.css'
</style>
<style scoped>
 @import '../../assets/css/user_center_seller_iconfont.css'
</style>
 <style scoped>
.header{
height: 43px;
background-color: #FFF;
font-size: 16px;
line-height: 43px;
position: relative;
}
.header .border{
position:relative;
}
.header .border.bottom:after{
bottom:0;
top:auto;
}
.header .border:after{
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
.header .back{
left:4%;
text-align:left;
}
.header .btn.save{
text-align:right;
color:#13AE24;
right:4%;
}
.header .btn {
height:100%;
position:absolute;
overflow:hidden;
cursor:pointer;
}

#contentbox  .userInfobox{
height:auto;
overflow:auto;
margin-top:10px;
width:100%;
background-color:#fff;
padding:0 4%;
}
#contentbox:after{display:block; content:" "; height:0; clear:both; overflow:hidden; visibility:hidden;}
#contentbox  .userInfobox .userInfoitem .avatar{
width:66px;
height:66px;
float:left;
margin:6px 0;
border-radius:35px;
border:1px solid #EEE;
padding:1px;

}
#contentbox  .userInfobox .userInfoitem.overfolwhidden{
overflow:hidden;
}

#contentbox  .userInfobox .userInfoitem .avatar .img{
    width: 60px;
    height: 60px;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    margin: 1px;
    border-radius: 100%;
}
#contentbox .userInfobox .userInfoitem{
width:99%;
line-height:46px;
position:relative;
font-size:14px;
cursor:pointer;
margin-left:1%;
overflow:auto;
border-bottom:1px dashed RGB(224,224,224);
}
#contentbox .userInfobox .userInfoitem.basic{
height:82px;
}
#contentbox .userInfobox .userInfoitem.basic .detailInfo{
overflow:hidden;
margin:0 55px 0 80px;
}
#contentbox .userInfobox .userInfoitem .title{
float:left;
width:23%;
height:46;
line-height:46px;
color:#424242;
font-size:16px;

}

#contentbox .userInfobox .userInfoitem .info{
float:left;
width:66%;
height:46;
line-height:46px;
overflow:hidden;
color:#888;
white-space:nowrap;
text-overflow:ellipsis;
word-break:break-all;
}
#contentbox .userInfobox .userInfoitem.address{
border-bottom:none;
}
#contentbox .userInfobox .userInfoitem .arrow{
position:absolute;
right:0;
height:46px;
line-height:46px;
color:#C8C8C8;
 top:0;  bottom:0;
}

#contentbox  .icon-shangjiantou1.fi-size{
    font-size:16px;
}
#contentbox  .fi-size{
    font-size:16px !important;
}
#contentbox .icon-shangjiantou1:before {
    content: "\e603";
}
</style>
