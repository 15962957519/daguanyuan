webpackJsonp([32],{207:function(t,i,o){o(644);var e=o(10)(o(340),o(561),"data-v-c07f42d8",null);t.exports=e.exports},340:function(t,i,o){"use strict";function e(t){return t&&t.__esModule?t:{default:t}}var n=o(56),r=e(n),s=o(116),a=(e(s),o(115)),c=e(a),d=o(55),p=(e(d),o(54)),l=e(p),f=o(117),u=o(11),g=o(146),m=o(148);t.exports={components:{"nsr-loading":o(130),paymentframe:m},directives:{coutdowntime:{inserted:function(t){var i=(t.target,new Date),o=i.valueOf(),e=new Date(t.getAttribute("data-unixtime").replace(/-/g,"/")).valueOf();o>=e?t.innerHTML="拍卖结束 "+t.getAttribute("data-unixtime"):new g(t,{format:"距离结束 hh小时mm分ss秒",lastTime:t.getAttribute("data-unixtime")})}},collectdataupdate:{componentUpdated:function(){}}},watch:{$route:function(){this.$store.state.cardData=[],this.$store.state.page=1,this.$store.cardData=[]}},mounted:function(){this.$store.state.page=1,this.$store.state.busy=!1,this.$store.state.cardData=[],$("#slide3").swipeSlide({continuousScroll:!0,mode:"horizontal",loop:!1,lazyLoad:!1,autoSwipe:!0,autoplayStopOnLast:!1,autoplayDisableOnInteraction:!1,speed:3e3,transitionType:"cubic-bezier(0.22, 0.69, 0.72, 0.88)",firstCallback:function(t,i,o){o.find(".dot").children().first().addClass("cur")},callback:function(t,i,o){o.find(".dot").children().eq(t).addClass("cur").siblings().removeClass("cur")}})},methods:{userLikeProduct:function(t,i){this.$store.dispatch("actions_userLikeProduct_function",{good_id:t,e:i,page:this.$store.state.page,toast:l.default})},userFocus:function(t,i){this.$store.dispatch("actions_userFocus_function",{u_id:t,e:i,toast:l.default})},priviewimges:function(t,i){var o=i.target.style.backgroundImage;if(o.length>5){for(var e=(i.target,i.target.getAttribute("data-noimg")),n=[],r=0;r<t.length;r++)r==e&&(o=t[r].img),n.push(t[r].img);window.wx&&wx.previewImage({current:o,urls:n})}},chujia:function(t,i,o,e,n){var r=(0,u.rmoney)(i)+(0,u.rmoney)(e);r=Math.floor(100*r)/100,n.stopPropagation(),n.preventDefault(),$(".fixednumMain").find(".tipBanner .last").html(e+"元"),$(".fixednumMain").show(),$(".fixednumMask").show().animate({opacity:.382},100),$(".fixednumMain").show().animate({bottom:"0px"},100,"ease-in-out"),$(".fixednumMain .editTxt .hover").html(r),$(".fixednumMain .editTxt").attr("lastnum",e),$(".fixednumMain .editTxt").data("good_id",t),$(".fixednumMain .editTxt").attr("fixedprice",o),$(".fixednumMain .editTxt").attr("bidmoney",0),$(".fixednumMain .editTxt").attr("ever_add_price",i)},fetchData:function(t){if(this.$store.state.page>4&&this.$store.state.page<70)return axios.get("/user/singlebond",{params:{token:storeWithExpiration.get("token")}}).then(function(t){if("200"==t.status)return t.data}).then(function(t){if("2000"!=t.code)return c.default.alert("想查看更多，请支付保证金!").then(function(){return $(".paymengbid").show(),$(".fixednumMask").show().animate({opacity:.382},100),$(".paymengbid").show().animate({bottom:"0px"},100,"ease-in-out"),!1}),!1}).catch(function(t){console.log(t)}),!1;if(this.$store.state.page>=70)return c.default.alert("不支持显示更多页数据!"),!1;var i=this.$route.query.cat_id||0,o=this.$store.state.page||1;if(i>0)var e="/product/indexjinpinprolist?page="+o+"&cat_id="+i;else var e="/product/indexjinpinprolist?page="+o;this.$store.dispatch("getDataforindex",{progress:t,refresh:!1,usl:e})},bidMore:function(t,i,o){o+=1,this.$store.dispatch("getDataforindexbondlist",{progress:this,e:i,refresh:!1,page:o,goods_id:t})},bidless:function(t,i,o){this.$store.dispatch("getDataforindexbondlist",{progress:this,e:i,refresh:!1,page:o+"",goods_id:t})},loadMore:function(){var t=(0,l.default)({message:"正在努力加载",position:"bottom",duration:2e3});return this.toast=t,this.$store.state.busy=!0,this.fetchData(this),!1},share:function(t,i,o,e){$("#wpt-share").animate({display:"block",height:$(document).height(),overflow:"hidden",opacity:"0.5"}),$("body,html").css({overflow:"hidden"}),$(document).on("click",function(){var t=t||window.event,i=t.target||t.srcElement;"wpt-share"==i.id&&$("#wpt-share").hide(),$("body,html").css("overflow","auto"),$("#wpt-share").hide()}),e.preventDefault(),e.stopPropagation();var n=default_domain_web+"/"+o.path+"?goods_id="+o.query.goods_id,s="";if(null!=i&&"object"==("undefined"==typeof i?"undefined":(0,r.default)(i))){for(var a in i){s=i[a].img;break}(0,u.commonsharejs)(t,n,s)}else(0,u.commonsharejs)(t,n,"")},frined:function(t){wx.onMenuShareTimeline({title:"",link:"",imgUrl:"",success:function(){alert("分享成功"),$("#wpt-share").hide()},cancel:function(){$("#wpt-share").hide()}})},gfrined:function(t){wx.onMenuShareAppMessage({title:"",desc:"",link:"",imgUrl:"",type:"",dataUrl:"",success:function(){alert("分享成功"),$("#wpt-share").hide()},cancel:function(){$("#wpt-share").hide()}})},descChange:function(t){var i=t.target;$(i).siblings(".desc").addClass("fullDesc"),$(i).remove()}},computed:(0,f.mapState)({proresults:function(t){return t.cardData},isloadingComplete:function(t){return t.isloadingComplete},busy:function(t){return t.busy},like_products:function(t){return t.like_products}})}},477:function(t,i,o){i=t.exports=o(0)(),i.i(o(488),""),i.push([t.i,"",""])},488:function(t,i,o){i=t.exports=o(0)(),i.push([t.i,'.l{float:left}.r{float:right}.pm{width:50%!important}#productlistsforjinpin .left:after{content:"";display:block;width:100%;height:36px}#productlistsforjinpin .icon_lists{color:#333}#productlistsforjinpin{max-width:640px}#productlistsforjinpin .icon_lists .icon{min-width:1em;font-size:30px;-webkit-transition:font-size .25s ease-out 0s;transition:font-size .25s ease-out 0s}@font-face{font-family:iconfont;src:url("//w.tianbaoweipai.com/static/font/index.eot?t=1484042365502");src:url("//w.tianbaoweipai.com/static/font/index.eot?t=1484042365502#iefix") format("embedded-opentype"),url("//w.tianbaoweipai.com/static/font/index.woff?t=1484042365502") format("woff"),url("//w.tianbaoweipai.com/static/font/index.ttf?t=1484042365502") format("truetype"),url("//w.tianbaoweipai.com/static/font/index.svg?t=1484042365502#index") format("svg")}#productlistsforjinpin .iconfont{font-family:iconfont!important;font-size:16px;color:inherit;font-style:normal;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}#productlistsforjinpin .fi-stack{position:relative;display:inline-block;width:2em;height:2em;line-height:2em;vertical-align:middle}#productlistsforjinpin .fi-stack-0x6,#productlistsforjinpin .fi-stack-0x8,#productlistsforjinpin .fi-stack-1x,#productlistsforjinpin .fi-stack-1x2,#productlistsforjinpin .fi-stack-1x5,#productlistsforjinpin .fi-stack-2x{position:absolute;left:0;width:100%;font-size:2em;text-align:center;line-height:inherit}#productlistsforjinpin .fi-stack-0x6{transform:scale(.3);-webkit-transform:scale(.3);transform-origin:center;-webkit-transform-origin:center}#productlistsforjinpin .fi-stack-0x8{transform:scale(.4);-webkit-transform:scale(.4);transform-origin:center;-webkit-transform-origin:center}#productlistsforjinpin .fi-stack-1x2{transform:scale(.6);-webkit-transform:scale(.6);transform-origin:center;-webkit-transform-origin:center}#productlistsforjinpin .fi-stack-1x5{transform:scale(.75);-webkit-transform:scale(.75);transform-origin:center;-webkit-transform-origin:center}#productlistsforjinpin .fi-stack-1x{transform:scale(.5);-webkit-transform:scale(.5);transform-origin:center;-webkit-transform-origin:center}#productlistsforjinpin .fi-stack-2x{font-size:2em}#productlistsforjinpin .verifyState1{font-size:14px!important}#productlistsforjinpin .icon.salelevel{font-size:14px}#productlistsforjinpin .icon-iconfuzhi01:before{content:"\\F000F"}#productlistsforjinpin .icon-fenxiang:before{content:"\\E631"}#productlistsforjinpin .icon-xin:before{content:"\\E64B"}#productlistsforjinpin .icon-baoyou:before{content:"\\E611"}#productlistsforjinpin .icon-gerendengji3:before{content:"\\E657"}#productlistsforjinpin .icon-gerendengji4:before{content:"\\E658"}#productlistsforjinpin .icon-gerendengji2:before{content:"\\E65A"}#productlistsforjinpin .icon-gerendengji5:before{content:"\\E65B"}#productlistsforjinpin .icon-gerendengji6:before{content:"\\E65C"}#productlistsforjinpin .icon-baoyou1:before{content:"\\E640"}#productlistsforjinpin .icon-gerendengji1:before{content:"\\E67E"}#productlistsforjinpin .icon-baotui:before{content:"\\E6B0"}#productlistsforjinpin .icon-combinedshape:before{content:"\\E61A"}#productlistsforjinpin .icon-dengji3:before{content:"\\E61E"}#productlistsforjinpin .icon-dengji1:before{content:"\\E628"}#productlistsforjinpin .icon-dengji2:before{content:"\\E629"}#productlistsforjinpin .icon-dengji5:before{content:"\\E62C"}#productlistsforjinpin .icon-dengji6:before{content:"\\E62D"}#productlistsforjinpin .icon-dengji7:before{content:"\\E62E"}#productlistsforjinpin .icon-dengjiv1:before{content:"\\E601"}#productlistsforjinpin .icon-dengjiv3:before{content:"\\E602"}#productlistsforjinpin .icon-dengjiv4:before{content:"\\E604"}#productlistsforjinpin .icon-dengjiv5:before{content:"\\E605"}#productlistsforjinpin .icon-dengjiv2:before{content:"\\E607"}#productlistsforjinpin .icon-huiyuan:before{content:"\\E62F"}#productlistsforjinpin .icon-yanjing:before{content:"\\E619"}#productlistsforjinpin .icon-yanjing1:before{content:"\\E63D"}#productlistsforjinpin .icon-7tianbaotui:before{content:"\\E6CC"}#productlistsforjinpin .icon-anquan:before{content:"\\E603"}#productlistsforjinpin .wpt-share{position:fixed;width:100%;height:100%;top:0;left:0;bottom:50px;background-color:rgba(0,0,0,.5);display:none}#productlistsforjinpin .share-box{position:fixed;width:7.5rem;top:auto;bottom:50px;background-color:#efeff4;z-index:1999}#productlistsforjinpin .share-box.fill-ip{animation:flipUp .3s ease-out;visibility:visible!important}#productlistsforjinpin .share-box .bt-share{position:relative;width:33%;font-size:12px;float:left;text-align:center;text-decoration:none;padding:60px 20px}#productlistsforjinpin .share-box .title{color:#424242;margin:0 auto;text-align:center}#productlistsforjinpin .share-box .title h1{margin:5px auto}#productlistsforjinpin .share-box .bt-sharef .icon{position:absolute;width:50px;height:50px;top:0;left:50%;margin-left:-25px;background:url('+o(143)+") no-repeat 0 0 scroll;background-size:contain;border:1px solid #def}#productlistsforjinpin .share-box .bt-sharefg .icon{position:absolute;width:50px;height:50px;top:0;left:50%;border:1px solid #def;margin-left:-25px;background:url("+o(144)+') no-repeat 0 0 scroll;background-size:contain}#productlistsforjinpin .saleMain .title{width:70%;font-size:.2rem;font-weight:600;display:inline-block;float:left}#productlistsforjinpin .saleMain .desc.fullDesc{width:100%;position:relative;line-height:21px;max-height:64px;overflow:hidden}#productlistsforjinpin .saleMain .desc.fullDesc:after{content:"...";position:absolute;bottom:0;right:0;padding-left:40px;background:-webkit-linear-gradient(left,transparent,#fff 55%);background:linear-gradient(90deg,transparent,#fff 55%)}#productlistsforjinpin .saleMain .bidBtnjinpin{margin-top:10px}#productlistsforjinpin .saleMain .popularity{background-image:url(/static/img/review.png);padding-left:0;color:#888;background-position:right 50%;background-repeat:no-repeat;width:auto;height:26px;float:right;background-size:16px;padding:0 22px 0 10px;line-height:26px;font-size:13px;color:#fe0100}#productlistsforjinpin .saleMain .bidBtnjinpin a{width:76px;display:block;text-align:center;height:29px;float:left;padding:2px 1px;line-height:29px;color:#c60;border-color:#c60;border:1px solid;border-radius:5px}#productlistsforjinpin .saleMain .officialspecial{width:70px;height:29px;text-align:center;margin-right:10px;float:left;line-height:29px;padding:2px 0;color:#270;border-color:#270;border:1px solid;border-radius:5px}#productlistsforjinpin .saleMain .price{color:#ff0909}#productlistsforjinpin .saleMain .imgList{width:90%;overflow:auto}#productlistsforjinpin .saleMain .saleItem{display:block!important}#productlistsforjinpin .saleMain .imgList div{width:auto;height:176px!important;overflow:hidden;background-repeat:no-repeat;background-size:contain;margin-bottom:15px;background-position:50%}#productlistsforjinpin .wptShare{display:none;opacity:.6}#productlistsforjinpin .wptShare,#productlistsforjinpin .wptShare .wptMask{position:fixed;top:0;bottom:0;width:100%;height:100%;background-color:#000;z-index:1999}#productlistsforjinpin .wptShare .wptMask{left:0;right:0}#productlistsforjinpin .wptShare .shareTip{position:fixed;top:0;width:96%;height:286px;background-image:url('+o(145)+");background-repeat:no-repeat;background-size:auto 55%;background-position:100% 0;z-index:2000}#productlistsforjinpin .verifyState1.membercolorv1{color:#c7c7cc;background-color:#757373;background-image:url(/static/img/nomember.png);background-repeat:no-repeat;background-size:cover;background-position:50%}#productlistsforjinpin .verifyState1.membercolorv5{background-image:url(/static/img/diamond.png);background-repeat:no-repeat;background-size:cover;background-color:#fff;background-position:50%}#productlistsforjinpin .verifyState1.membercolorv4{background-image:url(/static/img/gold_medal.png);background-repeat:no-repeat;background-size:cover;background-color:#fff;background-position:50%}#productlistsforjinpin .verifyState1.membercolorv3{background-image:url(/static/img/silver.png);background-repeat:no-repeat;background-size:cover;background-color:#fff;background-position:50%}#productlistsforjinpin .verifyState1.membercolorv2{background-image:url(/static/img/bronze.png);background-repeat:no-repeat;background-size:cover;background-color:#fff;background-position:50%}#productlistsforjinpin .verifyState1.membercolorv{color:#c7c7cc;background-color:#757373}#productlistsforjinpin .verifyState1.membercolorv.ismember{color:#fff;background-color:#e00}",""])},561:function(t,i){t.exports={render:function(){var t=this,i=t.$createElement,o=t._self._c||i;return o("div",{staticClass:"clear",attrs:{id:"productlistsforjinpin"}},[o("div",{staticClass:"slide clearfix",attrs:{id:"slide3"}},[o("ul",[o("li",[o("router-link",{attrs:{to:"/usersellindex/mumberupgrade"}},[o("img",{attrs:{src:"http://w.tianbaoweipai.com/static/img/adv/1707/001.jpg",alt:"首页轮播图"}})])],1),t._v(" "),o("li",[o("router-link",{attrs:{to:"/usersellindex/mumbercheck"}},[o("img",{attrs:{src:"http://w.tianbaoweipai.com/static/img/adv/1706/002.jpg",alt:"首页轮播图"}})])],1),t._v(" "),o("li",[o("router-link",{attrs:{to:"/usersellindex/mumbercheck"}},[o("img",{attrs:{src:"http://w.tianbaoweipai.com/static/img/adv/1706/003.png",alt:"首页轮播图"}})])],1)]),t._v(" "),t._m(0)]),t._v(" "),o("div",{directives:[{name:"infinite-scroll",rawName:"v-infinite-scroll",value:t.loadMore,expression:"loadMore"}],staticClass:"saleMain",attrs:{"infinite-scroll-disabled":"busy","infinite-scroll-distance":"10"}},t._l(t.proresults,function(i){return o("div",{staticClass:"saleItem clearfix"},[o("div",{staticClass:"pm l"},[o("div",{staticClass:"imgList"},t._l(i.nowaterimg,function(e,n){return o("div",{directives:[{name:"lazy",rawName:"v-lazy:background-image",value:e.img,expression:"item.img",arg:"background-image"}],staticClass:"lazyLoad",attrs:{"data-noimg":n},on:{click:function(o){t.priviewimges(i.img,o)}}})}))]),t._v(" "),o("div",{staticClass:"pm r"},[o("div",{staticClass:"title"},[t._v(t._s(i.goods_name))]),t._v(" "),o("div",{staticClass:"popularity"},[t._v(t._s(i.click_count))]),t._v(" "),o("div",{staticClass:"desc fullDesc",domProps:{innerHTML:t._s(i.goods_content)}}),t._v(" "),o("div",{staticClass:"price"},[o("span",[t._v(t._s(i.start_price))])]),t._v(" "),o("div",{staticClass:"bidBtnjinpin"},[o("router-link",{staticClass:"officialspecial",attrs:{tag:"div",to:{name:"goodsproductdetail",params:{goods_id:i.goods_id}}}},[t._v("详情")]),t._v(" "),o("router-link",{attrs:{to:{name:"goodsproductdetail",params:{goods_id:i.goods_id}}}},[2!=i.goods_status?o("em",[t._v("出  价")]):t._e()])],1)])])}))])},staticRenderFns:[function(){var t=this,i=t.$createElement,o=t._self._c||i;return o("div",{staticClass:"dot"},[o("span"),t._v(" "),o("span"),t._v(" "),o("span")])}]}},644:function(t,i,o){var e=o(477);"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);o(9)("1be9df18",e,!0)}});
//# sourceMappingURL=32.364026ea5287f5032165.js.map