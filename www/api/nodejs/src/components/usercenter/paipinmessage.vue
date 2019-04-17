<style scoped>
  #usercy .well{
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
  }
  #paipinmessage{
      margin-top:20px;
  }
  #paipinmessage .time{
      text-align: center;
      color: #888;
      margin-bottom:20px;
  }

  #paipinmessage .container{
    background-color:#FFF;
    margin:8px 10px 40px 10px;
    border: 1px solid #FFF;
    border-radius: 4px;
  }

  #paipinmessage .container .lists .itemlink{
    display: block;
      padding:0 14px;
  }
  #paipinmessage .container .lists .itemlink span{
      overflow:hidden;
      white-space:nowrap;
      text-overflow:ellipsis;
      text-overflow: ellipsis;/* IE/Safari */
      -ms-text-overflow: ellipsis;
      -o-text-overflow: ellipsis;/* Opera */
  }
  #paipinmessage .container .lists .itemlink .item div{
      height:45px;
      background-size: cover;
  }
  #paipinmessage .container .lists .itemlink .item{
      color:#000;
      border-top:1px solid #eee;
      overflow: hidden;
      vertical-align:middle;
      height: 45px;
      width: 45px;
      float: right;
  }
  .clearfix:after {
      content: ".";
      clear: both;
      display: block;
      overflow: hidden;
      font-size: 0;
      height: 0;
  }
    .clearfix {
      zoom: 1;
  }
  #paipinmessage .container .lists img{
      width:45px;
      height:45px;
      float:right;
      margin:10px 0;
      vertical-align:middle;
  }
  #paipinmessage .container .lists span{
      display: inline-block;
      width: 66%;
      line-height: 45px;
      height: 45px;
  }
  #paipinmessage .container .header{
    padding:14px;
   position:relative;
  }
  #paipinmessage .container .title{
      position: absolute;
      bottom: 0;
      width: 100%;
      line-height: 26px;
      text-indent: 10px;
      z-index: 100;
      padding: 12px 0;
      font-size: 18px;
      background-color: rgba(0,0,0,0.7);
      color: #FFF;
      overflow:hidden;
      white-space:nowrap;
      text-overflow:ellipsis;
      text-overflow: ellipsis;/* IE/Safari */
      -ms-text-overflow: ellipsis;
      -o-text-overflow: ellipsis;/* Opera */
  }
  #paipinmessage .container .header a{
      display: block;
      position: relative;
  }

   #paipinmessage .container .header img{
    width:100%;
    height:auto;
  }
</style>

<template>
  <div id="paipinmessage" v-infinite-scroll="loadMore"   infinite-scroll-disabled="busy" infinite-scroll-distance="10">
        <template v-for="items in proresults">
               <p class="time"><span>{{items.upload_time}}</span></p>
                <div class="container">
                    <div class="header">
                            <router-link  :to="{name:'mymain',params:{userid:items.user_id,type:1}}">
                                <img v-for="(item,index) in items.nowaterimg" v-lazy="item.img" />
                                <div class="title">{{items.goods_content}}</div>
                            </router-link>
                    </div>
                    <div class="lists">
                              <router-link v-for="subitems in items.plists"  class="itemlink clearfix" :to="{path:'/mymain',query:{goods_id:subitems.goods_id,type:2}}">
                                <div class="item">
                                   <div  class="lazyLoad" v-for="(item,index) in subitems.nowaterimg"  v-lazy:background-image="item.img"></div>
                                </div>
                                  <span>{{subitems.goods_content}}</span>
                            </router-link >

                    </div>
                </div>
        </template>
  </div>
</template>
<script>
  var config = require('../../../config')

  export default {
    data(){
      return {
            page:1,
            busy:false,
            proresults:[]
      }
    },
    mounted: function() {

    },
    methods:{
        loadMore(){
            var that=this
            that.busy = true;
            var userid= this.$route.params.userid||0;
            var type= this.$route.params.type||0;
            var page =this.$route.params.page||1;
            var goods_id =this.$route.query.goods_id||0;
            that.fetchData(that,userid,type,page,goods_id);
            return false;
        },
        fetchData(progress,uid,type,page,goods_id){
            var _that=this;
            _that.busy = true
            var paramtrasform ={};
                       if(type>0){
                            paramtrasform = __.extend(paramtrasform,{type:type} )
                        }
                      var page =  _that.page++;
                       axios.get('/product/indexpaimaiprolistmessage?page='+page, {
                            params: paramtrasform
                          })
                          .then(function (response) {
                                    if(response.status==200){
                                            return response.data.plists;
                                     }
                          }).then(function(response){
                              if(_that.page<60){
                                for(var i=0;i<response.data.length;i++){
                                    _that.proresults.push(response.data[i]);
                                }
                                  _that.busy = false
                              }else{
                               return false;
                              }
                          }) .catch(function (error) {
                              console.log(error);
                          });

        }
    },
    computed:{

    }
  }
</script>

