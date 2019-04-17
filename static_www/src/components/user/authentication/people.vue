<template>
    <div>
        <div class="margin">
            <!--顶部导航-->
            <yd-navbar :fixed="true" >
                <router-link   to="" slot="left" >
                    <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
                </router-link>
                <p style="font-size: .3rem" slot="center">个人认证</p>
                <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
            </yd-navbar>
            <!--个人认证-->
            <div class="people_ti">
                <p>填写资料<span class="bg-yellow">(必填)</span></p>
            </div>
            <!--流程-->
            <yd-cell-group>
                <yd-cell-item>
                    <span slot="left">姓名：</span>
                    <yd-input slot="right" required  v-model="card.uname" max="20"ref="input9" placeholder="请输入用户名"></yd-input>
                </yd-cell-item>
                <yd-cell-item>
                    <span slot="left">电话：</span>
                    <input slot="right" type="number" v-model="card.mobile" placeholder="请输入您手机号码">
                </yd-cell-item>
                <yd-cell-item>
                    <span slot="left">身份证号码：</span>
                    <input slot="right" type="text" v-model="card.number" placeholder="请输入您的身份证号码">
                </yd-cell-item>
                <!--个人认证-->
                <div class="people_ti">
                    <p>上传身份证件<span class="bg-yellow">(必填)</span></p>
                </div>
                <!--上传图片-->
                <div class="upload_img">
                    <p>上传身份证正面</p>
                    <!--<img  style="" src="@/assets/images/shengfenzhengzhengm.png"/>-->
                    <div id="one"  class="upload_im_div"  @click.stop="addImg"> </div>
                    <div  class="addimg"  v-for="(item, index)  in localIds"  v-model="card.frontid">
                        <div class="po_img_t"  @click="removeimgs()"><img  style=" position: absolute; top: .1rem ; right: 0.1rem;width: .5rem; height: .5rem;" src="@/assets/images/x.png" ></div>
                        <img :src="localIds">
                    </div>
                </div>
                <!--上传省份证反面-->
                <div class="upload_img">
                    <p>上传身份证反面</p>
                    <!--<img  style="width: 100%; padding: .1rem .3rem" src="@/assets/images/shengfenzhengfanm.png"/>-->
                    <div id="two" class="upload_im_div"  @click.stop="addImg1"> </div>
                    <div class="addimg" v-for="(item, index)  in localIds1"  v-model="card.backid">
                        <div class="po_img_t"  @click="removeimgs1()"><img  style=" position: absolute; top: .1rem ; right: 0.1rem;width: .5rem; height: .5rem;" src="@/assets/images/x.png" ></div>
                        <img :src="item">
                    </div>
                </div>
                <div>付款后请及时通知平台工作人员，加快审核速度。</div>
            </yd-cell-group>
            <!--提交-->

            <yd-button v-show="is_identify==0 && card.is_pay==0" class="btn_canc"   @click.native="setDate($event)"bgcolor="#af773e" color="#fff" size="large" type="warning" >确认提交并支付费用</yd-button>
            <yd-button v-show="is_identify==1 && card.is_pay==0" class="btn_canc"   @click.native="setDate1($event)"bgcolor="#af773e" color="#fff" size="large" type="warning" >提交并支付费用</yd-button>
            <yd-button v-show="is_identify==1 && card.is_pay==1 " class="btn_canc"   @click.native="setDate2($event)"bgcolor="#af773e" color="#fff" size="large" type="warning" >提交修改信息</yd-button>

        </div>


    </div>
