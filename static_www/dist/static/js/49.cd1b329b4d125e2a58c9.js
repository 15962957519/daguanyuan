webpackJsonp([49],{iods:function(t,e){},z7wo:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i={data:function(){return{tab2:0,items:{new_cates:[],newlist:[]}}},mounted:function(){this.showData()},methods:{showData:function(){var t=this,e="/news_lists?token="+(window.storeWithExpiration.get("token")||"");t.$axios.get(e).then(function(t){if(200==t.status)return t.data.data}).then(function(e){console.log(555,e.new_cates),t.items=e}).catch(function(t){console.log(t)})},fn:function(t,e){console.log(t,e)},itemClick:function(t){var e=this,n=window.storeWithExpiration.get("token");e.items.newlist=[];t=t||0;return e.$dialog.loading.open("数据加载中"),setTimeout(function(){if(e.tab2=t,e.$dialog.loading.close(),0==t)var i="/news_lists?token="+n+"&cat_id=2";if(1==t)i="/news_lists?token="+n+"&cat_id=3";else if(2==t)i="/news_lists?token="+n+"&cat_id=4";else if(3==t)i="/news_lists?token="+n+"&cat_id=5";e.$axios.get(i).then(function(t){if(200==t.status)return t.data.data}).then(function(t){console.log(t.newlist),e.items.newlist=t.newlist}).catch(function(t){console.log(t)})},500),!1},new_result:function(t){var e=t;this.$router.push({name:"new_result_link",query:{article_id:e}})}},filters:{date:function(t){var e=new Date(1e3*t),n="",i=((new Date).getTime()-e.getTime())/1e3;i<300?n="刚刚":i>=300&&i<3600?n=parseInt(i/60)+"分钟前":i>=3600&&i<86400?n=parseInt(i/3600)+"小时前":i>=86400&&i<2592e3?n=parseInt(i/3600/24)+"天前":i>=2592e3&&i<31104e3?n=parseInt(i/3600/24/30)+"个月前":t>=31104e3&&(n=parseInt(i/3600/24/30/12)+"年前");e.getFullYear();var s=e.getMonth()+1,a=e.getDate(),o=e.getHours(),l=e.getMinutes();e.getSeconds();return n+" "+s+"-"+a+" "+o+":"+l}}},s={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("yd-tab",{attrs:{"active-color":"#af773e",callback:t.fn,"prevent-default":!1,"item-click":t.itemClick},model:{value:t.tab2,callback:function(e){t.tab2=e},expression:"tab2"}},t._l(t.items.new_cates,function(e,i){return n("yd-tab-panel",{key:i,attrs:{label:e.cat_name}},[t._l(t.items.newlist,function(i){return[e.cat_id==i.cat_id?[n("div",{staticClass:"new_list_first",on:{click:function(e){return t.new_result(i.article_id)}}},[n("div",{staticClass:"new_list_first_l"},[n("dl",[n("dt",[t._v(t._s(i.title))]),t._v(" "),n("dd",{domProps:{innerHTML:t._s(i.description)}})]),t._v(" "),n("div",{staticClass:"new_list_first_bottom"},[n("ul",[n("li",[t._v(t._s(t._f("date")(i.add_time)))]),t._v(" "),n("li",[t._v("来源："+t._s(i.author))])])])]),t._v(" "),n("div",{staticClass:"new_list_first_r"},[n("img",{attrs:{src:i.thumb}})])])]:t._e()]})],2)}),1)],1)},staticRenderFns:[]};var a=n("C7Lr")(i,s,!1,function(t){n("iods")},"data-v-116c12be",null);e.default=a.exports}});
//# sourceMappingURL=49.cd1b329b4d125e2a58c9.js.map