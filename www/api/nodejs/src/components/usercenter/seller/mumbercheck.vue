<template>
<div  id="mumbercheck" class="verifyMain">
            <div class="verifyBox userInfo">
                <div class="avatar">
                    <div class="img"  v-bind:style="{backgroundImage:'url('+userinfo.head_pic+')'}"></div>
                </div>
                <div class="verifyInfo">
                    <div class="userName" v-cloak >{{userinfo.nickname}}</div>
                    	<div v-if="userinfo.is_authentication ==1">
                               <p>已经认证</p>
                        </div>
                        <div v-else-if="userinfo.is_authentication ==0">
                               <p>立即实名认证享受更多特权服务</p>
                        </div>
                         <div v-else>
                         </div>
                </div>
                <div class="verifyState">
                         <div class="verifyIcon">
                            <i class="tianbaoweipai icon-verifynotfinished"></i>
                        </div>
                                                	<div v-if="userinfo.level >1"></div>
                                                    <div v-else-if="userinfo.level ==1">
                                                        <p class=""></p>
                                                    </div>
                                                    <div v-else>
                                                     <p class=""></p>
                                                    </div>

                                <div class="arrow">
                                    <i class="tianbaoweipai icon-arrowright"></i>
                                </div>
                </div>
            </div>

			<div class="verifyBox apply">
                <div class="title border horizonBottom">申请认证</div>
                       	<div v-if="userinfo.level >1">
                                <div class="applyOptionList">
                                    <div class="applyItem">
                                              <router-link to="/usersellindex/individual">
                                                <div class="applyIcon finished">
                                                <span class="tianbaoweipai fi-stack">
                                                <i class="tianbaoweipai icon-circle-o fi-stack-2x"></i>
                                                <i class="tianbaoweipai icon-my-o fi-stack-1x"></i>
                                                </span>
                                                </div>
                                                <p>个人实名认证</p>
                                                <div class="applyBtn">查看</div>
                                              </router-link>
                                    </div>
                                </div>
                          </div>
                           <div v-else>
                                <div class="applyOptionList">
                                    <div class="applyItem">
                                                      <router-link to="/usersellindex/gerenverifyProcess">
                                                        <div class="applyIcon finished">
                                                        <span class="tianbaoweipai fi-stack">
                                                        <i class="tianbaoweipai icon-circle-o fi-stack-2x"></i>
                                                        <i class="tianbaoweipai icon-my-o fi-stack-1x"></i>
                                                        </span>
                                                        </div>
                                                        <p>个人实名认证</p>
                                                        <div class="applyBtn">点击申请</div>
                                                      </router-link>
                                        </div>
                                       <div class="applyItem">
                                                <router-link to="/usersellindex/companyverifyProcess">
                                                        <div class="applyIcon finished">
                                                            <span class="tianbaoweipai fi-stack">
                                                                <i class="tianbaoweipai icon-circle-o fi-stack-2x"></i>
                                                                <i class="tianbaoweipai icon-shopicon fi-stack-1x"></i>
                                                            </span>
                                                        </div>
                                                        <p>企业实名认证</p>
                                                        <div class="applyBtn">点击申请</div>
                                                    </router-link>
                                                </div>
                                        </div>
                                </div>
            </div>

            <!--这个地方修改了我注释的地方-->
            <div class="tips">认证特权一览</div>
            <div class="verifyBox powerInfo">
                <div class="powerList">
                    <table cellspacing="0">
                        <!--<thead>-->
                            <!--<tr>-->
                                <!--<td>功能权限</td>-->
                                <!--<td>认证前</td>-->
                                <!--<td>认证后</td>-->
                            <!--</tr>-->
                        <!--</thead>-->
                        <tbody>
                            <tr>
                                <td>信用等级高</td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                            </tr>
                            <tr>
                                <td>有专属认证标识</td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                            </tr>
                            <tr>
                                <td>店铺显示：已认证</td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                            </tr>
                            <tr>
                                <td>粉丝放心出价</td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                            </tr>
                            <tr>
                                <td>交易有保障</td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                                <td>
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                </td>
                            </tr>
                            <!--<tr>-->
                                <!--<td>黑名单数量增至320</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                            <!--</tr>-->
                            <!--<tr>-->
                                <!--<td>商品区预展功能</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                            <!--</tr>-->
                            <!--<tr>-->
                                <!--<td>专属认证标记</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                            <!--</tr>-->
                            <!--<tr>-->
                                <!--<td>增加点赞次数</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack rotate">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-addicon fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                                <!--<td>-->
                                    <!--<span class="tianbaoweipai fi-stack">-->
                                        <!--<i class="tianbaoweipai icon-circle fi-stack-2x"></i><i class="tianbaoweipai icon-selected fi-color-fff fi-stack-1x"></i>-->
                                    <!--</span>-->
                                <!--</td>-->
                            <!--</tr>-->
                        </tbody>
                    </table>
                </div>
                <!--<div class="tip">注：权重影响分类排名及摇一摇出现概率。</div>-->
            </div>