</template>
<style>
    #one{width: 100%;padding: .1rem .3rem;background :url("../../../assets/images/shengfenzhengzhengm.png");background-size:cover;}
    #two{width: 100%;padding: .1rem .3rem;background :url("../../../assets/images/shengfenzhengfanm.png");background-size:cover;}
    .people_ti{ text-align: left; padding: .2rem; height: .7rem; background: #f5f5f5; }
    .bg-yellow{ color: #af773e;}
    .margin{ padding-top: 1rem;}
    .yd-grids-4{margin-top:1.1rem;}
    /*.yd-grids-group{margin-top:1rem;}*/
    .upload_img p{ height:.5rem ;color: #af773e; line-height: .6rem}
    .upload_img{ width: 100%; height: 5rem; background: #eee; margin-top: .3rem; position: relative;}
    .upload_im_div{ position:absolute;z-index: 10;width: 100%;height: 4rem;border: 1px solid #eee;  }
    .addimg{ width: 100%; height: 4rem; position:absolute;z-index: 15;}
    .addimg img{ width: 100%; height: 4rem;}
    .yd-btn-block{ margin-top: 0;}
    .po_img_t{ position: absolute; top: .1rem ; right: 0.1rem;width: 1.5rem; height: 1.5rem;}
    .btn_canc{padding: 0 .3rem;display: block;margin-top: -.4rem;margin-bottom: .3rem; width: 85%; margin: 0 auto; margin-bottom: .2rem;}
</style>
<script>
    import Vue from 'vue';
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
    import wx from 'weixin-js-sdk'
    Vue.component(NavBar.name, NavBar);
    Vue.component(NavBarBackIcon.name, NavBarBackIcon);
    Vue.component(NavBarNextIcon.name, NavBarNextIcon);
    export default {
        data () {
            return {
                localIds:[],
                localIds1:[],
                is_identify:'',
                card: {
                    uname: '',
                    number: '',
                    mobile: '',
                    frontid:'',
                    backid:'',
                    state_numb:21,
                    title:"个人认证",
                    is_pay:'',

                },

            }
        },
        mounted:function(){
            this.category();
        },
        //方法
        methods: {
            category () {
                var token = window.storeWithExpiration.get('token');
                var url = "/identify_edit?token=" + token;
                var that = this;
                this.$axios.get(url).then(function(response){
                    // console.log(11,response.data.data)
                    // console.log(22,response.data.data.identity_type)
                    if(response.data.data.is_identify==1){
                        that.card.uname=response.data.data.name
                        that.card.mobile=response.data.data.telephone
                        that.card.number=response.data.data.idcode
                        that.localIds.push(response.data.data.verifyIdcodefront)
                        that.localIds1.push(response.data.data.verifyIdcodeback)
                        that.is_identify=response.data.data.is_identify
                        that.card.is_pay=response.data.data.is_pay
                    }
                })
            },
            goback () {
                this.$router.go(-1)
            },
            //提交支付
            payupshop(){
                var _that = this;
                var order_sn = _that.ordersn();
                var token = storeWithExpiration.get('token');
                //console.log(order_sn)
                var url = '/balancerecharge?token=' + token + '&state='+_that.card.state_numb + '&order_sn=' + order_sn
                _that.$axios.get(url).then(function(response) {
                    console.log(response)
                    if (response.status == 200){
                        if (response.data.code == 2000){
                            console.log(response)
                            _that.wxzfcallpay(JSON.parse(response.data.jsondata))
                        }
                    }
                }).catch(function(error) {
                    //console.log(error);
                });

            },
            wxzfcallpay(config){
                var _that = this;
                //有支付
                function jsApiCall(config) {
                    WeixinJSBridge.invoke('getBrandWCPayRequest', config, function(res) {
                        //WeixinJSBridge.log(res.err_msg);
                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                            // self.RouterLink('personal')
                            _that.$store.dispatch("getuserinfo");
                            _that.$dialog.toast({
                                mes: '支付成功',
                                timeout: 1500,
                                icon: 'success',
                            });

                            //_that.message="信息审核中..."
                            _that.$router.push({ path: '/user/authentication/authentication_index' })
                        }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                            _that.$store.dispatch("getuserinfo");
                            _that.$dialog.toast({
                                mes: '已取消支付',
                                timeout: 1500,
                                icon: 'success',
                            });
                            _that.$router.push({ path: '/user/authentication/authentication_index' })
                            // window.location.href = 'gift_failview.do?out_trade_no=' + this.orderId
                        } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                            _that.$dialog.alert({mes: '网络异常'})
                        }
                    });
                }
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall(config), false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall(config));
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall(config));
                    }
                } else {
                    jsApiCall(config);
                }
            },
            ordersn(){
                var now = new Date()
                var month = now.getMonth() + 1
                var day = now.getDate()
                var hour = now.getHours()
                var minutes = now.getMinutes()
                var seconds = now.getSeconds()
                return now.getFullYear().toString() + month.toString() + day + hour + minutes + seconds + (Math.round(Math.random() * 89 + 100)).toString()
            },

            //点击选择图片1
            addImg(e, style) {
                let _this = this;
                wx.ready(function(){
                    //绑定点击事件
                    wx.chooseImage({
                        count: 1, // 默认9
                        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                        success: function (res) {
                            _this.localIds  = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                            _this.wxuploadImage(_this.localIds );
                        },
                        fail: function (res) {
                            alert(JSON.stringify(res));
                        }
                    })
                })
            },
            wxuploadImage(localIds) {
                let _this = this;
                var i = 0;
                var length = localIds.length;
                var upload = function() {
                    let loacId = localIds[i];
                    if (window.__wxjs_is_wkwebview) {
                        if (loacId.indexOf("wxlocalresource") != -1) {
                            loacId = loacId.replace("wxlocalresource", "wxLocalResource");
                        }
                    }
                    wx.uploadImage({
                        localId: loacId, // 需要上传的图片的本地ID，由chooseImage接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            // alert(res.serverId);
                            _this.card.frontid=''
                            _this.card.frontid=res.serverId;
                        },
                        fail: function (error) {
                            alert("网络不佳，请稍后重试！")
                        }
                    });
                }
                upload();
            },
            //点击选择图片2
            addImg1() {
                let _this = this;
                wx.ready(function(){
                    //绑定点击事件
                    wx.chooseImage({
                        count: 1, // 默认9
                        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                        success: function (res) {
                            _this.localIds1  = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                            _this.wxuploadImage1(_this.localIds1 );
                        },
                        fail: function (res) {
                            alert(JSON.stringify(res));
                        }
                    })
                })
            },
            wxuploadImage1(localIds1) {
                let _this = this;
                var i = 0;
                var length = localIds1.length;
                var upload = function() {
                    let loacId = localIds1[i];
                    if (window.__wxjs_is_wkwebview) {
                        if (loacId.indexOf("wxlocalresource") != -1) {
                            loacId = loacId.replace("wxlocalresource", "wxLocalResource");
                        }
                    }
                    wx.uploadImage({
                        localId: loacId, // 需要上传的图片的本地ID，由chooseImage接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            //alert(res.serverId);
                            _this.card.backid=''
                            _this.card.backid=res.serverId;
                        },
                        fail: function (error) {
                            alert("网络不佳，请稍后重试！")
                        }
                    });
                }
                upload();
            },
            //上传图片取得seridsid
            removeimgs(){
                //点击清除divs
                var _that = this;
                _that.localIds=""
            },
            removeimgs1(){
                //点击清除divs
                var _that = this;
                _that.localIds1=""
            },
            //  提交代码
            //  提交代码
            setDate(e){
                var _that = this;
                if (_that.card.uname == ""){
                    //alert("姓名不能为空");
                    _that.$dialog.alert({mes: '姓名不能为空！'});
                    return;
                }
                if (_that.card.mobile == ""){
                    //alert("身份证号码不能为空");
                    _that.$dialog.alert({mes: '手机号码不能为空！'});
                    return;
                }
                if (_that.card.number == ""){
                    //alert("身份证号码不能为空");
                    _that.$dialog.alert({mes: '身份证号码不能为空！'});
                    return;
                }
                if(_that.card.frontid == ""){
                    _that.$dialog.alert({mes: '请上传身份证正面！'});
                    return false;
                }
                if(_that.card.backid == ""){
                    _that.$dialog.alert({mes: '请上传身份证反面！'});
                    return false;
                }
                _that.upload =function () {
                    var url = "/person_identify";
                    var dd = new FormData();
                    dd.append('token', storeWithExpiration.get('token'))
                    _that.goodstoserver = JSON.stringify(_that.card);
                    dd.append('card', _that.goodstoserver)
                    _that.$axios.post(url, dd, {timeout: 20000}).then(function(response) {
                        {
                            //_that.$dialog.alert({mes: '提交成功 等待审核！'});
                            _that.$dialog.toast({
                                mes: '提交成功 等待审核',
                                timeout: 1500,
                                icon: 'success',
                            });
                            _that.$store.commit("card",_that.card)
                            //_that.payupshop()
                            _that.$store.dispatch("getuserinfo");
                            _that.$router.push({ path: '/user/authentication/authentication_index' })

                            //   _that.$router.push({ path: '/user/authentication/authenication_end' })
                            return false;

                        }
                    }).catch(function(error) {
                            //console.log(error);
                        }
                    );
                }
                setTimeout(_that.upload,100);
                return  false;
            },
            setDate1(e){
                var _that = this;
                if (_that.card.uname == ""){
                    //alert("姓名不能为空");
                    _that.$dialog.alert({mes: '姓名不能为空！'});
                    return;
                }
                if (_that.card.mobile == ""){
                    //alert("身份证号码不能为空");
                    _that.$dialog.alert({mes: '手机号码不能为空！'});
                    return;
                }
                if (_that.card.number == ""){
                    //alert("身份证号码不能为空");
                    _that.$dialog.alert({mes: '身份证号码不能为空！'});
                    return;
                }
                if(_that.card.localIds == ""){
                    _that.$dialog.alert({mes: '请上传身份证正面！'});
                    return false;
                }
                if(_that.card.localIds1 == ""){
                    _that.$dialog.alert({mes: '请上传身份证反面！'});
                    return false;
                }
                _that.upload =function () {
                    var url = "/identify_person_edit";
                    var dd = new FormData();
                    dd.append('token', storeWithExpiration.get('token'))
                    _that.goodstoserver = JSON.stringify(_that.card);
                    dd.append('card', _that.goodstoserver)
                    _that.$axios.post(url, dd, {timeout: 20000}).then(function(response) {
                        {
                            //_that.$dialog.alert({mes: '提交成功 等待审核！'});
                            _that.$dialog.toast({
                                mes: '提交成功 等待审核',
                                timeout: 1500,
                                icon: 'success',
                            });
                            _that.$store.commit("card",_that.card)
                            //_that.payupshop()
                            _that.$store.dispatch("getuserinfo");
                            _that.$router.push({ path: '/user/authentication/authentication_index' })

                            //   _that.$router.push({ path: '/user/authentication/authenication_end' })
                            return false;

                        }
                    }).catch(function(error) {
                            //console.log(error);
                        }
                    );
                }
                setTimeout(_that.upload,100);
                return  false;
            },
            setDate2(e){
                var _that = this;
                if (_that.card.uname == ""){
                    //alert("姓名不能为空");
                    _that.$dialog.alert({mes: '姓名不能为空！'});
                    return;
                }
                if (_that.card.mobile == ""){
                    //alert("身份证号码不能为空");
                    _that.$dialog.alert({mes: '手机号码不能为空！'});
                    return;
                }
                if (_that.card.number == ""){
                    //alert("身份证号码不能为空");
                    _that.$dialog.alert({mes: '身份证号码不能为空！'});
                    return;
                }
                if(_that.card.localIds == ""){
                    _that.$dialog.alert({mes: '请上传身份证正面！'});
                    return false;
                }
                if(_that.card.localIds1 == ""){
                    _that.$dialog.alert({mes: '请上传身份证反面！'});
                    return false;
                }
                _that.upload =function () {
                    var url = "/identify_person_edit";
                    var dd = new FormData();
                    dd.append('token', storeWithExpiration.get('token'))
                    _that.goodstoserver = JSON.stringify(_that.card);
                    dd.append('card', _that.goodstoserver)
                    _that.$axios.post(url, dd, {timeout: 20000}).then(function(response) {
                        {
                            //_that.$dialog.alert({mes: '提交成功 等待审核！'});
                            _that.$dialog.toast({
                                mes: '提交成功 等待审核',
                                timeout: 1500,
                                icon: 'success',
                            });
                            _that.$store.commit("card",_that.card)
                            _that.$store.dispatch("getuserinfo");
                            _that.$router.push({ path: '/user/authentication/authentication_index' })

                            //   _that.$router.push({ path: '/user/authentication/authenication_end' })
                            return false;

                        }
                    }).catch(function(error) {
                            //console.log(error);
                        }
                    );
                }
                setTimeout(_that.upload,100);
                return  false;
            },
        },


    }
</script>

