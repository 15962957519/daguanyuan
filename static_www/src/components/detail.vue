<script>
    (function(){
        var bp = document.createElement('script');
        var curProtocol = window.location.protocol.split(':')[0];
        if (curProtocol === 'https') {
            bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
        }
        else {
            bp.src = 'http://push.zhanzhang.baidu.com/push.js';
        }
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(bp, s);
    })();
</script>
<template>
  <div class="detail">
    <!--顶部导航-->
    <yd-navbar :fixed="true" >
      <router-link   to="" slot="left" >
        <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
      </router-link>
      <p style="font-size: .3rem" slot="center">{{goods_detail.nickname}}的店铺</p>
      <!--<img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>-->
    </yd-navbar>
    <div v-on:click="cancelShare()" style=' position: fixed; height: 100%; background: rgba(0,0,0,0.5);top: 0; display: none; z-index: 99999;' v-show="shareShow">
      <img style="width: 100%;" src="@/assets/images/wxshare.png" />
    </div>
    <yd-slider autoplay="3000">
      <template v-for="(item,index) in goods_images">
        <yd-slider-item   >
          <img :src="getCurlofimgUsenoAuth(item.img)" @click="priviewimges(goods_images,$event,item.goods_id)">
        </yd-slider-item>
      </template>
    </yd-slider>
    <!--时间开始-->
    <div class="data_time">
      <div class="data_time_l">
        <template v-clock>
          <yd-countdown :time="goods_detail.endTime" ></yd-countdown>
        </template>
      </div>
      <div class="data_time_r">{{goods_detail.click_count}}人围观</div>
    </div>
    <!--时间结束-->
    <!--商品标题和点赞-->
    <div class="data_title">
      <div class="data_title_l">{{goods_detail.goods_name |little_name}}</div>
      <div class="data_title_r" v-on:click="cares(goods_detail.goods_id)">
        <p v-if="img01==0"><img src="@/assets/images/collection.png"> </p>
        <p v-if="img01==1"><img src="@/assets/images/collection2.png"> </p>
        收藏 &nbsp;{{care_count}}</div>
    </div>
    <!--商品标题和点赞结束-->
    <!--商品标题和点赞-->
    <div class="data_title">
      <div class="data_title_b">
        <p>
          <span>RMB</span>
          <span>当前价</span>
        </p>
        <p class="data_title_b_pr">￥{{goods_detail.start_price}}</p>
      </div>
    </div>
    <!--商品标题和点赞结束-->
    <!--担保交易开始-->
    <div class="secured_t">
      <ul>
        <li><img src="@/assets/images/right.png"/>担保交易</li>
        <li><img src="@/assets/images/right.png"/>包邮</li>
        <li><img src="@/assets/images/right.png"/>包退</li>
      </ul>
    </div>
    <!--担保交易结束-->
    <!--藏品描述-->
    <div class="describe">
      <dl>
        <dt>藏品描述:</dt>
        <dd>{{goods_detail.goods_content}}</dd>
      </dl>
    </div>
    <!--藏品描述结束-->
    <!--议价列表-->
    <div class="yijia">
      <p><img src="@/assets/images/user/order.png"> 共计<span style="color: red">{{chujialen}}</span>人议价</p>
      <ul v-if="chujialen>0">
        <swiper :options="swiperOption" ref="mySwiper" style="height: 1rem;background: #fff;" >
          <!-- slides -->
          <swiper-slide class="yijia_li"  v-for="(item,key) in bidperson" :key="key">
            <img :src=item.head_pic>{{ item.nickname|subTime}}
          </swiper-slide>

        </swiper>
      </ul>
    </div>
    <!--个人信息开始-->
    <div class="user_t">
      <ul>
        <li><img :src="goods_detail.head_pic"> </li>
        <li style="line-height: .8rem;">{{goods_detail.nickname}}</li>
        <li style="float: right;" class="user_t_r" @click="gotoshop(goods_detail.user_id)">进店逛逛</li>
      </ul>
    </div>
    <!--个人信息结束-->
    <div class="ui-alert-item"  id="bond"   v-show="isShow">
      <div class="list">
        <div class="header">当前购买需交￥{{caution}}保证金</div>
        <div class="top">
          <a href="#">支付保证金</a>
        </div>
        <div class="bottom" @click="isShow=false">取消</div>
      </div>
    </div>
    <!--出价列表-->

    <div style="height: 1rem !important;"></div>
    <!--担保交易开始-->
    <div class="bottom_p">
      <ul v-if="is_sale.code == 4000">
        <li  class="bottom_two" @click="nowAction('nowBuy')">立即购买</li>
        <li v-if="goods_detail.is_talk_price==1" class="bottom_one" @click="nowAction('nowBid')">议价</li>
        <li @click="wxshare()">一键分享</li>
      </ul>
      <ul v-if="is_sale.code == 5000">
        <li  class="bottom_two">已被购买</li>
        <li v-if="goods_detail.is_talk_price==1" class="bottom_one" @click="nowAction('nowBid')">议价</li>
        <li @click="wxshare()">一键分享</li>
      </ul>
    </div>
    <!--担保交易结束-->
  </div>
