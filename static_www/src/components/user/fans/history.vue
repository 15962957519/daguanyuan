<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">足迹</p>
            <yd-button  slot="right" type="hollow"  @click.native="deleteItemall">清空</yd-button>
            <!--<yd-navbar-back     @click.native="deleteItemall"></yd-navbar-back>-->
            <!--<img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>-->
        </yd-navbar>

        <!--我的历史-->
        <div class="container" style="width: 100%; overflow: hidden;">
            <div class="page-title">左滑可清除单个足迹</div>
            <ul>
                <yd-infinitescroll :callback="loadList" ref="infinitescrollDemo">

                    <yd-list theme="1" slot="list">
                        <yd-cell-group>
                            <li class="list-item " v-for="(item,index) in list " data-type="0">
                                <div class="list-box" @touchstart.capture="touchStart" @touchend.capture="touchEnd" @click="skip(item.goods_id)">
                                    <img class="list-img" :src="item.original_img" alt="">
                                    <div class="list-content">
                                        <p class="title">{{item.goods_id}}{{item.goods_name}}</p>
                                        <p class="tips"><em>¥</em>{{item.start_price}} </p>
                                        <p class="time">{{date(item.visittime)}}</p>
                                    </div>
                                </div>
                                <div class="delete" @click="deleteItem"  :data-index="index" >删除</div>
                            </li>
                        </yd-cell-group>
                    </yd-list>

                    <!-- 数据全部加载完毕显示 -->
                    <span slot="doneTip">暂没有更多足迹</span>

                    <!-- 加载中提示，不指定，将显示默认加载中图标 -->


                </yd-infinitescroll>

            </ul>
        </div>

    </div>
