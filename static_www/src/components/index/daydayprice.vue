<template>
    <div class="daydayprice">
    <!--顶部导航-->
    <yd-navbar :fixed="true" >
        <router-link   to="" slot="left" >
            <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
        </router-link>
        <p style="font-size: .3rem" slot="center">今日推荐</p>
        <!--<yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>-->
    </yd-navbar>
         <img  src="@/assets/images/today_tujian.png"  style=" width: 100%;" >

        <yd-infinitescroll :callback="loadList"  ref="infinitescrollDemo">
            <yd-list style="background:#ed5e50 " theme="4" slot="list">
                <!--外国商品-->
                <div class="ab_place"  v-for="(item,key)  in list"  @click="clickhref(item.goods_id)" >
                    <div class="ab_place_img">
                        <img  :src="item.original_img">
                        <div class="ab_place_img_p">
                            <span>截止:</span>
                            <yd-countdown :time="item.endTime-date" timetype="second"></yd-countdown>
                            <!--<span>18</span>:<span>18</span>:<span>18</span>:<span>18</span>-->
                        </div>
                    </div>
                    <div class="ab_place_title">
                        <ul>
                            <li class="ab_place_title1">{{item.goods_name}}</li>
                            <li class="ab_place_title2">描述:<span>{{item.goods_content}}</span></li>
                            <p class="ab_place_title3">
                                <span>价格:¥{{item.start_price}}</span>
                                <button >查看详情</button>
                            </p>
                        </ul>
                    </div>
                </div>
                <!--外国商品结束-->
            </yd-list>

            <!-- 数据全部加载完毕显示 -->
            <span slot="doneTip" style="color: #fff;">暂时没有更多推荐商品~~</span>

            <!-- 加载中提示，不指定，将显示默认加载中图标 -->
            <img slot="loadingTip" src="http://static.ydcss.com/uploads/ydui/loading/loading10.svg"/>

        </yd-infinitescroll>



    </div>
</template>

<script>
    import Vue from 'vue';
    let nsr_loading = require('@/components/loading').default;
    let listView = require('@/components/listview.vue').default;
    import {InfiniteScroll} from 'vue-ydui/dist/lib.rem/infinitescroll';
    /* 使用px：import {InfiniteScroll} from 'vue-ydui/dist/lib.px/infinitescroll'; */

    Vue.component(InfiniteScroll.name, InfiniteScroll);
    export default {
        name: "daydayprice",
        data() {
            return {
                page: 1,
                pageSize: 5,
                date:new Date()/1000,
                list: [

                ]
            }
        },
        mounted:function(){
            this.loadList()
        },
        methods:{
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
            loadList() {
                var that = this;
                var token = storeWithExpiration.get('token');
                that.$axios.get('/is_recommend', {
                    params: {
                        page: that.page,
                        token : token,
                    }
                }).then(function (response) {
                    // console.log(555,response.data.data)
                    const _list = response.data.data ;
                    that.list = [...that.list, ..._list];
                    if (_list.length < that.pageSize || that.page == 100) {
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

<style scoped>
    .daydayprice{ padding-top: 1rem; background:#ed5e50 }
    /*外国的商品*/
    .ab_place{ width: 100%; clear: both; height: 3.35rem; background: #fff; text-align: left;    margin-bottom: .15rem; padding: .2rem .3rem;}
    .ab_place_img{ width: 45%; height: 3rem;  float: left; position: relative; border: 0;    margin-top: -.1rem;}
    .ab_place_img_p{ padding-left: .1rem; line-height: .6rem; position: absolute; bottom: 0;color: #fff; width: 100%; height: .6rem;font-size: .1rem; background: rgba(0,0,0,.6)}
    .ab_place_img_p span{ background: #fff; color: #333;font-size: .26rem;}
    .ab_place_img img{ width: 100%; height: 3rem;}
    .ab_place_title{max-width: 55%; height: 3rem; float: left;}
    .ab_place_title ul{ margin-top: .17rem; margin-left: .2rem;}
    .ab_place_title ul li{ line-height: .6rem; height: .7rem; overflow: hidden;  }
    .ab_place_title1{ color: #333;  font-size: .4rem;}
    .ab_place_title2{ color: #999; font-size: .18rem;}
    .ab_place_title2 span{ font-size: .25rem; color: #ed5e50;}
    .ab_place_title4{color: #999; font-size: .18rem}
    .ab_place_title4 span{ font-size: .35rem; color: #ed5e50;}
    .ab_place_title3{ color: #fff; height: .64rem;  text-align:left;border: 1px solid #ed5e50;  width: 3.3rem;}
    .ab_place_title3 button{ border:0; background: #ed5e50; color: #fff; height: .6rem; padding: 0 .1rem; float: right;}
    .ab_place_title3 span{  box-sizing: border-box; padding: 0 .1rem; background: #fff; color:#ed5e50; display:inline-block; line-height: .6rem;  }
    /*出价列表*/
</style>