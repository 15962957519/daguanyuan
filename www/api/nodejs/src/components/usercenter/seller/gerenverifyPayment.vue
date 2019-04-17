<template>
<div  id="gerenverifyPayment" class="verifyProcessMain">
        <div class="menuList individual">
                        <div class="menuItem">
                    <a href="javascript:;">个人信息</a>        </div>
                <div class="menuItem arrow">
                    <span class="wptFi fi-stack">
                        <i class="wptFi icon-circle fi-stack-2x"></i>
                        <i class="wptFi icon-arrowright fi-color-fff fi-stack-1x"></i>
                    </span>
                </div>
                <div class="menuItem">
                    <!--<a href="javascript:;">店铺信息</a>       -->
                </div>
                <div class="menuItem arrow">
                    <span class="wptFi fi-stack">
                        <i class="wptFi icon-circle fi-stack-2x"></i>
                        <i class="wptFi icon-arrowright fi-color-fff fi-stack-1x"></i>
                    </span>
                </div>
                <div class="menuItem selected">
                  <router-link  to="/usersellindex/gerenverifyPayment">提交审核 </router-link>
                  </div>
            </div>

            <div id="contentbox">
                <div class="verifyMain">
                    <div class="verifyBox">
                                  <div class="infoItem border horizonBottom">
                                                                <div class="liHead">认证类型</div>

                                                                <div v-if="userinfo.userverfitytype ==1 ">
                                                                          <div class="liContent">个人认证</div>
                                                                </div>
                                                                <div v-else-if="userinfo.userverfitytype ==2" >
                                                                              <div class="liContent">企业认证</div>
                                                                 </div>
                                                                <div v-else>

                                                                </div>



                                                 </div>



                                <div class="infoItem info">
                                                <div class="liHead">姓名</div>
                                                <div class="liContent">{{userinfo.name}}</div>
                                            </div>

                                <div class="infoItem info">
                                                <div class="liHead">联系电话</div>
                                                <div class="liContent">{{userinfo.telephone}}</div>
                                            </div>

                                <div class="infoItem info">
                                                <div class="liHead">身份证号</div>
                                                <div class="liContent">{{userinfo.idcode}}</div>
                                            </div>
                                 <div class="infoItem info">
                                                 <div class="liHead">店铺名称</div>
                                                 <div class="liContent">{{userinfo.name}}</div>
                                             </div>
                           </div>
                       <div class="tips"> <router-link class="next"   to="/usersellindex/individual">返回修改信息 </router-link></div>
                                         <div style="text-align:center;line-height:40px;color: #0000FF;">
                                           <strong>付款后请及时告知客服顾问，加快审核通过时间</strong>
                                         </div>
                                                                        <div class="payBtn">
                                                                             <router-link class="submit"  tag="div"  to="/usersellindex/authentication">支付认证费用并提交审核</router-link>
                                                                         </div>
                    </div>



            </div>


    </div>
</template>
<script>
    var config = require('../../../../config')

    export default {
                        data(){
                                return {
                                        verifyIdCodeFront:'/static/img/verifyIdCodeFront.jpg',
                                        userinfo:{verifyIdcodefront_remote:'',verifyIdcodeback_romote:'',verifyIdcodehold_remote:''}
                                }

                        },
        mounted: function() {
                  this.getuserinfo();

            },
            methods:{
                                getuserinfo(){
                                var that=this;
                                         axios.get( decodeURIComponent(config.dev.env.default_domain_api)+'/user/usergetverfityinfo', {
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
                                                    }

                                                    }).catch(function(ex) {
                                                    console.log(ex);
                                                    });
                                },
            },
            computed:{

            }
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
 .verifyProcessMain .title {
        width: 100%;
        font-size: 20px;
        line-height: 80px;
        text-align: center;
        color: #6A6A6A;
    }

    .verifyProcessMain .verifyContent {
        display: table;
        width: 100%;
        height: auto;
        font-size: 13px;
        color: #888888;
    }

    .verifyProcessMain .verifyContent .process {
        width: 88%;
        margin: 15px 4% 0 8%;
    }

    .verifyProcessMain .verifyContent .process .step {
        position: relative;
        height: 80px;
        line-height: 20px;
        padding-left: 25px;
        border-left: 2px solid #169ADA;
    }

    .verifyProcessMain .verifyContent .process .step:last-child {
        border-left: 2px solid #EFEFF4;
    }

    .verifyProcessMain .verifyContent .process .step .num {
        position: absolute;
        width: 32px;
        height: 32px;
        left: -17px;
        top: -7px;
        border-radius: 16px;
        background-color: #169ADA;
        font-size: 17px;
        color: #FFFFFF;
        line-height: 32px;
        text-align: center;

    }

    .verifyProcessMain .verifyContent .process .step .num .pass {
        width: 18px;
        height: 18px;
        margin-left: 23px;
        margin-top: -17px;
        background-repeat: no-repeat;
        background-size: 18px;
    }

    .verifyProcessMain.re .verifyContent .process .step .num .pass {
        display: block;
    }

    .verifyProcessMain .button button {
        height: 40px;
        width: 90%;
        margin: 10px 5% 0 5%;
        background-color: #03bf06;
        border: 0;
        border-radius: 3px;
        color: #FFFFFF;
        text-align: center;
        line-height: 40px;
        font-size: 16px;
    }

    .verifyProcessMain .button .agreement {
        width: 90%;
        padding: 5px 5% 20px 5%;
        font-size: 12px;
        line-height: 16px;
        text-align: center;
        color: #888888;
    }

    .verifyProcessMain .button .agreement a {
        color: #576B95;
        text-decoration: none;
    }

    .verifyProcessMain.re .button .agreement {
        display: none;
    }

    #gerenverifyPayment #contentbox{
    min-height:834px;
    width:100%;
    }
     #gerenverifyPayment #contentbox .verifyMain .payBtn{
      width:100%;
      padding:20px 0;
      }

       #gerenverifyPayment #contentbox .verifyMain  .tips{
                font-size:14px;
                text-align:right;
                line-height:20px;
                background-color:#eee;
                color:#939393;
                padding:5px 5% 10px 5%;
       }

          #gerenverifyPayment #contentbox .verifyMain .payBtn .submit{
            width:92%;
            height:45px;
            line-height:45px;
            font-size:18px;
            text-align:center;
            background-color:#69c773;
            color:#FFF;
            margin:0 4%;
            cursor:pointer;
            border-radius:4px;
          }
  #gerenverifyPayment #contentbox .verifyMain .verifyBox{
  width:100%;
  display:table;
  }

  #gerenverifyPayment #contentbox .verifyMain .verifyBox{
background-color:#FFF;
margin:20px 0;
  }


  #gerenverifyPayment #contentbox .verifyMain  .infoItem{
            position: relative;
            height: 28px;
            width: 90%;
            margin-left: 5%;
            font-size: 16px;
            line-height: 28px;
            padding: 10px 5% 10px 0;
  }
   #gerenverifyPayment #contentbox .verifyMain  .infoItem{
          padding:5px 5% 5px 0;
   }


 #gerenverifyPayment #contentbox .verifyMain  .infoItem .liHead{
       width:30%;
       float:left;

 }
  #gerenverifyPayment #contentbox .verifyMain  .infoItem .liContent{
      width:70%;
      float:right;
      text-align:right;
         color:#888;
 }

</style>