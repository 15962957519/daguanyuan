<template>
    <div >
        <div v-on:click="share" style=' position: fixed; height: 100%; background: rgba(0,0,0,0.5);top: 0; display: none; z-index: 99999;' v-show="shareShow">
            <img style="width: 100%;" src="@/assets/images/wxshare.png" />
        </div>
        <div class="news_item_container">
            <!--底部-->
            <div class="xiangqing">
                <dl>
                    <dt style="text-align: center;font-size: 18px;color: #0e92bc"> {{ list.title }}  </dt>
                    <dd style="color: #8c8c8c; font-size: 1em">  {{ list.add_time|date }} &nbsp;来源：今日资讯</dd>
                    <dd>
                        <img :src="list.thumb" style="width: 100%;" />
                    </dd>
                    <dd v-html="list.content"></dd>
                </dl>
            </div>
        </div>
        <!--推荐-->
        <!--开始-->

        <div style="width: 100%;height: 60px;margin-top: 40px;">
            <div style="text-align: center;font-size: 20px;">今日推广</div>

            <div class="goods-warp" style="width:100%;" v-for="item in goods_speard">
                <div class="goods-img">
                    <img v-on:click.prevent="clickhref(item.goods_id)" :src="item.original_img"/>
                    <div class="end-time">
                        <yd-countdown :time="item.endTime-date" timetype="second"></yd-countdown>
                    </div>
                </div>
                <div class="goods-text">
                    <div class="title">{{item.goods_name}}</div>
                    <div class="cont">{{item.goods_content}}</div>
                    <div class="btm">
                        <span class="left"> </span>
                        <div class="right">
                            <span>¥{{item.start_price}}</span>
                            <!--<img src="__STATIC__/img/shijian_xinpin.png" style="width: 20px">-->
                            <span><img style="width:.35rem ;vertical-align: bottom" src="@/assets/images/glance.png">{{item.click_count}}</span>&nbsp;&nbsp;&nbsp;


                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

            <!--  -->
            <div class="xwxqjrtg">分享到</div>
            <div class="xwxqjrtg1" id="newsItemImg" @click="share">
                <img style=" margin-right: 1rem" src="@/assets/images/weixin.jpg">
                <img style=" margin-left: 1rem"  src="@/assets/images/pengyou.jpg">
            </div>
            <div class="xwxq_jrtg_fenxiang" @click="share" >
                <div  class="img01" > <img  :src="user_head_pic"></div>
                <div  class="img02" > <img  src="@/assets/images/erweima.png"></div>

            </div>
        </div>
    </div>

</template>

<script>
    import wx from 'weixin-js-sdk'
    export default {
        data() {
            return {
                article_id:1,
                list:{},
                shareShow:false,
                goods_speard:{},
                user_head_pic:{},
                date:new Date()/1000,
            }
        },
        mounted: function () {
            var that= this
            that.showData()
            },
        methods: {
            clickhref(goods_id){
                try {
                    this.$router.push({path: '/index/' + goods_id})
                } catch (e) {

                }
            },
            showData(){
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/news_detail?token=" + token + "&article_id=" + _that.$route.query.article_id;
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        return res.data.data;
                    }
                }).then(function (res) {
                    _that.list=res.new_detail
                   // console.log(88,res.goods_speard)
                    _that.goods_speard=res.goods_speard
                    _that.user_head_pic=res.user_head_pic
                    _that.shareconfig();
                }).catch(function (error) {
                    console.log(error)
                })
            },
            share: function () {
                this.shareShow = !this.shareShow
            },
            shareconfig:function(){
                var _that = this;
                wx.ready(function () {
                    var shareWxLink = window.location.href.split('#')[0] + 'static/html/redirect.html?app3Redirect=' + encodeURIComponent(window.location.href);

                    wx.onMenuShareTimeline({
                        title: '【艺品芳华】' + _that.list.title, // 分享标题
                        link: shareWxLink,        // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                        imgUrl: _that.list.thumb, // 分享图标
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
                        title: '【艺品芳华】' +_that.list.title, // 分享标题
                        desc: _that.list.description, // 分享描述
                        link: shareWxLink,  // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                        imgUrl:_that.list.thumb, // 分享图标
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
    .xwxq_jrtg_fenxiang{ position: relative; width: 100%;}
    .img01{ position: absolute;z-index: 11; width: .58rem; height: .58rem; top: 1.81rem; left: 1.61rem}
    .img01 img{ width: 100%;}
    .img02{ position: absolute; width: 100%  }
    .img02 img{ width: 100%; height: 4.2rem}
    .goods-warp{
        position:relative;
        padding:6px 7px;
        margin:7px 0;
        background: #fff;
    }
    /* line 102, ../scss/news_item.scss */
   .news_item_container{ background: #fff;}
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