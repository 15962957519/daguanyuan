<template>
    <div id="number">
        <div class="fixednumMask"></div>
       <div class="fixednumMain" >
            <div class="tipBanner">
                        <span class="title">领先价</span>
                        <div class="last">元</div>
                        <div class="close"></div>
            </div>
            <div class="priceBanner">
                        <span class="title">出价</span>
                        <div class="editTxt" lastnum="0" type="0" fixedprice="0"  data-good_id="0"  ever_add_price="0" bidmoney="0" bidbzj="0" multiwins="1"><span class="hover">0</span></div>
                        <div class="tips"><div class="clearPrice"></div></div>
                    </div>
            <div class="btnBanner">
                        <div  @click.stop="confirmBtn($event)" class="confirmBtn">出价</div>
                    </div>
            <div class="numkey">
                <ul>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>1</div> <span></span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>2</div> <span>ABC</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>3</div> <span>DEF</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>4</div> <span>GHI</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>5</div> <span>JKL</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>6</div> <span>MNO</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>7</div> <span>PQRS</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>8</div> <span>TUV</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>9</div> <span>WXYZ</span></li>
                    <li class="num othernum" style="background-color: rgb(209, 213, 218);">00</li>
                    <li class="num" style="line-height: 50px; background-color: rgb(255, 255, 255);">0</li>
                    <li class="delete" style="background-color: rgb(209, 213, 218);"></li>
                </ul>
            </div>
        </div>

            <div class="paymengbid">
                    <div class="fixednumMask"></div>
                    <div class="content">
                      <div class="tip">
                           <div class="msg">出价需先支付100元保证金</div>
                      </div>
                      <div class="button">
                        <button @click.stop="paymentbid($event)"   type="button">支付保证金 </button>
                        <button @click.stop="canclepayment($event)" type="button">取消</button>
                        </div>
                    </div>
            </div>
    </div>
</template>
<script>
    var config = require('../../../config')
  import {rmoney,isEmptyObject} from "../../assets/js/common_function.js"
  import {MessageBox,Indicator,Toast } from 'mint-ui';
    export default {
        data(){
            return {

            }
        },
        mounted: function() {
          var _self=this;
                     $(".fixednumMain .tipBanner .close").on("click",function(e){
                        _self.closepayment(e)
                     })
                                             var munberobj =$('.editTxt .hover')
                                         $('#number .priceBanner .clearPrice').on('click',function(e){
                                                        munberobj.html('');
                                        })


                                                                            	$(".fixednumMain .numkey ul li.num").on("touchend click", function(e) {
                                                                            		e.preventDefault();
                                                                            		 e.stopPropagation();
                                                                            		var clickNum = $(this).find("div").html();
                                                                            		if (clickNum == null) {
                                                                            			clickNum = $(this).html();
                                                                            		}

                                                                            		if(clickNum=='00' && munberobj.html()==''){
                                                                                            clickNum='';
                                                                            		}
                                                                            		var content = munberobj.html() + clickNum;
                                                                            		   munberobj.html(content);

                                                                            	});
                                                                            	$(".fixednumMain .numkey ul li.delete").on("touchend click", function(e) {
                                                                                		e.preventDefault();
                                                                                		 e.stopPropagation();
                                                                                		var content = munberobj.html();
                                                                                		var content = content.substr(0, content.length - 1);
                                                                                		munberobj.html(content);

                                                                                	});


            },
            methods:{
                    paymentbid(e){
                       var  $el =$('.priceBanner').find('.editTxt');
                       var good_id = $el.data('good_id')||0;
                       good_id = parseInt(good_id);
                        var lastnum =$el.attr('lastnum')||0;
                        lastnum =parseInt(lastnum);
                       this.$router.push({ path: '/paymenting',query:{lastnum:lastnum,good_id:good_id}})
                       return false;
                    },
                    canclepayment(evt){
                                this.closepayment(evt);
                                this.closepaymenting();
                    },
                    closepaymenting(){
                        $('.paymengbid').hide();
                        $(".fixednumMask").hide().animate({
                            opacity : 0.382
                        }, 100);
                        $(".paymengbid").hide().animate({
                            bottom : '0px'
                        }, 100, 'ease-in-out');
                    },
                    closepayment(evt){
                        evt.preventDefault();
                        evt.stopPropagation();
                        $('.fixednumMain').hide();
                        $(".fixednumMask").hide().animate({
                         opacity : 0
                        }, 100);
                        $(".fixednumMain").hide().animate({
                             bottom : '-410px'
                        }, 100, 'ease-in-out');
                        this.closepaymenting()
                    },
                    directorybidprice(good_id,lastnum,ever_add_price,e){
                                   //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
                                                var dd =new FormData();
                                                var _that=this;
                                                dd.append('token',storeWithExpiration.get('token'))
                                                dd.append('good_id',good_id)
                                                dd.append('bid_price',rmoney(lastnum)+rmoney(ever_add_price))
                                                axios.post('/index/bidproudctbyid', dd)
                                                  .then(function (response) {
                                                        if(response.status==200){
                                                            return response.data;
                                                        }
                                                  }).then(function(json){
                                                            //更新数据
                                                            var cardData =_that.$store.state.cardData;
                                                             var  tmpa =cardData;
                                                            if(cardData.length>0 && !isEmptyObject(json.data)){
                                                                for(var i=0;i<tmpa.length;i++){
                                                                    if( good_id>0 && tmpa[i].goods_id>0  && good_id ==tmpa[i].goods_id){
                                                                        tmpa[i].bidlists =json.data;
                                                                        tmpa[i].lastnum =String(json.lastnum);
                                                                    }
                                                                }
                                                            }
                                                            _that.$store.commit('refreshData',tmpa);
                                                             _that.closepayment(e)

                                                  })
                                                  .catch(function (error) {
                                                    console.log(error);
                                                  });

                    },
                    confirmBtn(e){
                            //检查保证金是否交 100元
                            var _that=this;
                            var $el= $(e.target).parents('.fixednumMain').find('.priceBanner .editTxt');
                            var good_id =   $el.data('good_id');
                            var lastnum =   $el.attr('lastnum');
                            var ever_add_price =   $el.attr('ever_add_price');

                         axios.get( '/user/bondcheck', {
                                        params: {
                                            token: storeWithExpiration.get('token'),
                                            good_id: good_id
                                        }
                                    })
                                    .then(function(response) {
                                    if(response.status=='200'){
                                          return response.data;
                                    }
                            }).then(function(json) {
                            //直接出价
                                if(json.code==2000){
                                        _that.directorybidprice(good_id,lastnum,ever_add_price,e);
                                }else if('5001' ==json.code){
                                            //自己不能给自己出价
                                       MessageBox.alert('自己不能给自己出价!').then(action => {
                                             _that.closepayment(e);
                                                                                       return false;
                                                                                     });
                                }else{
                                                         //支付保证金

                                                                         $('.paymengbid').show();
                                                                        $(".fixednumMask").show().animate({
                                                                        opacity : 0.382
                                                                        }, 100);
                                                                        $(".paymengbid").show().animate({
                                                                        bottom : '0px'
                                                                        }, 100, 'ease-in-out');


                                }
                            }).catch(function(ex) {
                            console.log(ex);
                            });

                            return false;


                    }
            },
            computed:{

            }
    }
