<template>
<div id="gerenverifyProcess_individual">
<div class="menuList individual">
                <div class="menuItem selected">
            <a href="/myVerify/verifyInfo/operator/individual">个人信息</a>        </div>
        <div class="menuItem arrow">
            <span class="wptFi fi-stack">
                <i class="wptFi icon-circle fi-stack-2x"></i>
                <i class="wptFi icon-arrowright fi-color-fff fi-stack-1x"></i>
            </span>
        </div>
        <!--<div class="menuItem">-->
            <!--店铺信息        </div>-->
     <div class="menuItem">
     </div>
        <div class="menuItem arrow">
            <span class="wptFi fi-stack">
                <i class="wptFi icon-circle fi-stack-2x"></i>
                <i class="wptFi icon-arrowright fi-color-fff fi-stack-1x"></i>
            </span>
        </div>
        <div class="menuItem">
            提交审核        </div>
    </div>

  <div id="contentbox">
      <div class="verifyMain operator individual">
      <form id="shimingrenzheng" >
          <div class="verifyBox operator">
              <div class="title">
                  实名认证
                  <span>(请上传真实的个人信息，认证通过后将无法修改)</span>

              </div>
              <div class="infoItem text border horizonBottom">
                  <div class="liHead">姓名</div>
                  <div class="liContent">
                      <input type="text" class="input" name="name" placeholder="请输入真实的姓名"  v-model="userinfo.name">
                  </div>
                  <div class="liFoot"></div>
              </div>
              <div class="infoItem text border horizonBottom">
                  <div class="liHead">身份证/护照</div>
                  <div class="liContent">
                      <input type="text" class="input" name="idcode" placeholder="请输入身份证/护照号" maxlength="21" v-model="userinfo.idcode">
                  </div>
                  <div class="liFoot"></div>
              </div>
              <div class="infoItem text last">
                  <div class="liHead">联系电话</div>
                  <div class="liContent telephone">
                      <div class="telephone" desc="联系电话" data-telephone="">
                            <input type="text" class="input" name="telephone" placeholder="请输入联系号码" maxlength="11" v-model="userinfo.telephone">
                      </div>
                  </div>
                  <div class="liFoot"></div>
              </div>
              <div class="tips">请填写有效电话，工作人员将会致电核实材料</div>
              <div class="tip">(照片资料系统会自动添加水印，请放心上传)</div>
          </div>
    </form>
          <div class="verifyBox operatorPhoto">
              <div class="infoItem photo">
                  <div class="liHead">
                      身份证正面照
                      <p class="">请用手机横向拍摄以保证图片正常显示</p>
                  </div>
                  <div  @click="uploadverifyIdCodeFront('/user/userverfityimgfront',$event)"  class="liContent" serverid="" v-bind:style="{backgroundImage:'url('+userinfo.verifyIdcodefront_remote+')'} ">
                                    <div  v-if="front" class="example">
                                     <div class="examplePhoto">
                                        <img :src="verifyIdCodeFront">
                                     </div>
                                        <div  @click="uploadverifyIdCodeFront('/user/userverfityimgfront',$event)" class="button">点击上传正面照</div>
                                     <p>照片必须清晰哟！</p>
                                    </div>

                  </div>
                  <div class="cover hide"></div>

              </div>
              <div class="infoItem photo">
                  <div class="liHead">
                      身份证反面照
                      <p class="">请用手机横向拍摄以保证图片正常显示</p>
                  </div>

                  <div @click="uploadverifyIdCodeFront('/user/userverfityimgback',$event)" class="liContent" serverid="" v-bind:style="{backgroundImage:'url('+userinfo.verifyIdcodeback_romote+')'}">
                            <div v-if="userinfo.verifyIdcodeback_romote =='' ">
                                          <div  v-if="back" class="example">
                          <div class="examplePhoto">
                              <img src="/static/img/verifyIdCodeBack.jpg">
                          </div>
                          <div @click="uploadverifyIdCodeFront('/user/userverfityimgback',$event)" class="button">点击上传反面照</div>
                          <p>照片必须清晰哟！</p>
                      </div>
                                      </div>
                                      </div>
                  <div class="cover hide"></div>
              </div>
              <div class="infoItem photo last">
                  <div class="liHead">
                      手持身份证照
                      <p class="">拍照时请对焦在证件上(屏幕上对着身份证按一下)</p>
                  </div>
                  <div  @click="uploadverifyIdCodeFront('user/userverfityimghold',$event)" class="liContent hold" serverid="" v-bind:style="{backgroundImage:'url('+userinfo.verifyIdcodehold_remote+')'}">
                                 <div v-if="userinfo.verifyIdcodehold_remote =='' ">

                                          <div v-if="hold" class="example">
                          <div class="examplePhoto">
                              <img src="/static/img/verifyIdCodeHold.jpg">
                          </div>
                          <div @click="uploadverifyIdCodeFront('user/userverfityimghold',$event)" class="button">点击上传手持身份证照</div>
                          <p>请严格按照范例姿势拍照，否则不会通过审核！</p>
                      </div>
                                      </div>
                                      </div>
                  <div class="cover hide"></div>
              </div>
              <div class="tips confirm">
                  <div>请确认以上信息准确无误</div>
              </div>
          </div>

          <div class="buttonBanner">
           <button @click.stop="next('/user/userverfityimgsave',$event)" class="next">下一步</button>
          </div>
      </div>
  </div>
    </div>
