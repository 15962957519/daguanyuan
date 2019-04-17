<?php
use app\dataapi\model\Goods;
use app\Home\Logic\UsersLogic;
use think\Cache;
use think\Db;
use think\Log;
use think\App;
use think\Config;
use app\dataapi\server\AliyunSdkMsg;
/**
 * 计算订单金额
 * @param type $user_id 用户id
 * @param type $order_goods 购买的商品区
 * @param type $shipping 物流code
 * @param type $shipping_price 物流费用, 如果传递了物流费用 就不在计算物流费
 * @param type $province 省份
 * @param type $city 城市
 * @param type $district 县
 * @param type $pay_points 积分
 * @param type $user_money 余额
 * @param type $coupon_id 优惠券
 * @param type $couponCode 优惠码
 */

function calculate_price(int $user_id = 0, array $order_goods, $shipping_code = '', $shipping_price = 0, $province = 0, $city = 0, $district = 0, $pay_points = 0, $user_money = 0, $coupon_id = 0, $couponCode = '')
{

    if (count($order_goods) === count($order_goods, 1)) {
        $order_goods[] = $order_goods;
    }


    $cartLogic = new \app\home\logic\CartLogic();


    $userLogic = new UsersLogic();
    $user = $userLogic->get_info($user_id); // 获取用户信息

    if (empty($order_goods)) {
        return array('status' => -9, 'msg' => '商品区列表不能为空', 'result' => '');
    }
    $goodsobj = new Goods();
    $goods_id_arr = array_column($order_goods, 'goods_id');
    $goods_arr = $goodsobj->where("goods_id in(" . implode(',', $goods_id_arr) . ")")->cache(true, TPSHOP_CACHE_TIME)->getField('goods_id,weight,market_price,is_free_shipping'); // 商品区id 和重量对应的键值对

    $goods_weight = 0;
    $goods_price = 0.00;
    $cut_fee = 0.00;
    $anum = 0;
    foreach ($order_goods as $key => $val) {
        // 如果传递过来的商品区列表没有定义会员价
        if (!array_key_exists('member_goods_price', $val)) {
            $user['discount'] = $user['discount'] ? $user['discount'] : 1; // 会员折扣 不能为 0
            $order_goods[$key]['member_goods_price'] = $val['member_goods_price'] = $val['goods_price'] * $user['discount'];
        }
        //如果商品区不是包邮的

        if ($goods_arr[$val['goods_id']]['is_free_shipping'] == 0)
            $goods_weight += $goods_arr[$val['goods_id']]['weight'] * $val['goods_num']; //累积商品区重量 每种商品区的重量 * 数量

        $order_goods[$key]['goods_fee'] = $val['goods_num'] * $val['member_goods_price'];    // 小计
        $order_goods[$key]['store_count'] = getGoodNum($val['goods_id'], $val['spec_key']); // 最多可购买的库存数量
        if ($order_goods[$key]['store_count'] <= 0)
            return array('status' => -10, 'msg' => $order_goods[$key]['goods_name'] . "库存不足,请重新下单", 'result' => '');

        $goods_price += $order_goods[$key]['goods_fee']; // 商品区总价
        $cut_fee += $val['goods_num'] * $val['market_price'] - $val['goods_num'] * $val['member_goods_price']; // 共节约
        $anum += $val['goods_num']; // 购买数量
    }


    // 优惠券处理操作
    $coupon_price = 0;
    if ($coupon_id && $user_id) {
        $coupon_price = $cartLogic->getCouponMoney($user_id, $coupon_id, 1); // 下拉框方式选择优惠券
    }
    if ($couponCode && $user_id) {
        $coupon_result = $cartLogic->getCouponMoneyByCode($couponCode, $goods_price); // 根据 优惠券 号码获取的优惠券
        if ($coupon_result['status'] < 0)
            return $coupon_result;
        $coupon_price = $coupon_result['result'];
    }
    // 处理物流
    if ($shipping_price == 0) {
        $freight_free = tpCache('shopping.freight_free'); // 全场满多少免运费
        if ($freight_free > 0 && $goods_price >= $freight_free) {
            $shipping_price = 0;
        } else {
            $shipping_price = $cartLogic->cart_freight2($shipping_code, $province, $city, $district, $goods_weight);
        }
    }
    if ($pay_points && ($pay_points > $user['pay_points']))
        return array('status' => -5, 'msg' => "你的账户可用积分为:" . $user['pay_points'], 'result' => ''); // 返回结果状态
    if ($user_money && ($user_money > $user['user_money']))
        return array('status' => -6, 'msg' => "你的账户可用余额为:" . $user['user_money'], 'result' => ''); // 返回结果状态

    $order_amount = $goods_price + $shipping_price - $coupon_price; // 应付金额 = 商品区价格 + 物流费 - 优惠券

    $pay_points = ($pay_points / tpCache('shopping.point_rate')); // 积分支付 100 积分等于 1块钱
    $pay_points = ($pay_points > $order_amount) ? $order_amount : $pay_points; // 假设应付 1块钱 而用户输入了 200 积分 2块钱, 那么就让 $pay_points = 1块钱 等同于强制让用户输入1块钱
    $order_amount = $order_amount - $pay_points; //  积分抵消应付金额

    $user_money = ($user_money > $order_amount) ? $order_amount : $user_money;  // 余额支付原理等同于积分
    $order_amount = $order_amount - $user_money; //  余额支付抵应付金额

    $total_amount = $goods_price + $shipping_price;
    //订单总价  应付金额  物流费  商品区总价 节约金额 共多少件商品区 积分  余额  优惠券
    $result = array(
        'total_amount' => $total_amount, // 商品区总价
        'order_amount' => $order_amount, // 应付金额
        'shipping_price' => $shipping_price, // 物流费
        'goods_price' => $goods_price, // 商品区总价
        'cut_fee' => $cut_fee, // 共节约多少钱
        'anum' => $anum, // 商品区总共数量
        'integral_money' => $pay_points,  // 积分抵消金额
        'user_money' => $user_money, // 使用余额
        'coupon_price' => $coupon_price,// 优惠券抵消金额
        'order_goods' => $order_goods, // 商品区列表 多加几个字段原样返回
    );
    return array('status' => 1, 'msg' => "计算价钱成功", 'result' => $result); // 返回结果状态
}


