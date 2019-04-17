<template>
    <div>
    <div class="heard">
            <div class="heard_img">
                <!--人物和头像-->
                <div class="heard_img_l">
                    <P><img :src="user_data.head_pic"></P>
                    <div class="heard_img_l_p" >{{user_data.store_name}}<br>店铺</div>
                </div>
                <!--个人信息-->
                <div class="heard_img_c">
                        <p>{{user_data.nickname}}</p>
                    <router-link tag="div" class="todo_real" :to="{name:'authentication_index_link'}">
                        <img v-show="user_data.is_authentication==0" src="@/assets/images/user/name_0.png">
                        <img  v-show="user_data.is_authentication==1" src="@/assets/images/user/name_1.png">
                        <img v-show="user_data.is_authentication==2" src="@/assets/images/user/neme_2.png">
                    </router-link>
                </div>
                <!--设置-->
                <router-link tag="div" class="heard_img_r" to="/user/set_user">
                     <img src="@/assets/images/user/set.png">设置
                </router-link>
            </div>
        <div class="fans_many">
            <ul>
                <router-link tag="li" to="/user/fans/fan_index">
                     <img src="@/assets/images/user/star.png"><span>粉丝:</span><span>{{fansilist.fans_num}}</span>
                </router-link>
                <router-link tag="li" to="/user/fans/follow">
                   <img src="@/assets/images/user/like2.png"><span>收藏:</span><span>{{fansilist.goods_collect_count}}</span>
                </router-link>
               <!--<li style="border: 0">-->
                <router-link tag="li" to="/user/fans/history">
                   <img src="@/assets/images/user/intergral.png"><span>足迹:</span><span>{{fansilist.footprint}}</span>
               </router-link>
            </ul>
        </div>
    </div>
    <!--我的订单-->
    <div class="dingdan">
        <div class="dingdan_title">
            <router-link to="/user/myorder/myorder">
            <p class="dingdan_title_l"><img src="@/assets/images/user/order.png"> 我的订单</p>
            <p class="dingdan_title_r">更多<img src="@/assets/images/into small.png"></p>
            </router-link>
        </div>
        <div class="dingdan_lc">
            <ul>
                <li><img src="@/assets/images/user/pay.png" v-on:click="ordermy(0)"><p>我的订单</p></li>
                <li><img src="@/assets/images/user/money.png" v-on:click="ordermy(1)"><p>待付款</p></li>
                <li><img src="@/assets/images/user/send.png" v-on:click="ordermy(2)"><p>待发货</p></li>
                <li><img src="@/assets/images/user/take.png" v-on:click="ordermy(3)"><p>待收货</p></li>
                <li><img src="@/assets/images/user/drawback.png" v-on:click="ordermy(4)"><p>退款售后</p></li>
            </ul>
        </div>
    </div>
        <!--功能展示-->
        <div class="gongneng">
                <router-link tag="div" class="dingdan_title" to="/user/money_bag">
                <p class="dingdan_title_l"><img src="@/assets/images/user/wallet.png">我的钱包</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
                </router-link>
        </div>
        <!--功能结束-->
        <!--<div class="gongneng">-->
            <!--<router-link tag="div" class="dingdan_title" to="/user/shoping_cart">-->
                <!--<p class="dingdan_title_l"><img src="@/assets/images/user/jfgjg.png">我的购物车</p>-->
                <!--<p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>-->
            <!--</router-link>-->
        <!--</div>-->
        <!--功能结束-->
        <div class="gongneng margin_p">
            <router-link tag="div" class="dingdan_title" to="/user/commodity_management">
                <p class="dingdan_title_l"><img src="@/assets/images/user/yujfgr.png">商品管理</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </router-link>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng">
            <router-link tag="div" class="dingdan_title" to="/user/bulk_hair">
                <p class="dingdan_title_l"><img src="@/assets/images/user/tyjfg.png"> 群发商品</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </router-link>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng">
            <div class="dingdan_title"  @click="gotoshop(user_data.user_id)">
                <p class="dingdan_title_l"><img src="@/assets/images/user/shop.png"> 我的店铺</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </div>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng margin_p">
            <router-link tag="div" class="dingdan_title" to="/user/shop_upgrade">
                <p class="dingdan_title_l"><img src="@/assets/images/user/shop.png">店铺推广</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </router-link>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng" @click="shake">
            <!--<router-link tag="div" class="dingdan_title" to="/user/shake">-->
            <div class="dingdan_title">
                <p class="dingdan_title_l"><img src="@/assets/images/user/trgh.png"> 摇粉丝</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </div>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng margin_p">
            <router-link tag="div" class="dingdan_title" to="/user/marketing_center">
                <p class="dingdan_title_l"><img src="@/assets/images/user/distribution.png">营销中心</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </router-link>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng margin_p  ">
            <router-link tag="div" class="dingdan_title" to="/user/employee_inquiry">
                <p class="dingdan_title_l"><img src="@/assets/images/user/people.png">员工查询</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </router-link>
        </div>
        <!--功能结束-->

        <!--功能结束-->
        <div class="gongneng ">
            <router-link tag="div" class="dingdan_title" to="/user/customer_service">
                <p class="dingdan_title_l"><img src="@/assets/images/user/custom.png">客服中心</p>
                <p class="dingdan_title_r"><img src="@/assets/images/into small.png"> </p>
            </router-link>
        </div>

        <!--功能结束-->
        <!--<yd-grids-group :rows="3" class="margin_b" >-->
            <!--<yd-grids-item v-for="n in 6">-->
                <!--<span slot="text">grids-3</span>-->
            <!--</yd-grids-item>-->
        <!--</yd-grids-group>-->
        <!--底部菜单-->
        <div style="width: 100%; height: 1rem;"></div>
        <menus></menus>
        <!--所有的路由展示-->
        <router-view></router-view>
    </div>
