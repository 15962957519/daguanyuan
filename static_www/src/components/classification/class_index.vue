<template>
<div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p class="search02" slot="center" @click="search"><img  src="@/assets/images/search.png">请输入您要搜索的商品</p>
            <p class="search01" slot="right"   @click="search"></p>
        </yd-navbar>
        <!--分类-->
        <div style="height: 500px; overflow-y: auto; position: relative;">
            <yd-scrolltab>
                <yd-scrolltab-panel  v-for="(item ,index) in categorylist" :key="index" :label="item.name" icon="demo-icons-category1">
                    <div class="img_list clearfix">
                        <ul>
                            <template v-for="(menu ,index_k) in item.tmenu">
                                <li @click="todo_list(item.id)"><img :src="menu.image"><p>{{menu.name}}</p></li>
                            </template>
                        </ul>
                    </div>
                </yd-scrolltab-panel>
            </yd-scrolltab>
        </div>
    <!--底部菜单-->
    <menus></menus>

    </div>
</template>
<script>
    import menu from '@/components/menu';
    import Vue from 'vue';
    import {ScrollTab, ScrollTabPanel} from 'vue-ydui/dist/lib.rem/scrolltab';
    /* 使用px：import {ScrollTab, ScrollTabPanel} from 'vue-ydui/dist/lib.px/scrolltab'; */

    Vue.component(ScrollTab.name, ScrollTab);
    Vue.component(ScrollTabPanel.name, ScrollTabPanel);
    export default {
        data() {
            return{
                categorylist:{},
            }

        },
        components: {
            'menus': menu,
        },
        created:function(){

        },
        mounted: function () {
            this.category();
        },

        methods: {
            muse_list:function(){

            },
            category(){
                var _that = this
                var token = window.storeWithExpiration.get('token');
                var url = "/goods_category?token=" + token;
                _that.$axios.get(url).then(response => {
                    _that.categorylist = response.data.data;
                }, response => {
                    console.log("error");
                });
            },
            goback () {
                //alert(111)
                this.$router.go(-1)
            },
            search() {
                this.$router.push({name: 'seach_index_link'})
                //this.$dialog.toast({mes: '搜索：' + this.keywords});
                // this.$router.push({name: 'class_list_link',query:{keywords:keywords}})
            },
            //菜单
            todo_list(id){
                this.$router.push({name: 'class_list_link',query:{id:id}})
            },


        }
    }

</script>
<style>
    .search02{ width: 100%; background: #eee; height: .65rem;  text-align: left;  padding-left: .15rem;  padding-top: .18rem; color: #999}
    .search02 img{vertical-align: top; margin-right: .1rem; width: .35rem; height: .35rem;}
    .search01{ position: relative;background: #eee;height: .65rem;   width: 100%;  right: .5rem;}
    .margin{ padding-top: 1rem;}
    .img_list{ width: 100%; height:auto;}
    .img_list ul li { width: 33.3%; float: left;}
    .img_list ul li p{  margin-top: -.2rem;}
    .img_list ul li img { width: 90%; padding: .2rem; margin-top: 10px;}
     .yd-scrolltab-content-title{ text-align: left;  font-weight: bold; color: #333; font-size: .26rem !important;  }
    .yd-scrolltab-active {
        background-color: #fff;
        color: #af773e !important;
        border-left: 3px solid #af773e;
        font-weight: bold;
    } .yd-scrolltab-title{ color: #333;}

    .clearfix:after{
        content:"";
        display:block;
        visibility:hidden;
        clear:both;
        height:0;
    }
    .clearfix{ /* 为了照顾ie6浏览器*/
        zoom:1;
    }
</style>