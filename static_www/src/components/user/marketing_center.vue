<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">推广管理</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--在售和下架-->
        <yd-tab active-color="#af773e">
            <yd-tab-panel label="资讯推广" >
                <template>
                        <!--开始-->
                        <div style="width: 100%;margin-top: 10px;background: #eee;">
                            <div class="goods-warp" style="width:100%;" v-for="item in goods_speard">
                                <div class="goods-img">
                                    <img :src="item.original_img"/>
                                    <div class="end-time">
                                        <template>
                                            <yd-countdown :time="item.endTime" format="{%d}天:{%h}时:{%m}分:{%s}"></yd-countdown>
                                        </template>
                                    </div>
                                </div>
                                <div class="goods-text">
                                    <div class="title">{{item.goods_name}}</div>
                                    <div class="cont">{{item.goods_content}}</div>
                                    <div class="btm">
                                        <span class="left"> </span>
                                        <div class="right">
                                            <span>¥{{item.shop_price}}</span>
                                            <!--<img src="__STATIC__/img/shijian_xinpin.png" style="width: 20px">-->
                                            <span><img style="width:.35rem ;vertical-align: bottom" src="@/assets/images/glance.png">{{item.click_count}}</span>&nbsp;&nbsp;&nbsp;


                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                </template>
            </yd-tab-panel>
            <yd-tab-panel label="二维码推广">
                <img :src="qrcode"/>
            </yd-tab-panel>
        </yd-tab>
    </div>
</template>

<script>
    var env = require('@/../config/dev.env');
    export default {
        data() {
            return {
                article_id:1,
                list:{},
                goods_speard:{},
                user_head_pic:{},
                tab2: 0,
                qrcode:'',

            }
        },
        mounted: function () {
            var that= this
            that.showData()
            var token = storeWithExpiration.get('token');
            this.qrcode = env.default_domain_api+"user/getuserimage?type=1" + "&token=" + token;
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            showData(){
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/news_detail?token=" + token + "&article_id=" + 1;
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        return res.data.data;
                    }
                }).then(function (res) {
                    _that.list=res.new_detail
                    _that.goods_speard=res.goods_speard
                    _that.user_head_pic=res.user_head_pic
                }).catch(function (error) {
                    console.log(error)
                })
            },

        },
        filters:{
            date(time){
                let oldDate = new Date(time*1000)
                let newDate = new Date()
                var dayNum = "";
                var getTime = (newDate.getTime() - oldDate.getTime())/1000;
                if(getTime < 60*5){
                    dayNum = "刚刚";
                }else if(getTime >= 60*5 && getTime < 60*60){
                    dayNum = parseInt(getTime / 60) + "分钟前";
                }else if(getTime >= 3600 && getTime < 3600*24){
                    dayNum = parseInt(getTime / 3600) + "小时前";
                }else if(getTime >= 3600 * 24 && getTime < 3600 * 24 * 30){
                    dayNum = parseInt(getTime / 3600 / 24 ) + "天前";
                }else if(getTime >= 3600 * 24 * 30 && getTime < 3600 * 24 * 30 * 12){
                    dayNum = parseInt(getTime / 3600 / 24 / 30 ) + "个月前";
                }else if(time >= 3600 * 24 * 30 * 12){
                    dayNum = parseInt(getTime / 3600 / 24 / 30 / 12 ) + "年前";
                }
                let year   = oldDate.getFullYear();
                let month  = oldDate.getMonth()+1;
                let day    = oldDate.getDate();
                let hour   = oldDate.getHours();
                let minute = oldDate.getMinutes();
                let second = oldDate.getSeconds();
                return dayNum+" "+month+"-"+day+" "+hour+":"+minute
            }
        }
    }
</script>

<style scoped>
    .margin{ padding-top: 1rem;}
    .goods-warp{
        position:relative;
        padding:6px 7px;
        margin:7px 0;
        background: #fff;
        height: 2rem;
    }
    /* line 102, ../scss/news_item.scss */
    .news_item_container .xiangqing {
        width: 100%;
        background: #fff;
        margin-top: 10px;
    }

    /* line 107, ../scss/news_item.scss */

    .news_item_container .xiangqing dl {
        padding: 10px;
    }

    /* line 110, ../scss/news_item.scss */

    .news_item_container .xiangqing dl dt {
        border-bottom: 1px #ccc dashed;
        font-size: 21px;
        line-height: 32px;
        /* height: 60px; */
        font-weight: 100;
    }

    /* line 117, ../scss/news_item.scss */

    .news_item_container .xiangqing dl dd {
        color: black;
        margin-top: 10px;
        font-size: 14px;
    }

    .xwxqjrtg{text-align: center; font-size: 1em; margin-top: 10px;margin-bottom: 10px;}
    .xwxqjrtg1 img{width: 65px; height:65px;margin-bottom: 10px;}
    .goods-img{
        position:absolute;
    }
    .goods-img>img{
        /*width: 90px;*/
        height: 90px;
        box-shadow: 5px 5px 5px #f1f1f1;
    }
    .end-time{
        font-size: 10px;
        position: absolute;
        bottom: 0;
        text-align: center;
        width: 100%;
        background: #af773e;
        COLOR: #FFF;
        opacity: 0.8;
    }

    .goods-text{
        text-align: left;
        margin-left: 100px;
    }
    .title{
        margin-top: 5px;
        font-size: 16px;
    }
    .cont{
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp:2;
        overflow: hidden;
        font-size: 13px;
        line-height:17px;
        margin-top: 6px;
        height: 35px;
    }
    .btm{
        margin-top: 4px;
        height: 17px;
        font-size: 13px;
        color: #E14857;
    }
    .left{
        width: 36%;
        overflow: hidden;
    }

</style>