<template>
    <div>
        <!--顶部导航-->
        <yd-navbar bgcolor="#af773e" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;color: #fff;" slot="center">积分明细</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>
        </yd-navbar>
        <!--空隙-->
        <div class="black"></div>

        <!--菜单-->
        <div id="container" style="padding-top: 1rem; font-size: .35rem;">
           暂无积分
        </div>
        <!--菜单结束-->
    </div>
</template>
<style scoped>
    @import url(../../../assets/css/rechargelog.css);

</style>
<script>
  import Vue from 'vue';
  import weipai from '@/commonjs/util.js'
  import { mapState} from 'vuex';
  import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
  /* 使用px：import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar'; */
  Vue.component(NavBar.name, NavBar);
  Vue.component(NavBarBackIcon.name, NavBarBackIcon);
  Vue.component(NavBarNextIcon.name, NavBarNextIcon);
  export  default{
    data(){
      return{}
    },
    methods:{
         goback () {
           this.$router.go(-1)
          }
    },

      computed:mapState({
          user_data(state){
                    if(weipai.isEmptyObject(state.menuItems)){
                        this.$store.dispatch("getuserinfo");
                    }
                    console.log(state.menuItems)
              return state.menuItems
          }
      }),
  }
</script>