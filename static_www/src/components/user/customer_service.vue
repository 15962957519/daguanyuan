<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">客服中心</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--在售和下架-->
        <div class="customer_se_img">
            <img src="@/assets/images/user/searve.png">
        </div>
        <!--问题反馈-->
        <div class="quesion_s">
        <yd-cell-group >
         <yd-cell-item type="label">
            <span slot="left">问题类型：</span>
            <select slot="right">
                <option value="1">个人店铺</option>
                <option value="2">商户店铺</option>
            </select>
         </yd-cell-item>

            <!--文本-->
                <yd-cell-item>
                    <yd-textarea slot="right" placeholder="请输入您要反馈的问题" maxlength="100"></yd-textarea>
                </yd-cell-item>
        </yd-cell-group>
            <div class="answer_it"  @click="answer">问答记录</div>
            <yd-button bgcolor="#af773e" color="#fff" size="large" type="warning">提  交</yd-button>
        </div>
        <!--&lt;!&ndash;问题反馈&ndash;&gt;-->
        <!--<div class="quesion_s">-->
            <!--<yd-cell-group >-->
                <!--<yd-cell-item >-->
                    <!--<span slot="left" style="color:#af773e;">您可以在下方添加图片</span>-->
                <!--</yd-cell-item>-->
                <!--&lt;!&ndash;文本&ndash;&gt;-->
                <!--<yd-cell-item>-->
                    <!--<yd-textarea slot="right" placeholder="请输入您要反馈的问题" maxlength="100"></yd-textarea>-->
                <!--</yd-cell-item>-->
            <!--</yd-cell-group>-->

        <!--</div>-->
    </div>
</template>
<script>

    export default {
        data() {
            return {
            }
        },
        methods: {
            goback () {
                this.$router.go(-1)
            },
            answer(){
                this.$router.push('/user/question_answer')
            }

        }
    }

</script>
<style>
    .margin{ padding-top: 1.1rem;  padding-bottom: .3rem;}
    .customer_se_img{ width: 100%; height: 3.5rem; }
    .customer_se_img img{ width: 100%;}
    .quesion_s{ width: 100%;  padding: 0 .2rem; margin-top: .1rem; }
    /*问答记录*/
    .answer_it{color: #fff; font-size: .3rem; line-height: .6rem; border-radius: .1rem; width: 2.5rem; height: .6rem; background: red; float: right; margin-bottom: .3rem; }
</style>