<?php
namespace app\api\controller;
use think\Db;
use think\Request;
use think\Session;
use think\Response;
use think\Config;
use think\Log;
use app\common\logic\UsersLogic;
use app\common\logic\CartLogic;
use app\api\controller\BaseApi;
use app\api\server\Teegon;


class TeegonPay extends BaseApi
{
    /**
     * 支付接口
     * 参数
    $data['order_no'] = '07268a02d567102fd1f49376';
    $data['channel'] = 'wxpaymp_pinganpay';
    $data['notify_url'] = Config::get('teegon_api.notify_url');
    $data['return_url'] = Config::get('teegon_api.return_url');
    $data['amount'] = '0.01';
    $data['subject'] = '支付0.01';
    $data['metadata'] = '';
    $data['client_ip'] = '192.168.0.59';
     */
    public function index(Request $request){
        $data = [];
        $data['order_no'] = $request->param('order_no');
        $data['channel'] = 'wxpaymp_pinganpay';
        $data['notify_url'] = Config::get('teegon_api.notify_url');
        $data['return_url'] = Config::get('teegon_api.return_url');
        $data['amount'] = $request->param('amount');
        $data['subject'] = $request->param('subject');
        $data['metadata'] = '';
        $data['client_ip'] = $_SERVER["REMOTE_ADDR"];

        //参数验证Todu
        $Teegon = new  Teegon();
        $result = $Teegon->index($data);
    }

    /**
     * 同步通知
     */
    public function returnUrl(Request $request){
        //签名验证
        $feedback = 'ERROR';
        $code = 4001;
        $sign = $request->param('sign');
        $param['amount']  = $request->param('amount');
        $param['bank']  = $request->param('bank');
        $param['buyer']  = $request->param('buyer');
        $param['buyer_openid']  = $request->param('buyer_openid');
        $param['channel']  = $request->param('channel');
        $param['charge_id']  = $request->param('charge_id');
        $param['device_info']  = $request->param('device_info');
        $param['domain_id']  = $request->param('domain_id');
        $param['is_success']  = $request->param('is_success');
        $param['metadata']  = $request->param('metadata');
        $param['order_no']  = $request->param('order_no');
        $param['pay_time']  = $request->param('pay_time');
        $param['payment_no']  = $request->param('payment_no');
        $param['real_amount']  = $request->param('real_amount');
        $param['status']  = $request->param('status');
        $param['timestamp']  = $request->param('timestamp');

        if($sign == $this->checkSign($param,Config::get('teegon_api.secret'))){
            if($request->param('is_success') == 'true'){
                header('location:http://guwanm.haiousystem.com/index');
            }
        }
    }

    /**
     * 异步通知
     */
    public function notifyUrl(Request $request){
        //签名验证
        $feedback = 'ERROR';
        $sign = $request->param('sign');
        $param['amount']  = $request->param('amount');
        $param['bank']  = $request->param('bank');
        $param['buyer']  = $request->param('buyer');
        $param['buyer_openid']  = $request->param('buyer_openid');
        $param['channel']  = $request->param('channel');
        $param['charge_id']  = $request->param('charge_id');
        $param['device_info']  = $request->param('device_info');
        $param['domain_id']  = $request->param('domain_id');
        $param['is_success']  = $request->param('is_success');
        $param['metadata']  = $request->param('metadata');
        $param['order_no']  = $request->param('order_no');
        $param['pay_time']  = $request->param('pay_time');
        $param['payment_no']  = $request->param('payment_no');
        $param['real_amount']  = $request->param('real_amount');
        $param['status']  = $request->param('status');
        $param['timestamp']  = $request->param('timestamp');

        if($request->param('sign') == $this->checkSign($param,Config::get('teegon_api.secret'))){
            if($request->param('is_success') == 'true'){
                //接收参数，修改订单状态,返回
                $order_sn = $request->param('order_no');  //创建订单是自己生成的订单order_sn
                $charge_id = $request->param('charge_id');  //支付单号
                $payment_no = $request->param('payment_no');  //第三方支付单号
                $pay_time = $request->param('pay_time');  //付款时间

                $data = ['charge_id'=>$charge_id,'payment_no'=>$payment_no,'pay_status'=>1,'pay_time'=>$pay_time];
                $result = Db::name('order')->where(['order_sn'=>$order_sn])->update($data);

                $feedback = 'SUCCESS';
            }
        }
        echo $feedback;exit;

    }

    //验签 $params 为通知参数集合，$secret为用户的app_secret
    public function checkSign($params,$secret){
        $sign = array(
            $secret,
            $this->isort($params),
            $secret
        );

        $sign = implode('', $sign);
        return strtoupper(md5($sign));
    }

    //参数排序
    public function isort($params) {
        if(is_array($params)) {
            ksort($params);
            $result = array();
            foreach($params as $key=>$value){
                if ($value === false)
                    $value = 0;
                if ($value !== null)
                    $result[] = $key.$value;
            }
            return implode('', $result);
        }
    }

}