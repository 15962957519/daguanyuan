webpackJsonp([48],{"7kwR":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=a("xd7I"),s=a("5Ku6"),i=a("R4Sj"),r=a("w1gC");n.default.component(r.NavBar.name,r.NavBar),n.default.component(r.NavBarBackIcon.name,r.NavBarBackIcon),n.default.component(r.NavBarNextIcon.name,r.NavBarNextIcon);var o={data:function(){return{order_sn:"",pay_things:""}},mounted:function(){this.order_sn=this.ordersn()},methods:{goback:function(){this.$router.go(-1)},ordersn:function(){var t=new Date,e=t.getMonth()+1,a=t.getDate(),n=t.getHours(),s=t.getMinutes(),i=t.getSeconds();return t.getFullYear().toString()+e.toString()+a+n+s+i+Math.round(89*Math.random()+100).toString()},payupshop:function(){var t=this;if(""!=t.pay_things){var e="/balancerecharge?token="+storeWithExpiration.get("token")+"&order_sn="+t.order_sn+"&order_account="+t.pay_things+"&state=1";t.$axios.get(e).then(function(e){console.log(e),200==e.status&&2e3==e.data.code&&(console.log(e),t.wxzfcallpay(JSON.parse(e.data.jsondata)))}).catch(function(t){})}else t.$dialog.alert({mes:"请输入金额"})},wxzfcallpay:function(t){var e=this;function a(t){WeixinJSBridge.invoke("getBrandWCPayRequest",t,function(t){"get_brand_wcpay_request:ok"==t.err_msg?(e.$store.dispatch("getuserinfo"),e.$dialog.toast({mes:"支付成功",timeout:1500,icon:"success"}),e.$router.push({path:"/user/money_bag"})):"get_brand_wcpay_request:cancel"==t.err_msg?e.$dialog.toast({mes:"已取消支付",timeout:1500,icon:"success"}):"get_brand_wcpay_request:fail"==t.err_msg&&e.$dialog.alert({mes:"网络异常"})})}"undefined"==typeof WeixinJSBridge?document.addEventListener?document.addEventListener("WeixinJSBridgeReady",a(t),!1):document.attachEvent&&(document.attachEvent("WeixinJSBridgeReady",a(t)),document.attachEvent("onWeixinJSBridgeReady",a(t))):a(t)}},computed:Object(i.b)({user_data:function(t){return s.a.isEmptyObject(t.menuItems)&&this.$store.dispatch("getuserinfo"),console.log(t.menuItems),t.menuItems}})},c={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("yd-navbar",{attrs:{bgcolor:"#af773e",fixed:!0}},[n("router-link",{attrs:{slot:"left",to:""},slot:"left"},[n("yd-navbar-back-icon",{attrs:{color:"#fff"},nativeOn:{click:function(e){return t.goback(e)}}},[n("span",{staticStyle:{color:"#fff"}},[t._v("返回")])])],1),t._v(" "),n("p",{staticStyle:{"font-size":".3rem",color:"#fff"},attrs:{slot:"center"},slot:"center"},[t._v("充值")]),t._v(" "),n("img",{staticStyle:{width:".5rem"},attrs:{slot:"right",src:a("RDmu")},slot:"right"})],1),t._v(" "),n("div",{staticClass:"integral_recharge_container",staticStyle:{"padding-top":"1rem"}},[n("div",{staticClass:"list"},[n("div",{staticClass:"item_l"},[t._v(" 订单号 :")]),t._v(" "),n("div",{staticClass:"item_r",attrs:{id:"order_sn"}},[t._v(t._s(t.order_sn))])]),t._v(" "),t._m(0),t._v(" "),n("div",{staticClass:"list"},[n("div",{staticClass:"item_l"},[t._v(" 充值金额 :")]),t._v(" "),n("div",{staticClass:"item_r"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.pay_things,expression:"pay_things"}],attrs:{type:"number",name:"jyear",pattern:"\\d*",datatype:"*",placeholder:"单笔限额50.0万"},domProps:{value:t.pay_things},on:{input:function(e){e.target.composing||(t.pay_things=e.target.value)}}})])]),t._v(" "),n("div",{staticClass:"but",on:{click:t.payupshop}},[t._v("立即充值")])])],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"list"},[e("div",{staticClass:"item_l"},[this._v(" 支付方式 :")]),this._v(" "),e("div",{staticClass:"item_r"},[this._v("微信支付")])])}]};var d=a("C7Lr")(o,c,!1,function(t){a("FXsk")},"data-v-2cab11ea",null);e.default=d.exports},FXsk:function(t,e){}});
//# sourceMappingURL=48.dbae74165a50d5b9af38.js.map