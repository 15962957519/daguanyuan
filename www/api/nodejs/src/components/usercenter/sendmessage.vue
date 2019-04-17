<style scoped>
 @import '../../assets/css/user_center_seller_center01.css';
</style>
<style scoped>
    #myassets_draw .icon {
        position:relative;
        min-width: 1em;
        font-size: 30px;
        -webkit-transition: font-size 0.25s ease-out 0s;
        -moz-transition: font-size 0.25s ease-out 0s;
        transition: font-size 0.25s ease-out 0s;
    }
@font-face {
    font-family: "usercenter";
    src: url('//w.tianbaoweipai.com/static/font/usercenter.eot?t=1484042365502'); /* IE9*/
    src: url('//w.tianbaoweipai.com/static/font/usercenter.eot?t=1484042365502#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('//w.tianbaoweipai.com/static/font/usercenter.woff?t=1484042365502') format('woff'), /* chrome, firefox */ url('//w.tianbaoweipai.com/static/font/usercenter.ttf?t=1484042365502') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/ url('//w.tianbaoweipai.com/static/font/usercenter.svg?t=1484042365502#usercenter') format('svg'); /* iOS 4.1- */
}


.iconfont {
    font-family: "usercenter";
    font-size: 32px;
    color: inherit;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
#sendmessage .baner{
    position: fixed;
    top: 0px;
    width: 100%;
    background-color: #fafafa;
    border-bottom: 1px solid #bfbfbf;
    display: table;
    z-index: 999;
}
    #sendmessage  .baner .btn{
        float: right;
        padding: 6px 10px;
        text-shadow: 0px 0px 0px #fff;
        text-align: right;
        font-size: 16px;
        line-height: 16px;
        border: 1px solid #fe0100;
        color: #fe0100;
        border-radius: 4px;
        margin: 10px 10px 10px 0;
    }
    #sendmessage    .salemsgmain{
        width: 100%;
        display: table;
        margin-top: 50px;
    }

    #sendmessage    .salemsgmain .sendheader{
        width: 100%;
        border-bottom: 1px solid #EBEBEB;
        display: table;
    }
    #sendmessage    .salemsgmain .sendheader .contentstatic{
        width:96%;
        line-height: 24px;
        margin:6px 3%;
    }
 #sendmessage   .saleBox .saleItem .mint-switch-input{
    display:none;
 }


    #sendmessage   .saleBox .saleItem{
        width:96%;
        padding:0px 2%;
        font-size:12px;
        overflow:hidden;
        margin-bottom:10px;
        border-bottom:1px solid #eaeae8;
        position: relative;
        background-color:#fff;
    }
    #sendmessage   .saleBox .saleItem .switchBtn{
        height: 30px;
        width: 20px;
        position: absolute;
        top: 50%;
        margin-top: -25px;
        right: 5%;
    }
    #sendmessage   .contentbox .myMain .listContainer{
        width:96%;
        display:table;
        height:auto;
        overflow:auto;
        margin:0 2%;
    }
    #sendmessage  .contentbox .myMain .listContainer .saleBox{
        width:100%;
        display:table;
    }
    #sendmessage   .saleBox .saleItem .info .content .goodsImg{
        float: left;
        width: 80px;
        height: 80px;
        margin: 1px;
        margin-right: 12px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
    }
    #sendmessage   .saleBox .saleItem .info{
        width:100%;
        padding:4px 0;
        display:table;

    }
    #sendmessage   .saleBox .saleItem .info .content{
        width:100%;
        float:left;
    }
    #sendmessage  .saleBox:after{
        display:block;
        content:"clear";
        height:0;
        clear:both;
        overflow:hidden;
        visibility:hidden;
    }

    #sendmessage .listContainer .saleBox .saleItem .bid{
        float:left;
        text-align:left;
        color:#fe0100;
        height:16px;
        line-height:16px;
        margin-top:8px;
    }

   #sendmessage .listContainer .saleBox .saleItem .status {
        line-height: 16px;
        height: 16px;
        float: left;
        text-align: left;
        margin-top: 8px;
        margin-left: 20px;
        text-indent: 20px;
        font-size: 12px;
        background-size: 16px;
        background-repeat: no-repeat;
        background-image: url(../../assets/img/time.png);
    }
    #sendmessage .listContainer .saleBox .saleItem .statics {
        color:#fe0100;
        font-size: 12px;
        line-height: 16px;
        height: 16px;
        float: left;
        text-align: left;
        margin-top: 8px;
        margin-left: 20px;
        text-indent: 20px;
        background-size: auto 70%;
        background-position: center left;
        background-repeat: no-repeat;
        background-image: url(../../assets/img/review.png);
    }
    .mint-checkbox-input:checked+.mint-checkbox-core {
        background-color: #26a2ff;
        border-color: #26a2ff;
    }
    .mint-checkbox-core {
        display: inline-block;
        background-color: #fff;
        border-radius: 100%;
        border: 1px solid #ccc;
        position: relative;
        width: 20px;
        height: 20px;
        vertical-align: middle;
    }
    #sendmessage  .saleBox .saleItem .tools{
        width:100%;
        height:32px;
        padding:4px 0;

    }
    .mint-checkbox-input{
    display:none;
    }
    .mint-checkbox-input:checked + .mint-checkbox-core::after {
        border-color: #fff;
        -webkit-transform: rotate(45deg) scale(1);
                transform: rotate(45deg) scale(1);
    }

    .mint-checkbox-core::after {
        border: 2px solid transparent;
        border-left: 0;
        border-top: 0;
        content: " ";
        top: 3px;
        left: 6px;
        position: absolute;
        width: 4px;
        height: 8px;
        -webkit-transform: rotate(45deg) scale(0);
                transform: rotate(45deg) scale(0);
        -webkit-transition: -webkit-transform .2s;
        transition: -webkit-transform .2s;
        transition: transform .2s;
        transition: transform .2s, -webkit-transform .2s;
    }

