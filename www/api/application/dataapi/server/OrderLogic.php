<?php
namespace app\dataapi\server;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class OrderLogic
{

    /**
     *  添加一个订单
     * @param type $user_id  用户id
     * @param type $address_id 地址id
     * @param type $shipping_code 物流编号
     * @param type $invoice_title 发票
     * @param type $coupon_id 优惠券id
     * @param type $car_price 各种价格
     * @param type $user_note 用户备注
     * @return type $order_id 返回新增的订单id
     */
    public function addOrder($user_id,$address_id,$shipping_code,$invoice_title,$coupon_id = 0,$car_price,$user_note='')
    {

        // 仿制灌水 1天只能下 50 单  // select * from `tp_order` where user_id = 1  and order_sn like '20151217%'
        $order_count = Db::name('Order')->where("user_id",$user_id)->where('order_sn', 'like', date('Ymd')."%")->count(); // 查找购物车商品区总数量
        if($order_count >= 50)
            return array('status'=>-9,'msg'=>'一天只能下50个订单','result'=>'');

        // 0插入订单 order
        $address = M('UserAddress')->where("address_id", $address_id)->find();
        $shipping = M('Plugin')->where("code", $shipping_code)->cache(true,TPSHOP_CACHE_TIME)->find();
        $data = array(
            'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$user_id, // 用户id
            'consignee'        =>$address['consignee'], // 收货人
            'province'         =>$address['province'],//'省份id',
            'city'             =>$address['city'],//'城市id',
            'district'         =>$address['district'],//'县',
            'twon'             =>$address['twon'],// '街道',
            'address'          =>$address['address'],//'详细地址',
            'mobile'           =>$address['mobile'],//'手机',
            'zipcode'          =>$address['zipcode'],//'邮编',
            'email'            =>$address['email'],//'邮箱',
            'shipping_code'    =>$shipping_code,//'物流编号',
            'shipping_name'    =>$shipping['name'], //'物流名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            'invoice_title'    =>$invoice_title, //'发票抬头',
            'goods_price'      =>$car_price['goodsFee'],//'商品区价格',
            'shipping_price'   =>$car_price['postFee'],//'物流价格',
            'user_money'       =>$car_price['balance'],//'使用余额',
            'coupon_price'     =>$car_price['couponFee'],//'使用优惠券',
            'integral'         =>($car_price['pointsFee'] * tpCache('shopping.point_rate')), //'使用积分',
            'integral_money'   =>$car_price['pointsFee'],//'使用积分抵多少钱',
            'total_amount'     =>($car_price['goodsFee'] + $car_price['postFee']),// 订单总额
            'order_amount'     =>$car_price['payables'],//'应付款金额',
            'add_time'         =>time(), // 下单时间
            'order_prom_id'    =>$car_price['order_prom_id'],//'订单优惠活动id',
            'order_prom_amount'=>$car_price['order_prom_amount'],//'订单优惠活动优惠了多少钱',
            'user_note'        =>$user_note, // 用户下单备注
        );
        $data['order_id'] = $order_id = M("Order")->insertGetId($data);
        $order = $data;//M('Order')->where("order_id", $order_id)->find();
        if(!$order_id)
            return array('status'=>-8,'msg'=>'添加订单失败','result'=>NULL);

        // 记录订单操作日志
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '您提交了订单，请等待系统确认',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>time(),
        );
        M('order_action')->insertGetId($action_info);

        // 1插入order_goods 表
        $cartList = M('Cart')->where(['user_id'=>$user_id,'selected'=>1])->select();
        foreach($cartList as $key => $val)
        {
            $goods = M('goods')->where("goods_id", $val['goods_id'])->cache(true,TPSHOP_CACHE_TIME)->find();
            $data2['order_id']           = $order_id; // 订单id
            $data2['goods_id']           = $val['goods_id']; // 商品区id
            $data2['goods_name']         = $val['goods_name']; // 商品区名称
            $data2['goods_sn']           = $val['goods_sn']; // 商品区货号
            $data2['goods_num']          = $val['goods_num']; // 购买数量
            $data2['market_price']       = $val['market_price']; // 市场价
            $data2['goods_price']        = $val['goods_price']; // 商品区价               为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            $data2['spec_key']           = $val['spec_key']; // 商品区规格
            $data2['spec_key_name']      = $val['spec_key_name']; // 商品区规格名称
            $data2['member_goods_price'] = $val['member_goods_price']; // 会员折扣价
            $data2['cost_price']         = $goods['cost_price']; // 成本价
            $data2['give_integral']      = $goods['give_integral']; // 购买商品区赠送积分
            $data2['prom_type']          = $val['prom_type']; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            $data2['prom_id']            = $val['prom_id']; // 活动id
            $order_goods_id              = M("OrderGoods")->insertGetId($data2);
            // 扣除商品区库存  扣除库存移到 付完款后扣除
            //M('Goods')->where("goods_id = ".$val['goods_id'])->setDec('store_count',$val['goods_num']); // 商品区减少库存
        }

        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
        if($data['order_amount'] == 0)
        {
            update_pay_status($order['order_sn'], 1);
        }

