<template>
<div id="main_box">
    <div id="top">
        <div id="hand" class="hand"><img src="/static/img/shaked/hand2.png"></div>
        <div id="loading" class="loading"><span class="icon"></span><span class="txt">正在努力的加载结果，请稍候~</span></div>
        <div id="result" class="result">
          <div class="con">
            <div class="imgLeft"><img v-lazy="datas.head_pic"></div>
            <div class="contRight">
              <p class="mainTitle">
              <span id="user_name">{{datas.nickname}}</span>

              <span v-if="datas.timelevel <= '1'">
                            <span id="level" class="icon iconfont level salelevel">V1</span>
                            </span>

                            <span v-else-if="datas.timelevel <= '2'">
                                 <span id="level" class="icon iconfont level salelevel">V2</span>
                            </span>
                            <span v-else-if="datas.timelevel <= '3'">
                                   <span id="level" class="icon iconfont level salelevel">V3</span>
                            </span>
                            <span v-else-if="datas.timelevel<= '4'">
                                 <span id="level" class="icon iconfont level salelevel">V4</span>
                            </span>
                            <span v-else-if="datas.timelevel <= '5'">
                                  <span id="level" class="icon iconfont level salelevel">V5</span>
                            </span>
                            <span v-else-if="datas.timelevel <= '6'">
                                  <span id="level" class="icon iconfont level salelevel">V6</span>
                            </span>
                            <span v-else-if="datas.timelevel <= '7'">
                                  <span id="level" class="icon iconfont level salelevel">V7</span>
                            </span>
                            <span v-else>
                                    <span id="level" class="icon iconfont level salelevel">V1</span>
                            </span>
                    </span>
              </p>
              <p class="subTitle"><span class="attention focus"  @click="userFocus(datas.user_id,$event)" >添加</span></p>
            </div>
          </div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="bottom">
        <center><div id="shaked_num">今日剩余 {{datas.remaintimes}} 次</div></center>
        <center><div class="change_one"><button id="buttom_d" v-on:click="getProduction">换一个</button></div></center>
    </div>
</div>
</template>

<script>
    import {Toast} from 'mint-ui';

    var SHAKE_THRESHOLD = 2500;
    var last_update = 0;
    //var x = y = z = last_x = last_y = last_z = 0;

    var x = 0;
    var y = 0;
    var z = 0;
    var last_x = 0;
    var last_y = 0;
    var last_z = 0;
    var times=0;
    var count = 0;
    var audio_shake;

    module.exports = {
        data() {
                return {
                   datas:{},
                   imgs:{},
                }
        },

         mounted: function() {
                document.title = "摇粉丝";

                audio_shake =  document.createElement("audio");
                audio_shake.src='/static/resource/shakes.mp3';

                if (window.DeviceMotionEvent) {
                    window.addEventListener('devicemotion', this.deviceMotionHandler, false);
                } else {
                    alert('抱歉，你的手机配置实在有些过不去，考虑换个新的再来试试吧');
                }



         },

         methods:{
            deviceMotionHandler:function(eventData){
                var acceleration = eventData.accelerationIncludingGravity;
                var curTime = new Date().getTime();

                if ((curTime - last_update) > 100) {
                    var diffTime = curTime - last_update;
                    last_update = curTime;
                    x = acceleration.x;
                    y = acceleration.y;
                    z = acceleration.z;
                    var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;
                    var status = document.getElementById("status");

                    if (speed > SHAKE_THRESHOLD) {
                        $("#top").css('height','70%');
                        $("#bottom").css('height','30%');
                        document.getElementById("loading").className = "loading loading-show";
                        document.getElementById("hand").className = "hand hand-animate";
                        count++;
                        if(count == 1){
                            this.doResult();
                            this.getProduction();
                        }

                        times++;
                        $("#buttom_d,#shaked_num").css('display','none');
                    }

                    last_x = x;
                    last_y = y;
                    last_z = z;
                }
            },

            autoPlay:function(){
                 var index = 0;

                if (navigator.vibrate) {
                    navigator.vibrate(2000);
                } else if (navigator.webkitVibrate) {
                    navigator.webkitVibrate(2000);
                }
                 audio_shake.play();
                 return false;

            },

            doResult:function() {
            		if(times>0){
            			return false;
            		}

            		this.autoPlay();
                    document.getElementById("result").className = "result";

            		 setTimeout(function(){
            			var audio = document.createElement("audio");
                        var index = 0;
                        audio.src = "/static/resource/skresult.mp3";
                        audio.play();
            		 }, 2000);
             },

             getProduction:function(){
                var	that =this;
                axios.get( '/product/getproductrandone', {
                    params: {
                        token: storeWithExpiration.get('token')
                    }
                }).then(function(response) {
                    if(response.status=='200'){
                         that.datas = response.data.plists[0];
                         that.imgs = response.data.plists[0].img[0];

                         var num = response.data.plists[0].remaintimes;
                         num = num < 0 ? 0 : num;

                         if(num <= 0){
                              $('#shaked_num').html('今日剩余' + num + '次');
                              count=0;
                              times=0;
                              $('#result').removeClass('result-show');
                              setTimeout(function(){
                                    //$('#shaked_num').html('对不起，您今天得摇一摇次数已经用完！');
                                    document.getElementById("loading").className = "loading";
                                    document.getElementById("hand").className = "hand";
                                    $("#buttom_d,#shaked_num").css('display','block');
                                    return false;
                              }, 2000);
                         }else{

                            if(num == 1){
                                $('#shaked_num').html('今日剩余' + 0 + '次');
                            }else{
                                num = num-1;
                                $('#shaked_num').html('今日剩余' + num + '次');
                            }
                            setTimeout(function(){
                                times=0;
                                count=0;
                                document.getElementById("loading").className = "loading";
                                document.getElementById("hand").className = "hand";
                                document.getElementById("result").className = "result result-show";
                                $("#buttom_d,#shaked_num").css('display','block');
                            }, 2000);
                         }
                    }
                 }).catch(function(ex) {
                     console.log(ex);
                 });
            },

            userFocus:function(u_id,e){
                  var _that=this;
                  _that.$store.dispatch('userfoucsfansan', {
                     u_id: u_id,
                     e: e,
                     toast:Toast
                  });
            },
         }
    }

    </script>

<style scoped>
 @import '../assets/css/shaked.css';
</style>