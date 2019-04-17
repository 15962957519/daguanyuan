<template>
    <!--个人的展示-->
    <div class="margin">
        <div v-on:click="cancelShare()" style=' position: fixed; height: 100%;top: 0; display: none; z-index: 99999;' v-show="shareShow">
            <img style="width: 100%;" src="@/assets/images/renzheng.png" />
        </div>
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">实名认证</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--选择实名认的类型，和店铺认证的类型-->
             <!--个人信息设置-->
            <yd-cell-group>
            <yd-cell-item style="height: 1.2rem;">

                <span slot="left"> <img style="width:1rem; margin-right: .1rem; border-radius: 1rem;" :src="user_data.head_pic"/></span>
                <span slot="left">{{user_data.nickname}}</span>
                <template v-if="user_data.is_authentication==2">
                    <span slot="right" class="border_c">已经认证</span>
                </template>
                <template v-else-if="user_data.is_authentication==1">
                    <span slot="right" class="border_c">审核中..</span>
                </template>
                <template v-else>
                    <span slot="right" class="border_c">未认证</span>
                </template>

            </yd-cell-item>
            </yd-cell-group>
            <!--未认证显示-->
           <template v-if="user_data.is_authentication==0">
            <div class="authencation_img" >
                <ul>
                    <router-link tag="li"  to="/user/authentication/people">
                        <img  style="width:1.5rem" src="@/assets/images/user/authentication1.png"/>
                        <p>个人实名认证</p>
                    </router-link>
                    <router-link tag="li"  to="/user/authentication/shop_authentication">
                        <img  style="width:1.5rem" src="@/assets/images/user/authentication2.png"/>
                        <p>企业实名认证</p>
                    </router-link>
                </ul>
            </div>
            <div class="smrs_tq_group">
                <div class="bgF2F2F2">信用等级高</div>
                <div class="bgF2F2F2">有专属认证标识</div>
                <div class="bgF2F2F2">店铺显示：未认证</div>
                <div class="bgF2F2F2" >粉丝放心出价</div>
                <div class="bgF2F2F2">交易有保障</div>
            </div>
        </template>
           <!--审核中个人显示-->
            <template  v-if="user_data.is_authentication==1 &&  card.identity_type==1">
              <div class="authencation_img1">
                    <ul>
                         <li>
                            <img  :src="card.verifyIdcodefront"/>
                         </li>
                        <p>为保障您的信息安全，图片已做模糊处理。</p>
                    </ul>
                </div>
              <div class="smrs_tq_group">
                <div class="bgF2F2F2">姓名：{{card.name}}</div>
                <div class="bgF2F2F2">电话：{{card.telephone}}</div>
                <div class="bgF2F2F2">身份证号码：{{card.idcode}}</div>
            </div>
              <!--<div v-show="card.is_pay== 0" class="people_ti11"   @click.stop="payupshop">支付费用</div>-->
              <div v-show="card.is_pay== 0" class="people_ti11"   @click.stop="wxshare">支付费用</div>
                <div class="people_title"   @click.stop="modify"><img src="@/assets/images/user/black.png">修改个人信息</div>
            </template>
           <!--审核中企业显示-->
            <template  v-if="user_data.is_authentication==1 && card.identity_type==2">
            <div class="authencation_img12">
                <ul>
                    <li>
                        <img  :src="card.verifyIdcodefront"/>
                    </li>
                    <p>为保障您的信息安全，图片已做模糊处理。</p>
                </ul>
            </div>
            <div class="smrs_tq_group1">
                <div class="bgF2F2F2">企业名称：{{card.name}}</div>
            </div>
                <div class="people_title"   @click.stop="modify"><img src="@/assets/images/user/black.png">修改企业信息</div>
            <!--<div v-show="card.is_pay== 0" class="people_ti11"   @click.stop="payupshop1">支付费用</div>-->
            <div v-show="card.is_pay== 0" class="people_ti11"   @click.stop="wxshare">支付费用</div>

            </template>
           <!--审核完成个人显示-->
           <template  v-if="user_data.is_authentication==2 && card.identity_type==1" >
               <div class="authencation_img1">
                   <ul>
                       <li>
                           <img  :src="card.verifyIdcodefront"/>
                       </li>
                       <p>为保障您的信息安全，图片已做模糊处理。</p>
                   </ul>
               </div>
               <div class="smrs_tq_group">
                   <div class="bgF2F2F2">姓名：{{card.name }}</div>
                   <div class="bgF2F2F2">电话：{{card.telephone |hideMiddle}}</div>
                   <div class="bgF2F2F2">身份证号码：{{card.idcode|hideMiddle}}</div>
               </div>
           </template>
           <!--审核完成企业显示-->
           <template  v-if="user_data.is_authentication==2 && card.identity_type==2">
            <div class="authencation_img12">
                <ul>
                    <li>
                        <img  :src="card.verifyIdcodefront"/>
                    </li>
                    <p>为保障您的信息安全，图片已做模糊处理。</p>
                </ul>
            </div>
            <div class="smrs_tq_group1">
                <div class="bgF2F2F2">企业名称：{{card.name}}</div>
            </div>
            <div v-show="card.is_pay== 0" class="people_ti11"   @click.stop="payupshop1">支付费用</div>
        </template>
    </div>
    <!--企业的展示-->

