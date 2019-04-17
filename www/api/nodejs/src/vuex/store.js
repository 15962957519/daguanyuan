// var Vuex = require('vuex'); // get vuex
//
//Vue.use(Vuex)
import {MessageBox,Indicator,Toast} from 'mint-ui';
var state = {
    cardData: [],
    page: 1,
    isloadingComplete: false,
    busy: false,
    isShow: false,
    addheight: 0,
    WEAPPNAME: "天宝微拍",
    WEAPPNAME_SUPPORT: "天宝微拍提供技术支持",
    localIds: [],
    localIds_product: [],
    localIds_php: [],
    my_lists: [],
    endText:'',
    like_products:[],
    focuslists:[],
    messagelist:[]
};

var getters = {}
import {userLikeProduct_function,isEmptyObject} from "../assets/js/common_function.js"
var mutations = {
    updateLoadingState(state, data){
        state.isloadingComplete = data;
    },
    commonadddata(state, data){
        if( !isEmptyObject(data.data)){
            var name=data.name;
            var  tmpaa =state[name];
            var  tmpa =data.data;
            for(var i=0;i<tmpa.length;i++){
                tmpaa.push(tmpa[i]);
            }
        }
    },
    unupfoucs(state, data){
        if( !isEmptyObject(data.data)){
            var  tmpaa =state.focuslists;
            var  tmpa =data.data;
            for(var i=0;i<tmpa.length;i++){
                tmpaa.push(tmpa[i]);
            }
        }
    }  ,
    upfoucs(state, data){
        if(state.focuslists.length>0 && !isEmptyObject(data.data)){
            var  tmpa =state.focuslists;
            for(var i=0;i<tmpa.length;i++){
                if( data.u_id>0 && tmpa[i].fans_id>0  && data.u_id ==tmpa[i].fans_id){
                    tmpa[i].t2fid =tmpa[i].user_id;
                    break;
                }
            }
            state.focuslists =tmpa;
        }else{
            state.focuslists =data.data;
        }
    },
    unupfoucsdel(state, data){
        if(state.focuslists.length>0 && !isEmptyObject(data.data)){
            var datauinfo=data.data;
            var  tmpa =state.focuslists;
            for(var i=0;i<tmpa.length;i++){
                if( data.u_id>0 && tmpa[i].user_id>0  && data.u_id ==tmpa[i].user_id){
                    tmpa.splice(i,1);
                    break;
                }
            }
        }
    },
    updateBusyState(state, data){
        state.busy = data;
    },
    addData(state, data){
        if (state.page == 1) {
            state.cardData = [];
        }
        for(var i=0;i<data.length;i++){
            state.cardData.push(data[i]);
        }
    },
    addDataOnlyReplacebidlists(state, data){
        if(!isEmptyObject(data.plists)){
            //添加数据
                var tmp = state.cardData;
                //引用传递
                for(var i=0;i<tmp.length;i++){
                        var row =tmp[i];
                        if(row.goods_id==data.goods_id){
                            tmp[i].bidlists =   tmp[i].bidlists.concat(data.plists);
                            tmp[i].pagenow++;
                        }
                }
        }
    },
    divbidlistsxxe(state, data){
        if(!isEmptyObject(data.plists)){
            //添加数据
                var tmp = state.cardData;
                //引用传递
                for(var i=0;i<tmp.length;i++){
                        var row =tmp[i];
                        if(row.goods_id==data.goods_id){
                            tmp[i].bidlists =  data.plists;
                            tmp[i].pagenow=1;
                        }
                }
        }
    },
    refreshData(state, data){
        state.cardData = data;
    },
    isShowAlert(state, data){
        state.isShow = data;
    },
    uploadimages(state, data){
        state.localIds = data;
    },
    localids_product(state, data){
        state.localIds_product = data;
    },
    uploadimages_server(state, data){
        state.localIds_php = data;
    },
    my_lists_function(state, data){
        state.my_lists =data;
    },
    pageadd(state, page){
        state.page ++;
    },
    like_product(state, data){
        state.like_products =data;
    } ,
    like_product_update(state, data,good_id){
    var good_id=data.good_id;
        if(state.cardData.length>0 && !isEmptyObject(data.data)){
            var  tmpa =state.cardData;

            for(var i=0;i<tmpa.length;i++){
                if( good_id>0 && tmpa[i].goods_id>0  && good_id ==tmpa[i].goods_id){
                    tmpa[i].collectdata =data.data;


                    tmpa[i].click_count =data.click_count;
                    tmpa[i].likecount =data.likecount;
                }
            }
            state.cardData =tmpa;
        }
    }
};

