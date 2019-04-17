<?php
namespace app\dataapi\server;

use app\dataapi\model\BidOrder;
use app\dataapi\model\Goods;
use think\Config;
use think\Db;
use think\image\Exception;
use app\dataapi\server\BidOrderServer;
use think\Request;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
/**
 * @title   自动出价
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class AutoBidProductServer
{


    /*
     *@//自动出价
     *
     */
    public function index(int $good_id,int $page=1){
        if($good_id<=0){
           return ;
        }
        $Image =new Image();
        $datauser = $Image->flagimage();
        $userid =$datauser['user_id'];
        if($userid<=0){
            return false;
        }

        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->vhostAddByCli($userid, $good_id);


        $mumber=mt_rand(2,4);
        while($mumber>0){
            $mumber--;
            $ddd= $Image->flagimage();
            $userid =$ddd['user_id'];
            $UserServer = new UserServer();
            if($userid>0 && $good_id>0) {
                $flag = $UserServer->userUpdateLike($userid, ['goods_id'=>$good_id]);
                if(!$flag){
                    continue;
                }
            }
        }
        if ($data > 0) {
            return true;
        }
        return false;
    }

    //虚拟出价信息
    public function getIndexProductlistsBy(int $limit=1,int $page=1):array{
        $config=['page'=>$page];
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id')
            ->where('a.is_toplatform', 1)
            ->where('a.is_helper_bid', 0)
            ->where('a.goods_status', 1)
            ->where('a.endTime', '>',time())
            ->whereNull('a.delete_time')
            ->field('a.goods_id,a.is_helper_bid,a.endTime')
            ->group('a.user_id')
            ->paginate($limit,false,$config);
        if($hot_goods->isEmpty()){
            return [];
        }
        $hot_goods = $hot_goods->toArray();
        return  $hot_goods;
    }



    public function getIndexProductlists(int $limit=1,int $page=1):array{
        $config=['page'=>$page];
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id')
            ->where('a.is_toplatform', 1)
            ->where('a.goods_status', 1)
            ->where('a.endTime', '>', time())
            ->whereNull('a.delete_time')
            ->field('a.*,q.*')
            ->group('a.user_id')
            ->order('a.goods_id asc')
            ->paginate($limit,false,$config);
        if($hot_goods->isEmpty()){
            return [];
        }
        $hot_goods = $hot_goods->toArray();
        return  $hot_goods;
    }
}