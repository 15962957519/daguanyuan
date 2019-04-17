<template>
            <!-- 商品区列表 -->
            <div class="saleMain" style="margin-top:0;" data-flag=""  v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
                <div class="saleItem sale273826" v-for="result in poresults">
                <div class="avatar">
                    <a href="/individual/8545.html" target="top"> <img src="http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020245593" width="90%"></a>
                </div>
                <div class="saleInfo">
                    <div class="nickname">
                        <div class="verifyState1"></div>
                        <div class="salelevel lv1"></div>{{result.nick_name}}
                    </div>
                    <div class="title">{{result.title}}</div>
                    <div class="desc"> {{result.description}}</div>
                    <div class="descChange">全文</div>
                    <div class="imgList">
                   < img    v-for="result in results" />
                        <img class="gallery" rel="rel273826" src="{{result.pic}}" width="100%" alt="" style="height: 92px;">
                        <img class="gallery" rel="rel273826" src="http://7xlbw7.com2.z0.glb.qiniucdn.com/07d4110b1477e1c41813f6bf4bd9723e-mobile" width="100%" alt="" style="height: 92px;">
                    </div>
                    <div class="createTime freePost">
                        <img src="/static/img/freepost.png" class="freePost">
                        <img src="/static/img/enableReturn.png" class="enableReturn">
                        <img src="/static/img/baozhengjin.png?t=3" class="hasBzj">
                        <div uri="/index.php/main/index/userid/8545/publishid/273826" img="http://7xlbw7.com2.z0.glb.qiniucdn.com/9da811a26b6a2a1182ad956a58ddaf02" saleid="273826" class="shareIt">分享</div>
                        <div id="likeIt_273826" class="likeIt" saleuri="273826">106</div>
                        <div class="popularity">961</div>
                    </div>
                    <div class="likeBox" style="" id="likeBox_273826">
                        <label class="likeRow">
                            <a href="" target="top" class="likeAvatar" style="background-image: url(http://wx.qlogo.cn/mmopen/6PMibiaabqEwLmC5EPc9S948RI6lAeon2oXAdpibRZRxodtxZmw44gNpLQaECf6E9dJI99vZsqSLKMdUbP8wb7C1SNGsOhzg6h8/132)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/1422367_2014124017306.jpg)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020342616)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226019978421)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020402600)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/56effd628986a186.jpeg!200x200.jpg)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020345372)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/1467622602041464)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/1467614166820177)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/1467622602032544)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676141668206168)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/8c866e9033f52eb9!200x200.jpg-132)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020253403)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676141668193734)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/10d896427fe09318!200x200.jpg-132)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020017370)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020311391)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020234985)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020344022)"> </a><a href="" target="top" class="likeAvatar" style="background-image: url(http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020199076)"> </a></label>
                    </div>
                    <div id="tmpLikeBox_273826" class="tmpLikeBox"></div>
                    <div class="bidBtns">
                        <div class="bidBtn" uri="/uri/1508171457WO6uXQ" saleid="273826" increase="100" bidbzj="0">
                            <div class="endTime saleEndTime_273826">
                                <span>结束时间:<em>11</em>时<em>25</em>分<em>53</em>秒</span>
                                <em>出&nbsp;&nbsp;价</em>
                            </div>
                            <script type="text/javascript">
                                saleEndTimeArr['saleEndTime_273826'] = 1;
                                saleEndTimeArr['time_saleEndTime_273826'] = 41716;
                                saleEndTimeArr['setInterval_saleEndTime_273826'] = setInterval(function () {
                                    saleEndTimeout('saleEndTime_273826')
                                }, 1000);
                            </script>
                            <div class="updateBid" saleid="273826">
                                <button>
                                    <i class="newbidTM"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="moneyInfo">
                        <div>
                            <span>5397</span>
                        </div>
                        <div>
                            <span>100</span>
                        </div>
                        <div>
                            <span>0</span>
                        </div>
                    </div>

                    <div class="moneyInfoPrice">

                    </div>
                    <div class="bidList">

                        <div class="ddli user8880">
                            <div class="state">
                                <div class="bidTime">16-12-20 22:02:55</div>
                                <div class="bidState">
                                    <span class="bidLeader"></span>
                                </div>
                            </div>
                            <div class="avatar">
                                <a href="" target="top"><img src="http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020371351"></a>
                            </div>
                            <div class="bidUser">
                                <div class="name">
                                    <a href="" target="top">闷声装个死</a>
                                </div>
                                <div class="price" money="6297">￥6297元</div>
                            </div>
                        </div><div class="ddli user1729">
                        <div class="state">
                            <div class="bidTime">16-12-20 20:33:12</div>
                            <div class="bidState">
                                <span class="bidOut"></span>
                            </div>
                        </div>
                        <div class="avatar">
                            <a href="" target="top"><img src="http://7xlbw7.com2.z0.glb.qiniucdn.com/01.jpg"></a>
                        </div>
                        <div class="bidUser">
                            <div class="name">
                                <a href="" target="top">嘿！老头</a>
                            </div>
                            <div class="price" money="6197">￥6197元</div>
                        </div>
                    </div><div class="ddli user8362">
                        <div class="state">
                            <div class="bidTime">16-12-20 20:25:45</div>
                            <div class="bidState">
                                <span class="bidOut"></span>
                            </div>
                        </div>
                        <div class="avatar">
                            <a href="" target="top"><img src="http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020055214"></a>
                        </div>
                        <div class="bidUser">
                            <div class="name">
                                <a href="" target="top">七方堂</a>
                            </div>
                            <div class="price" money="6097">￥6097元</div>
                        </div>
                    </div><div class="ddli user8365">
                        <div class="state">
                            <div class="bidTime">16-12-20 16:20:21</div>
                            <div class="bidState">
                                <span class="bidOut"></span>
                            </div>
                        </div>
                        <div class="avatar">
                            <a href="" target="top"><img src="http://7xlbw7.com2.z0.glb.qiniucdn.com/14676226020064019"></a>
                        </div>
                        <div class="bidUser">
                            <div class="name">
                                <a href="" target="top">嘉兴堂</a>
                            </div>
                            <div class="price" money="5997">￥5997元</div>
                        </div>
                    </div><div class="ddli user8149">
                        <div class="state">
                            <div class="bidTime">16-12-20 15:43:40</div>
                            <div class="bidState">
                                <span class="bidOut"></span>
                            </div>
                        </div>
                        <div class="avatar">
                            <a href="" target="top"><img src="http://7xlbw7.com2.z0.glb.qiniucdn.com/14676141668205470"></a>
                        </div>
                        <div class="bidUser">
                            <div class="name">
                                <a href="" target="top">瑰石坊</a>
                            </div>
                            <div class="price" money="5897">￥5897元</div>
                        </div>
                    </div>
                        <div data-id="273826" class="bidMore" page="1">查看更多</div>					</div>
                </div>

            </div>

            </div>
            </template>

<script>
    import {
            mapState
    } from 'vuex';

    module.exports = {
        data: function() {
            return {
                probusy: false,
                proisloadingComplete: false,
                poresults: []
            }
        },
        components: {
            'nsr-loading': require('../components/loading.vue'),
        },
        mounted: function() {
            this.$nextTick(function() {
                this.fetchData(this);
            })
        },
        methods: {
            fetchData: function(progress) {
                this.$store.dispatch('getData', {
                    progress: progress,
                    refresh: false
                });
            },
            loadMore: function() {
                this.fetchData(this);
            }
        },
        computed: mapState({
            poresults: function(state) {
                return state.cardData;
            },
            proisloadingComplete: function(state) {
                return state.isloadingComplete;
            },
            probusy: function(state) {
                return state.busy;
            }
        })
    }



</script>

<style>


</style>