<template>
    <div>
        <!--顶部导航-->
        <yd-navbar bgcolor="#af773e" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;color: #fff;" slot="center">充值</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>
        </yd-navbar>
        <!--空隙-->

        <!--菜单-->

                <div class="integral_recharge_container" style="padding-top: 1rem;">
                    <div class="list">
                        <div class="item_l"> 订单号 :</div>
                        <div class="item_r" id="order_sn">{{order_sn}}</div>
                    </div>
                    <div class="list">
                        <div class="item_l"> 支付方式 :</div>
                        <div class="item_r">微信支付</div>
                    </div>
                    <div class="list">
                        <div class="item_l"> 充值金额 :</div>
                        <div class="item_r">
                            <input type="number" v-model="pay_things" name="jyear" pattern="\d*" datatype="*" placeholder="单笔限额50.0万"/>
                        </div>
                    </div>
                    <div class="but"  @click="payupshop">立即充值</div>
                </div>

        <!--菜单结束-->
    </div>
</template>
<script>
  import Vue from 'vue';
  import weipai from '@/commonjs/util.js'
  import { mapState} from 'vuex';
  import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
  /* 使用px：import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar'; */
  Vue.component(NavBar.name, NavBar);
  Vue.component(NavBarBackIcon.name, NavBarBackIcon);
  Vue.component(NavBarNextIcon.name, NavBarNextIcon);
  export  default{
    data(){
      return{
          order_sn:'',
          pay_things:''
      }
    },
      mounted:function(){
          var _that=this
          _that.order_sn=_that.ordersn();
      },
    methods:{
        goback () {
           this.$router.go(-1)
          },
        ordersn(){
            var now = new Date()
            var month = now.getMonth() + 1
            var day = now.getDate()
            var hour = now.getHours()
            var minutes = now.getMinutes()
            var seconds = now.getSeconds()
            return now.getFullYear().toString() + month.toString() + day + hour + minutes + seconds + (Math.round(Math.random() * 89 + 100)).toString()
        },
        payupshop(){
            var _that = this;
            if (_that.pay_things == '') {
                //alert('请输入金额');
                _that.$dialog.alert({mes: '请输入金额'});
                return
            }
            var token = storeWithExpiration.get('token');
            //console.log(order_sn)
            var url = "/balancerecharge?token=" +token +"&order_sn=" +_that.order_sn +"&order_account=" +_that.pay_things + "&state=1";
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
                        // _that.category ()
                        _that.$store.dispatch("getuserinfo");
                        _that.$dialog.toast({
                            mes: '支付成功',
                            timeout: 1500,
                            icon: 'success',
                        });
                         _that.$router.push({ path: '/user/money_bag' })
                    }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                        _that.$dialog.toast({
                            mes: '已取消支付',
                            timeout: 1500,
                            icon: 'success',
                        });
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


    },
      computed:mapState({
          user_data(state){
                    if(weipai.isEmptyObject(state.menuItems)){
                        this.$store.dispatch("getuserinfo");
                    }
                    console.log(state.menuItems)
              return state.menuItems
          },

      }),

  }
</script>
<style scoped>
    @import url(../../../assets/css/recharge.css);
    /*@import '../../../assets/css/rechaarge.css'*/
</style>