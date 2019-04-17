<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;" slot="center">商品管理</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
         <!--分类-->
        <yd-tab active-color="#af773e" :item-click="goods_dows" :prevent-default="false" v-model="tabgoods">
            <yd-tab-panel  v-for="item in items" :label="item.label" :key="item.key">
                <!--渲染商品-->

                <yd-radio-group color="#af773e" v-for="item in mygoods" :key="item.goods_id">

                    <div class="want_img" v-on:click="goodsdetais(item.goods_id)">
                        <!--头像-->
                        <div class="want_img_img" >
                            <img :src="item.original_img">
                        </div>
                        <!--个人信息-->
                        <div class="want_img_title">
                            <p><strong>{{ item.goods_name }}</strong></p>
                            <p class="group_hair_title_e">
                                {{ item.goods_content }}
                            </p>
                        </div>
                    </div>
                    <!--出价-->
                    <div class="want_list">
                      <ul>
                          <li class="li_one">
                              时间：<template>
                                  <yd-countdown :time="item.endTime"></yd-countdown>
                              </template>
                          </li>
                          <li class="li_two" v-on:click="goodsedit(item.goods_id)">编辑</li>
                          <li class="li_three" v-on:click="goodsdown(item.goods_id)" v-show="isShow">下架</li>
                      </ul>
                  </div>

                </yd-radio-group>



            </yd-tab-panel>


        </yd-tab>


    </div>
</template>

<script>
    import Vue from 'vue';
    import {CountDown} from 'vue-ydui/dist/lib.rem/countdown';
    import {Tab, TabPanel} from 'vue-ydui/dist/lib.rem/tab';
    Vue.component(Tab.name, Tab);
    Vue.component(TabPanel.name, TabPanel);
    Vue.component(CountDown.name, CountDown);
    export default {
        data() {
            return {
                mygoods : [],
                downgoods :[],
                tabgoods :0,

                items: [
                    {label: '在线'},
                    {label: '下线'},

                ],
                isShow:false,

            }
        },
        mounted:function(){
            this.showdata();
        },
        methods:{
            goback () {
                this.$router.go(-1)
            },
            showdata(){

                var token = window.storeWithExpiration.get('token');
                var url = "/my_publish_goods?token=" + token + '&time=1';
                var _that = this;
                _that.mygoods = [];
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        for (var i = 0;i < res.data.data.my_goods.length;i++){
                            res.data.data.my_goods[i]['endTime'] = _that.timestampToTime(res.data.data.my_goods[i]['endTime']);
                            _that.mygoods.push(res.data.data.my_goods[i]);

                        }
                        _that.isShow = true;
                        console.log(_that.mygoods)
                    }
                    
                }).catch(function (error) {
                    console.log(error)
                })
            },
            goodsdown(id){
                var _that = this;
                _that.$dialog.confirm({
                    title: '商品下架',
                    mes: '确定要下架此商品?',
                    opts: () => {
                        var token = window.storeWithExpiration.get('token');
                        var url = '/down_paipin?goods_id=' + id + '&token=' + token;
                        _that.$axios.get(url).then(function (res) {
                            console.log(res)
                            if (res.status == 200){
                                if (res.data.code == 2000){
                                    _that.$dialog.toast({
                                        mes: '下架成功',
                                        timeout: 1500,
                                        icon: 'success'
                                    });
                                    for(var i=0;i<_that.mygoods.length;i++){
                                        if(id ==_that.mygoods[i].goods_id ){
                                            _that.mygoods.splice(i,1);
                                        }
                                    }
                                }else{
                                    _that.$dialog.toast({
                                        mes: res.data.message,
                                        timeout: 1500
                                    });
                                }
                            }

                        }).catch(function (error) {
                            console.log(error)
                        })
                    }
                });


            },
            //下架区
            goods_dows(key){
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                console.log(key)

                if (key == 0){
                    _that.$dialog.loading.open('数据加载中');
                    setTimeout(() => {
                        _that.tabgoods = key;
                        _that.$dialog.loading.close();
                        _that.showdata()
                    }, 1000);

                }else if (key == 1) {
                    _that.mygoods = [];
                    var url = "/my_publish_goods?token=" + token + '&time=2';
                    _that.$dialog.loading.open('数据加载中');
                    setTimeout(() => {
                        _that.tabgoods = key;
                        _that.$dialog.loading.close();

                        _that.$axios.get(url).then(function (res) {
                            if (res.status == 200){
                                for (var i = 0;i < res.data.data.my_goods.length;i++){
                                    res.data.data.my_goods[i]['endTime'] = _that.timestampToTime(res.data.data.my_goods[i]['endTime']);
                                    _that.mygoods.push(res.data.data.my_goods[i]);
                                }
                                _that.isShow = false;
                                console.log(_that.mygoods)
                            }

                        }).catch(function (error) {
                            console.log(error)
                        })

                    }, 1000);

                }



            },
            //编辑
            goodsedit(id){
                this.$router.push({ name: 'editgoods_link', query: { goods_id: id },meta: {title: '商品编辑'}})
            },
            //商品详情
            goodsdetais(id){
                this.$router.push({path: '/index/' + id})
            },
            timestampToTime(timestamp) {
                var  date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                var  Y = date.getFullYear() + '/';
                var  M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '/';
                var  D = date.getDate() + ' ';
                var  h = date.getHours() + ':';
                var  m = date.getMinutes() + ':';
                var  s = date.getSeconds();
                return Y+M+D+h+m+s;
            },

        }
    }
</script>

<style scoped>
    .margin{padding-top: 1rem;}
    /*要群发的商品*/
    .want_img{ width: 100%; height: 2rem; background: #fff; margin-top:.1rem;}
    .want_img_img{ width: 2rem; height: 2rem; margin-left: .1rem;float: left; width: 25%}
    .want_img_img img{ height: 1.8rem; width: 1.8rem; margin: .1rem; border-radius: .1rem;}
    .want_img_title{ float: left; text-align: left; margin-top: .1rem; padding-left: .1rem;  width: 70%; margin-left: .1rem; height: 1.5rem; overflow: hidden;}
    .want_img_title p{ margin-top: .1rem;}
    /*状态*/
    .want_list{ height: 1rem; border-top:1px solid #eee; border-bottom:5px solid #eee; }
    .li_one{ border-right:1px solid #af773e;  width: 55%;  }
    .li_two{ border-right:1px solid #af773e;  width: 20%;  }
    .li_three{width: 20%; }
    .want_list ul li{ float: left;   margin-left: .1rem;  height: .5rem; margin-top: .25rem; line-height: .6rem;}
</style>