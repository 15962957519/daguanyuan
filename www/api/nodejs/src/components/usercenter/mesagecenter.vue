<style scoped>
 @import '../../assets/css/user_center_seller_center01.css';
</style>
<style scoped>
#mesagecenter{
height:100%;
background-color:#eee;
}
#systemMessage{
    position: relative;
    height: 64px;
    font-size: 16px;
    padding-left: 20px;
    line-height: 64px;
    margin: 10px 0;
    background-color: #fff;
}
#systemMessage .systemMessageMain{
    display: table;
    width:100%;
}
#systemMessage .msgIcon{
     background-image: url(../../assets/img/message.png);
    background-repeat: no-repeat;
    background-position: center;
    width: 45px;
    /* top: 4.5px; */
    float: left;
    line-height: 45px;
    background-size: contain;
    height: 45px;
    margin-right:20px;
 }

#systemMessage .table-cell{
    display:table-cell;
    vertical-align: middle;
    float: left;
    padding:10px 0;
}
#systemMessage   .arrow{
        background:#fff url(../../assets/img/headerjiantou.png) no-repeat;
        background-position:50% 50%;
        background-size:contain;
    width: 20px;
    margin-right: 20px;
    float: right;
    display: inline-block;
    height: 64px;
    }
#mesagecenter .messagelists  .messageRoomList{

     width: 100%;
     display: table;
     overflow: hidden;
     background-color: #FFF;

 }
#mesagecenter .messagelists .messageItem{
    height:50px;
    padding: 10px 0;
    position:relative;
    cursor: pointer;
}
#mesagecenter .messagelists .messageItem .avatar{
    width:50px;
    height:50px;
    float:left;
    background-position: center;
    background-repeat: no-repeat;
    background-size: 100%;
}
#mesagecenter .messagelists .messageItem .detail{
    display: table;
}
#mesagecenter .messagelists .messageItem .info{
    position:absolute;
    right:3%;
    top:10px;
    height:50px;

}
#mesagecenter .messagelists .messageItem .info .updateTime{
    line-height: 25px;
    font-size:14px;
    color:#888;
}
</style>
<template>
    <div id="mesagecenter" v-infinite-scroll="loadMore"   infinite-scroll-disabled="busy" infinite-scroll-distance="10">
        <div id="systemMessageMain">
        <!--系统消息-->
            <div id="systemMessage">
                <div class="table-cell">
                    <div class="msgIcon"></div>
                </div>
                <span>系统消息</span>
                <div class="arrow"></div>
            </div>
        </div>

        <div class="messagelists">
            <div class="messageRoomList">
                <div v-for="item in messagelist" class="messageItem border horizonBottom system" uri="system" roomuri="system" data-updatetime="1492417020" data-time="1492417020">
                    <template v-if="item.type==1">
                        <div class="avatar" style="background-image: url(/static/img/logo100_100.png)" data-img="/static/img/logo100_100.png"></div>
                        <div class="detail">
                            <div class="nickname">天宝微拍消息</div><div class="lastMessage">[系统消息]{{item.message}}</div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="avatar" style="background-image: url(/static/img/logo100_100.png)" data-img="/static/img/logo100_100.png"></div>
                        <div class="detail">
                            <div class="nickname">天宝微拍消息</div><div class="lastMessage">[普通消息]{{item.message}}</div>
                        </div>
                    </template>
                    <div class="info">
                        <div class="updateTime">{{item.send_time|timetoformate }}</div><div class="unread"></div>
                    </div>
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
    export default {
        data(){
            return {
                busy: false,
                page:1,
                messagelist:[],
                 actions: [{
                           name: '确定'
                         }, {
                           name: '返回上一步',
                           method: this.goBack
                         }]
            }
        },
          watch: {
            // 如果 question 发生改变，这个函数就会运行
            question: function (newQuestion) {

            }
          },
        mounted: function() {
                document.title = "消息中心"
                this.$store.state.messagelist=[];
                this.$store.commit('commonadddata', {name:'messagelist',u_id:0,data:[]});
            },
            methods:{
                loadMore(){
                    this.busy = true
                    //加载数据
                    var obj3 = new Object();
                    this.getuserfocus(obj3);
                },
                unfocus(u_id,e){
                        this.$store.dispatch('actions_unuserFocus_function', {
                            u_id: u_id,
                            e: e,
                            toast:Toast
                        });
                },
                 focus(u_id,e){
                              var token = window.storeWithExpiration.get('token');
                              var el =e;
                              var dataupdate=[];
                              var _that=this;
                              var data=[];
                                  axios.get( '/user/userfoucs', {
                                      params: {
                                          token: token,
                                          u_id: u_id
                                      }
                                  }).then(function(response) {
                                      if(response.status=='200'){
                                          return response.data;
                                      }
                                  }).then(function(json) {
                                      var message ='关注成功';
                                      if(4000==json.code){
                                          message='已经关注';
                                      }else if(2000==json.code){
                                          _that.$store.commit('upfoucs', json);
                                      }
                                      Toast({
                                          message:message,
                                          iconClass: 'mintui mintui-success'
                                      });

                                  }).catch(function(ex) {
                                      Toast({
                                          message: '关注失败',
                                          iconClass: 'mintui mintui-success'
                                      });
                                  });
                },
                    getuserfocus(obj){
                    var	that =this;
                    axios.get( '/user/message', {
                        params: {
                            token: storeWithExpiration.get('token'),
                            page: that.page
                        }
                    }) .then(function(response) {
                            if(response.status=='200'){
                                return response.data;
                            }
                        }).then(function(json) {
                            that.busy = false;
                            if(typeof(json.data.data)!='undefined'){
                                if( !isEmptyObject(json.data.data)){
                                    that.$store.commit('commonadddata', {name:'messagelist',u_id:0,data:json.data.data});
                                    that.busy = false;
                                }else{
                                    that.busy =true;
                                }
                            }
                    }).catch(function(ex) {
                        console.log(ex);
                    });
                     that.page++;
                        that.busy = true;
                     return false;
                    }
            },
             computed: mapState({
                 messagelist: function(state) {
                         return state.messagelist;
                      }
                  })
    }
</script>

