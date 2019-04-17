<template>
  <div id="productuve">
    <div id="contentbox">
        <div class="topBanner fixtop logoutbtn">
            <div class="draft btn" @click="back">退出发布</div><span>上传数量{{hhsssss}}</span>
            <div @click="test($event)" class="next btn">立即发布</div>
        </div>
        <div class="editMain">
            <form  method="post" id="productupload">
                <input type="hidden" name="imgList" value="" />
                <input type="hidden" name="oldimgList" value="" />
                <input type="hidden" name="imgCert" id="imgCert" value="" />
                <input type="hidden" name="id" value="" />

                <div class="desc">
                    <div class="goodsImgList">
                            <div   @click.stop="preview(item,$event)" v-for="(item, index)  in newedimage" style="position:relative;"><img  class="img" :src="item" width="80px"><div @click="deletethiimage(item,index,$event)" class="delete"></div></div>
                            <div  @click.stop="addImg($event)"  class="addImg"></div>
                    </div>
                </div>
                <div class="saleItem">
                    <div class="lihead">联系手机：</div>
                    <input style="border:none;padding:4px 8px;font-size:16px;" type="tel" name="contact_mobile" placeholder="必须填写" :value="mobile" />
                </div>
                <div class="saleItem">
                    <div class="lihead">联系微信：</div>
                    <input style="border:none;padding:4px 8px;font-size:16px;" name="contact_wx" placeholder="非必须填写" />
                </div>
                <!--新添加处罚通告-->
                <div class="hh">
                    <div class="ff"><router-link to="/usersellindex/punish" style="color:#fe0100">关于滥留联系方式的处罚通告！</router-link></div>
                </div>

                <div class="desc">
                    <input placeholder="商品区名称:必填,最多不超过16字" type="text" name="goods_name" maxlength="16" value="" />
                </div>
                <div class="desc">
                    <textarea placeholder="商品区描述:必填,最多不超过256字" name="goods_content"    @input="descInput"   maxlength="256" v-model="desc"></textarea>
                </div>

                <div class="setBox">
                    <div class="saleItem endTime">
                        <div class="lihead">截止时间</div>
                        <div id="date5" class="endTimeInput">
                            <span>请选择时间</span>
                            <input type="hidden" name="endTime"  readonly="readonly"  value="" />
                        </div>
                    </div>
                    <div class="saleItem category">
                        <div class="lihead">分类</div>
                        <div class="categoryInput">
                            <span>请选择商品区分类</span>
                        </div>
                        <input type="hidden" id="category" name="cat_id" value="" />
                    </div>
                    <div class="saleItem">
                        <div class="lihead" desc="起拍价(元)">起拍价</div>
                        <div class="numInput" id="bidMoney">
                            <span>0</span>
                        </div>
                        <input type="hidden" name="start_price" value="0" />
                    </div>
                    <div class="saleItem">
                        <div class="lihead" desc="加价幅度(元)">加价幅度</div>
                        <div class="numInput" id="increase">
                            <span>100</span>
                        </div>
                        <input type="hidden" name="every_add_price" value="100" />
                    </div>
                </div>
                <div class="optionalTitle">可选设置</div>
                <div class="saleItem">
                    <div class="lihead">7天包退</div>
                    <div class="switchBtn enableReturn on"><mt-switch v-model="enableReturndefault"> </mt-switch></div>
                    <input type="hidden" id="enableReturn" name="enableReturn"  :value="enableReturndefault"  v-text="enableReturndefault"/>
                </div>
                <div class="saleItem">
                    <div class="lihead">包邮</div>
                    <div class="switchBtn expressFee on"> <mt-switch v-model="is_free_shipping_value"></mt-switch></div>
                    <input type="hidden" name="is_free_shipping"  :value="is_free_shipping_value"  v-text="is_free_shipping_value"/>
                </div>

                <div class="saleItem reserveprice">
                    <div class="lihead" desc="保留价(元)">保留价</div>
                    <div class="numInput" id="reserveprice">
                        <span>0</span>
                    </div>
                    <input type="hidden" name="reserveprice" value="0" />
                </div>
                <div class="tip">保留价可不设定，默认为数字0 如设定则最高竞拍价格大于保留价则竞拍成功，否则流拍。</div>
                <input type="hidden" name="bidBzj" value="0" />
                <button @click.stop="test($event)" id="immediatelyproduct">立即发布</button>
            </form>
        </div>
    </div>
        <numberview></numberview>
        <category  v-on:increment="parentgetvalue"></category>
  </div>
