<?php
namespace app\api\controller;
use think\Controller;
use think\Exception;
use think\Response;
use think\Hook;
use app\api\server\UsersServer;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Request;
use think\Db;
use think\Log;

class BalanceRecharge extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }

    /**[ 1:充值；2：认证押金；3：店铺升级；4：保证金 ]
     * @param Request $request
     * @return \think\response\Json
     */
    public function index(Request $request){
        $user_id = $this->user_data['user_id']; //登录者user_id;
        $openid = $this->user_data['openid']; //登录者openid;
        $nickname = $this->user_data['nickname']; //登录者微信昵称;
        $order_sn = $request->param('order_sn');   //充值单号
        $account = $request->param('order_account');   //如果没有获取到金额  则默认是押金金额
        $status = $request->param('state');   // 1:充值；2：认证；3：店铺升级；4：保证金

        $configobj = Db::name('config');
        if($status == 21){  //个人认证
            $account = $configobj->where('name','identify_person')->value('value');
        }
        if($status == 22){  //企业认证
            $account = $configobj->where('name','identify_company')->value('value');
        }

        if($status == 3){
            //当前用户信息等级
            $usersevers = new UsersServer();
            $user_data = $usersevers->usermsg($user_id);
            $store_level_id = $request->param('store_level_id'); //要升级的店铺等级
            $store_cost = Db::name('store_level')->where(['store_level_id'=>$store_level_id])->value('store_cost');
            $account = $store_cost - $user_data['store_cost']; //计算差价
        }

        if($status == 4){
            $account = $configobj->where('name','caution_money')->value('value');
        }
        //查一下单号是否存在
        if(false == Db::name('recharge')->where(['order_sn'=>$order_sn])->find()){
            $data = ['user_id'=>$user_id,'nickname'=>$nickname,'order_sn'=>$order_sn,'ctime'=>time(),'account'=>$account,'pay_name'=>'jsapi微信支付','status'=>$status];
            Db::name('recharge')->insert($data);  //先插入充值订单
        }
        //获取微信配置
        $wxConf = config('wechat');
        //实例化easyWeChat
        $wxApp = new Application($wxConf);
        //支付订单参数
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => 'fanghua',
            'detail'           => 'detail',    //详情
            'out_trade_no'     => $order_sn,     //'自己生成自己站点的唯一单号',
            'total_fee'        => $account*100, // 单位：分// 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'notify_url'       => 'http://api.fanghua.jiuxintangwenhua.com/rechargenotify',  //http://tp5013.com/Pay/Pay/index
            'openid'           => $openid  //openid
        ];
        //初始化订单
        $order = new Order($attributes);
        //实例化支付
        $payment = $wxApp->payment;
        //预支付
        $result = $payment->prepare($order);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
            $json = $payment->configForPayment($prepayId);
            return json(['jsondata'=>$json,'code'=>2000,'result'=>$result]);
        }else{
            return json(['code'=>2001,'msg'=>$result]);
        }
    }

    /**[ 异步通知 ]
     * @param Request $request
     */
    public function notify(Request $request){
            //微信支付notify
            $wxConf = config('wechat');
            $wxApp = new Application($wxConf);
            try{
                $response = $wxApp->payment->handleNotify(function ($notify,$successful) {
                    if($successful){
                        $out_trade_no = $notify->out_trade_no; //订单号
                        $total_fee = $notify->total_fee;   //支付金额
                        $total_fee2 = $total_fee/100;
                        //变更充值状态   0:待支付 1:充值成功 2:交易关闭
                        $data = ['pay_status' => 1, 'pay_time' => time()];
                        $recharge = Db::name('recharge')->where(['order_sn' => $out_trade_no])->field('user_id,pay_status,status')->find();
                        if($recharge['status'] == 1 && $recharge['pay_status'] == 0){
                            $account_log = ['user_id'=>$recharge['user_id'],'user_money'=>$total_fee2,'pay_points'=>$total_fee2,'change_time'=>time(),'desc'=>'余额充值'];  //账户记录
                            Db::name('users')->where(['user_id'=>$recharge['user_id']])->setInc('user_money',$total_fee2);
                            Db::name('recharge')->where(['order_sn' => $out_trade_no])->update($data);
                            Db::name('account_log')->insert($account_log);

                        }else if($recharge['status'] == 21  && $recharge['pay_status'] == 0){
                            $account_log = ['user_id'=>$recharge['user_id'],'user_money'=>$total_fee2,'pay_points'=>$total_fee2,'change_time'=>time(),'desc'=>'个人认证'];  //账户记录
                            Db::name('user_verifty')->where(['user_id'=>$recharge['user_id']])->update(['is_pay'=>1]);
                            Db::name('recharge')->where(['order_sn' => $out_trade_no])->update($data);
                            Db::name('account_log')->insert($account_log);
                        }else if($recharge['status'] == 22  && $recharge['pay_status'] == 0){
                            $account_log = ['user_id'=>$recharge['user_id'],'user_money'=>$total_fee2,'pay_points'=>$total_fee2,'change_time'=>time(),'desc'=>'企业认证'];  //账户记录
                            Db::name('user_verifty')->where(['user_id'=>$recharge['user_id']])->update(['is_pay'=>1]);
                            Db::name('recharge')->where(['order_sn' => $out_trade_no])->update($data);
                            Db::name('account_log')->insert($account_log);
                        }else if($recharge['status'] == 3 && $recharge['pay_status'] == 0){
                            $account_log = ['user_id'=>$recharge['user_id'],'user_money'=>$total_fee2,'pay_points'=>$total_fee2,'change_time'=>time(),'desc'=>'店铺升级'];  //账户记录
                            Db::name('recharge')->where(['order_sn' => $out_trade_no])->update($data);
                            Db::name('account_log')->insert($account_log);
                        }else if($recharge['status'] == 4 && $recharge['pay_status'] == 0){
                            $account_log = ['user_id'=>$recharge['user_id'],'user_money'=>$total_fee2,'pay_points'=>$total_fee2,'change_time'=>time(),'desc'=>'保证金'];  //账户记录
                            Db::name('recharge')->where(['order_sn' => $out_trade_no])->update($data);
                            Db::name('account_log')->insert($account_log);
                        }

                        return true;
                    }
                    return false;

                });
            }catch(\EasyWeChat\Core\Exceptions\FaultException $e){



            }

    }

    /**
     * [ 充值记录 ]
     */
    public function rechargeLog(){
        $user_id = $this->user_data['user_id']; //登录者user_id;
        $where = [
            'user_id' => $user_id,
            'pay_status' => 1,
            'status' => 1
        ];
        $rechargeLog = Db::name('recharge')
            ->field('order_sn,ctime,account,pay_status')
            ->where($where)
            ->limit(20)->select();
        Response::create(['rechargeLog'=>$rechargeLog, 'code' => 2000, 'message' => '充值记录'], 'json')->header($this->header)->send();

    }

    /**[ 提交提现申请 ]
     * @param Request $request
     */
    public function applyTixian(Request $request){
        $user_id = $this->user_data['user_id']; //登录者user_id;
        $money = (int)$request->param('money'); //提现金额
        $account_bank = $request->param('account_bank'); //银行账号  默认手机号
        $account_name = $request->param('account_name'); //银行账户名  默认微信昵称
        $apply_number = $request->param('apply_number'); //提现编号
        $bank_name = '微信'; //银行名称  默认微信
        $create_time = time(); //申请日期
        $user_balance = Db::name('users')->field('user_money,frozen_money')->where(['user_id'=>$user_id])->find(); //用户余额 , 冻结金额
        $enable_balance = $user_balance['user_money'] - $user_balance['frozen_money']; //可使用余额

        if($enable_balance < 1){
            Response::create([ 'code' => 2001, 'message' => '提现金额不能低于1元'], 'json')->header($this->header)->send();
        }
        if($money > $enable_balance){
            Response::create([ 'code' => 2001, 'message' => '提现金额不能大于可使用余额！'], 'json')->header($this->header)->send();
        }
        $data = ['user_id'=>$user_id,'create_time'=>$create_time,'money'=>$money,'bank_name'=>$bank_name,'account_bank'=>$account_bank,'account_name'=>$account_name,'apply_number'=>$apply_number];
        Db::startTrans();
        try{
            $result = Db::name('withdrawals')->insert($data);
            Db::name('users')->where(['user_id'=>$user_id])->setInc('frozen_money',$money); //提交金额变成冻结金额
            Db::commit();
            Response::create([ 'code' => 2000, 'message' => '申请提交成功'], 'json')->header($this->header)->send();
        }catch (Exception $e){
            Db::rollback();
            Response::create([ 'code' => 2001, 'message' => '申请提交失败'], 'json')->header($this->header)->send();
        }
    }

    /**
     * [ 提现申请记录 ]
     */
    public function tixianLog(){
        $user_id = $this->user_data['user_id']; //登录者user_id;
        $tixianLog = Db::name('withdrawals')->field('apply_number,create_time,money,status')->where(['user_id'=>$user_id])->limit(20)->select();
        Response::create(['tixianLog'=>$tixianLog, 'code' => 2000, 'message' => '提现记录'], 'json')->header($this->header)->send();

    }

    //我的余额
    public function balance(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $banlance=Db::name('users')->where(array('user_id'=>$user_id))->field('user_money,frozen_money')->find();
        Response::create(['data'=>$banlance,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //余额明细
    public function balance_detail(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $UsersServer = new UsersServer();
        $blance_log = $UsersServer->blance_log($user_id);
        Response::create(['data'=>$blance_log,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();

    }

    //用户是否支付保证金
    public function cautionMoney(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $sicaution = Db::name('recharge')->where(['user_id'=>$user_id,'status'=>4,'pay_status'=>1])->find();
        $user_acution = Db::name('users')->where(['user_id'=>$user_id])->value('is_acution');
        //获取保证金
        $config = tbCache('caution');
        $caution_money = $config['caution_money'];
        if(!empty($sicaution) || $user_acution == 1){
            $message = [
                'code' => 2000,
                'state'=>1,
                'caution_money'=> $caution_money,
                'msg'  =>'保证金已付'
            ];
        }else{
            $message = [
                'code' => 1001,
                'state'=>2,
                'caution_money'=> $caution_money,
                'msg'  =>'保证金未付'
            ];
        }
        Response::create($message, 'json')->header($this->header)->send();
    }

    /**
     * [ 各种支付金额 ]
     */
    public function pay_money(){
        $result = Db::name('config')->where('inc_type','caution')->column('value','name');
        Response::create($result, 'json')->header($this->header)->send();
    }

}