<template>
    <div>
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--我的买单-->
        <yd-tab  v-model="navcart"  active-color="#af773e" class="order_list"  :prevent-default="false"  :item-click="panelClick">
            <yd-tab-panel label="我的买单">
                <!--我的买单-->
                <div class="margin_x"></div>
                <yd-tab active-color="#af773e" class="margin_top"  v-model="tab2"  :prevent-default="false" :item-click="itemClick">
                    <yd-tab-panel v-for="(item, idx) in items" :label="item.label" :key="idx">

                        <!--单个的商品-->
                        <div class="order_o" v-for="item in payorderall">
                            <div class="order_title">
                                <ul>
                                    <li class="order_title1"><img :src="item.seller_detail.head_pic">{{ item.seller_detail.nickname }}</li>
                                    <li class="order_title3" v-if="item.pay_status == 0 && item.shipping_status == 0 && item.order_status == 0"> 订单生成-未付款</li>
                                    <li class="order_title3" v-else-if="item.pay_status == 1 && item.shipping_status == 0 && item.order_status == -1">付款成功-待发货</li>
                                    <li class="order_title3" v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 1">发货成功-待收货</li>
                                    <li class="order_title3" v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 6">售后处理中</li>
                                </ul>
                            </div>
                            <div class="order_img">
                                <div class="order_img_img" v-on:click="ordermore(item.goods_id)">
                                    <img :src="item.original_img">
                                </div>
                                <div class="order_img_r" v-on:click="orderDetail(item.order_id)">
                                    <dl>
                                        <dt>{{ item.goods_name }} </dt>
                                        <dd>成交金额：￥{{ item.order_amount }}</dd>
                                        <dd>下单时间：{{ item.add_time }} </dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="order_bottom">
                                <p v-if="item.pay_status == 0 && item.shipping_status == 0 && item.order_status == 0">未付款</p>
                                <p v-else-if="item.pay_status == 1 && item.shipping_status == 0 && item.order_status == -1">待发货</p>
                                <p v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 1">待收货</p>
                                <p v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 6">售后</p>
                            </div>
                        </div>
                        <!--单个的商品结束-->
                    </yd-tab-panel>
                </yd-tab>
                <!--我的买单-->
            </yd-tab-panel>
            <yd-tab-panel label="我的卖单">
                <!--我的卖单-->
                <!--我的买单-->
                <div class="margin_x"></div>
                <yd-tab active-color="#af773e" class="margin_top"  v-model="tab3"  :prevent-default="false" :item-click="itemClick1">
                    <yd-tab-panel v-for="(item, idx) in items1" :label="item.label" :key="idx">

                        <!--单个的商品-->
                        <div class="order_o" v-for="item in paynoorderall">
                            <div class="order_title">
                                <ul>
                                    <li class="order_title1"><img :src="item.seller_detail.head_pic">{{ item.seller_detail.nickname }}</li>
                                    <li class="order_title3" v-if="item.pay_status == 0 && item.shipping_status == 0 && item.order_status == 0"> 订单生成-未付款</li>
                                    <li class="order_title3" v-else-if="item.pay_status == 1 && item.shipping_status == 0 && item.order_status == -1">付款成功-待发货</li>
                                    <li class="order_title3" v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 1">发货成功-待收货</li>
                                    <li class="order_title3" v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 6">售后处理中</li>
                                </ul>
                            </div>
                            <div class="order_img">
                                <div class="order_img_img">
                                    <img :src="item.original_img">
                                </div>
                                <div class="order_img_r">
                                    <dl>
                                        <dt>{{ item.goods_name }} </dt>
                                        <dd>成交金额：￥{{ item.order_amount }}</dd>
                                        <dd>下单时间：{{ item.add_time }} </dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="order_bottom">
                                <p v-if="item.pay_status == 0 && item.shipping_status == 0 && item.order_status == 0">未付款</p>
                                <p v-else-if="item.pay_status == 1 && item.shipping_status == 0 && item.order_status == -1">待发货</p>
                                <p v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 1">待收货</p>
                                <p v-else-if="item.pay_status == 1 && item.shipping_status == 1 && item.order_status == 6">售后</p>
                            </div>
                        </div>
                        <!--单个的商品结束-->
                    </yd-tab-panel>
                </yd-tab>
                <!--我的卖单-->
            </yd-tab-panel>
        </yd-tab>


    </div>
