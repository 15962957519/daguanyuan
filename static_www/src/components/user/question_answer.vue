<template>
    <div class="margin">
        <!--顶部导航-->
        <yd-navbar :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon     @click.native="goback">返回</yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem" slot="center">我的问答</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different.png"/>
        </yd-navbar>
        <!--在售和下架-->
    </div>
</template>
<script>

    export default {

        methods: {
            goback () {
                this.$router.go(-1)
            },

        }
    }

</script>
<style>
    .margin{ padding-top: 1rem;}
</style>