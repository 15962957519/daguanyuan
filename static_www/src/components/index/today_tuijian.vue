<template>
    <div class="today_tuijian">
    <!--顶部导航-->
    <yd-navbar :fixed="true" >
        <router-link   to="" slot="left" >
            <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
        </router-link>
        <p style="font-size: .3rem" slot="center">天天特价</p>
        <!--<yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>-->
    </yd-navbar>
    <yd-infinitescroll :callback="loadList" ref="infinitescrollDemo">
        <yd-list theme="4" slot="list" >
            <yd-list-item v-for="item, key in list" :key="key" @click.native="clickhref(item.goods_id)">
                <img slot="img" :src="getCurlofimgUsenoAuth(item.original_img)">
                <yd-list-other slot="other">
                    <div class="list_first">
                       <div  class="list_first_title">
                           <p class="ta_title">{{item.goods_name}}</p>
                          <p class="ta_title_p"> <span class="colur_af">描述：</span>{{item.goods_content}}</p>
                       </div>
                    <div class="list_first_rollow"><img src="@/assets/images/inter.png"></div>
                    <div class="user_title01">
                        <ul>
                            <!--<li><img src="@/assets/images/share.png"></li>-->
                            <li><img src="@/assets/images/glance.png">{{item.click_count}}</li>
                            <li><img src="@/assets/images/like.png">{{item.care_count}}</li>
                        </ul>
                    </div>
                    </div>
                </yd-list-other>
            </yd-list-item>
        </yd-list>

        <!-- 数据全部加载完毕显示 -->
        <span slot="doneTip">暂时没有更多特价商品~~</span>

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
                pageSize: 10,
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
             clickhref(goods_id){
                // window.location.href="http://www.baidu.com"
                try {
                    this.$router.push({path: '/index/' + goods_id})
                } catch (e) {
                    console.log(e)

                }
            },
            getCurlofimgUsenoAuth(a, b, c) {
                return this.$weipai.getCurlofimgUsenoAuth(a, b, c, false);
            },
            loadList() {
                var that = this;
                var token = storeWithExpiration.get('token');
                that.$axios.get('/day_special_price', {
                    params: {
                        page: that.page,
                        token : token,
                    }
                }).then(function (response) {
                   // console.log(555,response.data.data)
                    const _list = response.data.data.special_goods_list ;
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
            }
        }
    }
</script>
<style>
    .yd-list-img{ background: #f2f2f2; position: relative;}
     .yd-list-img img {
         position: absolute;
         top: 100%;
         left: 50%;
         transform: translate(-50%, -50%);
     }
    .today_tuijian{padding-top: 1rem;}
    .colur_af{ color:#af773e }
    .list_first{ height: 1.9rem; overflow: hidden; width: 100%; text-align: left;}
    .list_first_title{ height: 1.5rem;  overflow: hidden;}
    .list_first_rollow{ float: right;  height: .5rem; margin-top: -1rem;}
    .list_first_rollow img{  height: .4rem;}
    .user_title01{ width: 100%;}
    .user_title01 ul li{ float: right;  margin-right: .15rem;}
    .ta_title{ font-size: .3rem; color: #333; height: .7rem; overflow: hidden;}
    .ta_title_p{ height: .65rem;  width: 90%; overflow: hidden; line-height: .35rem;}
    .user_title01 ul li img{ width: .4rem; vertical-align: middle;}
</style>