</template>
<script type="text/babel">
    import { mapState} from 'vuex';
    export default {
        data(){
        return{
            card:{},
            shareShow:false,
        }
        },
        mounted: function () {
            this.category();
        },
        methods: {
            wxshare:function(){
                this.shareShow = true
            },
            cancelShare:function(){
                this.shareShow = false
            },
            goback () {
                this.$router.go(-1)
            },
            category () {
                var token = window.storeWithExpiration.get('token');
                var url = "/identify_edit?token=" + token;
                var that = this;
                this.$axios.get(url).then(function(response){
                   // console.log(11,response.data.data)
                   // console.log(22,response.data.data.identity_type)
                    that.card=response.data.data
                })
            },
            // ordermy(id){
            //     this.$router.push({ name: 'myorder_link', query: { id: id }})
            // }
            //支付页面
            payupshop(){
                var _that = this;
                var order_sn = _that.ordersn();
                var token = storeWithExpiration.get('token');
                //console.log(order_sn)
                var url = '/balancerecharge?token=' + token + '&state=21'+ '&order_sn=' + order_sn
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
                            _that.$dialog.toast({
                                mes: '支付成功',
                                timeout: 1500,
                                icon: 'success',
                            });
                            _that.card.is_pay = 1;
                            //  _that.$router.push({ path: '/user/authentication/authenication_loding' })
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
            ordersn(){
                var now = new Date()
                var month = now.getMonth() + 1
                var day = now.getDate()
                var hour = now.getHours()
                var minutes = now.getMinutes()
                var seconds = now.getSeconds()
                return now.getFullYear().toString() + month.toString() + day + hour + minutes + seconds + (Math.round(Math.random() * 89 + 100)).toString()
            },
            modify(){
                var _that = this;
                if(_that.card.identity_type==1){
                    this.$router.push({ path: '/user/authentication/people'})
                }
                if(_that.card.identity_type==2){
                    this.$router.push({ path: '/user/authentication/shop_authentication'})
                }
            },
            //企业支付
            payupshop1(){
                var _that = this;
                var order_sn = _that.ordersn();
                var token = storeWithExpiration.get('token');
                //console.log(order_sn)
                var url = '/balancerecharge?token=' + token + '&state=22'+ '&order_sn=' + order_sn
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
        },
        computed:mapState({
            user_data(state){
                if(this.$weipai.isEmptyObject(state.menuItems)){
                    this.$store.dispatch("getuserinfo");
                }
                return state.menuItems
            }
        }),
        filters: {
            hideMiddle(val) {
                val = val || '';
                if(val!=undefined){
                    return `${val.substring(0,3)}****${val.substring(val.length-3)}`
                }else{
                    return val;
                }

            }

        }
    }

</script>
<style scoped>
    .margin{ padding-top: 1rem;}
    /*实名认证图标和店铺的认证图标*/
    .authencation_img{ height: 3rem; width: 100%; background:#af773e ;}
    .authencation_img ul li{  width: 50%; float: left; padding-top: .7rem;}
    .authencation_img ul li p{ color: #fff; font-size: .3rem  }
    /*会员功能介绍*/
    .smrs_tq_group{ background: #fff;  height: 3rem; line-height:1rem; text-align: left; text-indent: .3rem; }
    .smrs_tq_group1{ background: #fff;  height: 1rem; line-height:1rem; text-align: left; text-indent: .3rem; }
    .bgF2F2F2{ background: #fff; border-top: 1px solid #eee;height: 1rem;}
    .border_c{border: 1px solid #af773e; color:#af773e; padding: .1rem .3rem; }
      /*审核中的样式*/
    .authencation_img1{ height: 5.2rem; margin: 0 auto; width: 100%; background:#fff;  padding-top: .3rem; margin-top: -.3rem; }
    .authencation_img1 ul li{  width: 100%; float: left; }
    .authencation_img1 ul li img{ width: 100%; height: 4rem; border-radius: .3rem;  width: 90%; box-shadow: 1px 2px 6px #eee; filter: blur(1px);}
    .authencation_img1 ul  p{ color: #fff; font-size: .3rem; color: #ff0000; line-height: .8rem; }
    .authencation_img12{ height: 9rem; margin: 0 auto; width: 100%; background:#fff;  padding-top: .3rem; margin-top: -.3rem; }
    .authencation_img12 ul li{  width: 80%; margin: 0 auto;  }
    .authencation_img12 ul li img{  height: 8rem;   width: 90%; box-shadow: 1px 2px 6px #eee; filter: blur(1px);}
    .authencation_img12 ul  p{ color: #fff; font-size: .3rem; color: #ff0000; line-height: .8rem; }
     .people_ti11{ text-align: left; padding: .2rem; height: 1rem; width: 85%; text-align: center; margin: .5rem auto;  background: #af773e; color: #fff;  font-size: .35rem; line-height: .6rem;}
    .people_title{ color:#af773e; vertical-align: middle ;text-align: right; margin-right: .2rem; margin-top: .2rem }
    .people_title img{ height: .5rem;vertical-align: middle;  }
</style>