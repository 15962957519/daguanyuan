<template>
    <div id="eproduct">
        <yd-navbar title="在线数量" :fixed="true">
            <router-link  to="#" slot="left">
                <yd-navbar-back-icon  @click.native="goback" >返回</yd-navbar-back-icon>
            </router-link>
            <router-link to="#" slot="right" @click.native="handleClick" >
                <yd-navbar-next-icon>发布</yd-navbar-next-icon>
            </router-link>
        </yd-navbar>
        <div class="eblankheader" ></div>
        <!--上传图片-->
        <yd-grids-group>
            <div class="upload_imgeproduct">
                <p>上传图片 {{ imgnum }}/9</p>
                <div class="up_div" >
                    <div class="up_div_one" v-for="(item, index)  in goods.newedimage" :id="index">
                        <img :src="item.image_url" width="80px"/>
                        <img class="xianzhi" src="@/assets/images/x.png" @click="removeimgs(index)">
                    </div>

                    <div class="up_div_one" v-for="(item, index)  in localIds" :id="index" v-if="localIds.length">
                        <img :src="item" width="80px"/>
                        <img class="xianzhi" src="@/assets/images/x.png" @click="removeimgl(index)">
                    </div>
                    <div class="up_div_two" @click.stop="addImg($event)"></div>
                </div>

            </div>
        </yd-grids-group>

        <!--描述名称-->
        <yd-cell-group class="editproductcontent">
            <yd-cell-item>
                <span slot="left">藏品名称：</span>
                <input slot="right" type="text" placeholder="请输入名称" v-model="goods.goods_name" maxlength="30">
            </yd-cell-item>
            <!--文本描述-->

            <textarea placeholder="请输入描述" maxlength="100" v-model="goods.goods_content" class="textacss"></textarea>

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
                    <option :value="0">请选择分类</option>
                    <option v-for="item in cateres" :value="item.id">{{ item.name }}</option>

                </select>
            </yd-cell-item>

            <yd-cell-item>
                <span slot="left">藏品价格：</span>
                <input slot="right" type="text" regex="^\d{5,12}$" placeholder="请输入金额" v-model="goods.start_price">
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
                <yd-switch  color="#b58352"slot="right" v-model="goods.is_special_price"></yd-switch>
            </yd-cell-item>
            <yd-cell-item  @click.native="yijia">
                <span slot="left">议价：</span>
                <template  v-if="store_level==1 && is_authentication==0">
                    <yd-switch  color="#b58352" slot="right"  :disabled=true  v-model="goods.is_talk_price"></yd-switch>
                </template>
                <template v-else>
                    <yd-switch color="#b58352"  slot="right"   v-model="goods.is_talk_price"></yd-switch>
                </template>
            </yd-cell-item>
        </yd-cell-group>
        <!--按钮-->
        <yd-button-group>
            <yd-button style="background-color: rgb(175, 119, 62);" size="large" @click.native="handleClick" type="danger" >点击发布</yd-button>
        </yd-button-group>

        <!--按钮-->
        <!--<div class="submit" @click.stop="handleClick"></div>-->
        <div class="specheight"></div>
    </div>
