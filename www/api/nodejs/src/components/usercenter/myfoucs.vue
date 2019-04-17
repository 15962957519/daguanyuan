<style scoped>
 @import '../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
a{
text-decoration:none;
}
#myfoucs .myfriend{
width:100%;
position:relative;
height:70px;
display:table;
}
#myfoucs .myfriend .avatar{
float:left;
margin:15px;
width:40px;
height:40px;
background-size:cover;
background-repeat:no-repeat;
background-position:center;
}

#myfoucs .myfriend .detail{
height:70px;
float:left;
margin-top:9px;
}
#myfoucs .myfriend .detail .nickname{
width:100%;
height:25px;
line-height:25px;
font-size:15px;
text-indent:30px;
overflow:hidden;
position:relative;
}
#myfoucs .myfriend .detail .nickname .level{
position: absolute;
left: 0;
width: 28px;
margin-top: 5px;
height: 15px;
border-radius: 1px;
background-size: 48px auto;
background-image:url('/static/img/levelscommoncolor.png');
background-repeat:no-repeat;
}
#myfoucs .myfriend .detail .nickname .level.v1{
    background-position: -10px -7px;
}
#myfoucs .myfriend .detail .nickname .level.v2{
    background-position: -10px -37px;
}
#myfoucs .myfriend .detail .nickname .level.v3{
    background-position: -10px -67px;
}
#myfoucs .myfriend .detail .nickname .level.v4{
    background-position: -10px -97px;
}
#myfoucs .myfriend .detail .nickname .level.v5{
    background-position: -10px -126px;
}
#myfoucs .myfriend .detail .nickname .level.v6{
    background-position:-10px -156px;
}
#myfoucs .myfriend .detail .nickname .level.v7{
    background-position: -10px -185px;
}
#myfoucs .myfriend  .signature{
    height:35px;
    width:100px;
    overflow:hidden;;/* 内容超出宽度时隐藏超出部分的内容 */
    text-overflow:ellipsis;;/* 当对象内文本溢出时显示省略标记(...) ；需与overflow:hidden;一起使用。*/
    white-space:nowrap;/* 不换行 */
}
#myfoucs .myfriend  .detailaction{
position:absolute;
top:50%;
right:0;
margin-top:-10px;
height:auto;
}
#myfoucs .myfriend  .detailaction{
width:80px;
}
#myfoucs .myfriend  .detailaction .status{
width:70px;
height:20px;
line-height:20px;
background-size: auto 100%;
background-image: url(../../assets/img/follow.png?t=4);
background-repeat:no-repeat;
}
#myfoucs .myfriend  .detailaction .status.nofocus{
background-image: url(../../assets/img/unfollow.png?t=4);
}
#myfoucs .myfriend  .detailaction .status.focus{
background-image: url(../../assets/img/follow.png?t=4);
}
</style>
<template>
    <div id="myfoucs" v-infinite-scroll="loadMore" infinite-scroll-disabled="busy"  infinite-scroll-distance="10">
            <div class="myfriend border horizonBottom" v-for="item in lists">
              <a href="javascript:;">
              <div class="avatar" v-bind:style="{ backgroundImage: 'url(' + item.head_pic + ')' }">
                </div></a>
                <div class="detail">
                    <div class="nickname">
                        <template v-if="item.timelevel <= '1'">
                            <div class="icon iconfont salelevel level v1"></div>
                        </template>
                        <template v-else-if="item.timelevel <= '2'">
                            <div class="icon iconfont salelevel level v2"></div>
                        </template>
                        <template v-else-if="item.timelevel <= '3'">
                            <div class="icon iconfont salelevel level v3"></div>
                        </template>
                        <template v-else-if="item.timelevel<= '4'">
                            <div class="icon iconfont salelevel level v4"></div>
                        </template>
                        <template v-else-if="item.timelevel <= '5'">
                            <div class="icon iconfont salelevel level v5"></div>
                        </template>
                        <template v-else-if="item.timelevel <= '6'">
                            <div class="icon iconfont salelevel level v6"></div>
                        </template>
                        <template v-else-if="item.timelevel <= '7'">
                            <div class="icon iconfont salelevel level v7"></div>
                        </template>
                        <template v-else>
                            <div class="icon iconfont salelevel level v1"></div>
                        </template>
                        <span>{{item.nickname}}</span>
                    </div>
                    <div class="signature">{{item.usersingnature}}</div>
                </div>
                    <div  class="detailaction">
                           <div @click.stop="unfocus(item.user_id,$event)" class="status nofocus"></div>
                     </div>
            </div>
    </div>
</template>
<script>
var copyright  = require('../copyright.vue');
import { MessageBox,Navbar, TabItem,Indicator, Actionsheet ,Toast } from 'mint-ui';
import { getuserfocus,isEmptyObject  } from '../../assets/js/common_function.js';
    import {
            mapState
    } from 'vuex';
var config = require('../../../config')
    export default {
        data(){
            return {
                busy: false,
                page:1,
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
          components:{
                    'copyright':copyright
            },
        mounted: function() {
                  document.title = "我的关注";
                    this.$store.state.focuslists=[];
            },
            methods:{
                loadMore()
                {
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
                    axios.get('/user/userinfofocusall', {
                        params: {
                            token: storeWithExpiration.get('token'),
                            page: that.page,
                            is_fans:1
                        }
                    }) .then(function(response) {
                            if(response.status=='200'){
                                return response.data;
                            }
                        }).then(function(json) {
                            if(typeof(json.data)!='undefined'){
                                if( !isEmptyObject(json.data)){
                                    that.$store.commit('unupfoucs', {u_id:0,data:json.data});
                                    that.busy = false;
                                }else{
                                    that.busy =true;
                                }
                            }
                    }).catch(function(ex) {
                        console.log(ex);
                    });
                        that.page++;
                        return false;

                    }
            },
             computed: mapState({
                      lists: function(state) {
                         return state.focuslists;
                      }
                  })
    }
</script>