</template>
<script type="text/babel">
var config = require('../../../../config')
import { mapState } from 'vuex';
import {MessageBox,Navbar, Tabbar,TabItem,Switch,Indicator } from 'mint-ui';
import {weixincommonjsdk,weixin} from "../../../assets/js/common_function.js"
Vue.component(Switch.name, Switch);
Vue.component(TabItem.name, TabItem);
Vue.component(Navbar.name, Navbar);


    export default {
        data(){
                return {
                        verifyIdCodeFront:'/static/img/verifyIdCodeFront.jpg',
                        userinfo:{verifyIdcodefront_remote:'',verifyIdcodeback_romote:'',verifyIdcodehold_remote:''},
                        front:true,
                        back:true,
                        hold:true
                }

        },
        mounted: function() {
                 //   this.$store.dispatch('weixin',this);
                    weixin();
                    this.getuserinfo();
            },
            methods:{
                getuserinfo(){
                var that=this;
                         axios.get('/user/usergetverfityinfo', {
                                                                                params: {
                                                                                    token: storeWithExpiration.get('token')
                                                                                }
                                                                            })
                                                                            .then(function(response) {
                                                                            if(response.status=='200'){
                                                                                  return response.data;
                                                                            }

                                        			}).then(function(json) {
                                   if(typeof(json.data)!="undefined"){
                                                that.userinfo = json.data;

                                            if( typeof(that.userinfo.verifyIdcodefront_remote) !="undefined" && that.userinfo.verifyIdcodefront_remote!=''  && that.userinfo.verifyIdcodefront_remote !== null){
                                             that.front =false
                                            }else{
                                                    that.userinfo.verifyIdcodefront_remote ='';
                                            }

                                                if( typeof(that.userinfo.verifyIdcodeback_romote) !="undefined" && that.userinfo.verifyIdcodeback_romote!=''&& that.userinfo.verifyIdcodeback_romote !== null){
                                                      that.back =false
                                                }else{
                                                that.userinfo.verifyIdcodeback_romote ='';
                                                }


    if( typeof(that.userinfo.verifyIdcodehold_remote) !="undefined" && that.userinfo.verifyIdcodehold_remote!='' && that.userinfo.verifyIdcodehold_remote !== null){

                                                      that.hold =false

                                                }else{
                                                                                                                                                   that.userinfo.verifyIdcodehold_remote ='';
                                                                                                                                                 }
                                    }

                                    }).catch(function(ex) {
                                    console.log(ex);
                                    });
                },
            next(url,e){
            var _that=this;
                    this.shimingrenzheng('/user/userverfityimgsave','/usersellindex/gerenverifyPayment',e);
                     return false;

            },
            shimingrenzheng(url,neturl,e){
                        var _that =this;
                        var ksksskk =document.getElementById('shimingrenzheng');
                        var dd =new FormData(ksksskk);
                        dd.append('token',storeWithExpiration.get('token'))
                        Indicator.open({
                        text: '正在转到下一步，请稍后...',
                        spinnerType: 'fading-circle'
                        });
                    axios.post(url,dd,{timeout:10000})
                            .then(function (response) {
                                 Indicator.close();
                                 MessageBox.alert("跳转成功").then(action => {
                                             _that.$router.push({ path: neturl})

                                 });
                            })
                            .catch(function (error) {
                              MessageBox.alert("网络问题，请稍后重试！").then(action => {
                                                                                        return false;
                                                                            });
                              Indicator.close();

                            });
            },
            uploadverifyIdCodeFront(url,e){
                        e.preventDefault();
                        e.stopPropagation();
                        var _that=this;
                        var images = {
                             localId: [],
                             serverId: []
                        };
                                window.wx.chooseImage({
                                        count: 1, // 默认9
                                        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                                        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                                        success: function (res) {
                                         images.localId = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片

                                             var i = 0, length = images.localId.length;
                                                                function upload(){
                                                                              wx.uploadImage({
                                                                                localId: images.localId[i],
                                                                                isShowProgressTips: 1,
                                                                                success: function (res) {
                                                                                  i++;
                                                                                  images.serverId.push(res.serverId);
                                                                                  if (i < length) {
                                                                                    upload();
                                                                                  }else{

                                                                                    _that.uploadphpserver(images,url,e)

                                                                                  }
                                                                                },
                                                                                fail: function (res) {

                                                                                  return false;
                                                                                }
                                                                              });
                                                                            }
                                                                   upload();
                                        }
                                        });

                },
                uploadphpserver(images,url,e){
                                                          var _that =this;
                                                               //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
                                                            var dd =new FormData();
                                                            dd.append('token',storeWithExpiration.get('token'))
                                                            dd.append('MEDIA_ID',images.serverId)
                                                            Indicator.open({
                                                              text: '文件正在上传远程服务器，请稍后...',
                                                              spinnerType: 'fading-circle'
                                                            });
                                                            axios.post(url,dd,{timeout:10000})
                                                            .then(function(response){
                                                                    if(response.status=='200'){
                                                                     return response.data
                                                                    }

                                                            })
                                                            .then(function (response) {
                                                                 Indicator.close();
                                                                 MessageBox.alert("上传成功").then(action => {
                                                                            //verifyIdCodeFront
                                                               var ddd = $(e.target).parent('.example');
                                                                var s =response.data;
                                                                    if( /(userverfityimgfront)/.test(url)){
                                                                          _that.front=false;
                                                                       _that.userinfo.verifyIdcodefront_remote=s;
                                                                    }else if( /(userverfityimgback)/.test(url)){
                                                                      _that.back=false;
                                                                       _that.userinfo.verifyIdcodeback_romote=s;
                                                                    }else if( /(userverfityimghold)/.test(url)){
                                                                      _that.hold=false;
                                                                          _that.userinfo.verifyIdcodehold_remote=s;
                                                                    }
                                                                 });
                                                            })
                                                            .catch(function (error) {
                                                              MessageBox.alert("网络问题，请稍后重试！").then(action => {
                                                                                                                        return false;
                                                                                                            });
                                                                                                              Indicator.close();

                                                            });





                }
            },
            computed:mapState({




            })

            }