</script>

<style scoped>
#number .fixednumMain{
position:fixed;
left:auto;
right:auto;
width:100%;
bottom:0;
background-color:#F0F0F0;
z-index:1999;
color:#000;
display:none;
max-width:640px;
}
#number .fixednumMain .tipBanner{
height:42px;
line-height:42px;
background:#F0F0F0;
border-top:1px solid #cacaca;
font-size:16px;
}
#number .fixednumMain .tipBanner span.title{
font-size:16px;
line-height:42px;
float:left;

min-width:65px;
text-indent:10px;
}
#number .fixednumMain .tipBanner .finish{
font-size:16px;
line-height:42px;
float:right;
min-width:60px;
color:#007aff;
}
 #number   .fixednumMain .tipBanner .close {
        float: right;
        width: 30px;
        height: 30px;
        background-image: url(../../assets/img/close.png);
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 30px;
        overflow:hidden;
        cursor: pointer;
    }
 #number   .fixednumMain .tipBanner .last,
 #number   .fixednumMain .priceBanner .editTxt {
        float: left;
        width: 60%;
        height: 30px;
        line-height: 30px;
        margin: 6px 0;
        color: #7596D9;
        text-indent: 4px;
        overflow: hidden;
        font-size: 20px;
        font-family: "Helvetica neue", Verdana, Geneva, sans-serif;
    }

 #number   .fixednumMain .priceBanner .editTxt span.hover{

border-right:2px solid red;
 }
 #number  .fixednumMain .priceBanner .tips {
        color: #999;
        font-size: 14px;
        margin-right: 10px;
        overflow: hidden;
        height: 42px;
        position: absolute;
        right: 0;
        top: 0
    }
 .fixednumMain .priceBanner .tips .clearPrice {
        width: 20px;
        height: 42px;
        background-image: url(../../assets/img/clearPrice.png);
        background-repeat: no-repeat;
        background-position: center;
        background-size: 16px;
        cursor: pointer;
    }
 .paymengbid{
    display:none;
    background: #fe0100;
    position: fixed;
    left: auto;
    right: auto;
    width: 100%;
    bottom: 0;
    z-index: 1999;
    color: #000;
}
 .paymengbid .content{
 width:100%;
 height:300px;
position:fixed;
bottom:0;
background:#ededed;
 }
  .paymengbid .content .button{
    margin:0 15px;

  }
  .paymengbid .content button:nth-child(1){
    background: #fe0100;
    color:#FFF;
    margin-bottom:26px;
  }
  .paymengbid .content button:nth-child(2){
    background: #ccc;
  }
  .paymengbid .content .tips{
    line-height: 30px;
    padding: 10px 0px;
    font-size: 14px;
    border-radius: 8px;
}
  .paymengbid .content .msg{
    font-size: 18px;
    background-image: url(/static/img/faq.png);
    background-repeat: no-repeat;
    background-position: 10px center;
    background-size: auto 100%;
    line-height: 40px;
    height: 40px;
    text-align: left;
    padding-left: 60px;
    padding-right: 12px;
}

  .paymengbid .content button{
    width: 100%;
    padding:0 2px;
    line-height: 35px;
    font-size: 18px;
    border-radius: 10px;
    border: 1px solid #ccc;
    display:block;
    }
