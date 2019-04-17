<?php

namespace app\api\server;

use app\dataapi\model\GoodsShouc;
use think\Db;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class GoodCollectServer
{

    public function getCollect()
    {
        $GoodsCollect = new GoodsShouc();
        //  $collectdata = $GoodsCollect->alias('a')->join('third_users u','a.user_id=u.user_id')->field('u.user_id,u.head_pic')->where(['a.goods_id'=>$v['goods_id']])->paginate(16);
    }

    public function getOne(int $good_ids)
    {
        $collectdata = Db::connect('mycat104')->name('goods_collect01')->where('goods_id', $good_ids)->whereNull('delete_time')->count();
        return $collectdata;
    }


   public static function insetintogoolect(array $data){
        $goods_collect = Db::connect('mycat104')->name('goods_collect01')->insertGetId($data);
        return  $goods_collect;
    }
}