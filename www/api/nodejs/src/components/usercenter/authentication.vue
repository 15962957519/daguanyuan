<template>
    <div id="paying">
       		<div class="verifyMain">
			<form id="ksksksks">
                <h1><p class="payment-s" >温馨提示：以下为天宝微拍官方指定财务专用微信、银行卡、支付宝收款账号，请勿轻易直接向他人转账或发红包，以免审核不通过。</p></h1>
       			<h1><p class="payment">*选择以下支付方式进行认证支付</p></h1>

       			<mt-navbar class="page-part" v-model="selected">
                  <mt-tab-item id="微信转帐"><span style="font-size:16px;">微信转帐</span></mt-tab-item>
                  <mt-tab-item id="银行转帐"><span style="font-size:16px;">银行转帐</span></mt-tab-item>
                  <!--最新修改-->
                  <mt-tab-item id="支付宝转账"><span style="font-size:16px;">支付宝转账</span></mt-tab-item>
                </mt-navbar>
       			<div style="clear:both"></div>
                 <div class="currentframe">
                      <mt-cell class="page-part" title="当前选中">当前选中支付方式是：{{ selected }}</mt-cell>
                 </div>
                <!-- tab-container -->
                <mt-tab-container v-model="selected">
                  <mt-tab-container-item id="微信转帐">
                        <div class="content">
                            <div style="padding:10px;">
                                    <div v-if="userinfo.price == 99">
                                       <img class="bankimage" src="/static/img/weixin_pay99.png">
                                    </div>
                                    <div v-else-if="userinfo.price == 1698">
                                      <img class="bankimage" src="/static/img/weixin_pay1698.png">
                                    </div>
                                    <div v-else-if="userinfo.price == 2698 ">
                                     <img class="bankimage" src="/static/img/weixin_pay2698.png">
                                    </div> <div v-else-if="userinfo.price == 4698">
                                     <img class="bankimage" src="/static/img/weixin_pay4698.png">
                                    </div>
                                    <div v-else>
                                          <img class="bankimage" src="/static/img/weixin_pay99.png">
                                    </div>




                             </div>
                        <div id="upload1" style="padding:10px;">
                             <div @click="uploadpaychecket($event)"   class="payimage" id="img1" style="background-image:url(/static/img/demo_weixin_99.jpg);height:250px; width:100%;background-repeat:no-repeat;background-position:center;border:1px solid #ccc;border-radius:4px;background-size:100% 100%">							</div>
                            <div style="text-align:center;line-height:40px;color:#FE0100;">
                             <strong>点击上方上传付款凭证</strong></div>
                        </div>
                        <div class="payBtn">
                        <div   @click="uploadphpserver($event)"  id="submit1" class="submit">提交认证信息</div></div>
                        </div>
                  </mt-tab-container-item>
                  <mt-tab-container-item id="银行转帐">
                    <div class="content">
                           						<div style="padding:10px;">
                           							<div class="panel">
                           								<div class="body">
                           									<p style="color:#000;">提醒：为照顾不同会员客户地区银行差异，特为大家准备了公司财务专用收款银行卡，谷旭为公司指定收款财务人员。</p>

                           									<div class="bank">
                           										<p>	银行：中国工商银行<br>
                           											卡号：6222 0210 0113 9336 108<br>
                           											姓名：谷旭<br>金额：{{userinfo.price}} 元
                           										</p>
                           									</div>
                                                            <div class="bank">
                                                                <p>	银行：中国交通银行<br>
                                                                    卡号：6222 6201 1001 7387 730<br>
                                                                    姓名：谷旭<br>金额：{{userinfo.price}} 元
									                          	</p>
                                                            </div>

                                                            <div class="bank">
                                                                <p>	银行：中国农业银行<br>
                                                                    卡号：6228 4812 3175 8196 711<br>
                                                                    姓名：谷旭<br>金额：{{userinfo.price}} 元
									                          	</p>
                                                            </div>

                                                            <div class="bank">
                                                                <p>	银行：中国建设银行<br>
                                                                    卡号：6217 0012 1009 0215 268 <br>
                                                                    姓名：谷旭<br>金额：{{userinfo.price}} 元
									                          	</p>
                                                            </div>

                                                            <div class="bank">
                                                                <p>	银行：中国邮政储蓄<br>
                                                                    卡号：6217 9929 0003 5775 357 <br>
                                                                    姓名：谷旭<br>金额：{{userinfo.price}} 元
									                           	</p>
                                                            </div>

                                                            <div class="bank">
                                                                <p>	银行：上海农商银行（农村信用社）<br>
                                                                    卡号：6231 6260 3102 6675 078 <br>
                                                                    姓名：谷旭<br>金额：{{userinfo.price}} 元
									                         	</p>
                                                            </div>
                           								</div>
                           							</div>
                           						</div>
                           						<div id="upload2" style="padding:10px;">
                           								<div  @click="uploadpaychecket($event)" class="payimage" id="img2"></div>
                           							    <div class="goodsImgList">
                                                            <div  v-for="(item, index)  in newedimage"><img  class="img" :src="item" width="100%"></div>
                                                        </div>


                           								<div style="text-align:center;line-height:40px;color:#FE0100;">
                           									<strong>点击上方上传付款凭证</strong></div>
                           						</div>
                           							<div @click="uploadphpserver($event)" class="payBtn"><div id="submit2" class="submit">提交认证信息</div>	</div>
                           						</div>
                  </mt-tab-container-item>
                    <!--最新修改-->
                      <mt-tab-container-item id="支付宝转账">
                     	<div class="content">
                         		<div class="panel">
									<div class="body">
                                        <p style="color:#000;padding-left:4%;">支付宝企业收款账号：tianbaoweipai@163.com</p>
                                        <p style="color:#fe0100;padding-left:4%;">操作方法：截图并保存本页面，再打开支付宝软件识别相册中的该二维码，转账输入相应的会员级别金额即可。</p>
                                        <p style="color:#000;padding-left:4%;">支付宝企业收款账号二维码：</p>
                                        <div style="padding:10px;">
                                            <div>
                                                <img class="bankimage" src="/static/img/zfb.png">
                                            </div>
                                        </div>
										<div class="payBtn" style="background-color:#FFF;padding:0">
											<div class="submit" id="submit4" style="background-color:#3dbb2b;display:none;">微信在线支付</div>
										</div>
										<div @click="uploadphpserver($event)" class="payBtn" style="background-color:#FFF;padding-bottom:0">
											<div class="submit" id="submit5">提交认证信息</div>
										</div>
									</div>
                         		</div>
						</div>


                         					<input style="visibility:hidden" type="hiden" name="paypricetoserver" :value="userinfo.price" />
                         					<input style="visibility:hidden" type="hiden" name="pingzhengid" :value="userinfo.pingzhengid" />
                        </mt-tab-container-item>
                </mt-tab-container>
				</form>
			</div>

