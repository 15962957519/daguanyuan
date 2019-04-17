<?php
namespace app\api\controller;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Db;
use think\Request;
use think\Response;
use think\Hook;
use  app\dataapi\controller\BaseApi;
use app\dataapi\server\UserServer;
use app\dataapi\controller\JobQeue;
use app\dataapi\server\AliyunOss;
use app\dataapi\model\GoodsImages;
use think\Config;
class IndividualStore extends BaseApi
{
    private $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt','appGetTokencanNull',$this->user_data);
        parent::_initialize();
    }

    //活动专场
    public  function individualStore(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        if ($user_id > 0){
            $p = $request->param('page',1);
            $limitnum = 6;
            $condition = [
                'prom_type' => ['>',0],
                //'endtime' => ['>=',time()]
            ];

            $goods_list = Db::name('goods')->where($condition)
                ->whereNull('delete_time')
                ->field('goods_id,goods_name,original_img,upload_time,start_price,user_id')
                ->order('upload_time DESC')
                ->page($p,$limitnum)
                ->select();
            $usersobj = Db::name('users');
            foreach($goods_list as $k=>&$v){
                $v['user_msg'] = $usersobj->field('nickname,head_pic')->where('user_id',$v['user_id'])->find();
            }


            Response::create(['data'=>$goods_list, 'code' => '2000', 'message' => '加载数据成功'], 'json')->header($this->header)->send();
        }


    }




}
