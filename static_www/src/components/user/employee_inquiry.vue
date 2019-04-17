<template  style="background: #333;">
    <div class="margin_bg">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">员工查询</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--在售和下架-->
        <!--员工查询的小图标-->
          <div class="employee_img">
              <img style="width:1.5rem" src="@/assets/images/user/secher.png"/>
              <p>谨防于非平台客服人员联系</p>
          </div>
        <!--姓名查询-->
        <div class="input_phone">
            <div class="input_phone_p">
                <yd-cell-item>
                    <yd-input slot="right" v-model="mobile" regex="mobile" placeholder="请输入员工手机号码"></yd-input>
                </yd-cell-item>
            </div>
        </div>
       <!--点击查询-->
        <div class="bottom_an">
        <yd-button bgcolor="#af773e" color="#fff" size="large" type="warning" @click.native="handleClick">立 即 查 询</yd-button>
        </div>
        <!--调转-->
        <router-view></router-view>
    </div>
</template>
<script>
    import Vue from 'vue';
   import {Input} from 'vue-ydui/dist/lib.px/input';
    import axios from 'axios'
   import { Confirm, Alert, Toast, Notify, Loading } from 'vue-ydui/dist/lib.px/dialog';
    import weipai from '@/commonjs/util.js'
Vue.component(Input.name, Input);
    export default {
        data() {
            return {
                mobile: '',
            }
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            handleClick() {
                var that = this;
                //提示
                var moblie = weipai.Util.trim(this.mobile);
                if(moblie==""){
                    this.$dialog.alert({mes: '手机号为空！'});
                    return false;
                }
                if(!weipai.checkmobile(moblie)){
                    this.$dialog.alert({mes: '手机号格式错误！'});
                    return false;
                }
                this.$dialog.loading.open('正在查询.....');
                setTimeout(() => {
                    this.$dialog.loading.close();
                }, 1000);
                var dataparam =new FormData();
                dataparam.append('token',window.storeWithExpiration.get('token'))
                dataparam.append('keywords',moblie)
                axios.post('/checkworker',dataparam)
                    .then(function (response) {
                            return response.data;
                    }).then(function(response){
                    if(response.code=="4000"){
                        that.$dialog.alert({mes: '没有查询到员工信息'});
                    }else{
                        that.$store.state.staff_mobile = moblie;
                        that.$router.push({ path:'/user/staff_results/staff_index'})
                    }
                })    .catch(function (error) {
                        console.log(error)
                });




            },
        }
    }
</script>
<style>
    .margin_bg{ padding-top: 1rem; background: #333; height: 100%; width: 100%; }
    /*员工查询小图标*/
    .employee_img{ padding-top: 1.5rem;}
    .employee_img p{ color: #eee; font-size: .3rem;}
    /*输入姓名*/
    .input_phone{ padding-left: 1rem;}
    .input_phone_p{  margin-top: 1rem;  width: 90%; border-bottom: 1px solid #fff}
    /*按钮*/
    .bottom_an{  width: 100%; padding:5%;  margin-top: 2rem;}
</style>