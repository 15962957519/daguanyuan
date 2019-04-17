import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)



const index = function (r) {
    require.ensure([], function () {
        r(require('@/components/index'))
    }, 'index')
}

const productlists = (resolve) =>{
import('@/components/productlists').then((module) =>{
        resolve(module)
    })
}

const goodsproductdetail = function (r) {
    require.ensure([], function () {
        r(require('@/components/detail'))
    }, 'goodsproductdetail')
}
//个人中心配置
const user = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/user'))
    }, 'user')
}
//发布配置
const publish = function (r) {
    require.ensure([], function () {
        r(require('@/components/publish/publish'))
    }, 'publish')
}
//底部导航
//我的钱包
const money_bag = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/money_bag'))
    }, 'money_bag')
}
//固定导航
const fixedtitle = function (r) {
    require.ensure([], function () {
        r(require('@/components/fixed/fixedtitle'))
    }, 'fixedtitle')
}
//Shopping Cart
const shoping_cart = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/shoping_cart'))
    }, 'shoping_cart')
}
//Commodity management
const commodity_management = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/commodity_management'))
    }, 'commodity_management')
}
//导入群发商品组件
const bulk_hair = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/bulk_hair'))
    }, 'bulk_hair')
}
//我的额店铺
const my_shop = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/my_shop'))
    }, 'my_shop')
}
const seller_shop = function (r) {
    require.ensure([], function () {
        r(require(  '@/components/user/seller_shop'))
    }, 'seller_shop')
}
//店铺升级
const shop_upgrade = function (r) {
    require.ensure([], function () {
        r(require(  '@/components/user/shop_upgrade'))
    }, 'shop_upgrade')
}
//摇一摇
const shake = function (r) {
    require.ensure([], function () {
        r(require(  '@/components/user/shake'))
    }, 'shake')
}
//营销中心
const marketing_center = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/marketing_center'))
    }, 'marketing_center')
}
//员工查询
const employee_inquiry = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/employee_inquiry'))
    }, 'employee_inquiry')
}
//客服中心
const customer_service = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/customer_service'))
    }, 'customer_service')
}
//Staff query results
const staff_index = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/staff_results/staff_index'))
    }, 'staff_index')
}
//class_index
const class_index = function (r) {
    require.ensure([], function () {
        r(require('@/components/classification/class_index'))
    }, 'class_index')
}
//set_user
const set_user = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/set_user'))
    }, 'set_user')
}
//fans
const fan_index = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/fans/fan_index'))
    }, 'fan_index')
}
//follow
const follow = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/fans/follow'))
    }, 'follow')
}
//authentication_index
const authentication_index = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/authentication/authentication_index'))
    }, 'authentication_index')
}
//Verification result
//提交认证中
const authenication_loding = function (r) {
    require.ensure([], function () {
        r(require(   '@/components/user/authentication/authenication_loding'))
    }, 'authenication_loding')
}
const authenication_end = function (r) {
    require.ensure([], function () {
        r(require(  '@/components/user/authentication/authenication_end'))
    }, 'authenication_end')
}
const login = function (r) {
    require.ensure([], function () {
        r(require(  '@/components/loading'))
    }, 'login')
}
//路由 编辑商品
const editgoods = function (r) {
    require.ensure([], function () {
        r(require('@/components/publish/editpublish'))
    }, 'editgoods')
}
//手机验证Mobile phone verification
const phone_number = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/set_user/phone_number'))
    }, 'phone_number')
}
//个人认证
const people = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/authentication/people'))
    }, 'people')
}
//个人认证页面
const shop_authentication = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/authentication/shop_authentication'))
    }, 'shop_authentication')
}
//我的钱包
const recharge = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/money_bag/recharge'))
    }, 'recharge')
}
const rechargelog = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/money_bag/rechargelog'))
    }, 'rechargelog')
}
const withdrawals = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/money_bag/withdrawals'))
    }, 'withdrawals')
}
const withdrawalslog = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/money_bag/withdrawalslog'))
    }, 'withdrawalslog')
}
const paypoints = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/money_bag/paypoints'))
    }, 'paypoints')
}
const accountlog = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/money_bag/accountlog'))
    }, 'accountlog')
}
const caution = function (r) {
    require.ensure([], function () {
        r(require( '@/components/moneypay/caution'))
    }, 'caution')
}
//我的订单
const myorder = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/myorder/myorder'))
    }, 'myorder')
}
const orderdetail = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/myorder/orderdetail'))
    }, 'orderdetail')
}
//分类列表
const class_list = function (r) {
    require.ensure([], function () {
        r(require( '@/components/class_list'))
    }, 'class_list')
}
//店铺升级支付
const shopuppay = function (r) {
    require.ensure([], function () {
        r(require( '@/components/user/shopuppay'))
    }, 'shopuppay')
}
//竞拍服务协议
import agreement from '@/components/user/agreement'
//历史足迹
const history = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/fans/history'))
    }, 'history')
}

