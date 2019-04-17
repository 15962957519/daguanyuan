<template>
  <div id="app">
    <router-view @hasLoad="hasLoad" ></router-view>
    <!--<loading v-if="!loadingFlag"></loading>-->
  </div>
</template>
<script>
  import Loading from '@/components/base/loading/loading.vue';
export default {
  name: 'App',
  data() {
    return {
      loadingFlag: false
    };
  },
    created() {
        try {
            document.body.removeChild(document.getElementById('appLoading'))
            setTimeout(function() {
                document.getElementById('app').style.display = 'block';
            }, 1500)
        } catch (e) {

        }
    },
  methods: {
  hasLoad() {
      this.loadingFlag = true;
  }
  },
  components: {
    Loading,
  }
}
</script>

<style>
  [v-cloak] {
    display: none;
  }
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  height: 100%;
  color: #2c3e50;
}
</style>
