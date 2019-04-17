<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;
use app\api\server\Cache;

class Goods extends Model
{
    protected $table = 'tp_goods';
    protected $pk = 'goods_id';

    protected $readonly = ['goods_id'];

    use SoftDelete;
    public  $tempgoods_id = 0;

    protected $deleteTime = 'delete_time';

    protected static function init()
    {
/*        Goods::event('after_update', function ($goods) {
            dump($goods);
            //执行删除缓存对应的goods_id
            Cache::rm(md5('fh'.$goods->goods_id));  //商品列表
            Cache::rm(md5('fh_goods_images'.$goods->goods_id));  //商品详情图
            Cache::rm(md5('care_goods_pics'.$goods->goods_id));  // 详情关注者头像
            Cache::rm(md5('goods_detail_key'.$goods->goods_id)); //详情内容
            //生成最新的信息

        });*/

        Goods::afterUpdate(function ($goods) {
            if(isset($goods->tempgoods_id) && $goods->tempgoods_id>0){
                //执行删除缓存对应的goods_id
                Cache::rm(md5('yipinfanghua_virtua_list'.$goods->tempgoods_id));  //商品列表
                Cache::rm(md5('yipinfanghua_lists_fh1'.$goods->tempgoods_id));  //商品列表
                Cache::rm(md5('fh_goods_images'.$goods->tempgoods_id));  //商品详情图
                Cache::rm(md5('care_goods_pics'.$goods->tempgoods_id));  // 详情关注者头像
                Cache::rm(md5('goods_detail_key'.$goods->tempgoods_id)); //详情内容
                //生成最新的信息
            }
        });
    }

    public function GoodsImages()
    {
        return $this->hasMany('GoodsImages','goods_id','goods_id')->field('rescourse_id');
    }


    public function getGoodsImages()
    {
        return $this->belongsToMany('GoodsImages','','goods_id','goods_id');
    }
}
