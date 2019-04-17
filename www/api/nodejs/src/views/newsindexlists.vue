<style scoped>
ul{
    display: block;
}
 #newsindexlists{
 position: relative;
     z-index: 0;
     max-width: 660px;
     margin: 0 auto;
 }
 #newsindexlists .header{
 height:36px;
 background:#FFF;
 }

 #newsindexlists  .listsarticles{
 margin-top:50px;
 }
#newsindexlists  .sort{
width: 100%;
position: fixed;
overflow-y: scroll;
top: 0;
background-color: #FFF;
z-index: 9999;
}
#newsindexlists  .sort ul{
width:1200px;
}
#newsindexlists  .sort ul li{
height: 100%;
display: inline-block;
line-height: 50px;
padding: 0 4px;
margin: 0 4px;
font-size:18px;
position: relative;
cursor: pointer;
}

#newsindexlists ul li:before{
    width: 100%;
    height: 1px;
    left: 0;
    top: 0;
    position: absolute;
    content: "";
    background-color: #e4e4e4;
 }
 #newsindexlists ul .listsrow{
    position: relative;
    line-height: 30px !important;
    height: 49px!important;
    box-sizing:content-box !important;
    font-size: 18px;
    padding: 5px 15px 5px 10px;
    background: #f6f6f6;
 }
  #newsindexlists ul li .article_title{
 white-space: nowrap;
 overflow: hidden;
 text-overflow:ellipsis;      /*兼容IE*/
 }
#newsindexlists .l{
 float:left;
 font-size:13px;
  color:#8c97a2;
  line-height:18px;
}
#newsindexlists .r{
 float:right;
 color:#8c97a2;
 line-height:18px;
  font-size:13px;
}
  #newsindexlists ul li .article-date{
    width:39%;

  }
  #newsindexlists  .sort ul li.router-link-active {
    color: #fe0100;
    font-weight: bold;
}
 #newsindexlists   .sort .more{
height: 50px;
    width: 50px;
    position: fixed;
    top: 0;
    right: 0;
    z-index: 100;
    background-color: #FFF;
 }
   #newsindexlists   .sort .more i {
      display: inline-block;
      margin: 14px;
      height: 22px;
      width: 22px;
      background-image: url(/static/img/add.png);
      background-size: 100% auto;
  }


   #newsindexlists ul li .article-forward{
    width:30%;
  }
  #newsindexlists .HeaderContentRight img{
    display:inline-block;
  }
  .clearfloat{clear:both;height:0;font-size: 1px;line-height: 0px;}
  .clearfix:after{
    content:".";
    display:block;
    height:0;
    clear:both;
    visibility:hidden;
  }
  .clearfix{
    zoom:1
  }

</style>

<template>
    <div id='newsindexlists' class="newsindexlists">
            <div class="sort clearfix">
            			<ul data-flag="0">
            			  <router-link tag="li"  v-for="itemcoll in classification"  :to="{name:'newsindexlists',params:{cate_id:itemcoll.cat_id}}"    >{{itemcoll.cat_name}}</router-link>
            			</ul>
            			<div class="more"><i></i></div>
            		</div>
        <ul  class="listsarticles clearfix">
             <li class="listsrow" v-for="result in articles">
                    <div class="article_title"> <router-link  :to="{name:'newsdetail',params:{article:result.article_id}}" >{{result.title}}</router-link></div>
                    <div class="article-info">
                        <div class="article-date l">{{result.add_time}} </div>  <div class="article-forward l">转发{{result.forward}} 次</div>
                        <div class="article-source r">{{result.author}} </div>
                    </div>
             </li>
        </ul>
           <copyright></copyright>
    </div>
</template>

<script type="text/babel">
  let alert  = require('../components/alert.vue');
  let menu  = require('../components/menu.vue');
  let nsr_loading  = require('../components/loading.vue');
  var copyright  = require('../components/copyright.vue');
  module.exports = {
      data: function() {
          return {
              showlists: false,
              articles:'',
              classification:[],
              cate_id:0
          }
      },
    components:{
      'menus':menu,
      'nsr-loading':nsr_loading,
      copyright
    },
    methods:{
                fetchData: function (progress) {
                        var _that = this
                          var cate_id = this.$route.params.cate_id ||0;
                        axios.get('article/index',
                         {params: {
                                                limit: 20,
                                                cate_id:cate_id
                                            }}
                        ).then(function (response) {
                            if (response.status == '200') {
                                return response.data;
                            }
                        }).then(function (json) {
                            _that.articles=json.data.hot_article_lists
                            _that.classification=json.data.classification
                            console.log(_that.classification)
                        }).catch(function (ex) {
                            console.log(ex);
                        });
                    }
    },
    mounted:function() {
        document.title = "天宝微拍头条列表"
           this.cate_id = this.$route.params.cate_id ||0;
        this.fetchData();
        // 显示B
        setTimeout(() => {
            this.showlists = true;
        }, 0);
    },
    watch: {
         // 如果路由有变化，会再次执行该方法
         "$route": "fetchData"
        }
  }
</script>

