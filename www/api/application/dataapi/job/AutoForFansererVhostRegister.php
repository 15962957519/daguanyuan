<?php
namespace app\dataapi\job;


use think\Cache;
use think\Config;
use think\Db;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Hook;
use think\queue\Job;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use app\dataapi\server\BidOrderServer;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class AutoForFansererVhostRegister
{


    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        //延迟执行  指定时间执行
        $isJobDone = $this->index($data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            //   print("<info>fans Job has been done and deleted" . "</info>\n");
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                //  print("<warn>fans Job has been retried more than 3 times!" . "</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /*
     *@//AutoForFansererVhostRegister 注册时候一次性给粉丝
     *
     */
    public function index(array $data)
    {
        $user_id = $data['user_id'];
        $inseridid = $data['id'];
        $this->autoGiveFanUserOk($user_id);
        return true;
    }



//根据用户id 修改虚拟作品给

    /*
 * 赠送会员粉丝
 *
 */
    private function autoGiveFanUser(int $user_id)
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($user_id); // 获取用户信息
        if (isset($user_info['result']['level']) && $user_info['result']['level'] > 0) {
            $memberlevel = $user_info['result']['level'];
            $memberProductCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'lastPembeFansCount', $memberlevel);
            echo "正在执行{$user_id}\r\n";
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
                    Db::table('tp_users')
                        ->where('user_id', $ddd['user_id'])
                        ->update([
                            'level' => mt_rand(2, 5)
                        ]);
                    if (!empty($flag)) {
                        continue;
                    }
                    Db::table('tp_fans_collect')->insertGetId([
                        'user_id' => $user_id,
                        'fans_id' => $ddd['user_id'],
                        'add_time' => time()
                    ]);
                    echo $x;
                    echo "\r\n";
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
            return true;
        }
        return false;
    }


    /*
 * 赠送会员粉丝
 *
 */
    public function autoGiveFanUserOk(int $user_id)
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($user_id); // 获取用户信息
        if (isset($user_info['result']['level']) && $user_info['result']['level'] > 0) {
            $memberlevel = $user_info['result']['level'];
            $memberProductCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'lastPembeFansCount', $memberlevel);
            echo "正在执行{$user_id}\r\n";
            if (!$memberProductCount) {
                return false;
            }
            $x = $memberProductCount - $user_info['result']['is_give_freefans'];
            if ($x <= 0) {
                return true;
            }

            $obj = new Image();
            //实例化出
            $arruserneed_p = $this->flagimageAll(9);
            $arruserneed_np = $this->flagimageAll(2);
            while ($x > 0) {
                if (count($arruserneed_np) < $x) {
                    echo "数据不够了";
                    break;
                }
                $rangmax = mt_rand(1, 10);
                if ($rangmax > 5) {
                    $needuseridata_index = array_rand($arruserneed_p, 1);
                    if (!isset($arruserneed_p[$needuseridata_index])) {
                        continue;
                    }

                    $tempdatauser = $arruserneed_p[$needuseridata_index];
                    unset($arruserneed_p[$needuseridata_index]);
                } else {
                    $needuseridata_index = array_rand($arruserneed_np, 1);
                    if (!isset($arruserneed_np[$needuseridata_index])) {
                        continue;
                    }

                    $tempdatauser = $arruserneed_np[$needuseridata_index];
                    unset($arruserneed_np[$needuseridata_index]);
                }

                $flag = $obj->imagetest($tempdatauser['head_pic']);
                if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                    continue;
                }
                // 启动事务
                Db::startTrans();
                try {
                    $flag = Db::table('tp_fans_collect')->where(['user_id' => $user_id, 'fans_id' => $tempdatauser['user_id']])->find();
                    if (!empty($flag)) {
                        // 回滚事务
                        Db::rollback();
                        continue;
                    }

                    Db::table('tp_fans_collect')->insertGetId([
                        'user_id' => $user_id,
                        'fans_id' => $tempdatauser['user_id'],
                        'add_time' => time()
                    ]);
                    echo $x;
                    echo "\r\n";
                    $x--;
                    Db::table('tp_users')
                        ->where('user_id', $user_id)
                        ->update([
                            'is_give_freefans' => ['exp', 'is_give_freefans+1'],
                            'is_give_freefans_datetime' => ['exp', 'unix_timestamp()']
                        ]);
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
            }
        }
        return $userpic;
    }

    public function flagimageAll($rangmax)
    {

        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        if ($rangmax >= 3) {
            $datauser = Db::name('Goods')
                ->alias('a')
                ->join('users q', 'a.user_id=q.user_id')
                ->whereNull('a.delete_time')
                ->whereNull('q.delete_time')
                ->field('q.head_pic,q.user_id')
                ->group('a.user_id')
                ->select();
        } else {
            $datauser = Db::name('users')->where('fictitious', 1)->where('reg_time', '>', $unixstamp)->whereNull('delete_time')->field('user_id,head_pic')->select();
        }

        return $datauser;

    }


}
