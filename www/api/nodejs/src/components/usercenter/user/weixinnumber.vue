<template>
  <div id="contacts">
        <topbarner></topbarner>
        <form id="usersingnature">
         <div class="editMain">
                <input name="weixinnumber" maxlength="15" placeholder="请输入您的微信号..."/>
                <div class="countNum"></div>
            </div>
        </form>
  </div>
</template>
<script>
	var topbarner  = require('../topBanner.vue');
	var toast  = require('../../weui/toast.vue');
    import {
            mapState
    } from 'vuex';

module.exports={
        data: function() {
                    return {
                    userinfo: [{'user_name': '', 'head_pic': ''}]
                }
		},
		components:{
			'topbarner':topbarner,
			'toast':toast
		},
		methods:{
            submitform:function(s,e){
             console.log(3)
            }
		},
		mounted:function(){
		var  _self =this;
		        $("#topBanner .uutopBanner .save").on('click',function(e){
               //  axios.defaults.headers.common['Authorization'] = 'Bearer'+storeWithExpiration.get('token');
		        var formElement = document.querySelector("#usersingnature");
                var dd =new FormData(formElement);
                    dd.append('token',storeWithExpiration.get('token'))
                  //提交到后台
                    axios.post('/user/weixinnumber', dd)
                      .then(function (response) {
                          var $toast = $('#toast');
                                  if ($toast.css('display') != 'none') return;
                                  $toast.fadeIn(100);
                                  setTimeout(function () {
                                      $toast.fadeOut(100);
                                  }, 2000);


                      _self.$router.go(-1);
                      })
                      .catch(function (error) {
                        console.log(error);
                      });
                      e.preventDefault();
		        e.stopPropagation();
		        });



		},
		  computed: mapState({






		  })

}
</script>

<style scoped>
.editMain{
position:relative;
overflow:hidden;
margin-top:12px;
background-color:#FFF;
border-top:1px solid #d9d9d9;
border-bottom:1px solid #d9d9d9;
}
.editMain input{
width:78%;
padding:5px 8% 5px 2%;
border:none;
height:70px;
outline:none;
font-size:14px;
overflow:hidden;

}
.editMain .countNum{
position:absolute;
top:0;
right:5px;
line-height:70px;
}
</style>
