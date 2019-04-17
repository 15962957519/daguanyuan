<template>


</template>
<style>

</style>
<script>
  import Vue from 'vue';
  import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
  /* 使用px：import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar'; */
  Vue.component(NavBar.name, NavBar);
  Vue.component(NavBarBackIcon.name, NavBarBackIcon);
  Vue.component(NavBarNextIcon.name, NavBarNextIcon);
  export  default{
    data(){

      },

    methods:{
        goback () {
          this.$router.go(-1)
        }
      }

  }

</script>