.fixednumMask {
    position: fixed;
    width: 100%;
    height:100%;
    opacity: 0;
    left:0;
    top: 0px;
    bottom: 0px;
    background-color: #000;
    display: none;
    z-index: 999;
}



.fixednumMain .tipBanner {
    height: 42px;
    width: 96%;
    padding: 0 2%;
    background: #F0F0F0;
    border-top: 1px solid #cacaca;
    font-size: 16px;
}

.fixednumMain .tipBanner span.title, .fixednumMain .priceBanner span.title {
    line-height: 42px;
    float: left;
    min-width: 65px
}

.fixednumMain .tipBanner .last, .fixednumMain .priceBanner .editTxt {
    float: left;
    width: 60%;
    height: 30px;
    line-height: 30px;
    margin: 6px 0;
    color: #7596D9;
    text-indent: 4px;
    overflow: hidden;
    font-size: 20px;
    font-family: "Helvetica neue", Verdana, Geneva, sans-serif;
}

.fixednumMain .tipBanner .close {
    float: right;
    width: 28px;
    height: 28px;
    margin: 7px;
    background-image: url(/static/img/close.png);
    background-repeat: no-repeat;
    background-position: center center;
    border-radius: 28px;
    background-color: #d43131;
    background-size: 14px;
    background-image: url(/static/img/close.png);
}

.fixednumMain .priceBanner {
    background: #fff;
    height: 42px;
    line-height: 42px;
    font-size: 16px;
    width: 96%;
    padding: 0 2%;
    position: relative;
}

.fixednumMain .priceBanner .editTxt .hover {
    border-right: 2px solid #fe0100;
}


.numInput .hover{
    border-right: 2px solid #fe0100;

}
.fixednumMain .priceBanner .tips {
    color: #999;
    font-size: 14px;
    margin-right: 10px;
    overflow: hidden;
    height: 42px;
    position: absolute;
    right: 0;
    top: 0
}

.fixednumMain .btnBanner {
    margin: 0 auto;
    width: 96%;
    display: table;
}

.fixednumMain .btnBanner .tip {
    padding: 0 2%;
    width: 96%;
    height: 20px;
    line-height: 20px;
    text-align: right;
}
.fixednumMain .btnBanner .confirmBtn:only-child{
width:100%;
}
.fixednumMain .btnBanner .confirmBtn {
    border-radius: 4px;
    background: #fe0100;
    height: 39px;
    line-height: 39px;
    text-align: center;
    font-size: 18px;
    color: #fff;
    margin: 10px 0;
}




.fixednumMain .btnBanner .fixedPrice {
    background: #fe0100;
    padding: 2.5% 0;
    border-radius: 4px;
    width: 66%;
    text-align: center;
    font-size: 18px;
    color: #fff;
    border: none;
    float: left;
    height: 24px;
    line-height: 24px;
}

.fixednumMain .btnBanner .fixedPrice {
    background: #01B7F0;
    width: 32%;
    float: left;
    margin-right: 2%;
    font-size: 14px;
    height: 24px;
    line-height: 24px;
}

.fixednumMain .numkey {
    width: 100%;
    background: #fff;
    display: table;
}

.fixednumMain .numkey ul {
    padding: 0;
    margin: 0;
}

.fixednumMain .numkey ul, .fixednumMain .numkey li {
    text-decoration: none;
    list-style: none;
    vertical-align: middle;
}

.fixednumMain .numkey ul li {
    width: 33.1%;
    border-bottom: 1px solid #b3b3b3;
    border-right: 1px solid #b3b3b3;
    height: 50px;
    float: left;
    font-size: 28px;
    font-family: "Helvetica neue", Verdana, Geneva, sans-serif;
    text-align: center;
    background: #fff;
}

.fixednumMain .numkey ul li:nth-child(3n) {
    border-right: none;
}

.fixednumMain .numkey ul li.delete {
    background-image: url(../../assets/img/backspace.png);
    background-repeat: no-repeat;
    background-position: center;
    background-size: 30px;
    background-color: #D1D5DA;
}

.fixednumMain .numkey ul li.othernum {
    background-color: #D1D5DA;
    line-height: 50px;
}

.fixednumMain .numkey ul li div {
    color: #000;
    line-height: 32px;
}

.fixednumMain .numkey ul li span {
    position: relative;
    font-size: 12px;
    top: -18px;
    color: #000;
}

</style>