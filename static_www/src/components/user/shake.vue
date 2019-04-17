<template>
    <div>
        <!-- 红包音乐 -->
        <audio id="shakemusic" src="/static/music/red-01.mp3" style="display: none;"></audio>
        <audio id="openmusic" src="/static/music/red-02.mp3" style="display: none;"></audio>
        <!-- End 红包音乐 -->
        <!-- 红包 -->
        <div class="red_bg">
            <div class="red-ts"></div>
            <div class="red-ss-bg">
                <span id="result" class="red-ss animated"></span>
            </div>
            <div class="red-jh">
                您还有<span>{{fansilist.shark_count}}</span>次机会
            </div>
            <div class="red-tc">
                <!-- 已中奖 -->
                <div class="red-yzj">
                    <div class="red-tc-k">
                        <p><img style="width: 1.2rem; height: 1.2rem;" :src=user.head_pic></p>
                        {{user.nickname}}
                    </div>
                    <div class="red-tc-btn">
                        <button @click="close_red(user.user_id)">关注我</button>
                    </div>
                </div>
                <div style="margin-top: 2rem;" @click="close" ><img style="width:1.2rem; "  src="@/assets/images/shake/ronne.png"></div>

            </div>
        </div>
        <!-- End 红包 -->

    </div>
</template>
<script>
    import '../../assets/js/shake/js/zepto.min.js'
    import { mapState} from 'vuex';
    export default {
        name: "shake",
        data(){
            return{
                cont:0,
                user:[]
            }
        },
        computed:mapState({
            fansilist(state){
                if(this.$weipai.isEmptyObject(state.fansilist)){
                    this.$store.dispatch("getuserinfo");
                }
                return state.fansilist
            },

        }),
        mounted: function () {
            var that= this
            // that.showData()
            //运动事件监听
                if (window.DeviceMotionEvent) {
                    window.addEventListener('devicemotion', deviceMotionHandler, false);
                } else {
                    // alert('抱歉，你的手机配置实在有些过不去，考虑换个新的再来试试吧');
                    that.$dialog.alert({mes: '抱歉，你的手机配置实在有些过不去，考虑换个新的再来试试吧！'});
                }
            var SHAKE_THRESHOLD = 4000;
            var last_update = 0;
            var x, y, z, last_x = 0, last_y = 0, last_z = 0;
            function deviceMotionHandler(eventData) {
                var acceleration =eventData.accelerationIncludingGravity;
                var audio = document.getElementById("shakemusic");
                var openAudio = document.getElementById("openmusic");
                var curTime = new Date().getTime();
                if ((curTime-last_update)> 40) {
                    var diffTime = curTime -last_update;
                    last_update = curTime;
                    x = acceleration.x;
                    y = acceleration.y;
                    z = acceleration.z;
                    var speed = Math.abs(x +y + z - last_x - last_y - last_z) / diffTime * 10000;
                    if (speed > SHAKE_THRESHOLD) {
                        audio.play();
                        if(that.fansilist.shark_count<0){
                            that.$dialog.alert({mes: '您今天的次数用完了，明天再摇吧！'})
                            last_x = x;
                            last_y = y;
                            last_z = z;
                            return
                        }
                        $('.red-ss').addClass('wobble')
                        if( that.cont==0){that.showData()}
                        setTimeout(function(){
                            audio.pause();
                            openAudio.play();
                            $('.red-tc').css('display', 'block');
                        }, 1500);
                    }
                    last_x = x;
                    last_y = y;
                    last_z = z;
                }
            }
        },
        methods:{
            close(e){
                var _that =this;
                _that.cont=0
                $('.red-tc').css('display', 'none');
                document.getElementById("result").className = "red-ss animated";
            },
            close_red(e){
                var _that =this;
                var fanid = e;
                _that.cont=0;
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/users/create_care?fans_id=" + fanid+ "&sharked=1" + "&token=" + token ;
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        return res.data.data;
                    }
                }).then(function (res) {
                    _that.$dialog.alert({mes: '关注成功'});
                    $('.red-tc').css('display', 'none');
                    document.getElementById("result").className = "red-ss animated";

                }).catch(function (error) {
                    console.log(error)
                })

            },
            showData(){
                var _that =this;
                _that.cont=1
                var token = window.storeWithExpiration.get('token') || '';
                var url = "/users/shaked?token=" + token;
                _that.$axios.get(url).then(function (res) {
                    if (res.status == 200){
                        return res.data.data;
                    }
                }).then(function (res) {
                    _that.user =res

                    if(  _that.fansilist.shark_count<0){
                        fansilist.shark_count=0
                    }else {
                        _that.fansilist.shark_count--
                    }

                }).catch(function (error) {
                    console.log(error)
                })

            },
    },

    }
</script>

<style scoped>
    @import url(../../assets/css/shake/style.css);
    @import url(../../assets/css/shake/animate.css);
</style>