<template>
<div id="productlistsforindex" class="clear">
         <div  class="saleMain"  v-infinite-scroll="loadMore"   infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <div class="saleItem"  v-for="items in proresults" >
                <div class="avatar">
               <router-link  :to="{path:'/foucswebsite',query:{userid:items.user_id}}">
                    <img :src="items.head_pic" width="90%">
                </router-link>
                    <span  class="focus"  @click="userFocus(items.user_id,$event)">关注</span>
                </div>
                <div class="saleInfo icon_lists">
                    <div class="nickname">

                    <div v-if="items.level == '1'">
                        <div class="icon iconfont verifyState1 membercolorv1">&#xe62f;</div>
                    </div>
                    <div v-else-if="items.level == '2'">
                        <div class="icon iconfont verifyState1 membercolorv2">&#xe62f;</div>
                    </div>
                    <div v-else-if="items.level == '3'">
                         <div class="icon iconfont verifyState1 membercolorv3">&#xe62f;</div>
                    </div>
                    <div v-else-if="items.level == '4'">
                        <div class="icon iconfont verifyState1 membercolorv4">&#xe62f;</div>
                    </div>
                    <div v-else-if="items.level == '5'">
                        <div class="icon iconfont verifyState1 membercolorv5">&#xe62f;</div>
                    </div>
                    <div v-else>

                    </div>


                               <div v-if="items.timelevel <= '1'">
                                     <div class="icon iconfont salelevel">v1</div>
                                </div>
                                <div v-else-if="items.timelevel <= '2'">
                                     <div class="icon iconfont salelevel">v2</div>
                                </div>
                                <div v-else-if="items.timelevel <= '3'">
                                       <div class="icon iconfont salelevel">&#xe602;</div>
                                </div>
                                <div v-else-if="items.timelevel<= '4'">
                                     <div class="icon iconfont salelevel">&#xe604;</div>
                                </div>
                                <div v-else-if="items.timelevel <= '5'">
                                      <div class="icon iconfont salelevel">&#xe605;</div>
                                </div>
                                <div v-else-if="items.timelevel <= '6'">
                                      <div class="icon iconfont salelevel">&#xe62d;</div>
                                </div>
                                <div v-else-if="items.timelevel <= '6'">
                                      <div class="icon iconfont salelevel">&#xe62e;</div>
                                </div>
                                <div v-else>

                                </div>





                    </div>
                    <div class="title">{{items.nickname}}</div>
                    <div class="desc">{{items.goods_content}} </div>
                    <div  @click="descChange($event)" class="descChange">全文</div>
                    <div class="imgList">
                        <div @click="priviewimges(items.img,$event)"   :data-noimg="index" class="lazyLoad" v-for="(item,index) in items.nowaterimg"  v-bind:style="{ backgroundImage: 'url(' + item.img + ')' }"></div>
                    </div>
                    <div class="createTime freePost">
                        <img src="/static/img/freepost.png" class="freePost">
                        <img src="/static/img/enableReturn.png" class="enableReturn">
                        <img src="/static/img/baozhengjin.png?t=3" class="hasBzj">
                        <div  img="" :saleid="items.goods_id"  @click="share($event)" class="shareIt">分享</div>
                        <div  @click="userLikeProduct(items.goods_id,$event)"  :id="['likeIt_'+items.goods_id]" :class="['likeIt saleuri'+items.goods_id]">{{items.likecount}}</div>
                        <div class="popularity" >{{items.click_count}}</div>
                    </div>
                    <div class="likeBox" style="" :id="['likeBox_'+items.goods_id]">
                        <label class="likeRow">
                              <router-link   v-for="itemcoll in items.collectdata"  class="likeAvatar"  v-bind:style="{backgroundImage:'url('+itemcoll.head_pic+')'}"  to="/shop/itemcoll.user_id"   ></router-link>
                        </label>
                    </div>
                    <div :id="['tmpLikeBox_'+items.goods_id]" class="tmpLikeBox"></div>
                    <div class="bidBtns"  >
                        <div class="bidBtn"  saleid="items.goods_id" increase="items.every_add_price" bidbzj="items.reserveprice">
                            <div  @click.stop="chujia(items.goods_id,items.every_add_price,items.start_price,300,$event)"  :class="['endTime  saleEndTime_'+items.goods_id]">
                                <span :data-unixtime="items.endTime" v-coutdowntime></span>
                                <em>出&nbsp;&nbsp;价</em>
                            </div>

                            <div class="updateBid" :saleid="items.goods_id">
                                <button>
                                    <i class="newbidTM">更新</i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="moneyInfo">
                        <div>
                            <span>{{items.start_price}}</span>
                        </div>
                        <div>
                            <span>{{items.every_add_price}}</span>
                        </div>
                        <div>
                            <span>{{items.reserveprice}}</span>
                        </div>
                    </div>

                    <div class="moneyInfoPrice"> </div>
            </div>
            </div>
		</div>

        <div   class="wpt-share">
            <div id="sub-share" class="share-box fill-ip">
                <div  class="title"><h1>分享给小伙伴</h1></div>
                <ul>
                    <li>
                        <a @click="frined($event)" class="bt-share bt-sharef" href="javascript:;"><div class="icon" ></div><span>分享朋友圈</span></a>
                    </li>
                    <li>
                        <a  @click="gfrined" class="bt-share bt-sharefg" href="javascript:;"><div class="icon"></div><span>分享好友</span></a>
                    </li>

                </ul>
            </div>
        </div>

        <div id="wpt-share" class="wptShare">
                <div class="wptMask" style="opacity:0.7" ></div>
                <div class="shareTip"></div>
         </div>
                <paymentframe></paymentframe>
		</div>
