<template>
  <div id='main_container' class="main_container">
    <div class="left">
      <div class="content">
        <!-- 图片轮播 -->
        <div class="slide" id="slide3">
          <ul>
                  <li>
                                <router-link   to="/usersellindex/mumberupgrade">
                              <img    src="http://img.tianbaoweipai.com/weipaiimg/ad/1704/1.jpg" alt="首页轮播图">
                                </router-link>
                             </li>
                             <li>
                                <router-link   to="/usersellindex/mumbercheck">
                                             <img   src="http://img.tianbaoweipai.com/weipaiimg/ad/1704/2.png" alt="首页轮播图">
                                             </router-link>
                             </li>
          </ul>
          <div class="dot">
            <span></span>
            <span></span>
          </div>
        </div>

        <div class="Switch clear">
          <ul class="clear">
            <li class="fl active">
              <div class="SwitchBg" style='background:url("/static/img/category.png") no-repeat 0px -9px;'></div>
              <p>玉翠珠宝</p>
            </li>
            <li class="fl active">
              <div class="SwitchBg" style='background:url("/static/img/category.png") no-repeat 0px -70px;'></div>
              <p>书画篆刻</p>
            </li>
            <li class="fl active">
              <div class="SwitchBg" style='background:url("/static/img/category.png") no-repeat 0px -132px;'></div>
              <p>茶酒滋补</p>
            </li>
            <li class="fl active">
              <div class="SwitchBg" style='background:url("/static/img/category.png") no-repeat 0px -194px;'></div>
              <p>紫砂陶瓷</p>
            </li>
            <li class="fl active">
              <div class="SwitchBg" style='background:url("/static/img/category.png") no-repeat 0px -255px;'></div>
              <p>工艺作品</p>
            </li>
            <li class="fl active">
              <div class="SwitchBg" style='background:url("/static/img/category.png") no-repeat 0px -316px;'></div>
              <p>文玩杂项</p>
            </li>
          </ul>
        </div>

      </div>
    </div>
    </div>
</template>


<script>
module.exports={
      data:{

      },
    mounted:function(){
      $(function(){
        $('#slide3').swipeSlide({
          continuousScroll:true,
          mode:'horizontal',
          loop: true,
          autoplayStopOnLast:true,
          autoplayDisableOnInteraction:false,
          speed : 3000,
          transitionType : 'cubic-bezier(0.22, 0.69, 0.72, 0.88)',
          firstCallback : function(i,sum,me){
            me.find('.dot').children().first().addClass('cur');
          },
          callback : function(i,sum,me){
            me.find('.dot').children().eq(i).addClass('cur').siblings().removeClass('cur');
          }
        });
      });
    }
}
</script>

<style>

</style>
