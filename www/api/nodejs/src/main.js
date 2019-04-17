
import VueRouter from 'vue-router'
import {sync} from 'vuex-router-sync'
var store = require('./vuex/store.js'); // get vuxe -> store
Vue.use(VueRouter);

var App = require('./fabu.vue'); // get root module
const fabu = function (r) {
    require.ensure([], function () {
        //r(require('./views/product.vue'))
        r(require('./views/productbackd.vue'))
    }, 'product')
}

const editproduct = function (r) {
    require.ensure([], function () {
        r(require('./views/editproductnovue.vue'))
    }, 'editproduct')
}
const punish = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/seller/punish.vue'))
    }, 'punish')
}

const myqverfitymobile = function (r) {
    require.ensure([], function () {
        r(require('./components/usercenter/myqverfitymobile.vue'))
    }, 'myqverfitymobile')
}

const usercenterapp = function (r) {
    require.ensure([], function () {
        r(require('./usercenter.vue'))
    }, 'usercenterapp')
}
import {weixinlogin, urlrefer} from "./assets/js/common_function.js"
var routes = [
    {path: '/',name:'fabuindex', meta: {title: '天宝'}, component: fabu},
    {path: '/fabu',name:'fabuindex', meta: {title: '天宝'}, component: fabu},
    {path: '/editproduct', name:'editproductbyid',meta: {title: '卖家中心'}, component: editproduct},

    {
        path: '/usersellindex', name: 'usersellindex', component: usercenterapp,
        children: [
            {
                path: 'myqverfitymobile',
                component: myqverfitymobile
            },
            {
                path: 'punish',
                    component: punish
            }
        ]
    }
];


var router = new VueRouter({
    mode: 'hash',
    routes: routes,
    scrollBehavior (to, from, savedPosition) {
        return {x: 0, y: 0}
    }
});

router.beforeEach((to, from, next) => {
            var  flagislogin = window.storeWithExpiration.get('token') || '';
        if (is_weixn() && !flagislogin) {
            weixinlogin(to.fullPath, from, next)
        } else {
            next()
        }
})
//sync(store, router)
const vm = new Vue({
    store: store,
    router: router,
    render: function (h) {
        return h(App);
    }
})

vm.$mount('#app')