</template>
<script type="text/babel">
    import Vue from 'vue';
    import {PullRefresh} from 'vue-ydui/dist/lib.rem/pullrefresh';
    /* 使用px：import {PullRefresh} from 'vue-ydui/dist/lib.px/pullrefresh'; */
    Vue.component(PullRefresh.name, PullRefresh);
    export default {
        data() {
            return {
                page:1,
                pageSize: 3,
                list : [],
                startX : 0 ,
                endX : 0 ,
            }
        },
        mounted: function () {this.showData();},
        methods: {
            loadList() {
                var that = this;
                that.$axios.get('/users/footprint', {
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
            goback () {
                this.$router.go(-1)
            },
            showData(){
                            var _that =this;
                            var token = window.storeWithExpiration.get('token') || '';
                            var url = "/users/footprint?token=" + token;
                            _that.$axios.get(url).then(function (res) {
                                if (res.status == 200){
                                    return res.data.data;
                                }

                            }).then(function (res) {
                                for(var i=0;i<res.length;i++){
                                    _that.list.push(res[i]);
                                }

                            }).catch(function (error) {
                                console.log(error)
                            })
                        },
            delet_good(uid){
                 var goods_id=uid
                alert(goods_id)
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/users/cancel_care?token=" + token + "&fans_id=" + uid;

                _that.$dialog.confirm({
                    title: '删除足迹',
                    mes: '确定要删除此足迹？',
                    opts: () => {
                        _that.$axios.get(url).then(function (res) {
                            if (res.status == 200){
                                return res.data
                            }

                        }).then(function (res) {
                            console.log(res)
                            if (res.code == 2000){
                                _that.$dialog.toast({
                                    mes: '取消关注成功',
                                    timeout: 1000,
                                    icon: 'success'
                                });
                                for(var i=0;i<_that.follow.length;i++){
                                    if(uid ==_that.follow[i].fans_id ){
                                        _that.follow.splice(i,1);
                                    }
                                }

                            }


                        }).catch(function (error) {
                            console.log(error)
                        })
                    }
                });



            },
            //跳转
            skip(e){
                var goods_id=e
                if( this.checkSlide() ){
                    this.restSlide();
                }else{
                    this.$router.push({path: '/productlists/' + goods_id})
                }
            },
            //滑动开始
            touchStart(e){
                // 记录初始位置
                this.startX = e.touches[0].clientX;
            },
            //滑动结束
            touchEnd(e){
                // 当前滑动的父级元素
                let parentElement = e.currentTarget.parentElement;
                // 记录结束位置
                this.endX = e.changedTouches[0].clientX;
                // 左滑
                if( parentElement.dataset.type == 0 && this.startX - this.endX > 30 ){
                    this.restSlide();
                    parentElement.dataset.type = 1;
                }
                // 右滑
                if( parentElement.dataset.type == 1 && this.startX - this.endX < -30 ){
                    this.restSlide();
                    parentElement.dataset.type = 0;
                }
                this.startX = 0;
                this.endX = 0;
            },
            //判断当前是否有滑块处于滑动状态
            checkSlide(){
                let listItems = document.querySelectorAll('.list-item');
                for( let i = 0 ; i < listItems.length ; i++){
                    if( listItems[i].dataset.type == 1 ) {
                        return true;
                    }
                }
                return false;
            },
            //复位滑动状态
            restSlide(){
                let listItems = document.querySelectorAll('.list-item');
                // 复位
                for( let i = 0 ; i < listItems.length ; i++){
                    listItems[i].dataset.type = 0;
                }
            },
            //删除
            deleteItem(e){
             var that = this;
                // 当前索引
                let index = e.currentTarget.dataset.index;
                // 复位
                that.restSlide();
                // 删除
                var goods_id=that.list[index].goods_id
                that.list.splice(index,1);
                var token = window.storeWithExpiration.get('token');
                that.$axios.get('/users/delfootprint', {
                        params: {
                            token: token,
                            goods_id:goods_id
                        }
                    }) .then(function (response) {
                        if (response.status == '200') {
                            return response.data;
                        }
                    }).then(function (json) {
                    that.$dialog.alert({mes: '删除成功'});
                    }).catch(function (ex) {
                        console.log(ex);
                    });

            },
            //全部删除
            deleteItemall() {
                var that=this
                if(that.list.length>0 ){
                that.$dialog.confirm({
                    title: '确定要清空所有足迹吗？',
                    mes: '确认!',
                    opts: function() {

                        var token = window.storeWithExpiration.get('token');
                        var url = "/users/clearfootprint";
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
                    that.$dialog.alert({mes: '足迹空空如也，暂不需要清理'});
                }
            },
            date(time){
                let oldDate = new Date(time*1000)
                let newDate = new Date()
                var dayNum = "";
                var getTime = (newDate.getTime() - oldDate.getTime())/1000;
                if(getTime < 60*5){
                    dayNum = "刚刚";
                }else if(getTime >= 60*5 && getTime < 60*60){
                    dayNum = parseInt(getTime / 60) + "分钟前";
                }else if(getTime >= 3600 && getTime < 3600*24){
                    dayNum = parseInt(getTime / 3600) + "小时前";
                }else if(getTime >= 3600 * 24 && getTime < 3600 * 24 * 30){
                    dayNum = parseInt(getTime / 3600 / 24 ) + "天前";
                }else if(getTime >= 3600 * 24 * 30 && getTime < 3600 * 24 * 30 * 12){
                    dayNum = parseInt(getTime / 3600 / 24 / 30 ) + "个月前";
                }else if(time >= 3600 * 24 * 30 * 12){
                    dayNum = parseInt(getTime / 3600 / 24 / 30 / 12 ) + "年前";
                }
                let year   = oldDate.getFullYear();
                let month  = oldDate.getMonth()+1;
                let day    = oldDate.getDate();
                let hour   = oldDate.getHours();
                let minute = oldDate.getMinutes();
                let second = oldDate.getSeconds();
                return dayNum
            }
        },

    }

</script>
<style>
    .margin{ padding-top: 1.05rem;}
    .border_c{border: 1px solid #af773e; color:#af773e; padding: .1rem .3rem; }
    .yd-list-item01{ height: 2.2rem; width: 100%; background: #fff; padding-top: .15rem; margin-bottom: .1rem;  position: relative;}
    .yd-list-img01{ width: 25%; float: left; padding: .05rem;}
    .yd-list-img01 img{ width: 100%; }
    .yd-list-mes01{ width: 75%; float: left; padding: 0.05rem}
    .delete_list{ border: 1px solid #af773e; color:#af773e; padding: .1rem .3rem; margin-right:.2rem; right: 0; position: absolute; bottom: .2rem; }
    /*滑动组件*/
    .page-title{
        text-align: center;
        font-size: 12px;
        padding: 5px 15px;
        position: relative;
    }
    .page-title:after{
        content: " ";
        position: absolute;
        left: 0;
        bottom: 0;
        right: 0;
        height: 1px;
        border-bottom: 1px solid #ccc;
        color: #ccc;
        -webkit-transform-origin: 0 100%;
        transform-origin: 0 100%;
        -webkit-transform: scaleY(0.5);
        transform: scaleY(0.5);
        z-index: 2;
    }
    .list-item{
        position: relative;
        height: 1.6rem;
        -webkit-transition: all 0.2s;
        transition: all 0.2s;
    }
    .list-item[data-type="0"]{
        transform: translate3d(0,0,0);
    }
    .list-item[data-type="1"]{
        transform: translate3d(-2rem,0,0);
    }
    .list-item:after{
        content: " ";
        position: absolute;
        left: 0.2rem;
        bottom: 0;
        right: 0;
        height: 1px;
        border-bottom: 1px solid #ccc;
        color: #ccc;
        -webkit-transform-origin: 0 100%;
        transform-origin: 0 100%;
        -webkit-transform: scaleY(0.5);
        transform: scaleY(0.5);
        z-index: 2;
    }
    .list-box{
        padding: 0.2rem;
        background: #fff;
        display: flex;
        align-items: center;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        justify-content: flex-end;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        font-size: 0;
    }
    .list-item .list-img{
        display: block;
        width: 1rem;
        height: 1rem;
    }
    .list-item .list-content{
        padding: 0.1rem 0 0.1rem 0.2rem;
        position: relative;
        flex: 1;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        overflow: hidden;
    }
    .list-item .title{
        text-align: left;
        display: block;
        color: #333;
        overflow: hidden;
        font-size: 15px;
        font-weight: bold;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .list-item .tips{
        text-align: left;
        display: block;
        overflow: hidden;
        font-size: 12px;
        color: #999;
        line-height: 25px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .list-item .time{
        display: block;
        font-size: 12px;
        position: absolute;
        right: 0;
        top: 0.1rem;
        color: #666;
    }
    .list-item .delete{
        width: 2rem;
        height: 1.6rem;
        background: #af773e;
        font-size: 17px;
        color: #fff;
        text-align: center;
        line-height: 1.6rem;
        position: absolute;
        top:0;
        right: -2rem;
    }
</style>