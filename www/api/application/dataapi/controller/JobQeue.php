<?php

namespace app\dataapi\controller;

use think\Exception;
use app\dataapi\server\UserServer;
use Carbon\Carbon;
use think\Queue;
use think\Cache;
use think\Log;
use think\Db;
use app\Home\Logic\UsersLogic;
use think\App;
use app\dataapi\model\LikegoodMessageList;
use app\dataapi\server\MqProducer;
use think\Config;
use  app\dataapi\server\ActiveMqServer;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
set_time_limit(0);

class JobQeue
{


//自动点赞

    /*
       *$sumtime 定时时间
     * $id 日志id
       */
    public function autoLikeGoodsByAliMq(int $goods_id = 0, int $sumtime = 0, int $id = 0)
    {
        if ($goods_id <= 0 || $sumtime <= 0 || $id <= 0) {
            return;
        }
        $jobData = [ 'from'=>'fanghua','type'=>'like','ts' => $sumtime, 'bizId' => uniqid(), 'id' => $id, 'goods_id' => $goods_id, 'is_or_member' => false];
        //构造消息发布者
        $configdata = Config::get('vhostmember');
        $configdata = $configdata['alimqconfig'];
        $producer =  MqProducer::getInstance($configdata);
        return $producer->process($jobData);
    }


    //自动点赞
    /*
       *
       */
    public function autoLikeGoods(int $userid, int $goods_id = 0, int $endtime = 0, $randflag = false, $vistorassign = 0, $hasvistorassin = 0)
    {
        if ($goods_id <= 0 || $endtime <= 0) {
            return false;
        }
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($userid);

        if (!isset($abcdata['result']['store_level'])) {
            return false;
        } else {
            $memberlevel = (int)$abcdata['result']['store_level'];
        }
        if ($vistorassign == 0) {
            $temp = ['goods_id' => $goods_id, 'level' => $memberlevel, 'endtime' => $endtime, 'randflag' => $randflag];
            $clount = $this->getproductassigncount($temp);
            Db::name('goods')->where('goods_id', $goods_id)->update(['vistorassign' => $clount]);

        } else {
            $clount = $vistorassign - $hasvistorassin;
            if ($clount <= 0) {
                return true;
            }
        }
        return $this->getProductinfo(['goods_id' => $goods_id, 'level' => $memberlevel, 'endtime' => $endtime, 'randflag' => $randflag, 'assignclount' => $clount]);
    }

    public function getProductinfo($good)
    {
        /*$nowobj = Carbon::now();
        $currenttimeunixtime = time();
        $LikegoodMessageList = new LikegoodMessageList();
        $tmplevel1 = $good['endtime'];
        $loopcount = $good['assignclount'];
        while ($loopcount > 0) {
            if ($currenttimeunixtime > $tmplevel1) {
                break;
            }
            $mic = mt_rand($currenttimeunixtime, $tmplevel1);
            $dt = Carbon::createFromTimestamp($mic);
            if ($dt->hour <= 5) {
                continue;
            }
            $mic = $mic - $currenttimeunixtime;
            if ($mic < 0) {
                continue;
            }
            $sumtime = $mic + $currenttimeunixtime;
            //添加消息列队
            try {
                $messageid = '';
                $data = ['goods_id' => $good['goods_id'], 'start_time' => $sumtime, 'end_time' => $tmplevel1, 'messageid' => $messageid];
                $id = $LikegoodMessageList->insertGetId($data);
                $messageid = $this->autoLikeGoodsByAliMq($good['goods_id'], $sumtime + 1200, $id);
                if (!$messageid) {
                    App::$debug && Log::record('日志信息 消息列队错误' . $id, 'info ');
                    return false;
                }
                $LikegoodMessageList->where('id', $id)->update(['messageid' => $messageid]);
                Db::name('goods')->where('goods_id', $good['goods_id'])->update(['hasvistorassin' => ['exp', 'hasvistorassin+1']]);
                //更新
            } catch (\RuntimeException $e) {
                App::$debug && Log::record('日志信息', 'info' . $e);
                return false;
            }
            $loopcount--;
        }*/
        return true;
    }



    //自动出价
    /*
       *
       */
    public function autoBidGoods(int $goods_id = 0, int $endtime = 0, int $insretid)
    {
        return $this->autoBidGoodsByAliMq($goods_id, $endtime, $insretid);
    }


