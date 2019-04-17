<template>
    <div class="search d1">
        <div class="search_list"><img src="@/assets/images/search.png"></div>
        <div class="div_input"  v-on:click="search()">
            <!--<input type="text" v-model="keywords" placeholder="搜索从这里开始...">-->
            <!--<button type="submit">搜索</button>-->
            搜索您想要的商品
        </div>
        <div class="search_message" v-on:click="publish()"><img src="@/assets/images/message.png"></div>
    </div>

</template>
<script type="text/babel">
    export default {
        data() {
            return {
                keywords: ''
            }
        },
        methods: {
            search() {
                this.$router.push({name: 'seach_index_link'})
                //this.$dialog.toast({mes: '搜索：' + this.keywords});
                // this.$router.push({name: 'class_list_link',query:{keywords:keywords}})
            },
            publish(keywords) {
                this.$router.push({name: 'publishlink'})
                //this.$dialog.toast({mes: '搜索：' + this.keywords});
                // this.$router.push({name: 'class_list_link',query:{keywords:keywords}})
            }
        }
    }
</script>
<style>
    .search_message{float: right; width: 10%;   margin-right:.16rem; height: .65rem;  margin-top: -.8rem; }
    .search_message img{width: .65rem}
    .search_list{float: left;width: 10%; height: .65rem;margin-left: .37rem;background:#fff; border-radius: 2px 0 0 2px; }
    .search_list img{width:.35rem; margin-top:.15rem;}
    .search {padding: .1rem 0; width: 100%;top:0;left:0; height: .85rem; position: fixed;z-index: 10}
    .div_input {
        position: relative;
        width: 72%;
        margin: 0 auto;
        background: #fff;
        height: .65rem;
        text-align: left;
        line-height: .65rem;

        color: #999;
    }
    .d1 {background:rgba(0,0,0,.1)}
    .d1 input {
        border: 0;
        width: 100%;
        height: .65rem;
        padding-left: 10px;
        border-radius:0 2px 2px  0px;
        outline: none;
        background: #fff;
        color: #9E9C9C;
        font-size: .25rem;
    }
    .d1 button {
        position: absolute;
        top: 0;
        right: 0px;
        width: .8rem;
        height: .8rem;
        border: none;
        background: #7BA7AB;
        border-radius: 0 5px 5px 0;
        cursor: pointer;

    }

</style>

