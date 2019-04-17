<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">粉丝</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--个人信息设置-->
        <yd-infinitescroll :callback="loadList" ref="infinitescrollDemo">

        <yd-list theme="1" slot="list">
            <yd-cell-group>
                <template v-for="(item , index) in fans_list">
                    <yd-cell-item style="height: 1.5rem;" >
                        <span slot="left"> <img style="width:1rem; margin-right: .1rem;" :src="item.head_pic"/></span>
                        <span slot="left">{{ item.nickname }}</span>
                        <span slot="right" class="border_c_fan" v-if="item.is_care == 0"  v-on:click="follow(item.fans_id,index)">关注</span>
                        <span slot="right" class="border_c_fan" v-if="item.is_care == 1" v-on:click="unfollow(item.fans_id,index)">取消关注</span>
                    </yd-cell-item>
                </template>
            </yd-cell-group>
        </yd-list>

        <!-- 数据全部加载完毕显示 -->
        <span slot="doneTip">暂没有更多粉丝关注</span>

        <!-- 加载中提示，不指定，将显示默认加载中图标 -->


    </yd-infinitescroll>
        <!--收获地址-->

    </div>
</template>
<script>
    export default {
        data() {
            return {
                page:1,
                pageSize: 3,
                fans_list : [],
                is_follow : false,
            }
        },
        mounted: function () {
            this.showData();
        },
        computed: {

        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            loadList() {
                var that = this;
                that.$axios.get('/users/fans_list', {
                    params: {
                        token: storeWithExpiration.get('token'),
                        page:that.page
                    }
                }).then(function (response) {
                    console.log(response.data.data)
                    const _list = response.data.data;

                    that.fans_list = [...that.fans_list, ..._list];

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
            showData : function () {
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/users/fans_list?token=" + token;
                _that.$axios.get(url).then(function (response) {

                    if (response.status == 200){
                        //粉丝列表
                        for(var i=0;i<response.data.data.length;i++){
                            _that.fans_list.push(response.data.data[i]);
                        }
                        //_that.start = response.data['fans_count'];

                    }

                }).catch(function (error) {
                    console.log(error);
                });
            },

            follow(fid,index){
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/users/create_care?token=" + token + '&fans_id=' + fid;
                _that.$dialog.confirm({
                    title: '粉丝关注',
                    mes: '确定要关注此粉丝？',
                    opts: () => {
                        //this.$dialog.toast({mes: '你点了确定', timeout: 1000});
                        _that.$axios.get(url).then(function (response) {
                            console.log(response);
                            if (response.status == 200){
                                if (response.data.code == 2000){
                                    _that.$dialog.toast({
                                        mes: '关注成功',
                                        timeout: 1000,
                                        icon: 'success'
                                    });
                                    _that.fans_list[index].is_care = response['data']['state'];

                                }
                            }

                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                });


            },
            unfollow(fid,index){
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/users/cancel_care?token=" + token + '&fans_id=' + fid;
                _that.$dialog.confirm({
                    title: '取消关注',
                    mes: '确定要取消关注此粉丝？',
                    opts: () => {
                        //this.$dialog.toast({mes: '你点了确定', timeout: 1000});
                        _that.$axios.get(url).then(function (response) {
                            console.log(response);
                            if (response.status == 200){
                                if (response.data.code == 2000){
                                    _that.$dialog.toast({
                                        mes: '取消关注成功',
                                        timeout: 1000,
                                        icon: 'success'
                                    });
                                    _that.fans_list[index].is_care = response['data']['state'];

                                }
                            }

                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                });
            }

        }
    }

</script>
<style>
    .margin{ padding-top: 1.05rem;}
    .border_c_fan{border: 1px solid #af773e; color:#af773e; padding: .1rem .3rem; }
</style>