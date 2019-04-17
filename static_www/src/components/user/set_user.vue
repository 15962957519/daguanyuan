<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">设置</p>
        </yd-navbar>
        <!--个人信息设置-->
        <!--</div>-->
        <!--<div class="header">-->
            <!--<img  :src="user_data.head_pic" @click="xiugai" >-->
            <!--<div>-->
                <!--<div class="name">-->
                    <!--<input style="-webkit-user-select:text !important;"-->
                           <!--value="user_data.nickname"-->
                                         <!--v-model="user_data.nickname" @change='gainame'>-->
                <!--</div>-->
                <!--<div class="level">（点击头像可修改）</div>-->
            <!--</div>-->
        <!--</div>-->
        <!--替换图片-->
        <input ref="filElem" hidden  @change='add_img' type="file">
        <yd-cell-group>
            <yd-cell-item style="height: 1.3rem;">
                <span slot="left">头像  <span>(点击头像可修改)</span></span>
                <span slot="right"><img slot="right" style="width: 1rem; border-radius: 1rem;" :src="user_data.head_pic"  @click="xiugai" /></span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">用户名</span>
                <span slot="right">
                <input style="-webkit-user-select:text !important;"
                            v-model="user_data.nickname" @change='gainame'>
                    <!--<span @click="openConfrim" size="large">{{user_data.nickname}}</span>-->
                </span>
            </yd-cell-item>
            <yd-cell-item arrow @click.native="phone_verification">
                <span slot="left">手机</span>
                <span slot="right">{{user_data.mobile}}</span>
            </yd-cell-item>
            <yd-cell-item arrow>
                <span slot="left">邮箱</span>
                <span slot="right">{{user_data.email}}</span>
            </yd-cell-item>
        </yd-cell-group>
        <!--收获地址-->
        <yd-cell-group>
            <yd-cell-item arrow>
                <span slot="left">地址</span>
                <span slot="right">默认微信收货地址</span>
            </yd-cell-item>
        </yd-cell-group>


    </div>
</template>
<script>
    import Vue from 'vue';
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
    /* 使用px：import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar'; */
    Vue.component(NavBar.name, NavBar);
    Vue.component(NavBarBackIcon.name, NavBarBackIcon);
    Vue.component(NavBarNextIcon.name, NavBarNextIcon);
    import {CellGroup, CellItem} from 'vue-ydui/dist/lib.rem/cell';
    /* 使用px：import {CellGroup, CellItem} from 'vue-ydui/dist/lib.px/cell'; */

    Vue.component(CellGroup.name, CellGroup);
    Vue.component(CellItem.name, CellItem);
    import { mapState} from 'vuex';
    import weipai from '@/commonjs/util.js'
    export default {
        data() {
            return {

            }
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            //phone_verification
            phone_verification(){
                this.$router.push({ name: 'phone_number_link',query:{type:2}})
            },
            getusergoodsinfo:function(){
                var that = this;
                var token = window.storeWithExpiration.get('token');
                this.$axios.get( '/', {
                    params: {
                        token: token
                    }
                }).then(function(response) {
                    if(response.status=='200'){
                        return response.data;
                    }
                }).then(function(json) {
                    that.goodsinfo = json.data;

                    //  console.log( that.goodsinfo)


                }).catch(function(ex) {

                });

            },
            //    上传图片
            gainame(){
                console.log(123)
                var  _this =this
                let form = new FormData();
                form.append('nickname',_this.user_data.nickname)
                form.append('token',storeWithExpiration.get('token'))
                this.$axios.post("/personalsettings",form).then(function(response){
                }).catch(function (error){
                    //console.log(error);
                });
            },
            xiugai(){
                this.$refs.filElem.dispatchEvent(new MouseEvent('click'))
            },
            add_img(event){
                var  _this =this
               console.log(event)
                let reader =new FileReader();
                let img1=event.target.files[0];
                if (!event || !window.FileReader) return  // 看支持不支持FileReader
                reader.readAsDataURL(img1) // 这里是最关键的一步，转换就在这里
                reader.onloadend = function () {
                    _this.user_data.head_pic=this.result
                }
                let type=img1.type;//文件的类型，判断是否是图片
                let size=img1.size;//文件的大小，判断图片的大小
                if(size>3145728){
                    alert('请选择3M以内的图片！');
                    return false;
                }
                let form = new FormData();
                form.append('nickname',_this.user_data.nickname)
                form.append('token',storeWithExpiration.get('token'))
                form.append('head_pic',img1);
                this.$axios.post("/personalsettings",form).then(function(response){
                }).catch(function (error){
                    //console.log(error);
                });

            },
        },

        computed:mapState({
            user_data(state){
                if(weipai.isEmptyObject(state.menuItems)){
                    this.$store.dispatch("getuserinfo");
                }
                return state.menuItems
            }
        }),
    }

</script>
<style>
    .margin{ padding-top: 1.05rem;}
</style>