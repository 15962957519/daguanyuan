<template>
<div id="topBanner">
    <div class="uutopBanner">
            <div @click="goBack" class="btn cancel">取&nbsp;消</div>
            <div  class="btn save">保&nbsp;存</div>
        </div>
        <toast></toast>
</div>
</template>


<script>
var toast  = require('../weui/toast.vue');
  import {
            mapState
    } from 'vuex';
module.exports={
        data(){
            return {}

        },
        components:{
        			'toast':toast
        		},
        methods:{
            goBack: function () {
             this.$router.go(-1);
            },
             submitform:function(s,e){


                        }
        }
}




</script>


<style scoped>


#topBanner .uutopBanner{
    width:100%;
    background-color:#fafafa;
    border-bottom:1px solid #bfbfbf;
    display:table;
}
#topBanner .uutopBanner .btn.cancel{
float:left;
text-align:left;
color:#888;
}

#topBanner .uutopBanner .btn{
float:right;
text-align:right;
color:#13ae24;
padding:13px 14px 13px 14px;
text-shadow:0px 0px 0px #FFF;
font-size:16px;
}
</style>
<style scoped>
 @import '../../assets/css/commonm.css'
</style>