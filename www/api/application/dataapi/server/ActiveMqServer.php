<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 * 参考地址 http://www.cnblogs.com/txw1958/p/weixin-js-sharetimeline.html
 * http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html  微信JS-SDK说明文档
 */

namespace app\dataapi\server;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use  app\dataapi\server\UtilMq as Util;
use think\queue\Job;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use app\dataapi\model\LikegoodMessageList;
use app\dataapi\server\AutoBidProductServer;
use Carbon\Carbon;
use think\Db;
use app\dataapi\server\Weixin;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Hook;
use think\Cache;

/**
 * ativemq封装
 * Class CatsLogic
 * @package Home\Logic
 */
class ActiveMqServer
{

    private  $jobqeueobj=null;
    public  function  __construct()
    {
       $this->jobqeueobj  = new  \app\dataapi\server\MqProducer();

    }

    //自动点赞
    /*
       *$sumtime 定时时间
     * $id 日志id
       */
    public function autoLikeGoodsByActiveMq(int $goods_id = 0, int $sumtime = 0, int $id = 0)
    {
        if ($goods_id <= 0 || $sumtime <= 0 || $id <= 0) {
            return;
        }
        $jobData = ['type'=>'like','ts' => $sumtime, 'bizId' => uniqid(), 'id' => $id, 'goods_id' => $goods_id, 'is_or_member' => false];
        //构造消息发布者
        return  $this->jobqeueobj ->process($jobData);
    }




    //自动为新增加用户添加粉丝
    /*
       *
       */
    public function autoForFansererVhostbyAli(int $user_id = 0, int $sumtime = 0, int $id = 0)
    {
        if ($user_id <= 0) {
            return;
        }
        $jobData = ['type'=>'fanserver','ts' => $sumtime, 'bizId' => uniqid(), 'id' => $id, 'user_id' => $user_id, 'is_or_member' => false];
        $carbon = new Carbon();
        $carbon = $carbon->addDays(10);
        $sumtime = $carbon->getTimestamp();
        $loopcount = mt_rand(1, 30);
        while ($loopcount > 0) {
            $execution_unixtimestamp = mt_rand(time(), $sumtime);
            $time2wait = $execution_unixtimestamp - time();
            // 4.将该任务推送到消息队列，等待对应的消费者去执行
            //插入数据库
            $data = ['user_id' => $user_id, 'start_time' => $execution_unixtimestamp];
            $jobData['collectgoodmessagelist_insertid'] = Db::name('collectgoodmessagelist')->insertGetId($data);
            $jobData['tz'] = $execution_unixtimestamp;
            //构造消息发布者
            //启动消息发布者
            $this->jobqeueobj->process($jobData);
            $loopcount--;
        }
        return;
    }

}
