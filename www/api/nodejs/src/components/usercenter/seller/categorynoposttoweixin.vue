<template>
    <div id="category">
        <div id="wptCategory" class="wptCategory">
            <div class="wptMask"></div>
            <div class="categoryBox">
                <div class="categoryTitle">
                    <span class="title">分类 <label>请谨慎选择，切勿跨品类</label></span>
                    <div class="finish">完成</div>
                </div>
                <div class="categoryList">
                    <div class="categoryItem" :data-id="item.id" v-for="(item ,index) in categoryItem"
                         :data-title="item.name">
                        <div :class="['category'+ (index+1)]">{{item.name}}<span>{{item.mobile_name}}</span></div>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wptEndtime">
            <div class="wptMask"></div>
            <div class="categoryBox">
                <div class="categoryTitle endTimeListclose">
                    <span class="title">截止时间 <label>请选择拍卖截止时间</label></span>
                    <div class="finish">完成</div>
                </div>
                <div class="endTimeList">
                    <dl>
                        <dt>{{toady}}(<em>今天</em>)</dt>
                        <dd v-for="(item,index) in timeItem[0]" :value="item.unixtime"
                            :tips="[toady +'(今天)'+ item.string]">{{item.string}}
                        </dd>
                        <div style="clear:both;padding:0;"></div>
                    </dl>
                    <dl>
                        <dt>{{tomorrow}}(<em>明天</em>)</dt>
                        <dd v-for="(item,index) in timeItem[1]" :value="item.unixtime"
                            :tips="[tomorrow +'(明天)'+ item.string]">{{item.string}}
                        </dd>
                        <div style="clear:both;padding:0;"></div>
                    </dl>
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
                categoryItem: {},
                timeItem: [{}, {}],
                tomorrow: '',
                today: ''
            }
        },
        mounted: function () {
            this.getuploadindex();
        },
        methods: {
            returnvalue: function (response) {
                this.$emit('increment', response)
            },
            getuploadindex: function () {
                var __self = this;
                //提交到后台
                axios.get(decodeURIComponent(config.dev.env.default_domain_api)+'/up/index', {
                    params: {
                        token: storeWithExpiration.get('token'),
                    },
                    timeout: 10000
                }).then(function (response) {
                    return response.data
                }).then(function (response) {
                    return response.data
                }).then(function (response) {
                    __self.categoryItem = response.name,
                        __self.tomorrow = response.tomorrow,
                        __self.toady = response.toady,
                        __self.timeItem = response.astime
                    __self.returnvalue(response)
                })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            isEmptyObject: function (obj) {
                var name;
                for (name in obj) {
                    return false;
                }
                return true;
            },
            weixin_product: function () {
                var _that = this;
                if ($.os.android || !$.os.ios) {
                    var domain = location.href.split('#')[0];
                    axios.get('/makesign?url=' + encodeURIComponent(domain)).then(function (response) {
                        if (response.status == '200') {
                            return response;
                        }
                    }).then(function (response) {
                        wx.config({
                            debug: false,
                            appId: "wxdabbc66245346dda",
                            timestamp: String(response.data.timestamp),
                            nonceStr: response.data.noncestr,
                            signature: response.data.signature,
                            jsApiList: [
                                "closeWindow",
                                'getNetworkType',
                                "chooseImage",
                                "uploadImage",
                                "previewImage"
                            ]
                        });
                        wx.ready(function () {
                            wx.checkJsApi({
                                jsApiList: [
                                    'getNetworkType',
                                    'uploadImage',
                                    'chooseImage',
                                    'previewImage'
                                ],
                                success: function (res) {
                                    //{"errMsg":"checkJsApi:ok","checkResult":{"uploadImage":true,"imagePreview":true}}
                                    console.log(res)
                                }
                            });
                        });

                        wx.error(function (res) {
                            console.log(res)
                        })


                    }).catch(function (error) {
                        alert("请重试")
                    });
                }

            }
        },
        computed: {}
    }
</script>

<style scoped>
    .categoryBox {
        position: fixed;
        background: #ededed;
        bottom: 0;
        width: 100%;
        margin: auto;
        left: 0;
        right: 0;
        paddging: 2% 0;
        max-width: 640px;
        z-index: 1999;
    }

    .categoryBox .categoryTitle {
        width: 100%;
        background: #00cccc;
        height: 50px;
        line-height: 50px;
        display: table;
    }

    .categoryBox .categoryTitle span {
        font-szie: 16px;
        margin-left: 4%;
        color: #fff;
        float: left;
    }

    .categoryBox .categoryTitle .finish {
        font-szie: 16px;
        min-width: 65px;
        color: #fff;
        float: right;
    }

    .endTimeList {
        width: 100%;
        background: #fff;

    }

    .endTimeList dl dt {
        width: 100%;
        clear: both;
        color: #999;
        font-size: 15px;
        text-indent: 10px;
        padding: 10px;
    }

    .endTimeList dl dd {
        float: left;
        width: 28%;
        border: 1px solid #ccc;
        border-radius: 2px;
        text-align: center;
        line-height: 40px;
        height: 40px;
        margin: 0px 5px 10px 8px;
    }

</style>
