<style scoped>
 @import '../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
a{
text-decoration:none;
}
#foucswebsite .usercenter{
width:100%;
position:relative;
height:200px;
background-size:cover;
background-repeat:no-repeat;
}
#foucswebsite .usercenter .avatarbox{
position:absolute;
top:26px;
width:80px;
height:80px;
margin-left:-40px;
left:50%;
border-radius:80px;
background-color:#FFF;
}
#foucswebsite .usercenter .avatarbox .center{
text-align:center;
}


#foucswebsite .usercenter .avatarbox .avatar{
width:76px;
height:76px;
border-radius:76px;
background-size:100% 100%;
background-repeat:no-repeat;
background-position:center;
margin:2px;
}
#foucswebsite .usercenter  .userdetail{
position:absolute;
top:126px;
width:90%;
height:65px;
overflow:hidden;
left:0;
right:0;
margin:0 auto;
text-align:center;
}


#foucswebsite .usertatecontainer{
width:100%;
height:115px;
background-color:#FFF;
}
#foucswebsite .usertatecontainer .menuItemBanner{
width:100%;
height:45px;
text-align:center;
position:relative;
}

#foucswebsite .usertatecontainer .menuItemBanner .menuItem{
width:48%;
float:left;
height:25px;
line-height:25px;
margin:10px 0;
font-size:14px;
}
#foucswebsite .usertatecontainer .menuItemBanner .menuItem.borderright{
border-right:1px solid #eaeae8;
}

#foucswebsite .usertatecontainer .menuItemBanner .menuItem span.certificate{
display: inline-block;
width: 22px;
height: 16px;
margin-left: 5px;
border-radius: 1px;
vertical-align: middle;
background-image: url(../../assets/img/cert.png);
background-size: 28px auto;
background-repeat: no-repeat;
background-position: center 0;
}
#foucswebsite .usertatecontainer .menuItemBanner .menuItem .userlevel{
    display:inline-block;
    width: 24px;
    height: 18px;
    line-height: 18px;
    color: #fff;
    text-align: center;
    margin-right: 5px;
    border-radius: 1px;
    background-color: #f6ae69;
}
#foucswebsite .usertatecontainer .menuItemBanner .menuItem div.score{
    position: absolute;
    font-size: 10px;
    text-align: center;
    width: 100%;
    bottom: -8px;
}
#foucswebsite .usertatecontainer .menuItemBanner  .userRateBox{
width:96%;
height:70px;
margin:0 2%;
}

#foucswebsite .usertatecontainer   .userRateBox .userRateList{
width:100%;
height:70px;
}
#foucswebsite .usertatecontainer   .userRateBox .userRateList .userRate{
width:33.33%;
float:left;
height:40px;
line-height:20px;
text-align:center;
margin:15px 0;
}
#foucswebsite .usertatecontainer   .userRateBox .userRateList.buyer{
display:none;
}

#foucswebsite .usertatecontainer  .menuItem.userRateContainer .menuItemBanner .menuItem.selected {
color:#00cccc;
}
#foucswebsite .usertatecontainer  .menuItem.userRateContainer .menuItemBanner .menuItem.selected:after{
content:"\e653";
}
#foucswebsite   .userInfobox{
width:95%;
margin-top:10px;
background-color:#FFF;
padding:0 0 0 5%;
}
#foucswebsite   .userInfobox .userInfoitem{
height:48px;
line-height:48px;
color:#999;
font-size:14px;
position:relative;
}
#foucswebsite   .userInfobox .userInfoitem.productlist{
height:60px;
line-height:60px;
color:#999;
font-size:14px;
position:relative;
}
.l{
float:left;
}
.r{
float:right;
}
#foucswebsite   .userInfobox .title{
width:25%;
}
#foucswebsite   .userInfobox .info{
width:65%;
float:left;
height:48px;
overflow:hidden;
text-align:right;
position:relative;
}#foucswebsite   .userInfobox .info.margintop{
margin-top:6px;
}
#foucswebsite   .userInfobox .info.qrcode{
width:30px;
height:30px;
position:absolute;
top:50%;
margin-top:-15px;
right:0;
background-image:url(/static/img/qrcode_for_gh_76facb74998b_258.jpg);
background-repeat:no-repeat;
background-size:100% ;
background-position:center;
padding:0;
}
#foucswebsite   .userInfobox .imagelist{
height:100%;
width:86%;
position:relative;
}
#foucswebsite   .userInfobox .imagelist ul{
width:100%;
}
#foucswebsite   .userInfobox .imagelist ul li{
 float:right;
 list-style: none;
 margin-right:5px;
 width:48px;
 height:48px;
 line-height:48px;
