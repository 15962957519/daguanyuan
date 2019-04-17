<template>
	<div id="user_sellindex"  class="icon_lists" style="height: auto;">
		<div id="boxheader"><my_user_center_header></my_user_center_header></div>
        <div class="menuMain seller">
            <router-link  class="menuItem balance"  tag="div" :to="{ path: '/usercenter/myassets'}">
                        <span class="icon iconfont fi-stack" style="color: #ffad18">
                            <i class="icon iconfont icon-square fi-stack-2x"></i>
                            <i class="icon iconfont icon-yue fi-color-fff fi-stack-1x5"></i>
                        </span>
                    <div class="title">余额</div>
                    <div class="info money"  v-cloak>￥{{userinfo.user_money}}元</div>
            </router-link>
     <router-link class="menuItem balance" tag="div" :to="{ path: '/usersellindex/mumbercheck'}">
                 <span class="icon iconfont  fi-stack"  style="color: #ffad18"> <i class="icon iconfont icon-square fi-stack-2x"></i>
                  <i class="icon iconfont icon-shimingrenzheng fi-color-fff fi-stack-1x5"></i>
                  </span>
                <div class="title">实名认证</div>
      </router-link>

        <router-link class="menuItem balance" tag="div" :to="{ path: '/usersellindex/mumberupgrade'}">
             <span class="icon iconfont  fi-stack"  style="color: #ffad18"> <i class="icon iconfont icon-square fi-stack-2x"></i>
              <i class="icon iconfont icon-shengji fi-color-fff fi-stack-1x5"></i>
              </span>
            <div class="title">会员升级</div>
        </router-link>



   <router-link class="menuItem saleList" tag="div" to="/usersellindex/productlist">
                <span class="icon iconfont fi-stack" style="color: #2fcbc0">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-shangpinguanli fi-color-fff fi-stack-1x5"></i>
                </span>
            <div class="title">商品区管理</div>
            <div class="info txt">竞拍中 {{userinfo.auction}} 单</div>
   </router-link>
   <router-link class="menuItem sendSaleMsg" tag="div" to="/usersellindex/orderlistsale">
                <span class="icon iconfont fi-stack" style="color: #2fcbc0">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-mail fi-color-fff fi-stack-1x5"></i>
                </span>
                <div class="title">我的订单</div>
            </router-link>
        <router-link class="menuItem message" tag="div" to="/usercenter/mesagecenter">
                <span class="icon iconfont  fi-stack" style="color: #169ada">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-xiaoxizhongxin fi-color-fff fi-stack-1x5"></i>
                </span>
            <div class="title">消息中心</div>
            <div class="info message v-cloak" >{{userinfo.message_count}}</div>
        </router-link >


             <router-link class="menuItem sendSaleMsg" tag="div" to="/usercenter/sendmessage">
                <span class="icon iconfont fi-stack" style="color: #2fcbc0">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-mail fi-color-fff fi-stack-1x5"></i>
                </span>
                <div class="title">群发消息</div>
            </router-link>

        <div   @click.stop="checkmember($event)"  class="menuItem help"  >
                <span class="icon iconfont fi-stack" style="color: #3ccec4">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-weixinyaoyiyao fi-color-fff fi-stack-1x"></i>
                </span>
            <div class="title">摇粉丝</div>
        </div>
        <router-link class="menuItem help" tag="div" to="/usercenter/helper">
                <span class="icon iconfont fi-stack" style="color: #3ccec4">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-help fi-color-fff fi-stack-1x"></i>
                </span>
            <div class="title">客服问答</div>
        </router-link>
    </div>


    <router-link class="tab-item" :to="{path:'/usercenter/index'}">
    <div class="menuMain margin-bottom-distance">
        <div class="menuItem specheight">
                <span class="icon iconfont fi-stack" style="color: #ffad18">
                    <i class="icon iconfont icon-square fi-stack-2x"></i>
                    <i class="icon iconfont icon-me fi-color-fff fi-stack-1x2"></i>
                </span>
            <div class="title">买家中心</div>
        </div>
    </div>
	</router-link>
<!--  导航-->
		<menus></menus>
    </div>
</template>




<script>
	var config = require('../../config')
	var my_user_center_headers  = require('./usercenter/seller/user_center_header.vue');
	var alert  = require('../components/alert.vue');
	var menu  = require('../components/menu.vue');
	import {MessageBox,Indicator,Toast ,Actionsheet } from 'mint-ui';
    var isSale =true;
	module.exports = {
		data() {
			return {
                userinfo:{
                nickname:WEIPAT.userinfo.nickname,
                head_pic_init:WEIPAT.userinfo.headimgurl,
                user_money:0
                },
                actions:[],
                sheetVisible:false
		}
		},
		components:{
			'menus':menu,
			'my_user_center_header':my_user_center_headers,
		},
		methods:{
		  //进入详细
                      directbuyercenter:function(event){

                      },
                      checkmember:function(e){
                             //alert(1)
                                    if(1>=this.userinfo.level){
                                            this.sheetVisible=true;
                                            //正在努力加载
                                                              Indicator.open({
                                                                                         text:'普通游客不支持该功能',
                                                                                         spinnerType: 'fading-circle'
                                                                                    });
                                                                                     setTimeout(() => Indicator.close(), 1000);
                                          //  return false;
                                    }else{
                                   this.$router.push('/shaked')
                                    }


                      },
                      shake:function(){


                      }
		},
		mounted:function(){
		 this.actions = [{
                name: '拍照',
                method: this.takePhoto
              }, {
                name: '从相册中选择',
                method: this.openAlbum
              }];
              this.actions2 = [{
                name: '确定'
              }, {
                name: '返回上一步',
                method: this.goBack
              }];

		var	that =this;

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
                 	        	if(json.code  == 2000){
                 				that.userinfo = json.data.result;
                 			}
			}).catch(function(ex) {
				console.log(ex);
			});
		}
	}



</script>

<style scoped>
 @import '../assets/css/user_center_seller_iconfont.css'

</style>
<style scoped>
 @import '../assets/css/commonm.css';
</style>