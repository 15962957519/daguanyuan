<template>
    <div id="number">
       <div class="fixednumMain" >
            <div class="tipBanner">
                <span class="title"></span>
                <div class="finish">完成</div>
            </div>
            <div class="numkey">
                <ul>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>1</div> <span></span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>2</div> <span>ABC</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>3</div> <span>DEF</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>4</div> <span>GHI</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>5</div> <span>JKL</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>6</div> <span>MNO</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>7</div> <span>PQRS</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>8</div> <span>TUV</span></li>
                    <li class="num" style="background-color: rgb(255, 255, 255);"><div>9</div> <span>WXYZ</span></li>
                    <li class="num othernum" style="background-color: rgb(209, 213, 218);">00</li>
                    <li class="num" style="line-height: 50px; background-color: rgb(255, 255, 255);">0</li>
                    <li class="delete" style="background-color: rgb(209, 213, 218);"></li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
    var config = require('../../../../config')

    export default {
        data(){
            return {

            }
        },
        mounted: function() {


            },
            methods:{



            },
            computed:{

            }
    }
</script>

<style scoped>
#number .fixednumMain {
    position: fixed;
    bottom: -410px;
    left: 0;
    right:0;
    margin-left:auto;
    margin-right:auto;
    z-index: 1199;
    background: #F0F0F0;
    width: 100%;
    max-width:640px;
    display: none;
}
#number .fixednumMain .tipBanner{
width:100%;
height:42px;
line-height:42px;
background:#F0F0F0;
border-top:1px solid #cacaca;
font-size:16px;

}
#number .fixednumMain .tipBanner span.title{
font-size:16px;
line-height:42px;
float:left;

min-width:65px;
text-indent:10px;
}
#number .fixednumMain .tipBanner .finish{
font-size:16px;
line-height:42px;
float:right;
min-width:60px;
color:#007aff;
}


.fixednumMask {
    position: fixed;
    width: 100%;
    opacity: 0;
    top: 0px;
    bottom: 0px;
    background-color: #000;
    display: none;
    z-index: 999;
}



.fixednumMain .tipBanner {
    height: 42px;
    width: 96%;
    padding: 0 2%;
    background: #F0F0F0;
    border-top: 1px solid #cacaca;
    font-size: 16px;
}

.fixednumMain .tipBanner span.title, .fixednumMain .priceBanner span.title {
    line-height: 42px;
    float: left;
    min-width: 65px
}

.fixednumMain .tipBanner .last, .fixednumMain .priceBanner .editTxt {
    float: left;
    width: 60%;
    height: 30px;
    line-height: 30px;
    margin: 6px 0;
    color: #7596D9;
    text-indent: 4px;
    overflow: hidden;
    font-size: 20px;
    font-family: "Helvetica neue", Verdana, Geneva, sans-serif;
}

.fixednumMain .tipBanner .close {
    float: right;
    width: 28px;
    height: 28px;
    margin: 7px;
    background-image: url(/static/img/close.png);
    background-repeat: no-repeat;
    background-position: center center;
    border-radius: 28px;
    background-color: #d43131;
    background-size: 14px;
    background-image: url(/static/img/close.png);
}

.fixednumMain .priceBanner {
    background: #fff;
    height: 42px;
    line-height: 42px;
    font-size: 16px;
    width: 96%;
    padding: 0 2%;
    position: relative;
}

.fixednumMain .priceBanner .editTxt .hover {
    border-right: 2px solid #fe0100;
}


.numInput .hover{
    border-right: 2px solid #fe0100;

}
.fixednumMain .priceBanner .tips {
    color: #999;
    font-size: 14px;
    margin-right: 10px;
    overflow: hidden;
    height: 42px;
    position: absolute;
    right: 0;
    top: 0
}

.fixednumMain .btnBanner {
    margin: 0 auto;
    width: 96%;
    display: table;
}

.fixednumMain .btnBanner .tip {
    padding: 0 2%;
    width: 96%;
    height: 20px;
    line-height: 20px;
    text-align: right;
}

.fixednumMain .btnBanner .confirmBtn {
    border-radius: 4px;
    background: #fe0100;
    height: 30px;
    line-height: 30px;
    text-align: center;
    font-size: 18px;
    color: #fff;
    margin: 10px 0;
}

.fixednumMain .btnBanner .fixedPrice {
    background: #fe0100;
    padding: 2.5% 0;
    border-radius: 4px;
    width: 66%;
    text-align: center;
    font-size: 18px;
    color: #fff;
    border: none;
    float: left;
    height: 24px;
    line-height: 24px;
}

.fixednumMain .btnBanner .fixedPrice {
    background: #01B7F0;
    width: 32%;
    float: left;
    margin-right: 2%;
    font-size: 14px;
    height: 24px;
    line-height: 24px;
}

.fixednumMain .btnBanner .confirmBtn:only-child {
    padding: 10px 0;
    width: 100%;
}

.fixednumMain .numkey {
    width: 100%;
    background: #fff;
    display: table;
}

.fixednumMain .numkey ul {
    padding: 0;
    margin: 0;
}

.fixednumMain .numkey ul, .fixednumMain .numkey li {
    text-decoration: none;
    list-style: none;
    vertical-align: middle;
}

.fixednumMain .numkey ul li {
    width: 33.1%;
    border-bottom: 1px solid #b3b3b3;
    border-right: 1px solid #b3b3b3;
    height: 50px;
    float: left;
    font-size: 28px;
    font-family: "Helvetica neue", Verdana, Geneva, sans-serif;
    text-align: center;
    background: #fff;
}

.fixednumMain .numkey ul li:nth-child(3n) {
    border-right: none;
}

.fixednumMain .numkey ul li.delete {
    background-image: url(../../../assets/img/backspace.png);
    background-repeat: no-repeat;
    background-position: center;
    background-size: 30px;
    background-color: #D1D5DA;
}

.fixednumMain .numkey ul li.othernum {
    background-color: #D1D5DA;
    line-height: 50px;
}

.fixednumMain .numkey ul li div {
    color: #000;
    line-height: 32px;
}

.fixednumMain .numkey ul li span {
    position: relative;
    font-size: 12px;
    top: -18px;
    color: #000;
}

</style>