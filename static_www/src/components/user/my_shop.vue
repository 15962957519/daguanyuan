<template>
    <div>
        <!--顶部导航-->
        <yd-navbar bgcolor="#333" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;color: #fff;" slot="center">{{user_data.nickname}}的店铺</p>
        </yd-navbar>
        <!--店铺的个人信息-->
        <div class="my_shop_po">
        <dl>
            <dt> <img :src="user_data.head_pic"></dt>
            <dd style="color: #af773e"><strong>{{user_data.nickname}}</strong></dd>
            <!--<dd>个人宣言：</dd>-->
        </dl>
        </div>
        <!--功能介绍-->
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">是否认证</span>

                <template v-if="user_data.is_authentication==2">
                    <span slot="right">已认证</span>
                </template>
                <template v-else>
                    <span slot="right">未认证</span>
                </template>

            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">担保交易</span>
                <span slot="right">是</span>
            </yd-cell-item>
            <yd-cell-item arrow type="label">
                <span slot="left">店铺二维码</span>
                <span slot="right">   <a  :href="this.qrcode">点击查看</a></span>
            </yd-cell-item>
            <yd-cell-item arrow type="label">
                <span slot="left">全部商品</span>
            </yd-cell-item>
            <div style="height: .1rem; background:#eee;"></div>
            <yd-cell-item arrow type="label" @click.native="roulink(1)">
                <span slot="left">全部商品<span class="color_w">
                    <template v-if="goodsinfo.all_count>0">
                         ({{goodsinfo.all_count}})件
                    </template>
                    <template v-else>
                            （0件）
                    </template>
                </span></span>
                <span slot="right">
                    <template v-for="(item,index) in goodsinfo.allgoods">
                                  <img class="img_b" :src="getCurlofimgUsenoAuth(item.original_img)">
                    </template>
                </span>
            </yd-cell-item>

            <yd-cell-item arrow type="label" @click.native="roulink(2)">
                <span slot="left">新品<span class="color_w"> ({{goodsinfo.new_count}})件</span></span>
                <span slot="right">
                   <template v-for="(item,index) in goodsinfo.newgoods">
                                  <img class="img_b" :src="getCurlofimgUsenoAuth(item.original_img)">
                    </template>
                </span>
            </yd-cell-item>

            <yd-cell-item arrow type="label" @click.native="roulink(3)">
                <span slot="left">特价<span class="color_w"> ({{goodsinfo.tuijian_count}})件</span></span>
                <span slot="right">   <template v-for="(item,index) in goodsinfo.specgoods">
                                  <img class="img_b" :src="getCurlofimgUsenoAuth(item.original_img)">
                    </template></span>
            </yd-cell-item>
        </yd-cell-group>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {CellGroup, CellItem} from 'vue-ydui/dist/lib.rem/cell';
    /* 使用px：import {CellGroup, CellItem} from 'vue-ydui/dist/lib.px/cell'; */
    import {mapState} from 'vuex';
    Vue.component(CellGroup.name, CellGroup);
    Vue.component(CellItem.name, CellItem);
    var env = require('@/../config/dev.env');
    export default {
        name: "my_shop",
        data() {
            return {
                goodsinfo : [],
                qrcode:''
            }
        },
        mounted:function(){
            this.getusergoodsinfo();
            var token = window.storeWithExpiration.get('token');
            this.qrcode = env.default_domain_api+"user/getuserimage?type=1" + "&token=" + token;
        },
        methods:{
            getCurlofimgUsenoAuth(a,b,c){
                return  this.$weipai.getCurlofimgUsenoAuth(a,b,c,false);
            },
            getusergoodsinfo:function(){
                var that = this;
                var token = window.storeWithExpiration.get('token');
                  this.$axios.get( '/storeindexpolymerization', {
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
            roulink(e){
                var type =e
                this.$router.push({
                    name: 'shop_index_link',
                    query: {
                        user_id: this.$route.query.user_id || 0,
                        type:type
                    }
                })
            },
            goback () {
                this.$router.go(-1)
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
    .navbar-bottom-line-color:after {border-color: #333 !important;}
   .my_shop{color: #fff; background: #af773e; padding: .1rem .2rem; border-radius: .1rem;}
   .my_shop_po{ width: 100%; height: 4.1rem; background: #333; padding: 1rem 1rem 0 1rem; border-radius: 0 0 .3rem .3rem;}
    .my_shop_po dl { width: 100%; height: 3.1rem; }
    .my_shop_po dl dt img{ width: 1.5rem; border: 1px  solid #af773e; margin-top: .3rem; height: 1.5rem; border-radius: 1.5rem;}
    .my_shop_po dl dd{ margin-top: .1rem; color: #fff;}
    .color_w{ color: #af773e; margin-left: .1rem}
    .img_b{width: .7rem;height:.7rem;border: 1px  solid #af773e; margin-right:.1rem; }

</style>