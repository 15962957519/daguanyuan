webpackJsonp([47],{C2oD:function(t,e){},IbQi:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=i("IHPB"),s=i.n(o),n={data:function(){return{goodsinfo:{},page:1,pageSize:1,list:[]}},mounted:function(){this.loadList()},methods:{clickhref:function(t){try{this.$router.push({path:"/index/"+t})}catch(t){console.log(t)}},loadList:function(){var t=this,e=storeWithExpiration.get("token"),i=this.$route.query.user_id||0,o=this.$route.query.type||1;t.$axios.get("storeIndex",{params:{page:t.page,user_id:i,token:e,type:o}}).then(function(e){var i=e.data.data.mygoods;t.list=[].concat(s()(t.list),s()(i)),i.length<t.pageSize||1==t.page?t.$refs.infinitescrollDemo.$emit("ydui.infinitescroll.loadedDone"):(t.$refs.infinitescrollDemo.$emit("ydui.infinitescroll.finishLoad"),t.page++)})},goback:function(){this.$router.go(-1)}}},r={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticStyle:{"padding-top":"1rem"}},[i("yd-navbar",{attrs:{bgcolor:"#333",fixed:!0}},[i("router-link",{attrs:{slot:"left",to:""},slot:"left"},[i("yd-navbar-back-icon",{attrs:{color:"#fff"},nativeOn:{click:function(e){return t.goback(e)}}},[i("span",{staticStyle:{color:"#fff"}},[t._v("返回")])])],1),t._v(" "),i("p",{staticStyle:{"font-size":".3rem",color:"#fff"},attrs:{slot:"center"},slot:"center"},[t._v(t._s(this.$route.query.name)+"的店铺商品")])],1),t._v(" "),[i("yd-infinitescroll",{ref:"infinitescrollDemo",attrs:{callback:t.loadList}},[i("yd-list",{attrs:{slot:"list",theme:"1"},slot:"list"},t._l(t.list,function(e,o){return i("yd-list-item",{key:o,nativeOn:{click:function(i){return t.clickhref(e.goods_id)}}},[i("img",{attrs:{slot:"img",src:e.original_img},nativeOn:{click:function(i){return t.clickhref(e.goods_id)}},slot:"img"}),t._v(" "),i("span",{attrs:{slot:"title"},slot:"title"},[t._v(t._s(e.goods_name))]),t._v(" "),i("yd-list-other",{attrs:{slot:"other"},slot:"other"},[i("div",[i("span",{staticClass:"list-price"},[i("em",[t._v("¥")]),t._v(t._s(e.cur_price))])])])],1)}),1),t._v(" "),i("span",{attrs:{slot:"doneTip"},slot:"doneTip"},[t._v("期待更多商品~~")]),t._v(" "),i("img",{attrs:{slot:"loadingTip"},slot:"loadingTip"})],1)]],2)},staticRenderFns:[]};var l=i("C7Lr")(n,r,!1,function(t){i("C2oD")},"data-v-321de191",null);e.default=l.exports}});
//# sourceMappingURL=47.3ca1aabdb0f42421666f.js.map