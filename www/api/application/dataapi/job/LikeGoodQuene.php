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
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use app\dataapi\model\LikegoodMessageList;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class LikeGoodQuene
{


    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        //延迟执行  指定时间执行
        $isJobDone = $this->like($data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                // print("<warn>fans Job has been retried more than 3 times!" . "</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }


    function like($data)
    {
        $goods_id = $data['goods_id'];
        $randlike = mt_rand(1, 3);
        $image = new Image();
        $UserServer = new UserServer();
        do {
            $randlike--;
            $ddd = $image->flagimage();
            $userid = $ddd['user_id'];
            if ($userid > 0 && $goods_id > 0) {
                $flag = $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
            }
        } while ($randlike > 0);
        //更新数据库
        $LikegoodMessageList = new LikegoodMessageList();
        $LikegoodMessageList->where('id', $data['id'])->delete(true);
        return true;
    }
}