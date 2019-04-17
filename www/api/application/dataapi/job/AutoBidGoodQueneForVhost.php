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
class AutoBidGoodQueneForVhost
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
     *@//自动出价
     *
     */
    public function index(array $data){
        $good_id =$data['goods_id'];
         //插入id
        if($good_id<=0){
            return ;
        }
        $Image =new Image();
        $datauser = $Image->flagimage();
        $userid =$datauser['user_id'];
        if($userid<=0){
            return false;
        }
        $countmax = Db::name('goods')->where('goods_id',$good_id)->field('upload_time,goods_id')->find();
        if($countmax['upload_time']){
            $countmax_id = Db::name('forindex')->where('goods_id',$good_id)->where('datetime','>',$countmax['upload_time'])->where('is_over',1)->count('id');
            if( $countmax_id>12){
                return true;
            }
        }else{
            return true;
        }


        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->vhostAddByCli($userid, $good_id);
                $mumber=mt_rand(2,4);
                while($mumber>0){
                    $mumber--;
                    $ddd= $Image->flagimage();
                    $userid =$ddd['user_id'];
                    $UserServer = new UserServer();
                    if($userid>0 && $good_id>0) {
                        $flag = $UserServer->userUpdateLike($userid, ['goods_id'=>$good_id]);
                        if(!$flag){
                            continue;
                        }
                    }
                }
        if ($data > 0) {
            return true;
        }
        return false;
    }



}