        // 2修改优惠券状态
        if($coupon_id > 0){
            $data3['uid'] = $user_id;
            $data3['order_id'] = $order_id;
            $data3['use_time'] = time();
            M('CouponList')->where("id", $coupon_id)->save($data3);
            $cid = M('CouponList')->where("id", $coupon_id)->getField('cid');
            M('Coupon')->where("id", $cid)->setInc('use_num'); // 优惠券的使用数量加一
        }
        // 3 扣除积分 扣除余额
        if($car_price['pointsFee']>0)
            M('Users')->where("user_id", $user_id)->setDec('pay_points',($car_price['pointsFee'] * tpCache('shopping.point_rate'))); // 消费积分
        if($car_price['balance']>0)
            M('Users')->where("user_id", $user_id)->setDec('user_money',$car_price['balance']); // 抵扣余额
        // 4 删除已提交订单商品区
        M('Cart')->where(['user_id' => $user_id,'selected' => 1])->delete();

        // 5 记录log 日志
        $data4['user_id'] = $user_id;
        $data4['user_money'] = -$car_price['balance'];
        $data4['pay_points'] = -($car_price['pointsFee'] * tpCache('shopping.point_rate'));
        $data4['change_time'] = time();
        $data4['desc'] = '下单消费';
        $data4['order_sn'] = $order['order_sn'];
        $data4['order_id'] = $order_id;
        // 如果使用了积分或者余额才记录
        ($data4['user_money'] || $data4['pay_points']) && M("AccountLog")->add($data4);


        // 如果有微信公众号 则推送一条消息到微信
        $user = M('users')->where("user_id", $user_id)->find();
        if($user['oauth']== 'weixin')
        {
            $wx_user = M('wx_user')->find();
            $jssdk = new Jssdk($wx_user['appid'],$wx_user['appsecret']);
            $wx_content = "你刚刚下了一笔订单:{$order['order_sn']} 尽快支付,过期失效!";
            $jssdk->push_msg($user['openid'],$wx_content);
        }
        //用户下单, 发送短信给商家
        $res = checkEnableSendSms("3");
        $sender = tpCache("shop_info.mobile");

        if($res && $res['status'] ==1 && !empty($sender)){

            $params = array('consignee'=>$order['consignee'] , 'mobile' => $order['mobile']);
            $resp = sendSms("3", $sender, $params);
        }
        return array('status'=>1,'msg'=>'提交订单成功','result'=>$order_id); // 返回新增的订单id
    }


}