</template>
<style>
    /*.yd-tab-nav .yd-tab-active :first-child {*/
    /*color: #fff;*/
    /*background-color: #af773e;*/
    /*}*/
    .margin_x{ height: .1rem; width: 100%; background: #eee;}
    .navbar-bottom-line-color:after {border-color: #eee !important;}
    /*我的订单*/
    .order_list{ width: 100%;background:#fff;   }
    .order_list ul li {  width: 20%; float: left; line-height: 1rem;}
    .order_list_active{  color: #af773e; border-bottom:1px solid #af773e }
    /*单个的订单*/
    .order_o{ width: 100% ;background: #fff; height: 5rem; border-bottom:.1rem solid #eee;padding: 0 .2rem; }
    .order_title{ width: 100%; height: 1rem; border-bottom: 1px solid #eee; }
    .order_title ul li { width: 50%;vertical-align: center;  }
    .order_title1{  float: left;text-align: left;}
    .order_title1 img{ width: .6rem; margin-right: .2rem;  vertical-align: middle; }
    .order_title3{ width: 60%; float: right;text-align: right;color: #af773e;}
    .order_img{ width: 100%; height: 3rem;  margin-top: .1rem;}
    .order_img_img{ width: 40%; height: 3rem; float: left; padding: .2rem; }
    .order_img_img img{ width: 100%;box-shadow: 1px 2px 3px #999}
    .order_img_r{float: right;width: 60%;height: 3rem; }
    .order_img_r dl{ text-align: left; height: 3rem;}
    .order_img_r dl dt{ font-size: .3rem; color: #333; padding: .2rem; overflow: hidden; height: 1.6rem;}
    .order_img_r dl dd{ color: #999; padding-left: .2rem; margin-top: .18rem;}
    .order_bottom{ width: 100%; height: 1rem; border-top: 1px solid #eee; }
    .order_bottom p{float: right; border: 1px solid #af773e; border-radius: .1rem; color:#af773e; padding: .1rem .3rem; margin-top: .12rem; }
</style>
<script>
    import Vue from 'vue';
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar';
    //tab切换
    import {Tab, TabPanel} from 'vue-ydui/dist/lib.rem/tab';

    Vue.component(NavBar.name, NavBar);
    Vue.component(NavBarBackIcon.name, NavBarBackIcon);
    Vue.component(NavBarNextIcon.name, NavBarNextIcon);
    Vue.component(Tab.name, Tab);
    Vue.component(TabPanel.name, TabPanel);
    //正在加载
    import { mapState} from 'vuex';
    export  default{
        data(){
            return {
                tab2: 0,
                navcart: 0,
                items: [
                    {label: '全部'},
                    {label: '待付款'},
                    {label: '待发货'},
                    {label: '待收货'},
                    {label: '已完成'}
                ],
                tab3: 0,
                items1: [
                    {label: '全部'},
                    {label: '待付款'},
                    {label: '待发货'},
                    {label: '待收货'},
                    {label: '已完成'}
                ],
                payorderall : [],
                paynoorderall:[],

            }
        },
        mounted:function(){
            // this.panelClick();
            this.showclick();
        },
        methods:{
            goback () {
                this.$router.go(-1)
            },
            itemClick(key) {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                _that.payorderall = [];
                console.log(_that.payorderall)
                var key = key || 0;
                _that.$dialog.loading.open('数据加载中');
                setTimeout(() => {
                    _that.tab2 = key;
                    _that.$dialog.loading.close();
                    if (key == 0){
                        _that.panelClick(0)
                    }
                    if (key == 1){//待付款
                        var url = '/users/my_order?token=' + token + '&pay_status=0' + '&shipping_status=0' + '&order_status=0' + '&seller=1';
                    }else if (key == 2){ //待发货
                        var url = '/users/my_order?token=' + token + '&pay_status=1' + '&shipping_status=0' + '&order_status=-1' + '&seller=1';
                    }else if (key == 3){ //待收货
                        var url = '/users/my_order?token=' + token + '&pay_status=1' + '&shipping_status=1' + '&order_status=1' + '&seller=1';
                    }else if (key == 4){ //已完成
                        var url = '/users/my_order?token=' + token + '&pay_status=1' + '&shipping_status=2' + '&order_status=2' + '&seller=1';
                    }

                    _that.$axios.get(url).then(function (res) {
                        if (res.status == 200){
                            return res.data.data;
                        }

                    }).then(function (res) {

                        console.log(_that.payorderall)
                        console.log(res)
                        for (var i=0;i<res.length;i++){
                            res[i]['add_time'] = _that.timestampToTime(res[i]['add_time']);
                            _that.payorderall.push(res[i])
                        }

                    }).catch(function (error) {
                        console.log(error)
                    })

                }, 500);
                return false;

            },
            itemClick1(key) {
                var _that = this;
                var token = window.storeWithExpiration.get('token');

                _that.paynoorderall = [];
                var key = key || 0;
                _that.$dialog.loading.open('数据加载中');
                setTimeout(() => {
                    _that.tab3 = key;
                    _that.$dialog.loading.close();
                    if (key == 0){
                        _that.panelClick(1)
                    }
                    if (key == 1){//待付款
                        var url = '/users/my_order?token=' + token + '&pay_status=0' + '&shipping_status=0' + '&order_status=0' + '&seller=2';
                    }else if (key == 2){ //待发货
                        var url = '/users/my_order?token=' + token + '&pay_status=1' + '&shipping_status=0' + '&order_status=-1' + '&seller=2';
                    }else if (key == 3){ //待收货
                        var url = '/users/my_order?token=' + token + '&pay_status=1' + '&shipping_status=1' + '&order_status=1' + '&seller=2';
                    }else if (key == 4){ //已完成
                        var url = '/users/my_order?token=' + token + '&pay_status=1' + '&shipping_status=2' + '&order_status=2' + '&seller=2';
                    }

                    _that.$axios.get(url).then(function (res) {
                        if (res.status == 200){
                            return res.data.data;
                        }

                    }).then(function (res) {
                        console.log(res)
                        for (var i=0;i<res.length;i++){
                            res[i]['add_time'] = _that.timestampToTime(res[i]['add_time']);
                            _that.paynoorderall.push(res[i])
                        }

                    }).catch(function (error) {
                        console.log(error)
                    })

                }, 500);
            },
            panelClick(key) {
                var _that = this;
                var key = key || 0;
                var token = window.storeWithExpiration.get('token');

                if (key == 1){
                    var url = '/users/my_order?token=' + token + '&pay_status=-1' + '&shipping_status=-1' + '&order_status=-1' + '&seller=2';
                }
                if (key == 0){
                    var url = '/users/my_order?token=' + token + '&pay_status=-1' + '&shipping_status=-1' + '&order_status=-1' + '&seller=1';
                }
                _that.$dialog.loading.open('数据加载中');
                setTimeout(() => {
                    _that.navcart = key;
                    _that.$dialog.loading.close();

                }, 500);
                //查询订单信息
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        return res.data.data;
                    }

                }).then(function (res) {
                    if (key == 1){
                        _that.paynoorderall = [];
                        for (var i=0;i<res.length;i++){
                            res[i]['add_time'] = _that.timestampToTime(res[i]['add_time']);
                            _that.paynoorderall.push(res[i])
                        }
                    }
                    if (key == 0){
                        _that.payorderall = [];
                        for (var i=0;i<res.length;i++){
                            res[i]['add_time'] = _that.timestampToTime(res[i]['add_time']);
                            _that.payorderall.push(res[i])
                        }
                    }


                }).catch(function (error) {
                    console.log(error)
                })

            },
            showclick(){
                //获取页面id
                var _that = this;
                var id = _that.$route.query.id;
                _that.payorderall = [];
                console.log(id)
                if (id == 0){
                    _that.panelClick(0)
                }else if (id == 1){
                    _that.itemClick(1)
                }else if (id == 2){
                    _that.itemClick(2)
                }else if (id == 3){
                    _that.itemClick(3)
                }else if (id == 4){
                    _that.itemClick(4)
                }
            },

            ordermore(id){
                this.$router.push({path: '/productlists/' + id})
            },
            orderDetail(order_id){
                this.$router.push({name:'orderdetail_link',query:{order_id:order_id}})
            },

            timestampToTime(timestamp) {
                var  date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                var  Y = date.getFullYear() + '/';
                var  M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '/';
                var  D = date.getDate() + ' ';
                var  h = date.getHours() + ':';
                var  m = date.getMinutes() + ':';
                var  s = date.getSeconds();
                return Y+M+D+h+m+s;
            },
        },
        computed:mapState({
            bargaininglist(state){
                if(this.$weipai.isEmptyObject(state.bargaininglist)){
                    this.$store.dispatch("getbargaininglist");
                    if(this.$weipai.isEmptyObject(state.bargaininglist)){
                        //禁止去获取了
                        this.$store.state.bargaininglistflag =false;
                    }
                }
                return state.bargaininglist
            }
        }),
    }
</script>