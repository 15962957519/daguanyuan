<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;
use app\dataapi\model\Goods;
use app\Home\Logic\UsersLogic;
use think\Cache;
use think\Log;
use think\App;
use app\dataapi\server\AliyunSdkMsg;
use app\dataapi\server\Cache as selfcache;
if (!function_exists('M')) {
    /**
     * 兼容以前3.2的单字母单数 M
     * @param string $name 表名
     * @return DB对象
     */
    function M($name = '')
    {
        if(!empty($name))
        {
            return Db::name($name);
        }
    }
}




/**
 * 支付完成修改订单
 * @param $order_sn 订单号
 * @param array $ext 额外参数
 * @return bool|void
 */
function update_pay_status($order_sn,$ext=array())
{
    if(stripos($order_sn,'recharge') !== false){
        //用户在线充值
        $count = M('recharge')->where("order_sn = :order_sn and pay_status = 0")->bind(['order_sn'=>$order_sn])->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0) return false;
        $order = M('recharge')->where("order_sn", $order_sn)->find();
        M('recharge')->where("order_sn",$order_sn)->save(array('pay_status'=>1,'pay_time'=>time()));
        accountLog($order['user_id'],$order['account'],0,'会员在线充值');
    }else{
        // 如果这笔订单已经处理过了
        $count = M('order')->where("order_sn = :order_sn and pay_status = 0")->bind(['order_sn'=>$order_sn])->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0) return false;
        // 找出对应的订单
        $order = M('order')->where("order_sn",$order_sn)->find();
        // 修改支付状态  已支付
        M('order')->where("order_sn", $order_sn)->save(array('pay_status'=>1,'pay_time'=>time()));
        // 减少对应商品区的库存
        minus_stock($order['order_id']);
        // 给他升级, 根据order表查看消费记录 给他会员等级升级 修改他的折扣 和 总金额
        update_user_level($order['user_id']);
        // 记录订单操作日志
        if(array_key_exists('admin_id',$ext)){
            logOrder($order['order_id'],$ext['note'],'付款成功',$ext['admin_id']);
        }else{
            logOrder($order['order_id'],'订单付款成功','付款成功',$order['user_id']);
        }
        //分销设置
        M('rebate_log')->where("order_id" ,$order['order_id'])->save(array('status'=>1));
        // 成为分销商条件
        $distribut_condition = tpCache('distribut.condition');
        if($distribut_condition == 1)  // 购买商品区付款才可以成为分销商
            M('users')->where("user_id", $order['user_id'])->save(array('is_distribut'=>1));

        //用户支付, 发送短信给商家
        $res = checkEnableSendSms("4");
        if(!$res || $res['status'] !=1) return ;

        $sender = tpCache("shop_info.mobile");
        if(empty($sender))return;
        $params = array('order_sn'=>$order_sn);
        sendSms("4", $sender, $params);
    }

}


/**
 * 记录帐户变动
 * @param   int     $user_id        用户id
 * @param   float   $user_money     可用余额变动
 * @param   int     $pay_points     消费积分变动
 * @param   string  $desc    变动说明
 * @param   float   distribut_money 分佣金额
 * @return  bool
 */
function accountLog($user_id, $user_money = 0,$pay_points = 0, $desc = '',$distribut_money = 0){
    /* 插入帐户变动记录 */
    $account_log = array(
        'user_id'       => $user_id,
        'user_money'    => $user_money,
        'pay_points'    => $pay_points,
        'change_time'   => time(),
        'desc'   => $desc,
    );
    /* 更新用户信息 */
    $sql = "UPDATE __PREFIX__users SET user_money = user_money + $user_money," .
        " pay_points = pay_points + $pay_points, distribut_money = distribut_money + $distribut_money WHERE user_id = $user_id";
    if( DB::execute($sql)){
        M('account_log')->add($account_log);
        return true;
    }else{
        return false;
    }
}
/**
 * 获取短信配置信息
 * @param unknown $key
 * @return string
 */
function smsConfigValueScene($scene){

    $keys = array(
        'sms_product'=>'sms_product' ,        //产品名称
        1 => 'regis_sms_enable' ,                //1 : 用户注册
        2=>'forget_pwd_sms_enable',          //2 : 用户找回密码
        3=>'order_add_sms_enable',            //3. 客户下单
        4=>'order_pay_sms_enable',             //4.客户支付
        5=>'order_shipping_sms_enable' ,    //5.商家发货
        6=> 'bind_mobile_sms_enable'         //6.修改手机号码
    );

    $key = $keys[$scene];
    $config = tpCache('sms');
    $value = $config[$key];
    return $value;
}


if (!function_exists('F')) {
    /**
     * 兼容以前3.2的单字母单数 F
     * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
     * @param mixed $value 缓存值
     * @param mixed $path 缓存参数
     * @return mixed
     */
    function F($name,$value='',$path='') {
        if(!empty($value))
            Cache::set($name,$value);
        else
            return Cache::get($name);
    }
}

