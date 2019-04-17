<?php
namespace app\api\model;
use think\Model;
use app\api\server\Cache;
/*
 * 商品区相关逻辑处理
 */
class Goods extends Model
{
    public  $tempgoods_id = 0;

    protected static function init()
    {
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

}
