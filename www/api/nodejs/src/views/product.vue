<template>
  <div id="productuve">
    <div id="contentbox">
        <div class="topBanner fixtop logoutbtn">
            <div class="draft btn" @click="back">退出发布</div>
            <div @click="test($event)" class="next btn">立即发布</div>
        </div>
        <div class="editMain">
         <validator name="validation1">
            <form  method="post" id="productupload">
                <input type="hidden" name="imgList" value="" />
                <input type="hidden" name="oldimgList" value="" />
                <input type="hidden" name="imgCert" id="imgCert" value="" />
                <input type="hidden" name="id" value="" />

                <div class="desc">
                    <div class="goodsImgList">
                            <div   @click="preview(item,$event)" v-for="(item, index)  in newedimage" style="position:relative;"><img  class="img" :src="item" width="80px"><div @click="deletethiimage(item,index,$event)" class="delete"></div></div>
                            <div id="addImg" class="addImg"></div>
                    </div>
                </div>
                <div class="saleItem">
                    <div class="lihead">联系手机：</div>
                    <input style="border:none;padding:4px 8px;font-size:16px;" type="tel" name="contact_mobile" placeholder="必须填写"  v-validate:contact_mobile="['required']"/>
                </div>
                <div class="saleItem">
                    <div class="lihead">联系微信：</div>
                    <input style="border:none;padding:4px 8px;font-size:16px;" name="contact_wx" placeholder="非必须填写" />
                </div>


                <div class="desc">
                    <input placeholder="商品区名称:必填,最多不超过16字" type="text" name="goods_name" value="" />
                </div>
                <div class="desc">
                    <textarea placeholder="商品区描述:必填,最多不超过256字" name="goods_content"></textarea>
                </div>

                <div class="setBox">
                    <div class="saleItem endTime">
                        <div class="lihead">截止时间</div>
                        <div class="endTimeInput">
                            <span>请选择时间</span>
                            <input type="hidden" name="endTime" value="" />
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

                <div class="saleItem">
                    <div class="lihead" desc="保留价(元)">保留价</div>
                    <div class="numInput" id="reserveprice">
                        <span>0</span>
                    </div>
                    <input type="hidden" name="reserveprice" value="0" />
                </div>
                <div class="tip">保留价可不设定，默认为数字0 如设定则最高竞拍价格大于保留价则竞拍成功，否则流拍。</div>
                <!--
                <div class="saleItem bzj disable">
                    <div class="lihead" desc="">保留价</div>
                    <div class="bidBzjInput" id="bidBzj">
                        <span>0</span>
                        <div class="bidBzjList">
                            <div class="bzjItem">0</div>
                            <div class="bzjItem">2</div>
                            <div class="bzjItem">10</div>
                            <div class="bzjItem">30</div>
                            <div class="bzjItem">50</div>
                            <div class="bzjItem">100</div>
                            <div class="bzjItem">200</div>
                            <div class="bzjItem">500</div>
                            <div class="bzjItem">1000</div>
                        </div>
                    </div>
                </div>-->
                <input type="hidden" name="bidBzj" value="0" />
            </form>
              </validator>
        </div>
    </div>
        <numberview></numberview>
        <category></category>
  </div>
</template>

<script type="text/babel">
var numberview  = require('../components/usercenter/seller/number.vue');
var category  = require('../components/usercenter/seller/category.vue');
//import Vue from 'vue';
// 按需引入部分组件
import 'mint-ui/lib/style.css'
import { Switch } from 'mint-ui';
import { MessageBox ,Indicator} from 'mint-ui';
import { isPhoneNo } from '../assets/js/common_function.js';
var _ = require('lodash/core');
	var config = require('../../config')
import { mapState } from 'vuex';

