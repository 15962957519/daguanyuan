<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">查询结果</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--在售和下架-->
        <div class="logo_img">
            <img src="@/assets/images/user/logo.png">
        </div>
        <!--结果出来-->
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;位 : <span class="color_c">{{userinfo.worker_level}}</span></span>

            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">称&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;呼 : <span class="color_c">{{userinfo.username}}</span></span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">手机号码 : <span class="color_c">{{userinfo.phone}}</span></span>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">{{userinfo.belong}}</span>
            </yd-cell-item>
        </yd-cell-group>
    </div>
</template>
<script>
    import axios from 'axios'
    export default {
        data() {
            return {
                    userinfo:{}
            }
        },
        mounted:function(){
            this.getuserinfo();
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            getuserinfo(){
                var that= this;
                //获取手机号码
                var mobile =   that.$store.state.staff_mobile;
                axios.get('/checkworkerresult',   {params: {
                    token: storeWithExpiration.get('token'),
                    keywords:mobile
                }})
                    .then(function (response) {
                        return response.data;
                    }).then(function(response){
                        console.log(response)
                    if(response.code=="4000"){
                        that.$dialog.alert({mes: '没有查询到员工信息'});
                    }else{
                        that.userinfo = response.data;
                        that.userinfo.belong = "属于平台员工"
                    }
                })    .catch(function (error) {
                    console.log(error)
                });
            }
        }
    }
</script>
<style>
    .margin{ padding-top: 1rem;}
    .color_c{ color: #818181;}
    .logo_img{ background: #fff; padding-top: .1rem;}
    .logo_img img{ width:1.5rem; height: 1.5rem; margin-top: .5rem; margin-bottom: .5rem;}
</style>