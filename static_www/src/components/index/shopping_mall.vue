
<template>
    <div  class="shop_mall">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p v-if="this.$route.query.state==1" style="font-size: .3rem" slot="center">关于{{this.$route.query.keywords}}搜索结果</p>
            <template v-else>
                <p  class="search02"  slot="center" @click="search"><img  src="@/assets/images/search.png">请输入您要搜索的商品</p>
                <p class="search01" slot="right"   @click="search"></p>
            </template>

            <!--<yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>-->
        </yd-navbar>
        <!--底部导航结束-->
        <yd-infinitescroll :callback="getproductlist" ref="infinitescrollDemo">
            <yd-list theme="3" slot="list">
                <yd-list-item v-for="(item, key) in list" :key="key">
                    <img  slot="img" v-on:click.prevent="clickhref(item.goods_id)" :src="getCurlofimgUsenoAuth(item.original_img)"/>
                    <span slot="title">{{item.goods_name}}</span>
                    <yd-list-other slot="other">
                        <div>
                            <span class="demo-list-price"><em>¥</em>{{item.start_price}}</span>
                        </div>
                    </yd-list-other>
                    <yd-list-other slot="other">
                        <div class="user_title_mall">
                            <p><img :src="item.user_msg.head_pic"><span>{{item.user_msg.nickname|subTime}}</span> </p>
                            <ul>
                                <li>
                                    <yd-countdown :time="item.endTime-date" timetype="second"></yd-countdown>
                                  </li>
                            </ul>
                        </div>
                    </yd-list-other>

                </yd-list-item>
            </yd-list>
            <span slot="doneTip">首页仅展示部分商品~~</span>

        </yd-infinitescroll>
        <div id="childcontent">
            <router-view></router-view>
        </div>
        <span v-show="show" slot="doneTip">暂没有关于{{this.$route.query.keywords}}商品</span>
    </div>
</template>


<script type="text/babel" scoped>
    import Vue from 'vue';
    let nsr_loading = require('@/components/loading').default;
    let listView = require('@/components/listview.vue').default;
    import {InfiniteScroll} from 'vue-ydui/dist/lib.rem/infinitescroll';
    /* 使用px：import {InfiniteScroll} from 'vue-ydui/dist/lib.px/infinitescroll'; */

    Vue.component(InfiniteScroll.name, InfiniteScroll);
    export default {
        data: function () {
            return {
                busy:false,
                transitionName: 'slide-right',
                showlists: false,
                list: [],
                page:1,
                date:new Date()/1000,
                show:false
            }
        },
        watch: {
            '$route' (to, from) {
                let isBack = this.$router.isBack  //  监听路由变化时的状态为前进还是后退
                if(isBack) {
                    this.transitionName = 'slide-right'
                } else {
                    this.transitionName = 'slide-left'
                }
                this.$router.isBack = false
            }
        },
        components: {
            'nsr-loading': nsr_loading,
            listView
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            getCurlofimgUsenoAuth(a, b, c) {
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            clickhref(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/index/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },
            getCurlofimgUsenoAuth(a, b, c){
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            search() {
                this.$router.push({name: 'seach_index_link'})
                //this.$dialog.toast({mes: '搜索：' + this.keywords});
                // this.$router.push({name: 'class_list_link',query:{keywords:keywords}})
            },
            getproductlist(){
                var ko =this.$route.query.keywords
                var id=''
                var that = this;
                if(this.$route.query.keywords!=''){
                   if( this.$route.query.id!=''){
                       var id=this.$route.query.id
                   }
                    that.$axios.get('/goods/goods_list', {
                        params: {
                            keywords:ko,
                            token: storeWithExpiration.get('token'),
                            page:that.page,
                            id:id
                        }
                    }) .then(function (response) {
                        if (response.status == '200') {
                            return response.data;
                        }
                    }).then(function (json) {
                        const _list = json.data.goods_list;
                        if( json.data.goods_list==""){
                            that.show=true
                        }
                        that.list = [...that.list, ..._list];
                        that.page++;

                        //提交到父类
                        that.$emit('refresh',[]);//select事件触发后，自动触发showCityName事件
                        if (_list.length < that.pageSize || that.page <  1) {
                            /* 所有数据加载完毕 */
                            return;
                        }

                        return false;

                    }).catch(function (ex) {

                    });
                } else{
                    var that = this;
                    that.$axios.get('/goods/goods_list', {
                        params: {
                            token: storeWithExpiration.get('token'),
                            page:that.page,
                        }
                    }) .then(function (response) {
                        if (response.status == '200') {
                            return response.data;
                        }
                    }).then(function (json) {
                        const _list = json.data.goods_list;
                        that.list = [...that.list, ..._list];
                        that.page++;
                        //提交到父类
                        that.$emit('refresh',[]);//select事件触发后，自动触发showCityName事件
                        if (_list.length < that.pageSize || that.page <  1) {
                            /* 所有数据加载完毕 */
                            return;
                        }
                        return false;

                    }).catch(function (ex) {

                    });
                }
            },


            camera: function () {
                this.$router.push({path: '/fabuc'})
            }
        },
        created:function(){
            this.getproductlist();
            // 显示B
            setTimeout(() => {
                this.showlists = true;
            }, 10);
        },
        mounted: function () {

        },
        filters: {
            subTime: function (val) {
                val = val || '';
                if(val!=undefined){
                    return `${val.substring(0,1)}**${val.substring(val.length-1)}`
                    //return val.substring(0, 2);
                }else{
                    return val;
                }
            },
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
                return dayNum
            }
        },

    }
</script>

<style scoped>
    .yd-list-img {
        height: 0!important;
        width: 100%;
        padding: 50% 0!important;
        overflow: hidden;
    }
    .search02{ width: 100%; background: #eee; height: .65rem;  text-align: left;  padding-left: .15rem;  padding-top: .18rem; color: #999}
    .search02 img{vertical-align: top; margin-right: .1rem; width: .35rem; height: .35rem;}
    .search01{ position: relative;background: #eee;height: .65rem;   width: 100%;  right: .5rem;}
    .shop_mall{ padding-top: 1rem;}
    .Router {
        position: absolute;
        width: 100%;
        transition: all .8s ease;
        top: 40px;
    }

    .slide-left-enter,
    .slide-right-leave-active {
        opacity: 0;
        -webkit-transform: translate(100%, 0);
        transform: translate(100%, 0);
    }

    .slide-left-leave-active,
    .slide-right-enter {
        opacity: 0;
        -webkit-transform: translate(-100%, 0);
        transform: translate(-100% 0);
    }
    .demo-list-price{color: #af773e;  line-height: .5rem;}
    .user_title_mall{height: .8rem; width: 100%;}
    .user_title_mall p{ width: 40%; text-align: left; float: left;}
    .user_title_mall p span{line-height: .8rem;vertical-align: middle}
    .user_title_mall p img{ width: .4rem;vertical-align: middle; margin-right: .05rem;}
    .user_title_mall ul{ width: 60%; float: left;line-height: .8rem;}
    .user_title_mall ul li{  margin-right: .15rem; width: 100%; text-align: right;}
    .user_title_mall ul li img{ width: .3rem;vertical-align: middle}
</style>



