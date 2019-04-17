<template>
    <div>
    <!--顶部导航-->
    <yd-navbar bgcolor="#af773e" :fixed="true" >
        <router-link   to="" slot="left" >
            <yd-navbar-back-icon  color="#fff"   @click.native="goback"><span style="color: #fff;">返回</span></yd-navbar-back-icon>
        </router-link>
        <p style="font-size: .3rem;color: #fff;" slot="center">我的钱包</p>
        <img slot="right" style="width: .5rem" src="@/assets/images/user/different2.png"/>
    </yd-navbar>
        <!--空隙-->
        <div class="black"></div>
    <!--余额和保证金-->
    <div class="bond_money" >
        <div class="bond_money_one">
         <ul>
             <li style="border-right: 1px solid #eee;">余额：{{user_data.user_money}}</li>
             <li style="font-size: .2rem; line-height: .45rem;">冻结金额：{{user_data.frozen_money }}
                 <br>可用金额：{{user_data.user_money -user_data.frozen_money }}
               </li>
         </ul>
        </div>
    </div>
    <!--余额和保证金结束-->
        <!--菜单-->
        <div class="bond_muse">
            <ul>
                <li v-on:click="recharge()"><img src="@/assets/images/user/mone_bag/money.png">
                <p>充值</p>
                </li>
                <li v-on:click="rechargelog()"><img src="@/assets/images/user/mone_bag/money.png">
                    <p>充值记录</p>
                </li>
                <li v-on:click="withdrawlas()"><img src="@/assets/images/user/mone_bag/dfm.png">
                    <p>提现</p>
                </li>
                <li v-on:click="withdrawlaslog()"><img src="@/assets/images/user/mone_bag/dgjh.png">
                    <p>提现记录</p>
                </li>
                <li v-on:click="paypoints()"><img src="@/assets/images/user/mone_bag/tyjj.png">
                    <p>积分明细</p>
                </li>
                <li v-on:click="accountlog()"><img src="@/assets/images/user/mone_bag/jgjgj.png">
                    <p>账单明细</p>
                </li>
            </ul>
        </div>
        <!--菜单结束-->
    </div>
</template>
<style>
    .navbar-bottom-line-color:after {border-color: #af773e !important;}
    /*保证金与余额*/
    .bond_money{ width: 100%; height: 2.2rem; background:#af773e;padding: .5rem;  }
    .bond_money_one{ width: 100%; height: 1.5rem; background: #fff; margin: 0rem auto; padding: .3rem; }
    .bond_money_one ul li {  float: left; width: 50%; height: 1rem; text-align: left;padding-left: .2rem; font-size: .3rem; line-height: .8rem;}
     .black{width: 100%; height: .8rem; background:#af773e }
     /*菜单*/
     .bond_muse{ width: 100%; height: 3rem; background: #fff; margin-top: -.4rem; box-shadow: 3px 0px 15px #999; border-radius: .3rem .3rem 0 0}
     .bond_muse ul li{ width: 33.3%; float: left; margin-top: .25rem; }
     .bond_muse ul li img{ width: .7rem;}
</style>
<script>
  import Vue from 'vue';
  import {mapState} from 'vuex';
    import {NavBar, NavBarBackIcon, NavBarNextIcon} from 'vue-ydui/dist/lib.px/navbar';
  Vue.component(NavBar.name, NavBar);
  Vue.component(NavBarBackIcon.name, NavBarBackIcon);
  Vue.component(NavBarNextIcon.name, NavBarNextIcon);

  export  default{
    data(){
      return{}
    },
    methods:{
      goback () {
        this.$router.go(-1)
      },
      recharge () {
         this.$router.push({name: 'recharge_link'})
      },
        rechargelog () {
            this.$router.push({name: 'rechargelog_link'})
        },
        withdrawlas () {
            this.$router.push({name: 'withdrawals_link'})
        },
        withdrawlaslog () {
            this.$router.push({name: 'withdrawalslog_link'})
        },
        paypoints () {
            this.$router.push({name: 'paypoints_link'})
        },
        accountlog () {
            this.$router.push({name: 'accountlog_link'})
        },

    },

    computed:mapState({
         user_data(state){
            if(this.$weipai.isEmptyObject(state.menuItems)){
                this.$store.dispatch("getuserinfo");
            }
            return state.menuItems
         }
    }),
      filters:{
          number1(val){
              val = val || '';
              if(val!=undefined){
                  return parseFloat(val).toFixed(2)
              }else{
                  return val;
              }

           }
      }
  }
</script>