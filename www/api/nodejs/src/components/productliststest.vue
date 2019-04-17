<template>
<div id="productlisttemplate" class="clear">
         <div  class="saleMain"  v-infinite-scroll="loadMore"   :infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <div class="saleItem"  v-for="items in proresults" >
                <div class="avatar">
                       <router-link  :to="{path:'/foucswebsite',query:{userid:items.user_id}}">
                                       <img  v-lazy="items.head_pic">
                                   </router-link>
                      <span  class="focus"  @click="userFocus(items.user_id,$event)">关注</span>
                </div>
                <div class="saleInfo icon_lists">
                    <div class="nickname">
                                                      <div v-if="items.is_authentication == '0'">
                                                                                     <div class="icon iconfont verifyState1 membercolorv">&#xe62f;</div>
                                                                                 </div>
                                                                                 <div v-else-if="items.is_authentication == '1'">
                                                                                     <div class="icon iconfont verifyState1 membercolorv ismember">&#xe62f;</div>
                                                                                 </div>
                                                                                 <div v-else-if="items.is_authentication == '1'">
                                                                                      <div class="icon iconfont verifyState1 membercolorv ismember">&#xe62f;</div>
                                                                                 </div>
                                                                                 <div v-else-if="items.is_authentication == '1'">
                                                                                     <div class="icon iconfont verifyState1 membercolorv ismember">&#xe62f;</div>
                                                                                 </div>
                                                                                 <div v-else-if="items.is_authentication == '1'">
                                                                                     <div class="icon iconfont verifyState1 membercolorv ismember">&#xe62f;</div>
                                                                                 </div>
                                                                                 <div v-else>
                                                                                        <div class="icon iconfont verifyState1 membercolorv">&#xe62f;</div>
                                                                                 </div>
                        <div class="icon iconfont salelevel">V{{items.timelevel}}</div>
                    </div>
                    <div class="nickname">{{items.nickname}}</div>
                    <div class="title">{{items.goods_name}}</div>
                    <div class="desc">{{items.goods_content}} </div>
                    <div  @click="descChange($event)" class="descChange">全文</div>
                    <div class="imgList">
                          <div @click="priviewimges(items.img,$event)"   :data-noimg="index" class="lazyLoad" v-for="(item,index) in items.nowaterimg"  v-lazy:background-image="item.img"></div>
                    </div>
                    <div class="createTime freePost">
                        <img src="/static/img/freepost.png" class="freePost">
                        <img src="/static/img/enableReturn.png" class="enableReturn">
                        <img src="/static/img/baozhengjin.png" class="hasBzj">
                        <template  v-for="(item,index) in items.nowaterimg" >
                             <div   v-if="(index==0)"  :img="item.img" :saleid="items.goods_id"  @click="share(items.goods_name,item.img,{name:'mymain',query:{goods_id:items.goods_id}},$event)" class="shareIt">分享</div>
                        </template>
                          <div  @click="userLikeProduct(items.goods_id,$event)"  :id="['likeIt_'+items.goods_id]" :class="['likeIt saleuri'+items.goods_id]">{{items.likecount}}</div>
                        <div class="popularity">{{items.click_count}}</div>
                    </div>
                    <div class="likeBox" style="" :id="['likeBox_'+items.goods_id]">
                        <label class="likeRow">
                             <router-link    v-for="itemcoll in items.collectdata"  class="likeAvatar" v-lazy:background-image="itemcoll.head_pic"  to="/shop/itemcoll.user_id"   ></router-link>
                        </label>
                    </div>
                    <div :id="['tmpLikeBox_'+items.goods_id]" class="tmpLikeBox"></div>
                    <div class="bidBtns"  >
                        <div class="bidBtn"  :saleid="items.goods_id" :increase="items.every_add_price" :bidbzj="items.reserveprice">
                                                             <template v-if="items.goods_status==2">
                                                                                   <div   :class="['endTime  saleEndTime_'+items.goods_id]">
                                                                                   <span>拍卖结束</span>
                                                                                     <em v-if="items.goods_status!=2">出&nbsp;&nbsp;价</em>
                                                                                     </div>
                                                              </template>
                                                                                  <template v-else>
                                                                                    <div  @click.stop="chujia(items.goods_id,items.every_add_price,items.start_price,items.lastnum,$event)"  :class="['endTime  saleEndTime_'+items.goods_id]">
                                                                                   <span :data-unixtime="items.endTime" v-coutdowntime></span>
                                                                                     <em v-if="items.goods_status!=2">出&nbsp;&nbsp;价</em>
                                                                                                               </div>
                                                               </template>

                            <div class="updateBid" :saleid="items.goods_id">
                                <button>
                                    <i class="newbidTM">更新</i>
                                </button>
                            </div>
                        </div>
                           <div  @click.stop="sharetofriend(items.goods_name,{path:'/mymain',query:{goods_id:items.goods_id,type:2}},items.img,$event)" v-if="goods_id" class="sharefriends">
                                                        <div>
                                                            <span>
                                                                <em>分享给朋友们</em>
                                                            </span>

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

                                                   <div class="moneyInfoPrice">
                                                   </div>
                                                   <div class="bidList">
                                                       <div class="ddli user8880" v-for="(item, index) in items.bidlists">
                                                           <div class="state">
                                                               <div class="bidTime">{{item.add_time}}</div>
                                                               <div class="bidState">
                                                                   <div v-if="index==0 && items.endTimeflag==1">
                                                                      <span class="bidHasOrder"></span>
                                                                   </div>
                                                                    <div v-else-if="index==0">
                                                                        <span class="bidLeader"></span>
                                                                     </div>
                                                                   <div v-else>
                                                                      <span class="bidOut"></span>
                                                                   </div>
                                                               </div>
                                                           </div>
                                                           <div class="avatar">
                                                              <a href="javascript:;"> <img v-lazy="item.head_pic"></a>
                                                           </div>
                                                           <div class="bidUser">
                                                               <div class="name">
                                                                  <a href="javascript:;">{{item.nickname}}</a>
                                                               </div>
                                                               <div class="price" :money="item.bid_price">￥{{item.bid_price}}元</div>
                                                           </div>
                                                       </div>
                                                       <div :data-id="items.goods_id" class="bidMore" page="1">查看更多</div>
                                                   </div>
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
    import { mapState } from 'vuex';
    import {Toast} from 'mint-ui';
    var __=require('lodash')

    var config = require('../../config')
    import {rmoney,userLikeProduct_function,isEmptyObject} from "../assets/js/common_function.js"
    var paymentframe  = require('./usercenter/payment.vue');
    Vue.filter('discount', function(value,el) {
            let target = el.target
                          let myDate = new Date();
                          let now = myDate.valueOf();
                          let endtime = new Date(el.getAttribute("data-unixtime").replace(/-/g, "/")).valueOf();
                          if(now>=endtime){
                                  el.innerHTML = "拍卖结束 "+el.getAttribute("data-unixtime");

                          }else{
                              new Countdown(el,{
                                    format: "距离结束 hh小时mm分ss秒",
                                    lastTime: el.getAttribute("data-unixtime")
                                });
                          }
    });


    module.exports = {
    data: function() {
                    return {
                         proresults:[],
                         page:1,
                         gocontinuetoget:true,
                         goods_id:false,
                         busy:false
                    }
                  },
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
                                          if(now>=endtime){
                                                  el.innerHTML = "拍卖结束 "+el.getAttribute("data-unixtime");

                                          }else{
                                              new Countdown(el,{
                                                    format: "距离结束 hh小时mm分ss秒",
                                                    lastTime: el.getAttribute("data-unixtime")
                                                });
                                          }

             }
          }
        },
         watch: {
            // 如果路由有变化，会再次执行该方法
            '$route': function () {
                              this.$store.state.cardData= []
                              this.$store.state.page= 1
                            }
          },
        mounted: function () {
          var goods_id =this.$route.query.goods_id;
                       if(typeof(goods_id) !='undefined' && goods_id>0){
                          this.goods_id =true;
                       }
        },
        methods: {
        sharetofriend(title,url,image,e){
                    let tourl ='http://w.tianbaoweipai.com'+url.path+'?goods_id='+url.query.goods_id
                    tourl = encodeURI(tourl);
                    let imageurl='';
                    if(image!=null && typeof image == 'object'){
                       for(var a in image){
                            imageurl =image[a].img
                            break;
                        }

                      this.commonsharejs(title,tourl,imageurl)
                          $("#wpt-share").animate({
                                              display:'block',
                                              height: $(document).height(),
                                              overflow:'hidden',
                                              opacity:'0.5'
                                              });
                                              $("body,html").css({"overflow-y":"hidden"});
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


                    }



        },
                    userLikeProduct(good_id,e){
                                    var good_id =good_id;
                                    var token = window.storeWithExpiration.get('token');
                                    var el =e;
                                    var dataupdate=[];
                                    var toast =Toast;
                                    var _that=this;
                                    var data=[];
                                    axios.get( decodeURIComponent(config.dev.env.default_domain_api)+'/user/userlikeproduct', {
                                        params: {
                                            token: storeWithExpiration.get('token'),
                                            goods_id: good_id
                                        }
                                    }).then(function(response) {
                                            if(response.status=='200'){
                                                return response.data;
                                            }
                                        }).then(function(json) {
                                        var message ='喜欢成功';
                                        $(el.target).css('background-position','right 4px');

                                        if(4000==json.code){
                                            message='已经喜欢';
                                        }else if(2000==json.code){

                                            _that.like_product_update({data:json.data.data,good_id:good_id,click_count:json.click_count,likecount:json.likecount});
                                        }
                                        Toast({
                                            message:message,
                                            iconClass: 'mintui mintui-success'
                                        });
                                    }).catch(function(ex) {
                                        Toast({
                                            message: '喜欢失败',
                                            iconClass: 'mintui mintui-success'
                                        });
                                    });
                    },
                    commitc(functionname,obj){
                         window.eval(functionname + "('" + obj + "')");
                    },
                    like_product_update(data){
                        var good_id=data.good_id;
                            if(this.proresults.length>0 && !isEmptyObject(data.data)){
                                var  tmpa =this.proresults;
                                for(var i=0;i<tmpa.length;i++){
                                    if( good_id>0 && tmpa[i].goods_id>0  && good_id ==tmpa[i].goods_id){
                                        tmpa[i].collectdata =data.data;
                                        tmpa[i].click_count =data.click_count;
                                        tmpa[i].likecount =data.likecount;
                                    }
                                }
                                this.proresults =tmpa;
                            }
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
                 var ff =rmoney(b)+rmoney(d);
                ff =  Math.floor(ff * 100) / 100
                     e.stopPropagation();
                     e.preventDefault();
                 $('.fixednumMain').find('.tipBanner .last').html(d+''+'元');
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
                     $('.fixednumMain .editTxt').attr('ever_add_price',b);
            },
            //add data
            fetchData: function (progress,uid,type,page,goods_id) {
                        if(this.gocontinuetoget==false ||this.page>60){
                                return false;
                        }
                        var that=this;
                        var paramtrasform ={};
                        if(uid>0){
                            paramtrasform={user_id:uid};
                        }
                          if(goods_id>0){
                            paramtrasform={goods_id:goods_id};
                        }
                        var   token= storeWithExpiration.get('token') || '';
                        if(token !=undefined  && token !=null && token!=''  && token.replace(/(^s*)|(s*$)/g, "").length >0 ){
                            paramtrasform = __.extend(paramtrasform,{token:token})
                        }


                       if(type>0){
                            paramtrasform = __.extend(paramtrasform,{type:type} )
                        }
                         that.page++;
                       axios.get('/product/indexpaimaiprolist?page='+page, {
                            params: paramtrasform
                          })
                          .then(function (response) {
                                    if(response.status==200){
                                    if(that.page<=2){
                                        that.$emit('child-say',response.data.user);
                                    }

                                            return response.data.plists;

                                     }
                          }).then(function(response){
                              if(that.page<60){

                              //   that.proresults =response.data;
                                for(var i=0;i<response.data.length;i++){
                                    that.proresults.push(response.data[i]);
                                }
                              }else{
                              that.gocontinuetoget =false;
                               return false;
                              }
                          }) .catch(function (error) {
                              console.log(error);
                          });
            },
            loadMore: function () {
                var that=this
                that.busy = true;
                var userid= this.$route.params.userid;
                var type= this.$route.params.type;
                var page =this.$route.params.page;
                var goods_id =this.$route.query.goods_id;
                if(typeof(goods_id) =='undefined'){
                   goods_id =0;
                }
                if(typeof(page) =='undefined'){
                   page =this.page;
                }
                that.fetchData(that,userid,type,page,goods_id);
                return false;
            },
            share:function(title,img,obj,e){
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

                let linkgurl=      default_domain_web+'/'+obj.name+'?goods_id='+obj.query.goods_id
                this.commonsharejs(title,linkgurl,img)
            },
            commonsharejs(title,linkgurl,img){
        title =    config.build.env.FARENAME + title + config.build.env.SALING;
              wx.onMenuShareTimeline({
                                    title: title,
                                    link:linkgurl,
                                    imgUrl: img,
                                    success: function () {
                                           // 用户确认分享后执行的回调函数
                                       },
                                       cancel: function () {
                                           // 用户取消分享后执行的回调函数
                                       }
                                })


            wx.onMenuShareAppMessage({
                title: title, // 分享标题
                desc: title, // 分享描述
                link:linkgurl, // 分享链接
                imgUrl: img, // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
wx.onMenuShareQQ({
    title: title, // 分享标题
    desc: title, // 分享描述
    link: linkgurl, // 分享链接
    imgUrl: img, // 分享图标
    success: function () {
       // 用户确认分享后执行的回调函数
    },
    cancel: function () {
       // 用户取消分享后执行的回调函数
    }
});

wx.onMenuShareWeibo({
    title:title, // 分享标题
    desc: title, // 分享描述
    link: linkgurl, // 分享链接
    imgUrl: img, // 分享图标
    success: function () {
       // 用户确认分享后执行的回调函数
    },
    cancel: function () {
        // 用户取消分享后执行的回调函数
    }
});
wx.onMenuShareQZone({
    title: title, // 分享标题
    desc:title, // 分享描述
    link:linkgurl, // 分享链接
    imgUrl: img, // 分享图标
    success: function () {
       // 用户确认分享后执行的回调函数
    },
    cancel: function () {
        // 用户取消分享后执行的回调函数
    }
});




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
        computed:{
          _proresults: {
                    set: function(value) {
                        this.proresults = value;
                    },
                    get: function() {
                        return this.proresults
                    }
                }

        }
    };





</script>

<style scoped>
 @import '../assets/css/productlists.css'
</style>