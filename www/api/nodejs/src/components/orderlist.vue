<template>
  	<div id="orderlist" name="orderlist" class="icon_lists" style="height:auto;">
        <top>
        <div class="topMenu border horizonBottom">
                   <div class="buyAndSell">
                       <router-link tag="div" to="/usersellindex/orderlistsale" class="sell">我的卖单</router-link>
                                               <router-link  tag="div" to="/usercenter/orderlist" class="buy">我的买单</router-link>
                    </div>
                    <ul>
                        <router-link tag="li"  :to="{ name: 'orderlist', params: { status:0 ,issale:true}}" exact>
                             <a href="javascript:;">全部</a>
                        </router-link>
                        <router-link tag="li"  :to="{ name: 'orderlist', params: { status:1 ,issale:true}}">  <a href="javascript:;">未付款</a></router-link>
                       <router-link tag="li"  :to="{ name: 'orderlist', params: { status:2 ,issale:true}}">  <a href="javascript:;">待发货</a></router-link>
                        <router-link tag="li"  :to="{ name: 'orderlist', params: { status:3 ,issale:true}}">  <a href="javascript:;">未收货</a></router-link>
                        <router-link tag="li"  :to="{ name: 'orderlist', params:  { status:4 ,issale:true}}">  <a href="javascript:;">未评价</a></router-link>
                    </ul>
                    <div class="search"><div class="border verticalRight"><i class="icon iconfont icon-searchicon"></i></div></div>
            </div>
         </top>
        <orderlists msgfromfa='1'></orderlists>

    </div>
</template>
<script>
    var config = require('../../config')
    var my_user_center_headers  = require('../components/usercenter/user/user_center_header.vue');
    var alert  = require('../components/alert.vue');
    var menu  = require('../components/menu.vue');
    var copyright  = require('./copyright.vue');
    var orderlists  = require('../components/usercenter/orderlists.vue');
   	module.exports = {
        /*   data(){
               return ;
               },
               */
        components:{
        			'menus':menu,
        			'my_user_center_header':my_user_center_headers,
        			'copyright':copyright,
                    orderlists
        		},
        ready: function() {
            // this.getAnchorInfo();
        },
        created: function() {
                    //获取订单列表
        }
    };



</script>


<style scoped>
 @import '../assets/css/paycat.css'
</style>

<style scoped>
 @import '../assets/css/commonm.css'
</style>
