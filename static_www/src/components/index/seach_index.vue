<template>
    <div class="search_index">
    <!--顶部导航-->
    <yd-navbar :fixed="true" >
        <router-link   to="" slot="left" >
            <yd-navbar-back-icon    @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
        </router-link>
        <p style="font-size: .3rem;" slot="center">搜索</p>
        <!--<img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>-->
    </yd-navbar>
    <!--空隙-->
    <div class="top-box">
        <!--<div class="back-box"  @click="$router.back()">-->
        <!--<span class="iconfont icon-back-m"></span>-->
        <!--</div>-->
        <div class="search-box" :class="isFocus?'':'active'">
            <input class="search-ipt" style="text-indent:1.2em;" v-model="search" @focus="initPage" type="text" placeholder="请输入您要查找的内容"/>
            <!--搜索-->
            <div class="slidemenu-btn" @click="iptShearch">
                <span class="text" >搜索</span>
            </div>
        </div>
        <!--<div class="slidemenu-btn" v-if="isFocus">-->
        <!--<span class="iconfont icon-query1" @click="toggleDrawer">按钮=</span>-->
        <!--</div>-->
        <div class="history-panel"  v-if="historyxs">
            <h4 v-if="historyxs">最近搜索</h4>
            <ul class="his_ulcon" v-if="historyxs">
                <li v-for="(item,index) in searches_list" :key="index" @click="historysearch(item)">{{item}}</li>
            </ul>
            <p v-if="historyxs" @click="clearhis()">清空搜索记录</p>
        </div>
        <!--热门搜索-->
        <div class="history-panel" >
            <h4 >热门搜索</h4>
            <ul class="his_ulcon" >
                <li :class="index==n1||index==n2||index==n3?'':'border_co'" v-for="(item,index) in search_keyword" :key="index"  @click="historysearch(item)">{{item}}</li>
            </ul>
        </div>
        <!--分类-->
        <div class="history-panel" >
            <h4 >分类</h4>
            <ul class="his_ulcon01" >
                <li v-for="(item,index) in data" :key="index"  @click="todo_list(item.id)">{{item.mobile_name}}</li>
            </ul>
        </div>
    </div>

</div>
</template>

<script>
    import {saveSearch} from '../../assets/js/cache.js'  //引用本地存储js
    import storage from 'good-storage'  //引入good-storage包
    export default {
        data(){
            return{
                search:'',
                isFocus:true,
                searches_list:[], //历史搜索记录列表
                historyxs:true,
                n1:parseInt(8*Math.random()),
                n2:parseInt(8*Math.random()),
                n3:parseInt(8*Math.random()),
                search_keyword:{},
                data:{}
            }
        },
        mounted:function(){
            this.initPage()
            this.showdata()
        },
        methods:{
            showdata:function(){
                var that = this;
                that.$axios.get('/goods_category', {
                    params: {
                        token: storeWithExpiration.get('token'),
                    }
                }) .then(function (response) {
                    if (response.status == '200') {
                        return response.data;
                    }
                }).then(function (json) {
                    that.search_keyword=json.search_keyword
                    that.data=json.data
                   // console.log(22,json.data);

                }).catch(function (ex) {
                    console.log(ex);
                });
            },
            goback () {
                this.$router.go(-1)
            },
            //输入框获取焦点
            initPage(){
                this.isFocus = true;
                this.$emit('initSearchPage');
                //为避免重复先清空再添加
                this.searches_list = [];
                let searches=storage.get('_search_');
                this.searches_list = searches?searches:[];
                if (this.searches_list.length > 0 ) {

                    this.historyxs=true;
                }else{
                    this.historyxs=false;

                }

            },
            //点击搜索
            iptShearch(){
                this.isFocus = true;
                if(this.search!=''){ //搜索框不为空
                    saveSearch(this.search); // 本地存储搜索的内容
                    this.$router.push({name: 'class_list_link',query:{keywords:this.search}})
                    this.search='';
                }else {
                    this.$dialog.alert({mes: '请输入您要搜索的内容！'});
                }

            },
            //高级搜索按钮
            // toggleDrawer() {
            //     this.$emit('initSearchPage')
            //     this.$emit('listenSlide')
            // },
            //清空历史记录
            clearhis(){
                storage.remove('_search_');
                this.searches_list = [];
                this.historyxs=false;

            },
            //点击历史搜索把搜索的记录添加到good-storage中
            historysearch(item){
                 var ko =item
                this.$router.push({name: 'class_list_link',query:{keywords:ko,state:1}})
                this.search='';
                this.historyxs = false;
            },
            todo_list(id){
                this.$router.push({name: 'class_list_link',query:{id:id}})
            },
        }
    }
