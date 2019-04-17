<template>
    <div style="padding-top: 1rem;">
        <!--顶部导航-->
        <yd-navbar bgcolor="#333" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;color: #fff;" slot="center">{{this.$route.query.name}}的店铺商品</p>
        </yd-navbar>
        <!--切换-->

                <template>
                    <yd-infinitescroll :callback="loadList" ref="infinitescrollDemo">
                        <yd-list theme="1" slot="list">
                            <yd-list-item v-for="item, key in list" :key="key"   @click.native="clickhref(item.goods_id)">
                                <img slot="img" :src="item.original_img" @click.native="clickhref(item.goods_id)">
                                <span slot="title">{{item.goods_name}}</span>
                                <yd-list-other slot="other">
                                    <div>
                                        <span class="list-price"><em>¥</em>{{item.cur_price}}</span>
                                    </div>
                                </yd-list-other>
                            </yd-list-item>
                        </yd-list>

                        <!-- 数据全部加载完毕显示 -->
                        <span slot="doneTip">期待更多商品~~</span>
                        <!-- 加载中提示，不指定，将显示默认加载中图标 -->
                        <img slot="loadingTip">
                    </yd-infinitescroll>
                </template>

    </div>
</template>

<script>


    export default {
        data() {
            return {
                goodsinfo:{},
                page: 1,
                pageSize: 1,
                list: [

                ]
            }
        },
        mounted:function(){
            this.loadList()
        },
        methods: {
            clickhref(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/index/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },
            loadList() {
                var that = this;
                var token = storeWithExpiration.get('token');
                var user_id = this.$route.query.user_id || 0;
                var type = this.$route.query.type || 1
                that.$axios.get('storeIndex', {
                    params: {
                        page: that.page,
                        user_id: user_id,
                        token : token,
                        type:type
                    }
                }).then(function (response) {
                    const _list = response.data.data.mygoods;
                    that.list = [...that.list, ..._list];
                    if (_list.length < that.pageSize || that.page == 1) {
                        /* 所有数据加载完毕 */
                        that.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone');
                        return;
                    }

                    /* 单次请求数据完毕 */
                    that.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad');

                    that.page++;
                });
            },

            goback () {
                       this.$router.go(-1)
                   },
        },


    }
</script>

<style scoped>
   </style>