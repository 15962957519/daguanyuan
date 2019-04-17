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
use app\api\server\WxAddress;
use app\dataapi\model\Users as Userss;
use app\common\logic\UsersLogic;
use app\common\logic\CartLogic;
use app\common\logic\MessageLogic;
use app\common\logic\OrderLogic;
use app\dataapi\server\ProductServer;
use app\api\controller\BaseApi;

class ReturnGoods extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }
    //申请退货、换货
    public function applyReturn(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $order_id = $request->param('order_id');  //订单id
        $order_sn = $request->param('order_sn');  //订单编号
        $goods_id = $request->param('goods_id');  //商品区id
        $type = $request->param('type');  //0退货1换货
        $reason = $request->param('reason');  //退换货原因
        $apply_number = date('Ymd') . rand(1000000,9999999);  //生成随机申请编号
        $addtime = time();  //申请时间
        $data = ['user_id'=>$user_id,'order_id'=>$order_id,'order_sn'=>$order_sn,'goods_id'=>$goods_id,'type'=>$type,'reason'=>$reason,'addtime'=>$addtime,'apply_number'=>$apply_number];

        $is_apply = Db::name('return_goods')->where(['order_sn'=>$order_sn])->find();
        if($is_apply == NULL){
            Db::startTrans();
            try{
                Db::name('return_goods')->insert($data);  //提交售后申请
                Db::name('order')->where(['order_sn'=>$order_sn])->update(['order_status'=>6]);  //order_status=6 代表售后订单
                Db::commit();
                Response::create(['code'=>2000,'message'=>'申请提交成功,静待处理'], 'json')->header($this->header)->send();
            }catch (Exception $e){
                Db::rollback();
                Response::create(['code'=>2001,'message'=>'申请提交失败'], 'json')->header($this->header)->send();
            }
        }else{
            Response::create(['code'=>2000,'message'=>'申请已提交过'], 'json')->header($this->header)->send();
        }
    }
    //售后处理进度
    public function applyHandle(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $order_sn = $request->param('order_sn');  //订单编号
        //查询售后信息
        $return_goods_msg = Db::name('return_goods')->field('status,reason,addtime,apply_number')->where(['order_sn'=>$order_sn])->find();
        $order_detail =  Db::name('order')
            ->alias('O')
            ->join('__ORDER_GOODS__ G','O.order_id = G.order_id')
            ->field('G.goods_id,O.order_amount,O.order_sn,O.order_id,O.add_time,O.cancel_time,O.order_status,O.shipping_status,O.pay_status')
            ->where(array('O.order_sn'=>$order_sn))
            ->find();
        $goods_id = $order_detail['goods_id']; //订单order_id
        $result = Db::name('goods')->where(array('goods_id'=>$goods_id))->field('goods_name,original_img,is_free_shipping,enableReturn')->find();
        $res_id = $result['original_img'];
        $remote_img = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
        $order_detail['goods_name'] = $result['goods_name'];  //商品区名称
        $order_detail['original_img'] = $remote_img;  //商品区原始图
        $order_detail['is_free_shipping'] = $result['is_free_shipping'];  //是否包邮
        $order_detail['enableReturn'] = $result['enableReturn'];  //是否包退

        Response::create(['data'=>['return_goods_msg'=>$return_goods_msg,'order_detail'=>$order_detail],'code'=>2000,'message'=>'获取售后信息'], 'json')->header($this->header)->send();
    }
}
