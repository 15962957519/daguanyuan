<template>
  <div id="mumberupgrade">
              <div class="mumberimg">
                    <img src="http://img.tianbaoweipai.com/weipaiimg/ad/1704/membergrade.png?x-oss-process=image/resize,h_320">
              </div>
              <div class="vip">
                <router-link tag="div" class="vip-item" style="background:#87CECB;position:relative;"  to="/usercenter/paying/1198/1">
                          <div class="left">
                            <h3>铜牌会员</h3>
                            <p><strong>1198</strong>元/年 </strong></span></p>
                          </div>
                          <div class="right">
                                        <button>点击升级</button>
                          </div>
                          <div style="clear:both;"></div>
                </router-link>
                <router-link tag="div" class="vip-item" style="background:#90c5fb;" :to="{name:'paying',params:{price:2198,type:2}}">
                  <div class="left">
                    <h3>银牌会员</h3>
                    <p><strong>2198</strong>元/年<span class="oriprice"></span></p>
                  </div>
                  <div class="right">
                    <button>点击升级</button>
                  </div>
                  <div style="clear:both;"></div>
                </router-link>
               <router-link  tag="div" :to="{name:'paying',params:{price:3198,type:3}}" class="vip-item" style="background:#FFD700;position:relative;">
                      <span  class="hot_sale" >热销</span>
                      <div class="left">
                        <h3>金牌会员</h3>
                        <p><strong>3198</strong>元/年<span class="oriprice"></span></p>
                      </div>
                      <div class="right">
                          <button>点击升级</button>
                      </div>
                      <div style="clear:both;"></div>
                 </router-link>
           <router-link tag="div" :to="{name:'paying',params:{price:5998,type:4}}"  class="vip-item" style="background:#9932CC;">
                     <div class="left">
                      <h3>钻石会员</h3>
                      <p><strong>5998</strong>元/年<span class="oriprice"></span></p>
                    </div>
                    <div class="right">
                      <button>点击升级</button>
                    </div>
                    <div style="clear:both;"></div>
             </router-link>

               <h4  style="color:#fe0100;text-align:center;margin-top:10px; border-bottom: 2px solid #ff0000;">*会员升级必看*功能介绍&nbsp;&nbsp;可以左右滚动</h4>
            </div>

                       <function_product></function_product>
               <div style="padding:20px 10px;line-height:24px;">
                 <h4>升级流程</h4>
                 <p>1.根据您的需求选择合适的会员服务。</p>
                 <p>2.支付会员费用并且确认提交（目前在线支付仅支持微信支付，其他支付方式请联系专属客服顾问）</p>
                 <p>3.官方资料审核，开通会员服务。</p>
               </div>


               <div style="padding:20px 10px;line-height:24px;">
                   <h4>升级须知</h4>
                   <p>1.付费升级成功后，即可享受新的会员权益。</p>
                   <p>2.会员权益有效期为自升级成功日起的一整年。</p>
                   <p>3.会员升级为在线服务产品，一经升级无法退还、转让，敬请理解。</p>
                   <p>4.升级天宝微拍会员，即表示同意：<router-link to="/usersellindex/mumber_notice">《天宝微拍VIP商户服务协议》</router-link></p>
                 </div>
              <div style="padding:20px 10px;line-height:24px;">
                <h4>特别提醒</h4>
                <p>1.天宝微拍为在线拍卖服务平台，无法为所有会员提供鉴定、估价，私下交易的服务要求。</p>
                <p>2.普通游客升级为VIP会员，可获得相应的服务权限，但无法保证升级成VIP会员就一定成交。</p>
                <p>3.天宝微拍所匹配的粉丝均是通过网络平台投放广告获得，无法100%保证其质量。</p>
              </div>

      </div>

</template>
<script type="text/babel">
    var config = require('../../../../config')
   import {MessageBox, SwipeItem , Navbar,Tabbar,TabItem,Indicator } from 'mint-ui';
   Vue.component(Tabbar.name, Tabbar);
   Vue.component(Navbar.name, Navbar);
   Vue.component(TabItem.name, TabItem);

    Vue.component(SwipeItem.name, SwipeItem);

   const function_product =require('./function_introduction.vue');
    export default {
        data(){
            return {

            }
        },
        components:{
                    function_product
                },
        mounted: function() {

            },
            methods:{



            },
            computed:{

            }
    }
</script>

<style scoped>

#mumberupgrade .mumberimg{
ont-size:0; *font-size:200px; text-align:center;
width:100%;
min-height:188px;
}
#mumberupgrade .mumberimg:after{
display:inline-block; width:0; height:100%; content:"center"; vertical-align:middle; overflow:hidden;
}

#mumberupgrade .mumberimg img{

vertical-align:middle;
max-width:100%;
max-height:100%;
}


#mumberupgrade .currentstatus{
padding:20px 10px;
line-height:28px;
}



#mumberupgrade .currentstatus p{
 text-indent:30px;
}
#mumberupgrade .vip {
text-align:center;
}
#mumberupgrade .vip  .vip-item{
position:relative;
  padding: 10px;
  margin: 10px;
  color: #FFF;
}
#mumberupgrade .vip  .vip-item .oriprice{
  color: #FFF;
  text-decoration:line-through;
}
#mumberupgrade .vip  .vip-item .hot_sale{
position:absolute;
right:0;
top:0;
background:url(../../../assets/img/hot-flag.png) no-repeat;
width:60px;
height:60px;
text-indent: 30px;
line-height: 30px;
}
#mumberupgrade .vip  .vip-item h3{
 font-size:18px;

}
#mumberupgrade .vip  .vip-item .xuxian{
  border-bottom: 2px solid #ff0000;
}
#mumberupgrade .function_introduction{
width: 750px;
height:1200px;
background:url('/static/img/member.png') center  no-repeat;
background-size: cover;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
}

#mumberupgrade .vip  .vip-item p strong{
 font-size:18px;
}
#mumberupgrade .vip .vip-item .right button{
    border: 1px solid #FFF;
    padding: 4px 8px;
    background: none;
    color: #FFF;

}
#mumberupgrade .vip  .vip-item .left{
float:left;
width:60%;

}
#mumberupgrade .vip  .vip-item  .right{
float:right;
width:40%;
height:79px;
line-height:79px;
overflow:hidden;
}

.n-support{
  background: url(../../../assets/img/cha.png) no-repeat center;
  background-size: 14px;
}

.y-support{
  background: url(../../../assets/img/gou.png) no-repeat center;
  background-size: 14px;
}


</style>