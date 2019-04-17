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
use app\dataapi\model\Users as Userss;
use app\common\logic\UsersLogic;
use app\common\logic\CartLogic;
use app\common\logic\MessageLogic;
use app\common\logic\OrderLogic;
use app\dataapi\server\ProductServer;
use app\api\controller\BaseApi;

class NewTips extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }
    public function index(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
            $all_news = DB::name('message')->field('message_id')->where(array('type'=>1))->select();  //获取type=1的全体消息
            foreach($all_news as $v){
                $re = Db::name('user_message')->where(['user_id'=>$user_id,'message_id'=>$v['message_id']])->find();
                if($re == null){
                    Db::name('user_message')->insert(['user_id'=>$user_id,'message_id'=>$v['message_id']]);
                }
            }
            $system = Db::name('user_message')
                ->alias('M')
                ->join('__MESSAGE__ E','M.message_id = E.message_id')
                ->field('M.status,E.message_id,E.message,E.send_time')
                ->where(array('M.user_id'=>$user_id))
                ->select();


        Response::create(['data'=>['system'=>$system],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //点击查看消息
    public function check_message(Request $request){
        $message_id = $request->param('message_id');
        Db::name('user_message')->where(array('message_id'=>$message_id))->update(['status'=>1]);  //变更已读
        $message_detail = Db::name('message')->where(array('message_id'=>$message_id))->find();

        Response::create(['data'=>['message_detail'=>$message_detail],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();

    }


}