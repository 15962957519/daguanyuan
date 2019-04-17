<style scoped>
    .new_nav{
       max-width: 640px;
                 background-color: #F5F5F5;
                 height: auto;
                 margin: 0 auto;
                 overflow: hidden;
                  }
    #style{ border-bottom:  1px solid  #ccc; }
    .wei{padding: 6px;font-size:18px;color:#f90400;font-weight:600;height:26px;box-sizing:content-box!important;}
    .wei a{color:#888}
    .wei span{font-size:14px;font-weight:normal}
    #headermessage ul li:before{
         float: left;
         display: block;
         content: " ";
         width: 0;
         margin-left: 5px;
         height: 0;
         margin-top: 12px;
         border: 5px solid transparent;
         border-left: 5px solid #ccc;
    }
    #headermessage ul li{  width:100%; height:34px;line-height: 34px;  border-bottom:1px #999999 dashed; overflow: hidden;box-sizing: content-box}
    #headermessage ul li a{ margin-left:15px; line-height:20px; color:#373737;  }
</style>


<template>
    <div id="headermessage" class="new_nav">
        <div id="style">
            <div class="wei">今日头条<span style="float: right;color:#6b6bb;"><router-link  :to="{name:'newsindexlists'}" >查看全部></router-link></span>
            </div>
        </div>
        <ul >
            <li  v-for="result in articles"><router-link  :to="{name:'newsdetail',params:{article:result.article_id}}" >{{result.title}}</router-link></li>
        </ul>
    </div>
</template>
<script>
    var config = require('../../config')
    import {weixincommonjsdk} from "../assets/js/common_function.js"
    module.exports = {
        data: function () {
            return {
                articles:[]

            }
        },
        mounted: function () {
            this.fetchData();
        },
        methods: {
            fetchData: function (progress) {
                var _that = this
                axios.get('article/index').then(function (response) {
                    if (response.status == '200') {
                        return response.data;
                    }
                }).then(function (json) {
                    _that.articles=json.data.hot_article_lists
                }).catch(function (ex) {
                    console.log(ex);
                });

            }
        }
    }
</script>


