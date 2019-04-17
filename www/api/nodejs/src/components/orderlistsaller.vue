<template>
  	<div id="orderlistsaller"  name="orderlistsaller" class="icon_lists" style="height:100%;position:relative;overflow-y:auto;">
        <div class="topMenu border horizonBottom">
                   <div class="buyAndSell">
                         <router-link tag="div" to="/usersellindex/orderlistsale" class="sell">我的卖单</router-link>
                         <router-link  tag="div" to="/usercenter/orderlist" class="buy">我的买单</router-link>
                    </div>
                    <ul>
                        <router-link tag="li"  :to="{ name: 'orderlistsale', params: { status:0 ,issale:true}}" exact>
                             <a>全部</a>
                        </router-link>
                      <router-link tag="li"  :to="{ name: 'orderlistsale', params: { status:1 ,issale:true}}">  <a>未付款</a></router-link>
                       <router-link tag="li"  :to="{ name: 'orderlistsale', params: { status:2 ,issale:true}}">  <a>待发货</a></router-link>
                       <router-link tag="li"  :to="{ name: 'orderlistsale', params: { status:3 ,issale:true}}">  <a>未收货</a></router-link>
                       <router-link tag="li"  :to="{ name: 'orderlistsale', params: { status:4 ,issale:true}}">  <a>未评价</a></router-link>
                    </ul>
                    <div class="search" ><div class="border verticalRight"><i class="icon iconfontpay icon-searchicon"></i></div></div>
            </div>

                <orderlists msgfromfa='2'></orderlists>
    </div>
</template>
<script>
	var config = require('../../config')
	var my_user_center_headers  = require('../components/usercenter/user/user_center_header.vue');
	var alert  = require('../components/alert.vue');
	var menu  = require('../components/menu.vue');
	var orderlists  = require('../components/usercenter/saleorderlists.vue');
   	module.exports = {
     /*   data(){
        return ;

        },
        */
        components:{
        			'menus':menu,
        			'my_user_center_header':my_user_center_headers,
                    orderlists
        		},
        ready: function() {
            // this.getAnchorInfo();
        },
        created: function() {

        }
    };



</script>


<style scoped>
 @import '../assets/css/paycat.css'
</style>

<style scoped>
 @import '../assets/css/commonm.css'
</style>