/**
 * 获取商品区库存
 * @param type $goods_id 商品区id
 * @param type $key 库存 key
 */
function getGoodNum($goods_id, $key)
{
    if (!empty($key))
        return Db::name("SpecGoodsPrice")->where(['goods_id' => $goods_id, 'key' => $key])->getField('store_count');
    else
        return Db::name("Goods")->where("goods_id", $goods_id)->getField('store_count');
}


if(!function_exists('realSendSMS')) {

    function realSendSMS($mobile, $smsSign, $smsParam, $templateCode, $checkcode)
    {
        //时区设置：亚洲/上海
        date_default_timezone_set('Asia/Shanghai');
        //这个是你下面实例化的类
        vendor('Alidayu.TopSdk');
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
        $config = tbCache('sms');
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

        $c->format = 'json';
        //发送短信
        $resp = $c->execute($req);
        //短信发送成功返回True，失败返回false
        //if (!$resp)
        if ($resp && isset($resp->result)) {
            return array('status' => 1, 'msg' => '发送成功');
        } else {
            switch ($resp->sub_code) {
                case 'isv.OUT_OF_SERVICE':
                    $resp->sub_code = '业务停机';
                    break;
                case 'isv.PRODUCT_UNSUBSCRIBE':
                    $resp->sub_code = '产品服务未开通';
                    break;
                case 'isv.MOBILE_COUNT_OVER_LIMIT':
                    $resp->sub_code = '手机号码数量超过限制';
                    break;
                case 'isv.MOBILE_NUMBER_ILLEGAL':
                    $resp->sub_code = '手机号码格式错误';
                    break;
                case 'isv.PARAM_LENGTH_LIMIT':
                    $resp->sub_code = '业务停机';
                    break;
                case 'isv.OUT_OF_SERVICE':
                    $resp->sub_code = '变量长度受限';
                    break;
                case 'isv.BUSINESS_LIMIT_CONTROL':
                    $resp->sub_code = '允许每分钟1条，累计每小时7条';
                    break;
                case 'isv.AMOUNT_NOT_ENOUGH':
                    $resp->sub_code = '	余额不足';
                    break;
                default:
                    $resp->sub_code = '发送失败';
                    break;
            }
            return array('status' => -1, 'msg' => $resp->sub_code);
        }
    }

}

if(!function_exists('tbCache')) {
    /**
     * 获取缓存或者更新缓存
     * @param string $config_key 缓存文件名称
     * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
     * @return array or string or bool
     */
    function tbCache($config_key, $data = array())
    {
        $param = explode('.', $config_key);
        if (empty($data)) {
            // 切换到file操作
            $config = Cache::store('file')->get($param[0]);

            if (empty($config)) {
                //缓存文件不存在就读取数据库
                $res = Db::name('config')->where("inc_type", $param[0])->select();
                if ($res) {
                    foreach ($res as $k => $val) {
                        $config[$val['name']] = $val['value'];
                    }
                    Cache::store('file')->get($param[0], $config);
                }
            }
            if (count($param) > 1) {
                return $config[$param[1]];
            } else {
                return $config;
            }
        } else {
            //更新缓存
            $result = Db::name('config')->where("inc_type", $param[0])->select();
            if ($result) {
                foreach ($result as $val) {
                    $temp[$val['name']] = $val['value'];
                }
                foreach ($data as $k => $v) {
                    $newArr = array('name' => $k, 'value' => trim($v), 'inc_type' => $param[0]);
                    if (!isset($temp[$k])) {
                        Db::name('config')->insert($newArr);//新key数据插入数据库
                    } else {
                        if ($v != $temp[$k])
                            Db::name('config')->where("name", $k)->update($newArr);//缓存key存在且值有变更新此项
                    }
                }
                //更新后的数据库记录
                $newRes = Db::name('config')->where("inc_type", $param[0])->select();
                foreach ($newRes as $rs) {
                    $newData[$rs['name']] = $rs['value'];
                }
            } else {
                foreach ($data as $k => $v) {
                    $newArr[] = array('name' => $k, 'value' => trim($v), 'inc_type' => $param[0]);
                }

                Db::name('config')->insertAll($newArr);
                $newData = $data;
            }
            return Cache::store('file')->get($param[0], $newData);
        }
    }

}
/*
 * 获取用户地址列表
 */
