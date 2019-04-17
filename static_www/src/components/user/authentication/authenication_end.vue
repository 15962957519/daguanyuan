<template>
    <div>
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">{{card.title}}信息提交</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--店铺的个人信息-->
        <div class="my_shop_po">
            <dl>
                <dt> <img :src="user_data.head_pic"></dt>
                <dd ><strong>{{user_data.nickname}}</strong></dd>
                <!--<dd>个人宣言：{{user_data.nick_name}}</dd>-->
            </dl>
        </div>
        <!--个人-->
        <yd-cell-group  v-show="card.state_numb==21">
            <yd-cell-item>
                <span slot="left">姓名：</span>
                <span slot="right">{{card.uname}}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">电话：</span>
                <span slot="right">{{card.mobile}}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">身份证号码：</span>
                <span slot="right">{{card.number}}</span>
            </yd-cell-item>
        </yd-cell-group>
        <!--企业-->
        <yd-cell-group  v-show="card.state_numb==22">
            <yd-cell-item>
                <span slot="left">企业名称：</span>
                <span slot="right">{{card.uname}}</span>
            </yd-cell-item>
        </yd-cell-group>
        <!--提交-->
        <div class="people_ti" >
            <yd-button bgcolor="#af773e" color="#fff" size="large" type="warning"  @click.native="payupshop">{{message}}</yd-button>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {CellGroup, CellItem} from 'vue-ydui/dist/lib.rem/cell';
    /* 使用px：import {CellGroup, CellItem} from 'vue-ydui/dist/lib.px/cell'; */
    import { mapState} from 'vuex';
    Vue.component(CellGroup.name, CellGroup);
    Vue.component(CellItem.name, CellItem);

    export default {
        name: "my_shop",
        data(){
          return{
              message:"确认信息并提交支付",
          }
        },

        methods:{
            goback () {
                this.$router.go(-1)
            },
            //支付页面
            payupshop(){
                var _that = this;
                var order_sn = _that.ordersn();
                var token = storeWithExpiration.get('token');
                //console.log(order_sn)
                var url = '/balancerecharge?token=' + token + '&state='+_that.card.state_numb + '&order_sn=' + order_sn
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
                            _that.$store.dispatch("getuserinfo");
                            _that.$dialog.toast({
                                mes: '支付成功',
                                timeout: 1500,
                                icon: 'success',
                            });

                            //_that.message="信息审核中..."
                            _that.$router.push({ path: '/user/authentication/authentication_index' })
                          //  _that.$router.push({ path: '/user/authentication/authenication_loding' })
                        }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                            _that.$store.dispatch("getuserinfo");
                            _that.$dialog.toast({
                                mes: '已取消支付',
                                timeout: 1500,
                                icon: 'success',
                            });

                            //_that.message="信息审核中..."
                            _that.$router.push({ path: '/user/authentication/authentication_index' })
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
            },
            card(state){
                return state.card
                console.log( state.card)
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