var config = require('../../config')
var actions = {
    actions_userLikeProduct_function(context, data){
        var good_id =data.good_id;
        var token = window.storeWithExpiration.get('token');
        var el =data.e;
        var dataupdate=[];
        var toast =data.toast;
        var _that=this;
        var data=[];
        axios.get( '/user/userlikeproduct', {
            params: {
                token: storeWithExpiration.get('token'),
                goods_id: good_id
            }
        }).then(function(response) {
                if(response.status=='200'){
                    return response.data;
                }
            }).then(function(json) {
            var message ='喜欢成功';
            $(el.target).css('background-position','right 4px');
            if(4000==json.code){
                message='已经喜欢';
            }else if(2000==json.code){
                context.commit('like_product_update',{data:json.data.data,good_id:good_id,click_count:json.click_count,likecount:json.likecount});
            }
            toast({
                message:message,
                iconClass: 'mintui mintui-success'
            });

        }).catch(function(ex) {
            toast({
                message: '喜欢失败',
                iconClass: 'mintui mintui-success'
            });
        });

    },
    actions_userFocus_function(context, data){
        var u_id =data.u_id;
        var token = window.storeWithExpiration.get('token');
        var el =data.e;
        var dataupdate=[];
        var toast =data.toast;
        var _that=this;
        var data=[];
            axios.get( '/user/userfoucs', {
                params: {
                    token: token,
                    u_id: u_id
                }
            }).then(function(response) {
                if(response.status=='200'){
                    return response.data;
                }
            }).then(function(json) {
                var message ='关注成功';
                if(4000==json.code){
                    message='已经关注';
                }else if(2000==json.code){
                    context.commit('upfoucs', json);
                }
                toast({
                    message:message,
                    iconClass: 'mintui mintui-success'
                });

            }).catch(function(ex) {
                toast({
                    message: '关注失败',
                    iconClass: 'mintui mintui-success'
                });
            });
    },
    userfoucsfansan(context, data){
        var u_id =data.u_id;
        var token = window.storeWithExpiration.get('token');
        var el =data.e;
        var dataupdate=[];
        var toast =data.toast;
        var _that=this;
        var data=[];
            axios.get( '/user/userfoucsfansan', {
                params: {
                    token: token,
                    u_id: u_id
                }
            }).then(function(response) {
                if(response.status=='200'){
                    return response.data;
                }
            }).then(function(json) {
                var message ='吸粉成功';
                if(4000==json.code){
                    message='已经是粉丝';
                }else if(2000==json.code){
                    context.commit('upfoucs', json);
                }
                toast({
                    message:message,
                    iconClass: 'mintui mintui-success'
                });

            }).catch(function(ex) {
                toast({
                    message: '吸粉失败',
                    iconClass: 'mintui mintui-success'
                });
            });
    },
    actions_unuserFocus_function(context, data){
        var u_id =data.u_id;
        var token = window.storeWithExpiration.get('token');
        var el =data.e;
        var dataupdate=[];
        var toast =data.toast;
        var _that=this;
        var data=[];
            axios.get( '/user/unuserfoucs', {
                params: {
                    token: token,
                    u_id: u_id
                }
            }).then(function(response) {
                if(response.status=='200'){
                    return response.data;
                }
            }).then(function(json) {
                var message ='取消关注成功';
                if(4000==json.code){
                    message='取消关注失败';
                }else if(2000==json.code){
                    context.commit('unupfoucsdel', {u_id:u_id,data:json.data});
                }
                toast({
                    message:message,
                    iconClass: 'mintui mintui-success'
                });
            }).catch(function(ex) {
                toast({
                    message: '取消关注失败',
                    iconClass: 'mintui mintui-success'
                });
            });
    },
    localids_product_function(context, data){
        context.commit('localids_product', data);
    },
    my_lists_function_action(context, data){
        context.commit('my_lists_function', data);
    },
    uploadimages_server(context, data){
        context.commit('uploadimages_server', data);
    },
    uploadimages(context, data){
        context.commit('uploadimages', data);
    },
    getData(context, object){
        var progress = object.progress;
        var isRefresh = object.refresh;
        var page = object.page;
        //进度条对象
        progress.$Progress.start();
        context.commit('updateLoadingState', false);
        context.commit('updateBusyState', true);
        context.commit('pageadd',true);
     fetch('/product/indexpaimaiprolist?page='+page,
            {
                method: 'GET'
            })
            .then(function (response) {
                if (response.ok) {
                    return response.json();
                }
            }).then(function (json) {
            return json.plists.data;
        }).then(function(json){
            context.commit('updateLoadingState', true);
            context.commit('updateBusyState', false);
            if (isRefresh === true) {
                context.commit('refreshData', json);
            } else {
                context.commit('addData', json);
            }
            progress.$Progress.finish();
        }).catch(function (ex) {
            console.log(ex);
            context.commit('updateBusyState', false);
            progress.$Progress.fail();
        });
    },
    getDataforindex(context, object){
        var progress = object.progress;
        var isRefresh = object.refresh;
        var usl = object.usl;
        //进度条对象
        progress.$Progress.start();
        context.commit('updateLoadingState', false);
        context.commit('updateBusyState', true);
        context.commit('pageadd',true);

        axios.get(usl)
            .then(function (response) {
                if(response.status==200){
                    return response.data.plists;
                }
            }).then(function(response){
                return  response.data;
        }).then(function(json){
            context.commit('updateLoadingState', true);
            context.commit('updateBusyState', false);
            if (isRefresh === true) {
                context.commit('refreshData', json);
            } else {
                progress.toast.close();
                if(!isEmptyObject(json)){
                    Toast({
                        message: '加载完成',
                        position: 'bottom',
                        duration: 1000
                    });
                    context.commit('addData', json);
                }else{
                    Toast({
                        message: '没有更多数据了',
                        position: 'bottom',
                        duration: 1000
                    });
                    progress.$store.state.busy=true
                }
            }
            progress.$Progress.finish();
        }) .catch(function (error) {
            context.commit('updateBusyState', false);
        })
    },
    getDataforindexbondlist(context, object){
        var progress = object.progress;
        var isRefresh = object.refresh;
        var e = object.e;
        var page = object.page.toString();
        var goods_id = object.goods_id ||0;
        //进度条对象
        context.commit('updateLoadingState', false);
        context.commit('updateBusyState', true);
        context.commit('pageadd',true);
        if(goods_id>0){
            var usl=  '/product/getbondlists?page='+page+'&goods_id='+goods_id;
        }
        progress.$Progress.start();
        axios.get(usl)
            .then(function (response) {
                if(response.status==200){
                    return response.data;
                }
            }).then(function(response){
            return  response.data;
        }).then(function(json){
            context.commit('updateLoadingState', true);
            context.commit('updateBusyState', false);
            if (isRefresh === true) {
                context.commit('refreshData', json);
            }
            else {
                if(!isEmptyObject(json.plists)){

                    if(page==='1'){

                        context.commit("divbidlistsxxe",json);
                        if(1==json.surplus) {
                            $(e.target).parent().find('.bidthan').show();
                            $(e.target).parent().find('.bidless').hide();
                        }
                    }else{
                        context.commit('addDataOnlyReplacebidlists',json);
                    }
                    //提示没有更多数据
                    if(0==json.surplus){
                        //显示收起
                      $(e.target).parent().find('.bidthan').hide();
                      $(e.target).parent().find('.bidless').show();
                    }
                }else{
                    //提示没有更多数据
                    if(0==json.surplus){
                        //显示收起
                    }else{
                        $(e.target).parent().find('.bidthan').show();
                        $(e.target).parent().find('.bidless').hide();

                    }
                }
            }
            progress.$Progress.finish();
        }) .catch(function (error) {
            context.commit('updateBusyState', false);
        })
    }
};

var moduleCard = {
    state: state,
    getters: getters,
    mutations: mutations,
    actions: actions
};

var store = new Vuex.Store({
    state: state,
    getters: getters,
    mutations: mutations,
    actions: actions
});

module.exports = store;

// export default new Vuex.Store({
//   modules: {
//     moduleCard: moduleCard
//   }
// });