function get_user_address_list($user_id)
{
    $lists = M('user_address')->where(array('user_id' => $user_id))->select();
    return $lists;
}

if(!function_exists('cachecatchg')){
    function cachecatchg($name, $pre = '')
    {
        if (extension_loaded('redis')) {
            try {
                $b = Cache::store('redis')->get($pre . md5($name));
            } catch (\Exception $e) {
                App::$debug &&    Log::record('[ CACHE ] REDIS NOT ABLE ', 'info');
                $b = Cache::store('file')->get($pre . md5($name));
            }
        } else {
            $b = Cache::store('file')->get($pre . md5($name));
        }
        return $b;
    }
}

if(!function_exists('cachecatchset')) {
//异常检测
    function cachecatchset($name, $value, $pre = '', $timeout = 200)
    {
        if (extension_loaded('redis')) {
            try {
                $b = Cache::store('redis')->set($pre . md5($name), $value, $timeout);
            } catch (\Exception $e) {
                App::$debug &&    Log::record('[ CACHE ] REDIS NOT ABLE ', 'info');
                $b = Cache::store('file')->set($pre . md5($name), $value, $timeout);
            }
        } else {
            $b = Cache::store('file')->set($pre . md5($name), $value, $timeout);
        }
        return $b;
    }
}

if(!function_exists('cachecatchrm')) {
//异常检测 删除
    function cachecatchrm($name, $cacedrivename='redis')
    {
        if ($cacedrivename == 'redis') {
            if (extension_loaded('redis')) {
                try {
                    $b = Cache::store('redis')->rm(md5($name));
                } catch (\Exception $e) {
                    $b = Cache::store('file')->rm(md5($name));
                    App::$debug &&    Log::record('[ CACHE ] REDIS NOT ABLE ', 'info');
                }
            } else {
                return false;
            }
        } elseif ($cacedrivename == 'file') {
            $b = Cache::store('file')->rm(md5($name));
        }
        return $b;
    }
}


    if(!function_exists('cachecatchsdec')) {
//异常检测 减少
        function cachecatchsdec($name, $memberSharkedCount, $outtime)
        {
            $tempproductrandone = Cache::store('file')->get(md5($name));
            if($tempproductrandone===false){
                Cache::store('file')->set(md5( $name), $memberSharkedCount, $outtime);
                return $memberSharkedCount;
            }
            if($tempproductrandone<=0){
                return 0;
            }
            if ($tempproductrandone !== false) {
                Cache::store('file')->dec(md5( $name));
            }
            return $tempproductrandone;
        }


    }



if(!function_exists('flagimageAll')) {
    function flagimageAll($rangmax,$limit=1)
    {
        static $maxid=0;
        static $minid=0;
        static $goods_idmax=0;
        static $configdata=null;
        if(empty($configdata)){
            $configdata = Config::get('vhostmember');
        }
        if ($rangmax >= 3) {
            $Goodsobj = Db::name('Goods');
            if($goods_idmax==0){
                $goods_idmax = $Goodsobj->max('goods_id');
            }
            $ttmaxid =  mt_rand(1,$goods_idmax);
            $datauser = $Goodsobj
                ->alias('a')
                ->join('users q', 'a.user_id=q.user_id')
                ->where('goods_id','>=',$ttmaxid)
                ->whereNull('a.delete_time')
                ->whereNull('q.delete_time')
                ->field('q.head_pic,q.user_id')
                ->limit($limit)
                ->select();
        } else {
            if(!isset($maxid) ||  $maxid==0){
                $maxid =    Db::table( $configdata['membertime']['membername'])->max('id');
            }
            if(!isset($minid) ||  $minid==0){
                $minid =    Db::table( $configdata['membertime']['membername'])->min('id');
            }
            if($maxid>$minid && $minid>0){
                $ttmaxid =  mt_rand($minid,$maxid);
                $datauser = Db::table($configdata['membertime']['membername'])->field('user_id,head_pic')->where('id','>=',$ttmaxid)->limit($limit)->select();
            }else{
                return null;
            }
        }
        return $datauser;
    }
}