</script>
<style scoped>
 @import '../../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
 .menuList {
        width: 100%;
        height: 60px;
        background-color: #169ADA;
    }

    .menuList .menuItem {
        position: relative;
        width: 24%;
        height: 100%;
        float: left;
        color: #99CFEE;
        font-size: 16px;
        line-height: 60px;
        text-align: center;
    }

    .menuList.business .menuItem {
        width: 25%;
    }

    .menuList .menuItem a {
        display: block;
        text-decoration: none;
        color: #FFF;
    }

    .menuList .menuItem.selected:after {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        top: 55px;
        left: 50%;
        margin-left: -5px;
        background-color: #169ADA;
        transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
    }

    .menuList .arrow {
        width: 14%;
        height: 100%;
        color: #0072A9;
        font-size: 9px;
    }

    .menuList.business .menuItem.arrow {
        width: 12px;
        margin: 0 -6px;
    }

    .verifyMain .verifyBox {
        width: 100%;
        display: table;
    }
    .verifyMain .verifyBox.operator {
     background-color:#FFF;
    }

    .verifyMain .verifyBox .title {
        display: block;
        padding: 14px 5% 8px 5%;
        background-color: #EEE;
        color: #888;
        line-height: 20px;
        font-size: 14px;
    }

    .verifyMain .verifyBox .title span {
        font-size: 10px;
    }

    .verifyMain .verifyBox .infoItem {
        position: relative;
        height: 28px;
        width: 90%;
        margin-left: 5%;
        font-size: 16px;
        line-height: 28px;
        padding: 10px 5% 10px 0;
    }

    .verifyMain .verifyBox .infoItem .telephone span.verifyed:after {
        content: "号码已验证";
        position: absolute;
        right: 15px;
        font-size: 12px;
        color: #CCC;
    }

    .verifyMain .verifyBox .infoItem .liHead {
        width: 30%;
        float: left;
    }

    .verifyMain .verifyBox .infoItem .liContent {
        width: 60%;
        float: left;
    }

    .verifyMain .verifyBox .infoItem .liFoot {
        width: 10%;
        height: 28px;
        float: left;
    }

    .verifyMain .verifyBox .infoItem .liFoot.error:after {
        content: '';
        width: 100%;
        height: 100%;
        float: right;
        background-image: url("/res/img/verifyWarnIcon.png");
        background-repeat: no-repeat;
        background-size: 15px;
        background-position: center center;
    }

    .verifyMain .verifyBox .infoItem .liContent .input,
    .verifyMain .verifyBox .infoItem .liContent .numInput {
        border: 0;
        font-size: 14px;
        height: 28px;
        float: left;
        width: 100%;
    }

    .verifyMain .verifyBox .infoItem .liContent .numInput span {
        line-height: 28px;
        height: 28px;
        color: #A9A9A9;
    }

    .verifyMain .verifyBox .infoItem .liContent .numInput span.hover {
        border-right: 2px solid red;
    }

    .verifyMain .verifyBox .infoItem .liContent .numInput span.hasValue {
        color: #000;
    }

    .verifyMain .verifyBox .tips {
        padding: 5px 5% 10px 5%;
        color: #939393;
        background-color: #EEE;
        line-height: 20px;
        font-size: 12px;
        /*text-align: right;*/
    }

 .verifyMain .verifyBox .tip {
     padding: 5px 5% 5px 5%;
     color: #f10000;
     background-color: #EEE;
     font-size: 12px;
 }

    .verifyMain .verifyBox .tips.verified {
        color: #169ADA;
    }

    .verifyMain .verifyBox .tips.verifyError {
        color: #F94A45;
    }

    .verifyMain .verifyBox .tips.confirm {
        position: relative;
        width: 90%;
        height: 1px;
        padding: 0;
        border: 0;
        margin: 25px 5%;
        background-color: #AAA;
    }

    .verifyMain .verifyBox .tips.confirm div {
        position: absolute;
        width: 48%;
        left: 26%;
        margin-top: -6px;
        font-size: 12px;
        line-height: 12px;
        text-align: center;
        background-color: #EEE;
    }

    .verifyMain .verifyBox .infoItem.photo {
        background-color: #FFF;
        height: 290px;
        padding: 12px 5%;
        margin: 0 0 10px;
        position: relative;
    }
