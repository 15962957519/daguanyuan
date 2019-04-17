<style scoped>
 @import '../../assets/css/user_center_seller_center01.css'
</style>
<style scoped>
#myqverfitymobile .verifyBox{
    width: 100%;
    display: table;
}
#myqverfitymobile .verifyBox.operator{
    background: #FFF;
}
.verifyBox .buttonBanner button{
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
#myqverfitymobile .verifyBox .infoItem {
    position: relative;
    height: 28px;
    width: 96%;
    margin-left: 5%;
    font-size: 16px;
    line-height: 28px;
    padding: 10px 1% 10px 0;
    box-sizing:content-box;
}

#myqverfitymobile .verifyBox .infoItem .liHead {
    width: 27%;
    float: left;
}

#myqverfitymobile .verifyBox .infoItem .liContent {
    width: 72%;
    float: left;
}

#myqverfitymobile .verifyBox .infoItem .liFoot {
    width: auto;
    overflow: hidden;
    display: inline-block;
    height: 28px;
    float: left;
}
#myqverfitymobile .verifyBox .infoItem .liFoot.checkcode{
    width: auto;
    border-radius: 3px;
    color: #fff;
    font-weight: 500;
    margin-right: 8%;
    font-size: 16px;
    text-align: center;
    background: #fe0100;
    float: right;
}


#myqverfitymobile .verifyBox .infoItem .liFoot.error:after {
    content: '';
    width: 100%;
    height: 100%;
    float: right;
    background-image: url("/res/img/verifyWarnIcon.png");
    background-repeat: no-repeat;
    background-size: 15px;
    background-position: center center;
}

#myqverfitymobile .verifyBox .infoItem .liContent .input,
#myqverfitymobile .verifyBox .infoItem .liContent .numInput {
    border: 0;
    font-size: 14px;
    height: 28px;
    float: left;
    width: 55%;
}
#myqverfitymobile .verifyBox .infoItem .liContent .input.numinputcode {
    width: 45%;
}

#myqverfitymobile .verifyBox .infoItem .liContent .numInput span {
    line-height: 28px;
    height: 28px;
    color: #A9A9A9;
}

#myqverfitymobile .verifyBox .infoItem .liContent .numInput span.hover {
    border-right: 2px solid red;
}

#myqverfitymobile.verifyBox .infoItem .liContent .numInput span.hasValue {
    color: #000;
}

#myqverfitymobile .verifyBox .tips {
    padding: 5px 5% 10px 5%;
    color: #939393;
    background-color: #EEE;
    line-height: 20px;
    font-size: 12px;
    text-align: right;
}

#myqverfitymobile .verifyBox .tips.verified {
    color: #169ADA;
}

#myqverfitymobile .verifyBox .tips.verifyError {
    color: #F94A45;
}

#myqverfitymobile .verifyBox .tips.confirm {
    position: relative;
    width: 90%;
    height: 1px;
    padding: 0;
    border: 0;
    margin: 25px 5%;
    background-color: #AAA;
}

#myqverfitymobile .verifyBox .tips.confirm div {
    position: absolute;
    width: 48%;
    left: 26%;
    margin-top: -6px;
    font-size: 12px;
    line-height: 12px;
    text-align: center;
    background-color: #EEE;
}

</style>
<template>
    <div id="myqverfitymobile">
            <div class="verifyBox operator">
                <div class="infoItem text border horizonBottom">
                    <div class="liHead">手机号码</div>
                    <div class="liContent">
                        <input  class="input" v-model.number="mobile"   type="number"  maxlength="11" ref="idcode" placeholder="输入常用的手机号码"  >
                    </div>
                    <div class="liFoot"></div>
                </div>
                <div class="infoItem text border horizonBottom">
                    <div class="liHead">验证码</div>
                    <div class="liContent">
                        <input type="text" class="input numinputcode" v-model="idcode" ref="mobilecode"  placeholder="输入收到的验证码" maxlength="6" >
                        <div class="liFoot checkcode"  @click.stop="sendmobile($event)" ><label>点击获取验证码</label></div>
                    </div>
                </div>
                <div class="tips"></div>
                <div  class="buttonBanner" @click.stop="checkmobilecode($event)"><button  class="next">提交</button></div>
            </div>
    </div>
