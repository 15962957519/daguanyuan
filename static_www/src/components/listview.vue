<template>
    <div class="hello">

    </div>
</template>
<script type="text/babel">
    import Vue from 'vue';
    import {ListTheme, ListItem, ListOther} from 'vue-ydui/dist/lib.rem/list';


    Vue.component(ListTheme.name, ListTheme);
    Vue.component(ListItem.name, ListItem);
    Vue.component(ListOther.name, ListOther);
    var axios = this.$axios
    export default {
        name: 'listview',
        data: function () {
            return {
                busy:false,
                limitNum: 2,
                content: [],  //内容页
                //price_count : {}, //出价次数
                click_zan: '', //点赞次数
                bao_price: {
                    is_authentication: '',
                    nickname: '',
                    start_price: '',
                    protect_price: '',
                    every_add_price: '',
                    goods_name: '',
                    cur_price: ''
                },  //报价
                goods_image: [],//商品图片
                goods_image_detail: [],//商品图片
                goods_content: '', //商品详情
                goods_downtime: '', //倒计时
                goods_ids: '', //商品id
                dz_status: '', // 是或否点赞
                myself_id: '', //自己的id
                fans_img: '', //粉丝头像
                price_count: '',
                people_look: '',
                endtime: [],
                ifendtime: 0,
                current_time: 0,
                current_moneys: '',
                upload_time: [],
                bmoney: {state: ''},
                gz_people: '',
                fens_nums: '',
                goods_counts: '',
                new_counts: '',
                is_authr: '',
                isGz2: false,
                isGz1: true,
                zan_img: '',
                zan_img_yes: '__STATIC__/img/item_dz.png',
                zan_img_no: '__STATIC__/img/ax.png',
                share_config: ''
            }
        },
        props: {
            data: {
                type: Array,
                default: []
            }
        },
        template: '#parentitem_container',
        mounted: function () {

        },
        computed: {
            filterAddress: function () {
                return this.content.slice(0, this.limitNum);
            }
        },
        methods: {



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
</style>
