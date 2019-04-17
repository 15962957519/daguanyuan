
<style scoped>
    .l{
        float: left;
    }
    .r{
        float:right;
    }
    .pm{
        padding-left:5%;
        width:36% !important;
    }
    .border:after{
        position: absolute;
        content: '';
        background-color: #D9D9D9;
    }
    .border.horizonBottom:after{
        width:100%;
        height:1px;
    }
    .border.horizonBottom:after{
        left:0;
        bottom:0;
    }
    .orderlists{
    width:100%;
    max-width:640px;
}
        .orderlists_items{
            margin-top: 10px;
            padding-top:5px;
            background-color:#fff;
            height:170px;
        }
     .orderlists_items  .imgList div{
        width: 90px;
        height: 90px !important;
        overflow: hidden;
        background-repeat: no-repeat;
        background-size: contain;
        margin-bottom: 15px;
        background-position: 50%;
    }
     .orderlists_items_info{
            position: relative;
                margin-top: 10px;
     }
    .orderlists_items_info_header div img{
        width: 30px !important;
        height: 30px!important;
        overflow: hidden;
        float:left;
        background-repeat: no-repeat;
        background-size: contain;
        background-position: 50%;
    }

.orderlists_items_info_header .arrow{
    font-size: 22px;
    display: block;
    width: 25px;
    height: 30px;
    color: #cfcfcf;
     background:#fff url(../../assets/img/headerjiantou.png) no-repeat;
    background-position: 50% 50%;
    background-size: contain;
    margin-left: 8px;
    line-height: 30px;
    overflow: hidden;
}
    .orderdesc{
    margin-right: 20px;
    color: red;
    font-size: 16px;
    font-weight: 600;
    }
    .payment{
        height:30px;
            text-align: center;
            color: #fff;
        line-height:30px;
        background-color:#fe0100;

    }
    .hiddendesc{
     overflow:hidden;
    }
</style>
<template>
    <div class="orderlists clearfix" v-infinite-scroll="loadMore"   infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <div class="orderlists_items" v-for="items in proresults">
                    <div class="orderlists_items_info_header clearfix ">
                     <router-link  class="pm l" tag="div" :to="{path:'/foucswebsite',query:{userid:items.sale_user_id}}">
                        <img :src="items.head_pic"><i  class="arrow"></i>
                    </router-link><div class="r orderdesc">
                       <span>{{items.statusdesc}}</span>
                    </div></div>
                    <div class="orderlists_items_info clearfix">

                        <div class="pm l"><div class="imgList">
                           <router-link   :to="{path:'/mymain',query:{goods_id:items.goods_id,type:2}}">
                                  <div   :data-noimg="index" class="lazyLoad"  v-for="(item,index) in items.nowaterimg" v-lazy:background-image="item.img">
                                   </div>
                            </router-link>

                        </div></div>

                        <div class="l hiddendesc">
                            <h1 >{{items.goods_name}}</h1>
                           <p><span>成交金额:</span><span>{{items.bid_price}}</span></p>

                           <template v-if="items.order_status==1 || items.order_status==0">
                          <router-link :to="{path:'/paymenting/orderpaymenting',query:{good_id:items.goods_id,order_id:items.order_id,order_sn:items.order_sn}}"  tag="p" class="payment">
                            <span> 去支付:</span><span>{{items.bid_price}}</span>
                          </router-link>
                           </template>
                         <template v-elseif="items.order_status>2">
                                    <p>{{items.statusdesc}}</p>
                           </template>
                           <p><span>订单时间</span><span>{{items.add_time}}</span></p>
                        </div>
                    </div>
            </div>
             <copyright></copyright>
    </div>
</template>
<script>
import { MessageBox,Navbar, TabItem,Indicator, Actionsheet ,Toast } from 'mint-ui';
import { getuserfocus ,isEmptyObject } from '../../assets/js/common_function.js';
import {mapState } from 'vuex';
var copyright  = require('../copyright.vue');


var config = require('../../../config')
    export default {
        data(){
            return {
                busy:false,
                toast:null,
                proresults:[],
                page:1,
            }
        },
        components:{
            copyright
        },
        props: ['msgfromfa'],
          watch: {
            // 如果 question 发生改变，这个函数就会运行
          },
        mounted: function() {

            },
            methods:{
                loadMore: function () {
                    //正在努力加载
                     Indicator.open({
                                                text:'正在努力加载',
                                                spinnerType: 'fading-circle'
                                           });
                                            setTimeout(() => Indicator.close(), 1000);
                    var self = this;
                  //  this.toast = instance;
                    this.busy = true;
                    this.fetchData(this);
                    return false;
                },
                fetchData: function (progress) {
                    var _that = this
                     _that.busy = true;
                        axios.get('/user/getmyorder', {
                            params: {
                                token: storeWithExpiration.get('token'),
                                issale: _that.msgfromfa
                            }
                        }).then(function (response) {
                            if (response.status == '200') {
                                return response.data;
                            }
                        }).then(function (json) {
                                if(isEmptyObject(json.data)){
                                _that.busy = true;
                                 return false;
                                }
                            //   _that.proresults =  json.data
                               _that.proresults =  _that.proresults.concat(json.data);
                          //  _that.busy = false;
                        }).catch(function (ex) {
                            console.log(ex);
                        });
                        return false;
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


                }
            },
             computed:{




             }
    }
</script>

