webpackJsonp([53],{"5Ku6":function(n,e,t){"use strict";var i=t("NUMX"),o=t.n(i),a=t("IHPB"),r=t.n(a),c=t("aozt"),l=t.n(c),u=t("efG5"),m=t.n(u),h=t("dhIU"),d=t.n(h);t("wtEF"),t("zsLt"),t("ZLEe");var p=new Object;function f(n){var e=new RegExp("(^|&)"+n+"=([^&]*)(&|$)","i"),t=window.location.search.substr(1).match(e);return null!=t?t[2]:null}p.is_weixn=function(){return"micromessenger"==navigator.userAgent.toLowerCase().match(/MicroMessenger/i)};t("Kww8");p.is_weixn()?(p.login=function(n,e,t){var i,o,a;if(window.storeWithExpiration.get("token")||""){r=window.storeWithExpiration.get("fromurlrouter")||"";if(window.storeWithExpiration.set("fromurlrouter","",0),e(),r)try{n(JSON.parse(r).path)}catch(e){n()}else n()}else{var r;0==(r=window.storeWithExpiration.get("fromurlrouter")||"").length&&window.storeWithExpiration.set("fromurlrouter",t,50),i=d.a.weixing.appID,o=window.location.protocol+"//"+window.location.host+"/#/login",a="https://open.weixin.qq.com/connect/oauth2/authorize?appid="+i+"&redirect_uri="+encodeURIComponent(o)+"&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect",location.href=a}return!0},p.getLogin=function(n){l.a.defaults.baseURL=d.a.default_domain_api,l.a.get("user/getAccess_token?code="+n+"&state=123").then(function(n){return n.data}).then(function(n){"2000"==n.statue_code&&n.user&&(window.storeWithExpiration.set("token",n.token,1),window.location.href=d.a.default_domain)}).catch(function(n){alert(n)})},p.commonsharejs=function(n,e,t,i){m.a.onMenuShareTimeline({title:n,link:e,imgUrl:t,success:function(){},cancel:function(){}}),m.a.onMenuShareAppMessage({title:n,desc:i,link:e,imgUrl:t,type:"link",dataUrl:"",success:function(){},cancel:function(){}}),m.a.onMenuShareQQ({title:n,desc:i,link:e,imgUrl:t,success:function(){},cancel:function(){}}),m.a.onMenuShareWeibo({title:n,desc:i,link:e,imgUrl:t,success:function(){},cancel:function(){}}),m.a.onMenuShareQZone({title:n,desc:i,link:e,imgUrl:t,success:function(){},cancel:function(){}})}):p.login=function(n,e,t){n()},p.isEmptyObject=function(n){var e;for(e in n)return!1;return!0},p.is_code_state=function(){var n=f("code")||"";if(n.replace(/(^s*)|(s*$)/g,"").length>0){window.storeWithExpiration.set("code",n,1);return n}return""},p.GetQueryString=function(n){var e=new RegExp("(^|&)"+n+"=([^&]*)(&|$)","i"),t=window.location.search.substr(1).match(e);return null!=t?t[2]:null},p.checkmobile=function(n){return new RegExp(/^1[\d]{10}$/,"i").test(n)},p.Util={arrayMax:function(n){return Math.max.apply(Math,r()(n))},trim:function(n){return(n=n||"").replace(/^\s+|\s+$/g,"")||""},ltrim:function(n){return n.replace(/^\s+/,"")},rtrim:function(n){return n.replace(/\s+$/,"")},truncate:function(n,e){return n.length>e&&(n=n.substring(0,e)),n},onlyLetters:function(n){return n.toLowerCase().replace(/[^a-z]/g,"")},onlyLettersNums:function(n){return n.toLowerCase().replace(/[^a-z,0-9]/g,"")},anagrams:function(n){return _anagrams=function(n){function e(e){return n.apply(this,arguments)}return e.toString=function(){return n.toString()},e}(function(n){return n.length<=2?2===n.length?[n,n[1]+n[0]]:[n]:n.split("").reduce(function(e,t,i){return e.concat(_anagrams(n.slice(0,i)+n.slice(i+1)).map(function(n){return t+n}))},[])}),s=n,_anagrams(s)},capitalize:function(n){var e=o()(n),t=e[0],i=e.slice(1),a=arguments.length>1&&void 0!==arguments[1]&&arguments[1];return t.toUpperCase()+(a?i.join("").toLowerCase():i.join(""))},capitalizeEveryWord:function(n){return n.replace(/\b[a-z]/g,function(n){return n.toUpperCase()})},escapeRegExp:function(n){return n.replace(/[.*+?^${}()|[\]\\]/g,"\\$&")},fromCamelCase:function(n){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"_";return n.replace(/([a-z\d])([A-Z])/g,"$1"+e+"$2").replace(/([A-Z]+)([A-Z][a-z\d]+)/g,"$1"+e+"$2").toLowerCase()},reverseString:function(n){return[].concat(r()(n)).reverse().join("")},sortCharactersInString:function(n){return n.split("").sort(function(n,e){return n.localeCompare(e)}).join("")},toCamelCase:function(n){return n.replace(/^([A-Z])|[\s-_]+(\w)/g,function(n,e,t,i){return t?t.toUpperCase():e.toLowerCase()})},truncateString:function(n,e){return n.length>e?n.slice(0,e>3?e-3:e)+"...":n},currentURL:function(){return window.location.href}};var _=t("dhIU"),g=t("BvhE").Base64,b="logo/weipai.png?x-oss-process=image/resize,P_20";b=(b=(b=g.encode(b)).replace(/\+/,"-")).replace(/\//,"_"),b=p.Util.rtrim(b),p.getCurlofimgUsenoAuth=function(n,e,t,i){try{if(void 0===e&&(e=Math.ceil(200*Math.random())+600),void 0===t&&(t=600),void 0===i&&(i=!1),1==i)var o="image/resize,m_fill,h_"+e+",w_"+t+"/sharpen,100/watermark,image_"+b+",t_90,g_se,x_10,y_35,color_808080,image/bright,10",a=_.cdntianbao+"/"+n+"?x-oss-process="+o;else o="image/resize,m_fill,h_"+e+",w_"+t+"/sharpen,100,t_90,g_se,x_10,y_35,color_808080,image/bright,10",a=_.cdntianbao+"/"+n+"?x-oss-process="+o;return a}catch(n){return console.log(n),""}},p.priviewimges=function(n,e,t){var i=e.target.style.backgroundImage||e.target.src;if(i.length>5){e.target;for(var o=e.target.getAttribute("data-noimg"),a=[],r="",c=0;c<n.length;c++)r=p.getCurlofimgUsenoAuth(n[c].img,1200,1200,!0),c==o&&(i=r),a.push(r);m.a&&m.a.previewImage({current:i,urls:a}),console.log(m.a);var u=new FormData;u.append("token",storeWithExpiration.get("token")),u.append("goods_id",t),l.a.post("/product/addclick",u).then(function(n){if(200==n.status)return n.data}).then(function(n){}).catch(function(n){})}};e.a=p},DSVU:function(n,e,t){"use strict";(function(n){var e=t("3cXf"),i=t.n(e),o=t("hRKE"),a=t.n(o);!function(e){var i=!1;if("function"==typeof define&&t("Ycmu")&&(define(e),i=!0),"object"==("undefined"==typeof exports?"undefined":a()(exports))&&(n.exports=e(),i=!0),!i){var o=window.Cookies,r=window.Cookies=e();r.noConflict=function(){return window.Cookies=o,r}}}(function(){function n(){for(var n=0,e={};n<arguments.length;n++){var t=arguments[n];for(var i in t)e[i]=t[i]}return e}return function e(t){function o(e,a,r){var c;if("undefined"!=typeof document){if(1<arguments.length){if("number"==typeof(r=n({path:"/"},o.defaults,r)).expires){var l=new Date;l.setMilliseconds(l.getMilliseconds()+864e5*r.expires),r.expires=l}r.expires=r.expires?r.expires.toUTCString():"";try{c=i()(a),/^[\{\[]/.test(c)&&(a=c)}catch(e){}a=t.write?t.write(a,e):encodeURIComponent(String(a)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),e=(e=(e=encodeURIComponent(String(e))).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent)).replace(/[\(\)]/g,escape);var u="";for(var s in r)r[s]&&(u+="; "+s,!0!==r[s]&&(u+="="+r[s]));return document.cookie=e+"="+a+u}e||(c={});for(var m=document.cookie?document.cookie.split("; "):[],h=/(%[0-9A-Z]{2})+/g,d=0;d<m.length;d++){var p=m[d].split("="),f=p.slice(1).join("=");this.json||'"'!==f.charAt(0)||(f=f.slice(1,-1));try{var _=p[0].replace(h,decodeURIComponent);if(f=t.read?t.read(f,_):t(f,_)||f.replace(h,decodeURIComponent),this.json)try{f=JSON.parse(f)}catch(e){}if(e===_){c=f;break}e||(c[_]=f)}catch(e){}}return c}}return(o.set=o).get=function(n){return o.call(o,n)},o.getJSON=function(){return o.apply({json:!0},[].slice.call(arguments))},o.defaults={},o.remove=function(e,t){o(e,"",n(t,{expires:-1}))},o.withConverter=e,o}(function(){})})}).call(e,t("VC+f")(n))},Drwf:function(n,e){},Jtfc:function(n,e){},NHnr:function(n,e,t){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=t("xd7I"),o={render:function(){this.$createElement;this._self._c;return this._m(0)},staticRenderFns:[function(){var n=this.$createElement,e=this._self._c||n;return e("div",{staticClass:"loading"},[e("img",{staticStyle:{width:"100%"},attrs:{src:t("hF2f")}})])}]};var a={name:"App",data:function(){return{loadingFlag:!1}},created:function(){try{document.body.removeChild(document.getElementById("appLoading")),setTimeout(function(){document.getElementById("app").style.display="block"},1500)}catch(n){}},methods:{hasLoad:function(){this.loadingFlag=!0}},components:{Loading:t("C7Lr")({},o,!1,function(n){t("yA+7")},null,null).exports}},r={render:function(){var n=this.$createElement,e=this._self._c||n;return e("div",{attrs:{id:"app"}},[e("router-view",{on:{hasLoad:this.hasLoad}})],1)},staticRenderFns:[]};var c=t("C7Lr")(a,r,!1,function(n){t("iIN8")},null,null).exports,l=t("KGCO"),u={render:function(){this.$createElement;this._self._c;return this._m(0)},staticRenderFns:[function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("div",{staticClass:"agreement"},[t("strong",{staticStyle:{color:"rgb(175, 119, 62)"}},[n._v("艺品芳华VIP店铺服务协议")]),n._v(" "),t("p",[t("strong"),t("br"),n._v("\n               “艺品芳华微在线代理销售和结算服务”（以下简称“本服务”）是由台州众拓电子商务有限公司向艺品芳华微商城商户（以下简称“您”）提供的“艺品芳华微商城”软件系统（以下简称“本系统”）及（或）附随的商品代理销售和货款结算服务，方便艺品芳华微商城商户的买家通过艺品芳华微商城软件系统，并提供通过本系统集成的第三方支付网关完成付款，艺品芳华微商城收到买家货款后，依据艺品芳华微商城商户的指令结算相应货款。本协议由您和艺品芳华签订。 "),t("br"),n._v("\n                "),t("strong",[n._v("声明与承诺")]),t("strong"),t("br"),n._v("\n                一、您确认，在您申请开通艺品芳华微商城店铺的代理销售和结算服务之前，您已充分阅读、理解并接受本协议的全部内容，一旦您使用本服务，即表示您同意遵循本协议的所有约定。"),t("br"),n._v("\n        二、您同意，艺品芳华有权随时对本协议内容进行单方面的变更，并以在艺品芳华网站公告的方式予以公布，无需另行单独通知您；若您在本协议内容公告变更后继续使用本服务的，表示您已充分阅读、理解并接受修改后的协议内容，也将遵循修改后的协议内容使用本服务；若您不同意修改后的协议内容，您应停止使用本服务。 三、您声明，在您同意接受本协议并注册开通艺品芳华微商城店铺时，您是具有法律规定的完全民事权利能力和民事行为能力，能够独立承担民事责任的自然人、法人或其他组织；本协议内容不受您所属国家或地区的排斥。不具备前述条件的，您应立即终止注册或停止使用本服务。"),t("br"),n._v(" "),t("br"),n._v("\n                "),t("strong",[n._v("艺品芳华微商城代理销售和结算服务概要")]),t("strong"),t("br"),n._v("\n                   一、艺品芳华微商城店铺账户：指在您使用本服务时，艺品芳华向您提供的店铺唯一编号。您可自行设置密码，提交商品信息，并用以查询或计算您的货款。"),t("br"),n._v("\n        二、艺品芳华微商城代理销售服务：是指买卖双方使用本系统，且约定买卖合同项下的交易款由买方通过本系统集成的第三方支付网关以电子货币的方式支付到艺品芳华第三方支付网关的账户，艺品芳华第三方支付网关的账户在收到该款项后将交易货款记录到您的店铺账户记录中，但实际由艺品芳华代为收取该款项的一种服务。在您使用本服务时，除适用艺品芳华微商城代理销售服务的相关约定外，还将优先适用以下条款："),t("br"),n._v("\n        1、艺品芳华为您代理销售的交易货款系由您的交易对方通过第三方支付网关以电子货币付款的方式支付至艺品芳华的第三方支付网关账户，通过艺品芳华系统记录到您的艺品芳华微商城店铺账户记录内。您理解并同意，在您的交易对方通过第三方支付网关将电子货币支付至艺品芳华第三方支付网关账户的过程需要一定的时间，在第三方支付网关告知艺品芳华已收到您的交易对方支付的交易货款后，艺品芳华将向您的艺品芳华微商城店铺账户记录该笔交易货款。"),t("br"),n._v("\n        2、艺品芳华为您代理销售产生的交易货款系由买家通过第三方支付网关以电子货币付款的方式支付至艺品芳华第三方支付网关账户，第三方支付网关会因此向您单独收取费用，您理解并同意，该费用是第三方支付网关基于其向您提供的支付服务所收取的费用，与艺品芳华向您提供的本项服务无关。"),t("br"),n._v("\n        3、您在选用本项服务作为交易支付方式后，该支付行为能否完成取决于您的交易对方是否选用第三方支付网关方式支付，在交易对方无法通过第三方支付网关支付时，艺品芳华将提示您的交易对方重新选择艺品芳华的其他支付方式。"),t("br"),n._v("\n        三、艺品芳华微商城结算服务：即艺品芳华向您提供货款结算的中介服务，其中包含： 1、货款支付：您可以要求艺品芳华向其支付自己的货款。当您向艺品芳华做出结算指示时，必须提供一个与您或您的公司名称相符的有效的中华人民共和国境内（不含港澳台）银行账户，艺品芳华将于收到指示后的一至十五个工作日内，将相应的款项汇入您提供的有效收款账户。除本条约定外，艺品芳华不提供其他受领方式。"),t("br"),n._v("\n        2、 系统查询：艺品芳华将对您在本系统中的所有操作进行记录，不论该操作之目的最终是否实现。您可以在本系统中实时查询其艺品芳华微商城店铺账户名下的交易记录，若您认为记录有误的，您可向艺品芳华提出异议，艺品芳华将向您提供艺品芳华按照您的指示操作产生的收付款记录。并且您认同此记录为您交易记录的最终依据，不再对此有异议。 "),t("br"),n._v("\n        3、 款项专属：对通过您艺品芳华微商城店铺账户收到的货款，一艺品芳华将予以妥善保管，除本协议另行规定外，不作任何其他非您指示的用途。艺品芳华通过您的用户名和密码识别您的指示，请您妥善保管您的用户名和密码，对于因密码泄露所致的损失，由您自行承担。本服务所涉及到的任何款项只以人民币计结，不提供任何形式的外币兑换业务。 "),t("br"),n._v("\n        4、 异常交易处理：您使用本服务时，可能由于微信本身系统问题、微信相关作业网络连线问题或其他不可抗拒因素，造成暂时无法提供本服务。"),t("br"),n._v("\n        四、软件服务费：是指您使用本服务应向艺品芳华支付的费用。"),t("br"),n._v("\n        五、服务期：是指您支付软件服务费后，使用本服务的期限。"),t("br"),n._v("\n        六、订购成功：是指您同意本协议内容、成功支付软件服务费，且在您的店铺后台显示“已订购艺品芳华微商城”。"),t("br"),n._v("\n        七、续费：已经订购过艺品芳华微商城代理销售和结算服务，再一次付费订购的即为续费。"),t("br"),n._v(" "),t("br"),n._v("\n                "),t("strong",[n._v("艺品芳华微商城店铺账户")]),t("strong"),t("br"),n._v("\n                 一、注册相关"),t("br"),n._v("\n        （一）、在您注册艺品芳华微商城店铺账户时，您需提供手机号码，并正确填写验证码及相关信息艺品芳华，方能成功注册艺品芳华微商城店铺。"),t("br"),n._v("\n        （二）、您注册完成，取得艺品芳华提供给您的“艺品芳华微商城店铺账户”（以下简称该账户）并且支付软件服务费成功订购艺品芳华微商城代理销售和结算服务后，方可使用本服务。且使用本服务时，您同意："),t("br"),n._v("\n        1、 依本服务注册表之提示准确提供并在取得该账户后及时更新正确、最新及完整的资料。一旦艺品芳华发现您提供的资料错误、不实、过时或不完整的，艺品芳华有权暂停或终止向您提供部分或全部“艺品芳华微商城服务”，由此产生的任何直接或间接费用由您自行承担，艺品芳华对此不承担任何责任。"),t("br"),n._v("\n        2、 因您未及时更新资料，导致本服务不能提供或提供时发生任何错误的，您不得将此作为取消交易、拒绝发货的理由，您承担因此产生的一切后果，艺品芳华不承担任何责任。"),t("br"),n._v("\n        3、 您应3、如您发现有他人冒用或盗用您的账户及密码或任何其他未经合法授权之情形时，应立即修改账号密码并妥善保管，或立即以有效方式（包括但不限于电话、邮件等方式）通知艺品芳华，要求艺品芳华暂停相关服务。如要求艺品芳华暂停相关服务的，艺品芳华将根据您的情况，暂停提供相对其艺品芳华微商城店铺账户负责，只有您或您指定的管理员可以使用您的艺品芳华微商城店铺账号。在您决定不再使用该账户时，您应将该账户下所对应的可用款项全部结算，并向艺品芳华申请删除该账户。您同意，若您丧失全部或部分民事权利能力或民事行为能力，艺品芳华有权根据有效法律文书（包括但不限于生效的法院判决、生效的遗嘱等）处置您的艺品芳华微商城店铺账户相关的款项。"),t("br"),n._v("\n        4、遵守本协议、甲乙双方签订的其他协议及其他在艺品芳华商家公告上发布的规则、公告。"),t("br"),n._v("\n        （三）您注册完成，取得艺品芳华提供给您的“艺品芳华微商城店铺账户”（以下简称该账户）后，可以以游客会员的身份体验艺品芳华相关的服务权益。"),t("br"),n._v("\n        二、账户安全"),t("br"),n._v("\n        您将对使用该账户及密码进行的一切操作及言论负完全的责任，因此您同意："),t("br"),n._v("\n        1、不向其他任何人泄露该账户及密码，亦不使用其他任何人的“艺品芳华微商城店铺账户”及密码。"),t("br"),n._v("\n        2、及时更新“艺品芳华微商城店铺账户”的管理员权限，并且店铺下其他管理员的操作，视为您授权其进行管理。"),t("br"),n._v("\n        关服务。但是，在艺品芳华对您的请求采取行动所需的合理期限内，艺品芳华对已执行的指令及(或)所导致的您的损失不承担任何责任。"),t("br"),n._v("\n        4、因黑客行为或您的保管疏忽导致账号非法使用，艺品芳华概不承担任何责任。"),t("br"),n._v(" "),t("br"),n._v("\n                 "),t("strong",[n._v("艺品芳华微商城代理销售和结算使用规则")]),t("strong"),t("br"),n._v("\n                  为有效保障您使用本服务的合法权益，您理解并同意接受以下规则："),t("br"),n._v("\n                 一、一旦您使用本服务，您即允许艺品芳华代理您及（或）您的公司在您及（或）您指定人符合指定条件或状态时，结算款项给您指定人"),t("br"),n._v("\n        二、艺品芳华可通过您在艺品芳华微商城网站上依照本服务预设流程申请结算，是不可撤回或撤销的，且成为艺品芳华代理您结算款项的唯一指令。但您向艺品芳华发出结算指令前，您应已向艺品芳华提供真实的身份信息和资质证明，且经艺品芳华核查无误完成的有效店铺，否则您向艺品芳华申请提现的结算指令将会被系统自动拦截或无法提现。"),t("br"),n._v("\n        三、您同意在您与第三方发生交易纠纷时，艺品芳华无需征得您的同意，有权自行判断并决定将争议货款的全部或部分结算给交易一方或双方。"),t("br"),n._v("\n        四、您在使用本服务过程中，本协议内容、网页上出现的关于结算操作的提示或艺品芳华发送到其微信内容是您使用本服务的相关规则，您使用本服务即表示您同意接受本服务的相关规则。艺品芳华无须征得您的同意，有权单方修改本服务的相关规则，修改后的服务规则应以您使用服务时的页面提示为准。"),t("br"),n._v("\n        五、艺品芳华会以微信客户通知方式通知您交易进展情况以及提示您进行下一步的操作，但艺品芳华不保证您能够收到或者及时收到该信息，且不对此承担任何后果。因此，在交易过程中您应当及时登录到艺品芳华网站查看和进行交易操作。因您没有及时查看和对交易状态进行修改或确认或未能提交相关申请而导致的任何纠纷或损失，艺品芳华不负任何责任。"),t("br"),n._v("\n        六、您如果需要向交易对方交付货物，应根据交易状态页面显示的买方地址，委托有合法经营资格的承运人将货物直接运送至对方或其指定收货人，并要求对方或其委托的第三方（该第三方应当提供对方的授权文件并出示对方及第三方的身份证原件）在收货凭证上签字确认，因货物延迟送达或在送达过程中的丢失、损坏，艺品芳华不承担任何责任，应由您与交易对方自行处理。"),t("br"),n._v("\n        七、除代付订单，送礼订单、返现订单和自有支付订单外，其他类型的所有售前订单，当您或者您的交易对方（以下简称：买家）在规定时间内没有操作订单，系统都会根据设定流程，自动处理订单。以上简称为倒计时功能。"),t("br"),n._v("\n        1、倒计时功能的适用情形主要由以下几种："),t("br"),n._v("\n        A、仅退款"),t("br"),n._v("\n        1）买家申请退款后，等待您处理，时限为15天，超时系统将自动同意申请，并从您店铺账户自动退款给买家；"),t("br"),n._v("\n        2）您拒绝退款申请后，等待买家处理（买家可修改退款申请或者申请艺品芳华客服介入），时限为7天，超时系统将自动关闭退款申请；"),t("br"),n._v("\n        B、退货退款"),t("br"),n._v("\n        1）买家申请退货退款后，等待您处理，时限为15天，超时系统将自动同意申请，并发送退货地址给买家（退货地址您事先必须填写）；"),t("br"),n._v("\n        2）您拒绝退款申请后，等待买家处理（买家可修改退款退货申请或者申请艺品芳华客服介入），时限为15天，超时系统将自动关闭退款申请；"),t("br"),n._v("\n        3）您同意买家的退款退货申请，且买家已经退货，等待商家收货的，时限为自买家填写物流单后起7天，超时系统将自动退款给买家；"),t("br"),n._v("\n        4）您已经收到买家的退货，拒绝买家退款申请，等待买家处理（买家可修改退款申请或者申请艺品芳华客服介入），时限为15天，超时系统将自动关闭退款申请；"),t("br"),n._v("\n        5）您同意买家退款申请后，系统会提醒买家退回货物，此时限为7天，买家超时未填写物流单号，系统将自动关闭退款申请。"),t("br"),n._v("\n        2、在上述交易维权流程的敏感节点，艺品芳华会以艺品芳华微信服务号、艺品芳华微微信服务号客服通知的形式向您或者买家推送通知，提醒您或者买家及时处理维权订单；若因您或者买家未及时处理导致产生资损的，可以联系艺品芳华客服协调处理，但艺品芳华不对您或者买家的超时资损进行赔付。"),t("br"),n._v("\n        八、艺品芳华对您所交易的标的物不提供任何形式的鉴定、证明的服务。如果您与交易对方发生交易纠纷，艺品芳华有权根据本协议及艺品芳华微商城网站、艺品芳华商家微信公告上载明的各项规则进行处理。您为解决纠纷而支出的通讯费、文件复印费、鉴定费等均由您自行承担。因市场因素致使商品涨价跌价而使任何一方得益或者受到损失而产生的纠纷，艺品芳华不予处理。"),t("br"),n._v("\n        九、若您未完成注册过程成为艺品芳华微商城店铺，他人无法通过本服务购买您艺品芳华微商城店铺的商品，直到您完成该账户的注册。"),t("br"),n._v("\n        十、艺品芳华会将与您艺品芳华微商城店铺账户相关的资金，独立于艺品芳华营运资金之外，且不会将该资金用于非您指示的用途。"),t("br"),n._v("\n        十一、艺品芳华并非银行或其它金融机构，本服务也非金融业务，本协议项下的资金移转均通过银行来实现，你理解并同意您的资金于流转途中的合理时间。"),t("br"),n._v("\n        十二、您完全承担您使用本服务期间由艺品芳华保管或代理销售或结算的款项的货币贬值风险及可能的孳息损失。当您通过本服务进行各项交易或接受交易款项时，若您或交易对方未遵从本服务条款或网站说明、交易页面中之操作步骤，则艺品芳华有权拒绝为您与交易对方提供相关服务，且艺品芳华不承担损害赔偿责任。若发生上述状况，而款项已先行划付至您的艺品芳华微商城商户账户名下，您同意"),t("br"),n._v("\n        十三、您同意，基于运行和交易安全的需要，艺品芳华可以暂时停止提供或者限制本服务部分功能，或提供新的功能，在任何功能减少、增加或者变化时，只要您仍然使用本服务，表示您仍然同意本协议或者变更后的协议。"),t("br"),n._v("\n        十四、您不得将本服务用于非艺品芳华许可的其他用途。"),t("br"),n._v("\n        十五、交易风险"),t("br"),n._v("\n        艺品芳华有权直接自相关账户余额中扣回款项，并且您不享有要求艺品芳华支付此笔款项之权利。此款项若已汇入您的银行账户，您同意艺品芳华有向您事后索回之权利，因您的原因导致艺品芳华事后追索的，您应当承担艺品芳华合理的追索费用。"),t("br"),n._v("\n        十六、软件服务费、服务期及续费、续期"),t("br"),n._v("\n        1、您使用本服务前，应事先注册、开通艺品芳华微商城店铺，并向艺品芳华支付软件服务费，且不同的服务期软件服务费不同。普通用户年费0元，普通推广年费999元，专业推广年费2999元，高级推广年费3999元。且您只能通过店铺余额或直接向艺品芳华支付软件服务费的方式，订购本服务。"),t("br"),n._v("\n        2、您同意本协议并按本协议约定支付软件服务费以后，本服务即订购成功。一旦订购成功，您即可开始使用本服务。"),t("br"),n._v("\n        3、您同意一旦您订购本服务支付软件服务费，艺品芳华不因本协议的中止、终止或您单方面而退出将已支付的软件服务费退还给您。"),t("br"),n._v("\n        4、本协议服务期根据您支付软件服务费时所选择的服务期决定，但不论您订购一年、二年还是三年，只要是首次订购且不论是否在体验期内订购本服务，服务期均从您订购成功之日起开始计算。"),t("br"),n._v("\n        5、您应于服务期到期前续费，如您到期未续费的，您将于服务期到期日停止使用本服务。如您在到期前或到期当天续费的，新的服务期从原服务期到期的次日开始计算；若您在原服务期到期之后续费的，新的服务期于您续费完成之日起开始计算。"),t("br"),n._v("\n        6、如您需使用本服务中部分收费的功能、工具等，您须额外支付相应的费用才能使用。"),t("br"),n._v("\n        7、除上述软件服务费外，如交易对方使用第三方支付网关支付货款的，您同意艺品芳华有权在交易对方支付货款后按照第三方支付网关或银行的规定直接扣除相关手续费用，但艺品芳华有权随时对上述交易另行额外加收手续费（如艺品芳华须加收手续费的，应提前在艺品芳华商家社区以公告形式通知）。如第三方支付网关或银行提高或降低手续费用的，艺品芳华亦有权提高或降低手续费用，但艺品芳华应提前在艺品芳华商家社区以公告形式进行通知。"),t("br"),n._v("\n        8、艺品芳华有权对艺品芳华微商城的收费进行调整，具体的收费方案以您使用本服务时艺品芳华微商城网站上所列之收费公告或您与艺品芳华达成的其他书面协议为准；若在收费调整后您继续使用本服务的，表示您已完全知晓并接受艺品芳华调整后的收费方案，也将遵循调整后的收费方案支付费用；若您不同意调整后的收费方案，您应停止使用本服务。  "),t("br"),n._v("\n                 "),t("strong",[n._v("艺品芳华微商城代理销售和结算服务使用限制")]),t("strong"),t("br"),n._v("\n                  一、您在使用本服务时应遵守中华人民共和国相关法律法规，以及您所在国家或地区之法令及相关国际惯例，不将本服务用于任何非法目的（包括用于禁止或限制交易物品的交易），也不以任何非法方式使用本服务。"),t("br"),n._v("\n        二、您同意将不会利用本服务进行任何违法或不正当的活动，如有此类行为艺品芳华有权直接作删除内容、商品下架等处理，并且艺品芳华对此类行为不承担任何责任，由您自行承担由此引起的一切责任。若有导致艺品芳华或艺品芳华雇员受损的，您亦应对此承担赔偿责任。此类行为包括但不限于下列行为∶"),t("br"),n._v("\n        1、侵害他人名誉权、隐私权、商业秘密、商标权、著作权、专利权等合法权益；"),t("br"),n._v("\n        2、违反依法定或约定之保密义务；"),t("br"),n._v("\n        3、冒用他人名义使用本服务；"),t("br"),n._v("\n        4、从事不法交易行为，如洗钱、贩卖枪支、毒品、禁药、盗版软件、黄色淫秽物品、及其他艺品芳华认为不得使用本服务进行交易的物品等。"),t("br"),n._v("\n        5、提供赌博资讯或以任何方式引诱他人参与赌博。"),t("br"),n._v("\n        6、非法使用他人银行账户（包括信用卡账户）或无效银行账号（包括信用卡账户）交易。"),t("br"),n._v("\n        7、违反《银行卡业务管理办法》使用银行卡，或利用信用卡套取现金（以下简称套现）。"),t("br"),n._v("\n        8、进行与您或交易对方宣称的交易内容不符的交易，或不真实的交易。"),t("br"),n._v("\n        9、从事任何可能含有电脑病毒或是可能侵害本服务系统、资料之行为。"),t("br"),n._v("\n        10、含有中国法律、法规、规章、条例以及任何具有法律效力之规范所限制或禁止的其它内容的；"),t("br"),n._v("\n        11、其他艺品芳华有正当理由认为不适当之行为。"),t("br"),n._v(" "),t("br"),n._v(" "),t("strong",[n._v("违约责任")]),t("strong"),t("br"),n._v("\n        一、因您的过错导致的任何损失由您自行承担，该过错包括但不限于：不按照交易提示操作，未及时进行交易操作，遗忘或泄漏密码，密码被他人破解，您使用的计算机被他人侵入。"),t("br"),n._v("\n        二、因您未及时更新资料，导致本服务不能提供或提供时发生任何错误，您不得将此作为取消交易、拒绝付款的理由，您须自行承担因此产生的一切后果，艺品芳华不承担任何责任。"),t("br"),n._v("\n        三、如艺品芳华发现您存在欺诈、套现等违反法律、法规规定、本协议或相关服务条款或存在艺品芳华认为不适当的行为，艺品芳华有权根据情节严重程度，对您处以警告、限制或禁止使用部分或全部功能、封禁您店铺账户等处罚；由此导致或产生第三方主张的任何索赔、要求或损失，须由您自行承担一切损失，与艺品芳华无关；如艺品芳华因此也遭受损失的，您也应当一并赔偿。"),t("br"),n._v("\n        四、即使本服务终止，您仍应对您使用本服务期间的一切行为承担可能的违约或损害赔偿责任。"),t("br"),n._v(" "),t("br"),n._v(" "),t("strong",[n._v("免责声明")]),t("strong"),t("br"),n._v("\n        一、您确保其所输入的您的资料无误，如果因资料错误造成艺品芳华于异常交易发生时，无法及时通知您相关交易后续处理方式的，艺品芳华不承担任何损害赔偿责任。"),t("br"),n._v("\n        二、您理解并同意，艺品芳华不对因下述任一情况导致的任何损害赔偿承担责任，包括但不限于利润、商誉、使用、数据等方面的损失或其他无形损失的损害赔偿："),t("br"),n._v("\n        1、艺品芳华有权基于单方判断，包含但不限于艺品芳华认为您已经违反本协议的明文规定及精神，暂停、中断或终止向您提供本服务或其任何部分，并移除您的资料。"),t("br"),n._v("\n        2、艺品芳华在发现交易异常或您有违反法律规定或本协议约定的行为时，有权不经通知先行暂停或终止该账户的使用（包括但不限于对该账户名下的款项和在途交易采取取消交易、调账等限制措施）（但艺品芳华应在采取上述措施后以短信或艺品芳华微商城信息推送等形式通知您，如因系统故障或短信通道故障等原因导致您未收到通知的除外），并拒绝您使用本服务之部分或全部功能。"),t("br"),n._v("\n        3、在必要时，艺品芳华无需事先通知即可终止提供本服务，并暂停、关闭或删除该账户及您账号中所有相关资料及档案（但艺品芳华应在采取上述措施后以短信或艺品芳华微商城信息推送等形式通知您，如因系统故障或短信通道故障等原因导致您未收到通知的除外），并将您滞留在该账户的全部合法资金退回到您的银行账户。"),t("br"),n._v("\n        三、如因艺品芳华根据本协议声明与承诺中的第二条对本协议内容进行单方面变更，您不同意变更后的协议内容停止使用本服务，双方终止合作的，艺品芳华不承担任何损害赔偿责任。"),t("br"),n._v("\n        四、系统中断或故障"),t("br"),n._v("\n        系统因下列状况无法正常运作，导致您无法使用各项服务的，艺品芳华不承担损害赔偿责任，该状况包括但不限于： "),t("br"),n._v("\n        1、艺品芳华在艺品芳华微商城网站或艺品芳华商家公告之系统停机维护期间。"),t("br"),n._v("\n        2、电信设备出现故障不能进行数据传输的。"),t("br"),n._v("\n        3、因台风、地震、海啸、洪水、停电、战争、恐怖袭击等不可抗力之因素，造成艺品芳华系统障碍不能执行业务的。"),t("br"),n._v("\n        4、由于黑客攻击、电信部门技术调整或故障、网站升级、银行方面的问题等原因而造成的服务中断或者延迟。"),t("br"),n._v("\n        五、对下列情形，艺品芳华不承担任何责任："),t("br"),n._v("\n        1、并非由于艺品芳华的故意而导致本服务未能提供的；"),t("br"),n._v("\n             2、由于您的故意或过失导致您及/或任何第三方遭受损失的；"),t("br"),n._v("\n            3、因您未及时续费，导致本服务不能提供或提供时发生任何错误，您须自行承担因此产生的一切后果，艺品芳华不承担任何责任。"),t("br"),n._v(" "),t("br"),n._v("\n                  "),t("strong",[n._v("终止服务")])]),n._v(" "),t("ol",[t("li",[n._v("对下列情形如您需要删除自己的艺品芳华微商城账户的，应先向艺品芳华申请删除，经艺品芳华审核同意后方可删除艺品芳华微商城账户。艺品芳华同意删除该账户的，即表明艺品芳华与您之间的协议解除，但您仍应对其使用本服务期间的行为承担违约或损害赔偿责任。 ")]),n._v(" "),t("li",[n._v("如果艺品芳华发现或收到他人举报或投诉您违反本协议约定的，艺品芳华有权不经通知随时对相关内容进行删除、屏蔽，并视行为情节对您处以包括但不限于警告、限制或禁止使用部分或全部功能、封禁直至删除店铺账号的处罚。 ")]),n._v(" "),t("li",[n._v("在下列情况下，艺品芳华可以通过封禁店铺或删除您账户的方式终止服务："),t("br"),n._v("\n            （1）因您违反本服务协议相关规定，被艺品芳华终止提供服务的，后您再一次直接或间接或以他人名义注册为艺品芳华用户的，艺品芳华有权再次单方面终止向您提供服务；"),t("br"),n._v("\n            （2）一旦艺品芳华发现您注册数据中主要内容（身份信息、联系方式等）是虚假的，艺品芳华有权随时终止向您提供服务；"),t("br"),n._v("\n            （3）本服务协议更新时，您明示不愿接受新的服务协议的；"),t("br"),n._v("\n            （4）服务期到期，您未续费的；"),t("br"),n._v("\n            （5）您存在本协议其他条款约定的艺品芳华终止向您提供本服务的情形的；"),t("br"),n._v("\n            （6）其它艺品芳华认为需终止服务的情况。 ")]),n._v(" "),t("li",[n._v("服务中断、终止之前您交易行为的处理因您违反法律法规或者违反本服务协议规定而致使艺品芳华中断、终止对您提供服务的，对于服务中断、终止之前您的交易行为依下列原则处理："),t("br"),n._v("\n            （1）服务中断、终止之前，您已经上传至艺品芳华微商城的物品尚未交易或尚未交易完成的，艺品芳华有权在中断、终止服务的同时删除此项物品的相关信息；"),t("br"),n._v("\n            （2）服务中断、终止之前，您已经与交易对方就具体交易达成一致，艺品芳华可以不删除该项交易，但艺品芳华有权在中断、终止服务的同时将您被中断或终止服务的情况通知您的交易对方。  ")])]),n._v(" "),t("p",[n._v(" ")]),n._v(" "),t("p",[t("strong",[n._v(" 商标、知识产权的")]),t("strong",[n._v("保护")]),t("strong"),t("br"),n._v(" "),t("br"),n._v("\n        一、艺品芳华产品及相关网站上由艺品芳华上传、制作、拥有的所有内容，包括但不限于著作、图片、档案、资讯、资料、网站架构、网站画面的安排、网页设计，均由艺品芳华或其关联公司依法拥有其知识产权，包括但不限于商标权、专利权、著作权、商业秘密等。如您在艺品芳华产品或网站上上传由您自行制作、拥有的内容，包括但不限于图片、资讯、资料、店铺设计等，均由您或您的关联公司依法拥有其知识产权，包括但不限于商标权、专利权、著作权、商业秘密等；但如因您上传的由其自行制作、拥有的内容涉及侵犯艺品芳华或其他任何第三方的合法权益的，您应自行对其侵权行为产生的纠纷进行处理，并对其侵权行为承担法律责任，且就由此给艺品芳华造成的损失（包括但不限于艺品芳华声誉的影响、艺品芳华由此承担的连带责任（如有）等）进行赔偿。"),t("br"),n._v("\n        二、非经艺品芳华或其关联公司书面同意，任何人不得擅自使用、修改、复制、公开传播、改变、散布、发行或公开发表在本网站上程序或内容（仅限于由乙方上传、制作、拥有的所有内容，包括但不限于著作、图片、档案、资讯、资料、网站架构、网站画面的安排、网页设计等）；如您需使用著作权非艺品芳华所有的内容的，您应获得具体内容的著作权所有者的合法授权才能使用，如因您私自使用非自己所有的、且未经他人合法授权的著作、图片、档案、资讯、资料等内容的，由您自行承担责任，包括但不限于您自行对您的侵权行为产生的纠纷进行处理，并对您的侵权行为承担法律责任，且就由此给艺品芳华造成的损失（包括但不限于艺品芳华声誉的影响、艺品芳华由此承担的连带责任（如有）等）进行赔偿。"),t("br"),n._v("\n        三、尊重知识产权是您应尽的义务，如有违反，您应承担损害赔偿责任。"),t("br"),n._v(" "),t("br"),n._v(" "),t("strong",[n._v("其他")]),t("strong"),t("br"),n._v("\n        一、本协议之效力、解释、变更、执行与争议解决均适用中华人民共和国法律，没有相关法律规定的，参照通用国际商业惯例和（或）行业惯例。"),t("br"),n._v("\n        二、如本协议的任何条款被视作无效或无法执行，则上述条款可被分离，其余条款则仍具有法律效力。"),t("br"),n._v("\n        三、艺品芳华于您过失或违约时放弃本协议规定的权利的，不得视为其对您的其他或以后同类之过失或违约行为弃权。 "),t("br"),n._v("\n        四、本协议自您在订购本服务时勾选同意本协议内容点击确认订购且成功订购之日为本协议生效之日，且该点击确认行为与您加盖公章或签字的行为具有相同法律效力。双方签订线上协议或其他在线协议后，您因内部管理等原因需要签订纸质协议进行确认或存档的，您可在成功订购后通过店铺后台设置-服务协议中生成电子协议，自行下载打印后盖章、签字，且通过此方式签署的纸质协议视为双方均同意协议的内容并签署，但不能因此视为双方存在两个协议关系，纸质协议的内容必须与在线签署的协议内容一致，协议的生效与履行依照在线签署的协议约定执行，在线签署的协议内容与纸质协议的约定不一致的，以前者的约定为准。纸质协议自甲方和乙方签字或盖章之日起生效。"),t("br"),n._v("\n        五、本协议由双方约定，双方一同遵守。"),t("br"),n._v("\n        六、本协议最终解释权及修订权归艺品芳华所有。 ")])])}]};var s=t("C7Lr")({name:"agreement"},u,!1,function(n){t("ug52")},"data-v-3734ba44",null).exports;i.default.use(l.a);var m=function(n){Promise.all([t.e(0),t.e(3)]).then(function(){n(t("lFou"))}.bind(null,t)).catch(t.oe)},h=new l.a({routes:[{path:"/",redirect:"/index"},{path:"/productlists",name:"productlists",component:function(n){Promise.all([t.e(0),t.e(34)]).then(t.bind(null,"h1R6")).then(function(e){n(e)})},meta:{title:"藏品列表"},children:[{path:":goods_id(\\d+)",name:"goodsproductdetail",component:m,meta:{title:"商品详情"}}]},{path:"/index",name:"index",component:function(n){Promise.all([t.e(0),t.e(1)]).then(function(){n(t("dAjm"))}.bind(null,t)).catch(t.oe)},meta:{title:"首页"},children:[{path:":goods_id(\\d+)",name:"goodsproductdetaiffl",component:m,meta:{title:"商品详情"}}]},{path:"/user/user",name:"adminLink",component:function(n){Promise.all([t.e(0),t.e(2)]).then(function(){n(t("BWhR"))}.bind(null,t)).catch(t.oe)},meta:{title:"个人中心"}},{path:"/publish/publish",name:"publishlink",component:function(n){Promise.all([t.e(0),t.e(6)]).then(function(){n(t("HYBK"))}.bind(null,t)).catch(t.oe)},meta:{title:"发布"}},{path:"/user/money_bag",name:"money_baglink",component:function(n){Promise.all([t.e(0),t.e(4)]).then(function(){n(t("Cazx"))}.bind(null,t)).catch(t.oe)},meta:{title:"我的钱包"}},{path:"/fixed/fixedtitle",name:"fixedtitle_link",component:function(n){Promise.all([t.e(0),t.e(50)]).then(function(){n(t("Fj13"))}.bind(null,t)).catch(t.oe)},meta:{title:"固定导航"}},{path:"/user/shoping_cart",name:"shoping_cart_link",component:function(n){Promise.all([t.e(0),t.e(41)]).then(function(){n(t("7KM7"))}.bind(null,t)).catch(t.oe)},meta:{title:"购物车"}},{path:"/user/commodity_management",name:"commodity_management_link",component:function(n){Promise.all([t.e(0),t.e(21)]).then(function(){n(t("31EE"))}.bind(null,t)).catch(t.oe)},meta:{title:"商品管理"}},{path:"/user/bulk_hair",name:"bulk_hair_link",component:function(n){Promise.all([t.e(0),t.e(22)]).then(function(){n(t("QRnT"))}.bind(null,t)).catch(t.oe)},meta:{title:"商品管理"}},{path:"/user/my_shop",name:"my_shop_link",component:function(n){Promise.all([t.e(0),t.e(32)]).then(function(){n(t("qqgv"))}.bind(null,t)).catch(t.oe)},meta:{title:"我的店铺"}},{path:"/user/seller_shop/:userid(\\d+)",name:"seller_shop_link",component:function(n){Promise.all([t.e(0),t.e(18)]).then(function(){n(t("lV4r"))}.bind(null,t)).catch(t.oe)},meta:{title:"卖家店铺"}},{path:"/user/shop_upgrade",name:"shop_upgrade_link",component:function(n){Promise.all([t.e(0),t.e(14)]).then(function(){n(t("XdOk"))}.bind(null,t)).catch(t.oe)},meta:{title:"店铺推广"}},{path:"/user/shake",name:"shake_link",component:function(n){t.e(9).then(function(){n(t("L/0w"))}.bind(null,t)).catch(t.oe)},meta:{title:"摇一摇"}},{path:"/user/marketing_center",name:"marketing_center_link",component:function(n){Promise.all([t.e(0),t.e(40)]).then(function(){n(t("d0FY"))}.bind(null,t)).catch(t.oe)},meta:{title:"营销中心"}},{path:"/user/employee_inquiry",name:"employee_inquiry_link",component:function(n){Promise.all([t.e(0),t.e(12)]).then(function(){n(t("iUYF"))}.bind(null,t)).catch(t.oe)},meta:{title:"员工查询"}},{path:"/user/customer_service",name:"customer_service_link",component:function(n){Promise.all([t.e(0),t.e(20)]).then(function(){n(t("0hj8"))}.bind(null,t)).catch(t.oe)},meta:{title:"客服中心"}},{path:"/user/staff_results/staff_index",name:"staff_index_link",component:function(n){Promise.all([t.e(0),t.e(24)]).then(function(){n(t("6A3j"))}.bind(null,t)).catch(t.oe)},meta:{title:"查询结果"}},{path:"/classification/class_index",name:"class_index_link",component:function(n){Promise.all([t.e(0),t.e(19)]).then(function(){n(t("h+VS"))}.bind(null,t)).catch(t.oe)},meta:{title:"分类"}},{path:"/user/set_user",name:"set_user_link",component:function(n){Promise.all([t.e(0),t.e(33)]).then(function(){n(t("r8r6"))}.bind(null,t)).catch(t.oe)},meta:{title:"个人设置"}},{path:"/user/fans/fan_index",name:"fan_index_link",component:function(n){Promise.all([t.e(0),t.e(30)]).then(function(){n(t("zzug"))}.bind(null,t)).catch(t.oe)},meta:{title:"粉丝"}},{path:"/user/fans/follow",name:"follow_link",component:function(n){Promise.all([t.e(0),t.e(37)]).then(function(){n(t("i0mU"))}.bind(null,t)).catch(t.oe)},meta:{title:"收藏"}},{path:"/user/authentication/authentication_index",name:"authentication_index_link",component:function(n){Promise.all([t.e(0),t.e(7)]).then(function(){n(t("yWBa"))}.bind(null,t)).catch(t.oe)},meta:{title:"个人认证"}},{path:"/login",name:"login",component:function(n){t.e(0).then(function(){n(t("KgXo"))}.bind(null,t)).catch(t.oe)},meta:{title:"登录中心"}},{path:"/publish/editpublish",name:"editgoods_link",component:function(n){Promise.all([t.e(0),t.e(5)]).then(function(){n(t("/sev"))}.bind(null,t)).catch(t.oe)},meta:{title:"商品编辑"}},{path:"/user/set_user/phone_number",name:"phone_number_link",component:function(n){t.e(16).then(function(){n(t("5nsP"))}.bind(null,t)).catch(t.oe)},meta:{title:"手机验证"}},{path:"/user/authentication/authenication_end",name:"authenication_end_link",component:function(n){Promise.all([t.e(0),t.e(42)]).then(function(){n(t("LC+y"))}.bind(null,t)).catch(t.oe)},meta:{title:"认证结果"}},{path:"/user/money_bag/recharge",name:"recharge_link",component:function(n){Promise.all([t.e(0),t.e(48)]).then(function(){n(t("7kwR"))}.bind(null,t)).catch(t.oe)},meta:{title:"充值"}},{path:"/user/money_bag/rechargelog",name:"rechargelog_link",component:function(n){Promise.all([t.e(0),t.e(45)]).then(function(){n(t("eqlP"))}.bind(null,t)).catch(t.oe)},meta:{title:"充值记录"}},{path:"/user/money_bag/withdrawals",name:"withdrawals_link",component:function(n){Promise.all([t.e(0),t.e(39)]).then(function(){n(t("GyHq"))}.bind(null,t)).catch(t.oe)},meta:{title:"申请提现"}},{path:"/user/money_bag/withdrawalslog",name:"withdrawalslog_link",component:function(n){Promise.all([t.e(0),t.e(46)]).then(function(){n(t("t6dm"))}.bind(null,t)).catch(t.oe)},meta:{title:"提现记录"}},{path:"/user/money_bag/paypoints",name:"paypoints_link",component:function(n){Promise.all([t.e(0),t.e(51)]).then(function(){n(t("g4xS"))}.bind(null,t)).catch(t.oe)},meta:{title:"积分明细"}},{path:"/user/money_bag/accountlog",name:"accountlog_link",component:function(n){Promise.all([t.e(0),t.e(29)]).then(function(){n(t("ek2k"))}.bind(null,t)).catch(t.oe)},meta:{title:"账户明细"}},{path:"/moneypay/caution",name:"caution_link",component:function(n){t.e(15).then(function(){n(t("dbH3"))}.bind(null,t)).catch(t.oe)},meta:{title:"保证金"}},{path:"/user/myorder/myorder",name:"myorder_link",component:function(n){Promise.all([t.e(0),t.e(28)]).then(function(){n(t("8u7t"))}.bind(null,t)).catch(t.oe)},meta:{title:"我的订单"}},{path:"/user/myorder/orderdetail",name:"orderdetail_link",component:function(n){Promise.all([t.e(0),t.e(35)]).then(function(){n(t("+iCC"))}.bind(null,t)).catch(t.oe)},meta:{title:"订单详情"}},{path:"/user/authentication/people",name:"people_link",component:function(n){Promise.all([t.e(0),t.e(27)]).then(function(){n(t("pnES"))}.bind(null,t)).catch(t.oe)},meta:{title:"个人认证提交"}},{path:"/user/authentication/shop_authentication",name:"shop_authentication_link",component:function(n){Promise.all([t.e(0),t.e(44)]).then(function(){n(t("B1iu"))}.bind(null,t)).catch(t.oe)},meta:{title:"个人认证页"}},{path:"/class_list",name:"class_list_link",component:function(n){Promise.all([t.e(0),t.e(13)]).then(function(){n(t("W1wP"))}.bind(null,t)).catch(t.oe)},meta:{title:"分类列表"},children:[{path:":goods_id(\\d+)",name:"goodsproductdetaiffl4",component:m,meta:{title:"商品详情"}}]},{path:"/user/shopuppay",name:"shopuppay_link",component:function(n){Promise.all([t.e(0),t.e(36)]).then(function(){n(t("5W17"))}.bind(null,t)).catch(t.oe)},meta:{title:"店铺升级支付"}},{path:"/user/authentication/authenication_loding",name:"authenication_loding_link",component:function(n){Promise.all([t.e(0),t.e(26)]).then(function(){n(t("zojP"))}.bind(null,t)).catch(t.oe)},meta:{title:"审核中"}},{path:"/user/agreement",name:"agreement_link",component:s,meta:{title:"服务协议"}},{path:"/user/fans/history",name:"history_link",component:function(n){t.e(25).then(function(){n(t("EURH"))}.bind(null,t)).catch(t.oe)},meta:{title:"历史足迹"}},{path:"/new_lift/new_index",name:"new_index_link",component:function(n){t.e(49).then(function(){n(t("z7wo"))}.bind(null,t)).catch(t.oe)},meta:{title:"新闻首页"}},{path:"/new_lift/new_result",name:"new_result_link",component:function(n){Promise.all([t.e(0),t.e(8)]).then(function(){n(t("5h6Q"))}.bind(null,t)).catch(t.oe)},meta:{title:"详情资讯"}},{path:"/index/today_tuijian",name:"today_tuijian_link",component:function(n){Promise.all([t.e(0),t.e(17)]).then(function(){n(t("vvHc"))}.bind(null,t)).catch(t.oe)},meta:{title:"天天特价"}},{path:"/index/shopping_mall",name:"shopping_mall_link",component:function(n){Promise.all([t.e(0),t.e(11)]).then(function(){n(t("o0C+"))}.bind(null,t)).catch(t.oe)},meta:{title:"商城逛逛"}},{path:"/user/shop_index",name:"shop_index_link",component:function(n){t.e(47).then(function(){n(t("IbQi"))}.bind(null,t)).catch(t.oe)},meta:{title:"个人店铺"}},{path:"/index/daydayprice",name:"daydayprice_link",component:function(n){Promise.all([t.e(0),t.e(23)]).then(function(){n(t("veoy"))}.bind(null,t)).catch(t.oe)},meta:{title:"今日推荐"}},{path:"/index/good_shop",name:"good_shop_link",component:function(n){Promise.all([t.e(0),t.e(31)]).then(function(){n(t("a1l0"))}.bind(null,t)).catch(t.oe)},meta:{title:"优选店铺"}},{path:"/index/zhuanchang",name:"zhuanchang_link",component:function(n){Promise.all([t.e(0),t.e(43)]).then(function(){n(t("xIW1"))}.bind(null,t)).catch(t.oe)},meta:{title:"精选"}},{path:"/index/new_shop",name:"new_shop_link",component:function(n){Promise.all([t.e(0),t.e(10)]).then(function(){n(t("FyBK"))}.bind(null,t)).catch(t.oe)},meta:{title:"新品开抢"}},{path:"/index/seach_index",name:"seach_index_link",component:function(n){t.e(38).then(function(){n(t("63MZ"))}.bind(null,t)).catch(t.oe)},meta:{title:"搜索"}}]}),d=(t("R4Sj"),t("iDdd")),p=t.n(d),f=(t("zdS3"),t("Jtfc"),t("sB+f"),t("DSVU"),t("aozt")),_=t.n(f),g=t("dhIU"),b=t.n(g),v=t("wtEF"),w=t("5Ku6"),k=t("efG5"),y=t.n(k),x=new Object;x.getwxmakeings=function(){var n=location.href.split("#")[0];_.a.get("/makesign?url="+encodeURIComponent(n)).then(function(n){if("200"==n.status)return n}).then(function(n){n?(y.a.config({debug:!1,appId:n.data.appid,timestamp:String(n.data.timestamp),nonceStr:n.data.noncestr,signature:n.data.signature,jsApiList:["chooseImage","uploadImage","previewImage","onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","onMenuShareQZone","showOptionMenu","closeWindow","getNetworkType","editAddress"]}),y.a.ready(function(){y.a.checkJsApi({jsApiList:["getNetworkType","uploadImage","chooseImage","previewImage","editAddress","getLatestAddress"],success:function(n){}}),y.a.error(function(n){})})):alert("服务器响应异常！")}).catch(function(n){})};var P=x,C=t("DMPO"),E=t.n(C),I=t("vT+3"),S=t.n(I),U=(t("Y96J"),t("u7z7"));i.default.config.productionTip=!1,i.default.prototype.$axios=_.a,i.default.prototype.$weipai=w.a,_.a.defaults.baseURL=b.a.default_domain_api,p.a.attach(document.body),t("Drwf"),i.default.use(E.a),i.default.prototype.makeconfig=P.getwxmakeings,i.default.use(S.a),i.default.prototype.settrim=function(n){return n.replace(/(^\s*)|(\s*$)/g,"")},window.storeWithExpiration={set:function(n,e,t){var i,o,a,r;i=n,o=e,a=t=t||0,(r=new Date).setDate(r.getTime()+a),Cookies.set(i,o,{expires:a})},get:function(n){var e,t=(e=n,Cookies.get(e));return t||null}},h.afterEach(function(n,e,t){"class_list_link"==n.name||"index"==n.name||window.scrollTo(0,0)}),h.beforeEach(function(n,e,t){n.meta.title&&(document.title=n.meta.title),"login"==n.name?t():w.a.login(t,i.default.prototype.makeconfig,n)}),h.afterEach(function(n){}),i.default.prototype.$dialog={confirm:U.Confirm,alert:U.Alert,toast:U.Toast,notify:U.Notify,loading:U.Loading};new i.default({store:v.a,router:h,render:function(n){return n(c)}}).$mount("#app")},Y96J:function(n,e){},dhIU:function(n,e,t){const i=t("UKm7"),o=t("rBKV");n.exports=i(o,{NODE_ENV:'"development"'})},hF2f:function(n,e,t){n.exports=t.p+"static/img/loading.4d5a2b1.png"},iIN8:function(n,e){},rBKV:function(n,e,t){"use strict";n.exports={NODE_ENV:'"production"',default_domain_api:"http://api.fanghua.jiuxintangwenhua.com/",default_domain:"http://wap.yipinfanghua.com/",cdntianbao:"http://yipinfanghuaweipai.oss-cn-hangzhou.aliyuncs.com",bucketName:"yipinfanghuaweipai",weixing:{appID:"wx732d013f1dd089a8"}}},"sB+f":function(n,e){var t,i,o,a,r;t=window,i=t.document,o=i.documentElement,a="orientationchange"in t?"orientationchange":"resize",r=function n(){var e=o.getBoundingClientRect().width;return o.style.fontSize=5*Math.max(Math.min(e/750*20,11.2),8.55)+"px",n}(),o.setAttribute("data-dpr",t.navigator.appVersion.match(/iphone/gi)?t.devicePixelRatio:1),/iP(hone|od|ad)/.test(t.navigator.userAgent)&&(i.documentElement.classList.add("ios"),parseInt(t.navigator.appVersion.match(/OS (\d+)_(\d+)_?(\d+)?/)[1],10)>=8&&i.documentElement.classList.add("hairline")),i.addEventListener&&(t.addEventListener(a,r,!1),i.addEventListener("DOMContentLoaded",r,!1))},ug52:function(n,e){},wtEF:function(n,e,t){"use strict";var i=t("xd7I"),o=t("R4Sj"),a=t("aozt"),r=t.n(a);i.default.use(o.a);var c=new o.a.Store({state:{menuItems:[],tempuserinfo:[],histroy:[],staff_mobile:"",mobilecheckobj:{},bargaininglistflag:!0,bargaininglist:[],fansilist:[],card:{}},getters:{},mutations:{setMenuItems:function(n,e){n.menuItems=e},setfansilist:function(n,e){n.fansilist=e},card:function(n,e){n.card=e,console.log(e)},setdata:function(n,e,t){n[t]=e}},actions:{getuserinfo:function(){var n=window.storeWithExpiration.get("token");r.a.get("/users/index",{params:{token:n}}).then(function(n){if("200"==n.status)return n.data}).then(function(n){c.state.menuItems=n.data.user_data,c.state.fansilist=n.data}).catch(function(n){})},getbargaininglist:function(n,e){var t=window.storeWithExpiration.get("token");c.state.bargaininglistflag&&r.a.get("/product/getbondlists",{params:{token:t}}).then(function(n){if("200"==n.status)return n.data}).then(function(n){c.state.bargaininglist=n.data.plists}).catch(function(n){})}}});e.a=c},"yA+7":function(n,e){}},["NHnr"]);
//# sourceMappingURL=app.94ede21922c656e3e17f.js.map