
<template>
    <div id="product">
        <yd-navbar title="在线数量" :fixed="true">
            <router-link  to="#" slot="left">
                <yd-navbar-back-icon  @click.native="goback" >返回</yd-navbar-back-icon>
            </router-link>
            <router-link to="#" slot="right" @click.native="handleClick" >
                <yd-navbar-next-icon>发布</yd-navbar-next-icon>
            </router-link>
        </yd-navbar>
        <div class="blankheader" ></div>
        <!--上传图片-->
        <yd-grids-group>
            <div class="upload_imgproduct">
                <p>上传图片 {{ imgnum }}/9</p>
                <div class="up_div clearfix">
                    <div class="up_div_one" v-for="(item, index)  in localIds" :id="index">
                        <img :src="item" width="80px"/>
                        <img class="xianzhi" src="@/assets/images/x.png" @click="removeimgs(index)">
                    </div>
                    <div class="up_div_two" @click.stop="addImg($event)"></div>
                </div>
            </div>
        </yd-grids-group>

        <!--描述名称-->
        <yd-cell-group class="mardingspeccontent">
            <yd-cell-item>
                <span slot="left">藏品名称：</span>
                <input slot="right" type="text" placeholder="请输入名称" v-model="goods.goods_name" maxlength="20">
            </yd-cell-item>
            <!--文本描述-->
            <yd-cell-group>
                <yd-cell-item>
                    <yd-textarea slot="right" type="text" placeholder="请输入描述" v-model="goods.goods_content" maxlength="100"></yd-textarea>
                </yd-cell-item>
            </yd-cell-group>
            <!--时间插件-->
            <yd-cell-group>
                <yd-cell-item arrow>
                    <span slot="left">时间段范围：</span>
                    <yd-datetime :start-date="goods.starttime" :end-date="goods.endtime" v-model="goods.datetimeup" slot="right"></yd-datetime>
                </yd-cell-item>
            </yd-cell-group>
            <yd-cell-item arrow type="label">
                <span slot="left">分类：</span>
                <select slot="right" v-model="goods.cat_id">
                    <option value="0" v-bind:value="0">请选择分类</option>
                    <option  v-for="item in cateres" v-bind:value="item.id">{{ item.name }}</option>
                </select>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">藏品价格：</span>
                <input slot="right"  regex="^\d{5,12}$" type="number" placeholder="请输入金额" v-model="goods.start_price">
            </yd-cell-item>
            <!--设置-->
            <yd-cell-item>
                <span slot="left">包邮：</span>
                <yd-switch color="#b58352" slot="right" v-model="goods.is_free_shipping"></yd-switch>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">包退：</span>
                <yd-switch  color="#b58352" slot="right" v-model="goods.enableReturn"></yd-switch>
            </yd-cell-item>
            <!--设置-->
            <yd-cell-item>
                <span slot="left">同步到天天特价：</span>
                <yd-switch color="#b58352" slot="right" v-model="goods.is_special_price"></yd-switch>
            </yd-cell-item>
            <yd-cell-item  @click.native="yijia">
                <span   color="#b58352" slot="left">议价：</span>
                <template  v-if="store_level==1 && is_authentication==0">
                    <yd-switch color="#b58352"  slot="right"  :disabled=true  v-model="goods.is_talk_price"></yd-switch>
                </template>
                <template v-else>
                    <yd-switch  color="#b58352" slot="right"   v-model="goods.is_talk_price"></yd-switch>
                </template>
            </yd-cell-item>
        </yd-cell-group>
        <!--按钮-->
        <yd-button-group>
        <yd-button style="background-color: rgb(175, 119, 62)" size="large" @click.native="handleClick" :disabled="isDisable" type="danger" >点击发布</yd-button>
        </yd-button-group>

        <!--<div class="submit" @click.stop="handleClick"></div>-->
        <div class="specheight"></div>
    </div>
