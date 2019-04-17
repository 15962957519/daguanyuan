<template>
    <div>
        <!--顶部导航-->
        <yd-navbar bgcolor="#af773e" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;color: #fff;" slot="center">提现</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>
        </yd-navbar>
        <!--空隙-->

        <!--菜单-->

        <div class="integral_recharge_container" style="padding-top: 1rem;">
            <div class="list">
                <div class="item_l"> 账号名称 :</div>
                <div class="item_r" >微信</div>
            </div>
            <div class="list">
                <div class="item_l"> 微信昵称 :</div>
                <div class="item_r" id="order_sn">{{user_data.nickname}}</div>
            </div>
            <div class="list">
                <div class="item_l"> 手机号码 :</div>
                <div class="item_r">
                    <input type="number" v-model="phone" name="jyear" pattern="\d*" datatype="*" placeholder="请输入绑定平台的手机号码"/>
                </div>
            </div>
            <div class="list">
                <div class="item_l"> 提现金额 :</div>
                <div class="item_r">
                    <input type="number" v-model="tx_money" name="jyear" pattern="\d*" datatype="*" :placeholder="['最高可提现 '+user_data.user_money]"/>
                </div>
            </div>
            <div class="but"  @click="payupshop">立即申请</div>
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
                tx_money:'',
                phone:''
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
                if (_that.phone == '') {
                    //alert('请输入金额');
                    _that.$dialog.alert({mes: '请输入手机号码'});
                    return
                }
                if (_that.tx_money == '') {
                    //alert('请输入金额');
                    _that.$dialog.alert({mes: '请输入金额'});
                    return
                }
                if (_that.tx_money >_that.user_data.user_money) {
                    //alert('请输入金额');
                    _that.$dialog.alert({mes: '您的提现金额超过余额！'});
                    return
                }
                var token = storeWithExpiration.get('token');
                //console.log(order_sn)
                var url = "/applyTixian?"+"&money=" +_that.tx_money  +"&account_bank=" +_that.phone + "&account_name=" +_that.user_data.nickname+"&apply_number="+_that.order_sn+"&token="+token;
                  _that.$axios.get(url).then(function(response) {
                    console.log(response)
                    if (response.status == 200){
                        if (response.data.code == 2000){
                            _that.$dialog.toast({
                                mes: '提现申请提交成功。',
                                timeout: 1500,
                                icon: 'success',
                            });
                        }
                    }
                }).catch(function(error) {
                    //console.log(error);
                });
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