<template>
    <div>
        <yd-tab active-color="#af773e" v-model="tab2" :callback="fn" :prevent-default="false" :item-click="itemClick">
            <yd-tab-panel v-for="(item,oa) in items.new_cates" :label="item.cat_name" :key="oa">
                <template v-for="list in items.newlist">
                    <template v-if="item.cat_id==list.cat_id" >
                        <div class="new_list_first" @click="new_result(list.article_id)">
                            <div class="new_list_first_l">
                                <dl>
                                    <dt>{{list.title}}</dt>
                                    <dd v-html="list.description"></dd>
                                </dl>
                                <div class="new_list_first_bottom">
                                    <ul>
                                        <li>{{list.add_time|date}}</li>
                                        <li>来源：{{list.author}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="new_list_first_r">
                                <img :src="list.thumb"></img>
                            </div>
                        </div>
                    </template>
                </template>
            </yd-tab-panel>
        </yd-tab>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                tab2: 0,
                items: {
                    new_cates:[],
                    newlist:[]
                }
            }
        },
        mounted: function () {
            var that= this
            that.showData()
            },
        methods: {
            showData(){
                var _that =this;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/news_lists?token=" + token;
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        return res.data.data;
                    }
                }).then(function (res) {
                    console.log(555,res.new_cates)
                    _that.items=res
                }).catch(function (error) {
                    console.log(error)
                })
            },
            fn(label, key) {
                console.log(label, key);
            },
            itemClick(key) {
                var _that = this;
                var token = window.storeWithExpiration.get('token');
                _that.items.newlist = [];
                var key = key || 0;
                _that.$dialog.loading.open('数据加载中');
                setTimeout(() => {
                    _that.tab2 = key;
                    _that.$dialog.loading.close();
                    if (key == 0){
                        var url ='/news_lists?token=' + token +'&cat_id=2'
                    }
                    if (key == 1){//待付款
                        var url ='/news_lists?token=' + token +'&cat_id=3'
                    }else if (key == 2){ //待发货
                        var url ='/news_lists?token=' + token +'&cat_id=4'
                    }else if (key == 3){ //待收货
                         var url ='/news_lists?token=' + token +'&cat_id=5'
                    }

                    _that.$axios.get(url).then(function (res) {
                        if (res.status == 200){
                            return res.data.data;
                        }

                    }).then(function (res) {
                        console.log(res.newlist)

                             _that.items.newlist=res.newlist


                    }).catch(function (error) {
                        console.log(error)
                    })

                }, 500);
                return false;

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
        },
        filters:{
            date(time){
                let oldDate = new Date(time*1000)
                let newDate = new Date()
                var dayNum = "";
                var getTime = (newDate.getTime() - oldDate.getTime())/1000;
                if(getTime < 60*5){
                    dayNum = "刚刚";
                }else if(getTime >= 60*5 && getTime < 60*60){
                    dayNum = parseInt(getTime / 60) + "分钟前";
                }else if(getTime >= 3600 && getTime < 3600*24){
                    dayNum = parseInt(getTime / 3600) + "小时前";
                }else if(getTime >= 3600 * 24 && getTime < 3600 * 24 * 30){
                    dayNum = parseInt(getTime / 3600 / 24 ) + "天前";
                }else if(getTime >= 3600 * 24 * 30 && getTime < 3600 * 24 * 30 * 12){
                    dayNum = parseInt(getTime / 3600 / 24 / 30 ) + "个月前";
                }else if(time >= 3600 * 24 * 30 * 12){
                    dayNum = parseInt(getTime / 3600 / 24 / 30 / 12 ) + "年前";
                }
                let year   = oldDate.getFullYear();
                let month  = oldDate.getMonth()+1;
                let day    = oldDate.getDate();
                let hour   = oldDate.getHours();
                let minute = oldDate.getMinutes();
                let second = oldDate.getSeconds();
                return dayNum+" "+month+"-"+day+" "+hour+":"+minute
            }
        }
    }
</script>

<style scoped>
 .new_list_first{ width: 100%; height: 3rem; border-bottom: .2rem solid #eee;}
 .new_list_first_l{ width: 60%;float: left;  height: 2.8rem; overflow: hidden; padding: .15rem; position: relative;}
 .new_list_first_l dl dt{ font-weight: bold; text-align: left; max-height: .89rem; overflow: hidden; line-height: .45rem; font-size: .3rem; color: #333; }
 .new_list_first_l dl dd{ color: #666; text-align: left; line-height: .3rem;  max-height:1rem; overflow: hidden;}
 .new_list_first_r{ width: 40%;float: left; height: 2.8rem; overflow: hidden; padding-top: .25rem }
 .new_list_first_r img{ width: 85%;  height: 2.3rem; box-shadow: 1px 2px 3px #666;}
 .new_list_first_bottom{ position: absolute; height: .35rem;width: 100%; bottom:.1rem;}
 .new_list_first_bottom ul li {  float: left; text-align: left; color: #999; width: 50%;}

</style>