var VueValidator = require('vue-validator')


  module.exports = {
  data: function() {
                var needimage =this.$store.state.localIds;
                  var newedimage=new Array();
                  for(var a in needimage){
                       newedimage[a] =needimage[a];
                  }
  			return {
  			certflag: 0,
  			is_free_shipping_value:true,
  			enableReturndefault:false,
  			maskIsHide:false,
  			focusEditTxt:null,
  			newedimage:needimage
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
                  this.weixin_product();
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
                 					window.focusDiv = $("#bidMoney");
                                    //显示微信
                                    this.display()
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
                                                    			_self.numInput();
                                                    			   e.preventDefault();
                                                    			   e.stopPropagation();
                                                    });

                                                    		$(".endTimeInput").parent().on(
                                                            			'click',
                                                            			function(e) {
                                                            				e.preventDefault();
                                                            				e.stopPropagation();
                                                            				$(document.body).trigger("fixednum_view:hide");
                                                            				$(document.body).trigger("bidBzjList_view:hide");
                                                            				//pushStateEvent('endTimeList_view:hide');
                                                            				$('#category,#category .wptEndtime').show();
                                                            				$(".wptEndtime .wptMask").animate({
                                                            					opacity : "0.4"
                                                            				}, 100);


                                                                                                      $('.endTimeList dl dd').on('click', function(e) {
                                                                                                                    e.preventDefault();
                                                                                                                    e.stopPropagation();
                                                                                                                    var endTime = $(this).attr('value');
                                                                                                                    $("input[name='endTime']").val(endTime);


                                                                                                                    $(".endTimeInput span").html($(this).attr('tips'));
                                                                                                                    if(!$('.endTimeInput span').hasClass("redcolor")){
                                                                                                                        $('.endTimeInput span').addClass("redcolor");
                                                                                                                    }
                                                                                                                    if ($.os.android) {
                                                                                                                      //  history.back();
                                                                                                                    } else {

                                                                                                                    }
                                                                                                                      _self.endTimeList_view();
                                                                                                                });

                                                            				_self.maskIsHide = false;
                                                            				$(document).off('endTimeList_view:hide').one(
                                                            					'endTimeList_view:hide', function(e) {
                                                            						e.preventDefault();
                                                            						$('#category,#category .wptEndtime').hide();
                                                            						$(".wptEndtime .wptMask").animate({
                                                            							opacity : "0"
                                                            						}, 100);
                                                            						_self.maskIsHide = true;
                                                            					});
                                                            				return false;
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
                                                                            		if (clickNum == null) {
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
            display(){

 var _that =this;
            //绑定点击事件
             document.querySelector('#addImg').onclick = function () {
                    window.wx.chooseImage({
                    count: 9, // 默认9
                    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片

                        var totalimages  =_that.$store.state.localIds.concat(res.localIds);
                        _that.$store.dispatch('uploadimages',totalimages );

                    }
                    });
             }
            },
            uploadphpserver(){
                    var _that =this;
                     //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
                                        var formElement = document.querySelector("#productupload");
                                       // $('#some_form').serialize()
                                        var dd =new FormData(formElement);
                                        dd.append('token',storeWithExpiration.get('token'))
                                        dd.append('MEDIA_ID', _that.$store.state.localIds_php)
                                        //提交到后台
                                        Indicator.open({
                                          text: '文件正在上传远程服务器，请稍后...',
                                          spinnerType: 'fading-circle'
                                        });
                                        axios.post('/up/loading',dd,{timeout:10000})
                                              .then(function (response) {
                                              if(response.status=200){
                                                   return  response.data;
                                              }
                                        }).then(function(response){
                                              Indicator.close();
                                             MessageBox.alert("上传成功").then(action => {
                                                         _that.$router.push({name:'find',params:{type:1}})
                                             });
                                        }) .catch(function (error) {
                                          MessageBox.alert("网络问题，请稍后重试！").then(action => {
                                                                                                    return false;
                                                                                        });
                                                                                          Indicator.close();
                                        });

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
                                _that.$store.dispatch('uploadimages',[] );
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
                         this.$router.push({ path: '/'})
                       // this.$router.go(-1);
                    },
               test(e){
                        var  _self =this;
                        e.stopPropagation();
                        e.preventDefault();
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
                    if((this.certflag == 0) && (focusDiv.find("#reserveprice").length > 0)){
                       // alert("游客暂不支持保留价设定");
                       // return;
                    }
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

                   	$("#category,#category .wptCategory").show();
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
                   			    $("#category,#category .wptCategory").hide();
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
                   isEmptyObject: function( obj ) {
                       var name;
                       for ( name in obj ) {
                           return false;
                       }
                       return true;
                   },
                  weixin_product:function(){
                                 var _that=this;
                                                          // Optionally the request above could also be done as
                                                                  axios.get( '/makesign?url='+encodeURIComponent(location.href.split('#')[0])).then(function (response) {
                                                                               if(!_that.isEmptyObject(response)){
                                                                                    window.wx && wx.config({
                                                                                                  debug: false,
                                                                                                  appId: "wxdabbc66245346dda",
                                                                                                  timestamp: String(response.data.timestamp),
                                                                                                  nonceStr: response.data.noncestr,
                                                                                                  signature: response.data.signature,
                                                                                                  jsApiList: [
                                                                                                      'onMenuShareAppMessage',
                                                                                                      "hideOptionMenu",
                                                                                                      "showOptionMenu",
                                                                                                      "closeWindow",
                                                                                                      "chooseImage",
                                                                                                      "uploadImage",
                                                                                                      "previewImage"
                                                                                                  ]
                                                                                              });
/*
                                                                                              wx.error(function (res) {
                                                                                                alert("微信配置错误")
                                                                                              })
*/


                                                                               }else{
                                                                                    _that.weixin_product();
                                                                               }
                                                                          }).catch(function (error) {
                                                                              console.log(error);
                                                                          });


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
                           return state.localIds;
                        }
  })
  }
</script>

<style scoped>
 @import '../assets/css/product.css'
</style>