</template>
<style scoped>
    #eproduct  .editproductcontent{
        margin-bottom: 30px;
    }
    #eproduct  .eblankheader{margin-top: 1rem;}
    /*.yd-grids-group{margin-top:1rem;}*/
    #eproduct .upload_imgeproduct{width: 100%; background:#fff;margin-top:.1rem;}
    #eproduct .upload_imgeproduct p{ text-align:left; margin-left:.1rem; height:.8rem; line-height:.8rem;}
    #eproduct .upload_imgeproduct ul{background:#fff;}
    #eproduct .upload_imgeproduct ul li{ width:32%;height:2rem; border:1px solid #eee; margin-left:1%;margin-bottom:.1rem; background:#fff; float:left; }
    #eproduct .yd-step-content{ background:#fff; }
    #eproduct .yd-cell-box{ margin-top:.1rem;}
    #eproduct  .yd-btn-warning{padding:.3rem;}
    #eproduct .yd-btn-block{ padding:.3rem; }
    #eproduct .yd-cell-item{ margin-top:.1rem; }
    #eproduct .up_div{
        margin-left: 10px;
    }
    #eproduct .up_div_one{    float: left;
        width: 80px;
        margin-right: 5px;
        margin-bottom: 5px;
        height: 80px;
        position: relative;
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
    #eproduct .up_div_two{float: left; width: 80px;height:80px;border: 1px solid #ccc; background:url(../../../static/upload.jpg) no-repeat;background-size: cover}
    #eproduct .xianzhi{ position: absolute; top: .1rem ; right: 0.1rem;width: .5rem;}
    #eproduct .textacss{
        width: 100%;
        height: 1.5rem;
        border: 0;
        font-size: .3rem;
        padding: .3rem;
        border-bottom: 1px solid #eee;
    }
    #eproduct .submit{
        width:100%;
        height: 48px;
        background-position: center center;
    }
    #eproduct .specheight{
        height:40px;
    }
    #eproduct .specheightrem{
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
                datetime7:'2012-03-19 15:13',
                cateres:[],
                localIds:[],
                sersverids:[],
                mobile_validated:'',
                is_authentication: 0,
                goods_id:'',
                imgnum:0,
                store_level:1,
                goods:{
                    newedimage:[],
                    goods_name:'',
                    goods_content :"",
                    datetimeup:'',
                    starttime:'',
                    endtime:'',
                    start_price:"",
                    cat_id: 0,
                    is_free_shipping: false,//包邮
                    enableReturn: false,//包退
                    is_special_price: false,//同步到捡漏
                    is_talk_price: false,
                    contact_mobile:0
                },
            }
        },

        mounted:function(){
            this.category();
            this.goodsdemail();
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
                    //console.log(res);
                    if (res.status == 200){
                        //console.log(res.data.data.length);
                        for (var i=0;i<res.data.data.name.length;i++){
                            _that.cateres.push(res.data.data.name[i]);
                        }
                        //_that.timestampToTime()
                        _that.goods.starttime = res.data.data.userinfo.astime;
                        console.log(_that.goods.starttime)
                        _that.goods.endtime = res.data.data.userinfo.endDatec;
                        console.log(_that.goods.endtime)
                        _that.is_authentication = res.data.data.userinfo.is_authentication;
                        _that.goods.contact_mobile = res.data.data.userinfo.mobile;
                        _that.store_level = res.data.data.userinfo.store_level;
                    }

                    console.log(_that.localIds,666666)

                }).catch(function (error) {
                    console.log(error)
                })
            },

            //获取拍品信息
            goodsdemail(){
                var _that =this;
                var goods_id = _that.$route.query.goods_id;
                var token = window.storeWithExpiration.get('token');
                var url = '/up/editloading?goods_id=' + goods_id + "&token=" + token;
                _that.$axios.get(url).then(function (res) {
                    return res.data.data;
                }).then(function (res) {
                    //console.log(res)
                    _that.goods.goods_name = res.goods_edit.goods_name;
                    _that.goods.goods_content = res.goods_edit.goods_content;
                    _that.goods.datetimeup = _that.timestampToTime(res.goods_edit.endTime);
                    console.log(_that.goods.datetimeup)
                    _that.goods.start_price = res.goods_edit.start_price;
                    _that.goods.cat_id = res.goods_edit.cat_id;
                    _that.goods.goods_id = res.goods_edit.goods_id;
                    for (var i=0;i<res.goods_images.length;i++){
                        _that.goods.newedimage.push(res.goods_images[i])
                    }
                    _that.imgnum = res.goods_images.length;
                    if (res.goods_edit.is_free_shipping == 1){
                        _that.goods.is_free_shipping = true;
                    }else{
                        _that.goods.is_free_shipping = false;
                    }
                    if (res.goods_edit.is_distribute == 1){
                        _that.goods.is_distribute = true;
                    }else{
                        _that.goods.is_distribute = false;
                    }
                    if (res.goods_edit.enableReturn == 1){
                        _that.goods.enableReturn = true;
                    }else{
                        _that.goods.enableReturn = false;
                    }

                    if (res.goods_edit.is_talk_price == 1){
                        _that.goods.is_talk_price = true;
                    }else{
                        _that.goods.is_talk_price = false;
                    }
                    //console.log(res.goods_edit.goods_content)


                }).catch(function (error) {
                    console.log(error)
                })
            },

            //立即发布
            handleClick() {
                var _that = this;
                var shopname = _that.goods.goods_name;//拍品名称
                var desc = _that.goods.goods_content;//描述信息
                //console.log(_that.goods.cat_id);
                //console.log(_that.goods.is_free_shipping)//包邮
                //console.log(_that.goods.enableReturn)//包退
                //console.log(_that.goods.is_special_price)//同步到捡漏
                _that.imgnum = _that.localIds.length + _that.goods.newedimage.length;
                if (_that.imgnum <= 0){
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
                if (_that.imgnum >= 9){
                    _that.$dialog.alert({mes: '藏品图片不能多于9张'});
                    return false;
                }

                _that.uoloadimg();

            },
            //点击选择图片
            addImg() {
                var _that =this;
                _that.imgnum = _that.localIds.length + _that.goods.newedimage.length;
                //console.log(_that.imgnum);
                if(_that.imgnum >= 9){
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
                    if(0 == _that.goods.contact_mobile){
                        _that.$dialog.notify({
                            mes: '请验证手机后再上传拍品',
                            timeout: 1000,
                            callback: function () {
                                //跳转到手机验证页面verificationmobile
                                _that.$router.push({ name: 'phone_number_link',meta: {title: '手机验证'}})
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
                                console.log(_that.localIds)
                                _that.imgnum = _that.localIds.length + _that.goods.newedimage.length;
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
                if (length > 0){
                    function upload() {
                        wx.uploadImage({
                            localId: _that.localIds[i].toString(),
                            success: function (res) {
                                i++;

                                _that.sersverids.push(res.serverId);
                                if (i < length) {
                                    upload();
                                }else{
                                    //提交到后台
                                    _that.sendserves();
                                }

                            },
                            fail: function (res) {
                                alert(JSON.stringify(res));
                            }
                        });
                    }
                    upload();
                }else{
                    //提交到后台
                    _that.sendserves();
                }


            },
            sendserves(){

                var _that =this;
                var dd =new FormData();
                if(window.storeWithExpiration.get('token') == ''){
                    _that.$dialog.alert({mes: '登陆过期，请重新登陆！'})
                    //weixinlogin();
                }
                _that.goodstoserver =  JSON.stringify(_that.goods);
                //_that.goods.newedimage = JSON.stringify(_that.newedimage);
                dd.append('goods',_that.goodstoserver)
                dd.append('token',storeWithExpiration.get('token'))
                dd.append('MEDIA_ID', _that.sersverids)
                //dd.append('goods_id', _that.goods_id)
                //dd.append('newedimage', _that.newedimage)

                _that.$dialog.loading.open('文件正在上传远程服务器，请稍后...');
                _that.$axios.post('/up/editloading',dd,{timeout:200000}).then(function(response) {
                    if(response.status=200){
                        return  response.data;
                    }
                }).then(function(response){
                    var goods_id = response.goods_id;
                    _that.$dialog.loading.close();
                    var alertstr = "修改成功";
                    if(response.code==2000){
                        if(0 == _that.is_authentication){
                            alertstr ="已修改成功";
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
            removeimgs(obj){
                //点击清除divs
                //alert(obj)
                var _that = this;
                var imgid = obj;
                var arr = _that.goods.newedimage;
                arr.splice(imgid, 1);
                _that.imgnum = _that.localIds.length + _that.goods.newedimage.length;

            },
            removeimgl(index){
                var _that = this;
                var imgid = index;
                var arr = _that.localIds;
                arr.splice(imgid, 1);
                _that.imgnum = _that.localIds.length + _that.goods.newedimage.length;
            },
            timestampToTime(timestamp) {
                var  date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                var  Y = date.getFullYear() + '-';
                var  M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                var  D = date.getDate() + ' ';
                var  h = (date.getHours() < 10 ? '0'+(date.getHours()) : date.getHours()+1) + ':';
                var  m = (date.getMinutes() < 10 ? '0'+(date.getMinutes()) : date.getMinutes()+1);
                //var  s = date.getSeconds();
                return Y+M+D+h+m;
            },
        }
    }
</script>