
<template>
    <div>
        <yd-infinitescroll :callback="getproductlist" ref="infinitescrollDemo">
            <yd-list theme="3" slot="list" >
            <div style="width: 50%; float: left;  background: #f3f3f3;  padding-right: .05rem;">
                <!--瀑布流-->
                <div class="shoppping_list" v-for="(item, key) in articles" :key="key">
                    <img  v-on:click.prevent="clickhref(item[0].goods_id)" :src="getCurlofimgUsenoAuth(item[0].original_img)"/>
                    <p><strong>{{item[0].goods_name}}</strong></p>
                    <p class="demo-list-price"><em>¥</em>{{item[0].start_price}}</p>

                    <div class="user_title_p">
                        <p><img :src="item[0].head_pic"><span>{{item[0].nickname|subTime}}</span> </p>
                        <ul>
                            <li>{{item[0].upload_time |date}}</li>
                        </ul>
                    </div>
                </div>
                <!--瀑布流结束-->
            </div>
            <div style="width: 50%; float: right; background: #f3f3f3;  padding-left: .05rem;">
                <!--瀑布流-->
                <div class="shoppping_list" v-for="(item, key) in articles" :key="key">
                    <template v-if="typeof item[1] !='undefined'">
                        <img  v-on:click.prevent="clickhref(item[1].goods_id)" :src="getCurlofimgUsenoAuth(item[1].original_img)"/>
                        <p><strong>{{item[1].goods_name}}</strong></p>
                        <p class="demo-list-price"><em>¥</em>{{item[1].cur_price}}</p>

                        <div class="user_title_p">
                            <p><img :src="item[1].head_pic"><span>{{item[1].nickname|subTime}}</span> </p>
                            <ul>
                                <li>{{item[1].upload_time |date}}</li>
                            </ul>
                        </div>
                    </template>

                </div>
                <!--瀑布流结束-->
            </div>
            </yd-list>
        </yd-infinitescroll>

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
                page:1,
                articles:[],
                pagesize:1,
                articles1:[],
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
            },

            list(newValue, oldValue) {
                    this.articles = []
                    let a = 0
                    for (let i = 0; i < newValue.length; i++) {
                        if (i % 2 != 1 && i != 0) {
                            a++
                        }
                        this.articles[a] = this.articles[a] instanceof Array ? this.articles[a] : []
                        this.articles[a].push(newValue[i])
                    }
            }

        },
        components: {
            'nsr-loading': nsr_loading,
            listView
        },
        methods: {
            clickhref(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/index/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },
            clearproductlist(){
                this.list =[];
                this.page =1;
                this.getproductlist();
            },
            getCurlofimgUsenoAuth(a, b, c){
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            getproductlist(){
                var that = this;
                if(that.pagesize==0){
                    that.$dialog.alert({mes: '首页仅展示部分商品！'})
                    /* 所有数据加载完毕 */
                    return;
                }
                that.$axios.get('/virtua_getmore', {
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
                    if (json.data.length <1){
                        /* 所有数据加载完毕 */
                        that.pagesize=0
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

<style >
    /*瀑布流*/
    .shoppping_list{ width: 100%; float: left; background: #fff; padding: .25em; margin-bottom: .1rem;}
    .shoppping_list img{ width: 100%; }
    .shoppping_list p{ text-align: left;}
    /*<!--瀑布流结束-->*/

    .Router {position: absolute;width: 100%;transition: all .8s ease;top: 40px;}
    .slide-left-enter,
    .slide-right-leave-active {opacity: 0;-webkit-transform: translate(100%, 0);transform: translate(100%, 0);}
    .slide-left-leave-active,
    .slide-right-enter {opacity: 0;-webkit-transform: translate(-100%, 0);transform: translate(-100% 0);}
    .demo-list-price{color: #af773e;  line-height: .5rem;}
    .user_title_p{height: .8rem; width: 100%; margin-top: -.1rem;}
    .user_title_p p{ width: 40%; text-align: left; float: left;}
    .user_title_p p span{line-height: .8rem;vertical-align: middle}
    .user_title_p p img{ width: .4rem;vertical-align: middle; margin-right: .05rem;}
    .user_title_p ul{ width: 60%; float: left;line-height: .8rem;}
    .user_title_p ul li{  margin-right: .2rem; width: 100%; text-align: right}
    .user_title_p ul li img{ width: .3rem;vertical-align: middle}
</style>



