<template>
    <div id="content">
        <div v-on:click="cancelShare()" style=' position: fixed; height: 100%;top: 0; display: none; z-index: 99999;' v-show="shareShow">
            <img style="width: 100%;" src="@/assets/images/caution.png" />
        </div>
        <div class="payment_margin_header">
            <div class="top">支付保证金</div>
            <div class="bottom">
                <img src="@/assets/images/paymentmargin.jpg/">
                <div>¥&nbsp;{{caution}}</div>
            </div>
        </div>
        <div class="payment_margin_list"> 需要支付：
            <span>¥&nbsp;{{caution}}</span>
            <span style="margin-left: auto">订单号：{{order_sn}}</span>
        </div>
        <!--<div class="payment_margin_but"  v-on:click="payment">安全支付</div>-->
        <div class="payment_margin_but"  v-on:click="wxshare">安全支付</div>
        <!--<div class="payment_margin_but"  onclick="javascript:if(confirm('支付完成后请告知客服哦!')) window.location.href='{:url(\'mobile/Acution/index\')}' ">安全支付</div>-->

        <div class="payment_ts" style="text-align:center;color:#bf0f0f"> 付款后请及时告知客服顾问，加快审核通过时间</div>


    </div>
</template>

<script>
    import Vue from 'vue';
    export  default{
        data(){
            return{
                caution:0,
                order_sn:'',
                shareShow:false,
            }
        },
        mounted: function () {
            this.showData();
        },
        methods:{
            wxshare:function(){
                this.shareShow = true
            },
            cancelShare:function(){
                this.shareShow = false
            },
            showData(){
                var _that = this;
                var token = storeWithExpiration.get('token');
                var url = '/pay_money?token=' + token
                _that.$axios.get(url).then(function(response) {
                    _that.caution = response.data.caution_money;
                    _that.order_sn = _that.cauton_ordersn();
                }).catch(function(error) {
                    //console.log(error);
                });
            },
            payment(){
                var _that = this;
                var order_sn = _that.order_sn;
                var token = storeWithExpiration.get('token');
                var url = '/balancerecharge?token=' + token + '&state=4' + '&order_sn=' + order_sn
                _that.$axios.get(url).then(function(response) {
                    if (response.status == 200){
                        if (response.data.code == 2000){
                            _that.cautonpay(JSON.parse(response.data.jsondata))
                        }
                    }
                }).catch(function(error) {
                    //console.log(error);
                });

            },
            cautonpay(config){
                var _that = this;
                //有支付
                function jsApiCall(config) {
                    WeixinJSBridge.invoke('getBrandWCPayRequest', config, function(res) {
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
            cauton_ordersn(){
                var now = new Date()
                var month = now.getMonth() + 1
                var day = now.getDate()
                var hour = now.getHours()
                var minutes = now.getMinutes()
                var seconds = now.getSeconds()
                return now.getFullYear().toString() + month.toString() + day + hour + minutes + seconds + (Math.round(Math.random() * 89 + 100)).toString()
            }
        },

    }
</script>
<style scoped>
    @import url(../../assets/css/caution1.css);
    @import url(../../assets/css/caution2.css);
</style>