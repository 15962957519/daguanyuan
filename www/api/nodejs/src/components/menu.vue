<style scoped>
	.mainMenu {
		position: fixed;
		width: 100%;
		max-width: 640px;
		height: 50px;
		bottom: 0px;
		background-color: #FCFCFC;
		border-top: 1px solid #EAEAE8;
		font-size: inherit;
	}
.mainMenu:before{
        content: " ";
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        height: 1px;
        border-top: 1px solid #C0BFC4;
        color: #C0BFC4;
        -webkit-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scaleY(0.5);
        transform: scaleY(0.5);
}
	.mainMenu.max {
		max-width: 768px;
		margin: 0 auto;
	}

	.mainMenu div {
		width: 25%;
		height: 22px;
		line-height: 22px;
		float: left;
		text-align: center;
		padding-top: 28px;
		background-image: url(/static/img/menu08b.png);
		background-repeat: no-repeat;
		background-size: 26px auto;
		color: #888;
	}
	.mainMenu div span{
		line-height: 20px;
	}
	.mainMenu div.selected {

		color: #00cc;
	}

	.mainMenu a div {
		color: #888;
		position: relative;
	}

	.mainMenu .myHome {
		background-position: 50% 3px;
	}

	.mainMenu .publish {
		background-position: 50% -55px;
	}

	.mainMenu .find {
		background-position: 50% -44px;
	}

	.mainMenu .category {
		background-position: 50% -95px;
	}
   .mainMenu .router-link-active div{
	   background-image: url(/static/img/menu05f08.png);
        color:#f90400;

   }
	.mainMenu .find .redPoint {
		position: absolute;
		top: 7px;
		padding: 0;
		left: 50%;
		margin-left: 8px;
		width: 8px;
		height: 8px;
		border-radius: 4px;
		background-color: #DF1F0A;
		display: none;
	}

	.mainMenu .my {
		background-position: 50% -142px;
	}

	/*活动图标*/
	.mainMenu .find.activity {
		height: 25px;
		background-image: url(/webApp/images/activity/1612131/findIcon.png);
		background-size: 25px auto;
		background-position: center 4px;
		background-repeat: no-repeat;
	}

	.mainMenu .find.vote .redPoint{
		display: block;
	}
	.icon_lists .kuai{
        width:100%;
        display: block;
    }

    #menu:after{display:block; content:"clear"; height:0; clear:both; overflow:hidden; visibility:hidden;}
</style>
<template>
<div  id="menu" name="menu" class="mainMenu icon_lists" >
    <router-link to="/" class="tab-item" exact>
		<div class="myHome">首页</div>
	</router-link>
	 <a href="http://w.tianbaoweipai.com/fabuindex.html" class="tab-item" >
        <div id="upload" class="category">发布</div>
     </a>

	<router-link class="tab-item" to="/find">
		<div class="find">
			<span>拍卖圈</span>
			<div class="redPoint"></div>
		</div>
	</router-link>
	<router-link class="tab-item" to="/usersellindex/index">
		<div class="my">我的</div>
	</router-link>
</div>
</template>
<script>
    import { mapState} from 'vuex';
    var config = require('../../config')
    import {weixincommonjsdk} from "../assets/js/common_function.js"
	module.exports = {
		data: function () {
			return {
			    uploadflag:false
			}
		},
		mounted: function () {
             this.weixin()
		},
		methods: {
		    camera:function(e){
                var that=this;
                var images = {
                    localId: [],
                    serverId: []
                };
              this.$store.state.localIds=[];
              this.$store.state.localIds_php=[];
                wx.ready(function(){
                          window.wx.chooseImage({
                                                                             count: 9, // 默认9
                                                                             sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                                                                             sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                                                                             success: function (res) {
                                                                                 var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                                                                                    that.$store.state.localIds=res.localIds;
                                                                                       that.$store.dispatch('uploadimages',[] );
                                                                                      that.$store.dispatch('uploadimages_server',[] );
                                                                                      that.$store.dispatch('uploadimages',res.localIds );
                                                                                    that.$router.push({ name: 'fabu', params: { localIds: localIds }})
                                                                                    return false;
                                                                             }
                                                                         });
                })
              return false;
		    },
		    weixin:function(){
                weixincommonjsdk();
              }
	}
	}
</script>


