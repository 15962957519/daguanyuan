<template>
    <div>
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">店铺升级支付</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--店铺的个人信息-->
        <div class="my_shop_po">
            <dl>
                <dt> <img :src="user_data.head_pic"></dt>
                <dd ><strong>{{user_data.nickname}}</strong></dd>
            </dl>
        </div>
        <!--功能介绍-->
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">店铺类型：</span>
                <span slot="right">{{user_data.nickname}}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">升级类型：</span>
                <span slot="right" v-if="upid == 3">黄金店铺</span>
                <span slot="right" v-if="upid == 4">铂金店铺</span>
                <span slot="right" v-if="upid == 5">钻石店铺</span>

            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">升级费用：</span>
                <span slot="right">{{ mypay }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">审核时间：</span>
                <span slot="right">周一至周五(9:00-18:00)</span>
            </yd-cell-item>

        </yd-cell-group>
        <!--提交-->
        <div class="people_ti">
            <yd-button bgcolor="#af773e" color="#fff" size="large" type="warning" @click.native="payupshop">确认信息并提交支付</yd-button>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {CellGroup, CellItem} from 'vue-ydui/dist/lib.rem/cell';
    /* 使用px：import {CellGroup, CellItem} from 'vue-ydui/dist/lib.px/cell'; */
    import { mapState} from 'vuex';
    import wx from 'weixin-js-sdk'
    Vue.component(CellGroup.name, CellGroup);
    Vue.component(CellItem.name, CellItem);

    export default {
        data() {
            return {
                upid : '',
                user_id :'',
                paymoney:'',
                mypay:'',
            }
        },
        mounted: function () {
            this.$store.state.menuItems
            this.showData();
        },
        methods:{
            goback () {
                this.$router.go(-1)
            },
            showData(){
                var _that = this;
                var upid = _that.$route.query.upid;
                var money = _that.$route.query.money;
                var userid = _that.user_id;
                var token = storeWithExpiration.get('token');
                //console.log(upid)
                console.log(userid)
                _that.upid = upid;
                var url = 'users/index?token=' + token;
                _that.$axios.get(url).then(function(response) {
                    if (response.status == 200){
                        _that.user_id = response.data.data.user_data.user_id;
                        _that.paymoney = response.data.data.user_data.store_cost;
                        _that.mypay = money - _that.paymoney;
                        console.log(_that.user_id)
                    }
                }).catch(function(error) {
                    //console.log(error);
                });

            },
            payupshop(){
                var _that = this;
                var order_sn = _that.ordersn();
                var token = storeWithExpiration.get('token');
                var upid = _that.upid;
                console.log(order_sn)
                var url = '/balancerecharge?token=' + token + '&state=3' + '&order_sn=' + order_sn + '&store_level_id=' + upid;
                _that.$axios.get(url).then(function(response) {
                    console.log(response)
                    if (response.status == 200){
                        if (response.data.code == 2000){
                            console.log(response)
                            _that.wxzfcallpay(JSON.parse(response.data.jsondata))
                        }
                    }
                }).catch(function(error) {
                    //console.log(error);
                });

            },
            wxzfcallpay(config){
                var _that = this;
                //有支付
                function jsApiCall(config) {
                    WeixinJSBridge.invoke('getBrandWCPayRequest', config, function(res) {
                        //WeixinJSBridge.log(res.err_msg);
                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                            // self.RouterLink('personal')
                            _that.$dialog.alert({mes: '支付成功'})
                        }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                            _that.$dialog.alert({mes: '已取消支付'})
                            // window.location.href = 'gift_failview.do?out_trade_no=' + this.orderId
                        } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                            _that.$dialog.alert({mes: '网络异常'})
                        }
                    });
                }
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall(config), false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall(config));
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall(config));
                    }
                } else {
                    jsApiCall(config);
                }
            },
            ordersn(){
                var now = new Date()
                var month = now.getMonth() + 1
                var day = now.getDate()
                var hour = now.getHours()
                var minutes = now.getMinutes()
                var seconds = now.getSeconds()
                return now.getFullYear().toString() + month.toString() + day + hour + minutes + seconds + (Math.round(Math.random() * 89 + 100)).toString()
            }
        },
        computed:mapState({
            user_data(state){
                if(this.$weipai.isEmptyObject(state.menuItems)){
                    this.$store.dispatch("getuserinfo");
                }
                return state.menuItems
            }
        }),
    }
</script>

<style scoped>
    .navbar-bottom-line-color:after {border-color: #333 !important;}
    .my_shop{color: #fff; background: #af773e; padding: .1rem .2rem; border-radius: .1rem;}
    .my_shop_po{ width: 100%; height: 4.1rem; background: #af773e; padding: 1rem 1rem 0 1rem; border-radius: 0 0 .3rem .3rem;}
    .my_shop_po dl { width: 100%; height: 3.1rem; }
    .my_shop_po dl dt img{ width: 1.5rem; border: 1px  solid #af773e; margin-top: .3rem; height: 1.5rem; border-radius: 1.5rem;}
    .my_shop_po dl dd{ margin-top: .1rem; color: #fff;}
    .color_w{ color: #af773e; margin-left: .1rem}
    .img_b{width: .7rem;height:.7rem;border: 1px  solid #af773e; margin-right:.1rem; }

    /*实名认证图标和店铺的认证图标*/
    .people_ti{ text-align: left; padding: .2rem; height: .7rem; background: #f5f5f5; }
    .bg-yellow{ color: #af773e;}
</style>






