<?php
namespace app\dataapi\controller;
use think\Db;
use think\Hook;
use think\Request;
use think\Response;
use app\dataapi\server\BidOrderServer;
use app\dataapi\controller\BaseApi;
use app\dataapi\server\ProductServer;
use weixinpayment\JsApiPay;
use weixinpayment\lib\WxPayUnifiedOrder;
use weixinpayment\lib\WxPayApii;
use weixinpayment\lib\WxPayConfig;
use think\Config;
use app\dataapi\controller\JobQeue;
use think\Log;
use app\dataapi\lib\Image;
class BidProduct extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }
    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        $hot_goods =  Db::name('goods')->where('is_hot',1)->where('is_on_sale',1)->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();
        return ['data'=>['hot_goods'=>$hot_goods],'code'=>1,'message'=>'获取成功'];
    }

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function bidProudctById(Request $request)
    {
        $good_id = (int)$request->param('good_id');
        $userid = (int)$this->user_data['user_id'];
        $page = (int) (int)$request->param('page');
        $page =$page??1;

        $bid_price = floatval($request->param('bid_price'));

        if ($good_id == 0 || $userid == 0 || $bid_price == 0.00) {
            Response::create(['code' => '4008', 'message' => '出价失败'], 'json')->header($this->header)->send();
        }
        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->add($userid, $good_id, $request);
        if ($data > 0) {
            //返回最新出价列表
            $datalist = $BidOrderServer->listsarray($good_id, 15,false,['page'=>$page]);
            //发现是平台藏品的话 立即在规定时间顶上去
            $productserverobj = new ProductServer();
           $tempflaggood =  $productserverobj->getOneProduct($good_id);
            if($tempflaggood['is_toplatform']==1 && $tempflaggood['goods_status']!=2){
                try{
                    $JobQeueobj =  new  JobQeue();
                    //规定何时执行
                    $currenttimeunixstamp=time();
                    if(($tempflaggood['endTime']-10)>$currenttimeunixstamp){
                        $mic = mt_rand($currenttimeunixstamp, $tempflaggood['endTime']);
                        $JobQeueobj->autoBidGoodsForFansererVhost($good_id,$mic);
                    }else{
                        $this->immediatelyBid($good_id);
                    }
                }catch(\RuntimeException $e){
                    Log::record('日志信息','info'.$e);
                }
            }
            Response::create(['data' => $datalist,'lastnum'=>$bid_price, 'code' => '2000', 'message' => '出价成功'], 'json')->header($this->header)->send();
        }
        Response::create(['code' => '4000', 'message' => '出价失败'], 'json')->header($this->header)->send();
    }

    //立即出价
    private function immediatelyBid(int $good_id){
        $Image =new Image();
        $datauser = $Image->flagimage();
        $userid =$datauser['user_id'];
        if($userid<=0){
            return false;
        }
        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->vhostAddByCli($userid, $good_id);
        $mumber=1;
        while($mumber>0){
            $UserServer = new UserServer();
            if($userid>0 && $good_id>0) {
                $flag = $UserServer->userUpdateLike($userid, ['goods_id'=>$good_id]);
                if(!$flag){
                    continue;
                }
            }
            $mumber--;
        }
    }

    //出价信息列表
    public  function getProductById(Request $request){

        $good_id=(int)$request->param('good_id');
        $order_id=(int)$request->param('order_id');
        $order_sn=$request->param('order_sn')??'';
        $flag=(bool)$request->param('is_finalpayment');
        $user_id= (int)$this->user_data['user_id'];

        $BidOrderServer = new BidOrderServer();
        if($good_id==0){
            //虚拟一个
            $data=['goods_name'=>'支付保证金','order_sn'=>$BidOrderServer->build_order_no(),'goods_id'=>$good_id];
        }else{
            $ProductServer = new  ProductServer();
            $data = $ProductServer->bidGetProductById($good_id,$flag,$user_id,$order_id,$order_sn);
        }


        if(empty($data)){
            Response::create(['data' => $data, 'code' => '4000', 'message' => '没有可以支付的商品区'], 'json')->header($this->header)->send();
        }
        //①、获取用户openid
        $tools = new JsApiPay();
        //  $openId = $tools->GetOpenid();
        $openId = $this->user_data['openid']??'';
        if($flag){
            $bid_price =$data['bid_price']*100;
        }else{
            $bid_price =(string)100*100;
        }

        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($data['goods_name']);
        $input->SetAttach($data['goods_name']);
        if($flag){
            $input->SetOut_trade_no($data['order_sn']);
            $input->SetNotify_url(Config::get('weixin_api.finalorder'));
        }else{
            $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
            $input->SetNotify_url(Config::get('weixin_api.notify'));
        }



        $input->SetTotal_fee($bid_price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($data['goods_id'].$data['order_sn']);

        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApii::unifiedOrder($input);

        if(isset($order['return_code']) && ($order['return_code']=='FAIL')){
            Response::create(['data' => [], 'code' => '4000', 'message' => '获取作品信息' . $order['return_msg'] . '成功'], 'json')->header($this->header)->send();
        }
        //  $this->printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $tmpp= json_decode($jsApiParameters,true);

//        if($good_id==0){
//            Response::create(['data' => [],'jsApiParameters'=>$tmpp, 'code' => '2000', 'message' => '获取作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
//        }
        if(!empty($data)){
            Response::create(['data' => $data,'jsApiParameters'=>$tmpp, 'code' => '2000', 'message' => '获取作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data' => $data, 'code' => '4000', 'message' => '获取作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
    }

}
