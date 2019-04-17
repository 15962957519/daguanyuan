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

class CheckWorker extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }

    /**[ 员工查询 ]
     * @param Request $request
     */
    public function index(Request $request){
        $keywords = $request->param('keywords');  //查询关键字 手机或工号
        $result = Db::name('check_worker')
            ->where('phone|worknumber','like',$keywords)
            ->find();
       $outdata =  ['data' =>$result, 'code' => '2000', 'message' => '获取成功'];
        if(empty($result)){
            $outdata['code']= '4000';
            $outdata['reason']= '没有查询到数据！';
        }
        Response::create($outdata, 'json')->header($this->header)->send();
    }
    /**[ 员工查询结果页面 ]
     * @param Request $request
     */
    public function staffresult(Request $request){
        $keywords = $request->param('keywords');  //查询关键字 手机或工号
        $result = Db::name('check_worker')
            ->where('phone',$keywords)
            ->whereOr('worknumber',$keywords)
            ->find();
       $outdata =  ['data' =>$result, 'code' => '2000', 'message' => '获取成功'];
        if(empty($result)){
            $outdata['code']= '4000';
            $outdata['reason']= '没有查询到数据！';
        }
        Response::create($outdata, 'json')->header($this->header)->send();
    }

    /**[ 签到页面（累计签到 ]
     * @param Request $request
     */
    public function signedCount(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $sign_msg = Db::name('users')->where(['user_id'=>$user_id])->field('pay_points,rignin_time,rignin_count,continue_day,sign_get_points')->find();
        $sign_get_points = $sign_msg['sign_get_points'];
        $sign_msg['sign_get_points'] = explode(',',$sign_get_points);
        Response::create(['data' =>$sign_msg, 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();
    }

    /**[ 点击领取累计积分 ]
     * @param Request $request
     */
    public function getPoints(Request $request){
        //获取2,3,7,10,15,25
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $get_day = $request->param('sign_get_points');  //当被领取时，即时加入数据表
        $sign_day = array(2,3,7,10,15,25);
        $sign_msg = Db::name('users')->where(['user_id'=>$user_id])->field('pay_points,sign_get_points')->find();
        $get_points = $sign_msg['sign_get_points'];
        if($get_points == 0){
            $sign_get_points = $get_day;
        }else{
            $get_pointsss = explode(',',$get_points);  //转数组
            if(in_array($get_day,$get_pointsss)){
                Response::create(['code' => 2001, 'message' => '已被领取！'], 'json')->header($this->header)->send();
            }
            $sign_get_points = $sign_msg['sign_get_points'].','.$get_day;
        }
        Db::name('users')->where(['user_id'=>$user_id])->update(['pay_points'=>$sign_msg['pay_points']+10 ,'sign_get_points'=>$sign_get_points]);

        Response::create(['code' => 2000, 'message' => '领取成功'], 'json')->header($this->header)->send();

    }

    /**[ 每日签到(送积分) ]
     * @param Request $request
     */
    public function signed(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $result = Db::name('users')->where(['user_id'=>$user_id])->field('continue_day,rignin_time,pay_points,rignin_count')->find();
        $continue_day = $result['continue_day'];  //持续签到天数
        $last_sigin_time = $result['rignin_time'];   //最后签到时间戳
        $pay_points = $result['pay_points'];   //当前登陆者积分数
        $rignin_count = $result['rignin_count'];   //累计签到
        $sigin_time = mktime(0,0,0,date('m'),date('d')+1,date('Y'));  //签到时间（当日凌晨）
        //判断当日是否已签到
        if(time()>$last_sigin_time){
            $time = time() - $last_sigin_time; //时间差
            if ($time > 24*60*60 ) {  // 断签  （第一天）
                Db::name('users')->where(['user_id'=>$user_id])->update(['continue_day'=>1,'rignin_time'=>$sigin_time,'pay_points'=>$pay_points+1,'rignin_count'=>$rignin_count+1]);
            }else if($time < 24*60*60 && $continue_day==1){ //（第二天）
                Db::name('users')->where(['user_id'=>$user_id])->update(['continue_day'=>2,'rignin_time'=>$sigin_time,'pay_points'=>$pay_points+2,'rignin_count'=>$rignin_count+1]);
            }else if($time < 24*60*60 && $continue_day==2){ //（第三天）
                Db::name('users')->where(['user_id'=>$user_id])->update(['continue_day'=>3,'rignin_time'=>$sigin_time,'pay_points'=>$pay_points+4,'rignin_count'=>$rignin_count+1]);
            }else if($time < 24*60*60 && $continue_day==3){ //（第四天）
                Db::name('users')->where(['user_id'=>$user_id])->update(['continue_day'=>4,'rignin_time'=>$sigin_time,'pay_points'=>$pay_points+8,'rignin_count'=>$rignin_count+1]);
            }else if($time < 24*60*60 && $continue_day==4){ //（第五天）
                Db::name('users')->where(['user_id'=>$user_id])->update(['continue_day'=>5,'rignin_time'=>$sigin_time,'pay_points'=>$pay_points+10,'rignin_count'=>$rignin_count+1]);
            }else{     //（第五天以上）
                Db::name('users')->where(['user_id'=>$user_id])->update(['continue_day'=>$continue_day+1,'rignin_time'=>$sigin_time,'pay_points'=>$pay_points+10,'rignin_count'=>$rignin_count+1]); //超过5天 连续加积分5
            }
            Response::create(['code' => 2000, 'message' => '今日签到成功！'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code' => 2000, 'message' => '当日已签到！'], 'json')->header($this->header)->send();
        }
    }



}