</div>

</template>
<script>
    var config = require('../../../../config')
    import { Swipe, SwipeItem } from 'mint-ui';
    import 'mint-ui/lib/style.css'
    Vue.component(Swipe.name, Swipe);
    Vue.component(SwipeItem.name, SwipeItem);
    export default {
        data(){
            return {
                userinfo:{
                    nickname:WEIPAT.userinfo.nickname,
                    head_pic_init:WEIPAT.userinfo.headimgurl
                },
              userinfoinit:{
                                  head_pic_init:WEIPAT.userinfo.headimgurl
                              },
            }
        },
        mounted: function() {
            this.getuserinfo()

            },
            methods:{
                 getuserinfo(){
                          var  that =this;
                             var header = {
                                 'Accept': 'application/json',
                                 'Content-Type': 'application/json'
                             };

                                     axios.get('/user/userinfoall', {
                                                                         params: {
                                                                             token: storeWithExpiration.get('token')
                                                                         }
                                                                     })
                                                                     .then(function(response) {

                                                                     if(response.status=='200'){
                                                                           return response.data;
                                                                     }

                                            }).then(function(json) {
                            if(typeof(json.data.result)!="undefined"){
                                         that.userinfo = json.data.result;
                             }

                             }).catch(function(ex) {
                             console.log(ex);
                             });
                 }
            },
            computed:{

            }
    }
