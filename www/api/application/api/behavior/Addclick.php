<?php
namespace app\api\behavior;
use think\Db;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Addclick
{
    static public function appInit(&$params)
    {
        $user_id = $params['user_id']; //当前登录者user_id;
        $time = time();
        $where = [
            'user_id' => $user_id,
            'is_heler_likeand' => 1,
            'vistorassign' => ['>',0],
            'endTime' => ['>',$time]
        ];
        $user_online_goods = Db::name('goods')->where($where)->whereNull('delete_time')->select();   //goods_id = 1187863
        $goods_collect = Db::name('goods_collect');
        $goodsobj = Db::name('goods');
        $image = new \app\dataapi\lib\Image();
        $UserServer = new \app\dataapi\server\UserServer();
        foreach ($user_online_goods as $k=>$goods){
            $timespace = $goods['endTime'] - $goods['upload_time'];  //时间差
            if($timespace > 86400*3){  //大于3天 缩进一天
                $timespace = $timespace - 86400;
            }elseif ($timespace > 86400 && $timespace < 86400*3){   //大于1天小于3天  缩进半天
                $timespace = $timespace - 43200;
            }

            if($goods['vistorassign'] > 0){
                $vistorassign = $goods['vistorassign'];
            }else{
                $vistorassign = 1;
            }
            $cur_timespace = $time-$goods['upload_time']; //当前时间差
            $cur_vistorassign = intval($cur_timespace/($timespace/$vistorassign));  //当前需要分配的收藏个数
            if(mt_rand(1,10)/10 > 0.7){
                $cur_vistorassign = $cur_vistorassign + 1;
            }
            $goods_collect_count = $goods_collect->where(['goods_id' => $goods['goods_id']])->whereNull('delete_time')->count();
            $need_nums = $cur_vistorassign - $goods_collect_count;   // 需要执行的次数
            if($need_nums > 0){
                do{
                    $need_nums -- ;
                    $ddd = $image->flagimage();
                    if($ddd['user_id'] > 0 && $goods['goods_id'] > 0){
                        try{
                            $UserServer->vhostuserUpdateLike($ddd['user_id'], ['goods_id' => $goods['goods_id']]);
                        }catch (\Exception $e){
                            echo $e->getMessage();
                        }
                    }
                }while($need_nums > 0);

                $goodsobj->where('goods_id',$goods['goods_id'])->update(['hasvistorassin'=> $cur_vistorassign]);
            }else{
                //echo '已操作完毕,无需执行';
            }
        }

    }

    public function appEnd(&$params)
    {

    }


}