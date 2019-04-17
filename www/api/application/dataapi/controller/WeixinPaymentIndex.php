<?php
namespace app\dataapi\controller;

use think\Controller;
use think\Db;
use app\dataapi\controller\BaseApi;
use think\Hook;
use weixinpayment\JsApiPay;
use weixinpayment\lib\WxPayUnifiedOrder;
use weixinpayment\lib\WxPayApi;
use weixinpayment\lib\WxPayConfig;
use think\Response;
use think\Config;
class WeixinPaymentIndex extends BaseApi
{
    protected $user_data;

    public function _initialize()
    {
        parent::_initialize();
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
    }

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {

        //①、获取用户openid
        $tools = new JsApiPay();
        //  $openId = $tools->GetOpenid();
        $openId = $this->user_data['openid']??'';


        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(Config::get('weixin_api.notify'));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);

       $order = WxPayApi::unifiedOrder($input);

      //  $this->printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);


        Response::create(['data'=>$jsApiParameters,'code' => '2000', 'message' => '微信支付'], 'json')->header($this->header)->send();
    }
    function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#f00;'>$key</font> : $value <br/>";
        }
    }
}