</template>
<style scoped >
    #product  .blankheader{margin-top: 1rem;}
    /*.yd-grids-group{margin-top:1rem;}*/
    #product .upload_imgproduct{width: 100%; background:#fff;}
    #product .upload_imgproduct p{ text-align:left; margin-left:.1rem; height:.8rem; line-height:.8rem;}
    #product .upload_imgproduct ul{background:#fff;}
    #product .upload_imgproduct ul li{ width:32%;height:2rem; border:1px solid #eee; margin-left:1%;margin-bottom:.1rem; background:#fff; float:left; }
    .yd-step-content{ background:#fff; }
    .yd-cell-box{ margin-top:.1rem;}
    .yd-btn-warning{padding:.3rem;}
    .yd-btn-block{ padding:.3rem; }
    .yd-cell-item{ margin-top:.1rem; }
    #product .up_div{
        margin-left: 10px;
    }
    #product .up_div_one{    float: left;
        width: 80px;
        margin-right: 5px;
        margin-bottom: 5px;
        height: 80px;
        border: 1px solid #ccc;}
    .clearfix:after{
        content:".";
        display:block;
        height:0;
        clear:both;
        visibility:hidden;
    }
    .clearfix{
        zoom:1;
    }
    #product .up_div_two{float: left; width: 80px;height:80px;border: 1px solid #ccc; background:url(../../../static/upload.jpg) no-repeat;background-size: cover}
    #product .xianzhi{ top: .1rem ; right: 0.1rem;width: .5rem;}
    #product  .submit{
        width:100%;
        height: 48px;
        background-position: center center;
    }
    #product .mardingspeccontent{
        margin-bottom: 30px;
    }
    #product .specheight{
        height:40px;
    }
    #product .specheightrem{
        height:1rem;
    }