display: table-cell;
 vertical-align:middle;
 font-size:0;
 *font-size:200px;
 text-align:center;
 border:1px solid #ccc;
}
#foucswebsite   .userInfobox .imagelist ul li:after{
display:inline-block;
 width:0; height:100%;
 content:"center";
vertical-align:middle;
overflow:hidden;
}
#cancelattention{
    width: 94%;
    margin: 30px 3% 20px 3%;
    border: none;
    background: #fe0100;
    border-radius: 4px;
    height: 40px;
    line-height: 40px;
    font-size: 18px;
    color: #FFF;
    outline: none;
}

#foucswebsite   .userInfobox .imagelist ul li img{
vertical-align:middle;
max-height:100%;
max-width:100%;
display:inline-block;
}
#foucswebsite .arrow{
position:absolute;
top:0;
width:48px;
height:48px;
line-height:48px;
right:1%
}

@font-face {
    font-family: "iconfont";
    src: url('//w.tianbaoweipai.com/static/font/index.eot?t=1484042365502'); /* IE9*/
    src: url('//w.tianbaoweipai.com/static/font/index.eot?t=1484042365502#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('//w.tianbaoweipai.com/static/font/index.woff?t=1484042365502') format('woff'), /* chrome, firefox */ url('//w.tianbaoweipai.com/static/font/index.ttf?t=1484042365502') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/ url('//w.tianbaoweipai.com/static/font/index.svg?t=1484042365502#index') format('svg'); /* iOS 4.1- */
}

#foucswebsite .iconfont {
    font-family: "iconfont" !important;
    font-size: 16px;
    color: inherit;
    display:inline-block;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/*图标大小比例*/
#foucswebsite .fi-stack {
    position: relative;
    display: inline-block;
    width: 2em;
    height: 2em;
    line-height: 2em;
    vertical-align: middle;
}

#foucswebsite .icon-shangjiantou1:before{
content:"\e77b";
font-size:2em;
}
#foucswebsite   .verifyState1{
width:24px;
height:18px;
line-height:18px;
}
#foucswebsite   .verifyState1.membercolorv{
    color:#c7c7cc;
    background-color: #757373;
}
#foucswebsite   .verifyState1.membercolorv.ismember{
    color:#ffffff;
    display:inline-block;
    background-color: #EE0000;
}
#foucswebsite  .salelevel{
    width: 24px;
    height: 18px;
    line-height: 18px;
    color: #fff;
    text-align: center;
    margin-right: 5px;
    border-radius: 1px;
    background-color: #f6ae69;
}

.fix{*zoom:1;}
.fix:after{display:block; content:"clear"; height:0; clear:both; overflow:hidden; visibility:hidden;}
</style>
<template>
    <div id="foucswebsite">
        <div class="usercenter"   v-bind:style="{ backgroundImage: 'url(/static/img/userback412.png)' }">
            <div class="avatarbox">
                    <div class="avatar" v-bind:style="{ backgroundImage: 'url(' + userinfo.head_pic + ')' }"></div>
            </div>
            <div class="userdetail">
                <div class="center">{{userinfo.nickname}}</div>
                <div class="center">{{userinfo.usersingnature}}</div>
                <div class="center">结四海朋友，拍天下宝贝</div>
            </div>
        </div>
         <div class="usertatecontainer fix">
                <div class="menuItemBanner  border horizonBottom">
                    <div class="tianbaoweipai  menuItem borderright selected">
                            <span>卖家</span>
                             <template v-if="userinfo.is_authentication == '0'">
                                 <div class="icon iconfont verifyState1 membercolorv">&#xe62f;</div>
                             </template>
                             <template v-else>
                                 <div class="icon iconfont verifyState1 membercolorv ismember">&#xe62f;</div>
                             </template>
                        <div class="icon iconfont salelevel">V{{userinfo.timelevel}}</div>
                            <div class="score"></div>
                    </div>
                     <div class="tianbaoweipai menuItem">
                        <span>买家</span>
                        <span class="userlevel v1">v1</span>
                        <div class="score"></div>
                      </div>
                </div>

                <div class="userRateBox fix">
                        <div class="userRateList seller">
                        					<div class="userRate">
                        						商品区评分<br>0.00
                        					</div>
                        					<div class="userRate">
                        						争议比例<br>0%
                        					</div>
                        					<div class="userRate">
                        						违约比例<br>0%
                        					</div>
                        </div>
                       <div class="userRateList buyer">
                                            <div class="userRate">
                                                商品区评分<br>0.00
                                            </div>
                                            <div class="userRate">
                                                争议比例<br>0%
                                            </div>
                                            <div class="userRate">
                                                违约比例<br>0%
                                            </div>
                        </div>
                </div>
		</div>
                <div class="userInfobox fix">
                        <div class="userInfoitem border horizonBottom">
                                <div class="title l">所在地区</div>
                                <div class="info r"></div>
                        </div>
                   <div class="userInfoitem border horizonBottom">
                                                <div class="title l">认证类型</div>
                                                <div class="info r">
											<div v-if="userinfo.is_authentication ==1">
											已经认证
											</div>
											<div v-else-if="userinfo.is_authentication ==0">
											 未认证
											</div>
											<div v-else>

											</div>
											</div>
                    </div>

													<div class="userInfoitem border horizonBottom">
                                                        <div class="title l">二维码分享</div>
                                                          <router-link class="info r"  tag="div" :to="{path:'/usercenter/myqrcode',query:{userid:userinfo.user_id}}"> <div class="info r qrcode"></div>   </router-link>
                                                        </div>
                                                        <div class="userInfoitem productlist">
                                                        <div class="title l">最新商品区</div>
                                                        <router-link class="info r margintop"  tag="div" :to="{name:'mymain',params:{userid:userinfo.user_id,type:1}}">
                                                            <div class="imagelist">
                                                                <ul>
                                                                    <li v-for="item in product">
                                                                        <img :src="item.image_url_remote_nowater">
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="arrow">
                                                                   <i class="icon tianbaoweipai  icon-shangjiantou1 fi-size fi-stack-2 "></i>
                                                            </div>
                                                        </router-link>
													</div>
                </div>
                    <template v-if="isfocus==false">
                        <button  @click.stop="focus(userinfo.user_id,$event)"  id="cancelattention">关注</button>
                    </template>
                    <template v-else>
                    <button @click.stop="unfocus(userinfo.user_id,$event)" id="cancelattention">取消关注</button>
                    </template>

    </div>
