<template>
    <div ref="wrapper">
     <div>
        <!--顶部提示信息-->
        <div class="top-tip"><span class="refresh-hook">下拉刷新</span></div>
        <slot></slot>
        <!--底部提示信息-->
        <div class="bottom-tip"><span class="loading-hook"></span></div>
        <div class="alert"><span class="alert-hook">刷新成功</span></div>
    </div>
    </div>
</template>
<style>
    .wrapper{
        position:relative;
        height: 100%;
    }
    .wrapper ul.content{

    }
    /* 下拉、上拉提示信息 */
    .top-tip{
        position: absolute;
        top: -40px;
        left: 0;
        z-index: 1;
        width: 100%;
        height:40px;
        line-height:40px;
        text-align:center;
        color: #555;
    }

    .bottom-tip{
        width: 100%;
        height: 35px;
        line-height: 35px;
        text-align: center;
        color: #777;
        background: #f2f2f2;
    }

    /* 全局提示信息 */
    .alert{
        display: none;
        position: fixed;
        top: 55px;
        left: 0;
        z-index: 2;
        width: 100%;
        height: 35px;
        line-height: 35px;
        text-align: center;
        color: #fff;
        font-size: 12px;
        background: rgba(7, 17, 27, 0.7);
    }
</style>
<script type="text/ecmascript-6">
    import BScroll from 'better-scroll'
    export default {
        name: 'scroll',
        // 父子组件
        props: {
            probeType: {
                type: Number,
                default: 1
            },
            click: {
                type: Boolean,
                default: true
            },
            data: {
                type: Array,
                default: null
            },
            /**
             * 是否派发顶部下拉的事件，用于下拉刷新
             */
            pulldown: {
                type: Boolean,
                default: false
            },
        },
        created() {
            setTimeout(() => {
                this._initScroll()
        }, 20)
        },
        methods: {
            refreshalert(alert,text){
                text = text|| "操作成功";
                alert.innerHtml =text;
                alert.style.dislpay ="block";
                setTimeout(function(){
                    alert.style.dislpay ="none";
                },1000);
            },
            // 初始化
            _initScroll() {
                var _that = this;
                this.$nextTick(() => {
                    if (!this.$refs.wrapper) {
                        return
                    }

                  var   alert = document.querySelector('.alert-hook'),
                        topTip = document.querySelector('.refresh-hook'),
                        bottomTip = document.querySelector('.loading-hook');
                    if (!this.scroll) {
                        this.scroll = new BScroll(this.$refs.wrapper, {
                            probeType: this.probeType,
                            click: this.click,
                            scrollX: this.scrollX
                        })

                        // 滑动中
                        this.scroll.on('scroll', function (position) {

                            if(position.y > 30) {
                                topTip.innerText = '释放立即刷新';
                            }
                        });


                        //下拉
                        this.scroll.on('touchEnd', (pos) => {
                            // 下拉动作
                            if (pos.y > 50) {
                                // 恢复文本值
                                topTip.innerText = '下拉刷新';
                                _that.$emit('refreshpage');
                                _that.$emit('pulldown')
                                // 刷新成功后的提示
                                _that.refreshalert(alert,'刷新成功');
                            }else if(pos.y < (this.scroll.maxScrollY - 30)){
                                bottomTip.innerText = '加载中...';
                                     }
                                })
                        //上拉
                        if( this.pulldown){
                            this.scroll.on('scrollEnd', () => {
                                if (this.scroll.y <= (this.scroll.maxScrollY + 50)) {
                                    this.$emit('uppush')
                                }
                            });
                        }
                    }else{
                        this.refresh();
                    }
                });
            },
            enable() {
                this.scroll && this.scroll.enable()
            },
            refresh() {
                this.scroll && this.scroll.refresh()
            }
        },
        watch: {
            data() {
                // 监听data数组是否有新数据传入
                setTimeout(() => {
                    this.refresh()
            }, 20)
            }
        }
    }
</script>