</template>

<script>
var Countdown =require('js-countdown');
    import {
            mapState
    } from 'vuex';
    import {Toast} from 'mint-ui';
    import {rmoney,userLikeProduct_function} from "../assets/js/common_function.js"
var paymentframe  = require('./usercenter/payment.vue');
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

                   new Countdown(el,{
                                              format: "距离结束 hh小时mm分ss秒",
                                              lastTime: el.getAttribute("data-unixtime")
                                          });

             }
          },
          collectdataupdate:{
                componentUpdated:function(){

                }
          }
        },
         watch: {
            // 如果路由有变化，会再次执行该方法
            '$route': function () {
                              this.$store.state.cardData= []
                              this.$store.state.page= 1
                              this.fetchData(this)
                            }


          },
        mounted: function () {
            var that=this
            var userid= this.$route.query.userid;
            this.$nextTick(function () {
                that.fetchData(that,userid);
            })
        },
        methods: {
            userLikeProduct(good_id,e){
                              this.$store.dispatch('actions_userLikeProduct_function', {
                                                         good_id: good_id,
                                                         e: e,
                                                         page: this.$store.state.page,
                                                         toast:Toast
                                                     });

            },
            userFocus(u_id,e){
                              this.$store.dispatch('actions_userFocus_function', {
                                                         u_id: u_id,
                                                         e: e,
                                                         toast:Toast
                                                     });

            },
            priviewimges(data,e){

                   var currenturl = e.target.style.backgroundImage;
                   if(currenturl.length>5){
                 //     currenturl =currenturl.slice(5,currenturl.length-1);
                   let target =e.target;
                   let targetindex =e.target.getAttribute('data-noimg');
                   //var index =   $(target).index();
                    var dataimgs =[];
                      for(var i=0;i<data.length;i++){
                      if(i==targetindex){
                        currenturl =(data[i]).img;
                      }
                          dataimgs.push((data[i]).img);

                      }

                                       window.wx &&  wx.previewImage({
                                            current: currenturl, // 当前显示图片的http链接
                                                 urls:dataimgs // 需要预览的图片http链接列表
                                       });
                   }


            },
            chujia(a,b,c,d,e){
            var ff =rmoney(b)+rmoney(c);
           ff =  Math.floor(ff * 100) / 100

            $('.fixednumMain').find('.tipBanner .last').html(c+''+'元');

                $('.fixednumMain').show();
                $(".fixednumMask").show().animate({
                opacity : 0.382
                }, 100);
                $(".fixednumMain").show().animate({
                bottom : '0px'
                }, 100, 'ease-in-out');

                $('.fixednumMain .editTxt .hover').html(ff);
                $('.fixednumMain .editTxt').attr('lastnum',d);
                $('.fixednumMain .editTxt').data('good_id',a);
                $('.fixednumMain .editTxt').attr('fixedprice',c);
                $('.fixednumMain .editTxt').attr('bidmoney',0);

                e.stopPropagation();
                e.preventDefault();
            },
            //add data
            fetchData: function (progress,userid) {
             if(this.$store.state.page>6){
                     return false;
                }
                this.$store.dispatch('getDataforindex', {
                    progress: progress,
                    refresh: false,
                    page: this.$store.state.page
                });
            },
            loadMore: function () {
                var self = this;
                self.busy = true;
            /*    var saleMain = document.querySelector('.saleMain')
                var height = saleMain.clientHeight;
                if(this.$store.state.page>=1){
                    if(height==this.$store.state.addheight){
                         return false;
                    }
                }
                */
                self.busy = false
                this.fetchData(this);
            },
            share:function(e){
                e.preventDefault();
                e.stopPropagation();
           $("#wpt-share").animate({
                display:'block',
                height: $(document).height(),
                overflow:'hidden',
                opacity:'0.5'
              });
            $("body,html").css({"overflow":"hidden"});
             $(document).on("click",function(){
                                var e = e|| window.event,
                                target = e.target || e.srcElement;
                                if(target.id=='wpt-share'){
                                    $("#wpt-share").hide();

                                }

                          $("body,html").css("overflow",'auto');
                         $("#wpt-share").hide();
                    e.preventDefault();
                    e.stopPropagation();
  })
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
             var el =   e.target;


                $(el).siblings('.desc').addClass('fullDesc');
              $(el).remove();




         }
        },
        computed: mapState({
            proresults: function(state) {
               return state.cardData;
            },
            isloadingComplete: function(state) {
                return state.isloadingComplete;
            },
            busy: function(state) {
                return state.busy;
            }
        })
    };





</script>

<style scoped>
 @import '../assets/css/productlistsforindex.css'
</style>