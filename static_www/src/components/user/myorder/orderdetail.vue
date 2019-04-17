<template>
    <div id="container">
        <div class="order_details_container">
            <div class="header" v-if="order_ing.address" v-on:click="addAdress">
                <div class="title">收货地址 {{ order_ing.address }}</div>
                <div class="content">
                    <div class="text line_clamp">
                        收货人:{{ order_ing.consignee}}
                        手机：{{ order_ing.mobile }}
                    </div>
                </div>
            </div>
            <div v-if="order_ing.address == ''" class="header">
                <div class="content">
                    <div class="text" style=" line-height: 1.3rem; font-size: 0.35rem;" v-on:click="addAdress"> 请选择收货地址！ </div>
                </div>
            </div>
            <div class="details">
                <div class="top">买家：{{ order_m.nickname }}</div>
                <div class="content">
                    <div class="content_l">
                        <img :src="order_ing.original_img" /> </div>
                    <div class="content_r">
                        <div class="title">{{ order_ing.goods_name }}</div>
                        <div class="bottom">
                            <div>
                                ￥{{ order_ing.order_amount }}
                            </div>
                            <div>×1</div>
                        </div>
                    </div>
                </div>
                <!-- 付款显示信息 -->
                <div class="bottom">
                    <div class="top">
                        <div>运费</div>
                        <div>0元</div>
                    </div>
                    <div class="bottom">
                        <div class="bottom_l">实付款（含运费）</div>
                        <div class="bottom_r">
                            {{ order_ing.order_amount }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="other">
                <!-- <div>商品区编号：1121255135</div> -->
                <div>订单编号：{{order_ing.order_sn}}</div>
                <div>创建时间： {{ order_ing.add_time|time }}</div>
            </div>
            <div class="ui-alert" v-show="isShow" style="z-index: 9999;">

                <div class="list" style="height: 100px;">
                    <div style="float: right;width: 15%;" v-on:click="qx_new">X</div>
                    <div class="shopupAppointBut" data-id="1" style="width: 85%;">余额支付</div>
                    <div class="shopupAppointBut" data-id="2" @click="wx_pay">微信支付</div>
                </div>
            </div>
            <div class="footer">
                <div class="footer_l">
                    {{ order_ing.order_amount }}元
                </div>
                <div class="footer_r" v-on:click="festpay">立即付款</div>
            </div>
        </div>
</div>
</template>
<style scoped>
    @import url(../../../assets/css/orderdetail.css);

</style>


<script type="text/babel">
    import Vue from 'vue';
    import {InfiniteScroll} from 'vue-ydui/dist/lib.rem/infinitescroll';
    import wx from 'weixin-js-sdk'
    Vue.component(InfiniteScroll.name, InfiniteScroll);
    export default {
        data: function () {
            return {
                order_ing: {
                    order_status: '',
                    original_img: '',
                    goods_name: '',
                    order_sn: '',
                    order_amount: '',
                    add_time: '',
                    address: '',
                    consignee: '',
                    mobile: ''
                },
                order_m: {head_pic: '', nickname: ''},
                isShow: false,
                phones: '',
                adress: '',
                people: '',
                province: '',
                city: '',
                district: ''
            }
        },

        components: {},
        methods: {
            showData: function (order_id) {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                var url = "/users/orderDetail?token=" + token + "&order_id=" + order_id;
                _that.$axios.get(url).then(function (response) {
                    if (response.status == 200) {
                        _that.order_ing = response.data['data'];
                        _that.order_m = response.data['data']['seller_detail'];
                    }
                }).catch(function (error) {

                });
            },
            festpay: function () {
                var _that = this;
                _that.isShow = !_that.isShow;
            },
            qx_new: function () {
                var _that = this;
                _that.isShow = !_that.isShow;
            },
            wx_pay: function () {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                var order_sn = _that.order_ing.order_sn;
                var url = "/jsapipay?token=" + token + "&order_sn=" + order_sn;
                _that.$axios.get(url).then(function (response) {
                    if (response.status == 200) {
                        _that.orderdetailpay(JSON.parse(response.data.jsondata))

                    }
                }).catch(function (error) {

                });
            },
            orderdetailpay: function (config) {
                //有支付
                function jsApiCall(config) {
                    WeixinJSBridge.invoke('getBrandWCPayRequest', config, function (res) {
                        //WeixinJSBridge.log(res.err_msg);
                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                            // self.RouterLink('personal')
                            _that.$dialog.alert({mes: '支付成功'})

                        }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                            _that.$dialog.alert({mes: '已取消支付'})

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
            addAdress: function () {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                var order_sn = _that.order_ing.order_sn;
                WeixinJSBridge.invoke(
                    wx.openAddress({
                        success: function (res) {
                            //详细地址
                            _that.adress = res.provinceName + res.cityName + res.countryName + res.detailInfo;
                            _that.$axios.post('/addAdress', {
                                token: token,
                                order_sn: order_sn,
                                address: _that.adress,
                                consignee: res.userName,
                                mobile: res.telNumber,
                            }).then(function (response) {
                                if (response.status == 200) {
                                    _that.$dialog.toast({
                                        mes: response.data.message,
                                        timeout: 1500
                                    });
                                    _that.order_ing.address = response.data.data.address;
                                    _that.order_ing.consignee = response.data.data.consignee;
                                    _that.order_ing.mobile = response.data.data.mobile;
                                }
                            }).catch(function (error) {

                            });
                        },
                        error: function (error) {
                            consoe.log(JSON.stringify(error))
                        }
                    })
                )

            },
        },
        created: function () {

        },
        mounted: function () {
            var order_id = this.$route.query.order_id;
            this.showData(order_id);
        }

    }
    //时间戳处理
    Vue.filter('time', function (value) {
        return new Date(parseInt(value) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    });
</script>