<template>
    <div id="paymenting">
            <div class="blank"></div>
            <template v-if="$route.query.good_id >0">
                <div class="saleMain">
                    <div class="title border horizonBottom">商品区信息</div>
                    <div class="goodInfo border horizonBottom">
                        <div class="img"   v-lazy:background-image="prouuctdata.image_url_remote_nowater">
                            <div class="freePost">
                                <i class="tianbaoweipai icon-freepost"></i>            </div>
                        </div>
                        <div class="desc">{{prouuctdata.goods_content}}</div>
                        <div class="endTime"><span>本次出价：</span><span>{{prouuctdata.bid_price}}</span></div>
                    </div>
                    <div class="saleArgs">
                        <div class="argItem">
                            <span>起拍价：</span>
                            <span>{{prouuctdata.start_price}}</span>
                        </div>
                        <div class="argItem">
                            <span>加价：</span>
                            <span>{{prouuctdata.every_add_price}}</span>
                        </div>
                    </div>
                </div>
            </template>
           <div class="payMain">
               <div class="payPrice border horizonBottom">
                   <span>保证金：</span>
                   <span class="h1">￥100.00</span>
                   <span class="payMemo">（"拍卖失败"或"付款"后退还）</span>
               </div>
               <div class="payMethodList">
                   <div class="payMethod border horizonBottom weixin selected" paymethod="pc_weixin">
                       <div class="checkbox">
                           <div class="checked">
                               <span class="tianbaoweipai fi-stack">
                                   <i class="tianbaoweipai icon-circle fi-stack-2x"></i>
                                   <i class="tianbaoweipai icon-selected fi-stack-1x fi-color-fff"></i>
                               </span>
                           </div>
                       </div>
                       <div class="icon">
                           <span class="tianbaoweipai fi-stack">
                               <i class="tianbaoweipai icon-iconfontweixin fi-color-fff fi-stack-1x2"></i>
                           </span>
                       </div>
                       <div class="content">
                           <div class="title">微信支付</div>
                           <div class="desc">单笔最高5,000-50,000元</div>
                       </div>
                   </div>
                               </div>
               <div class="payBtn">
                   <div  @click.stop="callpay($event)" class="submit">安全支付</div>
               </div>
			  </div>
           </div>
</template>
<script>
	var config = require('../../../config')
import 'mint-ui/lib/style.css'
import { MessageBox ,Indicator} from 'mint-ui';
    export default {
        data(){
            return {
                    prouuctdata:{},
                    weixinpayconfig:{}
            }
        },
        mounted: function() {
                       //获取作品信息
                            this.geteditproductinfo()
            },
            methods:{
            geteditproductinfo(){
                    var good_id=    this.$route.query.good_id;
                    var	that =this;
                    axios.get('/index/getproductbyid', {
                                    params: {
                                        token: storeWithExpiration.get('token'),
                                        good_id:good_id
                                    }
                                })
                                .then(function(response) {
                                if(response.status=='200'){
                                      return response.data;
                                }
                    }).then(function(json) {
                    that.prouuctdata = json.data;
                      that.weixinpayconfig  = json.jsApiParameters;

                    }).catch(function(ex) {

                    });
            },
            //调用微信JS api 支付
            jsApiCall()
            {
            var _that=this;
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    this.weixinpayconfig,
                    function(res){
                        WeixinJSBridge.log(res.err_msg);
                        if(res.err_code==0){
                               MessageBox.alert("支付成功").then(action => {
                                                                         _that.$router.go(-1)
                                                                         });
                        }else if(res.err_code==-1){
                                 MessageBox.alert("出错了").then(action => {
                                                                                                     _that.$router.go(0)
                                                                                                     });

                        }else if(res.err_code==-2){
                                 MessageBox.alert("用户取消了支付").then(action => {
                                                                                                     _that.$router.go(0)
                                                                                                     });

                        }
                    }
                );
            },
            callpay()
            {
                Indicator.open({
                  text: '正在申请支付，请稍后...',
                  spinnerType: 'fading-circle'
                });
                 setTimeout(() => Indicator.close(), 2000);
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    this.jsApiCall();
                }
            }
            },
            computed:{

            }
    }
