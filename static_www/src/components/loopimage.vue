<template>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="//img1.shikee.com/try/2016/06/23/14381920926024616259.jpg" ></div>
            <div class="swiper-slide"><img src="//img1.shikee.com/try/2016/06/23/14381920926024616259.jpg" /></div>
            <div class="swiper-slide"><img src="//img1.shikee.com/try/2016/06/23/14381920926024616259.jpg"/></div>
        </div>
        <!-- 如果需要分页器 -->
        <div class="swiper-pagination"></div>
    </div>
</template>

<script type="text/babel">
    import Vue from 'vue';
    // require styles
    import Swiper from 'swiper';
    export default {
        name: 'HelloWorld',
        data () {
            return {
                msg: 'Welcome to Your Vue.js App'}
        },
        components: {

        },
        methods: {

        },
        mounted(){
            // current swiper instance
            // 然后你就可以使用当前上下文内的swiper对象去做你想做的事了
            var mySwiper = new Swiper ('.swiper-container', {
                direction: 'horizontal',
                // 如果需要分页器
                pagination: {
                    el: '.swiper-pagination',
                }
            })
        },
        computed: {
            swiper() {
                return this.$refs.mySwiper.swiper
            }
        }
    }
</script>
<style>
    .swiper-container {
        width: 100%;
        height: 180px;
        background: #eee;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        font-size: 14px;
        color:#000;
        margin: 0;
        padding: 0;
    }
    .swiper-container img{
        width:100%;
        display: block;
    }
    .swiper-pagination-bullet-custom {
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        color: #000;
        opacity: 1;
        background: rgba(0,0,0,0.2);
    }
    .swiper-pagination-bullet-custom.swiper-pagination-bullet-active {
        color: #fff;
        background: #007aff;
    }
</style>
<script></script>