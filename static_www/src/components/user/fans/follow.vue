<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">收藏</p>
            <yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>
        </yd-navbar>
        <!--个人信息设置-->
        <template>
            <yd-infinitescroll :callback="loadList" ref="infinitescrollDemo">

                <yd-list theme="1" slot="list">
                    <yd-list-item v-for="item, key in list" :key="key">
                        <img slot="img" :src="item.original_img">
                        <span slot="title">{{item.goods_name}}</span>
                        <yd-list-other slot="other">
                            <div>
                                <span class="list-del-price">¥{{item.shop_price}}</span>
                            </div>
                            <yd-button  @click.native="unfollow(item.goods_id)"  class="border_c"  type="hollow">取消收藏</yd-button>
                        </yd-list-other>
                    </yd-list-item>
                </yd-list>

                <!-- 数据全部加载完毕显示 -->
                <span slot="doneTip">暂时没有很多收藏...</span>

                <!-- 加载中提示，不指定，将显示默认加载中图标 -->
                <img slot="loadingTip" src="http://static.ydcss.com/uploads/ydui/loading/loading10.svg"/>

            </yd-infinitescroll>
        </template>
        <!--收获地址-->

    </div>
</template>
<script>
    import Vue from 'vue';
    import {InfiniteScroll} from 'vue-ydui/dist/lib.rem/infinitescroll';
    /* 使用px：import {InfiniteScroll} from 'vue-ydui/dist/lib.px/infinitescroll'; */
    Vue.component(InfiniteScroll.name, InfiniteScroll);
    export default {
        data() {
            return {
                page: 1,
                pageSize: 6,
                list: [
                 ]
            }
        },
        created:function(){
            this.loadList();
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },

            loadList() {
                var that = this;
                that.$axios.get('/collect_list', {
                    params: {
                        token: storeWithExpiration.get('token'),
                        page:that.page
                    }
                }).then(function (response) {
                    const _list = response.data.data;

                    that.list = [...that.list, ..._list];

                    if (_list.length < that.pageSize || that.page == 30) {
                        /* 所有数据加载完毕 */
                        that.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone');
                        return;
                    }

                    /* 单次请求数据完毕 */
                    that.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad');

                    that.page++;
                });

            },
            // 取消收藏
            unfollow(e){
                var goods_id =e
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/del_collectlist?token=" + token + "&goods_id=" + goods_id;
                _that.$dialog.confirm({
                    title: '取消收藏',
                    mes: '确定要取消收藏吗？',
                    opts: () => {
                        _that.$axios.get(url).then(function (res) {
                            if (res.status == 200){
                                return res.data
                            }

                        }).then(function (res) {
                            if (res.code == 2000){
                                _that.$dialog.toast({
                                    mes: '取消收藏成功',
                                    timeout: 1000,
                                    icon: 'success'
                                });
                                for(var i=0;i<_that.list.length;i++){
                                    if( goods_id==_that.list[i].goods_id ){
                                        _that.list.splice(i,1);
                                    }
                                }

                            }


                        }).catch(function (error) {
                            console.log(error)
                        })
                    }
                });



            },
            //取消全部
            //全部删除
            deleteItemall() {
                var that=this
                if(that.list.length>0 ){
                    that.$dialog.confirm({
                        title: '确定要清空所有收藏吗？',
                        mes: '确认!',
                        opts: function() {

                            var token = window.storeWithExpiration.get('token');
                            var url = "/users/clearcollectlist";
                            that.$axios.get(url, {
                                params: {
                                    token: storeWithExpiration.get('token'),
                                }
                            }) .then(function (response) {
                                if (response.status == '200') {
                                    return response.data;
                                }
                            }).then(function (json) {
                                that.list.splice(0,that.list.length);

                            }).catch(function (ex) {
                                console.log(ex);
                            });
                        }
                    });
                } else {
                    that.$dialog.alert({mes: '收藏空空如也，暂不需要清理'});
                }
            }

        }
    }

</script>
<style>
    .margin{ padding-top: 1.05rem;}
    .border_c{border: 1px solid #af773e; color:#af773e; padding: .1rem; height: .3rem; font-size: .2rem }

</style>