<template>
    <div id="productlistsforjinpin" class="clear">
           <!-- 图片轮播 -->
            <div class="slide clearfix" id="slide3">
              <ul>
                <li>
                   <router-link   to="###">
                 <img    src="http://w.tianbaoweipai.com/static/img/adv/1707/001.jpg" alt="首页轮播图">
                   </router-link>
                </li>
                <li>
                  <router-link   to="###">
                      <img    src="http://w.tianbaoweipai.com/static/img/adv/1706/002.jpg" alt="首页轮播图">
                    </router-link>
                </li>
                  <li>
                  <router-link   to="###">
                      <img    src="http://w.tianbaoweipai.com/static/img/adv/1706/003.png" alt="首页轮播图">
                    </router-link>
                </li>
              </ul>
              <div class="dot">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>


        <div class="saleMain" v-infinite-scroll="loadMore"   infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <div class="saleItem clearfix" v-for="items in proresults">

                    <div class="pm l">
                        <div class="imgList">
                            <div @click="priviewimges(items.img,$event)" :data-noimg="index" class="lazyLoad"
                                 v-for="(item,index) in items.nowaterimg" v-lazy:background-image="item.img"></div>
                        </div>
                    </div>
                    <div class="pm r">
                        <div class="title">{{items.goods_name}}</div>
                        <div class="popularity">{{items.click_count}}</div>
                        <div class="desc fullDesc" v-html="items.goods_content"></div>
                        <div class="price">
                            <span>{{items.start_price}}</span>
                        </div>
                        <div class="bidBtnjinpin">
                          <router-link class="officialspecial"  tag="div" :to="{name:'goodsproductdetail',params:{goods_id:items.goods_id}}">详情</router-link>
                          <router-link :to="{name:'goodsproductdetail',params:{goods_id:items.goods_id}}">
                                <em v-if="items.goods_status!=2">出&nbsp;&nbsp;价</em>
                          </router-link>
                        </div>
                </div>
        </div>
    </div>
    </div>
</template>

