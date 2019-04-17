<template>
  <div id="app" style="height:100%">
    <!-- set progressbar -->
      <!--<vue-progress-bar></vue-progress-bar>-->
      <router-view></router-view>
  </div>
</template>
export default {
  name: 'app',
}
<style>
  [v-cloak] {
    display: none;
  }
</style>
