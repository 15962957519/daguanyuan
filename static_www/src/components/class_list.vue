<template>
    <div class="class_list">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p v-if="this.$route.query.state==1" style="font-size: .3rem" slot="center">关于{{this.$route.query.keywords}}搜索结果</p>
            <template v-else>
                <p  class="search02"  slot="center" @click="search"><img  src="@/assets/images/search.png">请输入您要搜索的商品</p>
                <p class="search01" slot="right"   @click="search"></p>
            </template>
        </yd-navbar>
        <scroll class="wrapper"
                :data="list"
                :listenScroll="true"
                :pulldown="true"
                @pulldown="pulldownrefsh"
                @uppush="productlist"
                @refreshpage="refreshpage">
            <div style="height:50px;display: block;"></div>
            <productlists  @refresh="srcollrefsh"  :listcateory="list"></productlists>
        </scroll>
        <!--底部菜单-->
        <!--<menus></menus>-->
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
    import productlists from '@/components/cateroy_productlists';
    import scroll  from '@/components/slot/scroll';

    Vue.component(InfiniteScroll.name, InfiniteScroll);
    import menu from '@/components/menu';
    export default {
        data: function () {
            return {
                transitionName: 'slide-right',
                showlists: false,
                list: [],
                page:1,
                pulldown: false,
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
            'scroll':scroll,
            listView,
            'menus':menu,
            productlists
        },
        methods: {
            search() {
                this.$router.push({name: 'seach_index_link'})

            },
            srcollrefsh(){
                this.data.push(1);
            },
            pulldownrefsh(){
                this.list =[];
                this.page =1;
                this.getproductlist();
            },
            productlist(){
                this.getproductlist();
            },
            refreshpage(){
                this.page =1;
                this.data=[];
            },


            goback () {
                this.$router.go(-1)
            },

            clickhref(goods_id){
                try {
                    this.$router.push({path: '/class_list/' + goods_id})
                } catch (e) {
                    console.log(e)
                }
            },
            getCurlofimgUsenoAuth(a, b, c){
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            getproductlist(id,keywords){
                var that = this;
                that.$axios.get('/goods/goods_list', {
                    params: {
                        token: storeWithExpiration.get('token'),
                        id:id,
                        keywords:keywords,
                        page:that.page
                    }
                }) .then(function (response) {
                    if (response.status == '200') {
                        return response.data;
                    }
                }).then(function (json) {
                    const _list = json.data.goods_list;
                    that.list = [...that.list, ..._list];
                    that.page++;
                    if (_list.length < that.pageSize || that.page < 20) {
                        /* 所有数据加载完毕 */

                        return;
                    }
                    /* 单次请求数据完毕 */
                    return false;

                }).catch(function (ex) {
                    console.log(ex);
                });
            },
        },
        created:function(){
            var id = this.$route.query.id;
            var keywords = this.$route.query.keywords;
            this.getproductlist(id,keywords);
            // 显示B
            setTimeout(() => {
                this.showlists = true;
            }, 10);
        },
        mounted: function () {



        }
    }
</script>

<style>
    .class_list{ height:100%;}
    .class_list> .wrapper{
        height: inherit !important;
    }
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
    .user_title p{ width: 30%; text-align: left; float: left;}
    .user_title p span{line-height: .8rem;vertical-align: middle}
    .user_title p img{ width: .4rem;vertical-align: middle; margin-right: .05rem;}
    .user_title ul{ width: 70%; float: right;line-height: .8rem;}
    .user_title ul li{ float: left; margin-left: .1rem;}
    .user_title ul li img{ width: .3rem;vertical-align: middle}
    .search02{ width: 100%; background: #eee; height: .65rem;  text-align: left;  padding-left: .15rem;  padding-top: .18rem; color: #999}
    .search02 img{vertical-align: top; margin-right: .1rem; width: .35rem; height: .35rem;}
    .search01{ position: relative;background: #eee;height: .65rem;   width: 100%;  right: .5rem;}
</style>



