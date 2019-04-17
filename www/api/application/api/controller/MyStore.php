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

class MyStore extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }


    //店铺首页聚合参数
    public function StoreIndexPolymerization(Request $request){

        $user_id = (int)$this->user_data['user_id'];
        $current_user_id = $request->param('user_id',$user_id);
        $users = new UsersServer();
        if($user_id != $current_user_id){
            $is_care = $users->is_get_care($current_user_id,$user_id);  //判断是否被关注
        }else{
            $is_care = '';
        }
        $where = [
            'user_id'=>$current_user_id
        ];
        $care_count = Db::name('fans_collect')->where($where)->count();  //当前多少粉丝关注
        $all_count = Db::name('goods')->where($where)->whereNull('delete_time')->count();  //全部商品区
        $new_count = Db::name('goods')->where(['endTime'=>['>',time()],'user_id'=>$current_user_id])->whereNull('delete_time')->count();  //全部在线商品数量
        $tuijian_count = Db::name('goods')->where(['is_special_price'=>1,'user_id'=>$current_user_id])->whereNull('delete_time')->count();  //推荐特价商品区
        $newgoods = Db::name('goods')
            ->field('goods_id,goods_name,original_img')
            ->where($where)
            ->whereNull('delete_time')
            ->order('goods_id DESC')
            ->limit(2)
            ->select();  //新品（时间还没结束）
        $allgoods = Db::name('goods')
            ->field('goods_id,goods_name,original_img')
            ->where($where)
            ->whereNull('delete_time')
            ->order('endTime DESC')
            ->limit(2)
            ->select();  //当前登陆者发布的所有产品
        $where['is_special_price'] = 1;  //1：代表特价商品区
        $specgoods = Db::name('goods')
            ->field('goods_id,goods_name,original_img')
            ->where($where)
            ->whereNull('delete_time')
            ->order('click_count,endTime DESC')
            ->limit(2)
            ->select();
        Response::create(['data'=>['newgoods'=>$newgoods,'allgoods'=>$allgoods,'specgoods'=>$specgoods,'care_count'=>$care_count,'all_count'=>$all_count,'new_count'=>$new_count,'tuijian_count'=>$tuijian_count],
            'is_care'=>$is_care,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //店铺首页
    public function StoreIndex(Request $request){
        $user_id = (int)$this->user_data['user_id'];
        $current_user_id = $request->param('user_id',$user_id);
        $type = $request->param('type',1);   //1:全部商品区 2:新品 3:推荐(天天特价)
        $users = new UsersServer();
        if($user_id != $current_user_id){
            $is_care = $users->is_get_care($current_user_id,$user_id);  //判断是否被关注
        }else{
            $is_care = '';
        }

        $where = [
            'user_id'=>$current_user_id
        ];
        //当前用户信息等级
        $usersevers = new UsersServer();
        $store_msg = $usersevers->usermsg($where['user_id']);
        $care_count = Db::name('fans_collect')->where($where)->count();  //当前多少粉丝关注
        $all_count = Db::name('goods')->where($where)->whereNull('delete_time')->count();  //全部商品区
        $new_count = Db::name('goods')->where(['endTime'=>['>',time()],'user_id'=>$current_user_id])->whereNull('delete_time')->count();  //全部商品区
        $tuijian_count = Db::name('goods')->where(['is_special_price'=>1,'user_id'=>$current_user_id])->whereNull('delete_time')->count();  //推荐商品区

        if($type == 2){
            $where['endTime'] = ['>',time()];
            $mygoods = Db::name('goods')
                ->field('goods_id,goods_name,start_price,shop_price,goods_status,transaction_time,exchange_integral,original_img,endTime,click_count,goods_status,is_distribute,distribute_proportion,distribute_money')
                ->where($where)
                ->whereNull('delete_time')
                ->order('click_count,endTime DESC')
                ->select();  //新品（时间还没结束）
        }else if($type == 3 ){
            $where['is_special_price'] = 1;  //1：代表特价商品区
            $mygoods = Db::name('goods')
                ->field('goods_id,goods_name,start_price,shop_price,goods_status,transaction_time,exchange_integral,original_img,endTime,click_count,goods_status,is_distribute,distribute_proportion,distribute_money')
                ->where($where)
                ->whereNull('delete_time')
                ->order('click_count,endTime DESC')
                ->select();
        }else{
            $mygoods = Db::name('goods')
                ->field('goods_id,goods_name,start_price,shop_price,goods_status,transaction_time,exchange_integral,original_img,endTime,click_count,goods_status,is_distribute,distribute_proportion,distribute_money')
                ->where($where)
                ->whereNull('delete_time')
                ->order('click_count,endTime DESC')
                ->select();  //当前登陆者发布的所有产品
        }
        foreach($mygoods as $k=>$v){
            $res_id = $v['original_img'];
            $mygoods[$k]['original_img'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
            $mygoods[$k]['care_count'] = Db::name('goods_collect')->where(['goods_id'=>$v['goods_id']])->count();  //获取点赞数
            $re = Db::name('goods_collect')->where(['user_id'=>$user_id,'goods_id'=>$v['goods_id']])->find();   //当前登陆者是否已点赞当前商品区
            if($re){
                $mygoods[$k]['is_care'] = ['code'=>2,'msg'=>'已点赞'];
            }else{
                $mygoods[$k]['is_care'] = ['code'=>1,'msg'=>'未点赞'];
            }
            //获取当前出价
            $cur_price = Db::name('bid_order')->where(['goods_id'=>$v['goods_id']])->order('id DESC')->find();
            if($cur_price != false){
                $mygoods[$k]['cur_price'] = $cur_price['bid_price'];
            }else{
                $mygoods[$k]['cur_price'] = $v['start_price'];
            }
        }
        Response::create(['data'=>['mygoods'=>$mygoods,'store_ms'=>$store_msg,'care_count'=>$care_count,'all_count'=>$all_count,'new_count'=>$new_count,'tuijian_count'=>$tuijian_count],
            'is_care'=>$is_care,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }


}