/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function tpCache($config_key,$data = array()){
    $param = explode('.', $config_key);
    if(empty($data)){
        //如$config_key=shop_info则获取网站信息数组
        //如$config_key=shop_info.logo则获取网站logo字符串
        $config = F($param[0],'',TEMP_PATH);//直接获取缓存文件
        if(empty($config)){
            //缓存文件不存在就读取数据库
            $res = Db::name('config')->where("inc_type",$param[0])->select();
            if($res){
                foreach($res as $k=>$val){
                    $config[$val['name']] = $val['value'];
                }
                F($param[0],$config,TEMP_PATH);
            }
        }
        if(count($param)>1){
            return $config[$param[1]];
        }else{
            return $config;
        }
    }else{
        //更新缓存
        $result =  Db::name('config')->where("inc_type", $param[0])->select();
        if($result){
            foreach($result as $val){
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
                if(!isset($temp[$k])){
                    M('config')->insert($newArr);//新key数据插入数据库
                }else{
                    if($v!=$temp[$k])
                        M('config')->where("name", $k)->save($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = Db::name('config')->where("inc_type", $param[0])->select();
            foreach ($newRes as $rs){
                $newData[$rs['name']] = $rs['value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
            }
            M('config')->insertAll($newArr);
            $newData = $data;
        }
        return F($param[0],$newData,TEMP_PATH);
    }
}

/**
 * 发送短信逻辑
 * @param unknown $scene
 */
function sendSms($scene , $sender, $params){

    $smsTemp = M('sms_template')->where("send_scene= :send_scene")->bind(['send_scene'=>$scene])->find();    //用户注册.
    $code = !empty($params['code']) ? $params['code'] : false;
    $consignee = !empty($params['consignee']) ? $params['consignee'] : false;
    $user_name =  !empty($params['user_name']) ? $params['user_name'] : false;
    $order_sn = $params['order_sn'];

    $product = smsConfigValueScene('sms_product');

    $smsParams = array(
        1 => "{\"code\":\"$code\",\"product\":\"$product\"}",                                                                   //1. 用户注册
        2 => "{\"code\":\"$code\"}",                                                                                                          //2. 用户找回密码
        3 => "{\"consignee\":\"$consignee\",\"phone\":\"$sender\"}",                                                       //3. 客户下单
        4 => "{\"order_sn\":\"$order_sn\"}",                                                                                               //4. 客户支付
        5 => "{\"user_name\":\"$user_name\",\"order_sn\":\"$order_sn\",\"consignee\":\"$consignee\"}",  //5.商家发货
        6 => "{\"user_name\":\"$user_name\",\"code\":\"$code\"}"                                                            //6. 修改手机号码
    );

    $smsParam = $smsParams[$scene];

    $resp =  realSendSMS($sender, $smsTemp['sms_sign'], $smsParam , $smsTemp['sms_tpl_code']);

    if($resp['status'] == 1){
        $session_id = session_id();
        // 从数据库中查询是否有验证码
        $data = M('sms_log')->where(array('mobile' => $sender, 'status'=>0, 'session_id' => $session_id))->order('add_time desc')->find();
        if(empty($data)){
            // 没有就插入验证码,供验证用
            $data = array('mobile' => $sender,  'add_time' => time(),'status'=>1, 'session_id' => $session_id);
            if($code){
                $data['code'] = $code;
            }
            M('sms_log')->add($data);
        }else{
            //修改发送状态为成功
            M('sms_log')->where(array('id'=>$data['id']))->save(array('status'=>1));
        }
    }
    return $resp;
}



//    /**
//     * 为照顾新手开发者 方便调试, 此方法每一行加以注释说明
//     * 发送短信
//     * @param $mobile  手机号码
//     * @param $smsSign    短信签名 必须
//     * @param smsParam   短信模板 必须
//     * @param $templateCode    短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
//     * @return bool    短信发送成功返回true失败返回false
//     */
function realSendSMS($mobile, $smsSign, $smsParam , $templateCode)
{

    //时区设置：亚洲/上海
    date_default_timezone_set('Asia/Shanghai');
    //这个是你下面实例化的类
    vendor('Alidayu.TopClient');
    //这个是topClient 里面需要实例化一个类所以我们也要加载 不然会报错
    vendor('Alidayu.ResultSet');
    //这个是成功后返回的信息文件
    vendor('Alidayu.RequestCheckUtil');
    //这个是错误信息返回的一个php文件
    vendor('Alidayu.TopLogger');
    //这个也是你下面示例的类
    vendor('Alidayu.AlibabaAliqinFcSmsNumSendRequest');

    $c = new \TopClient;
    $config = tpCache('sms');
    //App Key的值 这个在开发者控制台的应用管理点击你添加过的应用就有了
    $c->appkey = $config['sms_appkey'];
    //App Secret的值也是在哪里一起的 你点击查看就有了
    $c->secretKey = $config['sms_secretKey'];
    //这个是用户名记录那个用户操作
    $req = new \AlibabaAliqinFcSmsNumSendRequest;
    //代理人编号 可选
    $req->setExtend("123456");
    //短信类型 此处默认 不用修改
    $req->setSmsType("normal");
    //短信签名 必须
    $req->setSmsFreeSignName($smsSign);
    //短信模板 必须
    $req->setSmsParam($smsParam);
    //短信接收号码 支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，
    $req->setRecNum("$mobile");
    //短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
    $req->setSmsTemplateCode($templateCode); // templateCode

    $c->format='json';
    //发送短信
    $resp = $c->execute($req);
    //短信发送成功返回True，失败返回false
    //if (!$resp)

    if ($resp && $resp->result)
    {
        return array('status'=>1 , 'msg'=>$resp->sub_msg);
    }
    else
    {
        return array('status'=>-1 , 'msg'=>$resp->sub_msg.' ,msg :'.$resp->msg.' subcode:'.$resp->sub_code);
    }
}
