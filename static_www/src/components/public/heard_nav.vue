<template>
  <yd-tabbar bgcolor="rgba(0,0,0,0.8)">
    <yd-tabbar-item title="首页" link="/index" >
      <img style="width: 0.7rem;"  src="@/assets/images/foot/home2.png">
    </yd-tabbar-item>
    <yd-tabbar-item title="分类" link="/classification/class_index">
      <img style="width: 0.7rem;" " src="@/assets/images/user/different11.png">
    </yd-tabbar-item>
    <yd-tabbar-item title="发布" link="/publish/publish">
      <img style="width: 0.7rem;" class="po_img"  src="@/assets/images/foot/publish.png">
    </yd-tabbar-item>
    <yd-tabbar-item title="购物车" link="/user/shoping_cart">
      <img style="width: 0.7rem;" src="@/assets/images/foot/trolley1.png">
    </yd-tabbar-item>
    <yd-tabbar-item title="个人中心" link="/user/user">
      <img style="width: 0.7rem;"  src="@/assets/images/foot/me.png">
    </yd-tabbar-item>
  </yd-tabbar>
</template>

<script>
    import Vue from 'vue';
    import {TabBar, TabBarItem} from 'vue-ydui/dist/lib.px/tabbar';
    Vue.component(TabBar.name, TabBar);
    Vue.component(TabBarItem.name, TabBarItem);
   import {Icons} from 'vue-ydui/dist/lib.px/icons';

    Vue.component(Icons.name, Icons);
export default {
  name: 'HelloWorld',
  data () {
    return {
      msg: 'Welcome to Your Vue.js App'
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
  .po_img{
    width: 1rem;
    height: 1rem;
    position: absolute;
    z-index: 33;
    margin-top: -.3rem;
    margin-left: -.5rem;
  }
</style>
