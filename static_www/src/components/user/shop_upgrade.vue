<template>
    <div style="padding-top: 1rem;">
        <div v-on:click="cancelShare()" style=' position: fixed; height: 100%;top: 0; display: none; z-index: 99999;' v-show="shareShow3">
            <img style="width: 100%;" src="@/assets/images/upshop3.png" />
        </div>
        <div v-on:click="cancelShare()" style=' position: fixed; height: 100%;top: 0; display: none; z-index: 99999;' v-show="shareShow4">
            <img style="width: 100%;" src="@/assets/images/upshop4.png" />
        </div>
        <div v-on:click="cancelShare()" style=' position: fixed; height: 100%;top: 0; display: none; z-index: 99999;' v-show="shareShow5">
            <img style="width: 100%;" src="@/assets/images/upshop5.png" />
        </div>
        <!--顶部导航-->
        <yd-navbar bgcolor="#333" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
           <!-- <p style="font-size: .3rem;color: #fff;" slot="center">店铺推广升级</p>-->
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>
        </yd-navbar>
        <!--群发标题-->
        <div class="group_hair">
            <div style="height:1px; background:#bebdbb; width: 100%;"></div>
            <!--头像-->
            <div class="group_hair_img">
                <img  :src="user_data.head_pic">
            </div>
            <!--个人信息-->
            <div class="group_hair_title">
                <p><strong>{{user_data.nick_name}}</strong></p>
                <p class="group_hair_title_e">店铺推广享受便捷服务！</p>
            </div>
            <!--点击发布-->
            <div class="group_hair_button">{{user_data.store_name}}</div>

        </div>
        <!--店铺的个人信息-->
        <div class="my_shop_po fix">
            <div  class="my_shop_po_title">
                <p>选择服务套餐<span>等级越高，权限越高</span></p>
            </div>
            <ul>
                <template v-for="item in viplsit">
                    <div class="my_shop_po_active" v-on:click="wxshare(item.store_level_id)">
                        <!--<img src="@/assets/images/photo.png">-->
                        <!--<img :src="item.store_img"  v-on:click="payupshop(item.store_level_id,item.store_cost)">-->
                        <img :src="item.store_img" >
                        <p style="margin-bottom: .1rem">{{  item.store_name }}</p>
                        <p>  ¥{{  item.store_cost }}</p>
                    </div>
                </template>


            </ul>
        </div>
        <!--功能介绍-->
        <!--店铺推广介绍-->
        <div class="shopup_liberty">
            <div class="title">
                    <p>店铺推广特权</p>
            </div>
            <div class="content">
                <table cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <th>星级特权</th>
                        <th>普通用户</th>
                        <th>普通推广</th>
                        <th>专业推广</th>
                        <th>高级推广</th>
                    </tr>

                    <tr>
                        <th>店铺推广标识</th>
                        <th>无</th>
                        <th>有</th>
                        <th>有</th>
                        <th>有</th>

                    </tr>
                    <tr>
                        <th><b>商品管理</b></th>
                    </tr>
                    <tr>
                        <th>在线商品</th>
                        <th>5件</th>
                        <th>20件</th>
                        <th>50件</th>
                        <th>100件</th>

                    </tr>
                    <tr>
                        <th>在线时间</th>
                        <th>36小时</th>
                        <th>60小时</th>
                        <th>72小时</th>
                        <th>96小时</th>
                    </tr>
                    <tr>
                        <th>下架编辑</th>
                        <th>×</th>
                        <th>√</th>
                        <th>√</th>
                        <th>√</th>
                    </tr>
                    <tr>
                        <th>下架保留</th>
                        <th>15天</th>
                        <th>30天</th>
                        <th>50天</th>
                        <th>90天</th>
                    </tr>
                    <tr>
                        <th>议价</th>
                        <th>×</th>
                        <th>√</th>
                        <th>√</th>
                        <th>√</th>
                    </tr>
                    <tr>
                        <th><b>推广管理</b></th>
                    </tr>
                    <tr>
                        <th>赠送粉丝</th>
                        <th>100人</th>
                        <th>600人</th>
                        <th>1500人</th>
                        <th>3000人</th>
                    </tr>
                    <tr>
                        <th>摇粉丝</th>
                        <th>0次/天</th>
                        <th>1/天</th>
                        <th>2次/天</th>
                        <th>3次/天</th>
                    </tr>
                    <tr>
                        <th>群发次数</th>
                        <th>0次/天</th>
                        <th>1次/天</th>
                        <th>2次/天</th>
                        <th>3次/天</th>
                    </tr>
                    <tr>
                        <th>天天特价</th>
                        <th>×</th>
                        <th>√</th>
                        <th>√</th>
                        <th>√</th>
                    </tr>
                    <tr>
                        <th>每日推荐</th>
                        <th>×</th>
                        <th>×</th>
                        <th>√</th>
                        <th>√</th>

                    </tr>
                    <tr>
                        <th>头条推广</th>
                        <th>×</th>
                        <th>×</th>
                        <th>×</th>
                        <th>√</th>
                    </tr>
                    <tr>
                        <th><b>资产管理</b></th>
                    </tr>
                    <tr>
                        <th>提现金额</th>
                        <th>3万/次</th>
                        <th>10万/次</th>
                        <th>30万/次</th>
                        <th>50万/次</th>
                    </tr>
                    <tr>
                        <th>到账周期</th>
                        <th>7天</th>
                        <th>5天</th>
                        <th>3天</th>
                        <th>2天</th>
                    </tr>
                    <tr >
                        <th>体现方式</th>
                        <th>微信</th>
                        <th style="padding-left: 0; font-size: .2rem">微信/银行卡</th>
                        <th style=" padding-left: 0; font-size: .2rem">微信/银行卡</th>
                        <th style=" padding-left: 0; font-size: .2rem">微信/银行卡</th>
                    </tr>
                    <tr>
                        <th><b>售后服务</b></th>
                    </tr>
                    <tr>
                        <th>服务周期</th>
                        <th>1年</th>
                        <th>1年</th>
                        <th>1年</th>
                        <th>1年</th>
                    </tr>
                    <tr>
                        <th>服务类型</th>
                        <th>普通售后</th>
                        <th>专业售后</th>
                        <th>高级售后</th>
                        <th>至尊售后</th>
                    </tr>
                    <tr>
                        <th>在线回复</th>
                        <th>48小时</th>
                        <th>36小时</th>
                        <th>24小时</th>
                        <th>12小时</th>
                    </tr>
                    <tr>
                        <th>专属客服</th>
                        <th>无</th>
                        <th>有</th>
                        <th>有</th>
                        <th>有</th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--建议-->
        <div class="shopup_liberty">
            <div class="title">
                升级流程
            </div>
            <div class="content"> 1.根据您的需求选择合适的服务。
                <br> 2.支付店铺推广费用并且确认提交(目前在线支付仅支持微信支付，其他支付方式请联系专属客服顾问)。
                <br> 3.官方资料审核，开通店铺推广服务。
            </div>
        </div>
        <!--建议-->
        <div class="shopup_liberty">
            <div class="title">
                升级须知
            </div>
            <div class="content"> 1.付费升级成功后，即可享受新的店铺推广权益。
                <br> 2.店铺推广权益有效期为自升级成功日起的一整年。
                <br> 3.店铺推广升级为在线服务产品，一经升级无法退还、转让，敬请理解。
                <br> 4.升级艺品芳华店铺推广，即表示同意
                <span style="color: #af773e"> <router-link to="/user/agreement">《艺品芳华VIP店铺服务协议》</router-link></span>。
            </div>
        </div>
        <!--建议-->
        <div class="shopup_liberty">
            <div class="title">
                特别提醒
            </div>
            <div class="content"> 1.艺品芳华为电商服务平台，无法为所有店铺推广提供鉴定、估价，私下交易的服务要求。
                <br> 2.免费体验店铺并升级为店铺推广，可获得相应的服务权限，但无法保证升级成店铺推广就一定成交。
                <br> 3.艺品芳华所匹配的粉丝均是通过网络平台投放广告获得，无法100%保证其质量。
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {CellGroup, CellItem} from 'vue-ydui/dist/lib.rem/cell';
    /* 使用px：import {CellGroup, CellItem} from 'vue-ydui/dist/lib.px/cell'; */
    import {mapState} from 'vuex';
    Vue.component(CellGroup.name, CellGroup);
    Vue.component(CellItem.name, CellItem);

    export default {
        data() {
            return {
                viplsit : [],
                userdata : '',
                shareShow3:false,
                shareShow4:false,
                shareShow5:false,

            }
        },
        mounted: function () {
          this.showData();
        },
        methods:{
            wxshare:function(upid){
                if(upid == 3){
                    this.shareShow3 = true
                }else if(upid == 4){
                    this.shareShow4 = true
                }else if(upid == 5){
                    this.shareShow5 = true
                }

            },
            cancelShare:function(){
                this.shareShow3 = false;
                this.shareShow4 = false;
                this.shareShow5 = false;
            },
            goback () {
                this.$router.go(-1)
            },
            showData(){
                var _that = this;
                var token = storeWithExpiration.get('token');
                var url = "/users/store_level/?token=" +token;
                _that.$axios.get(url).then(function(response) {
                    if (response.status == 200){
                        for (var i=0;i<response.data.data.length;i++){
                            _that.viplsit.push(response.data.data[i])
                        }
                        _that.userdata = response.data.user_data
                    }
                }).catch(function(error) {

                });
            },
            payupshop(upid,money){
                var _that = this;
                if (upid <= _that.userdata.store_level_id){
                    _that.$dialog.alert({mes: '您的等级大于或等于您当前登记，请重试'});
                    return false;
                }
                _that.$router.push({ name: 'shopuppay_link', query: { upid: upid,money:money }})
            }
        },
        computed:mapState({
            user_data(state){
                if(this.$weipai.isEmptyObject(state.menuItems)){
                    this.$store.dispatch("getuserinfo");
                }
                return state.menuItems
            }
        }),
    }
