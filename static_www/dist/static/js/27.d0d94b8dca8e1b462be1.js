webpackJsonp([27],{"mL/f":function(t,e){},pnES:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=a("3cXf"),o=a.n(i),r=a("xd7I"),n=a("w1gC"),s=a("efG5"),c=a.n(s);r.default.component(n.NavBar.name,n.NavBar),r.default.component(n.NavBarBackIcon.name,n.NavBarBackIcon),r.default.component(n.NavBarNextIcon.name,n.NavBarNextIcon);var d={data:function(){return{localIds:[],localIds1:[],is_identify:"",card:{uname:"",number:"",mobile:"",frontid:"",backid:"",state_numb:21,title:"个人认证",is_pay:""}}},mounted:function(){this.category()},methods:{category:function(){var t="/identify_edit?token="+window.storeWithExpiration.get("token"),e=this;this.$axios.get(t).then(function(t){1==t.data.data.is_identify&&(e.card.uname=t.data.data.name,e.card.mobile=t.data.data.telephone,e.card.number=t.data.data.idcode,e.localIds.push(t.data.data.verifyIdcodefront),e.localIds1.push(t.data.data.verifyIdcodeback),e.is_identify=t.data.data.is_identify,e.card.is_pay=t.data.data.is_pay)})},goback:function(){this.$router.go(-1)},payupshop:function(){var t=this,e=t.ordersn(),a="/balancerecharge?token="+storeWithExpiration.get("token")+"&state="+t.card.state_numb+"&order_sn="+e;t.$axios.get(a).then(function(e){console.log(e),200==e.status&&2e3==e.data.code&&(console.log(e),t.wxzfcallpay(JSON.parse(e.data.jsondata)))}).catch(function(t){})},wxzfcallpay:function(t){var e=this;function a(t){WeixinJSBridge.invoke("getBrandWCPayRequest",t,function(t){"get_brand_wcpay_request:ok"==t.err_msg?(e.$store.dispatch("getuserinfo"),e.$dialog.toast({mes:"支付成功",timeout:1500,icon:"success"}),e.$router.push({path:"/user/authentication/authentication_index"})):"get_brand_wcpay_request:cancel"==t.err_msg?(e.$store.dispatch("getuserinfo"),e.$dialog.toast({mes:"已取消支付",timeout:1500,icon:"success"}),e.$router.push({path:"/user/authentication/authentication_index"})):"get_brand_wcpay_request:fail"==t.err_msg&&e.$dialog.alert({mes:"网络异常"})})}"undefined"==typeof WeixinJSBridge?document.addEventListener?document.addEventListener("WeixinJSBridgeReady",a(t),!1):document.attachEvent&&(document.attachEvent("WeixinJSBridgeReady",a(t)),document.attachEvent("onWeixinJSBridgeReady",a(t))):a(t)},ordersn:function(){var t=new Date,e=t.getMonth()+1,a=t.getDate(),i=t.getHours(),o=t.getMinutes(),r=t.getSeconds();return t.getFullYear().toString()+e.toString()+a+i+o+r+Math.round(89*Math.random()+100).toString()},addImg:function(t,e){var a=this;c.a.ready(function(){c.a.chooseImage({count:1,sizeType:["original","compressed"],sourceType:["album","camera"],success:function(t){a.localIds=t.localIds,a.wxuploadImage(a.localIds)},fail:function(t){alert(o()(t))}})})},wxuploadImage:function(t){var e,a=this;t.length;e=t[0],window.__wxjs_is_wkwebview&&-1!=e.indexOf("wxlocalresource")&&(e=e.replace("wxlocalresource","wxLocalResource")),c.a.uploadImage({localId:e,isShowProgressTips:1,success:function(t){a.card.frontid="",a.card.frontid=t.serverId},fail:function(t){alert("网络不佳，请稍后重试！")}})},addImg1:function(){var t=this;c.a.ready(function(){c.a.chooseImage({count:1,sizeType:["original","compressed"],sourceType:["album","camera"],success:function(e){t.localIds1=e.localIds,t.wxuploadImage1(t.localIds1)},fail:function(t){alert(o()(t))}})})},wxuploadImage1:function(t){var e,a=this;t.length;e=t[0],window.__wxjs_is_wkwebview&&-1!=e.indexOf("wxlocalresource")&&(e=e.replace("wxlocalresource","wxLocalResource")),c.a.uploadImage({localId:e,isShowProgressTips:1,success:function(t){a.card.backid="",a.card.backid=t.serverId},fail:function(t){alert("网络不佳，请稍后重试！")}})},removeimgs:function(){this.localIds=""},removeimgs1:function(){this.localIds1=""},setDate:function(t){var e=this;if(""!=e.card.uname)if(""!=e.card.mobile){if(""!=e.card.number)return""==e.card.frontid?(e.$dialog.alert({mes:"请上传身份证正面！"}),!1):""==e.card.backid?(e.$dialog.alert({mes:"请上传身份证反面！"}),!1):(e.upload=function(){var t=new FormData;t.append("token",storeWithExpiration.get("token")),e.goodstoserver=o()(e.card),t.append("card",e.goodstoserver),e.$axios.post("/person_identify",t,{timeout:2e4}).then(function(t){return e.$dialog.toast({mes:"提交成功 等待审核",timeout:1500,icon:"success"}),e.$store.commit("card",e.card),e.$store.dispatch("getuserinfo"),e.$router.push({path:"/user/authentication/authentication_index"}),!1}).catch(function(t){})},setTimeout(e.upload,100),!1);e.$dialog.alert({mes:"身份证号码不能为空！"})}else e.$dialog.alert({mes:"手机号码不能为空！"});else e.$dialog.alert({mes:"姓名不能为空！"})},setDate1:function(t){var e=this;if(""!=e.card.uname)if(""!=e.card.mobile){if(""!=e.card.number)return""==e.card.localIds?(e.$dialog.alert({mes:"请上传身份证正面！"}),!1):""==e.card.localIds1?(e.$dialog.alert({mes:"请上传身份证反面！"}),!1):(e.upload=function(){var t=new FormData;t.append("token",storeWithExpiration.get("token")),e.goodstoserver=o()(e.card),t.append("card",e.goodstoserver),e.$axios.post("/identify_person_edit",t,{timeout:2e4}).then(function(t){return e.$dialog.toast({mes:"提交成功 等待审核",timeout:1500,icon:"success"}),e.$store.commit("card",e.card),e.$store.dispatch("getuserinfo"),e.$router.push({path:"/user/authentication/authentication_index"}),!1}).catch(function(t){})},setTimeout(e.upload,100),!1);e.$dialog.alert({mes:"身份证号码不能为空！"})}else e.$dialog.alert({mes:"手机号码不能为空！"});else e.$dialog.alert({mes:"姓名不能为空！"})},setDate2:function(t){var e=this;if(""!=e.card.uname)if(""!=e.card.mobile){if(""!=e.card.number)return""==e.card.localIds?(e.$dialog.alert({mes:"请上传身份证正面！"}),!1):""==e.card.localIds1?(e.$dialog.alert({mes:"请上传身份证反面！"}),!1):(e.upload=function(){var t=new FormData;t.append("token",storeWithExpiration.get("token")),e.goodstoserver=o()(e.card),t.append("card",e.goodstoserver),e.$axios.post("/identify_person_edit",t,{timeout:2e4}).then(function(t){return e.$dialog.toast({mes:"提交成功 等待审核",timeout:1500,icon:"success"}),e.$store.commit("card",e.card),e.$store.dispatch("getuserinfo"),e.$router.push({path:"/user/authentication/authentication_index"}),!1}).catch(function(t){})},setTimeout(e.upload,100),!1);e.$dialog.alert({mes:"身份证号码不能为空！"})}else e.$dialog.alert({mes:"手机号码不能为空！"});else e.$dialog.alert({mes:"姓名不能为空！"})}}},l={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("div",{staticClass:"margin"},[i("yd-navbar",{attrs:{fixed:!0}},[i("router-link",{attrs:{slot:"left",to:""},slot:"left"},[i("yd-navbar-back-icon",{nativeOn:{click:function(e){return t.goback(e)}}},[t._v("返回")])],1),t._v(" "),i("p",{staticStyle:{"font-size":".3rem"},attrs:{slot:"center"},slot:"center"},[t._v("个人认证")]),t._v(" "),i("img",{staticStyle:{width:".5rem"},attrs:{slot:"right",src:a("rgYI")},slot:"right"})],1),t._v(" "),t._m(0),t._v(" "),i("yd-cell-group",[i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("姓名：")]),t._v(" "),i("yd-input",{ref:"input9",attrs:{slot:"right",required:"",max:"20",placeholder:"请输入用户名"},slot:"right",model:{value:t.card.uname,callback:function(e){t.$set(t.card,"uname",e)},expression:"card.uname"}})],1),t._v(" "),i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("电话：")]),t._v(" "),i("input",{directives:[{name:"model",rawName:"v-model",value:t.card.mobile,expression:"card.mobile"}],attrs:{slot:"right",type:"number",placeholder:"请输入您手机号码"},domProps:{value:t.card.mobile},on:{input:function(e){e.target.composing||t.$set(t.card,"mobile",e.target.value)}},slot:"right"})]),t._v(" "),i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("身份证号码：")]),t._v(" "),i("input",{directives:[{name:"model",rawName:"v-model",value:t.card.number,expression:"card.number"}],attrs:{slot:"right",type:"text",placeholder:"请输入您的身份证号码"},domProps:{value:t.card.number},on:{input:function(e){e.target.composing||t.$set(t.card,"number",e.target.value)}},slot:"right"})]),t._v(" "),i("div",{staticClass:"people_ti"},[i("p",[t._v("上传身份证件"),i("span",{staticClass:"bg-yellow"},[t._v("(必填)")])])]),t._v(" "),i("div",{staticClass:"upload_img"},[i("p",[t._v("上传身份证正面")]),t._v(" "),i("div",{staticClass:"upload_im_div",attrs:{id:"one"},on:{click:function(e){return e.stopPropagation(),t.addImg(e)}}}),t._v(" "),t._l(t.localIds,function(e,o){return i("div",{staticClass:"addimg",model:{value:t.card.frontid,callback:function(e){t.$set(t.card,"frontid",e)},expression:"card.frontid"}},[i("div",{staticClass:"po_img_t",on:{click:function(e){return t.removeimgs()}}},[i("img",{staticStyle:{position:"absolute",top:".1rem",right:"0.1rem",width:".5rem",height:".5rem"},attrs:{src:a("VgmL")}})]),t._v(" "),i("img",{attrs:{src:t.localIds}})])})],2),t._v(" "),i("div",{staticClass:"upload_img"},[i("p",[t._v("上传身份证反面")]),t._v(" "),i("div",{staticClass:"upload_im_div",attrs:{id:"two"},on:{click:function(e){return e.stopPropagation(),t.addImg1(e)}}}),t._v(" "),t._l(t.localIds1,function(e,o){return i("div",{staticClass:"addimg",model:{value:t.card.backid,callback:function(e){t.$set(t.card,"backid",e)},expression:"card.backid"}},[i("div",{staticClass:"po_img_t",on:{click:function(e){return t.removeimgs1()}}},[i("img",{staticStyle:{position:"absolute",top:".1rem",right:"0.1rem",width:".5rem",height:".5rem"},attrs:{src:a("VgmL")}})]),t._v(" "),i("img",{attrs:{src:e}})])})],2),t._v(" "),i("div",[t._v("付款后请及时通知平台工作人员，加快审核速度。")])],1),t._v(" "),i("yd-button",{directives:[{name:"show",rawName:"v-show",value:0==t.is_identify&&0==t.card.is_pay,expression:"is_identify==0 && card.is_pay==0"}],staticClass:"btn_canc",attrs:{bgcolor:"#af773e",color:"#fff",size:"large",type:"warning"},nativeOn:{click:function(e){return t.setDate(e)}}},[t._v("确认提交并支付费用")]),t._v(" "),i("yd-button",{directives:[{name:"show",rawName:"v-show",value:1==t.is_identify&&0==t.card.is_pay,expression:"is_identify==1 && card.is_pay==0"}],staticClass:"btn_canc",attrs:{bgcolor:"#af773e",color:"#fff",size:"large",type:"warning"},nativeOn:{click:function(e){return t.setDate1(e)}}},[t._v("提交并支付费用")]),t._v(" "),i("yd-button",{directives:[{name:"show",rawName:"v-show",value:1==t.is_identify&&1==t.card.is_pay,expression:"is_identify==1 && card.is_pay==1 "}],staticClass:"btn_canc",attrs:{bgcolor:"#af773e",color:"#fff",size:"large",type:"warning"},nativeOn:{click:function(e){return t.setDate2(e)}}},[t._v("提交修改信息")])],1)])},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"people_ti"},[e("p",[this._v("填写资料"),e("span",{staticClass:"bg-yellow"},[this._v("(必填)")])])])}]};var u=a("C7Lr")(d,l,!1,function(t){a("mL/f")},null,null);e.default=u.exports}});
//# sourceMappingURL=27.d0d94b8dca8e1b462be1.js.map