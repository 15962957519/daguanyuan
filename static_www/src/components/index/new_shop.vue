
<template>
    <div  class="shop_mall">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">新品开抢</p>
            <!--<yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>-->
        </yd-navbar>
        <!--底部导航结束-->
        <yd-infinitescroll :callback="getproductlist" ref="infinitescrollDemo">
            <yd-list theme="5" slot="list">
                <yd-list-item v-for="(item, index) in list" :key="index" style="padding: .6rem; border-bottom: .2rem solid #eee;">
                     <template  v-for="(items, indexs) in item">
                    <img v-if="indexs == 0 " slot="img" v-on:click.prevent="clickhref(items.goods_id)" :src="getCurlofimgUsenoAuth(items.original_img)"/>
                    <yd-cell-group slot="other">
                        <yd-cell-item @click.native="clickhref(items.goods_id)">
                            <span slot="left">{{items.goods_name|subTime}}</span>
                            <span slot="right"> <img  style="width: .8rem;" slot="img" :src="getCurlofimgUsenoAuth(items.original_img)"/></span>
                        </yd-cell-item>
                     </yd-cell-group>
                     </template>
                </yd-list-item>

            </yd-list>
            <span slot="doneTip">暂没有更多数据~~</span>

        </yd-infinitescroll>
        <div id="childcontent">
            <router-view></router-view>
        </div>
    </div>
</template>
<script type="text/babel">
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
                page:1
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
            getproductlist(){
                var that = this;
                that.$axios.get('/users/new_product', {
                    params: {
                        token: storeWithExpiration.get('token'),
                        page:that.page
                    }
                }) .then(function (response) {
                    if (response.status == '200') {
                        return response.data;
                    }
                }).then(function (json) {
                    const _list = json.data;
                    that.list = [...that.list, ..._list];
                    that.page++;

                    //提交到父类
                    that.$emit('refresh',[]);//select事件触发后，自动触发showCityName事件
                    if (_list.length < that.pageSize || that.page <  20) {
                        /* 所有数据加载完毕 */
                        return;
                    }

                    return false;

                }).catch(function (ex) {

                });
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
                    return `${val.substring(0,15)}`
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

<style>
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
    .user_title{height: .8rem; width: 100%;}
    .user_title p{ width: 40%; text-align: left; float: left;}
    .user_title p span{line-height: .8rem;vertical-align: middle}
    .user_title p img{ width: .4rem;vertical-align: middle; margin-right: .05rem;}
    .user_title ul{ width: 60%; float: right;line-height: .8rem;}
    .user_title ul li{ float: left; margin-right: .1rem; width: 100%; text-align: right;}
    .user_title ul li img{ width: .3rem;vertical-align: middle}
</style>



