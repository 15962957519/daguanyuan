<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">绑定手机</p>
            <!--<img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>-->
        </yd-navbar>
        <!--个人手机-->
        <div class="phone_nu">
            <h2>绑定手机号码</h2>
            <!--<yd-cell-item>-->
                <!--<yd-icon slot="icon" size=".45rem"></yd-icon>-->
                <!--<yd-input type="text" slot="right"  name="mobile"   ref="mobile" required regex="mobile"  v-model="mobile" placeholder="请输入手机号码"></yd-input>-->
            <!--</yd-cell-item>-->

            <!--<yd-cell-item>-->
                <!--<span slot="left">验证码：</span>-->
                <!--<yd-input slot="right" v-model="mobile_code" ref="input9"  required   regex="^\d{4}$"  placeholder="验证码"></yd-input>-->
                <!--<yd-sendcode class="btn_set"   storage-key="dashuaibi" slot="right" v-model="start1" @click.native="sendCode1" type="warning"></yd-sendcode>-->
            <!--</yd-cell-item>-->
            <!--<yd-button-group>-->
                <!--<yd-button class="btn_ds" size="large" @click.native="clickHander">确认提交</yd-button>-->
            <!--</yd-button-group>-->
            <yd-cell-item>
                <yd-icon slot="icon" size=".45rem"></yd-icon>
                <yd-input type="text" slot="right"  name="mobile"   ref="mobile"   v-model="mobile" placeholder="请输入手机号码"></yd-input>
                <yd-sendcode class="btn_set"   storage-key="dashuaibi" slot="right" v-model="start1" @click.native="sendCode1" ></yd-sendcode>
            </yd-cell-item>

            <yd-cell-item>
                <span slot="left">验证码：</span>
                <yd-input slot="right" v-model="mobile_code" ref="input9"  placeholder="验证码"></yd-input>
            </yd-cell-item>

                <div class="btn_ds" size="large" @click="clickHander">确认提交</div>

        </div>



    </div>
</template>
<script type="text/babel">
    import Vue from 'vue';
    import {SendCode} from 'vue-ydui/dist/lib.px/sendcode';
    import { mapState} from 'vuex';
    import {Input} from 'vue-ydui/dist/lib.rem/input';
    /* 使用px：import {Input} from 'vue-ydui/dist/lib.px/input'; */
    Vue.component(Input.name, Input);
    Vue.component(SendCode.name, SendCode);
    export default {
        data() {
            return {
                start1: false,
                mobile: '',
                mobile_code: '',
                result: '',
                type:0
            }
        },
        mounted:function(){
            this.type = this.$route.query.type || 1;

            var token = window.storeWithExpiration.get('mobilecheckobj') || {};
            try{
                var token = JSON.parse(token); //由JSON字符串转换为JSON对象
                this.mobile  =  token.mobile
                this.mobile_code  =  token.mobilecode

                var exdate=new Date()
                var millisecond = exdate.getTime()
                console.log(typeof token)
            }catch(err){


            }
        },
        methods: {
            sendCode1() {

                var that = this;
                const mobile = this.$refs.mobile;
                if(mobile.valid==false) {
                    this.$dialog.toast({
                        mes: '手机号码错误',
                        icon: 'success',
                        timeout: 1500
                    });
                    return false;
                }
                this.$dialog.loading.open('发送中...');
                setTimeout(() => {
                    this.start1 = true;
                    this.$dialog.loading.close();

                    //执行发送的
                    var dd =new FormData();
                    dd.append('token',window.storeWithExpiration.get('token'))
                    dd.append('mobile',that.$weipai.Util.trim(that.mobile))
                    that.$axios.post( '/sendmoblievertifycode', dd).then(function(response) {
                        if(response.status=='200'){
                            return response.data;
                        }
                    }).then(function(json) {

                        that.$dialog.toast({
                            mes: json.message,
                            icon: 'success',
                            timeout: 1500
                        });

                    }).catch(function(ex) {

                    });
                }, 1000);
            },
            goback () {
                this.$router.go(-1)
            },
            clickHander() {
                const input = this.$refs.input9;
                if(input.valid==false){
                    this.$dialog.toast({
                        mes: '验证码错误',
                        icon: 'success',
                        timeout: 1500
                    });
                    return false;
                }
                var that = this;
                var mobilecheckobj = {};

                var exdate=new Date()
                var millisecond = exdate.getTime()
                mobilecheckobj.mobile=that.$weipai.Util.trim(that.mobile);
                mobilecheckobj.mobilecode=that.$weipai.Util.trim(that.mobile_code);
                mobilecheckobj.remainingtime =millisecond;
                window.storeWithExpiration.set('mobilecheckobj',mobilecheckobj,1);
                setTimeout(() => {
                    this.$dialog.loading.close();
                    //执行发送的
                    var dd =new FormData();
                    dd.append('token',window.storeWithExpiration.get('token'))
                    dd.append('mobile',that.$weipai.Util.trim(that.mobile))
                    dd.append('mobilecode',that.$weipai.Util.trim(that.mobile_code))
                    that.$axios.post( '/checkmcode', dd).then(function(response) {
                        if(response.status =='200'){
                            return response.data;
                        }
                    }).then(function(json) {
                        var message = json.message;
                        if(json.code=='2000'){
                            that.$dialog.toast({
                                mes: message,
                                icon: 'success',
                                timeout: 1500
                            });
                            if(that.type == 1){
                                that.$router.push({name:'publishlink'});
                            }else{
                                that.$router.push({path:'/user/set_user'});
                            }

                            return true;
                        }else{
                            that.$dialog.toast({
                                mes: message,
                                icon: 'error',
                                timeout: 1500
                            });
                        }
                    }).catch(function(ex) {
                        console.log(ex)
                        that.$dialog.toast({
                            mes: JSON.stringify(ex),
                            icon: 'error',
                            timeout: 1500
                        });
                    });
                }, 1000);
            }
        },
        computed:mapState({
            user_data(state){
                if(this.$weipai.isEmptyObject(state.mobilecheckobj)){
                    //从缓存里面获取
                    var token = window.storeWithExpiration.get('mobilecheckobj');
                    this.$store.state.mobilecheckobj = token;
                }
                return state.mobilecheckobj
            }
        }),
    }
</script>

<style  scoped>
    .yd-cell-item{  padding: 0 .5rem;border: 0;}
    .margin{ padding-top: .6rem;}
    .phone_nu{  padding: 0 .5rem; background: #fff; padding-top: 1rem; height: 100%; box-shadow: 1px 2px 5px #eee;}
    .phone_nu h2{  height: 1.5rem; font-size:.4rem; margin-top: .4rem;}
    .btn_ds{ background: #af773e; margin-bottom: .5rem; height: .8rem; line-height:.8rem;font-size: .3rem; margin-top:.5rem;color: #fff;}
    .btn_set{ background: #eee !important; color:#af773e!important; }
</style>