<?php
namespace app\api\controller;
use think\Db;
use think\Hook;
use think\Request;
use think\Session;
use think\Response;
use think\Config;
use app\dataapi\server\Weixin;
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

class Lottery extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }
    //抽奖页面
    public function lotteryGifts(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $pay_points = Db::name('users')->where(['user_id'=>$user_id])->value('pay_points');  //当前我的剩余积分
        $spend_jifen = Db::name('lottery_set')->where(['id'=>1])->value('content');  //每次抽奖消耗积分
        $lotteryGifts = Db::name('lottery_gifts')->where(['ifshow'=>0])->select();  //0:启用 1：禁用
        $lottery_logs = Db::name('lottery_logs')
            ->alias('L')
            ->join('__USERS__ U','L.user_id = U.user_id')
            ->join('__LOTTERY_GIFTS__ G','L.lottery_id = G.lottery_id')
            ->field('U.nickname,U.mobile,G.lottery_name')
            ->where('L.lottery_id','>',1)
            ->order('L.addtime DESC')
            ->limit(10)
            ->select();  //中奖名单  lottery_id = 1谢谢参与（固定）

        Response::create(['data'=>['pay_points'=>$pay_points,'spend_jifen'=>$spend_jifen,'lotteryGifts'=>$lotteryGifts,'lottery_logs'=>$lottery_logs],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //我的抽奖
    public function selfLottery(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $my_gifts = Db::name('lottery_logs')
            ->alias('L')
            ->join('__LOTTERY_GIFTS__ G','L.lottery_id = G.lottery_id')
            ->field('L.lottery_id,L.addtime,G.lottery_name')
            ->where('L.lottery_id','>',1)
            ->order('L.addtime DESC')
            ->limit(10)
            ->select();
        Response::create(['data'=>['my_gifts'=>$my_gifts],'code'=>2001,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //点击立即抽奖（积分减少）
    public function lottryReduce(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $pay_points = Db::name('users')->where(['user_id'=>$user_id])->value('pay_points');  //当前我的剩余积分
        $spend_jifen = Db::name('lottery_set')->where(['id'=>1])->value('content');  //每次抽奖消耗积分
        if($pay_points < $spend_jifen){
            Response::create(['code'=>2001,'message'=>'sorry!积分不足'], 'json')->header($this->header)->send();
        }else{
            Db::name('users')->where(['user_id'=>$user_id])->setDec('pay_points',$spend_jifen);
            Db::name('account_log')->insert(['user_id'=>$user_id,'pay_points'=>'-'.$spend_jifen,'desc'=>'抽奖积分减少']);  //账户积分记录
            Response::create(['code'=>2000,'message'=>'抽奖成功'], 'json')->header($this->header)->send();
        }

    }
    //点击领取奖品（不领取无记录 除外）
    public function lotteryLogs(Request $request){
        $data['user_id'] = (int)$this->user_data['user_id']; //当前登录者user_id;(中奖用户id)
        $data['lottery_id']  = $request->param('lottery_id'); //奖品ID
        $data['addtime'] = time();  //中奖领取时间
        $data['day'] = date('Ymd');  //中奖日期（20170303）方便查询
        $re = Db::name('lottery_logs')->insert($data);
        if($re){
            $lottery_gifts = Db::name('lottery_gifts')->where(['lottery_id'=>$data['lottery_id']])->field('lottery_type,lottery_jifen')->find();  //1:现金红包；2：优惠券；3：积分；4：活动
            if($lottery_gifts['lottery_type'] == 3){    //积分类型即时加入
                Db::name('users')->where(['user_id'=>$data['user_id']])->setInc('pay_points',$lottery_gifts['lottery_jifen']);
                Db::name('account_log')->insert(['user_id'=>$data['user_id'],'pay_points'=>$lottery_gifts['lottery_jifen'],'desc'=>'抽奖积分领取']);  //抽奖积分领取
            }

            Response::create(['code'=>2000,'message'=>'领取成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>2001,'message'=>'领取失败'], 'json')->header($this->header)->send();
        }

    }

}