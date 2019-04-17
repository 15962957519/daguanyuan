<template>
    <div>
        <!--顶部导航-->
        <yd-navbar bgcolor="#af773e" :fixed="true" >
            <router-link   to="" slot="left" >
                <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
            </router-link>
            <p style="font-size: .3rem;color: #fff;" slot="center">提现记录</p>
            <img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>
        </yd-navbar>


        <!--菜单-->
        <div class="mui-content balance_record_container" style="padding-top: 1rem;">
            <div class="bottom" id="log">
                <div id="item1" class="mui-control-content mui-active">
                    <!-- ================================================= -->
                    <div class="list" v-for="(item,index) in Array.prototype.reverse.call(rechargeLog)" >
                        <div class="top">
                            <div>提现</div>
                            <div>单号：{{ item.apply_number }}</div>
                        </div>
                        <div class="bottom">
                            <div>{{ item.create_time |time}}</div>
                            <div  class="text">
                               <span v-if="item.status==0"> 审核中</span>
                                <span v-if="item.status==1"> 提现成功</span>
                                <span style="color: #666" v-if="item.status==2">审核未通过</span>
                            </div>


                        </div>
                    </div>
                    <!-- ================================================= -->
                </div>

            </div>
        </div>

        <!--菜单结束-->
    </div>
</template>
<style scoped>
    @import url(../../../assets/css/rechargelog.css);

</style>
<script>
    import Vue from 'vue';
    import weipai from '@/commonjs/util.js'
    import { mapState} from 'vuex';
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.rem/navbar';
    /* 使用px：import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar'; */
    Vue.component(NavBar.name, NavBar);
    Vue.component(NavBarBackIcon.name, NavBarBackIcon);
    Vue.component(NavBarNextIcon.name, NavBarNextIcon);
    export  default{
        data(){
            return{
                rechargeLog:[]
            }
        },
        mounted:function(){
            var _that=this
            _that.category();

        },
        methods:{
            goback () {
                this.$router.go(-1)
            },
            category () {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                var url = "/tixianLog?token=" + token;
                _that.$axios.get(url).then(function(response){
                    _that.rechargeLog = response.data.tixianLog;

                })
            },
        },
        computed:mapState({
            user_data(state){
                if(weipai.isEmptyObject(state.menuItems)){
                    this.$store.dispatch("getuserinfo");
                }
                console.log(state.menuItems)
                return state.menuItems
            }
        }),
        filter:{
            time(value){
                return new Date(parseInt(value) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
            }
        }


    }
</script>