</style>
<template>
    <div id="sendmessage" v-infinite-scroll="loadMore" infinite-scroll-disabled="busy"  infinite-scroll-distance="10">
        <div class="baner">
            <!--此处修改-->
            <div @click.stop="sendmessage($event)" class="send btn">点击群发</div>
        </div>

        <div class="salemsgmain">
                <div class="sendheader">
                    <div class="contentstatic">
                        您目前的会员等级为<span><em>{{userinfolevel}}</em></span>，今日剩余群发次数：<span><em class="saleMsgNum">{{membersendactivecount}}</em></span>次，且一次最多发<span><em>1</em></span>件商品区
                        <a href="/javascript:;">（查看消息演示）</a>
                        <!--<a style="color:#FE0100;" href="javascript:;">（温馨提示）</a>-->
                        <!--此处修改-->
                          <router-link to="/usersellindex/warm_prompt" style="color:#FE0100;">（温馨提示）</router-link>
                    </div>
                </div>

            <div class="listContainer publish">
            <form id="productsf">
                <div class="saleBox sale">
                    <div class="saleItem" :id="['item'+item.goods_id]" v-for="(item, index)  in lists">
                        <div class="info">
                            <div class="content">
                                <router-link   :to="{path:'/mymain',query:{goods_id:item.goods_id,type:2}}">
                                    <div class="goodsImg" v-lazy:background-image="item.img"></div>
                                </router-link>
                                <div class="title">{{item.goods_name}}</div>
                                <div class="desc">{{item.goods_content}}</div>
                                <div class="publishTime">截拍时间：{{item.endTime}}</div>
                                <div class="publishTime">上传时间：{{item.upload_time}}</div>
                            </div>
                        </div>

                        <div class="tools">
                            <div class="bid">￥<span>{{item.start_price}}</span>元</div>
                            <div class="statics">{{item.click_count}}</div>
                            <div class="status disable">{{item.goods_status_desc}}</div>
                        </div>
                        <div class="switchBtn">      <input type="radio"  class="mint-checkbox-input" :id="item.goods_id"  :value="item.goods_id"   name="productids[]"/>
                            <label  class="mint-checkbox-core" :for="item.goods_id" ></label></div>
                    </div>
                </div>
            </form>

                <div class="loadNextPage" style="visibility: visible;">
                    <div class="loading">加载下一页</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { MessageBox,Navbar, TabItem,Indicator, Actionsheet ,Toast } from 'mint-ui';
import { getuserfocus ,isEmptyObject } from '../../assets/js/common_function.js';
import {mapState } from 'vuex';
var config = require('../../../config')
    export default{
        data(){
            return {
                busy:false,
                userinfolevel:'',
                membersendactivecount:'',
                token:storeWithExpiration.get('token'),
                enableReturndefault:false,
                userinfo:[],
                lists:[]
            }
        },
          watch: {
            // 如果 question 发生改变，这个函数就会运行
          },
        mounted: function() {
                document.title = "群发消息";

            },
            methods:{
                loadMore()
                {
                    this.busy = true
                    this.getprouuct();
                },
                getprouuct(){
                    var _that =this;
                    var dd =new FormData();
                    dd.append('token',storeWithExpiration.get('token'))
                    //提交到后台
                    axios.post('/usercenterproduct/indexsendmessage',dd)
                        .then(function (response) {
                            _that.lists =response.data.data.lists;
                            _that.userinfolevel =response.data.data.userinfolevel;
                            _that.membersendactivecount =response.data.data.membersendactivecount;
                        })
                        .catch(function (error) {

                        });
                },
                sendmessage(e){
                    var	that =this;
                    //先获取需要的值
                      Indicator.open({
                                                              text: '正在发送，请稍后...',
                                                              spinnerType: 'fading-circle'
                                                            });
                    var formElement = document.querySelector("#productsf");
                    var dd =new FormData(formElement);
                    dd.append('token',storeWithExpiration.get('token'))
                    axios.post('/masssendmessage', dd)
                        .then(function(response) {
                         Indicator.close();
                            if(response.status=='200'){
                                return response.data;
                            }
                        }).then(function(json) {
                        if(json.code==2000){
                           MessageBox.alert('发送成功');
                        }else{
                          MessageBox.alert(json.message);
                        }
                    }).catch(function(ex) {
                     Indicator.close();
                        console.log(ex);
                    });
                    return false;
                }
            },
             computed: mapState({

                  })
    }
</script>