</script>
<style>
    .search_index{ padding-top: 1rem; background: #fff;  height: 100%; width: 100%;}
    .top-box {
    }
    .back-box {
        display: block;
        height: 100px;
        padding: 0 30px;
        line-height: 100px;
        position: absolute;
        left: 0;
        top: 0;
    }
    .back-box span{
        font-size:.3rem;
        color:#333;
    }
    .search-box {
        height: 2rem;
        width: 100%;
        padding-top: .55rem;
    }
    .active{

     }
    .search-box input{
        margin-left: .5rem;
        float: left;
         background: #fafafa;
        width: 70%; height: .8rem;
        border:0;
        border: 1px solid #af773e;
        color: #999;
        border-radius: .1rem 0 0rem 0.1rem;

    }
    .border_co{ border: 1px solid #af773e}
    /*.search-box span{*/
        /*margin: 0 10px 0 18px;*/
        /*display: inline-block;*/
        /*height: 100%;*/
        /*line-height: 64px;*/
        /*font-size: 36px;*/
        /*color:#666;*/
    /*}*/
    .slidemenu-btn {
        background: #af773e;
        width: 1.2rem;
        float: left;
         height: .9rem;
        margin-top: -.05rem;
        color: #fff;
        font-size: .3rem;
        line-height: .9rem;
        box-shadow: 1px 2px 3px #999;

    }
    /*.slidemenu-btn span {*/
        /*color: blue;*/
        /*font-size: 45px;*/
    /*}*/
    /*.text{*/
         /*font-size: 24px;*/
     /*}*/
    .history-panel {
        padding-top: .2rem;
         max-height: 3.5rem;
         width: 100%;
        background: #fff;
        text-align: left;
        padding-left: .3rem;
        padding-right: .3rem;
        overflow: hidden;
        padding-bottom: .2rem;
        margin-bottom: .2rem;
    }
    .history-panel h4{
      font-size: .3rem;
        color: #666;
    }
    .his_ulcon  li{
        height: .5rem;
        float: left;
        width: 22%;
        background: #f5f5f5;
        margin: .1rem ;
        text-align: center;
        line-height: .5rem;
        overflow: hidden;
         border-radius: .05rem;

    }
    .his_ulcon{  margin-top: .1rem; height:.8rem;clear: both;}

    .his_ulcon01{  margin-top: .1rem; height:.8rem;clear: both;}
    .his_ulcon01  li{
        height: 1.2rem;
        float: left;
        width: 18%;
        background: #f5f5f5;
        margin: .1rem .15rem; ;
        text-align: center;
        line-height: 1.2rem;
        overflow: hidden;
        border-radius: 2rem;

    }
    /*.his_ulcon {*/
        /*margin-top: 40px;*/
        /*box-sizing: border-box;*/
        /*padding: 0 30px;*/
        /*display: flex;*/
        /*flex-direction: row;*/
        /*justify-content: flex-start;*/
        /*flex-wrap: wrap;*/
    /*}*/

    .history-panel p{
       font-size: .25rem;
        height: .5rem;
        float: right;
        margin-top: .1rem;
        color: #c5c3c3;
    }


</style>



