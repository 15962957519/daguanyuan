<template>
  <div  id="contentbox" class="contentbox" data-flag="mypublish">
    		<ul class="filter">
    			<li class="order" flag="0"><span>排序</span></li>
    			<li class="line" flag="0"></li>
    			<li class="sort" flag="0"><span>分类</span></li>
    			<li class="line" flag="0"></li>
    			<li class="status" flag="0"><span>状态</span></li>
    		</ul>

    		<div class="subItem">
    			<div class="order" style="display: none;">
    				<div class="wptMask" style="opacity: 0;"></div>
    				<ul>
    					<li data-flag="">默认排序</li>
    					<li data-flag="dealmoney">出价最高</li>
    					<li data-flag="visitcount">浏览人数最多</li>
    					<li data-flag="likecount">收藏人数最多</li>
    					<li data-flag="deadline">截拍时间</li>
    				</ul>

    			</div>
    			<div class="sort" style="display: none;">
    				<div class="wptMask" style="opacity: 0;"></div>
    				<ul>
    					<li data-flag="1">玉器珠宝</li>
    					<li data-flag="2">书画篆刻</li>
    					<li data-flag="3">茶酒滋补</li>
    					<li data-flag="4">紫砂陶瓷</li>
    					<li data-flag="5">工艺作品</li>
    					<li data-flag="6">文玩杂项</li>
    					<button>确定</button>
    				</ul>
    			</div>
    			<div class="status" style="display: none;">
    				<div class="wptMask" style="opacity: 0;"></div>
    				<ul>
    					<li data-flag="1">正在拍卖</li>
    					<li data-flag="2">拍卖成功</li>
    					<li data-flag="3">流拍</li>
    					<button>确定</button>
    				</ul>
    			</div>
    		</div>
    		<div class="myMain">
                             <div class="listContainer publish">
                                                 <div class="saleBox sale">
                                                     <div class="saleItem" :id="['item'+item.goods_id]" v-for="(item, index)  in lists">
                                                             <div class="info">
                                                                <div class="content">
                                                                    <router-link   :to="{path:'/mymain',query:{goods_id:item.goods_id,type:2}}">
                                                                    <div class="goodsImg" v-lazy:background-image="item.img"></div>
                                                                     </router-link>
                                                                    <div class="title">{{item.goods_name}}</div>
                                                                    <div class="desc">{{item.goods_content}}</div>
                                                                    <div class="publishTime">截拍时间：{{item.endTime}}</div>
                                                                    <div class="publishTime">上传时间：{{item.upload_time}}</div>
                                                                </div>
                                                            </div>

                                                        <div class="tools">
                                                            <div class="bid">￥<span>{{item.start_price}}</span>元</div>
                                                            <div class="statics">{{item.click_count}}</div>
															<template v-if="item.goods_status==2">
																 <router-link class="status disable"  tag="div" :to="{path:'/mymain',query:{goods_id:item.goods_id,type:2}}">{{item.goods_status_desc}}</router-link>
															</template>
															<template v-else>
																<div class="status disable">{{item.goods_status_desc}}</div>
															</template>
                                                            <div   @click="deleteproduct(item.goods_id,$event)" class="handle delete" :data-id="item.goods_id"></div>
															<template v-if="item.level>1">
																<div v-if="item.goods_status_f !=2 && item.goods_status_f!=3"   @click.stop="editproduct(item.level,item.goods_id,$event)"   class="handle republish"   :data-id="item.goods_id">
																</div>

															</template>
															<template v-else>
																<div v-if="item.goods_status_f !=2 && item.goods_status_f!=3"  @click="editproduct(item.level,item.goods_id,$event)"  class="handle republish"   :data-id="item.goods_id" >
																</div>
															</template>
                                                        </div>
                                                    </div>
                    							</div>


                                                <div class="loadNextPage" style="visibility: visible;">
                    								<div class="loading">加载下一页</div>
                                                </div>
                    		</div>
    		</div>
        <div class="my">
             <a class="tab-item selected" href="javascript:;">我的发布</a>
             <router-link class="tab-item" to="/usercenter/mycollectionproductlist">我的收藏</router-link>
        </div>
  </div>
</template>




<script>
var copyright  = require('../copyright.vue');
var config = require('../../../config')
import { MessageBox ,Indicator} from 'mint-ui';
import { mapState} from 'vuex';
  module.exports = {
    data: function() {
    			return {
                    lists:{}
    		}
    		},
    components:{
            'copyright':copyright
    },
    mounted:function(){
            this.$nextTick(function () {
                 document.title = "产品列表"
            })
            this.getprouuct()


    },
    methods:{
        editproduct(level,goods_id,e){
            if(level<=1 ){
                MessageBox.alert('编辑功能只对会员开放').then(action => {
                    return false;
            });
                return false;
            }else{
			    window.location.href = window.default_domain_web+'/fabuindex.html#/editproduct?goods_id='+goods_id;
			}
        },
        getprouuct(){
             var _that =this;
                                                var dd =new FormData();
                                                dd.append('token',storeWithExpiration.get('token'))
                                                //提交到后台
                                                axios.post('/usercenterproduct/index',dd)
                                                .then(function (response) {
                                                    _that.lists =response.data.data.lists;
                                                })
                                                .catch(function (error) {

                                                });
        },
        deleteproduct(id,e){
                                                var dd =new FormData();
                                                var _self=this;
                                                dd.append('token',storeWithExpiration.get('token'))
                                                dd.append('good_ids',id);

                                                 MessageBox.confirm('确定删除该产品吗?').then(action => {
                                                                               //提交到后台
                                                                    axios.post(config.dev.env.default_domain_api+"/usercenterproduct/del",dd)
                                                                    .then(function (response) {
                                                                        _self.getprouuct()
                                                                     //   _that.lists =response.data.data.lists;
                                                                     //   _that.$store.dispatch('my_lists_function_action',response.data.data.lists );
                                                                    })
                                                                    .catch(function (error) {

                                                                    });
                                                                        });

return false;
                         fetch(decodeURIComponent(config.dev.env.default_domain_api)+"/usercenterproduct/del",
                                    {
                                        method:'POST',
                                        mode:'cors',
                                        headers: {
                                        'Content-Type': 'application/json',
                                       //  'Authorization':'Bearer'+storeWithExpiration.get('token')
                                        },
                                         body: JSON.stringify({
                                                                token:storeWithExpiration.get('token')
                                                              })
                                    }).then(function(json) {

                        			}).catch(function(ex) {
                        				console.log(ex);
                        			});
                }
    }
  }
