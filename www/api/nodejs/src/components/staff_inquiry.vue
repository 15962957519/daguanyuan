<style>
  #tb{
    height:100%;
    max-width:640px;
    margin:0 auto;
    background: #FFFFFF;
    overflow: auto;
    font-family:'微软雅黑';

  }
  .tianbao{ padding:40px;
    font-size:22px;
    color:#F00;
    font-weight:100;
  }
  .yangshi1{
    padding:30px 40px;
    font-size: 17px;
  }
  .yangshi2{
    font-size:19px;
    padding-left:40px;
  }
  .input{
    width:200px;
    height:35px;
    margin-top:17px;
    text-indent:12px;
  }
  #buttom{
    margin-top:30px;
    color: #FFF;
    background:#F00;
    border:0px;
    font-size:16px;
    border-radius:8px;
    width:70px;
    height:35px;
  }
  #cxjg{
  background-color: #09c8f6;
  height: auto;
  width:300px;
  display: none;
  border-radius: 10px;
  margin-top: 10px;
  padding-top: 15px;
  padding-left:10px;
  line-height: 40px;
  }
</style>
<template>
  <div id="tb">
    <div class="tianbao"><strong>天宝微拍官方员工查询</strong></div>
    <div class="yangshi1">温馨提示：由于平台影响力较大，如果声称有天宝微拍官方工作人员与您联系，可在此页面查询验证。</div>
    <div class="yangshi2">输入手机号核实查询：<br />
      <div><input class="input" type="text" id="keyword" name="username" placeholder="请输入手机号码" /></div>
      <button id="buttom" @click="refer">点击查询</button>
      <div id="cxjg"></div>
    </div>
  </div>
</template>
<script>
  var data=[
    {"name":"天宝微拍高级客服顾问-关老师","job":"0282","phone":"17717853569"},
    {"name":"天宝微拍高级客服顾问-关老师","job":"0282","phone":"18051891709"},
    {"name":"天宝微拍高级客服顾问-关老师","job":"0253","phone":"18913216176"},
    {"name":"天宝微拍-陈老师","job":"0911","phone":"17710279661"},
    {"name":"天宝微拍-陈老师","job":"0911","phone":"17376588817"},
    {"name":"天宝微拍-谷老师","job":"0386","phone":"13052272369"},
    {"name":"天宝微拍高级分析师-王老师","job":"0126","phone":"13022106763"},
    {"name":"天宝微拍资深顾问-张洁","job":"0198","phone":"17718371605"},
    {"name":"天宝微拍资深顾问-张洁","job":"0198","phone":"18721168680"},
    {"name":"天宝微拍资深顾问-吕莉","job":"0218","phone":"17031270055"},
    {"name":"天宝微拍资深顾问-吕莉","job":"0218","phone":"18913219830"},
    {"name":"天宝微拍资深顾问-吕幂","job":"0291","phone":"18913225230"},
    {"name":"天宝微拍资深顾问-袁老师","job":"0263","phone":"18217036238"},
    {"name":"天宝微拍资深顾问-袁老师","job":"0263","phone":"18913247523"},
    {"name":"天宝微拍高级分析师-李治兴","job":"0185","phone":"13370144856"},
    {"name":"天宝微拍高级分析师-李治兴","job":"0185","phone":"18913284964"},
    {"name":"天宝微拍资深顾问-姜老师","job":"0169","phone":"13671667869"},
    {"name":"天宝微拍资深顾问-范苗苗","job":"0206","phone":"18516155224"},
    {"name":"天宝微拍资深顾问-范苗苗","job":"0206","phone":"17130040966"},
    {"name":"天宝微拍资深顾问-陈婠婠","job":"0196","phone":"17130049268"},
    {"name":"天宝微拍高级客服顾问-徐老师","job":"0233","phone":"15900822958"},
    {"name":"天宝微拍高级分析师-张老师","job":"0152","phone":"18516183546"},
    {"name":"天宝微拍高级分析师-张老师","job":"0152","phone":"17605812963"},
    {"name":"天宝微拍高级顾问-晏老师","job":"0316","phone":"17621447553"},
    {"name":"天宝微拍高级分析师-古老师","job":"0155","phone":"17701139183"},
    {"name":"天宝微拍高级客服顾问-李婷","job":"0362","phone":"15618088718"},
    {"name":"天宝微拍资深顾问-姜老师","job":"0169","phone":"18072766059"},
    {"name":"天宝微拍-张老师","job":"0152","phone":"18913213860"},
    {"name":"天宝微拍高级顾问-晏老师","job":"0316","phone":"18913219681"},
    {"name":"天宝微拍-郝老师","job":"0355","phone":"17702163373"},
    {"name":"天宝微拍高级顾问-小卫","job":"0365","phone":"13282404926"},
    {"name":"天宝微拍高级顾问-宋老师","job":"0118","phone":"17621798256"},
    {"name":"天宝微拍高级顾问-刘芳","job":"13020235397","phone":"13020235397"},
    {"name":"天宝微拍高级顾问-郑老师","job":"0389","phone":"13927215434"},
    {"name":"天宝微拍高级顾问-张老师","job":"0352","phone":"18013528739"},
    {"name":"天宝微拍高级顾问-张老师","job":"0152","phone":"18994308188"},
  ];
  module.exports = {
    data: function() {
      return {

      }
    },
    methods: {
      refer:function(){
        $("#cxjg").css("display","block");
        var phone = $("#keyword").val();
        var flag=false;
          if( phone == ''){
              $("#cxjg").html('您什么都没有输入呢？');
              return false;
          }
          if(!(/^1\d{10}$/.test(phone))){
              alert("手机号码不正确");
              return false;
          }

        $.each (data, function (i, item)
        {
          if( item.phone == phone ){

            $("#cxjg").html( item.name + '<br>工号：' + item.job +'<br>属于天宝微拍官方员工，请放心！');
            return flag = true;
          }
        })
        if( !flag ){
          $("#cxjg").html('该信息不属于天宝微拍官方员工，请谨慎！');
        }
      }
    }
  }
</script>