</template>
<script>
import { MessageBox,Navbar, TabItem,Indicator, Actionsheet ,Toast } from 'mint-ui';
Vue.component(Navbar.name, Navbar);
Vue.component(TabItem.name, TabItem);
var config = require('../../../config')
import {
    mapState
} from 'vuex';
import { getuserinfo } from '../../assets/js/common_function.js';
    export default {
        data(){

            return {
                 product:[],
                 userinfo:[],
                isfocus:false
            }
        },
          watch: {
            // 如果 question 发生改变，这个函数就会运行
            question: function (newQuestion) {

            }
          },
          components:{

            },
           mounted: function() {
                var userid=    this.$route.query.userid;
                var obj3 = new Object();
                obj3.u_id =userid;
                this.getuserinfo(obj3);
            },
            methods:{
                unfocus(u_id,e){
                    var u_id =u_id;
                    var token = window.storeWithExpiration.get('token');
                    var el =e;
                    var dataupdate=[];
                    var _that=this;
                    var data=[];
                    axios.get( '/user/unuserfoucs', {
                        params: {
                            token: token,
                            u_id: u_id
                        }
                    }).then(function(response) {
                        if(response.status=='200'){
                            return response.data;
                        }
                    }).then(function(json) {
                        var message ='取消关注成功';
                        if(4000==json.code){
                            message='取消关注失败';
                        }
                        var userid=    _that.$route.query.userid;
                        var obj3 = new Object();
                        obj3.u_id =userid;
                        _that.getuserinfo(obj3);
                        Toast({
                            message:message,
                            iconClass: 'mintui mintui-success'
                        });
                    }).catch(function(ex) {
                        Toast({
                            message: '取消关注失败',
                            iconClass: 'mintui mintui-success'
                        });
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
                        var userid=    _that.$route.query.userid;
                        var obj3 = new Object();
                        obj3.u_id =userid;
                        _that.getuserinfo(obj3);
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
                getuserinfo(obj){
                var	that =this;

                    Indicator.open({
                        text: '正在加载，请稍后...',
                        spinnerType: 'fading-circle'
                    });
               axios.get( '/product/getuserall', {
                    params: {
                        token: storeWithExpiration.get('token'),
                        u_id: obj.u_id
                    }
                }) .then(function(response) {
                        if(response.status=='200'){
                            return response.data;
                        }
                    }).then(function(json) {

                        if(typeof(json.user_info)!='undefined'){
                            that.product = json.product;
                            that.userinfo = json.user_info;
                            if(1==json.isfocus){
                                that.isfocus = true;
                            }else{
                                that.isfocus = false;
                            }
                            Indicator.close();
                        }
                }).catch(function(ex) {
                    console.log(ex);
                   Indicator.close();
                });

                }
            },
            computed:{

            }
    }
</script>