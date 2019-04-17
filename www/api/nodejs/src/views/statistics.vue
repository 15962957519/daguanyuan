<template>
<div id="main_container">
 <div class="left">
      <div class="content">
        <!-- 图片轮播 -->
        <div class="slide" id="slide3">
          <ul>
            <li>
                            <router-link   to="/usersellindex/mumberupgrade">
                          <img    src="/static/img/adv004.jpg" alt="首页轮播图">
                            </router-link>
            </li>
            <li>
              <a href="javascript:;">
                <img   src="/static/img/adv005.jpg" alt="首页轮播图">
              </a>
            </li>
          </ul>
          <div class="dot">
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
<!-- 商品区列表 -->
            <lists></lists>
      </div>
  <!--  导航-->
        <menus></menus>
</div>
</template>
<script>
 var slooppic = require('../components/slooppic.vue');
 var list = require('../components/statistics.vue');
  var alert  = require('../components/alert.vue');
  var menu  = require('../components/menu.vue');
  module.exports = {
    components:{
      'slooppic':slooppic,
      'lists':list,
      'menus':menu,
    },
    methods:{

    },
   mounted:function(){
     this.$nextTick(function () {
             document.title = "统计"
       });
         $(function(){
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
         });
   }
  }
</script>