</script>
<style scoped>
 @import '../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
div{
display:block;
}
#paymenting .blank{
width:100%;
height:10px;
}

#paymenting  .saleMain .title {
        width: 96%;
        margin-left: 4%;
        height: 45px;
        line-height: 45px;
        font-size: 16px;
    }

#paymenting  .saleMain .goodInfo{
width: 92%;
padding: 12px 4% 12px 0;
margin-left: 4%;
display: table;
position:relative;
}
 #paymenting  .saleMain .goodInfo .img {
        position: relative;
        float: left;
        margin-right: 10px;
        width: 80px;
        height: 80px;
        border-radius: 2px;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        background-color: #EEE;
    }

   #paymenting    .saleMain .goodInfo .desc {
            line-height: 20px;
            margin-top: -3px;
            padding-left: 90px;
            padding-right: 2%;
        }
         #paymenting    .saleMain  .endTime{
           position: absolute;
                color: #888;
                height: 24px;
                line-height: 24px;
                bottom: 8px;
                right: 12px;
            }

        #paymenting     .saleMain .saleArgs{
    width: 92%;
    margin: 0 4%;
    height: 35px;
    line-height: 35px;
    color: #888;
    font-size: 12px;

        }
        .border{
        position:relative;
        }
          #paymenting     .saleMain .saleArgs .argItem{
            float:left;
            width:48%;
            height:35px;
            line-height:35px;
          }

          #paymenting .payMain{
           width: 100%;
                        margin-top: 10px;
                        background-color: #FFF;
                        display: table;


          }
#paymenting .payMain .payMethodList{
width:96%;
display:table;
margin-left:4%;


}
#paymenting .payMain .payMethodList .payMethod{

width:100%;
display:table;
height:60px;
cursor:pointer;

}
 .payMain .payMethodList .payMethod .checkbox {
        float: left;
        width: 26px;
        height: 26px;
        margin: 17px 5px 17px 0;
    }
 .payMain .payMethodList .payMethod .checkbox {
        float: left;
        width: 26px;
        height: 26px;
        margin: 17px 5px 17px 0;
    }
     .payMain .payMethodList .payMethod .checkbox .checked{
        width:26px;
        height:26px;
        float:left;
        color:#28DA46;
        font-size:16px;
        margin-left:-2px;
          display: -webkit-flex; /* Safari */
           -webkit-align-items: center; /* Safari 7.0+ */
           display: flex;
           align-items: center;

     }
     .payMain .payMethodList .payMethod .icon{
        width:38px;
        height:38px;
        float:left;
        font-size: 25px;
        align-items: center;
        display: -webkit-flex;
        margin: 11px 16px 11px 4px;
        -webkit-align-items: center;
     }
      .payMain .payMethodList .payMethod  .content{

      float:left;
      height:38px;
      margin:11px 0 11px 6px;
      }
#paymenting .payMain  .fi-stack-2x{
font-size:2em;
}#paymenting .payMain  .fi-stack-1x{
font-size:2.1em;
}
.payMain .payMethodList .payMethod .checkbox  .icon-circle:before{
content:"\e6ae";
}
.payMain .payMethodList .payMethod .checkbox  .icon-selected:before{
content:"\e6ba";
}
#paymenting  .payMain .payMethodList .payMethod .icon .icon-iconfontweixin:before{
content:"\e668";
color:#08ba06;
font-size:2.2em;
}

#paymenting .payMain .payPrice{
margin:0 4%;
}

#paymenting .payMain .payBtn{
width:92%;
margin:0 4%;
display:table;
background-color:#08BA06;
border-radius:4px;
font-size:18px;
color:#FFF;
text-align:center;
cursor:pointer;
height:45px;
line-height:45px;

}
</style>