<template>
    <div>
        <yd-list theme="3"  slot="listcateory">
            <yd-list-item v-for="(item, key) in listcateory" :key="key">
                <span>{{key}}</span>
                <img slot="img" v-on:click.prevent="clickhref(item.goods_id)" :src="getCurlofimgUsenoAuth(item.original_img)"/>
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
                page:1,
                date:0
            }
        },
        props: {
            listcateory: {
                type: Array,
                default:[]
            },
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
        beforeRouteEnter: (to, from, next) => {
            this.routepath =   to.path;
            console.log( to.path)
        },
        methods: {
            getCurlofimgUsenoAuth(a,b,c){
                return  this.$weipai.getCurlofimgUsenoAuth(a,b,c,false);
            },
            clickhref(goods_id){
                try {
                    this.$router.push({path: '/class_list/' + goods_id})
                } catch (e) {

                }
            },

        },
        filters: {
            subTime: function (val) {
                val = val || '';
                if(val!=undefined){
                    return `${val.substring(0,1)}***${val.substring(val.length-1)}`
                    //return val.substring(0, 2);
                }else{
                    return val;
                }
            }
        },
        created:function(){
            var dateobj = new Date()
            this.date= parseInt(dateobj.getTime()/1000);

        },
        mounted: function () {

        }

    }
</script>

<style scoped>
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
    .demo-list-price_ba{color:#999; display: inline-block; border-radius: .3rem; padding: 0 .15rem; border: 1px solid rgba(239,239,239,1);   line-height: .4rem;}
    .demo-list-price_re{color: rgba(243,10,0,.8); display: inline-block; border-radius: .3rem; padding: 0 .15rem; border: 1px solid rgba(243,10,0,.8);   line-height: .4rem;}
    .demo-list-price{color: rgba(243,10,0,.8);    line-height: .5rem;}
    .user_title{height: .8rem; width: 100%;}
    .user_title_mall{height: .8rem; width: 100%;}
    .user_title_mall p{ width: 40%; text-align: left; float: left;}
    .user_title_mall p span{line-height: .8rem;vertical-align: middle}
    .user_title_mall p img{ width: .4rem;vertical-align: middle; margin-right: .05rem;}
    .user_title_mall ul{ width: 60%; float: left;line-height: .8rem;}
    .user_title_mall ul li{  margin-right: .15rem; width: 100%; text-align: right;}
    .user_title_mall ul li img{ width: .3rem;vertical-align: middle}
    .user_title p{ width: 40%; text-align: left; float: left;}
    .user_title p span{line-height: .8rem;vertical-align: middle}
    .user_title p img{ width: .4rem;vertical-align: middle; margin-right: .05rem;}
    .user_title ul{ width: 60%; float: right;line-height: .8rem;}
    .user_title ul li{ float: left; margin-left: .1rem;}
    .user_title ul li img{ width: .3rem;vertical-align: middle}
    .time{ position: absolute; top:2.3rem; right: .2rem; background: rgba(243,10,0,.8); color: #fff; padding: .1rem 0 .1rem .1rem; border-radius: .3rem 0 0 .3rem;color:#fff !important;}
    .blackfisrt{ position: absolute; top:3rem;  right: .2rem; background: rgba(239,239,239,.8);     display: inline-block;color: #666; padding: .1rem 0 .1rem .1rem; border-radius: .3rem 0 0 .3rem;}
</style>



