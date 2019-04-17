<style>
  #main_container .HeaderContentRight img{
    display:inline-block;
  }
  .clearfloat{clear:both;height:0;font-size: 1px;line-height: 0px;}
  .clearfix:after{
    content:".";
    display:block;
    height:0;
    clear:both;
    visibility:hidden;
  }
  .clearfix{
    zoom:1
  }

  #main_container .HeaderContentLeft .circular{
    border-radius: 50%;
    min-width: 22px;
    width: 22px;
    line-height: 22px !important;
    min-width: 22px;
    font-weight:600;
    font-size:12px;
    margin-left:5px;
    color:#fe0100;
    background-color: #fff;
    display: inline-block;
  }
</style>

<template>
  <div id='main_container' class="main_container">
    <div class="left">
      <div class="content">
        <div class="BoxHeader clearfix clear">
          <div class="ContentAuto">
            <div class="fl HeaderContentLeft">
              <router-link :to="{path:'/paipinmessage'}">商品区消息<span class="circular">{{todayprodcut}}</span></router-link>
            </div>
            <div class="fr HeaderContentRight">
               <router-link  class="Digitaka"  :to="{path:'/goodsproductlists'}"> 精选 </router-link>
            </div>
          </div>
        </div>
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

        <div class="Switch clear">
          <ul class="clear">
            <li class="fl active">
              <router-link   :to="{name:'index',query:{cat_id:'845'}}">
              <div class="SwitchBg" style='background:url("/static/img/category_menu051301.jpg") no-repeat;background-size: cover;'></div>
              <p>玉翠珠宝</p>
               </router-link>
            </li>
            <li class="fl active">
              <router-link   :to="{name:'index',query:{cat_id:'846'}}">
              <div class="SwitchBg" style='background:url("/static/img/category_menu051302.jpg") no-repeat;background-size: cover;'></div>
              <p>书画篆刻</p>
             </router-link>
            </li>
            <li class="fl active">
              <router-link  :to="{name:'index',query:{cat_id:'847'}}">
              <div class="SwitchBg" style='background:url("/static/img/category_menu051303.jpg") no-repeat;background-size: cover;'></div>
              <p>茶酒滋补</p>
                 </router-link>
            </li>
            <li class="fl active">
              <router-link   :to="{name:'index',query:{cat_id:'848'}}">
              <div class="SwitchBg" style='background:url("/static/img/category_menu051304.jpg") no-repeat;background-size: cover;'></div>
              <p>紫砂陶瓷</p>
                 </router-link>
            </li>
            <li class="fl active">
              <router-link   :to="{name:'index',query:{cat_id:'849'}}">
              <div class="SwitchBg" style='background:url("/static/img/category_menu051305.jpg") no-repeat;background-size: cover;'></div>
              <p>工艺作品</p>
                 </router-link>
            </li>
            <li class="fl active">
              <router-link   :to="{name:'index',query:{cat_id:'850'}}">
              <div class="SwitchBg" style='background:url("/static/img/category_menu051306.jpg") no-repeat;background-size: cover;'></div>
              <p>文玩杂项</p>
                 </router-link>
            </li>
          </ul>
          <div class="clearfloat"></div>
        </div>
        <headermessage></headermessage>
        <div class="clearfloat"></div>
        <!-- 商品区列表 -->
        <lists v-if="showlists"></lists>
       <!--  导航-->
        <keep-alive>
        <menus></menus>
          </keep-alive>
      </div>
    </div>
            <div class="nsr-card-loading">
              <nsr-loading :hide-loading="isloadingComplete" :is-end-text="endText"></nsr-loading>
            </div>
    </div>
</template>

<script type="text/babel">
  let headermessage = require('../components/headermessage.vue');
  let list = require('../components/productlistsforindex.vue');
  let alert  = require('../components/alert.vue');
  let menu  = require('../components/menu.vue');
  let nsr_loading  = require('../components/loading.vue');
  module.exports = {
      data: function() {
          return {
              showlists: false,
              todayprodcut:''
          }
      },
    components:{
      'lists':list,
      'menus':menu,
      'nsr-loading':nsr_loading,
        headermessage
    },
    methods:{
        gettodayproduct(){
            var	that =this;
            axios.get('/product/uploadproduct', {
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
                    if(json.data>100){
                        that.todayprodcut = '99+';
                    }else{
                        that.todayprodcut = json.data;
                    }

                }
            }).catch(function(ex) {
                console.log(ex);
            });
        },
    camera:function(){
             this.$router.push({ path: '/fabuc'})
    }
    },
    mounted:function() {
        document.title = "天宝微拍"
        this.gettodayproduct();
        // 显示B
        setTimeout(() => {
            this.showlists = true;
        }, 0);
        $('#slide3').swipeSlide({
          continuousScroll:true,
          mode:'horizontal',
          loop: false,
          lazyLoad:false,
          autoSwipe:true,
          autoplayStopOnLast:false,
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

