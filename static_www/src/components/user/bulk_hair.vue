<template>
    <div class="margin_bur">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;" slot="center">群发商品</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--群发标题-->
        <div class="group_hair">

            <!--头像-->
            <div class="group_hair_img">
            <img :src="user_data.head_pic">
            </div>
            <!--个人信息-->
            <div class="group_hair_title">
                <p><strong>{{user_data.nickname}}</strong></p>
                <p class="group_hair_title_e">今日剩余次数0次</p>
            </div>
            <!--点击发布-->
            <div class="group_hair_button" @click.stop="realsendweixinsms">点击群发</div>
        </div>
        <!--渲染商品-->
        <yd-radio-group v-model="radio2" color="#af773e">
                <!--头像-->
                <!--个人信息-->
                <template v-for="(item,key) in goodslist">
                    <div class="want_img">
                    <div class="want_img_img">
                        <img :src="getCurlofimgUsenoAuth(item.original_img)">
                    </div>
                    <div class="want_img_title">
                        <p><strong>{{user_data.nickname}}</strong></p>
                        <p class="group_hair_title_e">{{item.goods_name}}</p>
                    </div>
                        <div class="radio">
                            <input :id="item.goods_id"  name="sendmessage"  :value="item.goods_id" v-model="param"    type="radio" style="float: right; width: 20px; margin-right: .1rem; margin-top:.2rem;"/>
                            <label :for="item.goods_id" >选中</label >
                        </div>
                    </div>
                </template>

        </yd-radio-group>

    </div>
</template>

<script>
    import Vue from 'vue';
    import {Radio, RadioGroup} from 'vue-ydui/dist/lib.rem/radio';
    /* 使用px：import {Radio, RadioGroup} from 'vue-ydui/dist/lib.px/radio'; */
    import { mapState} from 'vuex';
    Vue.component(Radio.name, Radio);
    Vue.component(RadioGroup.name, RadioGroup);
    export default {
        name: "bulk_hair",
        data() {
            return {
                radio2: 2,
                goodslist:[],
                param:0
            }
        },
        mounted:function(){
            this.getproductlist();
        },
        methods:{
            getCurlofimgUsenoAuth(a,b,c){
                return  this.$weipai.getCurlofimgUsenoAuth(a,b,c,false);
            },
            goback () {
                this.$router.go(-1)
            },
            getproductlist(){
                var that =this;
                //获取该用户所有议价信息
                var token = window.storeWithExpiration.get('token');
                  this.$axios.get( '/spread_goods/', {
                    params: {
                        token: token
                    }
                }).then(function(response) {
                    if(response.status=='200'){
                        return response.data;
                    }
                }).then(function(json) {
                    that.goodslist = json.data.my_goods;
                }).catch(function(ex) {

                });
            } ,
            realsendweixinsms(){
                var that =this;
                //群发行为
                var goods_id = Number(this.param)
                if(goods_id==0){
                    that.$dialog.toast({
                        mes: "请选择要群发的商品",
                        timeout: 1500,
                    });
                    return
                }
                var token = window.storeWithExpiration.get('token');
                  this.$axios.get( '/realsendweixinsms/', {
                    params: {
                        token: token,
                        goods_id: goods_id
                    }
                }).then(function(response) {
                    if(response.status=='200'){
                        return response.data;
                    }
                }).then(function(json) {
                      that.$dialog.alert({mes: json.msg})
                    that.goodslist = json.data.my_goods;

                }).catch(function(ex) {

                });
            }
        },
        computed:mapState({
            user_data(state){
                if(this.$weipai.isEmptyObject(state.menuItems)){
                    this.$store.dispatch("getuserinfo");
                }
                return state.menuItems
            }
        }),
    }
</script>

<style scoped>
    /*个人信息*/

    .group_hair{width: 100%; height: 2.2rem; background: #fff; padding: 1rem 0rem; }
    .group_hair_img{ width: 1rem; height: 1rem; margin-left: .1rem;float: left;}
    .group_hair_img img{ height: .8rem; width: .8rem; margin: .1rem; border-radius: .1rem;}
    .group_hair_title{ float: left; text-align: left; margin-top: .1rem; margin-left: .05rem;}
    .group_hair_title p{ margin-top: .05rem;}
    .group_hair_title_e{ color: #666;}
    .group_hair_button{ font-size: .3rem; line-height: .65rem; width: 1.8rem;  height: .65rem; background:#af773e; float: right;margin-right: .2rem; margin-top:.22rem; color: #fff;}
     /*要群发的商品*/
    .want_img{ width: 100%; height: 2rem; background: #fff; margin-top:.1rem;position: relative}
    .want_img_img{ width: 2rem; height: 2rem; margin-left: .1rem;float: left; width: 25%}
    .want_img_img img{ height: 1.8rem; width: 1.8rem; margin: .1rem; border-radius: .1rem;}
    .want_img_title{ float: left; text-align: left; margin-top: .1rem; padding-left: .1rem;  width: 42%; margin-left: .1rem; height: 1.5rem; overflow: hidden;}
    .want_img_title p{ margin-top: .1rem;}
    .radio{
        display: inline-block;
        width: 58px;
        position: absolute;
        top:50%;
        padding:1px;
        -webkit-transform: translateX(-50%) translateY(-50%);
        -webkit-transform: translateX(-50%) translateY(-50%);
        -moz-transform: translateX(-50%) translateY(-50%);
        -ms-transform: translateX(-50%) translateY(-50%);
        transform: translateX(-50%) translateY(-50%);
        overflow: hidden;
    }
    input[type="radio"] + label::before {
        content: "\a0"; /*不换行空格*/
        display: inline-block;
        vertical-align: middle;
        font-size: 22px;
        width: 1em;
        height: 1em;
        margin-right: .4em;
        border-radius: 50%;
        border: 1px solid #af773e;
        text-indent: .15em;
        line-height: 1;
    }

    input[type="radio"]:checked + label::before {
        background-color: #af773e;
        background-clip: content-box;
        padding: .2em;
    }

    input[type="radio"] {
        position: absolute;
        clip: rect(0, 0, 0, 0);
    }
</style>