/**
 * Created by ericssonon on 2017/6/23.
 */
const index = function (r) {
    require.ensure([], function () {
        r(require('./views/index.vue'))
    }, 'index')
}

const fabu = function (r) {
    require.ensure([], function () {
        r(require('./views/productback.vue'))
    }, 'product')
}

const editproduct = function (r) {
    require.ensure([], function () {
        r(require('./views/editproduct.vue'))
    }, 'editproduct')
}



const usercenter = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter.vue'))
    }, 'usercenter')
}

const user_sellindex = function (r) {
    require.ensure([], function () {
        r(require('./components/user_sellindex.vue'))
    }, 'user_sellindex')
}
const paimaiquan = function (r) {
    require.ensure([], function () {
        r(require('./views/paimaiquan.vue'))
    }, 'paimaiquan')
}

const cacse = function (r) {
    require.ensure([], function () {
        r(require('./views/cacse.vue'))
    }, 'cacse')
}
const statistics = function (r) {
    require.ensure([], function () {
        r(require('./views/statistics.vue'))
    }, 'statistics')
}
const orderlist = function (r) {
    require.ensure([], function () {
        r(require('./components/orderlist.vue'))
    }, 'usercenter')
}
const orderlistsale = function (r) {
    require.ensure([], function () {
        r(require('./components/orderlistsaller.vue'))
    }, 'user_sellindex')
}
const productlist = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/productlist.vue'))
    }, 'user_sellindex')
}

const mycollectionproductlist = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/mycollectionproductlist.vue'))
    }, 'usercenterapp')
}



const usersetvue = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/usersetting.vue'))
    }, 'user_sellindex')
}

const usersellindexsetting = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/user_seller_center.vue'))
    }, 'usersellindexsetting')
}


const msignature = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/user/msignatureform.vue'))
    }, 'usercenter')
}

const contacts = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/user/contacts.vue'))
    }, 'usercenter')
}
const weixinnumber = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/user/weixinnumber.vue'))
    }, 'usercenter')
}
const mobile = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/user/mobile.vue'))
    }, 'usercenter')
}
const usercenterapp = function (r) {
    require.ensure([], function () {
        r(require('./usercenter.vue'))
    }, 'usercenterapp')
}

const paymenting = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/paymenting.vue'))
    }, 'index')
}

const mumberupgrade = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/mumberupgrade.vue'))
    }, 'mumberupgrade')
}

const authentication = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/authentication.vue'))
    }, 'authentication')
}

const mumbercheck = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/mumbercheck.vue'))
    }, 'mumbercheck')
}

const gerenverifyProcess = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/gerenverifyProcess.vue'))
    }, 'gerenverifyProcess')
}
const companyverifyProcess = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/companyverifyProcess.vue'))
    }, 'companyverifyProcess')
}

const gerenverifyProcess_individual = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/gerenverifyProcess_individual.vue'))
    }, 'gerenverifyProcess')
}

const gerenverifyPayment = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/gerenverifyPayment.vue'))
    }, 'gerenverifyPayment')
}

const mumber_notice = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/mumber_notice.vue'))
    }, 'mumber_notice')
}
const paying = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/paying.vue'))
    }, 'paying')
}

const myfoucs = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/myfoucs.vue'))
    }, 'myfoucs')
}

const foucsme = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/foucsme.vue'))
    }, 'foucsme')
}

const foucswebsite = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/foucswebsite.vue'))
    }, 'foucswebsite')
}

const myqrcode = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/myqrcode.vue'))
    }, 'usercentermyqrcode')
}
const mesagecenter = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/mesagecenter.vue'))
    }, 'mesagecenter')
}
const myassets = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/myassets.vue'))
    }, 'myassets')
}
const withdraw = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/myassets_draw.vue'))
    }, 'withdraw')
}
const sendmessage = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/sendmessage.vue'))
    }, 'sendmessage')
}
const helper = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/helper.vue'))
    }, 'helper')
}
const myqverfitymobile = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/myqverfitymobile.vue'))
    }, 'myqverfitymobile')
}
const user_privacy = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/user_privacy.vue'))
    }, 'user_privacy')
}
const paipinmessage = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/paipinmessage.vue'))
    }, 'paipinmessage')
}

const punish = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/punish.vue'))
    }, 'punish')
}

const warm_prompt = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/warm_prompt.vue'))
    }, 'warm_prompt')
}




