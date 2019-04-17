<template>
  <div  id="index"  class="index">

      <!--头部的导航-->
      <app-header></app-header>
      <!--<loopimage></loopimage>-->
    <scroll class="wrapper"
            :data="data"
            :listenScroll="true"
            :pulldown="true"
            @pulldown="pulldownrefsh"
            @uppush="productlist"
            @refreshpage="refreshpage">
      <!--图片轮播-->
      <loopbanners :top_banners="top_banners"></loopbanners>
      <!--新闻列表-->
      <newlifts :two_articles="two_articles"></newlifts>
      <!--新闻列表-->
      <shchs :two_articles="two_articles"></shchs>
      <!--活动分类-->
      <Activitylists></Activitylists>
      <!--开拍专场-->
      <shootings :center_banners="center_banners"></shootings>
      <!--详情介绍-->
      <productlists  @refresh="srcollrefsh"  ref="productlists" ></productlists>
    </scroll>
       <!--底部菜单-->
      <menus></menus>
    <div id="childcontentdetail" :class="[isIos ? 'xn-ios':'']">
      <transition :name="transitionName">
        <router-view></router-view>
      </transition>
    </div>

  </div>
</template>
<style>
  body{ background: #eee;}
  #index{
    height:100%;
  }
</style>
<script type="text/babel">
    import menu from '@/components/menu';
    import productlists from '@/components/productlists';
    import loopimage from '@/components/loopimage';
    import Header from '@/components/public/Header';
    import loopbanner from '@/components/index/loopbanner';
    import newlift from '@/components/index/newlift';
    import shch from '@/components/index/shch';
    import Activitylist from '@/components/index/Activitylist';
    import Shooting from '@/components/index/Shooting';
    import scroll  from '@/components/slot/scroll';

    export default {
        data: function () {
            return {
                top_banners: [],
                center_banners:[],
                two_articles: [],
                page: 1,
                pulldown: false,
                data: [],
                tuijian_goods:[],
                isIos:false,
                transitionName:''
            }
        },
        components: {
            "app-header":Header,
            'menus': menu,
            'productlists': productlists,
            'loopimage': loopimage,
            'loopbanners': loopbanner,
            'newlifts': newlift,
            'Activitylists': Activitylist,
            'Shootings': Shooting,
            'scroll':scroll
        },
        methods: {
            getDevice(){
                if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
                    this.isIos = true;
                }
            },
            srcollrefsh(){
                this.data.push(1);
            },
            pulldownrefsh(){
                this.$refs.productlists.clearproductlist()
            },
            productlist(){
                this.$refs.productlists.getproductlist()
            },
            refreshpage(){
                this.page =1;
                this.data=[];
            },
          firstlist:function(){
              var that = this;
              that.$axios.get('/api', {
                  params: {
                      token: storeWithExpiration.get('token'),
                  }
              }) .then(function (response) {
                  if (response.status == '200') {
                      return response.data;
                  }
              }).then(function (json) {
                  that.top_banners = json.data.top_banners;
                  that.center_banners = json.data.center_banners;
                  that.two_articles = json.data.two_articles;
                  console.log(22,that.top_banners);

              }).catch(function (ex) {
                  console.log(ex);
              });
          }
        },
        created:function(){

        },
        mounted: function () {
            this.getDevice();
            this.firstlist();
        }
    }
</script>
