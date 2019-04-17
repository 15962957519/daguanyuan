<template>
  <div>
        <topbarner></topbarner>

  </div>
</template>
<script>
	var topbarner  = require('./topBanner.vue');


module.exports={
        data: function() {
                    return {
                    userinfo: [{'user_name': '', 'head_pic': ''}]
                }
		},
		components:{
			'topbarner':topbarner
		},
		methods:{



		},
		mounted:function(){

		}

}
</script>

<style scoped>

</style>
<style scoped>
 @import '../../assets/css/commonm.css'
</style>