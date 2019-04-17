<?php
namespace app\api\server;
use app\api\model\Goods;
use app\api\model\Users;
use app\api\model\AccountLog;
use think\Db;
use think\Request;
use think\Config;

/*
 * 用户逻辑处理
 */
class UsersServer
{
    //用户账户变动记录
    public function blance_log($user_id){
        $re_log = model('AccountLog')
            ->where(['user_id'=>$user_id,'user_money'=>['<>',0]])
            ->order('log_id DESC')
            ->limit(20)
            ->select()->toArray();
        return $re_log;
    }


    /*
     * 用户是否关注
     * $user_id  用户,$fans_id  粉丝
     */
    public function is_get_care($user_id,$fans_id){
        if($user_id == $fans_id){
            $message = [
                'code' =>2,
                'msg' =>'发布人为自己，无需点击关注(隐藏)'
            ];
            return $message;
            exit;
        }
        $re = Db::name('fans_collect')->where(array('user_id'=>$user_id,'fans_id'=>$fans_id))->value('collect_id');
        if($re>0){
            $message = [
                'code' =>1,
                'msg' =>'已关注'
            ];
        }else{
            $message = [
                'code' =>0,
                'msg' =>'未关注'
            ];
        }
        return $message;
    }

    //获取当前用户等级信息
    public function usermsg($user_id){
        $user_data=model('users')
            ->alias('U')
            ->join('__STORE_LEVEL__ S','U.store_level=S.store_level_id','LEFT')
            ->where(['U.user_id'=>$user_id])
            ->field('U.user_id,U.nickname,U.email,U.mobile,U.head_pic,U.pay_points,U.frozen_money,U.user_money,U.user_level,U.is_authentication,U.is_goodstore,S.*')
            ->find();//个人数据
        $user_data['is_caution'] = self::cautionMoney($user_id);
        return $user_data;
    }

    //用户是否支付保证金
    static public function cautionMoney($user_id){
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
        return $message;
    }

}