</template>

<script type="text/babel">
    //固定头部
    // import fixedtitle from '@/components/fixed/fixedtitle'
    var axios =  this.$axios;

    import Vue from 'vue';
    import wx from 'weixin-js-sdk'
    import {Slider, SliderItem} from 'vue-ydui/dist/lib.rem/slider';
    /* 使用px：import {Slider, SliderItem} from 'vue-ydui/dist/lib.px/slider'; */
    Vue.component(Slider.name, Slider);
    Vue.component(SliderItem.name, SliderItem);
    //  返回上一层
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
    /* 使用px：import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar'; */
    Vue.component(NavBar.name, NavBar);
    Vue.component(NavBarBackIcon.name, NavBarBackIcon);
    Vue.component(NavBarNextIcon.name, NavBarNextIcon);
    export default {
        name: 'detail',
        data(){
            return {
                swiperOption: {
                    observer:true, //修改swiper自己或子元素时，自动初始化swiper
                    observeParents:true,//修改swiper的父元素时，自动初始化swiper
                    lazy: true,
                    slidesPerView: 3,
                    slidesPerColumn: 1,
                    lazyLoading : true,//懒加载开启
                    spaceBetween: 5,
                    autoplay: {
                        delay: 2000,
                        stopOnLastSlide: false,
                        disableOnInteraction: true,
                    },
                },
                goods_detail:{},
                goods_images:[],
                care_count:0,
                caution:0,
                isShow:false,
                shareShow:false,
                shareimg:'',
                bidperson:[],
                user_data:{},
                chujialen:0,
                img01:0,
                is_sale:{}
            }
        },
        template: '#parentitem_container',
        mounted: function () {
            var goods_id = this.$route.params.goods_id;
            this.showData(goods_id);
        },
        computed: {

        },
        methods: {
            gotoshop(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/user/seller_shop/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },
            priviewimges(data,e,goods_id){
                return  this.$weipai.priviewimges(data,e,goods_id);
            },
            getCurlofimgUsenoAuth(a, b, c){
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            sharegetCurlofimgUsenoAuth(){
                var _that = this;
                _that.shareimg =  _that.$weipai.getCurlofimgUsenoAuth(_that.goods_images[0]['img']);
            },
            goback: function () {
                this.$router.go(-1);
            },
            showData : function (goods_id) {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                var url = "/goods/goods_detail?goods_id=" + goods_id + "&token=" + token;
                _that.$axios.get(url).then(function (response) {
                    if (response.status == '200') {
                        return response.data;
                    }
                }).then(function (json) {
                    _that.goods_images = json.data.goods_images;
                    _that.goods_detail = json.data.goods_detail;
                    _that.goods_detail['endTime'] = _that.timestampToTime(json.data.goods_detail['endTime']);
                    _that.care_count = json.data.care_count;
                    _that.caution = json.data.caution;
                    _that.bidperson=json.data.bidperson
                    _that.chujialen=json.data.bidperson.length
                    _that.is_sale=json.data.is_sale
                    _that.sharegetCurlofimgUsenoAuth();
                    _that.shareconfig();

                }).catch(function (ex) {
                    console.log(ex);
                });
            },
            nowAction:function(o){
                var _that = this;
                var token = storeWithExpiration.get('token');
                _that.$axios.get("/userslevel?token=" + token).then(function(response) {
                    if(response.data.data['mobile'] == null || response.data.data['mobile'] == ''){
                        _that.$dialog.confirm({
                            title: '当前您未绑定手机号',
                            mes: '确认绑定!',
                            opts: function() {
                                _that.$router.push({name: 'phone_number_link'})
                            }
                        });
                    }else if(response.data.data.is_caution.code ==1001 ){
                        _that.$dialog.confirm({
                            title: '当前购买需交￥'+ _that.caution +'保证金',
                            mes: '确认要交!',
                            opts: function() {
                                _that.$router.push({name: 'caution_link'})
                            }
                        });
                    } else {
                        if (o == 'nowBid') {
                            _that.user_data = response.data.data
                            _that.bid();

                        } else if (o == 'nowBuy') {
                            _that.buy();
                        }
                    }

                }).catch(function(error) {
                    console.log(error);
                });
            },
            //去购买
            buy:function(){
                var _that = this;
                var token = storeWithExpiration.get('token');
                //检查是否支付保证金
                //商品区是否生成自己的订单
                var goods_id = _that.goods_detail.goods_id;
                _that.$axios.post("/isOrderOwn?goods_id=" + goods_id +"&token=" + token).then(function(response) {
                    if(response.data['code'] != 2000){  //变为购买流程,生成新订单
                        _that.$dialog.confirm({
                            title: '是否购买',
                            mes: '确认要购买!',
                            opts: function() {
                                _that.$axios.post("/makeorder?goods_id=" + goods_id +"&token=" + token).then(function(response) {
                                    _that.$dialog.toast({
                                        mes: response.data.data['msg'],
                                        timeout: 3000,
                                        icon: 'success'
                                    });
                                    if(response.data.data['code'] == 2000){
                                        /*window.location.href = "/pay/" + response.data.data['order_id'] + '.html';*/
                                        _that.$router.push({name:'orderdetail_link',query:{order_id:response.data.data['order_id']}})

                                    }
                                }).catch(function(error) {

                                });
                            }
                        });
                    }else{  //已存在该订单
                        _that.$dialog.toast({
                            mes: response.data['msg'],
                            timeout: 3000
                        });
                    }
                }).catch(function(error) {


                }).catch(function(error) {

                });
            },
            //去议价
            bid:function(){
                var _that = this;
                var token = storeWithExpiration.get('token');
                alert(_that.goods_detail.goods_id)
                var goods_id =_that.goods_detail.goods_id ;
                _that.$dialog.confirm({
                    title: '是否议价',
                    mes: '确认要议价!',
                    opts: function() {
                        _that.$axios.get("/new_bid?goods_id=" + goods_id +"&token=" + token).then(function(response) {
                            _that.$dialog.toast({
                                mes: response.data['msg'],
                                timeout: 3000,
                                icon: 'success'
                            });
                            if(response.data['code'] == 2000){
                                _that.bidperson.unshift(_that.user_data)
                                _that.chujialen++
                            }
                        }).catch(function(error) {

                        });
                    }
                });


            },
            cares : function (goods_id) {
                var _that = this;
                var token = storeWithExpiration.get('token');
                var url = "/click_heart?goods_id=" +goods_id + "&token=" +token;
                _that.$axios.get(url).then(function(response) {
                    if (response.status == 200){
                        if (response.data.code == 3){
                            _that.$dialog.toast({
                                mes: response.data.msg,
                                timeout: 1500,
                                callback: function() {
                                    _that.care_count++;
                                    _that.img01=1
                                }
                            });
                        }else{
                            _that.$dialog.toast({
                                mes: response.data.msg,
                                timeout: 1500
                            });
                        }
                    }

                }).catch(function (error) {

                });
            },
            wxshare:function(){
                this.shareShow = true
            },
            cancelShare:function(){
                this.shareShow = false
            },
            timestampToTime:function(timestamp){
                var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                var Y = date.getFullYear() + '/';
                var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '/';
                var D = date.getDate() + ' ';
                var h = date.getHours() + ':';
                var m = date.getMinutes();
                return Y+M+D+h+m;
            },
            shareconfig:function(){
                var _that = this;
                wx.ready(function () {
                    var shareWxLink = window.location.href.split('#')[0] + 'static/html/redirect.html?app3Redirect=' + encodeURIComponent(window.location.href);

                    wx.onMenuShareTimeline({
                        title: '【艺品芳华】' + _that.goods_detail.goods_name, // 分享标题
                        link: shareWxLink,        // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                        imgUrl: _that.shareimg, // 分享图标
                        success:function() {
                            _that.$dialog.toast({
                                mes: '分享朋友圈成功',
                                timeout: 1500,
                                icon: 'success'
                            });
                            // 用户确认分享后执行的回调函数
                        },
                        cancel:function() {
                            // 用户取消分享后执行的回调函数
                        }
                    });

                    wx.onMenuShareAppMessage({
                        title: '【艺品芳华】' + _that.goods_detail.goods_name, // 分享标题
                        desc: _that.goods_detail.goods_content, // 分享描述
                        link: shareWxLink,  // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                        imgUrl: _that.shareimg, // 分享图标
                        success: function () {
                            // alert('分享给朋友成功')
                            _that.$dialog.toast({
                                mes: '分享给朋友成功',
                                timeout: 1500,
                                icon: 'success'
                            });
                            // 用户确认分享后执行的回调函数
                        },
                        cancel: function () {
                            // 用户取消分享后执行的回调函数
                        }
                    })
                })
            }
        },
        filters:{
            little_name(value){
                if (value != null) {
                    return value.substr(0,14);
                }else{
                    return value;
                }
            },
            subTime: function (val) {
                val = val || '';
                if(val!=undefined){
                    return `${val.substring(0,1)}**${val.substring(val.length-1)}`
                    //return val.substring(0, 2);
                }else{
                    return val;
                }
            },

        }


    }
</script>

<style scoped  rel="stylesheet/stylus">
  .detail{padding-top: .5rem;}
  /*议价*/
  .yijia{ width: 100%;  background: #fff; margin-top: .1rem;}
  .yijia p{ height: .8rem; width: 100%; color: #666; border-bottom: 1px solid #eee; text-align: left; line-height: .8rem;}
  .yijia p img{ width: .5rem; float: left; margin: 0 .1rem; padding-top: .1rem}
  .yijia ul{  line-height: 1rem; width: 100%; height:1rem;}
  .yijia_li{ border: 1px solid #eee; width: 25% !important; float: left; height: 1rem !important; text-align: left;}
  .yijia_li img{  margin:0 .1rem;width: .7rem; float: left; padding-top: .12rem;}
  [v-clock] {
    display: none;
  }
  .slide-enter-active, .slide-leave-active{transition: all 0.3s}
  .detail{position: fixed;z-index: 100;top: 0;left: 0;bottom: 0;right: 0;background: #eee;display: block;overflow-y: scroll;}
  .slide-enter, .slide-leave-to{transform: translate3d(100%, 0, 0)}
  /*倒计时和关注*/
  .data_time{ width: 100%; height: .8rem; background: rgba(0,0,0,.6); color: #fff;position: absolute;
    margin-top: -.8rem;z-index: 111; }
  .data_time_l{ width: 60%; float: left; text-align: left; padding-left: .3rem; font-size: .3rem; line-height: .8rem; }
  .data_time_r{  width: 40%;float: left;text-align: right; padding-right: .3rem; font-size: .3rem; line-height: .8rem;}
  /*商品标题*/
  .data_title{ width: 100%; height: 1.1rem; background: #fff; padding:0  .3rem ;}
  .data_title_l{border-bottom:1px solid #eee;font-weight:bold;width: 60%; float: left; color: #333; text-align: left;  font-size: .3rem; line-height: 1.1rem; }
  .data_title_r p img{     width: .6rem;margin-top: .1rem;}
  .data_title_r{ border-bottom:1px solid #eee; width: 40%;float: left;text-align: right; font-size: .25rem; height: 1.11rem;}
  /*我是价格*/
  .data_title_b{  padding-top: .2rem}
  .data_title_b p span{ text-align: left; display: block; height: .3rem;}
  .data_title_b p{ float: left;}
  .data_title_b_pr{ margin-left: .1rem; line-height: .6rem; color:#af773e;font-size: .4rem; }
  /*担保交易*/
  .secured_t{ width: 100%; height: 1rem; background: #fff; margin:.1rem 0rem;}
  .secured_t ul li {  width: 33.3%; float: left; line-height: 1rem; font-size: .25rem; color: #666;}
  .secured_t ul li img{    vertical-align: middle; height: .35rem;  margin-top: -.05rem;margin-right: .05rem;}
  /*描述*/
  .describe{ width: 100%; height: auto; background: #fff; text-align: left; padding: .3rem;}
  .describe  dl dt{  margin-bottom: .2rem; font-size: .35rem;}
  .describe  dl dd{ line-height: .4rem}
  /*个人信息*/
  .user_t{ width: 100%; height: 1rem; background: #fff; margin:.1rem 0rem;padding: 0.1rem .3rem;}
  .user_t ul li { float: left;  font-size: .35rem;}
  .user_t ul li img{ width: .8rem; height: .8rem; border-radius:.8rem;margin-right: .1rem; }
  .user_t_r{border-radius:.1rem; border: 1px solid #af773e;margin-top:.1rem; color: #af773e; font-size: .2rem; padding: 0.1rem .2rem; height: .6rem; line-height: .38rem;}
  /*底部出价*/
  .bottom_p{ width: 100%; height: 1rem; background: #fff; position: fixed; bottom: 0; box-shadow:4px 2px 10px #666; z-index: 999;}
  .bottom_p ul li {  width: 33.3%; float: right; line-height: 1rem; font-size: .35rem;}
  .bottom_one{ background:#ffb03f; color: #fff; }
  .bottom_two{background:#af773e;color: #fff;}
  /*去除顶部的间距*/
  .yd-slider:before {
    content: "";
    display: block;
    width: 100%;
    height:0!important;
  }
</style>