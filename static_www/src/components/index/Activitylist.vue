<template>
    <div class="activitylist">
        <!--新的分类-->

        <div style="float: left" class="huodong2">
            <div @click="promp03">
                <div class="title-td" style="border-right: 0">
                    <p class="huodong_title"><strong>今日推荐</strong></p>
                    <p class="huodong_title_p"> &nbsp;  </p>
                    <p><img src="@/assets/images/huodong01.png"> </p>
                </div>
            </div>

            <div @click="promp03">
                <div style="padding-top:1.05rem;" class="title-td">
                    <p><img  src="@/assets/images/huodong02.png"> </p>
                </div>
            </div>
        </div>
        <!--新的分类结束-->

        <!--右边结束-->
        <div style="float: left" class="huodong2">
            <div @click="promp02">
                <div class="title-td" style="border-right: 0">
                    <p class="huodong_title"><strong>商城逛逛</strong></p>
                    <p class="huodong_title_p"> &nbsp;  </p>
                    <p><img src="@/assets/images/photo222.png"> </p>
                </div>
            </div>

            <div @click="promp02">
                <div style="padding-top:1.05rem;" class="title-td">
                    <p><img  src="@/assets/images/photo221.png"> </p>
                </div>
            </div>
        </div>
        <!--右边开始-->
        <div style="float: left" class="huodong3">

            <div @click="promp01">
                <div class="title-td">
                    <p class="huodong_title">天天特价</p>
                    <p class="huodong_title_p">抢到即赚到</p>
                    <p><img src="@/assets/images/huodong04.png"></p>
                </div>
            </div>

            <div @click="promp05">
                <div class="title-td">
                    <p class="huodong_title">新品开抢</p>
                    <p class="huodong_title_p">好货不可错过</p>
                    <p><img style=" margin-top: .4rem;" src="@/assets/images/huodong08.png"></p>
                </div>
            </div>

            <div @click="promp04">
                <div class="title-td">
                    <p class="huodong_title">优选店铺</p>
                    <p class="huodong_title_p"> 臻品收藏</p>
                    <p><img style="width: 1.2rem" src="@/assets/images/shop.png"></p>
                </div>
            </div>
        </div>
        <!--新的分类结束-->

    </div>
</template>
<script>
    export default{
        methods:{
            promp(){
                alert("专区暂未开放，敬请期待！")
            },
            promp01(){
                this.$router.push({ name: 'today_tuijian_link'})
            },
            promp02(){
                this.$router.push({ name: 'class_list_link'})
            },
            promp03(){
                this.$router.push({ name: 'daydayprice_link'})
            },
            promp04(){
                this.$router.push({ name: 'good_shop_link'})
            },
            promp05(){
                this.$router.push({ name: 'new_shop_link'})
            }
        }
    }
</script>
<style>
    .activitylist{width: 100%; height: 6rem;}
    /*工艺活动*/
    .huodong{ float:left;border-bottom:1px solid #eee; height:3rem;;border-right: 1px solid #eee;background:#fff; width: 50%}
    .huodong p{ text-align: left}
    .huodong p img{ width: 1.2rem; }
    .huodong_title{ font-size: .35rem; color: #333;margin-left: .2rem;padding-top: .2rem; }
    .huodong_title_p{ font-size: .25rem; color: #999;margin-left: .2rem; padding-top: .1rem}
    .huodong2{background: #fff; height:3rem; width: 50%; float: right; }
    .title-td{height:3rem; width:50%; border-right: 1px solid #eee; float: left; border-bottom:1px solid #eee; text-align: left;}
    .title-td img{ width: 1rem; margin-right: .3rem; margin: 0.5rem auto; display: block }
    .title-tr{height:1.5rem;border-bottom:1px solid #eee; text-align: left;}
    .title-tr img{ float: right; width: 1rem; margin-right: .3rem; margin-top: -.9rem}
    .huodong3{background: #fff; height:3rem; width: 100%;  }
    .huodong3  .title-td{ width: 33.33333%; text-align: center;}
</style>