</script>
<style scoped>
 @import '../../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
 #mumbercheck .tips {
        margin: 15px 3% 10px 3%;
        font-size: 14px;
        color: #888;
    }

    #mumbercheck .tips a span {
        float: right;
        color: #888;
    }

    #mumbercheck .verifyBox {
        width: 97%;
        display: inline-block;
        padding: 12px 0 10px 3%;
        margin-top: 10px;
        background-color: #FFF;
    }

    #mumbercheck .verifyBox.powerInfo {
        width: 100%;
        padding: 12px 0 10px 0;
        margin-top: 0;
    }

    #mumbercheck .verifyBox.pointer {
        cursor: pointer;
    }

    #mumbercheck .verifyBox .avatar,
    #mumbercheck .verifyBox .logo {
        float: left;
        width: 50px;
        height: 50px;
        overflow: hidden;
        border-radius: 25px;
        box-sizing: border-box;
        border: 1px #D9D9D9 solid;
    }

    #mumbercheck .verifyBox .avatar .img,
    #mumbercheck .verifyBox .logo .img {
        width: 46px;
        margin: 1px;
        height: 46px;
        border-radius: 28px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
    }

    #mumbercheck .verifyBox .logo,
    #mumbercheck .verifyBox .logo .img {
        border-radius: 0;
    }

    #mumbercheck .verifyBox .verifyInfo {
        height: 50px;
        float: left;
        font-size: 15px;
        margin-left: 10px;
        line-height: 25px;
    }

    #mumbercheck .verifyBox .verifyInfo p {
        font-size: 13px;
        color: #9C9C9C;
    }

    #mumbercheck .verifyBox .verifyState {
        width: 45px;
        height: 50px;
        float: right;
        margin-right: 3%;
    }

    #mumbercheck .verifyBox .verifyState.finished {
        position: relative;
        width: 55px;
    }

    #mumbercheck .verifyBox .verifyState .verifyIcon {
        width: 26px;
        height: 17px;
        color: #D0D0D0;
        font-size: 32px;
        margin-top: 10px;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        -webkit-align-items: center;
        justify-content: center;
        -webkit-justify-content: center;
    }

    #mumbercheck .verifyBox .verifyState .verifyIcon.individual {
        color: #FFB123;
    }

    #mumbercheck .verifyBox .verifyState .verifyIcon.business {
        color: #1CB4F4;
    }

    #mumbercheck .verifyBox .verifyState p {
        position: relative;
        margin-top: 4px;
        right: 4px;
    }

    #mumbercheck .verifyBox .verifyState .arrow,
    #mumbercheck .verifyBox .shopInfo .arrow {
        position: absolute;
        top: 50%;
        right: 0;
        height: 46px;
        display: none;
        color: #C8C8C8;
        font-size: 18px;
        margin-top: -23px;
        line-height: 46px;
    }

    #mumbercheck .verifyBox .verifyState.finished .arrow,
    #mumbercheck .verifyBox .shopInfo .arrow {
        display: block;
    }

    #mumbercheck .verifyBox .verifyState img {
        width: 26px;
        margin-top: 10px;
    }

    #mumbercheck .verifyBox .verifyState p:after {
        content: '未认证';
        display: table;
        height: 10px;
        line-height: 10px;
        font-size: 10px;
        color: #C6C6C6;
        text-align: center;
    }

    #mumbercheck .verifyBox .verifyState.pending p:after {
        content: '审核中';
    }

    #mumbercheck .verifyBox .verifyState p.individual:after {
        content: '已认证';
        color: #FFB300;
    }

    #mumbercheck .verifyBox .verifyState p.business:after {
        content: '已认证';
        color: #169BDB;
    }

    #mumbercheck .verifyBox .title {
        padding-bottom: 13px;
        font-size: 15px;
        color: #424242;
        position:relative;
    }

    #mumbercheck .verifyBox .title span {
        font-size: 12px;
        color: #9C9C9C;
    }

    #mumbercheck .verifyBox .shopInfo {
        position: relative;
        display: table;
        width: 87%;
        padding-right: 10%;
        margin-top: 10px;
    }

    #mumbercheck .verifyBox .powerList {
        width: 94%;
        margin: 0px 3%;
    }

    #mumbercheck .verifyBox .powerList table {
        width: 100%;
        text-align: center;
    }

    #mumbercheck .verifyBox .powerList table tr td {
        /*border-right: 1px solid #D9D9D9;*/
        width: 29%;
    }

    #mumbercheck .verifyBox .powerList.verified table tr td {
        width: 58%;
    }

    #mumbercheck .verifyBox .powerList table tr td:first-child{
        width: 45%;
    }

    #mumbercheck .verifyBox .powerList table tr td:last-child{
        border-right: 0px;
    }

    #mumbercheck .verifyBox .powerList table thead tr td {
        height: 40px;
        line-height: 40px;
        background-color: #eeeeee;
        font-size: 15px;
        /*border-bottom: 1px solid #D9D9D9;*/;
    }

    #mumbercheck .verifyBox .powerList table tbody tr td {
        height: 45px;
        line-height: 45px;
        font-size: 14px;
    }

    #mumbercheck .verifyBox .powerList table tbody tr:last-child td {
        /*border-bottom: 1px solid #D9D9D9;*/
    }

    #mumbercheck .verifyBox .powerList table tbody tr:nth-child(odd) {
        background-color: #F7F7F7;
    }

    #mumbercheck .verifyBox .powerList table tbody tr:nth-child(even) {
        background-color: #FCFCFC;
    }

    #mumbercheck .verifyBox .powerList table tbody tr td:nth-child(2){
        color: #D51505;
    }

    #mumbercheck .verifyBox .powerList table tbody tr td:last-child {
        color: #06BC07;
    }

    #mumbercheck .verifyBox.powerInfo .tip {
        font-size: 12px;
        color: #888;
        margin: 12px 0 0 3%;
    }

    #mumbercheck .verifyBox .applyItem {
        width: 48.5%;
        float: left;
    }

    #mumbercheck .verifyBox a {
        display: block;
        text-decoration: none;
        color: #000;
    }

    #mumbercheck .verifyBox .applyItem .applyIcon {
        width: 80px;
        height: 60px;
        color: #DBDBDB;
        font-size: 40px;
        border-radius: 25px;
        margin: 20px auto 0 auto;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        -webkit-align-items: center;
    }

    #mumbercheck .verifyBox .applyItem .applyIcon.finished {
        color: #169ADA;
    }

    #mumbercheck .verifyBox .applyItem p {
        line-height: 35px;
        text-align: center;
        font-size: 14px;
    }

    #mumbercheck .verifyBox .applyItem.disabled p {
        color: #999;
    }

    #mumbercheck .verifyBox .applyItem .applyBtn {
        width: 47px;
        height: 24px;
        margin: 0 auto;
        border-radius: 3px;
        text-align: center;
        font-size: 11px;
        line-height: 24px;
        color: #FFF;
        background-color: #169BDB;
    }

    #mumbercheck .verifyBox .applyItem .applyBtn.reject {
        background-color: #ff3931;
    }

    #mumbercheck .verifyBox .applyItem.disabled .applyBtn {
        background-color: #FFF;
    }

    #mumbercheck .verifyBox .applyItem.pending .applyBtn {
        background-color: #FFF;
        color: #169BDB;
    }

    #mumbercheck .verifyBox .applyItem.pending p {
        color: #169BDB;
    }

  #mumbercheck .verifyBox  .tianbaoweipai.icon-circle-o:before{
 content: "\e6ae";
  }
.fi-stack-2x{
font-size:2em;
}
.fi-stack-1x{
font-size:2.1em;
}
  #mumbercheck .verifyBox  .tianbaoweipai.icon-addicon:before {
        content: "\e618";
  }
    #mumbercheck .verifyBox  .tianbaoweipai.icon-selected:before {
          content: "\e643";
    }
  #mumbercheck .verifyBox  .tianbaoweipai.icon-my-o:before {
        content: "\e6a1";
  }


  #mumbercheck .verifyBox  .tianbaoweipai.icon-shopicon:before {
        content: "\e666";
  }


    .rotate {
        transform: rotate(0deg);
    }

</style>
