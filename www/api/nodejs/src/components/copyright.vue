<template>
    <div class="supportBanner">
       <router-link :to="{path:'/'}">
         <img alt="微拍" src="../assets/img/logo100_100.png"><p> 由微拍免费技术支持</p>
       </router-link>
    </div>
</template>
<script>
  	module.exports = {
  		data: function () {
  			return {
          isShow: false
  			}
  		},
	    mounted: function () {

	    },
	    methods: {

	    }
  	}
</script>

<style>
.supportBanner{
visibility: visible;
padding: 30px 0;
width: 100%;
bottom: 0px;
#position: absolute;
margin: 0 auto;
text-align: center;
}
.supportBanner img{
display:inline;
vertical-align:middle;
width:70px;
height:70px;
}
</style>