/**
 * 根据 order_goods 表扣除商品区库存
 * @param type $order_id  订单id
 */
function minus_stock($order_id){
    $orderGoodsArr = M('OrderGoods')->where("order_id", $order_id)->select();
    foreach($orderGoodsArr as $key => $val)
    {
        // 有选择规格的商品区
        if(!empty($val['spec_key']))
        {   // 先到规格表里面扣除数量 再重新刷新一个 这件商品区的总数量
            M('SpecGoodsPrice')->where("goods_id = :goods_id and `key` = :key")->bind(['goods_id'=>$val['goods_id'],'key'=>$val['spec_key']])->setDec('store_count',$val['goods_num']);
            refresh_stock($val['goods_id']);
        }else{
            M('Goods')->where("goods_id", $val['goods_id'])->setDec('store_count',$val['goods_num']); // 直接扣除商品区总数量
        }
        M('Goods')->where("goods_id", $val['goods_id'])->setInc('sales_sum',$val['goods_num']); // 增加商品区销售量
        //更新活动商品区购买量
        if($val['prom_type']==1 || $val['prom_type']==2){
            $prom = get_goods_promotion($val['goods_id']);
            if($prom['is_end']==0){
                $tb = $val['prom_type']==1 ? 'flash_sale' : 'group_buy';
                M($tb)->where("id", $val['prom_id'])->setInc('buy_num',$val['goods_num']);
                M($tb)->where("id", $val['prom_id'])->setInc('order_num');
            }
        }
    }
}



/**
 * 更新会员等级,折扣，消费总额
 * @param $user_id  用户ID
 * @return boolean
 */
function update_user_level($user_id){
    $level_info = M('user_level')->order('level_id')->select();
    $total_amount = M('order')->where("user_id=:user_id AND pay_status=1 and order_status not in (3,5)")->bind(['user_id'=>$user_id])->sum('order_amount');
    if($level_info){
        foreach($level_info as $k=>$v){
            if($total_amount >= $v['amount']){
                $level = $level_info[$k]['level_id'];
                $discount = $level_info[$k]['discount']/100;
            }
        }
        $user = session('user');
        $updata['total_amount'] = $total_amount;//更新累计修复额度
        //累计额度达到新等级，更新会员折扣
        if(isset($level) && $level>$user['level']){
            $updata['level'] = $level;
            $updata['discount'] = $discount;
        }
        M('users')->where("user_id", $user_id)->save($updata);
    }
}



/**
 * 订单操作日志
 * 参数示例
 * @param type $order_id  订单id
 * @param type $action_note 操作备注
 * @param type $status_desc 操作状态  提交订单, 付款成功, 取消, 等待收货, 完成
 * @param type $user_id  用户id 默认为管理员
 * @return boolean
 */
function logOrder($order_id,$action_note,$status_desc,$user_id = 0)
{
    $status_desc_arr = array('提交订单', '付款成功', '取消', '等待收货', '完成','退货');
    // if(!in_array($status_desc, $status_desc_arr))
    // return false;

    $order = M('order')->where("order_id", $order_id)->find();
    $action_info = array(
        'order_id'        =>$order_id,
        'action_user'     =>$user_id,
        'order_status'    =>$order['order_status'],
        'shipping_status' =>$order['shipping_status'],
        'pay_status'      =>$order['pay_status'],
        'action_note'     => $action_note,
        'status_desc'     =>$status_desc, //''
        'log_time'        =>time(),
    );
    return M('order_action')->add($action_info);
}


/**
 * 检测是否能够发送短信
 * @param unknown $scene
 * @return multitype:number string
 */
function checkEnableSendSms($scene){

    $smsEnable = smsConfigValueScene($scene);
    $sendScenes = config('SEND_SCENE');
    $sceneName = $sendScenes[$scene][0];

    if(!$smsEnable){
        return array("status"=>-1,"msg"=>"['$sceneName']短信模板已经关闭'");
    }

    //判断是否添加"注册模板"
    $size = M('sms_template')->where("send_scene=$scene")->count('tpl_id');
    if(!$size){
        return array("status"=>-1,"msg"=>"请先添加['$sceneName']短信模板");
    }

    return array("status"=>1,"msg"=>"可以发送短信");
}



/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if($ssl){
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}


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


//异常检测 删除
function cachecatchrm($name, $cacedrivename='redis')
{
    if ($cacedrivename == 'redis') {
        if (extension_loaded('redis')) {
            try {
                $b = Cache::store('redis')->rm(md5($name));
            } catch (\Exception $e) {
                $b = Cache::store('file')->rm(md5($name));
                App::$debug &&     Log::record('[ CACHE ] REDIS NOT ABLE ', 'info');
            }
        } else {
            return false;
        }
    } elseif ($cacedrivename == 'file') {
        $b = Cache::store('file')->rm(md5($name));
    }
    return $b;
}

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



