<style scoped>
    .new_nav{max-width:640px; height:auto; background:#fff; margin:0 auto; }
    #style{ border-bottom:  1px solid  #ccc; }
    .wei{padding: 10px;font-size:18px;color:#ff0909;font-weight:600;}
    .wei span{font-size:14px;font-weight:normal}
    #headermessage ul li:before{
        float: left;
        display: block;
        content: ' ';
        width: 0px;
        height: 0px;
        margin-top: 7px;
        border: 5px solid transparent;
        border-left: 5px solid #ccc;
    }
    #headermessagedetail{
        clear: both;
        overflow: hidden;
        background: #fff;
        position: relative;
    }
    #headermessagedetail h2
    {
        font-size: 20px;
        color: #282828;
        font-weight: bold;
        line-height: 32px;
        font-family: \5FAE\8F6F\96C5\9ED1,\9ED1\4F53;
        margin: 12px 4.7% 0px;
    }
    #headermessagedetail .md_ly
    {
        margin:0px 4.7% 10px;
    }
    .td_nr p
    {
        padding-bottom: 8px;
        font-size: 18px;
        line-height: 27px;
        margin-top: 12px;
    }
    .td_nr{line-height: 30px;margin:0px 4.7%;color:#1a1a1a}
    .TRS_Editor{
        font-size: 14px;
    }

#headermessagedetail .wpt-share{
    position: fixed;

    width:100%;
    height:100%;
    top:0;
    left:0;
    bottom:50px;
    background-color:rgba(0,0,0,0.5);
display:none;



}

#headermessagedetail .share-box{
    position: fixed;
    width:7.5rem;
    top:auto;
    bottom:50px;
    background-color: #EFEFF4;
    z-index: 1999;


}
#headermessagedetail .share-box.fill-ip{
    animation:flipUp 0.3s ease-out;
    visibility: visible !important;

}

#headermessagedetail .share-box .bt-share{
position: relative;
width:33%;
font-size:12px;
float:left;
text-align: center;
text-decoration:none;
padding: 60px 20px;


}
#headermessagedetail .share-box .title{
    color:#424242;
    margin:0 auto;
    text-align: center;
}
#headermessagedetail .share-box .title h1{
    margin:5px auto;
}
#headermessagedetail .share-box .bt-sharef .icon{
    position: absolute;
    width:50px;
    height:50px;
    top:0;
    left:50%;
    margin-left:-25px;
    background:url(../assets/img/friend.png)  no-repeat top left scroll;
    background-size:contain;
    border:1px solid #def;

}


#headermessagedetail .share-box .bt-sharefg .icon{
    position: absolute;
    width:50px;
    height:50px;
    top:0;
    left:50%;
    border:1px solid #def;
    margin-left:-25px;
    background:url(../assets/img/goodfriend.png)  no-repeat top left scroll;
    background-size:contain;


}

#headermessagedetail .saleMain .saleInfo .desc.fullDesc{
max-height:none;
overflow-y: auto;
}

#headermessagedetail  .wptShare{
display: none;
position:fixed;
top:0;
opacity: 0.6;
bottom:0;
width:100%;
height:100%;
background-color:#000;
z-index:1999;
}



#headermessagedetail  .wptShare .wptMask{
    position:fixed;
    top:0;
    bottom:0;
    left:0;
    ringht:0;
    width:100%;
    height:100%;
    background-color:#000;
    z-index:1999;

}

#headermessagedetail  .wptShare .shareTip{
    position:fixed;
    top:0;
    width:96%;
    height:286px;
    background-image:url(../assets/img/share.png);
    background-repeat:no-repeat;
    background-size:auto 55%;
    background-position:right top;
    z-index:2000;
}
#headermessagedetail  .tip p{
    margin-left:5%;
    margin-top: 10px;
    line-height: 30px;
    font-size: 16px;
    text-indent: 10px;
}
</style>


<template>
    <div v-if="articles_itemflag" id="headermessagedetail" class="new_nav">
        <h2>{{articles.title}}</h2>
        <div class="md_ly clearfix">
            <span class="time">{{articles.add_time}}</span> <span class="media_ly">来源：{{articles.author}}</span>
        </div>

        <!--n正文内容-->

        <div class="td_nr">
            <div id="content" class="TRS_Editor" v-html="articles.content">
            </div>
        </div>
         <div id="wpt-share" class="wptShare">
                <div class="wptMask" style="opacity:0.7" ></div>
                <div class="shareTip"></div>
         </div>
        <spread v-if="articles_itemflag"  :articles_item="articlesspread"></spread>
        <div class="tip">
                 <p>本文被转发次数 >> {{articles.forward}}次</P>
                <p> 99%的人阅读后会选择转发此文章</P>
                <p> 1.点击右上角“...”</P>
                <p>2.分享到朋友圈或发送给朋友</P>
        </div>
    </div>
</template>
<script>
    var config = require('../../config')
    import {weixincommonjsdk,commonsharejs} from "../assets/js/common_function.js"
    var spread  = require('./spread.vue');
    module.exports = {
        components:{
            'spread':spread,
        },
        data: function () {
            return {
                articles:[],
                articlesspread:[],
                articles_itemflag:false
            }
        },
        mounted: function () {
           var  article_id = this.$route.params.article ||0;

            this.fetchData(article_id);
        },
        methods: {
            fetchData: function (article_id) {
                var _that = this
                //检查是否支付过保证金
                axios.get('article/detail',
                    {
                        params:{article_id:article_id},
                        headers: {
                            'Content-Type': 'application/json;charset=UTF-8'
                        }
                }
                ).then(function (response) {
                    if (response.status == '200') {
                        return response.data;
                    }
                }).then(function (json) {
                        var  articlest=  json.data.article

                         _that.articlesspread =articlest;

                        articlest['content']= _that.htmlspecialchars_decode(articlest['content']);
                         _that.articles =articlest
                        //   _that.articlesspread['content']='';
                        _that.articles_itemflag=true;

                        var btn = document.getElementById("headermessagedetail");
                        //通用方法
                        btn.onclick = function() {
                            var e=document.createEvent("MouseEvents");
                            document.dispatchEvent(e);
                        }

                }).catch(function (ex) {
                    console.log(ex);
                });

            },
             htmlspecialchars_decode:function (str){
                        if(str.replace(/(^s*)|(s*$)/g, "").length ==0){
                                return '';
                        }
                str = str.replace(/&amp;/g, '&');
                str = str.replace(/&lt;/g, '<');
                str = str.replace(/&gt;/g, '>');
                str = str.replace(/&quot;/g, "''");
                str = str.replace(/&#039;/g, "'");
                return str;
            },
        }
    }
</script>


