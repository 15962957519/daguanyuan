<?php
namespace app\api\controller;
use think\Db;
use think\Hook;
use think\Request;
use think\Response;
use think\Session;
use think\Log;
use think\Config;
use app\api\model\Goods;
use app\dataapi\server\AliyunOss;
use app\api\controller\BaseApi;
use app\api\server\GoodsServer;
class IntegralMall extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }
    //积分商城列表
    public function integralMall(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        //我的积分
        $my_integral = Db::name('users')->where(array('user_id'=>$user_id))->value('pay_points');
        //喇叭 兑换 公告
        $ad = Db::name('order')
            ->alias('O')
            ->join('__USERS__ U','O.user_id = U.user_id')
            ->join('__ORDER_GOODS__ OG','O.order_id = OG.order_id')
            ->where(array('integral'=>1,'pay_status'=>1))
            ->field('U.nickname,U.mobile,OG.goods_name')
            ->order('pay_time DESC')
            ->limit(6)
            ->select();
        //查询积分商品区
        $integralMall = DB::name('goods')
            ->where('exchange_integral','<>',0)
            ->whereNull('delete_time')
            ->field('goods_id,goods_name,shop_price,shop_price,exchange_integral,original_img,endTime,click_count,goods_status,is_distribute,distribute_proportion,distribute_money')
            ->order('goods_id DESC')
            //->cache(true)
            ->select();
        foreach($integralMall as $k=>$v){
            $res_id = $v['original_img'];
            $integralMall[$k]['original_img'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
        }
        Response::create(['data'=>['my_integral'=>$my_integral,'ad'=>$ad,'integralMall'=>$integralMall],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    /**[ 积分商品区详情 ]
     * @param Request $request
     */
    public function integra_detail(Request $request){
        $goods_id = $request->param('goods_id');  //商品区id
        $goods_detail=Db::name('goods')->field('goods_id,goods_name,store_count,shop_price,exchange_integral,goods_content')
            ->where(array('goods_id'=>$goods_id))
            ->find();
        //商品区详情图
        $goods_images=Db::name('goods_images')->field('image_url')->where(array('goods_id'=>$goods_id))->select();
        foreach($goods_images as $k=>$v){
            $res_id = $v['image_url'];
            $goods_images[$k]['image_url'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
        }
        Response::create(['data'=>['goods_detail'=>$goods_detail,'goods_images'=>$goods_images],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //积分兑换记录（积分商城订单列表）
    public function exchange_order(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $where = [
            'user_id'=>$user_id,
            'integral'=>1      //1:代表积分订单
        ];
        $orders =  Db::name('order')
            ->alias('O')
            ->join('__ORDER_GOODS__ G','O.order_id = G.order_id')
            ->field('G.goods_id,G.goods_num,O.order_amount,O.integral_money,O.order_sn,O.order_id,O.add_time,O.cancel_time,O.order_status,O.shipping_status,O.pay_status')
            ->where($where)
            ->select();
        $order_array = array();
        foreach($orders as $v){
            $goods_id = $v['goods_id'];
            $result = Db::name('goods')->where(array('goods_id'=>$goods_id))->field('goods_id,goods_name,original_img')->find();
            $res_id = $result['original_img'];
            $result['original_img'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
            $result['goods_num'] = $v['goods_num'];   //订单商品区数量
            $result['order_amount'] = $v['order_amount'];   //积分订单应支付金额
            $result['order_sn'] = $v['order_sn'];   //积分订单编号
            $result['order_id'] = $v['order_id'];   //积分订单order_id
            $result['integral_money'] = $v['integral_money'];   //积分订单应支付积分
            $result['add_time'] = $v['add_time'];   //积分订单时间
            $result['pay_status'] = $v['pay_status'];   //积分订单支付状态
            $order_array[] = $result;
        }
        Response::create(['data'=>$order_array,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //积分明细log（+ -）
    public function exchange_log(){
        $account_log = Db::name('account_log')->field('pay_points,change_time,desc')->where(array('pay_points'=>['<>',0]))->limit(20)->select();

        Response::create(['data'=>$account_log,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    /**
     * 自营专区列表
     */
    public function self_product_list(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $where=array(
            'is_on_sale'=>1,        //上架商品区
            'exchange_integral'=>0 ,      //不检索积分商品区
            'is_self'=>1,               // 1：代表是自营专区
            'endTime' => ['>',time()]
        );
        $goods_list = Db::name("goods")
            ->where($where)
            ->whereNull('delete_time')
            ->field('goods_id,goods_name,start_price,shop_price,exchange_integral,original_img,endTime,click_count,goods_status,is_distribute,distribute_proportion,distribute_money')
            ->order('goods_id DESC')
            ->limit(4)
            //->cache(true)
            ->select();
        $goods_count = Db::name('goods')->count();
        foreach($goods_list as $k=>$v){
            $res_id = $v['original_img'];
            $goods_list[$k]['original_img'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
            $goods_list[$k]['care_count'] = Db::name('goods_collect')->where(['goods_id'=>$v['goods_id']])->count();  //获取点赞数
            //获取当前出价
            $cur_price = Db::name('bid_order')->where(['goods_id'=>$v['goods_id']])->order('id DESC')->find();
            if($cur_price != false){
                $goods_list[$k]['cur_price'] = $cur_price['bid_price'];
            }else{
                $goods_list[$k]['cur_price'] = $v['start_price'];
            }

            $re = Db::name('goods_collect')->where(['user_id'=>$user_id,'goods_id'=>$v['goods_id']])->find();   //当前登陆者是否已点赞当前商品区
            if($re){
                $goods_list[$k]['is_care'] = ['code'=>2,'msg'=>'已点赞'];
            }else{
                $goods_list[$k]['is_care'] = ['code'=>1,'msg'=>'未点赞'];
            }
        }

        Response::create(['data'=>['goods_list'=>$goods_list,'goods_count'=>$goods_count],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();

    }


    /**
     * [ 进入天天特价列表页 ]
     */
    public function day_special_price(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 6;
        $where=array(
            'is_on_sale'=>1,        //上架商品区
            'exchange_integral'=>0 ,      //不检索积分商品区
            'is_special_price'=>1,         // 1：代表是天天特价
            'endTime' => ['>',time()]
        );
        $special_goods_list = Db::name("goods")
            ->where($where)
            ->whereNull('delete_time')
            ->field('goods_id,goods_name,shop_price,original_img,goods_content,endTime,click_count,goods_status,is_distribute,
            distribute_proportion,distribute_money,is_free_shipping,enableReturn,user_id')
            ->order('goods_id DESC')
            ->page($p,$limitnum)
            ->select();
        foreach($special_goods_list as $k=>&$v){
            $v['care_count'] = Db::name('goods_collect')->where(['goods_id'=>$v['goods_id']])->count();  //获取点赞数
        }

        Response::create(['data'=>['special_goods_list'=>$special_goods_list],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();

    }


}