<?php
namespace app\dataapi\controller;

use app\common\lib\Logins;
use app\dataapi\model\Goods;
use app\dataapi\server\OrderLogic;
use think\Controller;
use think\Hook;
use think\Request;

class GenerateOrder extends Controller
{
    use Logins;
    private $user_data;
    private $header;

    public function _initialize()
    {

        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);

        $this->header = ['Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept', 'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST,DELETE,OPTIONS'];

    }


    /**
     * 生成一个拍买记录
     * @return mixed
     */
    public function purchaseRecord(Request $request)
    {

        $goods_id = $request->params('goods_id');//产品id
        $userid = $this->user_data['user_id'];//用户id

        $order_sn = '';//生成订单号
        $goods_price = '';//价格
        $shipping_price = '';//快递费价格
        $total_amount = '';//订单总价格
        $add_time = $_SERVER['REQUEST_TIME'];//订单总价格


        $address_id = $request->params("address_id"); //  收货地址id
        $shipping_code = $request->params("shipping_code"); //  物流编号
        $invoice_title = $request->params('invoice_title'); // 发票名称 默认个人名字
        $couponTypeSelect = $request->params("couponTypeSelect"); //  优惠券类型  1 下拉框选择优惠券 2 输入框输入优惠券代码
        $coupon_id = $request->params("coupon_id"); //  优惠券id
        $couponCode = $request->params("couponCode"); //  优惠券代码
        $pay_points = $request->params("pay_points")??0; //  使用积分
        $user_money = $request->params("user_money")??0; //  使用余额


        if (!$address_id) exit(json_encode(array('status' => -3, 'msg' => '请先填写收货人信息', 'result' => null))); // 返回结果状态


        // 或者使用助手函数`model`
        $UserAddress = model('UserAddress');


        $address = $UserAddress->where('address_id', $address_id)->find();


        $goodsobj = new Goods();

        $googddata = $goodsobj->get(['goods_id' => $goods_id]);
        $result = calculate_price($this->user_id, $googddata, $shipping_code, 0, $address[province], $address[city], $address[district], $pay_points, $user_money, $coupon_id, $couponCode);

        if ($result['status'] < 0)
            exit(json_encode($result));
        // 订单满额优惠活动
        $order_prom = get_order_promotion($result['result']['order_amount']);
        $result['result']['order_amount'] = $order_prom['order_amount'];
        $result['result']['order_prom_id'] = $order_prom['order_prom_id'];
        $result['result']['order_prom_amount'] = $order_prom['order_prom_amount'];

        $car_price = array(
            'postFee' => $result['result']['shipping_price'], // 物流费
            'couponFee' => $result['result']['coupon_price'], // 优惠券
            'balance' => $result['result']['user_money'], // 使用用户余额
            'pointsFee' => $result['result']['integral_money'], // 积分支付
            'payables' => $result['result']['order_amount'], // 应付金额
            'goodsFee' => $result['result']['goods_price'],// 商品区价格
            'order_prom_id' => $result['result']['order_prom_id'], // 订单优惠活动id
            'order_prom_amount' => $result['result']['order_prom_amount'], // 订单优惠活动优惠了多少钱
        );


        $result = (new OrderLogic())->addOrder($userid, $address_id, $shipping_code, $invoice_title, $coupon_id, $car_price); // 添加订单


    }

}