</script>



<style scoped>
 @import '../../assets/css/user_center_seller_iconfont.css';
 </style>
 <style scoped>
.contentbox .filter{
width:100%;
height:40px;
line-height:40px;
color:#333;
text-align:center;
background-color:#fff;
border-bottom:1px solid #EEE;
z-index:9999;
 }
  #contentbox .filter li{
  float:left;
  width:33%;
  overflow:hidden;
  }
 .contentbox .filter >li .line{
  float: left;
      width: 1px;
      height: 24px;
      margin: 8px 0;
      background-color: #EEE;
 }


    #contentbox .filter li span{
    height:40px;
    line-height:40px;
    overflow:hidden;
    }
    .contentbox > .filter >li span:after {
    	margin-top: -2px;
    	content: '';
    	width: 0;
    	font-size: 0;
    	border-width: 4px;
    	border-style: solid;
    	 border-top-color: #999;
    	 border-right-color: transparent;
    	 border-bottom-color: transparent;
    	border-left-color: transparent;
    }

.contentbox .myMain{
width: 100%;
background-color: #FFF;
display: table;
}

.saleBox .saleItem{
width:96%;
padding:0px 2%;
font-size:12px;
overflow:hidden;
margin-bottom:10px;
border-bottom:1px solid #eaeae8;
background-color:#fff;
}

.contentbox .myMain .listContainer{
width:96%;
display:table;
height:auto;
overflow:auto;
margin:0 2%;
}
.contentbox .myMain .listContainer .saleBox{
width:100%;
display:table;
}
.saleBox .saleItem .info .content .goodsImg{
	float: left;
	width: 80px;
	height: 80px;
	margin: 1px;
	margin-right: 12px;
	background-repeat: no-repeat;
	background-position: center;
	background-size: cover;
}
.saleBox .saleItem .info{
width:100%;
padding:4px 0;
display:table;

}
.saleBox .saleItem .info .content{
width:100%;
float:left;
}
.saleBox:after{
display:block;
content:"clear";
height:0;
clear:both;
overflow:hidden;
visibility:hidden;
}


.saleBox .saleItem .tools{
width:100%;
height:32px;
padding:4px 0;

}
.myMain .listContainer .saleBox .saleItem .tools .handle{
	width: 32px;
	height: 32px;
	float: right;
	margin-left: 20px;
	background-repeat: no-repeat;
	background-size: 30px 110px;
	background-image: url(../../assets/img/publishHandle.png?t=2);

}

.myMain .listContainer .saleBox .saleItem  .delete{
margin-right:10px;
background-position:center -40px;
}

.myMain .listContainer .saleBox .saleItem .bid{
float:left;
text-align:left;
color:#fe0100;
height:16px;
line-height:16px;
margin-top:8px;
}

.myMain .listContainer .saleBox .saleItem .statics {
	color:#fe0100;
	font-size: 12px;
	line-height: 16px;
	height: 16px;
	float: left;
	text-align: left;
	margin-top: 8px;
	margin-left: 20px;
	text-indent: 20px;
	background-size: auto 70%;
	background-position: center left;
	background-repeat: no-repeat;
	background-image: url(../../assets/img/review.png);
}

.myMain .listContainer .saleBox .saleItem .status {
	line-height: 16px;
	height: 16px;
	float: left;
	text-align: left;
	margin-top: 8px;
	margin-left: 20px;
	text-indent: 20px;
	font-size: 12px;
	background-size: 16px;
	background-repeat: no-repeat;
	background-image: url(../../assets/img/time.png);
}

.contentbox .myMain .listContainer  .loadNextPage{
height:40px;
width:100%;
display:table;

}
.contentbox  .my{
position:fixed;
bottom:0;
width:100%;
max-width:640px;
background-color:#EAEAEA;
}
.contentbox .supportBanner{
width:100%;
line-height:20px;
padding:10px 0;
text-align:center;
border-top:1px solid #f4f4f4;
margin-top:10px;

}
.contentbox  .my a{
display:block;
color:#fff;
float:left;
width:45%;
margin:2px 2.5% 2px;
height:40px;
line-height:40px;
font-size:14px;
text-align:center;
border-radius:8px;
background-color:#Fe0100;
}


</style>
<style scoped>
 @import '../../assets/css/commonm.css'
</style>