</template>
<script type="text/babel">
  import Vue from 'vue';
  import {GridsGroup, GridsItem} from 'vue-ydui/dist/lib.rem/grids';
  import { mapState} from 'vuex';
  /* 使用px：import {GridsItem, GridsGroup} from 'vue-ydui/dist/lib.px/grids'; */
  Vue.component(GridsGroup.name, GridsGroup);
  Vue.component(GridsItem.name, GridsItem);
  import menu from '@/components/menu';
    export default {
        data () {
            return {
                showlists: false,
            }
        },
        components: {
            'menus': menu,
        },
        computed:mapState({
            user_data(state){
               // console.log(state.menuItems)
                return state.menuItems
            },fansilist(state){
                return state.fansilist
            }
        }),

        mounted: function () {
            this.category();

        },
        methods: {
            category () {
                var token = window.storeWithExpiration.get('token');
                var url = "/users/index?token=" + token;
                var that = this;
                this.$axios.get(url).then(function(response){
                    that.$store.commit("setMenuItems",response.data.data.user_data)
                    that.$store.commit("setfansilist",response.data.data)
                })
            },
            ordermy(id){
                this.$router.push({ name: 'myorder_link', query: { id: id }})
            },
            shake(){
                var that = this;
                if(that.user_data.store_level_id>1){
                    this.$router.push({ name: 'shake_link'})
                }else{
                    that.$dialog.alert({mes: '摇粉丝暂不对普通用户开放！'})

                }
            },
            gotoshop(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/user/seller_shop/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },

        },

    }
</script>
<style>
    /*左边的个人信息*/
    .heard { width: 100%;  padding: .1rem .2rem; height: 3.1rem;background-image :url("../../assets/images/user/head.png");background-size:cover }
    .heard_img {  width: 100%;  height: 2.2rem;  background: #fff;    }
    .heard_img_l{ width: 40%; float: left; position:relative; }
    .heard_img_l p {  margin-bottom: .3rem;  color: #af773e;  width: 3rem;  height: 1.2rem;  padding-top: .5rem;}
    .heard_img_l p img {  width: 1.5rem;  height: 1.5rem;  border-radius:1.2rem ;  }
    .heard_img_l_p{ position: absolute; top:.2rem; left: 1.5rem; background: #fff;color:#af773e;  border: 1px solid #af773e;font-size: .2rem;  border-radius: .8rem;height:.8rem; width:.8rem; padding-top:.05rem;}
    .fans_many{ width: 100%;  height: .8rem;  background: #fff;  box-shadow: 1px 2px 2px #999;}
    .fans_many ul li{ float: left;line-height: .8rem; width: 33.3333%; vertical-align: middle;  border-right:1px solid #af773e; height: .6rem; }
    .fans_many ul li img{ vertical-align: middle; margin-right: .1rem;}
   /*中间的个人信息*/
    .heard_img_c{ float: left; width: 50%;padding-top: .6rem; vertical-align: middle; text-align: left;}
    .heard_img_c p{ color:#af773e; font-size: .4rem; }
    .heard_img_c ul li{text-align: left; height: .4rem;   margin-bottom:.1rem;  font-size: .25rem;}
    .heard_img_c ul li img{ width: .3rem; display: block;  float: left; }
    .heard_img_c ul li span{ margin-left: .2rem;}
    .todo_real{ margin-top:.2rem; }
    .todo_real img{ height: .5rem;}
    /*右边的设置*/
    .heard_img_r{ float: left; width: 10%; color:#af773e; padding: .1rem .1rem 0 0;}
    .heard_img_r img{ width: .6rem;}
    /*我的订单*/
    .dingdan{ margin-top: .1rem; margin-bottom: .1rem; background:#fff; width: 100%; height: 2.2rem;vertical-align: middle;}
    .dingdan_title{ height: .9rem;background:#fff; padding-top: .25rem;vertical-align: middle; border-bottom: 1px solid #eee;width: 100%;}
    .dingdan_title_l{float: left;line-height: .45rem;  margin-left: .1rem; font-size: .25rem}
    .dingdan_title_l img{ width: .4rem;  float: left; margin-right: .1rem; }
    .dingdan_title_r{float: right; margin-right: .1rem; line-height: .5rem;}
    .dingdan_title_r img{ width: .3rem;}
    .dingdan_lc{ width: 100%; background: #af773e;}
    .dingdan_lc ul li{float: left; width: 20%;height: .7rem; padding-top: .22rem;}
    .dingdan_lc ul li img{ width: .6rem; height: .6rem;}
    /*功能展示*/
    .margin_p{ margin-top: .1rem;}
    .margin_b{ margin-bottom: 1.5rem;}
</style>