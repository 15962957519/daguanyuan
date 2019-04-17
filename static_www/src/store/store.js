import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

const  store = new Vuex.Store({
     state:{
         menuItems:[],
         tempuserinfo:[],
         histroy:[],
         staff_mobile:"",
         mobilecheckobj:{},
         bargaininglistflag:true,
         bargaininglist:[],//议价信息
         fansilist:[],//粉丝
         card:{},
      },
    getters:{

    },
    mutations:{
        setMenuItems(state,data){
            state.menuItems = data
            //console.log(data)
        }, setfansilist(state,data){
            state.fansilist = data
            //console.log(data)
        }, card(state,data){
            state.card = data
            console.log(data)
        },setdata(state,data,name){
            state[name] = data
        }
    },
    actions:{
            getuserinfo(){
                var token = window.storeWithExpiration.get('token');
                axios.get( '/users/index', {
                    params: {
                        token: token
                    }
                }).then(function(response) {
                    if(response.status=='200'){
                        return response.data;
                    }
                }).then(function(json) {
                    store.state.menuItems = json.data.user_data;
                    store.state.fansilist = json.data;
                }).catch(function(ex) {

                });
            },
          getbargaininglist(context,data){
                //获取该用户所有议价信息
            var token = window.storeWithExpiration.get('token');
              store.state.bargaininglistflag  &&   axios.get( '/product/getbondlists', {
                params: {
                    token: token
                }
            }).then(function(response) {
                if(response.status=='200'){
                    return response.data;
                }
            }).then(function(json) {
                store.state.bargaininglist = json.data.plists;

            }).catch(function(ex) {

            });
        }
    }
})
export default store;