.verifyMain .verifyBox .infoItem{
    -moz-box-sizing: content-box;
    -webkit-box-sizing: content-box;
    -o-box-sizing: content-box;
    -ms-box-sizing: content-box;
    box-sizing: content-box;
}



    .verifyMain .verifyBox .infoItem.photo.last .liContent {
        background-size: cover;
    }

    .verifyMain .verifyBox .infoItem.photo .liHead {
        position: relative;
        border: 0;
        padding: 0;
        width: 100%;
        font-size: 16px;
        line-height: 16px;
        background-color: #FFF;
    }

    .verifyMain .verifyBox .infoItem.photo .liHead p {
        line-height: 25px;
        font-size: 12px;
        padding-left: 16px;
        color: #169ADA;
        font-weight: 100;
        background-image: url("/res/img/attention.png");
        background-repeat: no-repeat;
        background-position: 0 45%;
        background-size: 12px;
    }

    .verifyMain .verifyBox .infoItem.photo .liHead p.error {
        color: #FF4949;
        background-image: url("/res/img/verifyWarnIcon.png");
    }

    .verifyMain .verifyBox .infoItem.photo .liHead p.error a {
        color: #169ADA;
        text-decoration: underline;
    }

    .verifyMain .verifyBox .infoItem.photo .liContent {
        width: 100%;
        height: 250px;
        cursor: pointer;
        overflow: hidden;
        border-radius: 3px;
        background-color: #DDD;
        background-size: 100% auto;
        background-position: center;
        background-repeat: no-repeat;
    }

    .verifyMain .verifyBox .infoItem.photo .cover {
        position: absolute;
        top: 53px;
        width: 90%;
        height: 250px;
        overflow: hidden;
        cursor: pointer;
        background-position: center;
        background-repeat: no-repeat;
        background-image: url(/res/img/cover.png);
        background-size: contain;
        background-color: transparent;
        z-index: 999;
    }

    .verifyMain .verifyBox .infoItem.photo .liContent .example {
        background-color: #DDD;
        width: 160px;
        margin: 40px auto;
    }

    .verifyMain .verifyBox .infoItem.photo .liContent .example .examplePhoto {
        width: 100%;
        height: 120px;
        border-radius: 4px;
        overflow: hidden;
    }

    .verifyMain .verifyBox .infoItem.photo .liContent .example .examplePhoto img {
        width: 100%;
        border-radius: 6px;
        display: block;
    }

    .verifyMain .verifyBox .infoItem.photo .liContent .example .button {
        background-color: #169ADA;
        width: 100%;
        height: 30px;
        margin: 9px 0;
        color: #FFF;
        text-align: center;
        font-size: 14px;
        border-radius: 2px;
    }

    .verifyMain .verifyBox .infoItem.photo .liContent .example p {
        color: #666666;
        text-align: center;
        line-height: 16px;
        font-size: 12px;
    }

    .verifyMain .buttonBanner button{
        border: 0;
        width: 90%;
        color: #FFF;
        height: 42px;
        font-size: 16px;
        line-height: 42px;
        text-align: center;
        border-radius: 3px;
        margin: 5px 5% 15px 5%;
        background-color: #06BC07;
    }

    .hide {
        display: none;
    }
    .verifyTelephoneMain {
        position: fixed;
        bottom: -410px;
        width: 100%;
        max-width: 640px;
        display: none;
        z-index: 1200;
    }

    .verifyTelephoneMain .wptMask{
        position: fixed;
        width: 100%;
        opacity: .4;
        top: 0px;
        bottom: 0px;
        background-color: #000;
        z-index: 1200;
    }

    .verifyTelephoneMain .verifyMain{
        position: absolute;
        bottom: 0px;
        width: 100%;
        background: #F0F0F0;
        z-index: 1200;
    }

    .verifyTelephoneMain .telephoneBox,
    .verifyTelephoneMain .secretCodeBox{
        margin: 0 auto;
        width: 92%;
        background: #F0F0F0;
        font-size: 14px;
    }

    .verifyTelephoneMain .telephoneBox .title,
    .verifyTelephoneMain .secretCodeBox .title{
        height: 50px;
        line-height: 50px;
    }

    .verifyTelephoneMain .secretCodeBox{
        width: 100%;
    }

    .verifyTelephoneMain .secretCodeBox span{
        color:#9a0006;
    }

    .verifyTelephoneMain .telephoneBox .telephoneInput,
    .verifyTelephoneMain .secretCodeBox .secretCodeInput {
        background: #fff;
        height: 45px;
        line-height: 45px;
        font-size: 16px;
        margin: 0 auto;
        width: 92%;
        position: relative;
        overflow: hidden;
        border-radius: 4px;
        text-indent: 10px;
    }

    .verifyTelephoneMain .secretCodeBox .callNotice {
        line-height: 12px;
        font-size: 12px;
        color: #878787;
        padding-bottom: 10px;
        display: none;
    }

    .verifyTelephoneMain .secretCodeBox .callNotice span {
        color: red;
    }

    .verifyTelephoneMain .telephoneBox .telephoneInput {
        width: 100%;
    }

    .verifyTelephoneMain .telephoneBox .telephoneInput span:after {
        content: "输入手机号码";
        color: #888;
        font-size: 14px;
    }

    .verifyTelephoneMain .telephoneBox .telephoneInput span.hasValue:after {
        content: "";
    }

    .verifyTelephoneMain .secretCodeBox .secretCodeInput span:after {
        content: "验证码";
        color: #888;
        font-size: 14px;
    }

    .verifyTelephoneMain .secretCodeBox .secretCodeInput span.hasValue:after {
        content: "";
    }

    .verifyTelephoneMain .secretCodeBox {
        margin: 0 auto;
        width: 92%;
        background: #F0F0F0;
        font-size: 16px;
        display: none;
    }

    .verifyTelephoneMain .secretCode {
        width: 100%;
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
    }

    .verifyTelephoneMain .secretCodeBox .secretCode .secretCodeInput {
        width: 200px;
    }

    .verifyTelephoneMain .telephoneInput .hover,
    .verifyTelephoneMain .secretCodeBox .secretCodeInput .hover {
        border-left: 2px solid #169BD9;
    }

    .verifyTelephoneMain .telephoneInput .hover.hasValue,
    .verifyTelephoneMain .secretCodeBox .secretCodeInput .hover.hasValue {
        border-right: 2px solid #169BD9;
        border-left: none;
    }

    .checkBtnBanner {
        width: 100%;
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
    }

    .verifyTelephoneMain .secretCode .callCheck,
    .verifyTelephoneMain .secretCode .smsCheck {
        width: 100%;
        background: #06BE04;
        text-align: center;
        font-size: 15px;
        color: #fff;
        line-height: 45px;
        height: 45px;
        border-radius: 4px;
    }

    .verifyTelephoneMain .secretCode .callCheck.keyDown,
    .verifyTelephoneMain .secretCode .smsCheck.keyDown {
        background-color: #D9D9D9;
    }

    .verifyTelephoneMain .secretCode .callCheck {
        background-color: #169BD9;
    }

    .verifyTelephoneMain .secretCode .checkBtnBanner .callCheck,
    .verifyTelephoneMain .secretCode .checkBtnBanner .smsCheck {
        flex: auto;
        display: none;
        margin-left: 4%;
    }

    .verifyTelephoneMain .secretCode .checkBtnBanner .callCheck.show,
    .verifyTelephoneMain .secretCode .checkBtnBanner .smsCheck.show {
        display: block;
    }

    .verifyTelephoneMain .btnBanner {
        margin: 0 auto;
        width: 92%;
        padding: 0px 0px 15px;
        display: table;
    }

    .verifyTelephoneMain .btnBanner .confirmBtn {
        background-color: #06BE04;
        border-radius: 4px;
        text-align: center;
        font-size: 18px;
        color: #fff;
        border: none;
        float: left;
        height: 45px;
        line-height: 45px;
        width: 100%;
        cursor: pointer;
    }

    .verifyTelephoneMain .tips {
        height: 45px;
        line-height: 45px;
        margin: 0 auto;
        width: 92%;
        font-size: 12px;
        display: table;
    }

    .verifyTelephoneMain .tips .checkResult {
        background-image: url(/res/img/warning.png);
        background-size: 14px;
        background-position: left center;
        background-repeat: no-repeat;
        padding-left: 18px;
        color:#D70000;
        float: left;
    }

    .verifyTelephoneMain .tips .tipText {
        float: right;
        color: #878787;
    }

    .verifyTelephoneMain .tips .tipText a {
        color: #169db9;
        padding: 0 2px;
    }

    .verifyTelephoneMain .numkey {
        width: 100%;
        background: #fff;
        display: table;
    }

    .verifyTelephoneMain .numkey ul {
        padding: 0;
        margin: 0;
    }

    .verifyTelephoneMain .numkey ul,
    .verifyTelephoneMain .numkey li {
        text-decoration: none;
        list-style: none;
        vertical-align: middle;
    }

    .verifyTelephoneMain .numkey ul li {
        width: 33%;
        border-bottom: 1px solid #b3b3b3;
        border-right: 1px solid #b3b3b3;
        box-sizing: border-box;
        height: 50px;
        float: left;
        font-size: 28px;
        font-family: "Helvetica neue", Verdana, Geneva, sans-serif;
        text-align: center;
        background: #FFF;
        cursor: pointer;
        -moz-user-select:none;/*火狐*/
        -webkit-user-select:none;/*webkit浏览器*/
        -ms-user-select:none;/*IE10*/
        -khtml-user-select:none;/*早期浏览器*/
        user-select:none;
    }

    .verifyTelephoneMain .numkey ul li:nth-child(3n) {
        width: 34%;
        border-right: none;
    }

    .verifyTelephoneMain .numkey ul li.delete {
        background-image: url(/res/img/backspace.png);
        background-repeat: no-repeat;
        background-position: center;
        background-size: 30px;
        background-color: #D1D5DA;
    }

    .verifyTelephoneMain .numkey ul li.othernum {
        background-color: #D1D5DA;
        line-height: 50px;
        font-size: 20px;
    }

    .verifyTelephoneMain .numkey ul li div {
        color: #000;
    }

    .verifyTelephoneMain .numkey ul li span {
        position: relative;
        font-size: 12px;
        top: -18px;
        color: #000;
    }

</style>