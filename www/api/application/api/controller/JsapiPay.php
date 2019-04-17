<?php
namespace app\api\controller;
use think\Controller;

use think\Hook;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Request;
use think\Db;
use think\Log;

class JsapiPay extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }
    public function index(Request $request){
        $openid = $this->user_data['openid']; //获取当前登录者openid;
        $order_sn = $request->param('order_sn');   //获取订单号
        $ordersmsg = Db::name('order')->where(['order_sn'=>$order_sn])->field('order_amount,order_id')->find();   //订单金额
        $goods_name = Db::name('order_goods')->where('order_id',$ordersmsg['order_id'])->value('goods_name');
        $order_amount = $ordersmsg['order_amount'];
        //获取微信配置
        $wxConf = config('wechat');
        //实例化easyWeChat
        $wxApp = new Application($wxConf);
        //支付订单参数
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $goods_name,
            'detail'           => $goods_name,
            'out_trade_no'     => $order_sn,     //'自己生成自己站点的唯一单号',
            'total_fee'        => $order_amount*100, // 单位：分// 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'notify_url'       => 'http://api.fanghua.jiuxintangwenhua.com/paynotify',
            'openid'           => $openid  //openid
        ];
        //初始化订单
        $order = new Order($attributes);
        //实例化支付
        $payment = $wxApp->payment;
        //预支付
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
            $json = $payment->configForPayment($prepayId);
            return json(['jsondata'=>$json,'code'=>2000]);
        }else{
            return json(['code'=>2001,'msg'=>$result]);
        }
    }

    //异步通知
    public function notify(Request $request){
            //微信支付notify
            $wxConf = config('wechat');
            $wxApp = new Application($wxConf);
            try{
                $response = $wxApp->payment->handleNotify(function ($notify,$successful) {
                    if($successful){
                        $out_trade_no = $notify->out_trade_no;
                        $total_fee = $notify->total_fee;   //支付金额(单位：分)
                        $total_fee2 = $total_fee/100;  //转化为单位：元
                        //变更订单状态   0:未支付;1已支付
                        $data = ['pay_status' => 1, 'pay_name'=>'微信支付', 'pay_time' => time()];
                        $recharge = Db::name('order')->where(['order_sn' => $out_trade_no])->field('user_id,pay_status')->find();
                        $account_log = ['user_id'=>$recharge['user_id'],'change_money'=>$total_fee2,'pay_points'=>$total_fee2,'change_time'=>time(),'desc'=>'购买商品区赠送积分'];  //账户记录

                        if($recharge['pay_status'] == 0){
                            Db::name('order')->where(['order_sn' => $out_trade_no])->data($data)->update();
                            Db::name('account_log')->insert($account_log);   // 购买商品区赠送积分
                        }
                    }

                    return SUCCESS;
                });

            }catch(\EasyWeChat\Core\Exceptions\FaultException $e){





            }


    }

}