    //自动出价 消息队列
    /*
       *$sumtime 定时时间
     * $id 日志id
       */
    public function autoBidGoodsByAliMq(int $goods_id = 0, int $sumtime = 0, int $id = 0)
    {
        if ($goods_id <= 0 || $sumtime <= 0 || $id <= 0) {
            return false;
        }
        try {
            $jobData = ['from'=>'fanghua','type' => 'consumerbid', 'ts' => $sumtime, 'bizId' => uniqid(), 'id' => $id, 'goods_id' => $goods_id, 'is_or_member' => false];
            Db::name('forindex')->where('id', $id)->update(['data' => serialize($jobData), 'addtime' => time()]);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        //构造消息发布者
        $producer = new MqProducer();
        return $producer->process($jobData);
    }


    //自动点赞
    /*
       *
       */
    public function autoBidGoodsForFanserer(int $goods_id = 0, int $sumtime = 0, int $id = 0)
    {
        if ($goods_id <= 0 || $id <= 0) {
            return;
        }
        $jobData = ['from'=>'fanghua','ts' => time(), 'bizId' => uniqid(), 'id' => $id, 'goods_id' => $goods_id, 'is_or_member' => false];
        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName = 'app\dataapi\job\AutoBidGoodQuene';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName = "autobidgoodquene";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        //is_or_member 是否给非会员赠送粉丝
        // 延迟到 2017-02-18 01:01:01 时刻执行
        $time2wait = $sumtime - time();
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        if ($time2wait <= 0) {
            $isPushed = Queue::push($jobHandlerClassName, $jobData, $jobQueueName);
        } else {
            $isPushed = Queue::later($time2wait, $jobHandlerClassName, $jobData, $jobQueueName);
        }
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if ($isPushed !== false) {
            //  echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        } else {
            //  echo 'Oops, something went wrong.';
        }
        return;

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
        $jobData = ['from'=>'fanghua','type' => 'fanserver', 'ts' => $sumtime, 'bizId' => uniqid(), 'id' => $id, 'user_id' => $user_id, 'is_or_member' => false];
        $carbon = new Carbon();
        $carbon = $carbon->addDays(10);
        $sumtime = $carbon->getTimestamp();
        $loopcount = mt_rand(1, 3);
        $activemqserverobj = new  ActiveMqServer();
        while ($loopcount > 0) {
            $execution_unixtimestamp = mt_rand(time(), $sumtime);
            $time2wait = $execution_unixtimestamp - time();
            // 4.将该任务推送到消息队列，等待对应的消费者去执行
            //插入数据库
            $data = ['user_id' => $user_id, 'start_time' => $execution_unixtimestamp];
            $jobData['collectgoodmessagelist_insertid'] = Db::name('collectgoodmessagelist')->insertGetId($data);
            $jobData['tz'] = $execution_unixtimestamp;
            //构造消息发布者
            $configdata = Config::get('vhostmember');
            $configdata = $configdata['alimqconfigfensi'];
            $producer = new MqProducer($configdata);
            //启动消息发布者
            $producer->process($jobData);
            $loopcount--;
        }
        return;
    }

    //自动发送微信提醒
    public static function actionsendtoquenuefe($openid, $goods_data, $sumtime)
    {
        $jobData = ['from'=>'fanghua','ts' => time(), 'bizId' => uniqid(), 'openid' => $openid, 'goods_data' => $goods_data];
        //构造消息发布者
        $configdata = Config::get('vhostmember');
        $configdata = $configdata['alimqconfig48remind'];
        $producer = new MqProducer($configdata);
        //启动消息发布者
        $producer->process($jobData);
        return true;
    }



//精品专场的虚拟喜欢
    public function getProductinfoVhost($good)
    {
        $nowobj = Carbon::now();
        $currenttimeunixtime = time();
        $LikegoodMessageList = new LikegoodMessageList();
        //通过判断获取不同级别的情况
        switch ($good['level']) {
            case 1:
                $time_interval = 2 * 12 * 3600;
                $tmplevel1 = $nowobj->addHours(24)->getTimestamp();

                $a = range(1, mt_rand(50, 80));//5-7个点赞
                break;
            case 2:
                $time_interval = 3 * 12 * 3600;
                $tmplevel1 = $nowobj->addHours(36)->getTimestamp();

                $a = range(1, mt_rand(160, 300));
                break;
            case 3:
                $time_interval = 4 * 12 * 3600;
                $tmplevel1 = $nowobj->addHours(48)->getTimestamp();

                $a = range(1, mt_rand(260, 520));
                break;
            case 4:
                $time_interval = 6 * 12 * 3600;
                $tmplevel1 = $nowobj->addHours(60)->getTimestamp();

                $a = range(1, mt_rand(350, 700));
                break;
            case 5:
                $time_interval = 6 * 12 * 3600;
                $tmplevel1 = $nowobj->addHours(72)->getTimestamp();

                $a = range(1, mt_rand(400, 800));
                break;
            default:
                $time_interval = 2 * 12 * 3600;
                $tmplevel1 = $nowobj->addHours(24)->getTimestamp();
                $a = range(1, mt_rand(23, 35));//5-7个点赞
                break;
        }
        $tmplevel1 = $good['endtime'];
        //计算百分比

        if ($tmplevel1 <= $currenttimeunixtime) {
            return false;
        }
        $loopcount = count($a);
        while ($loopcount > 0) {
            $mic = mt_rand($currenttimeunixtime, $tmplevel1);
            $dt = Carbon::createFromTimestamp($mic);
            if ($dt->hour <= 5) {
                continue;
            }
            $mic = $mic - $currenttimeunixtime;
            if ($mic < 0) {
                continue;
            }
            $sumtime = $mic + $currenttimeunixtime;
            //   Timer::add($mic, $like_pff, array($good['goods_id']), false);
            //插入数据库
            $data = ['goods_id' => $good['goods_id'], 'start_time' => $sumtime, 'end_time' => $currenttimeunixtime + $time_interval];
            $id = $LikegoodMessageList->insertGetId($data);
            if ($id > 0) {
                //添加消息列队
                try {
                    $this->autoLikeGoodsByAliMq($good['goods_id'], $sumtime, $id);
                } catch (\RuntimeException $e) {
                    App::$debug && Log::record('日志信息', 'info' . $e);
                }
            } else {
                continue;
            }
            $loopcount--;
        }
    }

    public function getproductassigncount(array $good)
    {
        $tmplevel1 = 0;
        $nowobj = Carbon::now();
        $currenttimeunixtime = time();

        //通过判断获取不同级别的情况
        if ($good['randflag'] == true) {
            $a = rand(1, 5);
            $time_interval = 2 * 12 * 3600;
        } else {
            switch ($good['level']) {
                case 1:
                    $time_interval = 2 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(24)->getTimestamp();
                    $a =  mt_rand(10, 50);//5-7个点赞
                    break;
                case 2:
                    $time_interval = 2 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(24)->getTimestamp();
                    $a =  mt_rand(10, 50);
                    break;
                case 3:
                    $time_interval = 3 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(36)->getTimestamp();
                    $a =  mt_rand(30, 60);
                    break;
                case 4:
                    $time_interval = 4 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(48)->getTimestamp();
                    $a = mt_rand(40, 70);
                    break;
                case 5:
                    $time_interval = 5 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(60)->getTimestamp();
                    $a =  mt_rand(50, 80);
                    break;
                case 6:
                    $time_interval = 6 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(72)->getTimestamp();
                    $a =  mt_rand(600, 100);
                    break;
                default:
                    $time_interval = 2 * 12 * 3600;
                    $tmplevel1 = $nowobj->addHours(24)->getTimestamp();
                    $a = mt_rand(10, 50);
                    break;
            }
        }
        $tmplevel1 = $good['endtime'];
        //计算用户商品区时间
        $loopcount = $a;
        $loopcount = self::getUserLikeBytime($good['endtime'], $time_interval, $loopcount);
        if ($loopcount == 0) {
            return 0;
        }
        //计算百分比
        if ($tmplevel1 <= $currenttimeunixtime) {
            return 0;
        }
        return $loopcount;
    }

    public static function getUserLikeBytime($endtime, $leveltime, $count): int
    {
        $difftime = $endtime - time();
        if ($difftime < 0) {
            return 0;
        }
        if ($leveltime > 0) {
            $count_o = $count * bcdiv($difftime, $leveltime, 3);
            $count_o = ceil($count_o);
            if ($count_o < 10) {
                $count_o = mt_rand($count_o, 10);
            }
            return $count_o < $count ? $count_o : $count;
        }
        return 0;
    }


}