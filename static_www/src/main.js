// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
if (module.hot) {
    module.hot.accept();
}
import Vue from 'vue'
import App from './App'
import router from './router'
import Vuex from 'vuex'
Vue.config.productionTip = false
import fastclick from 'fastclick'
import VueLazyload from 'vue-lazyload'
import 'vue-ydui/dist/ydui.base.css'
import '@/assets/js/ydui.flexible.js'
import '@/assets/js/js.cookie.min.js'
import axios from 'axios'
import weipaiconfig from '@/../config/dev.env'
import store from '@/store/store.js'
import weipai from '@/commonjs/util.js'
Vue.prototype.$axios = axios
Vue.prototype.$weipai = weipai
//全局配置api接口url
axios.defaults.baseURL = weipaiconfig.default_domain_api;
//axios.defaults.headers['Content-Type'] = 'application/x-www-form-urlencoded'
//axios.defaults.headers['XPS-Version'] = '1.0.0'
//引入公共样式
fastclick.attach(document.body)
//makeing x信息
import wxmakething from '@/commonjs/makethings.js'
//使用swpier
require('swiper/dist/css/swiper.css')
import VueAwesomeSwiper from 'vue-awesome-swiper'
Vue.use(VueAwesomeSwiper)

Vue.prototype.makeconfig = wxmakething.getwxmakeings;
//左滑样式
//  ydui样式

import YDUI from 'vue-ydui'; /* 相当于import YDUI from 'vue-ydui/ydui.rem.js' */
import 'vue-ydui/dist/ydui.rem.css';
/* 使用px：import 'vue-ydui/dist/ydui.px.css'; */

Vue.use(YDUI);
//ydui样式结束


function setCookie(c_name,value,expiredays)
{
    var exdate=new Date()
    exdate.setDate(exdate.getTime()+(expiredays))
    Cookies.set(c_name,value,  { expires: expiredays});
}


function getCookie(c_name)
{
    return Cookies.get(c_name);
}

//去掉两边空格
function settrim(str)
{
    return str.replace(/(^\s*)|(\s*$)/g, "");
}
Vue.prototype.settrim = settrim //挂载到Vue实例上面


window.storeWithExpiration = {
    set: function (key, val, exp) {
        //exp天数
        exp =exp||0;
        setCookie(key,val,exp)
    },
    get: function (key) {
        var info = getCookie(key)
        if (!info) {
            return null
        }
        return  info;
    }
};



router.afterEach((to, from, next) => {
    if(to.name=='class_list_link'||to.name=='index' ){
    }else {
        window.scrollTo(0, 0);
    }
    //
});

router.beforeEach((to, from, next)=>{
  /* 路由发生变化修改页面title */
    if (to.meta.title) {
      document.title = to.meta.title
    }
//store.commit('updateLoadingStatus', true);
    //判断微信

    if("login"== to.name){
        next()
    }else{
        weipai.login(next,Vue.prototype.makeconfig,to);
    }
});

router.afterEach(route => {
    /* 隐藏加载中动画 */
  //  store.commit('updateLoadingStatus', false);
});
//正在加载
import { Confirm, Alert, Toast, Notify, Loading } from 'vue-ydui/dist/lib.rem/dialog';
/* 使用px：import { Confirm, Alert, Toast, Notify, Loading } from 'vue-ydui/dist/lib.px/dialog'; */
Vue.prototype.$dialog = {
    confirm: Confirm,
    alert: Alert,
    toast: Toast,
    notify: Notify,
    loading: Loading,
};

const app = new Vue({
        store,
        router,
        render: v => v(App)
}).$mount('#app');