</style>
<script type="text/babel">
    import Vue from 'vue';
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
    import wx from 'weixin-js-sdk'
    Vue.component(NavBar.name, NavBar);
    Vue.component(NavBarBackIcon.name, NavBarBackIcon);
    Vue.component(NavBarNextIcon.name, NavBarNextIcon);
    //流程图
    import {Step, StepItem} from 'vue-ydui/dist/lib.rem/step';
    Vue.component(Step.name, Step);
    Vue.component(StepItem.name, StepItem);
    //上传图片
    import {GridsGroup, GridsItem} from 'vue-ydui/dist/lib.rem/grids';
    /* 使用px：import {GridsItem, GridsGroup} from 'vue-ydui/dist/lib.px/grids'; */
    Vue.component(GridsGroup.name, GridsGroup);
    Vue.component(GridsItem.name, GridsItem);
    //描述
    import {CellGroup, CellItem} from 'vue-ydui/dist/lib.rem/cell';
    /* 使用px：import {CellGroup, CellItem} from 'vue-ydui/dist/lib.px/cell'; */
    Vue.component(CellGroup.name, CellGroup);
    Vue.component(CellItem.name, CellItem);
    //文本
    import {TextArea} from 'vue-ydui/dist/lib.rem/textarea';
    /* 使用px：import {TextArea} from 'vue-ydui/dist/lib.px/textarea'; */
    Vue.component(TextArea.name, TextArea);
    //时间插件
    import {DateTime} from 'vue-ydui/dist/lib.rem/datetime';
    /* 使用px：import {DateTime} from 'vue-ydui/dist/lib.px/datetime'; */
    Vue.component(DateTime.name, DateTime);
    //按钮
    import {Switch} from 'vue-ydui/dist/lib.rem/switch';
    /* 使用px：import {Switch} from 'vue-ydui/dist/lib.px/switch'; */
    Vue.component(Switch.name, Switch);
    //按钮
    import {Button, ButtonGroup} from 'vue-ydui/dist/lib.rem/button';
    /* 使用px：import {Button, ButtonGroup} from 'vue-ydui/dist/lib.px/button'; */
    Vue.component(Button.name, Button);
    Vue.component(ButtonGroup.name, ButtonGroup);
    export default {
        data () {
            return {
                cateres:[],
                localIds:[],
                sersverids:[],
                mobile_validated:'',
                is_authentication: 0,
                isDisable: false,
                imgnum:0,
                store_level:1,
                goods:{
                    goods_name:'',
                    goods_content :'',
                    datetimeup:'',
                    starttime:'',
                    endtime:'',
                    start_price:"",
                    cat_id: 1,
                    is_free_shipping: false,//包邮
                    enableReturn: false,//包退
                    is_special_price: false,//同步到捡漏
                    is_talk_price: false,
                    contact_mobile:0,
                    csrf_token:0
                },
                is_public:true
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
            yijia(){
                var _that = this;
                if(_that.store_level<=1){
                    _that.$dialog.alert({mes: '游客不支持议价'});
                    _that.goods.is_talk_price=false
                }
            },
            //获取分类
            category() {
                var token = window.storeWithExpiration.get('token');
                var url = "/up/index?token=" + token;
                var _that =this;
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        _that.cateres = res.data.data.name;
                        _that.goods.starttime = res.data.data.userinfo.astime;
                        _that.goods.endtime = res.data.data.userinfo.endDatec;
                        _that.is_authentication = res.data.data.userinfo.is_authentication;
                        _that.goods.contact_mobile = res.data.data.userinfo.mobile;
                        _that.goods.csrf_token = res.data.data.userinfo.csrf_token;
                        _that.store_level = res.data.data.userinfo.store_level;
                    }
                }).catch(function (error) {
                    console.log(error)
                })
            },

            //立即发布
            handleClick() {
                var _that = this;
                if(_that.is_public==false){
                    _that.$dialog.alert({mes: '请不要重复提交'});
                    return false;
                }
                var shopname = _that.goods.goods_name;//拍品名称
                var desc = _that.goods.goods_content;//描述信息
                if (_that.localIds.length <= 0){
                    _that.$dialog.alert({mes: '请上传藏品图片'});
                    return false;
                }
                if (_that.settrim(shopname) == ''){
                    _that.$dialog.alert({mes: '拍品名称不能为空'});
                    return false;
                }
                if (_that.settrim(desc) == ''){
                    _that.$dialog.alert({mes: '拍品描述不能为空'});
                    return false;
                }
                if (_that.goods.cat_id == 0){
                    _that.$dialog.alert({mes: '请选择拍品分类'});
                    return false;
                }
                if (_that.settrim(_that.goods.start_price) == ''){
                    _that.$dialog.alert({mes: '拍品价格不能为空'});
                    _that.goods.is_special_price = false;
                    return false;
                }

                if (_that.goods.is_special_price){
                    if (_that.goods.start_price > 3000){
                        _that.$dialog.alert({mes: '拍品超过3000元不能进入天天特价'});
                        _that.goods.is_special_price = false;
                        return false;
                    }
                }
                if (_that.localIds.length > 9){
                    _that.$dialog.alert({mes: '藏品图片不能多于9张'});
                    return false;
                }
                _that.uoloadimg();
                _that.is_public=false;
            },
            //点击选择图片
            addImg() {
                var _that =this;
                if(_that.localIds.length>=9){
                    _that.$dialog.alert({mes: '图片数量限制9张内'})
                    return false;
                }
                var _formdata =new FormData();
                _formdata.append('token',storeWithExpiration.get('token'))
                //检查手机号码是否验证
                _that.$axios.post('/up/mobileisorcheck',_formdata,{timeout:200000}).then(function(response) {
                    if(response.status=200){
                        return  response.data;
                    }
                }).then(function(response){
                    return response.data;
                }).then(function(response){
                    if(_that.goods.contact_mobile == '' || _that.goods.contact_mobile == null){
                        _that.$dialog.notify({
                            mes: '请验证手机后再上传拍品',
                            timeout: 1000,
                            callback: function () {
                                //跳转到手机验证页面verificationmobile
                                //window.location.href= urldomain+"/verificationmobile";
                                _that.$router.push({ name: 'phone_number_link',query:{type:1}})
                            }
                        });
                        return false;
                        //跳转到验证手机页面
                    }

                    wx.ready(function(){
                        var count = 9 - _that.localIds.length || 9;
                        //绑定点击事件
                        wx.chooseImage({
                            count: count, // 默认9
                            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                            success: function (res) {
                                var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片

                                for(var i = 0;i < localIds.length; i++) {
                                    wx.getLocalImgData({
                                        localId:  localIds[i], // 图片的localID
                                        success: function (res) {
                                            localIds[i] = res.localData; // localData是图片的base64数据，可以用img标签显示
                                        }
                                    });
                                }
                                _that.localIds  =_that.localIds.concat(localIds);
                                _that.imgnum = _that.localIds.length;
                            },
                            fail: function (res) {
                                alert(JSON.stringify(res));
                            }
                        })
                    })
                    wx.error(function (res) {

                    })
                }).catch(function (error) {
                    console.log(error)
                    _that.$dialog.alert({mes: '数据加载异常！请稍后重试'})
                    return false;
                });
            },
            //上传图片取得seridsid
            uoloadimg(){

                var _that = this;
                var i = 0, length = _that.localIds.length;

                function upload() {
                    wx.uploadImage({
                        localId: _that.localIds[i].toString(),
                        success: function (res) {
                            i++;
                            //document.getElementById('hiddenserid').innerHTML += '<div id="' + i + '">' + '<input type="hidden" name="servers[]" value="'+res.serverId+'"/>' + '</div>';
                            _that.sersverids.push(res.serverId);
                            if (i < length) {
                                upload();
                            }else{
                                //提交到后台
                                _that.sendserves();

                                _that.isDisable = true
                                setTimeout(() => {
                                    _that.isDisable = false
                                }, 3600000)
                            }
                        },
                        fail: function (res) {
                            alert(JSON.stringify(res));
                        }
                    });
                }
                upload();
            },
            sendserves(){

                var _that =this;
                var dd =new FormData();
                if(window.storeWithExpiration.get('token') == ''){
                    _that.$dialog.alert({mes: '登陆过期，请重新登陆！'})
                    //weixinlogin();
                }
                _that.goodstoserver =  JSON.stringify(_that.goods);

                dd.append('goods',_that.goodstoserver)
                dd.append('token',storeWithExpiration.get('token'))
                dd.append('MEDIA_ID', _that.sersverids)

                _that.$dialog.loading.open('文件正在上传远程服务器，请稍后...');
                _that.$axios.post('/up/loading',dd,{timeout:200000}).then(function(response) {
                    if(response.status=200){
                        return  response.data;
                    }
                }).then(function(response){
                    _that.$dialog.loading.close();
                    var goods_id = response.goods_id;
                    var alertstr = "发布成功";
                    if(response.code==2000){
                        if(0 == _that.is_authentication){
                            alertstr ="已发布成功";
                        }
                        _that.$dialog.toast({
                            mes:alertstr,
                            timeout: 1500,
                            icon: 'success',
                            callback:function(){
                                _that.localIds = [];
                                _that.sersverids = [];

                                //跳转到藏品详情
                                _that.$router.push({path: '/index/' + goods_id})
                            }
                        });
                    }else{
                        _that.$dialog.confirm({mes: response.message})
                    }
                }).catch(function (error) {
                    _that.$dialog.loading.close();
                    _that.$dialog.toast({
                        mes:"上传失败服务器异常 请稍后重试",
                        timeout: 1500,
                        icon: 'error'
                    });
                    //openCustomConfrim(_that,'',"网络问题，请稍后重试！"+JSON.stringify(error))
                });
                return false;
            },
            //删除数组图片lianjie
            removeimgs(index){
                //点击清除divs
                var _that = this;
                var imgid = index;
                var arr = _that.localIds;
                arr.splice(imgid, 1);
                _that.imgnum = _that.localIds.length;
            }
        }
    }
</script>