<?php
namespace   weixinpayment;

use  weixinpayment\lib\WxPayApii;
use  weixinpayment\lib\WxPayNotify;
use  weixinpayment\lib\WxPayOrderQuery;
use app\dataapi\server\UserServer;
use think\Db;
use  app\dataapi\model\UserBond;
/**
 * 
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class JsApiPayTwo  extends WxPayNotify
{



    public function updateBid($data){

        //通过openid 获取用户个人信息
        if(isset($data['openid'])&& !empty($data['openid'])){
            $uu = new UserServer();
            $userong = $uu->getThirdUserinfo($data['openid']);
            $uu = new UserBond();
           $flag = $uu->where('order_sn',$data['out_trade_no'])->find();
            if(!empty($flag)){
                return  true;
            }elseif(empty($flag)){
                $hot_goods =  Db::name('user_bond')
                    ->insertGetId([
                        'bond'  => bcdiv($data['total_fee'],100,2),
                        'order_sn'  => $data['out_trade_no'],
                        'user_id'  => $userong['user_id'],
                        'datetime'  => time()
                    ]);
                return  true;
            }
        }
        return false;
    }



    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApii::orderQuery($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }

        return  $this->updateBid($data);
    }

}