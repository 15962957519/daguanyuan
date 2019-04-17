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
class AutoForFansererVhost
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
     *@//自动出价
     *
     */
    public function index(array $data)
    {
        $user_id = $data['user_id'];
        $inseridid = $data['collectgoodmessagelist_insertid']??0;
        $this->autoGiveFanUser($user_id,$inseridid);
        return true;
    }



//根据用户id 修改虚拟作品给

    /*
 * 赠送会员粉丝
 *
 */
    private function autoGiveFanUser(int $user_id,int $inseridid)
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($user_id); // 获取用户信息

        if (isset($user_info['result']['level']) && $user_info['result']['level'] ==1) {
            $fancollectcount = Db::name('fans_collect')->alias('a')->join('third_users w','a.fans_id=w.user_id')->where('a.user_id',$user_id)->whereNull('a.delete_time')->whereNull('w.delete_time')->count(); //获取收藏数量    粉丝
            if($fancollectcount>80){
                //减少粉丝
                $limit =mt_rand(1,20);
              $useridarray =  Db::table('tp_fans_collect')->where('user_id',$user_id)->whereNull('delete_time')->order('add_time asc')->limit($limit)->column('collect_id');

              if(!empty($useridarray)){

                  $useridarray =array_unique($useridarray);
              }
                Db::table('tp_fans_collect')
                    ->whereIn('collect_id', $useridarray)
                    ->update([
                        'delete_time' =>time()
                    ]);

              if($inseridid>0){
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
            echo "正在执行{$user_id}\r\n";
            if (!$memberProductCount) {
                return false;
            }
            $x = $memberProductCount;
            $Image =new Image();
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
                    if($flag['level']==1){
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

            if($inseridid>0){
                Db::table('tp_collectgoodmessagelist')
                    ->where('id', $inseridid)
                    ->delete();

            }

            return true;
        }
        return false;
    }


}