</template>
<script>
var config = require('../../../config')
import {MessageBox ,Switch,Indicator,Toast } from 'mint-ui';
    export default {
        data(){
            return {
                 lists: [],
                 mobile: '',
                mics: 0
            }
        },
          components:{

            },
        mounted: function() {
                document.title = "手机验证"
                var obj3 = new Object();
                this.getuserfocus(obj3);
            },
            methods:{
                    timer(second,e){
                        var that=this;
                        that.mics =second
                        var nicknamefunction=function(){
                            e.target.innerHTML = that.mics +'s后可以获取'
                            that.mics--;
                            //去掉定时器的方法
                            if(that.mics<=0){
                                e.target.innerHTML ='点击获取验证码'
                                window.clearInterval(t1);
                            }
                        }
                        var t1 = window.setInterval(nicknamefunction,1000);
                    },
                    sendmobile(e){
                        if(this.mics>0){
                            return false;
                        }

                        var mobilestr =this.$refs.idcode.value;
                        var reg = /^1[0-9]{10}$/;
                        if(mobilestr.replace(/(^s*)|(s*$)/g, "").length ==0){
                            MessageBox.alert('手机号码不能为空!', '提示').then(action=>{return false });
                            return false;
                        }
                        if(!reg.test(mobilestr)){
                            MessageBox.alert('手机号码格式不正确!', '提示').then(action=>{return false });
                            return false;
                        }
                        var dd =new FormData();
                        var that=this;
                        dd.append('token',storeWithExpiration.get('token'))
                        dd.append('mobile',this.$refs.idcode.value)
                           axios.post( '/sendmoblievertifycode', dd) .then(function(response) {
                                                    if(response.status=='200'){
                                                        return response.data;
                                                    }
                                                }).then(function(json) {
                                                    if(json.code=='2000'){
                                                        MessageBox.alert('发送成功!', '提示');
                                                        window.storeWithExpiration.set('mobilecode', mobilestr, 60000);
                                                        that.timer(60,e);

                                                    }else{
                                                        MessageBox.alert(json.message, '提示');
                                                    }
                                            }).catch(function(ex) {
                                                console.log(ex);
                                            });
                        return false;
                    },
                checkmobilecode(e){
                    var mobilestr =this.$refs.idcode.value;
                    var reg = /^1[0-9]{10}$/;
                    var regcode = /^[0-9]{4}$/;
                    if(mobilestr.replace(/(^s*)|(s*$)/g, "").length ==0){
                        MessageBox.alert('手机号码不能为空!', '提示').then(action=>{return false });
                        return false;
                    }
                    if(!reg.test(mobilestr)){
                        MessageBox.alert('手机号码格式不正确!', '提示').then(action=>{return false });
                        return false;
                    }
                    if(!regcode.test(this.$refs.mobilecode.value)){
                        MessageBox.alert('验证码不正确', '提示').then(action=>{return false });
                        return false;
                    }


                    var dd =new FormData();
                    var that=this;
                    dd.append('token',storeWithExpiration.get('token'))
                    dd.append('mobile',this.$refs.idcode.value)
                    dd.append('mobilecode',this.$refs.mobilecode.value)
                    axios.post( '/checkmcode', dd) .then(function(response) {
                        if(response.status=='200'){
                            return response.data;
                        }
                    }).then(function(json) {
                        if(json.code=='2000'){
                            MessageBox.alert(json.message, '提示').then(action=>{ that.$router.push({ path: '/fabu' })});
                        }else{
                            MessageBox.alert(json.message, '提示');
                        }
                    }).catch(function(ex) {
                        console.log(ex);
                    });
                    return false;
                },
                getuserfocus(obj){
                    var	that =this;
                    axios.get( '/user/userinfofocusall', {
                        params: {
                            token: storeWithExpiration.get('token'),
                            page: 1
                        }
                    }) .then(function(response) {
                            if(response.status=='200'){
                                return response.data;
                            }
                        }).then(function(json) {
                            if(typeof(json.data.data)!='undefined'){
                                that.lists = json.data.data;
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

