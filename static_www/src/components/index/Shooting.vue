<template>
    <div class="shooting" @click="promp04">
        <div class="shooting_title">
            <p>精选</p>
            <div class="shooting_title_right">更多></div>
        </div>
        <template v-for="(item,key) in center_banners">
            <img :src="item.ad_code">
        </template>

    </div>
</template>

<script>
    export default {
        data(){
            return {
                isShow:false
            }
        },
        components:{

        },
        props:{
            center_banners:{
                type:Array,
                default:[]
            }

        },
        computed:{

        },
        methods:{
            promp04(){
                this.$router.push({ name: 'zhuanchang_link'})
            }
        }
    }
</script>
<style>
    .shooting{ margin-top: .1rem; margin-bottom: .1rem; width: 100%;  height: 4.3rem;  }
    .shooting img{ height: 3.5rem; width: 100%; padding: 0 .1rem}
    .shooting_title{ height: .8rem; width: 100%; background: #fff}
    .shooting_title p{ float: left; text-align: left; margin-left: .2rem;line-height:.8rem;font-size: .35rem}
    .shooting_title_right{ float: right;margin-right: .2rem; line-height: .8rem; color: #666;}
    .shooting_bottom{ width: 100%; height: .8rem; background: #fff;}
    .shooting_bottom p{float: left; text-align: left; margin-left: .2rem;line-height:.8rem;font-size: .3rem}
    .shooting_bottom_right{ border-radius:.4rem; height: .6rem; margin-top: .1rem; width:1.4rem;float: right;margin-right: .2rem; line-height: .6rem; color: #d9d9d9;  background: #999;}
</style>

