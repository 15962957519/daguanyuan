import 'whatwg-fetch'
import Promise from 'promise-polyfill';
// To add to window
if (!window.Promise) {
    window.Promise = Promise;
}
//var FastClick = require('fastclick');
//FastClick.attach(document.body);// init fastclick
//var Vue  = require('vue'); // get vue
import VueRouter from 'vue-router'
Vue.use(VueRouter);
//import {sync} from 'vuex-router-sync'
import VueProgressBar from 'vue-progressbar'

var store = require('./vuex/store.js'); // get vuxe -> store

const options = {
    color: '#00ff00',
    failedColor: '#874b4b',
    thickness: '1px',
    transition: {
        speed: '0.1s',
        opacity: '0.5s'
    },
    autoRevert: false,
    location: 'top',
    inverse: false
}
Vue.use(VueProgressBar, options)


// or for a single instance
import infiniteScroll from 'vue-infinite-scroll'
new Vue({
    directives: {infiniteScroll}
})
var App = require('./app.vue'); // get root module


// supports both of Vue 1.0 and Vue 2.0
import VueLazyload from 'vue-lazyload'
Vue.use(VueLazyload, {
    preLoad: 1.3,
    error: 'static/img/error.png',
    loading: 'static/img/loading.gif',
    attempt: 1
})
const index = function (r) {
    require.ensure([], function () {
        r(require('./views/index.vue'))
    }, 'index')
}

const fabu = function (r) {
    require.ensure([], function () {
        //r(require('./views/product.vue'))
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




var routes = [
    {path: '/', meta: {title: '天宝微拍'}, component: index},
    {path: '/fabu',name:'fabu', meta: {title: '发布作品'}, component: fabu},


   // {path: '/index/:cat_id?',name:'index', meta: {title: '天宝微拍'}, component: index},
    {path: '/index/:cat_id?',name:'index', meta: {title: '天宝微拍'}, component: index},
    {path: '/shop/:userid?',name:'shopindex', meta: {title: '天宝微拍用户官网'}, component: index},
    {path: '/foucswebsite/:userid?',name:'foucswebsite', meta: {title: '天宝微拍用户官网'}, component: foucswebsite},

    {path: '/find/:userid(\\d+)?/:type(\\d+)?',name:'find', meta: {title: '发现圈子'}, component: paimaiquan},
    {path: '/statistics/:userid(\\d+)?/:type(\\d+)?',name:'statistics', meta: {title: '发现圈子'}, component: statistics},


    {path: '/mymain/:userid(\\d+)?/:type(\\d+)?',name:'mymain', meta: {title: '圈子'}, component: paimaiquan},
    {path: '/paymenting/:lastnum\\_:good_id(\\d+)\\.html',name:'paymenting', meta: {title: '支付'}, component: paymenting},
    {path: '/paymenting',name:'paymentingold', meta: {title: '支付'}, component: paymenting},
    {path: '/usercenter/mycollectionproductlist', component: mycollectionproductlist},
    {
        path: '/usercenter', component: usercenterapp,

        children: [
            {path: '', meta: {title: '用户中心'}, component: usercenter},
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
                path: '*',
                component: usercenter
            }
        ]
    },
    {
        path: '/usersellindex',meta: {title: '卖家中心'}, component: usercenterapp,
        children: [
            {path: '',name:'usersellindex',meta: {title: '卖家用户中心'},  component: user_sellindex},
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
                path: 'mumbercheck',
                meta: {title: '实名认证'},
                component: mumbercheck
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
        ]
    },
    {path: '*', meta: {title: '天宝微拍'}, component: index}
];
import {weixinlogin,urlrefer} from "./assets/js/common_function.js"
var router = new VueRouter({
    mode: 'history',
    routes: routes,
    scrollBehavior (to, from, savedPosition) {
        return {x: 0, y: 0}
    }
});
router.beforeEach((to, from, next) => {
    var  flagislogin =   window.storeWithExpiration.get('token') ||'';

    if(is_weixn() && !flagislogin){
        weixinlogin(to.fullPath,from,next)
    }else{
        next()
    }
}
)
//sync(store, router)
const vm = new Vue({
    store: store,
    router: router,
    render: function (h) {
        return h(App);
    }
})

vm.$mount('#app')