</template>

<script type="text/babel">
let numberview  = require('../components/usercenter/seller/number.vue');
let category  = require('../components/usercenter/seller/categorynoposttoweixin.vue');
//import Vue from 'vue';
// 按需引入部分组件
var dateFormat = require('dateformat');
import {MessageBox ,Switch,Indicator,Toast } from 'mint-ui';
Vue.component(Switch.name, Switch);
import { isPhoneNo } from '../assets/js/common_function.js';
var _ = require('lodash/core');
let datepicker = require('../assets/js/plugin/lidatepicker.js');
let config = require('../../config')
import { mapState } from 'vuex';
  module.exports = {
  data: function() {
                  let dataobj =new Date()
                  let year = dataobj.getFullYear()
                  let month = dataobj.getMonth()-1
                  let day =dataobj.getDate()
                return {
                    certflag: 0,
                    is_free_shipping_value:true,
                    enableReturndefault:false,
                    maskIsHide:false,
                    focusEditTxt:null,
                    startDatec: new Date(),
                    endDatec:  new Date(year,month,day),
                    hhsssss:'',
                    endTime:0,
                    memberProductCounthased:0,
                    memberProductCount:0,
                    mobile_validated:0,
                    mobile:''
            }
  		},
    components:{
            "Switch.name":Switch,
            "numberview":numberview,
            "category":category
    },
    mounted:function(){
                 window.focusDiv = null;
                  var _self =this;
                          _self.$store.dispatch('uploadimages_server',[] );
                          _self.$store.dispatch('uploadimages',[] );

                 					//添加事件
                 					$(document).find('#productuve').on('fixednum_view:focus', function(e, editObj) {
                 						_self.focusEditTxt = $(editObj);
                 					});
                 					$(document).find('#productuve').on('fixednum_view:hide', function(e) {
                 						_self.hideFixednum_view();
                 					});
                 					$(document).find('#productuve').on('fixednum_view:show',function(e, txt, fixedPrice, increase, bidbzj) {
                 						_self.showFixednum_view(txt, fixedPrice, increase, bidbzj);
                 					});

                 					            $(".fixednumMain .close, .tipBanner .finish, .fixednumMask").on("click",
                                                    function(e) {
                                                        e.preventDefault();
                                                        if ($(this).hasClass("finish")) {
                                                            if (typeof fixednum_extend != 'undefined') {
                                                                if (typeof fixednum_extend.finish == 'function') {
                                                                    if (!fixednum_extend.finish()) {
                                                                        return;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        _self.hideFixednum_view();
                                                    });

                                                            $(".numInput").parent().on("click", function(e) {
                                                                window.focusDiv = $(this);
                                                                e.preventDefault(), e.stopPropagation();
                                                                 if (_self.isontainsclass(e.target, 'reserveprice')) {
                                                                    if(_self.memberProductCount == 3){
                                                                                                         MessageBox.alert("只有会员可设保留价").then(action => {
                                                                                                         //设置保留价为0
                                                                                                         focusDiv.find("input[name='reserveprice']").val(0);
                                                                                                              return false;
                                                                                                         });
                                                                                                         return false;
                                                                                             }
                                                                    }
                                                                _self.numInput();
                                                    });
$(document).find('#productuve').on('click',function(e) {
        if (!_self.isontainsclass(e.target, 'saleItem')
            && !_self.isontainsclass(e.target, 'numkey')) {
            $(".numInput span").removeClass("hover");
        }
        if (!_self.isontainsclass(e.target, 'numkey')) {
            $(".numInput span").each(function(i, n) {
                var text = $.trim($(n).text());
                if (text == '') {
                    $(n).html('0');
                } else if (text == '#') {
                    $(n).html('');
                }
            });
             _self.hideFixednum_view();
        }
        if (_self.isontainsclass(e.target, 'endTimeListclose')) {
             _self.endTimeList_view();
        }
        if (!_self.isontainsclass(e.target, 'bidBzjInput')) {
             $(document).find('#productuve').trigger("bidBzjList_view:hide");
        }
        if (_self.isontainsclass(e.target, 'logoutbtn')) {
            e.stopPropagation();
            e.preventDefault();
            _self.back();
        }


});
                                                                            	$(".fixednumMain .numkey ul li.num").on("touchend click", function(e) {
                                                                            		e.preventDefault();
                                                                            		  e.stopPropagation();
                                                                            		var clickNum = $(this).find("div").html();
                                                                            		if (clickNum == null || 0==clickNum) {
                                                                            			clickNum = $(this).html();
                                                                            		}
                                                                            		var content = _self.focusEditTxt.find('span').html() + clickNum;
                                                                            		if(!_self.focusEditTxt.find('span').hasClass("redcolor")){
                                                                            			_self.focusEditTxt.find('span').addClass("redcolor");
                                                                            		}
                                                                            		if (typeof fixednum_extend != 'undefined') {
                                                                            			if (typeof fixednum_extend.tip == 'function') {
                                                                            				$(".fixednumMain .tips").html(fixednum_extend.tip(content));
                                                                            			}
                                                                            			if (typeof fixednum_extend.format == 'function') {
                                                                            				content = fixednum_extend.format(content);
                                                                            			}
                                                                            		}
                                                                            		_self.focusEditTxt.find('span').html(content);
                                                                                         content=Number(content);
                                                                            			_self.focusEditTxt.parent('.saleItem').find('input').val(content);

                                                                                        var tipBannertitle = $(".tipBanner .title");
                                                                                        var tipBannertitle_content =   focusDiv.find(".lihead").attr("desc");
                                                                                          tipBannertitle.html(tipBannertitle_content+" "+content);
                                                                            		if (clickNum.length == 1 && clickNum != ".") {
                                                                            			$(this).css({
                                                                            				"background-color" : "#FFF"
                                                                            			});
                                                                            		} else {
                                                                            			$(this).css({
                                                                            				"background-color" : "#D1D5DA"
                                                                            			});
                                                                            		}
                                                                            	});
                                                                            	$(".fixednumMain .numkey ul li.delete").on("touchend click", function(e) {
                                                                                		e.preventDefault();
                                                                                		  e.stopPropagation();
                                                                                		var content = _self.focusEditTxt.find('span').html();
                                                                                		var content = content.substr(0, content.length - 1);
                                                                                		if (typeof fixednum_extend != 'undefined') {
                                                                                			if (typeof fixednum_extend.tip == 'function') {
                                                                                				$(".fixednumMain .tips").html(fixednum_extend.tip(content));
                                                                                			}
                                                                                			if (typeof fixednum_extend.format == 'function') {
                                                                                				content = fixednum_extend.format(content);
                                                                                			}
                                                                                                }
                                                                                                    _self.focusEditTxt.find('span').html(content);
                                                                                                    var tipBannertitle = $(".tipBanner .title");
                                                                                                    var tipBannertitle_content =   focusDiv.find(".lihead").attr("desc");
                                                                                                    tipBannertitle.html(tipBannertitle_content+" "+content);
                                                                                		$(this).css({
                                                                                			"background-color" : "#D1D5DA"
                                                                                		});
                                                                                	});

                                                                                	$('.saleItem.category').on('click', function(e) {
                                                                                    			e.preventDefault();
                                                                                    			  e.stopPropagation();
                                                                                    			$(document.body).trigger('fixednum_view:hide');
                                                                                    			$(".numInput span").removeClass("hover");
                                                                                    			_self.wptCategory();
                                                                                    		});
    },
    methods:{
        descInput() {
            var txtVal = this.desc.length;  //desc 是设置v-model的值
            this.remnant = txtVal;
        },
    init(obj){
    //结束时间
     var ddd=obj
     var  _self =this;
        if(this.isDateString(ddd)){
                     let strtime = ddd;
                     var dateddd = new Date(strtime.replace(/-/g, '/'));
                      $('.endTime').datePicker({
                                          beginyear: 2017,
                                          endyear:2017,
                                          beginminute:0,
                                          endminute:0,
                                          curdate:true,
                                          theme: 'datetime',
                                          startDate:new Date(),
                                          endDate: dateddd,
                                          callBack: function() {
                                             var  ddd=$('.endTime').val()
                                            $("input[name='endTime']").val(_self.convert(ddd));
                                            $(".endTimeInput span").html(_self.getweekday(ddd)+'：'+ddd);
                                          }
                                      });
        }
    },
    getweekday(str){
     if(this.isDateString(str)){
                      let strtime = str;
                              var week = new Date(strtime.replace(/-/g, '/')).getDay();
                               var a = new Array("日", "一", "二", "三", "四", "五", "六");
                                  var str = "星期"+ a[week];
                                  return str;
      }else{
         return '';
      }
    },
    convert:function(str){
                if(this.isDateString(str)){
                  let strtime = str;
                          var date = new Date(strtime.replace(/-/g, '/'));
                              // 有三种方式获取，在后面会讲到三种方式的区别
                           let   time1 = date.getTime();
                           let   time2 = date.valueOf();
                           let   time3 = Date.parse(date)/1000;
                          return  time3
                }else{
                 alert('日期格式不对!')
                }
            return false;
    },
      isDateString(str) {
        return /\d{4}(\-|\/|.)\d{1,2}\1\d{1,2}/.test(str);
      },
      getYear(value) {
        return this.isDateString(value) ? value.split(' ')[0].split(/-|\/|\./)[0] : value.getFullYear();
      },
      getMonth(value) {
        return this.isDateString(value) ? value.split(' ')[0].split(/-|\/|\./)[1] : value.getMonth() + 1;
      },
      getDate(value) {
        return this.isDateString(value) ? value.split(' ')[0].split(/-|\/|\./)[2] : value.getDate();
      },
      getHour(value) {
        if (this.isDateString(value)) {
          const str = value.split(' ')[1] || '00:00:00';
          return str.split(':')[0];
        }
        return value.getHours();
      },
      getMinute(value) {
        if (this.isDateString(value)) {
          const str = value.split(' ')[1] || '00:00:00';
          return str.split(':')[1];
        }
        return value.getMinutes();
      },
     parentgetvalue:function(json){
        this.hhsssss =json.userinfo.memberProductCounthased+'/'+json.userinfo.memberProductCount;
        this.memberProductCounthased =json.userinfo.memberProductCounthased
        this.memberProductCount =json.userinfo.memberProductCount;
        this.mobile =json.userinfo.mobile;
        this.is_authentication =json.userinfo.is_authentication;
        var now = new Date();
        this.value4 = dateFormat(now, "isoDate")+' '+dateFormat(now, "isoTime")
        //astime 当前时间
        this.endDatec = new Date(json.userinfo.endDatec);
        this.mobile_validated =json.userinfo.mobile_validated;
        this.init(json.userinfo.endDatec);
        // Basic usage
    },
     handleChange(value) {

      var now = new Date(value.toString());
        var dddd = dateFormat(now, "isoDate")+' '+dateFormat(now, "isoTime")
  $(".endTimeInput span").html(dddd);

        var timestamp2 = Date.parse(new Date(value.toString()));
         this.endTime= timestamp2
         /*   Toast({
              message: '已选择 ' + value.toString(),
              position: 'bottom'
            });
            */
          },
     open(picker){
            this.$refs[picker].open();
    },
    deletethiimage(item,index,e){

            e.stopPropagation();
            e.preventDefault();

            var needimage =this.$store.state.localIds;
            needimage.splice(index,1)
            this.$store.dispatch('uploadimages',needimage );
    },
            preview(c,e){
                                var needimage =this.$store.state.localIds;
                                var newedimage=new Array();
                                var that =this;
                                var currentimg =c;
                                for(var a in needimage){
                                     newedimage.push(needimage[a]);
                                }
                               window.wx &&  wx.previewImage({
                                                   current: currentimg, // 当前显示图片的http链接
                                                   urls:newedimage // 需要预览的图片http链接列表
                                               });
            },
            addImg(e){
                var _that =this;
                var dd =new FormData();
                dd.append('token',storeWithExpiration.get('token'))
                axios.post('/up/mobileisorcheck',dd,{timeout:200000}).then(function(response) {
                    if(response.status=200){
                        return  response.data;
                    }
                }).then(function(response){
                        return response.data;
                }).then(function(response){
                    _that.mobile_validated = response.mobile_validated ||0;
                    if(0==_that.mobile_validated){
                        MessageBox.alert('请验证手机后再上传商品区!', '提示').then(action=>{  _that.$router.push({ path: 'usersellindex/myqverfitymobile' })});
                        return false;
                        //跳转到验证手机页面
                    }
                    wx.ready(function(){
                        //绑定点击事件
                        window.wx.chooseImage({
                            count: 9, // 默认9
                            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                            success: function (res) {
                                var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                                for(var i = 0;i < localIds.length; i++) {
                                    wx.getLocalImgData({
                                        localId:  localIds[i], // 图片的localID
                                        success: function (res) {
                                            localIds[i] = res.localData; // localData是图片的base64数据，可以用img标签显示
                                        }
                                    });
                                }

                                var totalimages  =_that.$store.state.localIds.concat(localIds);
                                _that.$store.dispatch('uploadimages',totalimages );
                            },
                            fail: function (res) {
                                alert(JSON.stringify(res));
                            }
                        })
                    })
                    wx.error(function (res) {
                       // alert(JSON.stringify(res))
                        console.log(JSON.stringify(res));
                    })
                }) .catch(function (error) {
                    MessageBox.alert('网络问题!', '提示');
                    return false;
                });
                return false;
            },
            uploadphpserver(){
                    var _that =this;
                     //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
                                        var formElement = document.querySelector("#productupload");
                                       // $('#some_form').serialize()
                                        var dd =new FormData(formElement);
                                        if(window.storeWithExpiration.get('token')==''){
                                              MessageBox.alert('登录超时 请重新登陆!', '提示');
                                              _that.$router.push('/')
                                              return false;
                                        }

                                        dd.append('token',storeWithExpiration.get('token'))
                                        dd.append('MEDIA_ID', _that.$store.state.localIds_php)
                                        //提交到后台
                                        Indicator.open({
                                          text: '文件正在上传远程服务器，请稍后...',
                                          spinnerType: 'fading-circle'
                                        });
                                        axios.post('/up/loading',dd,{timeout:200000}).then(function(response) {
                                              if(response.status=200){
                                                   return  response.data;
                                              }
                                        }).then(function(response){
                                              Indicator.close();
                                                var alertstr = "已发布成功";
                                              if(response.code==2000){
                                                  if(0== _that.is_authentication){
                                                      alertstr ="已发布成功 \r\n (实名认证后交易更有保障！)";
                                                  }
                                                  MessageBox.alert(alertstr).then(action=>{
                                                      _that.$store.dispatch('uploadimages',[] );
                                                      _that.$store.dispatch('uploadimages_server',[] );
                                                      //删除本地文件
                                                   //   _that.$router.push({name:'find',params:{userid:response.userid,type:1}})
                                                         window.location.href = window.default_domain_web+'/find/'+response.userid+'/1';
                                                  });
                                              }else{
                                                  MessageBox.alert(response.message).then(action => {
                                                      return false;
                                                  });
                                              }



                                        }) .catch(function (error) {
                                          MessageBox.alert("网络问题，请稍后重试！").then(action => {
                                                                                                    return false;
                                                                                        });
                                                                                          Indicator.close();
                                        });
                                        return false;

            },
            uploadtoalisd(){
                var needimage =this.$store.state.localIds;
                var newedimage=new Array();
                var _that =this;
                for(var a in needimage){
                   newedimage[a] =needimage[a];
                }
                var images = {
                 localId: [],
                 serverId: []
                };
                images.localId =needimage;
                var i = 0, length = images.localId.length;
                if(length==0){
                      MessageBox.alert('作品为空，请重新选择!').then(action => {
                                                   return false;
                                                 });
                    return false;
                }
                 function upload(){
                          wx.uploadImage({
                             localId: images.localId[i],
                             isShowProgressTips: 1,
                            success: function (res) {
                              i++;
                              //alert('已上传：' + i + '/' + length);
                              images.serverId.push(res.serverId);
                              if(_that.$store.state.localIds_php.constructor==Array){
                                   _that.$store.state.localIds_php.push(res.serverId);
                              }else{
                                    alert("请排查错误")
                                    console.log("请排查错误")
                              }
                              if (i < length) {
                                upload();
                              }else{
                                _that.$store.commit('uploadimages',[] );
                                images =null;
                                _that.uploadphpserver()

                              }
                            },
                            fail: function (res) {
                              alert(JSON.stringify(res));
                              return false;
                            }
                          });
                        }
               upload();
             },
              back: function() {
                        var stateObject = {};
                        var title = "天宝微拍";
                        var newUrl = "/";
                        // history.pushState(stateObject,title,default_domain_web+'/');
                             window.location.replace(window.default_domain_web)
                                             window.location.href = window.default_domain_web;
                        return false;
                    },
               test(e){
                        var  _self =this;
                        e.stopPropagation();
                        e.preventDefault();
                        var dec='';

                       switch(this.memberProductCount){
                           case 3:
                               dec="非会员";
                               break;
                           case 30:
                               dec="铜牌会员";
                               break;
                           case 80:
                               dec="银牌会员";
                               break;
                           case 300:
                               dec="金牌会员";
                               break;
                           case 600:
                               dec="钻石会员";
                               break;
                           default:
                               break;
                       }
                              if(this.memberProductCounthased >= this.memberProductCount ){
                               MessageBox.alert(dec+'发布数量有限制！').then(action => {
                                       return false;
                                });
                             return false;
                            }



                        //验证字段的值

                        var formElement = document.querySelector("#productupload");

                        var dd =new FormData(formElement);



                        var contact_mobile = $("input[name='contact_mobile']").val();
                        var contact_wx = $("input[name='contact_wx']").val();

                        var goods_name = $("input[name='goods_name']").val();

                        var goods_content =$("textarea[name='goods_content']").val();


                        var start_price = $("input[name='start_price']").val();


                        var every_add_price = $("input[name='every_add_price']").val();


                        every_add_price=Number(every_add_price)

                        var cat_id = $("input[name='cat_id']").val();

                            cat_id=Number(cat_id);
                        start_price =Number(start_price);

                        var endTime = $("input[name='endTime']").val();
                        endTime = Number(endTime)

                        if(contact_mobile==''||contact_mobile==null){
                             MessageBox('提示', '手机号码不能为空');
                             return false;
                        } else if(!isPhoneNo(contact_mobile)){
                             MessageBox.alert('手机号码不正确').then(action => {
                               return false;
                             });
                        }else if(_.isEmpty(goods_name) ){
                          MessageBox.alert('拍卖的作品名称为空');
                             return false;

                        }else if(_.isEmpty(goods_content) ){
                          MessageBox.alert('拍卖的作品描述为空')
                             return false;

                        }

                         if(endTime<=100 ){
                                                      MessageBox.alert('时间选不正确')
                                                      return false;

                         }else if(isNaN(cat_id) || cat_id<=0 || cat_id==''){
                                                      MessageBox.alert('请选择作品分类')
                                                         return false;

                         }else if(!_.isNumber(start_price) || start_price <=0 ){
                                                      MessageBox.alert('价格不能为0')
                                                      return false;
                         }else if(!_.isNumber(every_add_price) || every_add_price <=0 ){
                                                      MessageBox.alert('加价价格不能为0')
                                                      return false;
                         }


                        MessageBox.confirm('确定发布吗?').then(action => {
                            _self.uploadtoalisd()
                        });


               },
               showFixednum_view:function(txt, fixedPrice, increase, bidbzj) {
                    this.maskIsHide= false;
                    if (txt.length > 16) {
                        $(".tipBanner .title").css("font-size", "12px");
                    } else {
                        $(".tipBanner .title").css("font-size", "16px");
                    }
                    $(".tipBanner .title").html(txt);
                    $(".fixednumMask").show().animate({
                        opacity : 0.382
                    }, 100);
                    $(".fixednumMain").show().animate({
                        bottom : '0px'
                    }, 100, 'ease-in-out');
               },
                hideFixednum_view:function(){
                                   this.maskIsHide= true;
                                   $(".fixednumMain .tips").html('');
                                   $(".fixednumMain").animate({
                                       bottom : '-400px'
                                   }, 150, function() {
                                       $(".fixednumMain, .fixednumMask").hide();
                                   });
                               },
                  numInput:function() {
                    $(".numInput span").removeClass("hover");
                        focusDiv.find("span").addClass("hover");
                        if (parseInt($.trim(focusDiv.find("span").text())) == 0) {
                            focusDiv.find("span").html('');
                        }
                        var desc = focusDiv.find(".lihead").attr("desc");
                        $(document).find('#productuve').trigger("fixednum_view:focus", focusDiv.find(".numInput"));
                        $(document).find('#productuve').trigger("fixednum_view:show", desc);
                   },
                  isontainsclass:function(child, parentClass) {
                   	while (child && !$(child).hasClass(parentClass)) {
                   		child = child.parentNode;
                   	}
                   	return ($(child).hasClass(parentClass));
                   },
                   wptCategory:function() {
                   	this.maskIsHide = false;
                   	var category = parseInt($('#category').val());
                   	category = isNaN(category) ? 0 : category;
                   	$('#category .wptCategory .categoryItem').removeClass("selected");
                   	$('#category .wptCategory .categoryItem[data-id="' + category + '"]').addClass(
                   		"selected");

                   	$("#category .wptCategory").show();
                   	var categoryBox = $("#category .wptCategory .categoryBox");
                   	$("#category .wptCategory .wptMask").animate({
                   		opacity : "0.4"
                   	}, 100);
                   	categoryBox.animate({
                   		bottom : '0'
                   	}, 100);


                   	//pushStateEvent("wptCategory_view:hide");
                   	var _self =this;
                   	$(document).off("wptCategory_view:hide").one("wptCategory_view:hide",
                   		function(e) {
                   			_self.maskIsHide = true;
                   			$("#category .wptCategory .wptMask").animate({
                   				opacity : "0"
                   			}, 100);
                   			var bottom = '-' + categoryBox.height() + 'px';
                   			categoryBox.animate({
                   				bottom : bottom
                   			}, 100, function() {
                   				$("#category .wptCategory").hide();
                   			    $("#category .wptCategory").hide();
                   			});
                   		});
                   	$('#category .wptCategory .categoryTitle .finish,#category  .wptCategory .wptMask').off(
                   		'touchend click').one(
                   		'touchend click',
                   		function(e) {
                   			e.preventDefault();
                   			if ($(e.target).hasClass('finish')) {
                   				var selectedCategory = $('#category .wptCategory .categoryItem')
                   				.filter('.selected');
                   				if (selectedCategory.length == 0) {
                   					$('#category').val(0);
                   					$('.categoryInput span').text('');
                   				} else {
                   					$('#category').val(selectedCategory.data('id'));
                   					$('.categoryInput span').text($.trim(selectedCategory.data("title")));
                   					if(!$('.categoryInput span').hasClass("redcolor")){
                   						$('.categoryInput span').addClass("redcolor");
                   					}
                   				}
                   			}
                   			if ($.os.android) {
                   				//history.back();
                   			} else {

                   			}
                   			$(document.body).trigger("wptCategory_view:hide");
                   		});

                        $('#category .wptCategory .categoryItem').on(
                                    'touchend click',
                                    function(e) {
                                        e.preventDefault();
                                        var _self = $(this);
                                        $(document.body).trigger("fixednum_view:hide");
                                        if (!_self.hasClass('selected')) {
                                            $('#category .wptCategory .categoryItem').filter('.selected')
                                            .removeClass('selected');
                                            _self.addClass('selected');
                                        }
                                    });
                   	},
                   endTimeList_view:function(){
                   	          $(document.body).trigger("endTimeList_view:hide");
                   },
            openAlert() {
                  MessageBox.alert('操作成功!', '提示');
            },
            openConfirm() {
                  MessageBox.confirm('确定执行此操作?', '提示');
              }
    },
    computed: mapState({
              newedimage: function(state) {
                     /*      var needimage =this.$store.state.localIds;
                            var newedimage=new Array();
                            for(var a in needimage){
                                 newedimage[a] =needimage[a];
                            }
                            */
                           return state.localIds;
           }
  })
  }
</script>

<style scoped>
 @import '../assets/css/product.css';
</style>
