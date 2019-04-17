<?php
namespace app\api\server;

use app\dataapi\model\FansCollect;
use app\dataapi\model\GoodsShouc;
use app\dataapi\model\GoodsCollect;
use app\dataapi\model\ThirdUsers;
use app\dataapi\model\UserBond;
use app\dataapi\model\Goods;
use app\dataapi\model\Users;
use app\dataapi\model\UserVerifty;
use Defuse\Crypto\KeyProtectedByPassword;
use think\Db;
use think\Request;
use think\Config;
use app\Home\Logic\UsersLogic;
use think\Hook;
use app\dataapi\lib\Image;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class UserBidServer
{
    /*
   *@//自动出价
   *
   */
    public static function indexbid(array $data)
    {
        $good_id = $data['goods_id'];
        $inseridid = $data['id'];
        //插入id
        if ($good_id <= 0) {
            return;
        }
        //获取执行时间 匹配是否需要再执行
        $implement_datetime = Db::name('forindex')->where('id', $inseridid)->value('datetime');
        if ($implement_datetime && $implement_datetime > 0 && ($implement_datetime + 100 < time())) {
            return true;
        }
        $Image = new Image();
        $datauser = $Image->flagimage();
        $userid = $datauser['user_id'];
        if ($userid <= 0) {
            return false;
        }
        $countmax = Db::name('goods')->where('goods_id', $good_id)->field('is_toplatform,upload_time,goods_id,endTime')->find();
        if ($countmax['upload_time'] && $countmax['endTime'] > time()) {
            $countmax_id = Db::name('forindex')->where('goods_id', $good_id)->where('datetime', '>', $countmax['upload_time'])->where('is_over', 1)->count('id');
            if ($countmax['is_toplatform'] == 3) {
                Db::name('goods')->where('goods_id', $good_id)->update(['on_time' => time()]);
            }
            if ($countmax_id > 13) {
                return true;
            }
        } else {
            return true;
        }
        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->vhostAddByCli($userid, $good_id);
        //更新是否完成了
        if ($inseridid > 0) {
            $goods = Db::name('forindex')->where('id', $inseridid)->update(['is_over' => 1, 'delete_time' => time()]);
            if ($goods != false) {
                $UserServer = new UserServer();
                $mumber = mt_rand(2, 4);
                while ($mumber > 0) {
                    $mumber--;
                    $ddd = $Image->flagimage();
                    $userid = $ddd['user_id'];
                    if ($userid > 0 && $good_id > 0) {
                        $flag = $UserServer->userUpdateLike($userid, ['goods_id' => $good_id]);
                        if (!$flag) {
                            continue;
                        }
                    }
                }
            }
        }
        if ($data > 0) {
            return true;
        }
        return false;
    }


}