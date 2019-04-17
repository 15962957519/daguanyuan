<template>
  <div id="mumberupgrade">
    <div class="well">
      <h3 class="kk">关于滥留联系方式的处罚通告！</h3>
      <hr/>
      <p>尊敬的天宝微拍用户朋友们：</p>
      <p class="vv">因接到大量客户投诉，部分不良商户在商品区描述中滥留联系方式，诱导私下交易，并诱骗对方私下转账，更有严重者偷换商品区。这种现象严重扰乱平台规则，并对客户的财产和安全造成重大威胁，平台将严厉打击此类不规范用户，情节严重的将移交司法机关处理。</p>
      <p class="vv">在此，天宝微拍风控部门再次强调处罚措施，请各位用户知晓，并认真遵守，以免违规成封号。</p>
      <p>处罚：</p>
      <p>违规一次，禁封账号2天，并降低相应等级。</p>
      <p>违规二次，禁封账号10天（发布的商品区，审核后才可发布）。</p>
      <p>违规三次，彻底封号。</p>
      <p>说明：在用户名、商品区名称、商品区描述、商品区图片中滥留联系（电话号码、微信号、QQ等）均属于违规行为。</p>
      <p class="vv">同时提示各位买家群体，不要受联系方式的诱骗，严格遵守天宝微拍平台规则。若在交易过程发现违规操作，请及时投诉，避免损失，若因违规操作造成的损失平台不承担任何责任。</p>
      <hr/>
      <p class="ll"> 天宝微拍风控部</p>
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
  #mumberupgrade .well{
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
  }
  #mumberupgrade .kk{
    text-align: center;
  }
  #mumberupgrade .vv{
    text-indent:29px;
  }
  #mumberupgrade .ll{
    text-align: right;
  }
</style>