<script>
    var Countdown = require('js-countdown');
    import {mapState} from 'vuex';
    import {MessageBox, Indicator, Toast} from 'mint-ui';
    import {rmoney, userLikeProduct_function, commonsharejs} from "../assets/js/common_function.js"
    var paymentframe = require('./usercenter/payment.vue');
    module.exports = {
        components: {
            'nsr-loading': require('./loading.vue'),
            paymentframe
        },
        directives: {
            coutdowntime: {
                // 当绑定元素插入到 DOM 中。
                inserted: function (el) {
                    let target = el.target
                    let myDate = new Date();
                    let now = myDate.valueOf();
                    let endtime = new Date(el.getAttribute("data-unixtime").replace(/-/g, "/")).valueOf();
                    if (now >= endtime) {
                        el.innerHTML = "拍卖结束 " + el.getAttribute("data-unixtime");
                    } else {
                        new Countdown(el, {
                            format: "距离结束 hh小时mm分ss秒",
                            lastTime: el.getAttribute("data-unixtime")
                        });
                    }
                }
            },
            collectdataupdate: {
                componentUpdated: function () {

                }
            }
        },
        watch: {
            // 如果路由有变化，会再次执行该方法
            '$route': function () {
                this.$store.state.cardData = []
                this.$store.state.page = 1
                this.$store.cardData=[];
            }
        },
        mounted: function () {
            this.$store.state.page = 1
            this.$store.state.busy=false;
            this.$store.state.cardData=[];
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

        },
        methods: {
            userLikeProduct(good_id, e){
                this.$store.dispatch('actions_userLikeProduct_function', {
                    good_id: good_id,
                    e: e,
                    page: this.$store.state.page,
                    toast: Toast
                });

            },
            userFocus(u_id, e){
                this.$store.dispatch('actions_userFocus_function', {
                    u_id: u_id,
                    e: e,
                    toast: Toast
                });

            },
            priviewimges(data, e){

                var currenturl = e.target.style.backgroundImage;
                if (currenturl.length > 5) {
                    //     currenturl =currenturl.slice(5,currenturl.length-1);
                    let target = e.target;
                    let targetindex = e.target.getAttribute('data-noimg');
                    //var index =   $(target).index();
                    var dataimgs = [];
                    for (var i = 0; i < data.length; i++) {
                        if (i == targetindex) {
                            currenturl = (data[i]).img;
                        }
                        dataimgs.push((data[i]).img);

                    }

                    window.wx && wx.previewImage({
                        current: currenturl, // 当前显示图片的http链接
                        urls: dataimgs // 需要预览的图片http链接列表
                    });
                }


            },
            chujia(a, b, c, d, e){

                var ff = rmoney(b) + rmoney(d);
                ff = Math.floor(ff * 100) / 100
                e.stopPropagation();
                e.preventDefault();
                $('.fixednumMain').find('.tipBanner .last').html(d + '' + '元');
                $('.fixednumMain').show();
                $(".fixednumMask").show().animate({
                    opacity: 0.382
                }, 100);
                $(".fixednumMain").show().animate({
                    bottom: '0px'
                }, 100, 'ease-in-out');
                $('.fixednumMain .editTxt .hover').html(ff);
                $('.fixednumMain .editTxt').attr('lastnum', d);
                $('.fixednumMain .editTxt').data('good_id', a);
                $('.fixednumMain .editTxt').attr('fixedprice', c);
                $('.fixednumMain .editTxt').attr('bidmoney', 0);
                $('.fixednumMain .editTxt').attr('ever_add_price', b);
            },
            //add data
            fetchData: function (progress) {
                var _that = this
                if (this.$store.state.page > 4 && this.$store.state.page < 70) {
                    //检查是否支付过保证金
                    axios.get('/user/singlebond', {
                        params: {
                            token: storeWithExpiration.get('token')
                        }
                    }).then(function (response) {
                        if (response.status == '200') {
                            return response.data;
                        }
                    }).then(function (json) {
                        //直接出价
                        if (json.code != '2000') {
                            MessageBox.alert('想查看更多，请支付保证金!').then(function () {
                                $('.paymengbid').show();
                                $(".fixednumMask").show().animate({
                                    opacity: 0.382
                                }, 100);
                                $(".paymengbid").show().animate({
                                    bottom: '0px'
                                }, 100, 'ease-in-out');
                                return false;
                            });
                            return false;
                        }


                    }).catch(function (ex) {
                        console.log(ex);
                    });


                    return false;
                } else if (this.$store.state.page >= 70) {
                    MessageBox.alert('不支持显示更多页数据!')
                    return false;
                }
                var cat_id = this.$route.query.cat_id || 0;
                var page = this.$store.state.page || 1;
                if (cat_id > 0) {
                    var usl = '/product/indexjinpinprolist?page=' + page + '&cat_id=' + cat_id;
                } else {
                    var usl = '/product/indexjinpinprolist?page=' + page;
                }
                this.$store.dispatch('getDataforindex', {
                    progress: progress,
                    refresh: false,
                    usl: usl
                });
            },
            bidMore(goods_id, e, page){
                page = page + 1
                this.$store.dispatch('getDataforindexbondlist', {
                    progress: this,
                    e: e,
                    refresh: false,
                    page: page,
                    goods_id: goods_id
                });
            },
            bidless(goods_id, e, page){
                this.$store.dispatch('getDataforindexbondlist', {
                    progress: this,
                    e: e,
                    refresh: false,
                    page: page + '',
                    goods_id: goods_id
                });
            },
            loadMore: function () {
                //正在努力加载
                var instance = Toast({
                    message: '正在努力加载',
                    position: 'bottom',
                    duration: 2000
                });

                var self = this;
                this.toast = instance;
                this.$store.state.busy = true;
                this.fetchData(this);
                return false;
            },
            share: function (title, img, obj, e) {
                $("#wpt-share").animate({
                    display: 'block',
                    height: $(document).height(),
                    overflow: 'hidden',
                    opacity: '0.5'
                });
                $("body,html").css({"overflow": "hidden"});
                $(document).on("click", function () {
                    var e = e || window.event,
                        target = e.target || e.srcElement;
                    if (target.id == 'wpt-share') {
                        $("#wpt-share").hide();

                    }
                    $("body,html").css("overflow", 'auto');
                    $("#wpt-share").hide();
                })
                e.preventDefault();
                e.stopPropagation();
                let linkgurl = default_domain_web + '/' + obj.path + '?goods_id=' + obj.query.goods_id
                let imageurl = '';
                if (img != null && typeof img == 'object') {
                    for (var a in img) {
                        imageurl = img[a].img
                        break;
                    }
                    commonsharejs(title, linkgurl, imageurl)
                } else {
                    commonsharejs(title, linkgurl, '')
                }

            },
            frined(e){
                wx.onMenuShareTimeline({
                    title: '', // 分享标题
                    link: '', // 分享链接
                    imgUrl: '', // 分享图标
                    success: function () {
                        alert('分享成功');
                        $("#wpt-share").hide();
                    },
                    cancel: function () {
                        $("#wpt-share").hide();
                    }
                });


            },
            gfrined(e){
                wx.onMenuShareAppMessage({
                    title: '', // 分享标题
                    desc: '', // 分享描述
                    link: '', // 分享链接
                    imgUrl: '', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        alert('分享成功');
                        $("#wpt-share").hide();
                    },
                    cancel: function () {
                        $("#wpt-share").hide();
                    }
                });

            },
            descChange(e){
                var el = e.target;
                $(el).siblings('.desc').addClass('fullDesc');
                $(el).remove();
            }
        },
        computed: mapState({
            proresults: function (state) {
                return state.cardData;
            },
            isloadingComplete: function (state) {
                return state.isloadingComplete;
            },
            busy: function (state) {
                return state.busy;
            }
            , like_products: function (state) {
                return state.like_products;
            }
        })
    };
</script>
<style scoped>
    @import '../assets/css/productlistsforjinpin.css'
</style>