//新闻首页
const new_index = function (r) {
    require.ensure([], function () {
        r(require('@/components/new_lift/new_index'))
    }, 'new_index')
}
//详情咨询
const new_result = function (r) {
    require.ensure([], function () {
        r(require('@/components/new_lift/new_result'))
    }, 'new_result')
}
//今日推荐
const today_tuijian = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/today_tuijian'))
    }, 'today_tuijian')
}
//shopping_mall.vue
const shopping_mall = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/shopping_mall'))
    }, 'shopping_mall')
}
//个人店铺首页
const shop_index = function (r) {
    require.ensure([], function () {
        r(require('@/components/user/shop_index'))
    }, 'shop_index')
}
//个人店铺首页
const daydayprice = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/daydayprice'))
    }, 'daydayprice')
}
//精选好店
const good_shop = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/good_shop'))
    }, 'good_shop')
}
//专场
const zhuanchang = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/zhuanchang'))
    }, 'zhuanchang')
}
//新品开抢
const new_shop = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/new_shop'))
    }, 'new_shop')
}
//搜索
const seach_index = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/seach_index'))
    }, 'seach_index')
}

//登陆
const loginindex = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/login'))
    }, 'login')
}
//上传
const shangchuanindex = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/shangchuan'))
    }, 'shangchuan')
}
//上传1
const shchindex = function (r) {
    require.ensure([], function () {
        r(require('@/components/index/shch'))
    }, 'shch')
}

