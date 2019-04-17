<style>
.my-componet h2{
color:red;
}

</style>


<template>
	<div class="my-componet">
	 <h2>{{msg}}</h2>
	 </div>
	 </template>

	 
	 <script>
	 
	 module.exports ={
	  data:function(){
			return {
			msg:'hello!'
			}
	  
	  }
	 
	 
	 
	 
	 }
	</script>