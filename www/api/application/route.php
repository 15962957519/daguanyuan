<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//Route::domain('localhost:999', function () {
Route::domain('api.fanghua.jiuxintangwenhua.com', function () {
    /*Route::rule('', 'dataapi/Index/index');
    Route::rule('valid', 'dataapi/GetTokenByCode/valid');
    Route::rule('valid/responseMsg', 'dataapi/User/responseMsg');

    Route::rule('product/indexprolist', 'dataapi/product/indexprolist');


    Route::rule('product/indexpaimaiprolistmessage', 'dataapi/product/indexpaimaiprolistmessage');
    Route::rule('product/indexpaimaiprolistcase', 'dataapi/Index/indexpaimaiprolistCase');
    //统计分析
    Route::rule('product/statistics', 'dataapi/Index/indexpaimaiprolist');
    Route::rule('product/taskindex', 'dataapi/TaskIndex/index');


    Route::rule('product/index', 'dataapi/product/index');
    Route::rule('product/getProductsByUserid', 'dataapi/product/getProductsByUserid');
    Route::rule('product/getProductsById', 'dataapi/product/getProductsById');
    Route::rule('product/getproductbyid', 'dataapi/product/getProductById');
    Route::rule('product/getuserall', 'dataapi/product/getUserAll', 'get');
    Route::rule('product/uploadproduct', 'dataapi/product/uploadProduct', 'get');

    Route::rule('user/getshouquanccess_token', 'dataapi/User/getAccess_token', 'post');
    Route::rule('user/userinfo', 'dataapi/User/userinfo');
    Route::rule('user/getAccess_token', 'dataapi/GetTokenByCode/getToken');
    Route::rule('user/getAccess_token_p', 'dataapi/GetTokenByCode/pgettoken');
    Route::rule('user/userinfoall', 'dataapi/User/userinfoall');
    Route::rule('user/getshouquanccess_token', 'dataapi/User/getAccess_token', 'post');

    Route::rule('user/getJccess_token', 'dataapi/User/get_js_sdk_access_token', 'get');
    Route::rule('user/getsignature', 'dataapi/User/getsignature', 'get');
    Route::rule('user/msignature', 'dataapi/User/msignature', 'post');
    Route::rule('user/weixinnumber', 'dataapi/User/weixinNumber', 'post');
    Route::rule('user/userverifty', 'dataapi/User/userVerifty', 'post');
    Route::rule('user/message', 'dataapi/User/message', 'get');//用户消息

    Route::rule('user/userinfofocusall', 'dataapi/User/userinfoFocusAll', 'get');
    //二维码
    Route::rule('user/qrcode', 'dataapi/User/qrcode', 'get');
    Route::rule('user/getuserimage', 'dataapi/User/getUserImage', 'get');


    Route::rule('user/userverfityimgfront', 'dataapi/UserUpload/userverfityimgfront', 'post');
    Route::rule('user/userverfityimgback', 'dataapi/UserUpload/userverfityimgback', 'post');
    Route::rule('user/userverfityimghold', 'dataapi/UserUpload/userverfityimghold', 'post');
    Route::rule('user/userverfityimgsave', 'dataapi/UserUpload/userverfityimgsave', 'post');


    Route::rule('user/usergetverfityinfo', 'dataapi/User/userGetVerfityInfo', 'get');
    Route::rule('user/userlikeproduct', 'dataapi/User/userLikeProduct');
    Route::rule('user/userfoucs', 'dataapi/User/userFoucs');
    Route::rule('user/userfoucsfansan', 'dataapi/User/userfoucsfansan');
    Route::rule('user/unuserfoucs', 'dataapi/User/unuserFoucs');
    Route::rule('user/bondcheck', 'dataapi/User/bondCheck', 'get');
    Route::rule('user/singlebond', 'dataapi/User/singlebondCheck', 'get');


    Route::rule('up/index', 'dataapi/UserUpload/index');
    Route::rule('up/mobileisorcheck', 'dataapi/UserUpload/mobileisorcheck');
    Route::rule('up/pubindex', 'dataapi/UserUploadPublic/index');

    Route::rule('usercenterproduct/index', 'dataapi/UserCenterProduct/index');
    Route::rule('usercenterproduct/del', 'dataapi/UserCenterProduct/delate');
    Route::rule('usercenterproduct/indexsendmessage', 'dataapi/UserCenterProduct/indexsendmessage');

    Route::rule('up/loading', 'dataapi/UserUpload/loading');
    Route::rule('up/editloading', 'dataapi/UserUpload/editloading');
    Route::rule('useruploadfinance/loading', 'dataapi/UserUploadFinance/loading');
    Route::rule('useruploadfinance/save', 'dataapi/UserUploadFinance/save');
    Route::rule('makesign', 'dataapi/GetTokenByCode/makesign');
    Route::rule('getproductinfobygoodid', 'dataapi/UserCenterProduct/getProductinfoBygoodId');


    //出价信息
    Route::rule('/index/bidproudctbyid', 'dataapi/BidProduct/bidProudctById');
    Route::rule('/index/getproductbyid', 'dataapi/BidProduct/getProductById');



    //生成会员
    Route::rule('/liqipengindex', 'dataapi/UppIndex/index');
    Route::rule('/liqipengindex/bidproudctbyid', 'dataapi/UppIndex/bidProudctById');
    //Route::rule('liqipengindex/userlikeproduct', 'dataapi/UppIndex/userLikeProduct');


    Route::rule('liqipengindex/intendeduser', 'dataapi/UppIndex/IntendedUser');
    Route::rule('liqipengindex/uppcase', 'dataapi/UppCase/IntendedUser');


    //job列队
    Route::rule('jobqeue', 'dataapi/JobQeue/actionWithHelloJob', 'get');
    Route::rule('actionautolikegoodsforfanserer', 'dataapi/JobQeue/actionautoLikeGoodsForFanserer', 'get');
    Route::rule('autogivefanuser', 'dataapi/JobQeue/autoGiveFanUser', 'get');
    Route::rule('autogivefanuser', 'dataapi/JobQeue/autoGiveFanUser', 'get');
    Route::rule('testquene', 'dataapi/UppCase/testQuene', 'get');


    //会员图片信息上传
    Route::rule('adduser', 'dataapi/AddUser/uploadImg', 'post');


    //微信支付
    Route::rule('/weixin/payment', 'dataapi/WeixinPaymentIndex/index');
    Route::rule('/paymenting/notify', 'dataapi/PayNotify/index');
    Route::rule('/paymenting/isfinalorderpayment', 'dataapi/PayNotify/isfinalorderpayment');


    //发送短信
    Route::rule('/sendmoblievertifycode', 'dataapi/MobileMessage/index');
    Route::rule('/checkmcode', 'dataapi/MobileMessage/checkmobilecode');

    Route::rule('masssendmessage', 'dataapi/MassSendMessage/index');
    //我的订单
    Route::rule('user/getmyorder', 'dataapi/User/getMyOrder');

    Route::rule('article/index', 'dataapi/Article/index');
    Route::rule('article/detail', 'dataapi/Article/detail');
    Route::rule('article/addforwardcount', 'dataapi/Article/addForwardCount');
    Route::rule('product/indexjinpinprolist', 'dataapi/Product/indexjinpinprolist', 'get');
    Route::rule('product/indexprolistofjinpin', 'dataapi/Product/indexprolistofjinpin', 'get');
    //随机一件作品
    Route::rule('product/getproductrandone', 'dataapi/Product/productrandone', 'get');
    Route::rule('product/indexjextensionprolist', 'dataapi/Product/indexjExtensionprolist', 'get');
    Route::rule('product/addpreproduct', 'dataapi/AddPreProduct/uploadImg', 'post');


    //积分抽奖

    Route::rule('/integralottery', 'dataapi/Integralottery/index');
    Route::rule('/lottery', 'dataapi/Integralottery/lottery');
    Route::rule('/lotterymy', 'dataapi/Integralottery/lotterymy');


    // 积分商品区列表 积分商城首页接口
    Route::rule('/lotteryshop', 'dataapi/LtteryShop/index');

    //积分对象用户信息
    Route::rule('/lotteryshop/user', 'dataapi/LtteryShop/user');

    //兑换记录
    Route::rule('/lotteryshop/record', 'dataapi/LtteryShop/userrecord');

    //我的积分对象记录
    Route::rule('/lotteryshop/getmyorder', 'dataapi/LtteryShop/getMyOrder');

    //积分记录
    Route::rule('/lotteryshop/account_log', 'dataapi/LtteryShop/account_log');

    //产品详情
    Route::rule('/lotteryshop/prolist', 'dataapi/LtteryShop/prolist');
    //添加地址
    Route::rule('/lotteryshop/add_address', 'dataapi/UserAddress/add_address');

    //地址列表
    Route::rule('/lotteryshop/address_list', 'dataapi/UserAddress/address_list');
    //兑换详情
    Route::rule('/lotteryshop/cprolist', 'dataapi/LtteryShop/cprolist');
    Route::rule('/consureindex', 'dataapi/ConsureIndex/index');
    Route::rule('/modifytodaynogoods', 'dataapi/TaskIndex/modifytodaynogoods');
    Route::rule('/modifytodaynogoods25', 'dataapi/TaskIndex/modifytodaynogoods25');
    Route::rule('/randlikegoods', 'dataapi/TaskIndex/randlikegoods');
    Route::rule('/consumermessage', 'dataapi/TaskIndex/consumermessage');
    //消费喜欢 java调用
    Route::rule('/consumerlike', 'dataapi/ConsureIndex/consumerlike','post');
    Route::rule('/consumerfanserver', 'dataapi/ConsureIndex/consumerfanserver','post');
    Route::rule('/consumerbid', 'dataapi/ConsureIndex/consumerbid');
    Route::rule('/flushtomycat', 'dataapi/TaskIndex/indexExpireProduct','get');


    //采集商品区
    Route::rule('/collectiongoods', 'dataapi/CollectionGoods/index','post');*/
//==========================================================================

    Route::rule('/api', 'api/Index/index');            // 首页
    Route::rule('/news_lists', 'api/Index/new_list');  //新闻列表
    Route::rule('/news_detail', 'api/Index/news_detail','get');  //新闻详情
    Route::rule('/click_heart', 'api/Index/click_heart'); //商品区点赞
    Route::rule('product/indexpaimaiprolist', 'dataapi/product/indexpaimaiprolist');
    Route::rule('/virtua_getmore', 'api/Index/virtua_goods_get');   //首页虚拟作品加载
    Route::rule('/is_recommend', 'api/GoodStore/is_recommend'); //今日推荐区
    Route::rule('/well_chosen', 'api/GoodStore/well_chosen'); //精选

    Route::rule('/goods/goods_list', 'api/Goods/goods_list');  //商品区列表页
    Route::rule('/goods/goods_detail', 'api/Goods/goods_detail','get');  //商品区详情页
    Route::rule('/goods/publish_add', 'api/Goods/publish_add');  //加载商品区发布页 商品区分类
    Route::rule('/goods/publish', 'api/Identify/publish');  //上传客服问题图片
    Route::rule('/sellerservice', 'api/Identify/seller_service');  //卖家客服(提交)
    Route::rule('/goods/uploadGoods', 'api/Goods/uploadGoods');  //提交发布商品区
    Route::rule('/goods/publish_edit', 'api/Goods/publish_edit');  //编辑发布商品区
    Route::rule('/ajaxGetMore', 'api/Goods/ajaxGetMore');   //加载更多

    Route::rule('/goods/self_product_list', 'api/IntegralMall/self_product_list');  //自营商品区

    Route::rule('/day_special_price', 'api/IntegralMall/day_special_price');  //特价商品区

    Route::rule('/goods/integralMall', 'api/IntegralMall/integralMall'); //积分商城列表
    Route::rule('/goods/integra_detail', 'api/IntegralMall/integra_detail'); //积分商城详情页
    Route::rule('/goods/exchange_order', 'api/IntegralMall/exchange_order'); //积分兑换(积分订单)
    Route::rule('/exchange_log', 'api/IntegralMall/exchange_log'); //积分变更+ -


    Route::rule('/get_goods', 'api/Order/get_goods'); //点击收货
    Route::rule('/users/createOrder', 'api/Order/createOrder'); //生成订单
    Route::rule('/isOrderOwn', 'api/Order/isOrderOwn'); //该商品区是否生成自己的订单列表里
    Route::rule('/users/my_order', 'api/Order/my_order'); //我的订单
    Route::rule('/users/orderDetail', 'api/Order/orderDetail'); //订单详情
    Route::rule('/ajaxDelOrder', 'api/Order/ajaxDelOrder'); //删除订单
    Route::rule('/kdapi', 'api/Order/kdapi'); //物流接口kdapi

    //个人中心主页
    Route::rule('/userslevel', 'api/Users/curUserLevel'); //当前用户等级
    Route::rule('/users/index', 'api/Users/index'); //个人主页（我的
    Route::rule('/users/clearsign', 'dataapi/TaskIndex/clearSign'); //积分累计签到每月底更新清零
    Route::rule('/users/storeLevel', 'api/Users/storeLevel'); //当前店铺等级权限
    Route::rule('/users/personal', 'api/Users/personal'); //个人资料

    Route::rule('/users/fans_list', 'api/Users/fans_list'); //粉丝
    Route::rule('/users/care_list', 'api/Users/care_list'); //关注
    Route::rule('/users/create_care', 'api/Users/create_care'); //加关注
    Route::rule('/users/cancel_care', 'api/Users/cancel_care'); //取消关注
    Route::rule('/users/my_buying', 'api/Users/my_buying'); //我的抢购
    Route::rule('/users/shaked', 'api/Users/shaked'); //摇一摇
    Route::rule('/businessplace', 'api/Users/businessPlace'); //商户专区
    Route::rule('/collect_list', 'api/Users/collect_list'); //收藏
    Route::rule('/users/footprint', 'api/Users/footprint'); //足迹
    Route::rule('del_collectlist', 'api/Users/del_collectlist'); //收藏删除
    Route::rule('/users/delfootprint', 'api/Users/delfootprint'); //足迹删除
    Route::rule('/users/clearcollectlist', 'api/Users/clearcollectlist'); //收藏清除所有
    Route::rule('/users/clearfootprint', 'api/Users/clearfootprint'); //足迹清除所有


    //二维码
    Route::rule('user/qrcode', 'api/Users/qrcode', 'get');
    Route::rule('product/qrcode', 'api/Product/qrcode', 'get');
    Route::rule('user/getuserimage', 'api/Users/getUserImage', 'get');

    Route::rule('/users/new_product', 'api/Users/new_product'); //新品开拍


    Route::rule('/person_identify', 'api/Identify/new_person_identify'); //实名认证（个人） 新
    Route::rule('/company_identify', 'api/Identify/new_company_identify'); //实名认证（企业） 新
    Route::rule('/identify_person_edit', 'api/Identify/new_identify_person_edit'); //实名认证 修改(个人) 新
    Route::rule('/company_identify_edit', 'api/Identify/new_company_identify_edit'); //实名认证 修改(公司) 新


    Route::rule('/identify_edit', 'api/Identify/identify_edit'); //认证修改 （内容提交过）
    Route::rule('/updateStore', 'api/Identify/updateStore');  //提交店铺升级
    Route::rule('/checkworker', 'api/CheckWorker/index','post'); //员工查询
    Route::rule('/checkworkerresult', 'api/CheckWorker/staffresult','get'); //员工查询结果页面
    Route::rule('/signed', 'api/CheckWorker/signed'); //每日签到（行为）
    Route::rule('/signshow', 'api/CheckWorker/signedCount'); //每日签到页面
    Route::rule('/signgetpoints', 'api/CheckWorker/getPoints'); //累计签到领取
    Route::rule('/balancerecharge', 'api/BalanceRecharge/index'); //余额充值
    Route::rule('/rechargenotify', 'api/BalanceRecharge/notify'); //余额充值回调
    Route::rule('/applyTixian', 'api/BalanceRecharge/applyTixian'); //提交提现申请
    Route::rule('/tixianLog', 'api/BalanceRecharge/tixianLog'); //提现记录
    Route::rule('/rechargeLog', 'api/BalanceRecharge/rechargeLog'); //充值记录
    Route::rule('/users/balance', 'api/BalanceRecharge/balance'); //我的余额
    Route::rule('/balance_detail', 'api/BalanceRecharge/balance_detail'); //余额明细
    Route::rule('/cautionMoney', 'api/BalanceRecharge/cautionMoney'); //是否支付保证金
    Route::rule('/pay_money', 'api/BalanceRecharge/pay_money'); //各种支付金额

    //================================================================

    //卖家中心（seller_center）
    Route::rule('/goodstorelist', 'api/GoodStore/index'); //精选好店列表
    Route::rule('/users/store_level', 'api/GoodStore/store_level'); //店铺升级
    Route::rule('/my_publish_goods', 'api/GoodStore/goods_manager'); //商品区管理(列表)
    Route::rule('/del_paipin', 'api/GoodStore/del_paipin'); //删除我的商品区
    Route::rule('/down_paipin', 'api/GoodStore/down_paipin'); //下架我的商品区

    Route::rule('/storeIndex', 'api/MyStore/StoreIndex');  //我的店铺
    Route::rule('/storeindexpolymerization', 'api/MyStore/StoreIndexPolymerization');  //我的店铺首页各种参数
    Route::rule('/product/storeindexpolymerization', 'api/Product/StoreIndexPolymerization');  //我的店铺首页各种参数
    Route::rule('/storeDetail', 'api/MyStore/storeDetail');  //店铺详情
    Route::rule('/spread_goods', 'api/GoodStore/spread_goods'); //群发商品区列表
    Route::rule('/realsendweixinsms', 'api/MassSendMessage/realSendWeixinSMS'); //群发商品区(行为)
    Route::rule('/masssendmessage', 'api/MassSendMessage/index');//群发商品区

    //系统消息
    Route::rule('/newtipslist', 'api/NewTips/index');//系统消息
    Route::rule('/check_message', 'api/NewTips/check_message');//消息详情

    //发送短信
    Route::rule('/sendmoblievertifycode', 'api/MobileMessage/index','post');  //阿里短信
    Route::rule('/checkmcode', 'api/MobileMessage/checkmobilecode');   //检验短信

    //积分抽奖
    Route::rule('/lotteryGifts', 'api/Lottery/lotteryGifts'); //抽奖页面
    Route::rule('/selfLottery', 'api/Lottery/selfLottery'); //我的抽奖列表
    Route::rule('/lottryReduce', 'api/Lottery/lottryReduce'); //点击立即抽奖（积分减少）
    Route::rule('/lotteryLogs', 'api/Lottery/lotteryLogs');  //点击领取奖品（不领取无记录 除外）
    Route::rule('/applyReturn', 'api/ReturnGoods/applyReturn');  //申请退货
    Route::rule('/applyhandle', 'api/ReturnGoods/applyHandle');  //售后进度

    Route::rule('/individualStore', 'api/IndividualStore/individualStore');  //活动专场
    Route::rule('/user/getAccess_token', 'dataapi/GetTokenByCode/getToken'); //接收code 获取用户信息
    Route::rule('/get_accessToken', 'dataapi/GetTokenByCode/get_accessToken'); //获取accesstoken
    Route::rule('/makesign', 'dataapi/GetTokenByCode/makesign');
    Route::rule('/user/getAccess_token_p', 'dataapi/GetTokenByCode/pgettoken');
    //easywebchat 支付
    Route::rule('/jsapipay', 'api/JsapiPay/index');    //支付接口
    Route::rule('/paynotify', 'api/JsapiPay/notify');//异步通知
    Route::rule('/balance_pay', 'api/Pay/balance_pay');//余额支付
    Route::rule('/integral_pay', 'api/Pay/integral_pay');//积分支付

    //采集商品区
    Route::rule('/collectiongoods', 'dataapi/CollectionGoods/index','get');

    //拍卖模式接口
    Route::rule('/new_bid', 'api/Order/new_bid'); //议价
    Route::rule('/auction', 'api/Order/auction'); //竞拍 点击出价
    Route::rule('/makeorder', 'api/Order/makeorder'); //竞拍倒计时结束，自动生成订单
    Route::rule('/addAdress', 'api/Order/addAdress'); //添加微信地址到订单
    Route::rule('/orderCancel', 'api/Order/orderCancel'); //订单24小时不支付 自动取消
    Route::rule('/orderNotice', 'api/Order/orderNotice'); //订单最后一小时即将结束提醒
    Route::rule('/buyList', 'api/Order/buyList'); //我的买入列表
    Route::rule('/delBid', 'api/Order/delBid'); //截止且他人成交 此时删除我的出价

    //设置微信中控token
    Route::rule('/refresh/settoken', 'dataapi/Refresh/settoken');
    Route::rule('/refresh/setjsapi', 'dataapi/Refresh/setjsapi');
    //获取token
    Route::rule('/generateaccess_token', 'dataapi/Token/index');
    Route::rule('/generatejssdkaccess_token', 'dataapi/Token/get_js_sdk_access_token');

    Route::rule('/ordergeneration', 'dataapi/OrderGeneration/autoremindproductserver', 'get');

    //会员图片信息上传
    Route::rule('adduser', 'dataapi/AddUser/uploadImg', 'post');
    Route::rule('updateweixin', 'dataapi/UpdateWeixin/index', 'get');
    Route::rule('consumerfirstenterplatorm', 'dataapi/ConsureIndex/consumerfirstenterplatorm', 'get');

    Route::rule('/commonconsumer', 'dataapi/ConsureIndex/commonconsumer');
    Route::rule('/testsendmessageweixin', 'dataapi/ConsureIndex/testsendmessageweixin');

    //给首页藏品虚拟出价
    Route::rule('/chujia', 'dataapi/TaskIndex/chujia');
    Route::rule('tautogivefanuser', 'dataapi/TaskIndex/autoGiveFanUser');
    Route::rule('indexproductrandtouser', 'dataapi/TaskIndex/indexProductRandTouser');
    Route::rule('/modifytodaynogoods', 'dataapi/TaskIndex/modifytodaynogoods');

    //作品上传编辑
    Route::rule('up/index', 'dataapi/UserUpload/index');
    Route::rule('up/loading', 'dataapi/UserUpload/loading');
    Route::rule('up/editloading', 'dataapi/UserUpload/editloading');
    Route::rule('up/mobileisorcheck', 'dataapi/UserUpload/mobileisorcheck');

    //======================================================================================
    Route::rule('goods_category', 'api/Goods/categoryList');  //商品分类树

    Route::rule('/product/getbondlists', 'dataapi/Product/getbondlists');

    Route::rule('product/addclick', 'dataapi/product/addclick');
    //用户信息

    Route::rule('product/getuserinfo', 'api/product/getuserinfo');

    Route::rule('/liqipengindex/intendeduser', 'dataapi/UppIndex/IntendedUser');


    Route::rule('/collection', 'api/Goods/collection');  //百度收录商品详情链接

    //个人中心设置(修改昵称，头像)
    Route::rule('/personalsettings', 'api/Users/personalsettings');





});


Route::domain('w', function () {
    // 动态注册域名的路由规则
    Route::rule('/', 'mobile/Index/index');

});
// 注册路由到index模块的News控制器的read操作
//Route::rule('fabu/','index/index/');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/index', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/index', ['method' => 'get']]
    ],

    '[fabu]' => ['' => ['mobile/Product/fabu', ['method' => 'get|post']]],
    '[find]' => ['' => ['mobile/Product/find', ['method' => 'get|post']]],
];
