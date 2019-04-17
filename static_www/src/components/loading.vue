<template>
     <div name="nsr-loading" class="nsr-loading-center">
		<i class="fa fa-spinner fa-spin" :class="{'nsr-loading-hiden': hideLoading}">加载更多</i>
	</div>
</template>

<script type="text/babel">
  import weipai from '@/commonjs/util.js'
    export default{
      props: {
          hideLoading: {
              default: true
          },
          isEndText: {
              default: ''
          }
      },
      methods: {},
      created: function () {
          //判断是否是微信登录
		  var code =  weipai.is_code_state();
          weipai.getLogin(code)
      },
      mounted: function () {

      }
  }
</script>


<style type="text/css">
  .nsr-loading-center{
    text-align:center;
    margin: 10px auto 10px auto;
    color: #929292;
  }
  .nsr-loading-hiden{
    display: none;
  }
</style>