</script>
<style scoped>
    /*个人信息*/
    .group_hair{width: 100%; height: 1.1rem; background: #333; }
    .group_hair_img{ width: 1rem; height: 1rem; margin-left: .1rem;float: left;}
    .group_hair_img img{ height: .8rem; width: .8rem; margin: .1rem; border-radius: .1rem;}
    .group_hair_title{ float: left;color: #fff; text-align: left; margin-top: .1rem; margin-left: .05rem;}
    .group_hair_title p{ margin-top: .3rem; font-size: .3rem;}
    .group_hair_title_e{ color:#bebdbb;}
    .group_hair_button{ font-size: .3rem; line-height: .65rem; width: 1.8rem;  height: .65rem; background:#af773e; float: right;margin-right: .2rem; margin-top:.22rem; color: #fff;}
    /*升级类型*/
    .navbar-bottom-line-color:after {border-color: #333 !important;}
    .my_shop_po{ width: 100%; height: auto; background: #fff; padding: 0rem .2rem 0 .2rem; margin-bottom: .1rem;}
    .my_shop_po_title{text-align: left; height: .8rem; line-height: .8rem; }
    .my_shop_po_title p{ font-size: .3rem;}
    .my_shop_po_title p span{ font-size: .25rem; color: #bebdbb; margin-left: .1rem;}
    .my_shop_po ul li { width: 23%; margin-right: 2%; border-radius: .2rem; float:left; color: #333; padding: .3rem;  border: 1px solid #eee; height: 2.6rem;}
    .my_shop_po ul li img { width: .9rem; height: .9rem; margin-bottom: .15rem; margin-top: .2rem;}
    .my_shop_po_active{ background: rgba(175,119,62,.2); color:#af773e; border: 1px solid #af773e;width: 31%; margin-right: 2%; border-radius: .2rem; float:left;  padding: .3rem;  height: 2.6rem; }
    .my_shop_po_active img { width: 1.2rem; height: 1.2rem; margin-bottom: .1rem; }
    /*店铺推广介绍*/
    .shopup_liberty{ background: #fff; padding: .2rem .2rem; margin-bottom: .1rem;}
    .title{ color:#af773e;width: 100%; text-align: left; height: .5rem; margin-bottom: .15rem;  border-bottom: 1px solid #eee;}
    .content{ text-align: left;}
    .content>table {    width: 100%;}
    .content>table tr th{ font-size:.25rem;background: rgba(238,238,238,.8); margin: .05rem .05rem 0 0; padding-left: .15rem; height: .45rem; line-height: .5rem; width: 19%;display: inline-block}
</style>