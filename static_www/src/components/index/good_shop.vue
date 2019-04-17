<template>
    <div class="today_tuijian">
    <!--顶部导航-->
    <yd-navbar :fixed="true" >
        <router-link   to="" slot="left" >
            <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
        </router-link>
        <p style="font-size: .3rem" slot="center">优选店铺</p>
        <!--<yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>-->
    </yd-navbar>
    <yd-infinitescroll :callback="loadList" ref="infinitescrollDemo">
        <yd-list theme="4" slot="list" >
            <yd-list-item v-for="item, key in list" :key="key" @click.native="gotoshop(item.user_id)">
                <img slot="img" :src="item.head_pic">
                <yd-list-other slot="other">
                    <div class="list_first">
                       <div  class="list_first_title">
                           <p class="ta_title">{{item.nickname}}的店铺</p>

                          <p class="ta_title_p"> 共计<span class="colur_af">{{item.arr_count}}</span>件商品</p>
                       </div>
                    <div  class="list_first_rollow border_cor" @click="gotoshop(item.user_id)">
                        进店逛逛
                    </div>

                    </div>
                </yd-list-other>
            </yd-list-item>
        </yd-list>

        <!-- 数据全部加载完毕显示 -->
        <span slot="doneTip">暂时没有更多推荐商品~~</span>

        <!-- 加载中提示，不指定，将显示默认加载中图标 -->
        <img slot="loadingTip" src="http://static.ydcss.com/uploads/ydui/loading/loading10.svg"/>

      </yd-infinitescroll>
    </div>
</template>
<script type="text/babel">
    import Vue from 'vue';
    import {InfiniteScroll} from 'vue-ydui/dist/lib.rem/infinitescroll';
    /* 使用px：import {InfiniteScroll} from 'vue-ydui/dist/lib.px/infinitescroll'; */

    Vue.component(InfiniteScroll.name, InfiniteScroll);
    export default {
        data() {
            return {
                page: 1,
                pageSize: 3,
                list: [

                ]
            }
        },
        mounted:function(){
            this.loadList()
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            getCurlofimgUsenoAuth(a, b, c) {
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            gotoshop(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/user/seller_shop/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },


            loadList() {
                var that = this;
                var token = storeWithExpiration.get('token');
                that.$axios.get('/goodstorelist', {
                    params: {
                        page: that.page,
                        token : token,
                    }
                }).then(function (response) {
                    const _list = response.data.data.good_store ;
                    that.list = [...that.list, ..._list];
                    if (_list.length < that.pageSize) {
                        /* 所有数据加载完毕 */
                        that.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone');
                        return;
                    }
                    /* 单次请求数据完毕 */
                    that.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad');
                    that.page++;
                });
            }
        }
    }
</script>
<style>
    .border_cor{border: 1px solid #af773e;color:#af773e; padding: .1rem .3rem; }
    .today_tuijian{padding-top: 1rem;}
    .colur_af{ color:#af773e }
    .list_first{ height: 1.9rem; overflow: hidden; width: 100%; text-align: left;}
    .list_first_title{ height: 1.5rem;  overflow: hidden;}
    .list_first_rollow{ float: right;  height: .5rem; margin-top: -.9rem;}
    .list_first_rollow img{  height: .6rem;}
    .user_title01{ width: 100% }
    .user_title01 ul li{ float: right; }
    .ta_title{ font-size: .3rem; color: #333; height: .7rem; overflow: hidden;}
    .ta_title_p{ height: .8rem; }
    .user_title01 ul li img{ width: .5rem; vertical-align: middle;}
</style>