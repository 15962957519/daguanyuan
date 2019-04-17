<?php
namespace app\api\controller;
use think\Db;
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
use app\common\logic\CartLogic;
use app\common\logic\MessageLogic;
use app\common\logic\OrderLogic;
use app\dataapi\server\ProductServer;
use app\api\controller\BaseApi;
use app\dataapi\server\Jssdk;
use app\Home\Logic\UsersLogic;

class Order extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }

    //提交订单（生成）
    public function createOrder(Request $request){
        $methods =   $request->method();
        if('OPTIONS'==$methods){
            Response::create(['code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
        }
        //tp_order 表
        $order['user_id'] = (int)$this->user_data['user_id']; //当前登录者user_id;
        $order['address'] = input('address','上海嘉定江桥万达广场9号写字楼3层319室(默认)');  //详细地址（微信地址）
        $order['consignee'] = input('consignee');  //收货人
        $order['mobile'] = input('mobile');  //手机
        $order['user_note'] = input('user_note');  //买家留言（用户备注）
        $order['integral'] = input('integral');  //订单类型 0:正常订单 1:积分订单
        $order['goods_price'] = input('order_amount');  //商品区总价
        $order['total_amount'] = input('order_amount');  //订单总价
        $order['order_amount'] = input('order_amount');  //应付款金额
        $order['integral_money'] = input('integral_money');  //订单所需积分
        $order['add_time'] = time();  //下单时间
        $order['order_sn'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //生成订单编号
        //订单编号;2017121348531014  2017121351505154
        $order_msg = input('post.')['order_msg']; //获取商品区信息列表 包括 goods_id, goods_num
        //tp_order_goods 表
        Db::startTrans();
        try{
            $order_id = Db::name('order')->insertGetId($order);  //插入订单表 Order 获取订单id
            $order_goods['order_id'] = $order_id;  //订单id
            foreach($order_msg as $v){
                if(isset($v['id'])){
                    Db::name('cart')->where(array('id'=>$v['id']))->delete();  //删除购物车商品区
                }
                $order_goods['goods_id'] = $v['goods_id'];  //获取商品区id
                $order_goods['goods_name'] = $v['goods_name'];  //获取商品区名称
                $order_goods['goods_num'] = $v['goods_num'];  //购买商品区数量
                $order_goods['goods_price'] = $v['shop_price'];  //商品区价格
                $order_goods['exchange_integral'] = $v['exchange_integral'];  //商品区所需积分
                Db::name('order_goods')->insert($order_goods);  //插入order_goods 表
                //商品区库存操作(减少)
                //Db::name('goods')->where(array('goods_id'=>$v['goods_id']))->setDec('goods_num',$v['goods_num']);
            }
            Db::commit();
            $message=array(
                'status'=>0,
                'code'=>2000,
                'message'=>'订单生成成功',
                'data'=>['order_id'=>$order_id,'order_sn'=>$order['order_sn'],'integral_money'=>$order['integral_money'],'order_amount'=>$order['order_amount']]
            );
        }catch (\Exception $e){
            Db::rollback();
            $message=array(
                'status'=>0,
                'code'=>2001,
                'message'=>'订单生成失败，请稍后再试'
            );
        }
        return json($message);

    }

    //我的买单,卖单列表
    public function my_order(Request $request ){
        /* `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态 0待确认；1已确认；2已收货；3已取消；4已完成；5已作废；6申请售后 ',
         *  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态 0:未发货;1已发货；2部分发货',
         *   `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态 0:未支付;1已支付',
         *
         * 全部   pay_status=-1&shipping_status=-1&order_status=-1
         * 未付款 pay_status=0&shipping_status=0&order_status=0
         * 待发货 pay_status=1&shipping_status=0&order_status=-1
         * 待收货 pay_status=1&shipping_status=1&order_status=1  //已确认 代表已发货
         * 售后   pay_status=1&shipping_status=1&order_status=6
         */
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $seller = $request->param('seller',1);   //默认买单1，卖单2
        if(isset($seller) && $seller == 2){
            $where['sale_user_id'] = $user_id;
        }else{
            $where['user_id'] = $user_id;
        }

        if(input('pay_status') != -1){
            $where['pay_status'] =input('pay_status');
        }
        if(input('shipping_status') != -1){
            $where['shipping_status'] =input('shipping_status');
        }
        if(input('order_status') != -1){
            $where['order_status'] =input('order_status');
        }

        $orders =  Db::name('order')->alias('O')
            ->join('__ORDER_GOODS__ G','O.order_id = G.order_id')
            ->field('G.goods_id,O.order_amount,O.integral_money,O.order_id,O.order_sn,O.add_time,O.integral,O.cancel_time,O.order_status,O.shipping_status,O.pay_status')
            ->where($where)->select();
        $order_array = array();
        $ossobj = new AliyunOss(true);
        $aliyunbucket = Config::get('aliyun.bucket');
        foreach($orders as $v){
            $goods_id = $v['goods_id'];
            $seller_detail = Db::name('goods')->alias('G')->join('__USERS__ U','G.user_id = U.user_id')->field('U.user_id,U.nickname,U.head_pic')->where(array('goods_id'=>$goods_id))->find();
            $result = Db::name('goods')->where(array('goods_id'=>$goods_id))->field('goods_id,goods_name,original_img,is_free_shipping,enableReturn')->find();
            $res_id = $result['original_img'];
            $result['original_img']= $ossobj->getCurlofimgUsenoAuth($aliyunbucket, $res_id);
            $result['order_id'] = $v['order_id'];   //订单id
            $result['order_sn'] = $v['order_sn'];   //订单编号
            $result['order_amount'] = $v['order_amount'];   //成交金额
            $result['integral_money'] = $v['integral_money'];   //所需积分
            $result['add_time'] = $v['add_time'];   //下单时间
            $result['cancel_time'] = $v['cancel_time'];   //下单时间
            $result['order_status'] = $v['order_status'];   //订单状态
            $result['shipping_status'] = $v['shipping_status'];   //物流状态
            $result['pay_status'] = $v['pay_status'];   //支付状态
            $result['order_status'] = $v['order_status'];   //订单状态
            $result['seller_detail'] = $seller_detail;   //卖家详情
            $order_array[] = $result;
        }
        Response::create(['data'=>$order_array,'code'=>2000,'message'=>'获取订单信息'], 'json')->header($this->header)->send();
    }

    //我的订单详情页面
    public function orderDetail(Request $request){
        $order_id = $request->param('order_id'); //订单order_id;
        $order_detail =  Db::name('order')
            ->alias('O')
            ->join('__ORDER_GOODS__ G','O.order_id = G.order_id')
            ->field('G.goods_id,O.order_amount,O.integral_money,O.order_sn,O.order_id,O.add_time,O.cancel_time,O.order_status,O.shipping_status,O.pay_status,O.consignee,O.mobile,O.address')
            ->where(array('O.order_id'=>$order_id))
            ->find();
        $spend = Db::name('delivery_doc')->where(['order_id'=>$order_id])->field('invoice_no,create_time')->find();
        $order_detail['invoice_no'] = $spend['invoice_no'];   //获取物流单号
        $order_detail['delivery_create_time'] = $spend['create_time'];  //物流创建时间

        $result = Db::name('goods')->where(array('goods_id'=>$order_detail['goods_id']))->field('goods_name,goods_content,original_img,is_free_shipping,enableReturn')->find();
        $res_id = $result['original_img'];
        $remote_img = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
        $order_detail['goods_name'] = $result['goods_name'];  //商品区名称
        $order_detail['goods_content'] = htmlspecialchars_decode($result['goods_content']);  //商品区描述
        $order_detail['original_img'] = $remote_img;  //商品区原始图
        $order_detail['is_free_shipping'] = $result['is_free_shipping'];  //是否包邮
        $order_detail['enableReturn'] = $result['enableReturn'];  //是否包退
        //卖家详情
        $seller_detail = Db::name('goods')->alias('G')->join('__USERS__ U','G.user_id = U.user_id')->field('U.user_id,U.nickname,U.head_pic,U.mobile')->where(array('goods_id'=>$order_detail['goods_id']))->find();
        $order_detail['seller_detail'] = $seller_detail;

        Response::create(['data'=>$order_detail,'code'=>2000,'message'=>'获取订单信息'], 'json')->header($this->header)->send();
    }

    //取消订单(逻辑删除)
    public function ajaxDelOrder(Request $request){
        $order_id = $request->param('order_id'); //获取删除订单id
        Db::startTrans();
        try{
            Db::name('order')->where(['order_id'=>$order_id])->update(['order_status'=>3]);
            Db::commit();
            $message=array(
                'status'=>1,
                'code'=>2000,
                'message'=>'取消订单成功'
            );
        }catch (\Exception $e){
            Db::rollback();
            $message=array(
                'status'=>0,
                'code'=>2001,
                'message'=>'取消订单失败，请稍后再试'
            );
        }
        return json($message);
    }

    //点击已收货
    public function get_goods(Request $request){
        $methods =   $request->method();
        if('OPTIONS'==$methods){
            Response::create(['code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
        }

        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $order_id = $request->param('order_id');  //订单id
        $re = Db::name('order')->where(array('order_id'=>$order_id))->update(['order_status'=>2,'shipping_status'=>2]);
       if($re){
           $message=array(
               'status'=>1,
               'code'=>2000,
               'message'=>'确认收货成功'
           );
       }else{
           $message=array(
               'status'=>0,
               'code'=>2001,
               'message'=>'确认收货失败'
           );
       }
       return json($message);
    }

    //快递物流接口
    public function kdapi(Request $request){
        vendor('KdApi.KdApi');  //引入快递鸟快递接口
        // 根据物流单号查询物流信息 http://nide.yrwang.net/index.php/order/kdapi?tracknum=3823980691133
        $ordernum = $request->param('invoice_no');  //获取物流单号
        $com=getOrder($ordernum);
        $com=json_decode($com);
        $b=$com->Shippers;
        $kd=$b[0]->ShipperCode;
        $result = getOrderTracesByJson($kd,$ordernum);
        Response::create(['data'=>$result,'code'=>2000,'message'=>'获取物流信息'], 'json')->header($this->header)->send();
    }

    /**
     * @param Request $request 竞拍 点击出价
     */
    public function auction(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id'); //产品ID

        $sicaution = Db::name('recharge')->where(['user_id'=>$user_id,'status'=>4,'pay_status'=>1])->find();
        $user_acution = Db::name('users')->where(['user_id'=>$user_id])->value('is_acution');
        //获取保证金
        if(empty($sicaution) && $user_acution == 0){
            Response::create(['code' => 4000, 'message' => '保证金未付！'], 'json')->header($this->header)->send();
            exit;
        }

        //该商品区名称 及 结拍时间
        $current_goods = Db::name('goods')->where(['goods_id'=>$goods_id])->field('endTime,goods_name,start_price,every_add_price,upload_time')->find();
        if($current_goods['endTime'] <= time()){
            Response::create(['code' => 4001, 'message' => '截拍时间已结束！'], 'json')->header($this->header)->send();
        }
        //查询第一出价者
        $is_first = Db::name('bid_order')->where(['goods_id'=>$goods_id])->field('user_id,bid_price')->order('id DESC')->find();
        if($is_first['user_id'] == $user_id){
            Response::create(['code' => 4003, 'message' => '土豪，休息会，您已排名第一了'], 'json')->header($this->header)->send();
        }else{
            if($is_first == false){  //首次出价
                $bid_price = $current_goods['start_price']+$current_goods['every_add_price'];
            }else{
                $bid_price = $is_first['bid_price']+$current_goods['every_add_price'];
            }
            $data = ['user_id'=>$user_id,'bid_price'=>$bid_price,'goods_id'=>$goods_id,'add_time'=>time(),'upload_time'=>$current_goods['upload_time']];
            Db::name('bid_order')->insert($data); //进入出价表
            //出价数据(ID 倒叙)
            $bid_datelist = Db::name('bid_order')->alias('B')
                ->join('__USERS__ U','B.user_id = U.user_id')
                ->where(['B.goods_id'=>$goods_id])
                ->field('B.*,U.nickname,U.head_pic')
                ->order('B.id DESC')
                ->select();
            if(count($bid_datelist) >=2 ){
                Db::name('bid_order')->where(['id'=>$bid_datelist[1]['id']])->update(['status'=>10]);  //排名第二变更出局
                $Second_userid = $bid_datelist[1]['user_id'];//第二名出价user_id
                //出价超越提醒
                $userLogic = new UsersLogic();
                $rest  =  $userLogic->get_infoThird($Second_userid);
                $content=[
                    'first'  =>  ["value"=>"您的出价已被人超过！"],
                    'keyword1'  =>  ["value"=>"【{$current_goods['goods_name']}】",'color'=>"#173177"],
                    'keyword2'  =>  ["value"=>"￥{$bid_datelist[0]['bid_price']}",'color'=>"#173177"],
                    'remark'  => ["value"=>"立即前往加价！",'color'=>"#173177"],
                ];

                $template_id ='MvfpSyOAm4vNbtlX_7cjg86Mpbk4-4auWx2a10fTORc';
                $urlto =Config::get('domain')."index.html?#!dist/item?a={$current_goods['goods_id']}";
                $jssdkobj = new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));
                $msg =   $jssdkobj->activePushMsg($rest['openid'],$template_id,$urlto,$content);
            }

            Response::create(['data' => $bid_datelist, 'code' => 2000, 'message' => '出价成功'], 'json')->header($this->header)->send();
        }
    }

    /**
     * [出价倒计时结束 出价最高者为购买者 生成订单]
     * @param Request $request
     * @return \think\response\Json
     */
    public function makeorder(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id'); //产品ID
        $goods_msg = Db::name('goods')->where(['goods_id'=>$goods_id])->find(); //该商品区信息

        $order['user_id'] = $user_id; //当前登录者user_id;
        $order['sale_user_id'] = $goods_msg['user_id']; //商品区卖家user_id;
        $order['total_amount'] = $goods_msg['start_price'];
        $order['order_amount'] = $goods_msg['start_price'];  //应付款金额
        $order['add_time'] = time();  //下单时间
        $order['order_sn'] = date('YmdHis') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(true), 7, 14), 1))), 0, 9) . mt_rand(10000, 99999);
        //订单编号;2017121348531014  2017121351505154
        Db::startTrans();
        try{
            $order_id = Db::name('order')->insertGetId($order);  //插入订单表 Order 获取订单id
            $order_goods['order_id'] = $order_id;  //订单id
            $order_goods['goods_id'] = $goods_id;  //获取商品区id
            $order_goods['goods_name'] = $goods_msg['goods_name'];  //获取商品区名称
            $order_goods['goods_price'] = $goods_msg['start_price'];  //商品区价格
            $order_goods['upload_time'] = $goods_msg['upload_time'];  //商品区上传时间
            Db::name('order_goods')->insert($order_goods);  //插入order_goods 表
            Db::commit();

            self::ordermessage($user_id,$order_id,$goods_msg['goods_name'],$order['order_sn'],$goods_msg['start_price']);

            $message = ['code'=>2000, 'msg'=>'订单生成成功','order_id'=>$order_id];
        }catch (\Exception $e){
            echo $e->getMessage();
            Db::rollback();
            $message=['code'=>4000, 'msg'=>'订单生成失败','order_id'=>'no order_sn'];
        }
        Response::create(['data'=>$message], 'json')->header($this->header)->send();


    }

    /*
 * 订单信息推送
 */
    static public function ordermessage($user_id,$order_id,$goods_name,$order_sn,$order_amount){
        $userLogic = new UsersLogic();
        $user  =  $userLogic->get_infoThird($user_id);
        $jssdkobj = new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));

        $goods_msg = Db::name('order_goods')->where('order_id',$order_id)->field('goods_id,goods_name')->find();
        $template_id = 'aW4cvSKq3uzox5epHdiaDDItmbzxJ_908EClOM-QYLg';
        $urlto = "http://wap.yipinfanghua.com/#/user/myorder/orderdetail?order_id={$order_id}";
        $content = [
            'first' => ["value" => "恭喜您,您刚刚下了一笔订单!"],
            'keyword1' => ["value" => "{$order_sn}", 'color' => "#173177"],
            'keyword2' => ["value" => "￥{$order_amount}", 'color' => "#173177"],
            'keyword3' => ["value" => "{$goods_name}", 'color' => "#173177"],
            'remark' => ["value" => "立即前往查看订单详情！", 'color' => "#173177"],
        ];

        $jssdkobj->activePushMsg($user['openid'],$template_id,$urlto,$content);


        //给商品发布者发消息
        $uploader_user_id = Db::name('goods')->where('goods_id',$goods_msg['goods_id'])->value('user_id');
        $uploader_user_openid = Db::name('third_users')->where(['user_id'=>$uploader_user_id])->value('openid');
        if($uploader_user_openid != ''){
            $link = "http://wap.wsguwancheng.com/#/index/{$goods_msg['goods_id']}";
            $wx_content = "您发布的商品已被别人下单!\n订单号:{$order_sn}\n商品名称:{$goods_msg['goods_name']}\n点击查看:{$link}";
            $jssdkobj->push_msg($uploader_user_openid, $wx_content);
        }




    }

    /**
     * 添加微信地址到订单
     */
    public function addAdress(){
        $order_sn = input('order_sn'); //获取订单号
        $order['address'] = input('address','上海嘉定江桥万达广场9号写字楼3层319室(默认)');  //详细地址（微信地址）
        $order['consignee'] = input('consignee');  //收货人
        $order['mobile'] = input('mobile');  //手机
        $addobj = Db::name("order");
        $res = $addobj->where('order_sn',$order_sn)->update($order);
        if($res){
            $addres = $addobj->where('order_sn',$order_sn)->field('address,consignee,mobile')->find();
            Response::create(['data'=>$addres,'code' => 2000, 'message' => '添加成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code' => 2001, 'message' => '添加失败，请重试'], 'json')->header($this->header)->send();
        }

    }

    /**
     * [订单24小时不支付 自动取消]
     * @param Request $request
     */
    public function orderCancel(Request $request){
        $order_sn = $request->param('order_sn');

        Db::name("order")->where(['order_sn'=>$order_sn])->update(['order_status'=>3]); //订单取消

        Response::create(['code' => 2000, 'message' => '订单取消成功'], 'json')->header($this->header)->send();

    }

    /**
     * [ 订单最后一小时即将结束提醒 ]
     */
    public function orderNotice(Request $request){
        $order_sn = $request->param('order_sn'); //
        $orderMsg = Db::name('order')->where(['order_sn'=>$order_sn])->field('order_id,user_id,add_time')->find();
        $add_time = date('Y-m-d H:i:s',$orderMsg['add_time']);//订单时间
        $end_time = $orderMsg['add_time'] + 24*3600 ;
        $endTime = date('Y-m-d H:i:s',$end_time);//订单截止时间

        $userLogic = new UsersLogic();
        $rest  =  $userLogic->get_infoThird($orderMsg['user_id']);
        $content=[
            'first'  =>  ["value"=>"订单到期提醒"],
            'keyword1'  =>  ["value"=>"{$order_sn}",'color'=>"#173177"],
            'keyword2'  =>  ["value"=>"{$add_time}",'color'=>"#173177"],
            'keyword3'  =>  ["value"=>"{$endTime}",'color'=>"#173177"],
            'remark'  => ["value"=>"您的订单即将超时，系统将自动取消",'color'=>"#173177"],
        ];

        $template_id ='lDc2FNhwzhmR_THvT089y5xUlPgCjmq07qxDybWfREg';
        $urlto =Config::get('domain')."index.html?#!dist/order_details?a={$orderMsg['order_id']}";
        $jssdkobj = new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));
        $msg =   $jssdkobj->activePushMsg($rest['openid'],$template_id,$urlto,$content);



    }

    /**
     * [ 我的买入列表 ]
     */
    public function buyList(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $where = ['OR.user_id'=>$user_id,'OR.pay_status'=>1];
        $goods_ids = Db::name('order')->alias('OR')->join('__ORDER_GOODS__ OG','OR.order_id = OG.order_id')
            ->where($where)->column('OG.goods_id');
        $buylist = [];
        $oss = new AliyunOss(true);
        foreach($goods_ids as $k=>$v){
            $goodmsg = Db::name('goods')->where(['goods_id'=>$v])->find();
            $goodmsg['original_img'] = $oss->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $goodmsg['original_img']);
            $buylist[] = $goodmsg;
        }
        Response::create(['data'=>$buylist,'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();

    }

    /**[截止 且他人成交订单 此时可删除(物理删) ]
     * @param Request $request
     */
    public function delBid(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id');
        $del_re = Db::name('bid_order')->where(['goods_id'=>$goods_id,'user_id'=>$user_id])->update(['delete_time'=>time()]);
        if($del_re){
            Response::create(['code' => 2000, 'message' => '删除成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code' => 2001, 'message' => '删除失败'], 'json')->header($this->header)->send();
        }

    }

    /**[ 该商品区是否生成自己的订单列表里 ]
     * @param Request $request
     */
    public function isOrderOwn(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id');
        $orderToUserid = Db::name('order_goods')->alias('OG')
            ->join('__ORDER__ OR','OG.order_id = OR.order_id')
            ->where('OG.goods_id',$goods_id)
            ->column('OR.user_id');
        if(in_array($user_id,$orderToUserid)){
            Response::create(['code' => 2000, 'msg' => '该订单已存在'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code' => 4000, 'msg' => '准备生成订单'], 'json')->header($this->header)->send();
        }
    }

    /**[ 商品议价 ]
     * @param Request $request
     */
    public function new_bid(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = (int)$request->param('goods_id'); //产品ID
        if(!isset($goods_id) || $goods_id<=0){
            Response::create(['code' => 5005, 'msg' => '无商品Id'], 'json')->header($this->header)->send();
        }

        if($user_id == Db::name('goods')->where(['goods_id'=>$goods_id])->value('user_id')){
            Response::create(['code' => 5001, 'msg' => '自己的商品不能议价'], 'json')->header($this->header)->send();
        }
        $is_exit = Db::name('bid_order')->where(['user_id'=>$user_id,'goods_id'=>$goods_id])->value('id');
        if($is_exit > 0){
            Response::create(['code' => 2001, 'msg' => '不能重复议价'], 'json')->header($this->header)->send();
        }
        $data = ['user_id'=>$user_id,'goods_id'=>$goods_id,'add_time'=>time()];
        $res = Db::name('bid_order')->insertGetId($data);
        if($res > 0){
            Response::create(['code' => 2000, 'msg' => '议价成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code' => 4000, 'msg' => '议价失败'], 'json')->header($this->header)->send();
        }


    }




}




