export default new Router({
routes:[
    {path: '/', redirect:'/index'},
    {path: '/login', name: 'login', component: loginindex, meta: {title: '登陆'}},
    {path: '/shangchuan', name: 'shangchuan', component: shangchuanindex, meta: {title: '上传'}},
    {path: '/shch', name: 'shch', component: shchindex, meta: {title: '上'}},
    {path: '/productlists', name: 'productlists', component: productlists, meta: {title: '藏品列表'},
        children: [{path: ':goods_id(\\d+)', name: 'goodsproductdetail', component: goodsproductdetail, meta: {title: '商品详情'}}]
    },
    {path: '/index', name: 'index', component: index, meta: {title: '首页'},
        children: [{path: ':goods_id(\\d+)', name: 'goodsproductdetaiffl', component: goodsproductdetail, meta: {title: '商品详情'}}]
    },
    //个人中心
    {path: '/user/user', name: 'adminLink', component: user, meta: {title: '个人中心'}},
    //发布
    {path: '/publish/publish', name: 'publishlink', component: publish,meta: {title: '发布'}},
    //我的钱包
    {path: '/user/money_bag', name: 'money_baglink', component:money_bag,meta: {title: '我的钱包'}},
    //固定导航
    {path: '/fixed/fixedtitle', name: 'fixedtitle_link', component:fixedtitle,meta: {title: '固定导航'}},
    //Shopping Cart
    {path: '/user/shoping_cart', name: 'shoping_cart_link', component:shoping_cart,meta: {title: '购物车'}},
    //Commodity management
    {path: '/user/commodity_management', name: 'commodity_management_link', component:commodity_management,meta: {title: '商品管理'}},
    //群发商品
    {path: '/user/bulk_hair', name: 'bulk_hair_link', component:bulk_hair,meta: {title: '商品管理'}},
    //我的店铺
    {path: '/user/my_shop', name: 'my_shop_link', component:my_shop,meta: {title: '我的店铺'}},
    {path: '/user/seller_shop/:userid(\\d+)', name: 'seller_shop_link', component:seller_shop,meta: {title: '卖家店铺'}},
    //店铺升级
    {path: '/user/shop_upgrade', name: 'shop_upgrade_link', component:shop_upgrade,meta: {title: '店铺推广'}},
    //摇一摇
    {path: '/user/shake', name: 'shake_link', component:shake,meta: {title: '摇一摇'}},
    //营销中心
    {path: '/user/marketing_center', name: 'marketing_center_link', component:marketing_center,meta: {title: '营销中心'}},
    //员工出巡
    {path: '/user/employee_inquiry', name: 'employee_inquiry_link', component:employee_inquiry,meta: {title: '员工查询'}},
    //客服中心
    {path: '/user/customer_service', name: 'customer_service_link', component:customer_service,meta: {title: '客服中心'}},
    //Staff query results
    {path: '/user/staff_results/staff_index', name: 'staff_index_link', component:staff_index,meta: {title: '查询结果'}},
    //classification
    {path: '/classification/class_index', name: 'class_index_link', component:class_index,meta: {title: '分类'}},
    //heard_nav
    //set_user
    {path: '/user/set_user', name: 'set_user_link', component:set_user,meta: {title: '个人设置'}},
    //fans
    {path: '/user/fans/fan_index', name: 'fan_index_link', component:fan_index,meta: {title: '粉丝'}},
    //follow
    {path: '/user/fans/follow', name: 'follow_link', component:follow,meta: {title: '收藏'}},
    //authentication_index
    //authentication_index people
    {path: '/user/authentication/authentication_index', name: 'authentication_index_link', component:authentication_index,meta: {title: '个人认证'}},
    {path: '/login', name: 'login', component:login,meta: {title: '登录中心'}},
    //编辑商品
    {path: '/publish/editpublish', name: 'editgoods_link', component:editgoods,meta: {title: '商品编辑'}},
    //手机验证Mobile phone verification
    {path: '/user/set_user/phone_number', name: 'phone_number_link', component:phone_number,meta: {title: '手机验证'}},
    //验证结果
    {path: '/user/authentication/authenication_end', name: 'authenication_end_link', component:authenication_end,meta: {title: '认证结果'}},
    //我的钱包
    {path: '/user/money_bag/recharge', name: 'recharge_link', component:recharge,meta: {title: '充值'}},  //充值
    {path: '/user/money_bag/rechargelog', name: 'rechargelog_link', component:rechargelog,meta: {title: '充值记录'}},  //充值记录
    {path: '/user/money_bag/withdrawals', name: 'withdrawals_link', component:withdrawals,meta: {title: '申请提现'}},  //申请提现
    {path: '/user/money_bag/withdrawalslog', name: 'withdrawalslog_link', component:withdrawalslog,meta: {title: '提现记录'}},  //提现记录
    {path: '/user/money_bag/paypoints', name: 'paypoints_link', component:paypoints,meta: {title: '积分明细'}},  //积分明细
    {path: '/user/money_bag/accountlog', name: 'accountlog_link', component:accountlog,meta: {title: '账户明细'}},  //账户明细
    {path: '/moneypay/caution', name: 'caution_link', component:caution,meta: {title: '保证金'}},  //账户明细
    //我的订单
    {path: '/user/myorder/myorder', name: 'myorder_link', component:myorder,meta: {title: '我的订单'}},
    {path: '/user/myorder/orderdetail', name: 'orderdetail_link', component:orderdetail,meta: {title: '订单详情'}},
      //实名认证
    {path: '/user/authentication/people', name: 'people_link', component:people,meta: {title: '个人认证提交'}},
    //personal_authentication
    {path: '/user/authentication/shop_authentication', name: 'shop_authentication_link', component:shop_authentication,meta: {title: '个人认证页'}},
    //分类列表
    {path: '/class_list', name: 'class_list_link', component:class_list,meta: {title: '分类列表'},
        children: [{path: ':goods_id(\\d+)', name: 'goodsproductdetaiffl4', component: goodsproductdetail, meta: {title: '商品详情'}}]
    },
    //店铺升级支付
    {path: '/user/shopuppay', name: 'shopuppay_link', component:shopuppay,meta: {title: '店铺升级支付'}},
    //审核中
    {path: '/user/authentication/authenication_loding', name: 'authenication_loding_link', component:authenication_loding, meta: {title: '审核中'}},
    //竞拍协议
    {path: '/user/agreement', name: 'agreement_link', component:agreement, meta: {title: '服务协议'}},
    //历史足迹
    {path: '/user/fans/history', name: 'history_link', component:history, meta: {title: '历史足迹'}},
    // import history from '@/components/user/fans/history'
    //新闻首页
    {path: '/new_lift/new_index', name: 'new_index_link', component:new_index, meta: {title: '新闻首页'}},
    //新闻列表
    {path: '/new_lift/new_result', name: 'new_result_link', component:new_result, meta: {title: '详情资讯'}},
    //今日推荐
    {path: '/index/today_tuijian', name: 'today_tuijian_link', component:today_tuijian, meta: {title: '天天特价'}},
    //shopping_mall.vue
    {path: '/index/shopping_mall', name: 'shopping_mall_link', component:shopping_mall, meta: {title: '商城逛逛'}},
    //店铺个人的首页
    {path: '/user/shop_index', name: 'shop_index_link', component:shop_index,meta: {title: '个人店铺'}},
    //天天特价
    {path: '/index/daydayprice', name: 'daydayprice_link', component:daydayprice,meta: {title: '今日推荐'}},
    //精选好店
    {path: '/index/good_shop', name: 'good_shop_link', component:good_shop,meta: {title: '优选店铺'}},
    //专场
    {path: '/index/zhuanchang', name: 'zhuanchang_link', component:zhuanchang,meta: {title: '精选'}},
    //新品开抢
    {path: '/index/new_shop', name: 'new_shop_link', component:new_shop,meta: {title: '新品开抢'}},
    //搜索
    {path: '/index/seach_index', name: 'seach_index_link', component:seach_index,meta: {title: '搜索'}},
]
})