export var routes = [
    {path: '/', meta: {title: '天宝微拍'}, component: index},
    {path: '/fabu',name:'fabu', meta: {title: '发布作品'}, component: fabu},


    // {path: '/index/:cat_id?',name:'index', meta: {title: '天宝微拍'}, component: index},
    {path: '/index/:cat_id?',name:'index', meta: {title: '天宝微拍'}, component: index},
    {path: '/shop/:userid?',name:'shopindex', meta: {title: '天宝微拍用户官网'}, component: index},
    {path: '/foucswebsite/:userid?',name:'foucswebsite', meta: {title: '天宝微拍用户官网'}, component: foucswebsite},

    {path: '/find/:userid(\\d+)?/:type(\\d+)?',name:'find', meta: {title: '发现圈子'}, component: paimaiquan},

    {path: '/cacse/:goods_id?',name:'cacse', meta: {title: '成交案例'}, component: cacse},



    {path: '/statistics/:userid(\\d+)?/:type(\\d+)?',name:'statistics', meta: {title: '发现圈子'}, component: statistics},


    {path: '/mymain/:userid(\\d+)?/:type(\\d+)?',name:'mymain', meta: {title: '圈子'}, component: paimaiquan},
    {path: '/paymenting/:lastnum\\_:good_id(\\d+)\\.html',name:'paymenting', meta: {title: '支付'}, component: paymenting},
    {path: '/paymenting',name:'paymentingold', meta: {title: '支付'}, component: paymenting},
    {path: '/paipinmessage',name:'paipinmessage', meta: {title: '商品区消息'}, component: paipinmessage},
    {path: '/usercenter/mycollectionproductlist', component: mycollectionproductlist},
    {
        path: '/usercenter', component: usercenterapp,
        children: [
            {path: '', meta: {title: '用户中心'}, component: usercenter},
            {path: 'index', meta: {title: '用户中心'}, component: usercenter},
            {
                path: 'orderlist/:status?/:issale?',
                name:'orderlist',
                component: orderlist
            },
            {
                path: 'setting',
                component: usersetvue
            },
            {
                path: 'myfoucs',
                component: myfoucs
            },
            {
                path: 'foucsme',
                component: foucsme
            },
            {
                path: 'paying/:price(\\d+)/:type(\\d+)',
                name:'paying',
                component: paying
            },
            {
                path: 'index',
                component: usercenter
            },
            {
                path: 'signature',
                component: msignature
            },
            {
                path: 'contacts',
                component: contacts
            },
            {
                path: 'weixinnumber',
                component: weixinnumber
            },
            {
                path: 'mobile',
                component: mobile
            },
            {
                path: 'myqrcode/:userid(\\d+)?',
                component: myqrcode
            },
            {
                path: 'mesagecenter',
                component: mesagecenter
            },
            {
                path: 'myassets',
                component: myassets
            },
            {
                path: 'withdraw',
                component: withdraw
            },
            {
                path: 'sendmessage',
                component: sendmessage
            },
            {
                path: 'helper',
                component: helper
            },
            {
                path: '*',
                component: usercenter
            }
        ]
    },
    {
        path: '/usersellindex',name:'usersellindex', component: usercenterapp,
        children: [
            {
                path: 'index',
                name:'usersellindexindex',
                meta: {title: '卖家用户中心'},
                component: user_sellindex
            },
            {
                path: '',
                meta: {title: '卖家用户中心'},
                component: user_sellindex
            },
            {
                path: 'productlist',
                meta: {title: '商品区列表'},
                component: productlist
            },
            {
                path: 'editproduct',
                name:'editproductbyid',
                meta: {title: '商品区列表'},
                component: editproduct
            },
            {
                path: 'orderlistsale/:status?/:issale?',
                name:'orderlistsale',
                meta: {title: '卖家订单列表'},
                component: orderlistsale
            },
            {
                path: 'mumberupgrade',
                meta: {title: '会员升级'},
                component: mumberupgrade
            },
            {
                path: 'authentication',
                meta: {title: '认证费用'},
                component: authentication
            },
            {
                path: 'companyverifyProcess',
                meta: {title: '企业认证'},
                component: companyverifyProcess
            },
            {
                path: 'mumbercheck',
                meta: {title: '实名认证'},
                component: mumbercheck
            },
            {
                path: 'user_privacy',
                meta: {title: '用户隐私 '},
                component: user_privacy
            },
            {
                path: 'gerenverifyProcess',
                meta: {title: '实名认证'},
                component: gerenverifyProcess
            },
            {
                path: 'individual',
                meta: {title: '实名认证'},
                component: gerenverifyProcess_individual
            },
            {
                path: 'gerenverifyPayment',
                meta: {title: '实名认证'},
                component: gerenverifyPayment
            },
            {
                path: 'mumber_notice',
                meta: {title: '会员升级通知'},
                component: mumber_notice
            },
            {
                path: 'setting',
                component: usersellindexsetting
            },
            {
                path: 'myqverfitymobile',
                component: myqverfitymobile
            },
            {
                path: 'punish',
                component: punish
            },
            {
                path: 'warm_prompt',
                component: warm_prompt
            },
            {
                path: '*',
                meta: {title: '卖家用户中心'},
                component: user_sellindex
            }
        ]
    },
    {path: '*', meta: {title: '天宝微拍'}, component: index}
];

