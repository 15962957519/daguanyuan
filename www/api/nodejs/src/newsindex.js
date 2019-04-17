
import VueRouter from 'vue-router'
import {sync} from 'vuex-router-sync'
var store = require('./vuex/store.js'); // get vuxe -> store
Vue.use(VueRouter);

var App = require('./fabu.vue'); // get root module
const newsindexlists = function (r) {
    require.ensure([], function () {
        r(require('./views/newsindexlists.vue'))
    }, 'newsindexlists')
}
const headermessagedetail = function (r) {
    require.ensure([], function () {
        r(require('./components/headermessagedetail.vue'))
    }, 'headermessagedetail')
}

const goodsproductdetail = function (r) {
    require.ensure([], function () {
        r(require('./components/goodsproductdetail.vue'))
    }, 'goodsproductdetail')
}
import {weixinlogin, urlrefer} from "./assets/js/common_function.js"
var routes = [
    {path: '/',name:'newsindexlists', meta: {title: '新闻列表'}, component: newsindexlists},
    {path: '',name:'newsindexlists', meta: {title: '新闻列表'}, component: newsindexlists},
    {
        path: '/headermessagedetail/:article(\\d+)/:user_id(\\d+)?',
        name: 'newsdetail',
        meta: {title: '新闻详情页面'},
        component: headermessagedetail
    },
    {
        path: '/goodsproductdetail/:goods_id(\\d+)',
        name: 'goodsproductdetail',
        meta: {title: '精品详情页面'},
        component: goodsproductdetail
    },
    {
        path: '*',
        component: newsindexlists
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
