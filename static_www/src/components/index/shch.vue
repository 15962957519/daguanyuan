<template>
    <div class="new_list">
        <ul>
            <li class="new_list_one" @click="todo_new">
                <img src="@/assets/images/zixun.png">
                <!--<span style="color:#b58352;padding: .15rem .1rem; height: .4rem; line-height: .1rem; border-left:1px solid #eee; "> NEW </span>-->
            </li>
            <li class="new_list_two">
                <yd-rollnotice autoplay="2000" align="left" >
                    <template v-for="(item,key) in articles">
                        <yd-rollnotice-item @click.native="new_result(item.article_id)"><span style="color:#b58352;padding: .15rem .1rem; height: .4rem; line-height: .1rem; border-left:1px solid #eee; "> NEW </span>{{item.title}}</yd-rollnotice-item>
                    </template>
                </yd-rollnotice>
                <yd-rollnotice direction="down" style="margin-top: -.15rem;" autoplay="2000" align="left" >
                    <template v-for="(item1,key) in articles1">
                        <yd-rollnotice-item @click.native="new_result(item1.article_id)"><span style="color:#b58352;padding: .15rem .1rem; height: .4rem; line-height: .1rem; border-left:1px solid #eee; "> HOT </span>{{item1.title}}</yd-rollnotice-item>
                    </template>
                </yd-rollnotice>
            </li>
        </ul>


    </div>

</template>
<script>

    import Vue from 'vue';
    import {RollNotice, RollNoticeItem} from 'vue-ydui/dist/lib.rem/rollnotice';
    /* 使用pximport {RollNotice, RollNoticeItem} from 'vue-ydui/dist/lib.px/rollnotice'; */

    Vue.component(RollNotice.name, RollNotice);
    Vue.component(RollNoticeItem.name, RollNoticeItem);
    export default {
        data(){
            return {
                isShow:false,
                articles: [],
                articles1:[]
            }
        },
        components:{

        },
        watch: {
            two_articles(newValue, oldValue) {
                //   console.log(oldValue)
                //  return;
                this.articles = []
                this.articles1 = []
                var lengh=newValue.length/2
                for(var i=0;i<lengh;i++){
                    this.articles[i]=newValue[i*2];
                    this.articles1[i]=newValue[i*2+1];
                }
                if(newValue.length % 2 != 0 ){
                    this.articles1.pop()
                }
                // console.log(this.articles1)
                // let a = 0
                // for (let i = 0; i < newValue.length; i++) {
                //     if (i % 2 != 1 && i != 0) {
                //         a++
                //     }
                //     this.articles[a] = this.articles[a] instanceof Array ? this.articles[a] : []
                //     this.articles[a].push(newValue[i])
                // }
            }
        },
        props:{
            two_articles:{
                type:Array,
                default:[]
            }

        },

        methods:{
            todo_new(){
                this.$router.push({name: 'new_index_link'})
            },
            new_result(e){
                var article_id =e
                this.$router.push({
                    name: 'new_result_link',
                    query: {
                        article_id: article_id
                    }
                })
            }

        }
    }

</script>
<style>
    .new_list_one{ width: 17%;}
    .new_list_one img{  padding: .15rem 0 .12rem 0; height: 1.2rem}
    .new_list_two{ width: 80%;}
    .new_list{ height: 1.3rem; width: 100%; background: #fff; margin: .1rem 0;}
    .new_list ul li{ float: left;}
    .yd-rollnotice-item{ background: #fff; }
    .yd-rollnotice{   margin-bottom: .1rem;line-height: 30px; overflow: hidden }
    .yd-rollnotice-item img{ width: 1rem; margin-left: .1rem; margin-right: .1rem;}

</style>
