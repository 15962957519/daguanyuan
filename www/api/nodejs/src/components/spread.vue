    <style type="text/css">
      #spread{     width: 100%; background:#efefef; margin:0 auto; }
      .spread_nav{     width: 100%; height:36px; background:#efefef; }
      .spread_line{     width: 90%; height:17px;  :#252525; border-bottom:2px solid #252525; margin: 0 auto;  }
      .spread_line p{text-align: center; margin: 0 auto; width:120px;font-size:21px;  height:8px; color: #f73246; padding: 8px;  background:#efefef; line-height: 22px;}
       .shopping{width: 100%; height:93px; position: relative; margin-bottom:10px; background: #fff;  }
       .shopping_img{  width: 20%;
                           height: 80px;
                           position: absolute;
                           left: 5px;
                           text-align: center;
                           top: 5px;
                           border: 1px solid #ccc;}
        .shopping_img .endtimebg{width:94px ;height:26px;line-height:26px;font-size:12px;background-image:url(/static/img/endTimeBg.png);background-size:100% 100%;color:#FFF;margin-top:50px;text-indent:10px;bottom: 0px;position: absolute;}
       .shopping_img img{   max-width:80px;max-height:80px; *margin-top:expression((60-this.height )/2); }
       .shopping_text{     width: 70%; height: 62px; font-size: 14px;  position:absolute; left:110px; top:5px; padding:2px; overflow:hidden;}
       .shopping_text b { font-size: 15px;}
       .shopping_text p{ margin-top:0px;     font-size: 13px; }
       .shopping_look{width: 70%; height: 20px; font-size: 14px; background: #fff; position:absolute; left:110px; top:68px; padding:1px;}
       .shopping_look span{ color: #f73246;display: inline-block;float: left;font-size: 13px; }
       .shopping_look .money{background-image: url(/static/img/spread/rmb.png);
                                           width: 40%;
                                           background-repeat: no-repeat;
                                           background-size: 15px;
                                           background-position-y: 2px;
                                           text-indent: 16px;
                                           display: inline-block;}
       .shopping_look .view_num{background-image: url(/static/img/review.png);
                                    width: 30%;
                                    background-repeat: no-repeat;
                                    background-size: 15px;
                                    background-position-y: 5px;
                                    text-indent: 16px;
                                    display: inline-block;}
       .shopping_look .goods_status{background-image: url(/static/img/spread/click.png);
                                            width: 30%;
                                            background-repeat: no-repeat;
                                            background-size: 15px;
                                            background-position-y: 3px;
                                            text-indent: 16px;
                                            display: inline-block;}
       .fenxiang{  width: 100%;
                       height: 90px;
                  }
        .fenxiang .bt-share img{display:inline-block !important;}
       .fenxiang .bt-share:after{display:inline-block; width:0; height:100%; content:"center"; vertical-align:middle; overflow:hidden;}
       .fenxiang p{text-align: center; padding-top: 5px; }
       .fenxiang span{
               width: 50%;
               float: left;
               display: inline-block;
               height: 60px;
               /* background: #fff; */
               font-size: 0;
               text-align: center;
            }
        .scanning{width: 100%;
                      background: #f2d80f;
                      margin: 0 auto;
                      text-align: center;}
       .scanning p{text-align: center; padding-top:15px; font-size: 25px; color: #f73246; font-weight:bold;}
       .scanning span{width:100%;padding:20px;    }
       .scanning span img{    width: 64%;
                              margin: 0 auto;}

       .wptShare{
           display: none;
           position:fixed;
           top:0;
           opacity: 0.6;
           bottom:0;
           width:100%;
           height:100%;
           background-color:#000;
           z-index:1999;
       }

        .wptShare .wptMask{
           position:fixed;
           top:0;
           bottom:0;
           left:0;
           ringht:0;
           width:100%;
           height:100%;
           background-color:#000;
           z-index:1999;

       }

       .wptShare .shareTip{
           position:fixed;
           top:0;
           width:96%;
           height:286px;

           z-index:2000;
       }

        .fenxiang .bt-share p{
            width:60px;
            height:40px;
            line-height:40px;
        }
    </style>
<template>
    <div>
        <!-- 今日推荐标题 -->
        <div id="spread">
            <div class="spread_nav">
                    <div class="spread_line">
                     <b><p>今日推广</p></b>
             </div>
              </div>
      <!-- 商品区推荐 -->
      <div class="lists_items" v-for="items in todayprodcut">
          <router-link :to="{name:'goodsproductdetail',params:{goods_id:items.goods_id}}">
          <div class="shopping">
              <div class="shopping_img">
                 <img v-lazy="items.img[0].img" >
                 <div class="endtimebg">
                        {{items.endTimestr}}
                 </div>
              </div>
              <div class="shopping_text">
                  <b>{{items.goods_name}}</b>
                  <p v-html="items.goods_content"></p>
              </div>
               <div class="shopping_look">
                   <span class="money">{{items.start_price}}</span>
                   <span class="view_num">{{items.click_count}}</span>
                   <span class="goods_status">拍卖中</span>
               </div>
            </div>
            </router-link>
        </div>
        <!-- 商品区推荐结束 -->
        <!-- 分享开始 -->
        <div class="fenxiang">
          <p> 分享到</p>
          <span @click="frined(articles_item,$event)" class="bt-share bt-sharefg" ><img src="/static/img/spread/weixin.jpg" height="60" width="60"></span>
          <span @click="frined(articles_item,$event)" class="bt-share bt-sharef" ><img src="/static/img/spread/pengyou.jpg" height="60" width="60"></span>
        </div>
        <!-- 长按扫描 -->
        <div class="scanning">
         <p>长按指纹，一键关注！</p>
             <span><img :src="userinfodata" width="500" alt=""></span>
        </div>
        </div>
        <div id="wpt-share" class="wptShare">
                        <div class="wptMask" style="opacity:0.7" ></div>
                        <div class="shareTip"></div>
                 </div>
    </div>
</template>

<script>
 import {Toast} from 'mint-ui';
 var config = require('../../config')
 import {weixincommonjsdk,weixin} from "../assets/js/common_function.js"
 module.exports = {
    data: function () {
        return {
            todayprodcut:[],
            userinfodata:null,
            userinfo:[]
        }
    },
    mounted:function() {
        this.weixin()
        this.getuserqrode();

        this.gettodayproduct();

    },
    props: ['articles_item'],

    methods: {
     initfrined:function(items){
                    var imgUrl =document.getElementById("content").getElementsByTagName("img")[0].src;
                    var fromuserid='/'+this.userinfo.user_id||0;
                    let linkgurl = default_domain_web + '/headermessagedetail/' + items.article_id +fromuserid+'/';
                    var title = config.build.env.TODAYHEADER +  items.title; // 分享标题
                 wx.ready(function () {
                    window.wx.onMenuShareAppMessage({
                        title: title, // 分享标题
                        desc: title, // 分享描述
                        link: linkgurl, // 分享链接
                        // imgUrl: default_domain_web+'/static/img/spread/weixin.jpg', // 分享图标
                        imgUrl:imgUrl, // 分享图标
                        type: '', // 分享类型,music、video或link，不填默认为link
                        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                         success: function () {
                            //往数据插入记录
                            //article/addforwardcount
                    axios.get('article/addforwardcount',
                        {
                            params:{article_id:items.article_id},
                    }
                    ).then(function (response) {
                        if (response.status == '200') {
                            return response.data;
                        }
                    }).then(function (json) {

                    }).catch(function (ex) {

                    });

                         },
                         cancel: function () {
                         },
                         fail: function (res) {
                                 console.log(JSON.stringify(res));
                         }
                      });


                    wx.onMenuShareTimeline({
                       title:  title, // 分享标题分享给朋友
                       link: linkgurl, // 分享链接
                     //  imgUrl: default_domain_web+'/static/img/spread/pengyou.jpg', // 分享图标
                       imgUrl:imgUrl, // 分享图标
                       success: function () {
                                                 //往数据插入记录
                                                 //article/addforwardcount
                                         axios.get('article/addforwardcount',
                                             {
                                                 params:{article_id:items.article_id},
                                         }
                                         ).then(function (response) {
                                             if (response.status == '200') {
                                                 return response.data;
                                             }
                                         }).then(function (json) {

                                         }).catch(function (ex) {

                                         });
                       },
                       cancel: function () {
                             $("#wpt-share").hide();
                       }
                   });
                 })
           },
       getuserqrode(){
          var user_id = this.$route.params.user_id ||0;
                        this.userinfodata=decodeURIComponent(config.dev.env.default_domain_api)+'/user/qrcode?user_id='+user_id+'&token='+storeWithExpiration.get('token')
                    },
       frined(items,e){
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
                                               })
                                               e.preventDefault();
                                               e.stopPropagation();
       },
       gettodayproduct:function(){
           var	that =this;
           axios.get('/product/indexjextensionprolist', {
               params: {
                   token: storeWithExpiration.get('token')
               }
           }) .then(function(response) {
               if(response.status=='200'){
                   that.todayprodcut = response.data.plists.data;
                that.userinfo = response.data.user;
                that.initfrined(that.articles_item);
               }
           }).catch(function(ex) {
               console.log(JSON.stringify(ex))
           });
       },
       weixin:function(){
           weixin();
         }
    }

 }

</script>