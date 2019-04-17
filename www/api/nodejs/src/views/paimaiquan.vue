<template>
<div id="main_container">
 <div class="left">
      <div class="content">
        <!-- 图片轮播 -->
        <div class="slide" id="slide3">
          <ul>
              <li>
                  <router-link   to="/usersellindex/mumberupgrade">
                      <img    src="http://img.tianbaoweipai.com/weipaiimg/ad/1711/05.jpg" alt="首页轮播图">
                  </router-link>
              </li>
              <li>
                  <router-link   to="/usersellindex/mumberupgrade">
                      <img    src="http://img.tianbaoweipai.com/weipaiimg/ad/1710/02.jpg" alt="首页轮播图">
                  </router-link>
              </li>
              <li>
                  <router-link   to="/usersellindex/mumbercheck">
                      <img   src="http://img.tianbaoweipai.com/weipaiimg/ad/1710/01.jpg" alt="首页轮播图">
                  </router-link>
              </li>
          </ul>
          <div class="dot">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
<!-- 商品区列表 -->
            <lists  v-on:child-say="listenToMyBoy" v-if="showlists"></lists>
      </div>
  <!--  导航-->
        <menus></menus>
</div>
</template>
<script>
var slooppic = require('../components/slooppic.vue');
var list = require('../components/productliststest.vue');
var alert  = require('../components/alert.vue');
var menu  = require('../components/menu.vue');
var config = require('../../config')
import {commonsharejs,isEmptyObject} from "../assets/js/common_function.js"
  module.exports = {
      data: function() {
          return {
              showlists: false,
              userinfo:[]
          }
      },
    components:{
      'slooppic':slooppic,
      'lists':list,
      'menus':menu
    },
    methods:{
            listenToMyBoy:function(o){
                this.userinfo =o||[];
                var user_id = this.$route.params.userid ||0;
                var type = this.$route.params.type ||0;
                var title =this.userinfo.nickname|| '';
                var linkgurl =config.build.env.DOMAIN +'/mymain/'+user_id+'/1';
                commonsharejs(title,linkgurl,this.userinfo.head_pic);
       }
    },
   mounted:function(){
             document.title = "发现"
            //title, linkgurl, img
            //config.build.env.FARENAME + title + config.build.env.SALING;
            //http://w.tianbaoweipai.com/mymain/97706/1
           var  that =this;

       // 显示B
               setTimeout(()=>{
                   this.showlists = true;
           },0);
           $('#slide3').swipeSlide({
             continuousScroll:true,
             mode:'horizontal',
             loop: true,
             autoplayStopOnLast:true,
             autoplayDisableOnInteraction:false,
             speed : 3000,
             transitionType : 'cubic-bezier(0.22, 0.69, 0.72, 0.88)',
             firstCallback : function(i,sum,me){
               me.find('.dot').children().first().addClass('cur');
             },
             callback : function(i,sum,me){
               me.find('.dot').children().eq(i).addClass('cur').siblings().removeClass('cur');
             }
           });
   }
  }
</script>
