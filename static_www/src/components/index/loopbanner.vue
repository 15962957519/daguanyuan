<template>
    <yd-slider autoplay="3000">
        <template v-for="(item,key) in top_banners">
            <yd-slider-item>
                <a :href="item.ad_link">
                    <img   style=" height: 3.8rem"  :src="item.ad_code">
                </a>
            </yd-slider-item>
        </template>

    </yd-slider>
</template>

<script>
    import Vue from 'vue';
    import {Slider, SliderItem} from 'vue-ydui/dist/lib.rem/slider';
    /* 使用px：import {Slider, SliderItem} from 'vue-ydui/dist/lib.px/slider'; */

    Vue.component(Slider.name, Slider);
    Vue.component(SliderItem.name, SliderItem);
    export default {
        data(){
            return {
                isShow:false
            }
        },
        components:{

        },
        props:{
            top_banners:{
                type:Array,
                default:[]
            }

        },
        computed:{

        },
        methods:{

        }
    }
</script>
<style>

</style>
