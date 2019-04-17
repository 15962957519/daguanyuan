<template>
	<div id="usercenter" class="icon_lists" style="height:auto;">
		<div id="boxheader"><my_user_center_header></my_user_center_header></div>
	<div class="orderMain">
		<div class="myOrder" href="javascript:;">
			<div class="title">买买买</div>
			<div class="arrow">
				<i class="wptFi icon-arrowright"></i>
			</div>
		</div>
	</div>

	<div  id="menuMain"  class="menuMain buyer">
		<router-link  class="menuItem balance"  tag="div" :to="{ path: '/usercenter/myassets'}">
			<span><i class="icon iconfont icon-yue" style="color: #ffad17"></i></span>
			<div class="title">余额</div>
			<div class="arrow" href="/balance"></div>
			<div class="info money" v-cloak>￥{{userinfo.user_money}}</div>
		</router-link>
		<router-link  tag="div" class="menuItem likeSale" to="/usersellindex/orderlistsale">
        			<span><i class="icon iconfont icon-weiguan" style="color: #ffad17"></i></span>
        			<div class="title">我的订单</div>
        			<div class="arrow" href="/my/likeSale"></div>
        		</router-link>
	<router-link class="menuItem message" tag="div" to="/usercenter/mesagecenter">
			<span><i class="icon iconfont icon-xiaoxizhongxin" style="color: #169ada"></i></span>
			<div class="title">消息中心</div>
			<div class="arrow" href="/message"></div>
			<div class="info message" v-cloak >{{userinfo.message_count}}</div>
		</router-link>
		<router-link  tag="div" class="menuItem bidSale" :to="{path:'/paipinmessage'}">
			<span><i class="icon iconfont icon-canpai" style="color: #169ada"></i></span>
			<div class="title">参拍商品区</div>
			<div class="arrow" href="/my/bidSale"></div>
		</router-link>

		<div class="menuItem"></div>
	</div>
		<router-link class="tab-item" :to="{path:'/usersellindex/index'}">
            <div class="menuMain sellerCenter margin-bottom-distance" >
                <div class="menuItem seller">
                    <i class="icon iconfont icon-iconfontzhizuobiaozhunBduan"></i>
                    <div class="title">卖家中心</div>
                        <div class="arrow">
                             <i class="wptFi icon-arrowright"></i>
                        </div>
                </div>
            </div>
		</router-link>
		<!--  导航-->
		<menus></menus>
	</div>

</template>


<script>
	var config = require('../../config')
	var my_user_center_headers  = require('./usercenter/user/user_center_header.vue');

	var alert  = require('../components/alert.vue');
	var menu  = require('../components/menu.vue');
    var isSale =false;

	module.exports = {
		data: function() {
			return {
			userinfo: [{'user_name': '', 'head_pic': ''}]
		}
		},
		components:{
			'menus':menu,
			'my_user_center_header':my_user_center_headers,
		},
		methods:{
            //进入详细
            directbuyercenter:function(event){
                var el = event.currentTarget;
                var editArrow = $(el).find('.arrow');
                if (editArrow.length > 0) {
                    wptRedirect(editArrow.attr('href'));
                }
            }
		},
		mounted:function(){
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
                        				if(json.code  == 2000){
                                                     				that.userinfo = json.data.result;
                                                     			}
			}).catch(function(ex) {
				console.log(ex);
			});

			   this.$nextTick(function () {
                    document.title = "用户中心"
                  })
		}

	}


</script>


<style scoped>
 @import '../assets/css/user_center_iconfont.css'
</style>
<style scoped>
 @import '../assets/css/commonm.css'
</style>