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
class UserFanServer
{
    /*
 * 赠送会员粉丝
 *
 */
    public static  function autoGiveFanUser(int $user_id, int $inseridid)
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($user_id); // 获取用户信息
        if (isset($user_info['result']['level']) && $user_info['result']['level'] == 1) {
            $fancollectcount = Db::name('fans_collect')->alias('a')->join('third_users w', 'a.fans_id=w.user_id')->where('a.user_id', $user_id)->whereNull('a.delete_time')->whereNull('w.delete_time')->count(); //获取收藏数量    粉丝
            if ($fancollectcount > 80) {
                //减少粉丝
                $limit = mt_rand(1, 20);
                $useridarray = Db::name('fans_collect')->where('user_id', $user_id)->whereNull('delete_time')->order('add_time asc')->limit($limit)->column('collect_id');
                if (!empty($useridarray)) {
                    $useridarray = array_unique($useridarray);
                }
                Db::name('fans_collect')
                    ->whereIn('collect_id', $useridarray)
                    ->update([
                        'delete_time' => time()
                    ]);

                if ($inseridid > 0) {
                    Db::table('tp_collectgoodmessagelist')
                        ->where('id', $inseridid)
                        ->delete();
                }
                return true;
            }
        }

        if (isset($user_info['result']['level']) && $user_info['result']['level'] > 0) {
            $memberlevel = $user_info['result']['level'];
            $memberProductCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'lastPembeFansCount', $memberlevel);
            echo "is going fanserver {$user_id}\r\n";
            if (!$memberProductCount) {
                return false;
            }
            $x = $memberProductCount;
            $Image = new Image();
            do {
                // 启动事务
                Db::startTrans();
                try {
                    $ddd = $Image->flagimage();
                    $flag = Db::table('tp_fans_collect')->where(['user_id' => $user_id, 'fans_id' => $ddd['user_id']])->find();
                    if (!empty($flag)) {
                        Db::rollback();
                        continue;
                    }
                    if ($flag['level'] == 1) {
                        Db::table('tp_users')
                            ->where('user_id', $ddd['user_id'])
                            ->update([
                                'level' => mt_rand(2, 5)
                            ]);
                    }
                    Db::table('tp_fans_collect')->insertGetId([
                        'user_id' => $user_id,
                        'fans_id' => $ddd['user_id'],
                        'add_time' => time()
                    ]);
                    $x--;
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return false;
                }
            } while ($x > 0);
            Db::table('tp_users')
                ->where('user_id', $user_id)
                ->update([
                    'is_give_freefans' => ['exp', 'is_give_freefans+' . $x],
                    'is_give_freefans_datetime' => time()
                ]);

            if ($inseridid > 0) {
                Db::table('tp_collectgoodmessagelist')
                    ->where('id', $inseridid)
                    ->delete();
            }
            return true;
        }
        return false;
    }

}