<?php
namespace app\api\controller;
use think\Db;
use think\Exception;
use think\Hook;
use think\Request;
use think\Session;
use think\Response;
use think\Config;
use app\dataapi\server\AliyunOss;
use app\api\server\GoodsServer;
use app\api\server\UsersServer;
use app\dataapi\model\Users as Userss;
use app\common\logic\UsersLogic;
use app\common\logic\CartLogic;
use app\common\logic\MessageLogic;
use app\common\logic\OrderLogic;
use app\dataapi\server\ProductServer;
use app\api\controller\BaseApi;

class Pay extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }

    //余额支付
    public function balance_pay(Request $request){
        $methods =   $request->method();
        if('OPTIONS'==$methods){
            Response::create(['code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
        }

        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $order_id = $request->param('order_id'); //获取订单id
        //$order_sn = $request->param('order_sn'); //获取订单编号
        $order_amount = $request->param('order_amount'); //订单金额
        $user_msg = Db::name('users')->where(array('user_id'=>$user_id))->field('user_money,frozen_money')->find();
        $eable_money = $user_msg['user_money'] - $user_msg['frozen_money']; //可用余额
        if($order_amount > $eable_money){
            $message = [
                'code' =>2001,
                'msg'=>'sorry,可用余额不足'
            ];
        }else{
            Db::startTrans();
            try{
                Db::name('users')->where(array('user_id'=>$user_id))->setDec('user_money',$order_amount);  //变更账户余额
                Db::name('order')->where(array('order_id'=>$order_id))->update(['pay_name'=>'余额支付','pay_status'=>1,'pay_time'=>time()]);  //支付方式 支付状态,支付时间
                $account_log = ['user_id'=>$user_id,'user_money'=>'-'.$order_amount,'pay_points'=>$order_amount,'change_time'=>time(),'desc'=>'余额支付账户记录'];  //余额支付赠送积分记录
                Db::name('account_log')->insert($account_log);
                Db::commit();
                $message = [
                    'code' =>2000,
                    'msg'=>'余额支付成功'
                ];
            }catch (Exception $e){
                Db::rollback();
                $message = [
                    'code' =>2001,
                    'msg'=>'余额支付失败'
                ];
            }
        }
        return $message;
    }

    //积分商品区 积分支付
    public function integral_pay(Request $request){
        $methods =   $request->method();
        if('OPTIONS'==$methods){
            Response::create(['code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
        }
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $order_id = $request->param('order_id'); //获取订id
        //$order_sn = $request->param('order_sn'); //获取订单编号
        $integral_money = $request->param('integral_money'); //所需积分
        $user_points = Db::name('users')->where(array('user_id'=>$user_id))->value('pay_points');
        if($integral_money > $user_points){
            $message = [
                'code' =>2001,
                'msg'=>'sorry,积分不足'
            ];
        }else{
            Db::name('users')->where(array('user_id'=>$user_id))->setDec('pay_points',$integral_money);  //变更账户余额
            Db::name('order')->where(array('order_id'=>$order_id))->update(['pay_name'=>'积分支付','pay_status'=>1,'pay_time'=>time()]);  //支付方式 支付状态,支付时间
            $message = [
                'code' =>2000,
                'msg'=>'积分支付成功'
            ];
        }
        return $message;
    }


}