if(!function_exists('cachecatchsdecuser')) {
//异常检测 减少
    function cachecatchsdecuser($name, $memberSharkedCount, $outtime)
    {
        $tempproductrandone = Cache::store('file')->get(md5($name));
        if ($tempproductrandone === false) {
            Cache::store('file')->set(md5($name), $memberSharkedCount, $outtime);
            Cache::store('file')->dec(md5($name));
            return $memberSharkedCount - 1;
        }
        if ($tempproductrandone <= 0) {
            return 0;
        }
        return $tempproductrandone;
    }
}

if(!function_exists('filterwname')) {
//异常检测 减少
    function filterwname($name)
    {
        if (!empty($name)) {
            $name= preg_replace('/([a-zA-Z\d_]{5,})/', '****', $name);
            $name= preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}',$name);
        }
        return $name;
    }
}


if(!function_exists('getusercollectlistsdbobj')) {
    function getusercollectlistsdbobj(int $goods_id,$db=null,$dbuser=null){
        $collectdata = $db->name('goods_collect01')->field('collect_id,user_gooods_id,goods_id,user_id')->where(['goods_id' => $goods_id])->whereNull('delete_time')->paginate(16, false, ['page' => 1]);
        //获取用户头像和id
        if($collectdata){
            $collectdata =$collectdata->toArray();
        }
        $user_id_ids=  array_column($collectdata['data'],'user_id');
        $user_id_ids= array_unique($user_id_ids);
        $user_data =[];
        $users =  $dbuser->whereIn('user_id',$user_id_ids)->field(['user_id','head_pic','id'])->select();
        foreach ($users as $user) {
            $user_data[$user['user_id']] =$user;
        }
        foreach ($collectdata['data'] as &$v){
            $v['head_pic']=$user_data[$v['user_id']]['head_pic']??'';
        }
        return  $collectdata;
    }
}



if(!function_exists('getusercollectlists')) {
    function getusercollectlists(int $goods_id){
        $collectdata = Db::connect('mycat104')->name('goods_collect01')->field('collect_id,user_gooods_id,goods_id,user_id')->where(['goods_id' => $goods_id])->whereNull('delete_time')->paginate(16, false, ['page' => 1]);
        //获取用户头像和id
        if($collectdata){
            $collectdata =$collectdata->toArray();
        }
        $user_id_ids=  array_column($collectdata['data'],'user_id');
        $user_id_ids= array_unique($user_id_ids);
        $user_data =[];
        Db::name('third_users')->field('user_id,head_pic,id')->whereIn('user_id',$user_id_ids)->whereNull('delete_time')->chunk(8, function($users) use(&$user_data){
            foreach ($users as $user) {
                $user_data[$user['user_id']] =$user;
            }
        },'id');
        foreach ($collectdata['data'] as &$v){
            $v['head_pic']=$user_data[$v['user_id']]['head_pic']??'';
        }
        return  $collectdata;
    }
}



if(!function_exists('getusercollectcount')) {
    function getusercollectcount(int $goods_id){
        $likecount= Db::connect('mycat104')->name('goods_collect01')->where(['goods_id' => $goods_id])->whereNull('delete_time')->count();
        return  $likecount;
    }
}


if(!function_exists('ihtmlspecialchars')) {
    function ihtmlspecialchars($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = ihtmlspecialchars($val);
            }
        } else {
            $string = preg_replace('/&amp;((#(d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&1',
                str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
        }
        return $string;
    }
}


if(!function_exists('insetintogoolect')) {
    function insetintogoolect(array $data){
        $goods_collect = Db::connect('mycat104')->name('goods_collect01')->insertGetId($data);
        return  $goods_collect;
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



if(!function_exists('realApi_10SendSMS')) {

//    /**
//     * 为照顾新手开发者 方便调试, 此方法每一行加以注释说明
//     * 发送短信
//     * @param $mobile  手机号码
//     * @param $smsSign    短信签名 必须
//     * @param smsParam   短信模板 必须
//     * @param $templateCode    短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
//     * @return bool    短信发送成功返回true失败返回false
//     */
    function realApi_10SendSMS($mobile, $smsSign, $templateCode,$checkcode)
    {
        $config = tbCache('sms');
        $demo = new AliyunSdkMsg(
            $config['sms_appkey'],
            $config['sms_secretKey']
        );
        $response = $demo->sendSms(
            $smsSign, // 短信签名
            $templateCode, // 短信模板编号
            $mobile, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$checkcode
            ),
            "123"
        );
        if ($response && isset($response->Code) && $response->Code =='OK') {
            return array('status' => 1, 'msg' => '发送成功');
        } else {
            return array('status' => -1, 'msg' => $response->Message??'');
        }
    }

    function filter_Emoji($str)
    {
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }
}


if(!function_exists('getuserinfofromcache')) {
    function getuserinfofromcache(int $user_id)
    {
        $userinfo = Db::name('users')->field('user_id,head_pic,store_level,user_level,nickname,is_authentication')->where('user_id', $user_id)->find();
        $userinfo['store_name'] = Db::name('store_level')->where('store_level_id',$userinfo['store_level'])->value('store_name');

        return $userinfo;
    }
}