<!--这个去掉了-->
        <!--<copyright></copyright>-->
    </div>
</template>
<script>
//var copyright  = require('../copyright.vue');
import { MessageBox,Navbar,Tabbar, TabItem,Indicator } from 'mint-ui';
import { TabContainer, TabContainerItem } from 'mint-ui';
Vue.component(TabContainer.name, TabContainer);
Vue.component(TabContainerItem.name, TabContainerItem);
Vue.component(Navbar.name, Navbar);
Vue.component(TabItem.name, TabItem);
Vue.component(Tabbar.name, Tabbar);
var config = require('../../../config')
    export default {
        data(){
            return {
                 selected: '微信转帐',
                 userinfo: {name:'',price:'0',pingzhengid:0},
                 newedimage:[],
                 imagepayfor:false,
                 pingzhengid:0
            }
        },
          watch: {
            // 如果 question 发生改变，这个函数就会运行
            question: function (newQuestion) {

            }
          },
          components:{
//                    'copyright':copyright
            },
        mounted: function() {
                this.userinfo.price=99.00;
                this.weixin()
            },
            methods:{
                 weixin:function(){
                    var that =this;
                        // Optionally the request above could also be done as
                                axios.get('/makesign', {
                                    params: {
                                        url: encodeURIComponent(location.href.split('#')[0])
                                    }
                                }).then(function (response) {
                                            window.wx && wx.config({
                                                debug: false,
                                                appId: "wxdabbc66245346dda",
                                                timestamp: String(response.data.timestamp),
                                                nonceStr: response.data.noncestr,
                                                signature: response.data.signature,
                                                jsApiList: [
                                                    'chooseImage',
                                                    'onMenuShareTimeline',
                                                    'onMenuShareAppMessage',
                                                    'onMenuShareQQ',
                                                    'onMenuShareWeibo',
                                                    'onMenuShareQZone',
                                                    "showOptionMenu",
                                                    "closeWindow",
                                                    "uploadImage",
                                                    "previewImage"
                                                ]
                                            });
                                        }).then(function(){
                                               //   that.uploadflag=false;
                                        }) .catch(function (error) {
                                            console.log(error);
                                        });

                    },
                    uploadpaychecket(){
                        var _that=this;
                        window.wx.chooseImage({
                        count: 3, // 默认9
                        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                        success: function (res) {
                        _that.needimage =res.localIds;
                        var images = {
                            localId: [],
                            serverId: []
                        };
                     images.localId = res.localIds;
                     var i = 0,length = images.localId.length;
                                        function upload(){
                                                                                                         wx.uploadImage({
                                                                                                           localId: images.localId[i],
                                                                                                            isShowProgressTips: 1,
                                                                                                           success: function (res) {
                                                                                                             i++;
                                                                                                             //alert('已上传：' + i + '/' + length);
                                                                                                             images.serverId.push(res.serverId);

                                                                                                             if (i < length) {
                                                                                                               upload();
                                                                                                             }else{
                                                                                                            //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
                                                                                                                var formElement = document.querySelector("#ksksksks");
                                                                                                                var dd =new FormData(formElement);
                                                                                                                dd.append('token',storeWithExpiration.get('token'))
                                                                                                                     dd.append('MEDIA_ID', images.serverId)
                                                                                                                //提交到后台
                                                                                                                Indicator.open({
                                                                                                                  text: '正在提交，请稍后...',
                                                                                                                  spinnerType: 'fading-circle'
                                                                                                                });
                                                                                                                axios.post('/useruploadfinance/loading',dd)
                                                                                                                .then(function (response) {
                                                                                                                         Indicator.close();
                                                                                                                    _that.userinfo.pingzhengid =     response.data.data;
                                                                                                                         MessageBox.alert("凭证上传成功，请提交信息").then(action => {
                                                                                                                                   _that.imagepayfor=true;
                                                                                                                          });
                                                                                                                        }
                                                                                                                     )
                                                                                                                 .then(action => {

                                                                                                                     })
                                                                                                                    .catch(function (error) {

                                                                                                                     });
                                                                                                             }
                                                                                                           },
                                                                                                           fail: function (res) {
                                                                                                             alert(JSON.stringify(res));
                                                                                                             return false;
                                                                                                           }
                                                                                                         });
                                                                                                       }
                                                                                              upload();
                            }
                            });
                    },
                        uploadphpserver(){
                                var _that =this;
                                if(this.imagepayfor==false){
                                  MessageBox.alert("请上传图片").then(action => {

                                        });
                                return false;
                                }



                                                          //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
                                                            var formElement = document.querySelector("#ksksksks");
                                                            var dd =new FormData(formElement);
                                                            dd.append('token',storeWithExpiration.get('token'))
                                                            //提交到后台
                                                            Indicator.open({
                                                              text: '正在提交，请稍后...',
                                                              spinnerType: 'fading-circle'
                                                            });
                                                            axios.post('/useruploadfinance/save',dd)
                                                            .then(function (response) {
                                                                 Indicator.close();
                                                                 MessageBox.alert("提交成功，请联系客服").then(action => {
                                                                         _that.$router.push({ path: '/'})
                                                                 });

                                                            console.log(response);
                                                            })
                                                            .catch(function (error) {

                                                            });

                                },
            },
            computed:{

            }
    }
