<?php
namespace app\dataapi\job;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Cache;
use think\Config;
use think\Db;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Hook;
use think\queue\Job;
use  app\dataapi\server\UserServer;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Quene
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->autoGiveFanUser($data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            print("<info>fans Job has been done and deleted" . "</info>\n");
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                print("<warn>fans Job has been retried more than 3 times!" . "</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /*
   * 赠送会员粉丝
   *
   */
    public function autoGiveFanUser(array $userid)
    {
        $user_id = $userid['user_id'];
        $userpic = false;
        $hot_users = Db::table('tp_users')
            ->whereNull('delete_time');
        if (isset($userid['is_or_member']) && $userid['is_or_member'] == true) {
            $hot_users = $hot_users->where('level', '>', 1);
        }
        $hot_users = $hot_users->where('user_id', $user_id)
            ->where('is_give_freefans', 0)
            ->field('is_give_freefans,user_id')
            ->find();
        if (isset($hot_users['user_id']) && $hot_users['user_id'] > 0) {
            $userLogic = new UsersLogic();
            $user_info = $userLogic->get_info($hot_users['user_id']); // 获取用户信息
            $memberlevel = $user_info['result']['level'];
//                $memberProductCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit','membeFansCount',$memberlevel);
//                if(!$memberProductCount){
//                    return false;
//                }
            $memberProductCount = mt_rand(15, 20);
            //获取作品id
            $x = $memberProductCount;
            $i = 0;
            $goods_collectobj = Db::name('fans_collect');

            try {
                // 启动事务
              //  Db::startTrans();
                do {
                    $x--;
                    $ddd = $this->flagimage();
                    $flag = $goods_collectobj->where(['user_id' => $hot_users['user_id'], 'fans_id' => $ddd['user_id']])->find();
                    if (!empty($flag)) {
                        continue;
                    }
                    $goods_collectobj->insertGetId([
                        'user_id' => $hot_users['user_id'],
                        'fans_id' => $ddd['user_id'],
                        'add_time' => time()
                    ]);
                    $i++;
                } while ($x > 0);
                Db::name('users')
                    ->where('user_id', $hot_users['user_id'])
                    ->update([
                        'is_give_freefans' => ['exp', 'is_give_freefans+' . $i],
                        'is_give_freefans_datetime' => time()
                    ]);
                // 提交事务
             //   Db::commit();
                return true;
            } catch (\Exception $e) {
                // 回滚事务
              //  Db::rollback();
                return false;
            }
        }
        return $userpic;
    }


    public function flagimage()
    {
        //有着品的
        $datauser = Db::name('Goods')
            ->whereNull('delete_time')
            ->field('user_id')
            ->order('rand()')
            ->find();
        if(isset($datauser['user_id'])){
             $p =    UserServer::getUserinfostatic($datauser['user_id'],'head_pic');
            $flag = $this->imagetest($p);
            if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                return $this->flagimage();
            }
            return $datauser;
        }






    }

    public function imagetest($url)
    {
        if (empty($url)) {
            return false;
        }
        $FastImageSize = new \FastImageSize\FastImageSize();
        $imageSize = $FastImageSize->getImageSize($url);
        return $imageSize;
    }

}