webpackJsonp([44],{B1iu:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var i=e("3cXf"),o=e.n(i),n=e("xd7I"),r=e("w1gC"),s=e("efG5"),c=e.n(s);n.default.component(r.NavBar.name,r.NavBar),n.default.component(r.NavBarBackIcon.name,r.NavBarBackIcon),n.default.component(r.NavBarNextIcon.name,r.NavBarNextIcon);var d={data:function(){return{localIds:[],is_identify:"",card:{uname:"",number:"",mobile:"",frontid:"",backid:"",state_numb:22,title:"企业认证",is_pay:""}}},mounted:function(){this.category()},methods:{goback:function(){this.$router.go(-1)},category:function(){var t="/identify_edit?token="+window.storeWithExpiration.get("token"),a=this;this.$axios.get(t).then(function(t){1==t.data.data.is_identify&&(a.card.uname=t.data.data.name,a.card.mobile=t.data.data.telephone,a.card.number=t.data.data.idcode,a.localIds.push(t.data.data.verifyIdcodefront),a.is_identify=t.data.data.is_identify,a.card.is_pay=t.data.data.is_pay)})},addimg11:function(t,a){var e=this;c.a.ready(function(){c.a.chooseImage({count:1,sizeType:["original","compressed"],sourceType:["album","camera"],success:function(t){e.localIds=t.localIds,e.wxuploadImage(e.localIds)},fail:function(t){alert(o()(t))}})})},wxuploadImage:function(t){var a,e=this;t.length;a=t[0],window.__wxjs_is_wkwebview&&-1!=a.indexOf("wxlocalresource")&&(a=a.replace("wxlocalresource","wxLocalResource")),c.a.uploadImage({localId:a,isShowProgressTips:1,success:function(t){e.card.frontid=t.serverId},fail:function(t){alert("网络不佳，请稍后重试！")}})},removeimgs:function(){this.localIds=""},setDate:function(t){var a=this;if(""!=a.card.uname)return""==a.card.frontid?(a.$dialog.alert({mes:"请上传企业正面高清图片！"}),!1):(a.upload=function(){var t=new FormData;t.append("token",storeWithExpiration.get("token")),a.goodstoserver=o()(a.card),t.append("card",a.goodstoserver),a.$axios.post("/company_identify",t,{timeout:2e4}).then(function(t){return a.$dialog.toast({mes:"提交成功 等待审核",timeout:1500,icon:"success"}),a.$store.commit("card",a.card),a.$router.push({path:"/user/authentication/authenication_end"}),!1}).catch(function(t){})},setTimeout(a.upload,100),!1);a.$dialog.alert({mes:"企业名称不能为空！"})},setDate1:function(t){var a=this;if(""!=a.card.uname)return""==a.card.localIds?(a.$dialog.alert({mes:"请上传企业正面高清图片！"}),!1):(a.upload=function(){var t=new FormData;t.append("token",storeWithExpiration.get("token")),a.goodstoserver=o()(a.card),t.append("card",a.goodstoserver),a.$axios.post("/company_identify_edit",t,{timeout:2e4}).then(function(t){return a.$dialog.toast({mes:"提交成功 等待审核",timeout:1500,icon:"success"}),a.$store.commit("card",a.card),a.$router.push({path:"/user/authentication/authenication_end"}),!1}).catch(function(t){})},setTimeout(a.upload,100),!1);a.$dialog.alert({mes:"企业名称不能为空！"})},setDate2:function(t){var a=this;if(""!=a.card.uname)return""==a.card.localIds?(a.$dialog.alert({mes:"请上传企业正面高清图片！"}),!1):(a.upload=function(){var t=new FormData;t.append("token",storeWithExpiration.get("token")),a.goodstoserver=o()(a.card),t.append("card",a.goodstoserver),a.$axios.post("/company_identify_edit",t,{timeout:2e4}).then(function(t){return a.$dialog.toast({mes:"提交成功 等待审核",timeout:1500,icon:"success"}),a.$store.commit("card",a.card),a.$store.dispatch("getuserinfo"),a.$router.push({path:"/user/authentication/authentication_index"}),!1}).catch(function(t){})},setTimeout(a.upload,100),!1);a.$dialog.alert({mes:"企业名称不能为空！"})}}},l={render:function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("div",[i("div",{staticClass:"margin"},[i("yd-navbar",{attrs:{fixed:!0}},[i("router-link",{attrs:{slot:"left",to:""},slot:"left"},[i("yd-navbar-back-icon",{nativeOn:{click:function(a){return t.goback(a)}}},[t._v("返回")])],1),t._v(" "),i("p",{staticStyle:{"font-size":".3rem"},attrs:{slot:"center"},slot:"center"},[t._v("企业认证")]),t._v(" "),i("img",{staticStyle:{width:".5rem"},attrs:{slot:"right",src:e("rgYI")},slot:"right"})],1),t._v(" "),t._m(0),t._v(" "),i("yd-cell-group",[i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("企业名称：")]),t._v(" "),i("yd-input",{ref:"input9",attrs:{slot:"right",required:"",max:"20",placeholder:"请输入用户名"},slot:"right",model:{value:t.card.uname,callback:function(a){t.$set(t.card,"uname",a)},expression:"card.uname"}})],1),t._v(" "),i("div",{staticClass:"people_ti"},[i("p",[t._v("上传营业执照"),i("span",{staticClass:"bg-yellow"},[t._v("(必填)")])])]),t._v(" "),i("div",{staticClass:"upload_img11"},[i("p",[t._v("请营业执照正面高清图")]),t._v(" "),i("div",{staticClass:"upload_im_div11",attrs:{id:"three"},on:{click:function(a){return a.stopPropagation(),t.addimg11(a)}}}),t._v(" "),t._l(t.localIds,function(a,o){return i("div",{staticClass:"addimg11",model:{value:t.card.frontid,callback:function(a){t.$set(t.card,"frontid",a)},expression:"card.frontid"}},[i("div",{staticClass:"po_img_t11",on:{click:function(a){return t.removeimgs()}}},[i("img",{staticStyle:{position:"absolute",top:".1rem",right:"0.1rem",width:".5rem",height:".5rem"},attrs:{src:e("VgmL")}})]),t._v(" "),i("img",{attrs:{src:a}})])})],2)],1),t._v(" "),i("yd-button",{directives:[{name:"show",rawName:"v-show",value:0==t.is_identify&&0==t.card.is_pay,expression:"is_identify==0 && card.is_pay==0"}],staticClass:"btn_canc",attrs:{bgcolor:"#af773e",color:"#fff",size:"large",type:"warning"},nativeOn:{click:function(a){return t.setDate(a)}}},[t._v("确认提交并支付费用")]),t._v(" "),i("yd-button",{directives:[{name:"show",rawName:"v-show",value:1==t.is_identify&&0==t.card.is_pay,expression:"is_identify==1 && card.is_pay==0"}],staticClass:"btn_canc",attrs:{bgcolor:"#af773e",color:"#fff",size:"large",type:"warning"},nativeOn:{click:function(a){return t.setDate1(a)}}},[t._v("提交并支付费用")]),t._v(" "),i("yd-button",{directives:[{name:"show",rawName:"v-show",value:1==t.is_identify&&1==t.card.is_pay,expression:"is_identify==1 && card.is_pay==1 "}],staticClass:"btn_canc",attrs:{bgcolor:"#af773e",color:"#fff",size:"large",type:"warning"},nativeOn:{click:function(a){return t.setDate2(a)}}},[t._v("提交修改信息")])],1)])},staticRenderFns:[function(){var t=this.$createElement,a=this._self._c||t;return a("div",{staticClass:"people_ti"},[a("p",[this._v("填写资料"),a("span",{staticClass:"bg-yellow"},[this._v("(必填)")])])])}]};var u=e("C7Lr")(d,l,!1,function(t){e("zVcM")},null,null);a.default=u.exports},zVcM:function(t,a){}});
//# sourceMappingURL=44.529436edf6442cf351d2.js.map