</script>

<style scoped>
a{
text-decoration:none;
}
#paying .verifyMain{
background:#fff;
overflow:hidden;
}
#paying .verifyMain .verifyBox{
display:table;
width:100%;
padding:10px 0;
margin-bottom:10px;
}

#paying .verifyMain .verifyBox .infoItem {
padding: 5px 5% 5px 0;
height: 30px;
line-height: 30px;
border-bottom:1px solid #EAEAE8;
font-size:15px;
margin-left:5%;
width:90%;
}
#paying .verifyMain .verifyBox .infoItem .first {
    width: 30%;
    float: left;
}
#paying .verifyMain .verifyBox .infoItem .last {
    width: 70%;
    text-align:left;
    float: left;
}
#paying  .content div:nth-child(1){
margin-top:10px;
}
#paying  .content{
width:100%;
background-color:#fff;
display:table;
}
#paying  .content .bankimage{
width:100%;
border:0;

}
#paying  .content .payimage{
background-image:url(/static/img/pay_bank_emample.jpg);
height:250px;
width:100%;
background-repeat:
no-repeat;
background-position:center;
border:1px solid #ccc;
border-radius:4px;
background-size:100% 100%
}
#paying .payment-s{
    padding:0 5%;
    margin-bottom: 18px;
    line-height:23px;
    color:#fe0100;
    font-size:14px;
}

#paying .payment{
    margin-bottom: 0px;
}

#paying  .content .bank{
border: 1px solid #eee;
padding: 10px;
margin-bottom: 20px;
position: relative;
}
#paying  .content .submit{
width:96%;
margin:0 2%;
background-color:#fe0100;
border-radius:4px;
height:48px;
line-height:48px;
text-align:center;
font-size:18px;
color:#FFF;
}

#paying  .body p{
    /*line-height:40px;*/
line-height:28px;
text-algin:left;
}
#paying  .currentframe{
line-height: 30px;
padding: 0 8%;
border-bottom: 1px solid #ccc;
}
</style>