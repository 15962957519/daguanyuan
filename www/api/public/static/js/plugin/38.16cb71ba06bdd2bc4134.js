webpackJsonp([38],{227:function(e,t,a){a(593);var r=a(12)(a(360),a(522),"data-v-2a0e2741",null);e.exports=r.exports},360:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=a(20);t.default={data:function(){return{lists:[],userinfo:{src:""},userinfodata:{}}},components:{},mounted:function(){new Object;this.getuserqrode()},methods:{getuserfocus:function(e){axios.get(decodeURIComponent(r.dev.env.default_domain_api)+"/user/qrcode",{params:{token:storeWithExpiration.get("token"),userid:this.$route.query.userid}}).then(function(e){if("200"==e.status)return e.data}).then(function(e){console.log(e)}).catch(function(e){console.log(e)})},getuserqrode:function(){this.userinfodata=decodeURIComponent(r.dev.env.default_domain_api)+"/user/getUserImage?token="+storeWithExpiration.get("token")+"&userid="+this.$route.query.userid}},computed:{}}},426:function(e,t,a){t=e.exports=a(1)(),t.push([e.i,"body[data-v-2a0e2741],html[data-v-2a0e2741]{height:100%}#myqrcode[data-v-2a0e2741]{margin:0 auto;height:100%;background:#404040;max-width:620px;position:relative}#myqrcode .myqrv[data-v-2a0e2741]{position:fixed;padding:20px 0;margin:auto;background:#fff;border-radius:11px;text-align:center;left:50%;top:50%;transform:translate(-50%,-50%)}#myqrcode .myqrv .headerpic[data-v-2a0e2741]{width:90%;height:60px;overflow:hidden;margin:0 auto;text-align:left;margin-bottom:12px}#myqrcode .myqrv .headerpic img[data-v-2a0e2741]{display:inline-block}#myqrcode .myqrv .headerpic span[data-v-2a0e2741]{margin-left:10px}#myqrcode .qr[data-v-2a0e2741]{width:90%;margin:0 auto}#myqrcode .qr h1[data-v-2a0e2741]{color:gray;margin-top:8px}#myqrcode .qr img[data-v-2a0e2741]{width:100%;height:100%;border-radius:5px}#myqrcode .myqrv img[data-v-2a0e2741]{border-radius:5px;display:block}",""])},522:function(e,t){e.exports={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{attrs:{id:"myqrcode"}},[a("div",{staticClass:"myqrcodecontainer"},[a("div",{staticClass:"myqrv"},[a("img",{attrs:{src:e.userinfodata,width:"300",height:"400"}})])])])},staticRenderFns:[]}},593:function(e,t,a){var r=a(426);"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);a(10)("29a180a4",r,!0)}});
//# sourceMappingURL=38.16cb71ba06bdd2bc4134.js.map