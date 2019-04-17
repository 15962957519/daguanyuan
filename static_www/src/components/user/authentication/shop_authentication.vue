
<template>
    <div>
        <div class="margin">
            <!--顶部导航-->
            <yd-navbar :fixed="true" >
                <router-link   to="" slot="left" >
                    <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
                </router-link>
                <p style="font-size: .3rem" slot="center">企业认证</p>
                <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
            </yd-navbar>
            <!--个人认证-->
            <div class="people_ti">
                <p>填写资料<span class="bg-yellow">(必填)</span></p>
            </div>
            <!--流程-->
            <yd-cell-group>
                <yd-cell-item>
                    <span slot="left">企业名称：</span>
                    <yd-input slot="right" required  v-model="card.uname" max="20"ref="input9" placeholder="请输入用户名"></yd-input>
                </yd-cell-item>
                <!--<yd-cell-item>-->
                    <!--<span slot="left">电话：</span>-->
                    <!--<input slot="right" type="number" v-model="card.mobile" placeholder="请输入您手机号码">-->
                <!--</yd-cell-item>-->
                <!--<yd-cell-item>-->
                    <!--<span slot="left">身份证号码：</span>-->
                    <!--<input slot="right" type="number" v-model="card.number" placeholder="请输入您的身份证号码">-->
                <!--</yd-cell-item>-->
                <!--个人认证-->
                <div class="people_ti">
                    <p>上传营业执照<span class="bg-yellow">(必填)</span></p>
                </div>
                <!--上传图片-->
                <div class="upload_img11">
                    <p>请营业执照正面高清图</p>
                    <!--<img  style="" src="@/assets/images/shengfenzhengzhengm.png"/>-->
                    <div id="three"  class="upload_im_div11"  @click.stop="addimg11"> </div>
                    <div  class="addimg11"  v-for="(item, index)  in localIds"  v-model="card.frontid">
                        <div class="po_img_t11"  @click="removeimgs()"><img  style=" position: absolute; top: .1rem ; right: 0.1rem;width: .5rem; height: .5rem;" src="@/assets/images/x.png" ></div>
                        <img :src="item">
                    </div>
                </div>

            </yd-cell-group>
            <!--提交-->
            <yd-button v-show="is_identify==0 && card.is_pay==0" class="btn_canc"   @click.native="setDate($event)"bgcolor="#af773e" color="#fff" size="large" type="warning" >确认提交并支付费用</yd-button>
            <yd-button v-show="is_identify==1 && card.is_pay==0" class="btn_canc"   @click.native="setDate1($event)"bgcolor="#af773e" color="#fff" size="large" type="warning" >提交并支付费用</yd-button>
            <yd-button v-show="is_identify==1 && card.is_pay==1 " class="btn_canc"   @click.native="setDate2($event)"bgcolor="#af773e" color="#fff" size="large" type="warning" >提交修改信息</yd-button>
        </div>


    </div>
</template>
<style>
    #three{width: 80%;  margin-left:10%;padding: .1rem .3rem;background :url("../../../assets/images/qiye.jpg");background-size:cover;}

    .people_ti{ text-align: left; padding: .2rem; height: .7rem; background: #f5f5f5; }
    .bg-yellow{ color: #af773e;}
    .margin{ padding-top: 1rem;}
    .yd-grids-4{margin-top:1.1rem;}
    /*.yd-grids-group{margin-top:1rem;}*/
    .upload_img11 p{ height:.5rem ;color: #af773e; line-height: .6rem}
    .upload_img11{ width: 100%; height: 9rem; background: #eee; margin-top: .3rem; position: relative;}
    .upload_im_div11{ position:absolute;z-index: 10;width: 100%;height: 8rem;border: 1px solid #eee;  }
    .addimg11{ width: 100%; height: 9rem; position:absolute;z-index: 15;}
    .addimg11 img{   margin: 0 auto;width: 80%; height: 8rem;}
    .yd-btn-block{ margin-top: 0;}
    .po_img_t11{ position: absolute; top: .1rem ; right: 0.6rem;width: 1.5rem; height: 1.5rem;}
    .btn_canc{padding: 0 .3rem;display: block;margin-top: -.4rem;margin-bottom: .3rem;}
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
                is_identify:'',
                card: {
                    uname: '',
                    number: '',
                    mobile: '',
                    frontid:'',
                    backid:'',
                    state_numb:22,
                    title:"企业认证",
                    is_pay:''
                },

            }
        },
        mounted:function(){
            this.category();
        },
        //方法
        methods: {
            goback () {
                this.$router.go(-1)
            },
            //显示企业信息
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
                        that.is_identify=response.data.data.is_identify
                        that.card.is_pay=response.data.data.is_pay
                    }

                })
            },
            //点击选择图片1
            addimg11(e, style) {
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

            //上传图片取得seridsid
            removeimgs(){
                //点击清除divs
                var _that = this;
                _that.localIds=""
            },
            //  提交代码
            //  提交代码
            setDate(e){
                var _that = this;
                if (_that.card.uname == ""){
                    //alert("姓名不能为空");
                    _that.$dialog.alert({mes: '企业名称不能为空！'});
                    return;
                }

                if(_that.card.frontid == ""){
                    _that.$dialog.alert({mes: '请上传企业正面高清图片！'});
                    return false;
                }

                _that.upload =function () {
                    var url = "/company_identify";
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
                            _that.$router.push({ path: '/user/authentication/authenication_end' })
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
                    _that.$dialog.alert({mes: '企业名称不能为空！'});
                    return;
                }

                if(_that.card.localIds == ""){
                    _that.$dialog.alert({mes: '请上传企业正面高清图片！'});
                    return false;
                }

                _that.upload =function () {
                    var url = "/company_identify_edit";
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
                            _that.$router.push({ path: '/user/authentication/authenication_end' })
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
                    _that.$dialog.alert({mes: '企业名称不能为空！'});
                    return;
                }

                if(_that.card.localIds == ""){
                    _that.$dialog.alert({mes: '请上传企业正面高清图片！'});
                    return false;
                }

                _that.upload =function () {
                    var url = "/company_identify_edit";
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
                          //  _that.$router.push({ path: '/user/authentication/authenication_end' })
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