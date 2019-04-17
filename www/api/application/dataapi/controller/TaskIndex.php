<?php

namespace app\dataapi\controller;

use think\Controller;
use think\Db;
use think\Config;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use app\dataapi\model\Goods;
use app\dataapi\server\HttpConsumer;
use Carbon\Carbon;
use think\Log;
use app\api\server\UsersServer;
set_time_limit(1200);
class TaskIndex extends BaseApi
{
    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {

        $imageobg = new Image();
        $UserServer = new UserServer();
        $db_goods = Db::name('goods');
        $hot_users = Db::name('likegoodmessagelist')
            ->where('is_send', 0)
            ->where('start_time', '<', time())
            ->field('SQL_NO_CACHE id,goods_id,start_time,is_send,end_time,delete_time')
            ->order('id desc')
            ->limit(1)
            ->select();
        //  $b = function ($vulues) use ($db_goods, $imageobg, $UserServer) {
        foreach ($hot_users as $value) {
            //检查是否过期了
            $tepdata = $db_goods->where('goods_id', $value['goods_id'])->field('goods_id,endTime,delete_time')->find();
            if (!$tepdata || $tepdata['endTime'] < time() || $tepdata['delete_time'] > 0) {
                //过期删除任务
                Db::name('likegoodmessagelist')->where('id', $value['id'])->delete();
                continue;
            }
            try {
                $randlike = mt_rand(1, 3);
                do {
                    $randlike--;
                    $ddd = $imageobg->flagimage();

                    $userid = $ddd['user_id'];
                    if ($userid > 0 && $value['goods_id'] > 0) {
                        $flag = $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $value['goods_id']]);
                    }
                    echo "like" . $value['goods_id'] . "---{$value['id']}\r\n";
                } while ($randlike > 0);
                //更新数据库
                $sql = "delete from tp_likegoodmessagelist where id=" . $value['id'];
                Db::name('likegoodmessagelist')->execute($sql);
                // 提交事务
            } catch (\Exception $e) {
                // 回滚事务
                return false;
            }
        }

        //  return true;
        //  };
        //  $c =  call_user_func ($b, $hot_users);
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }


    function modifytodaynogoods()
    {
        $JobQeueobj = new  JobQeue();
        $hot_users = Db::table('tp_goods')
            ->where('is_heler_likeand', 0)
            ->where('endTime', '>', time())
            ->field('user_id,goods_id,is_upload,is_heler_likeand,endTime,vistorassign,hasvistorassin')
            ->limit(1)
            ->select();
        $ctime = time();
        $outids = [];
        //新添加的增加粉丝
        foreach ($hot_users as $value) {
            if( $value['vistorassign']>0 &&  $value['vistorassign'] <=$value['hasvistorassin']){
                Db::name('goods')->where('goods_id', $value['goods_id'])->update(['is_heler_likeand' => 1]);
                continue;
            }
            //使用阿里云的消息队列
            $flag=  $JobQeueobj->autoLikeGoods($value['user_id'], $value['goods_id'], $value['endTime'],false,$value['vistorassign'],$value['hasvistorassin']);
            if($flag){
                Db::name('goods')->where('goods_id', $value['goods_id'])->update(['is_heler_likeand' => 1]);
            }
            $outids[] = $value['goods_id'];
        }
        Response::create(['data' => $outids, 'status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }


//随机喜欢作品 a和浏览数量 不走消息列队
    function randlikegoods()
    {
        $gd = new Goods();
        $JobQeueobj = new  JobQeue();
        $hot_users = Db::name('goods');
        $maxgoods_id = $hot_users->field('max(goods_id) as  goods_id')->find();
        $lg = mt_rand(1, $maxgoods_id['goods_id']);
        $hot_data = Db::name('goods')
            ->where('endTime', '>', time())
            ->field('user_id,goods_id,is_upload,is_heler_likeand,endTime')
            ->where('goods_id', '>', $lg)
            ->where('goods_status', 1)
            ->limit(5)
            ->select();
        $ctime = time();
        Db::transaction(function () use ($hot_data, $JobQeueobj, $gd, $ctime) {
            foreach ($hot_data as $value) {
                self::like(['goods_id'=>$value['goods_id']]);
            }
        });
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }

    static function like($data)
    {
        $goods_id = $data['goods_id'];
        $randlike = mt_rand(1, 3);
        $image = new Image();
        $UserServer = new UserServer();
        $tmpdatauseridarray = Db::name('goods')->where('goods_id', $goods_id)->where('goods_status',1)->field(['user_id','endTime'])->find();
        if(isset($tmpdatauseridarray['endTime']) && $tmpdatauseridarray['endTime']<=time()){
            return false;
        }
        do {
            $randlike--;
            $ddd = $image->flagimage();
            $userid = $ddd['user_id'];
            if ($userid > 0 && $goods_id > 0) {
                $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
            }
        } while ($randlike > 0);
        return true;
    }


    //消费消息
    function consumermessage()
    {
        //构造消息订阅者
        $configdata = Config::get('vhostmember');
        $configdata= $configdata['alimqconfig'];
        $consumer = new HttpConsumer($configdata);
        //启动消息订阅者
        $consumer->processbyhttp();
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }


    /*
          *
          * 同步喜欢收藏到mycat数据库

          */
    function indexExpireProduct()
    {
        $hot_goods = Db::name('goods_collect01');
        $hot_goods = $hot_goods->where('is_send', 0)
            ->field('*')
            ->limit(30)
            ->select();
        $ids = [];
        $insertsql = '';
        foreach ($hot_goods as $v) {
            if ($v['delete_time'] > 0) {
                continue;
            }
            $data = [
                'collect_id' => $v['collect_id'],
                'user_gooods_id' => $v['user_gooods_id'],
                'user_id' => $v['user_id'],
                'goods_id' => $v['goods_id'],
                'add_time' => $v['add_time'],
                'delete_time' => Null
            ];
            try {
                //到mycat里面查询
                $likecount = Db::connect('mycat104')->name('goods_collect01')->where(['collect_id' => $v['collect_id']])->field('collect_id')->count();
                if (!$likecount) {
                    $ids[] = $v['collect_id'];
                    $insertsqlbefare = "insert into tp_goods_collect01 (collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time)  values (%collect_id%,%user_gooods_id%,%user_id%,%goods_id%,%add_time%,null);";
                    $insertsql .= str_replace(['%collect_id%', '%user_gooods_id%', '%user_id%', '%goods_id%', '%add_time%'], $data, $insertsqlbefare);
                    // insetintogoolect($data);
                } else {
                    Db::name('goods_collect01')->where('collect_id', $v['collect_id'])->delete(true);
                }
            } catch (\ErrorException $e) {
            }
        }
        if (!empty($insertsql)) {
            Db::connect('mycat104')->execute($insertsql);
        }
        if(!empty($ids)){
            Db::transaction(function () use ($ids) {
                $ids = implode(',', $ids);
                $deletesql = "delete from tp_goods_collect01 where collect_id in (" . $ids . ")";
                Db::name('goods_collect01')->execute($deletesql);
            });
        }
        Response::create(['status' => '200', 'code' => '1', 'message' => 'to mycat is sucess'], 'json')->header($this->header)->send();
    }

    //积分累计签到每月底更新清零
    public function clearSign(){
        $data = ['rignin_count' => 0, 'sign_get_points' => 0];
        Db::name('users')->where('1=1')->update($data);

    }



    //出价分配
    public function bidassgin(Request $request)
    {
        $autobidproductserver = new AutoBidProductServer();
        //获取首页24条数据
        $goods_ids = [];
        $data = $autobidproductserver->getIndexProductlistsBy(24);
        if (isset($data['data']) && !empty($data['data'])) {
            foreach ($data['data'] as $ks => $vs) {
                //判断是否分配过，分配过则不分配了，放弃操作
                if ($vs['is_helper_bid'] == 0) {
                    $bidcount = mt_rand(0, 12);
                    while ($bidcount > 0) {
                        if ($vs['endTime'] <= time() + 1000) {
                            break;
                        }
                        $mic = mt_rand(time() + 1000, $vs['endTime']);
                        $dt = Carbon::createFromTimestamp($mic);
                        //时间在零晨12-6点则放弃
                        if ($dt->hour <= 6 || $dt->hour == 12) {
                            continue;
                        }
                        $data = ['goods_id' => $vs['goods_id'], 'datetime' => $mic];
                        // echo "AutoBidProductServerof page {$vs['goods_id']}  task run\n";
                        $insertid = Db::name('forindex')->insertGetId($data);
                        if ($insertid > 0) {
                            //添加消息列队
                            try {
                                $JobQeueobj = new  JobQeue();
                                $JobQeueobj->autoBidGoods($vs['goods_id'], $mic, $insertid);
                            } catch (\RuntimeException $e) {
                                Log::record('日志信息', 'info' . $e);
                            }
                        }
                        $bidcount--;
                    }
                    $goods_ids[] = $vs['goods_id'];
                    Db::name('goods')->where('goods_id', $vs['goods_id'])->update(['is_helper_bid' => 1]);
                }
            }
            Response::create(['status' => '200', 'data' => $goods_ids, 'code' => '1', 'message' => 'to mycat is sucess'], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '400', 'code' => '1', 'error' => '', 'message' => 'to mycat is sucess'], 'json')->header($this->header)->send();
    }



    //获取首页出价信息
    public  function  chujia(){
            $autobidproductserver = new  \app\dataapi\server\AutoBidProductServer();
            //获取首页30条数据
            $data = $autobidproductserver->getIndexProductlistsBy(1);
            if (isset($data['data']) && !empty($data['data'])) {
                $JobQeueobj = new  JobQeue();
                foreach ($data['data'] as $ks => $vs) {
                    //判断是否分配过，分配过则不分配了，放弃操作
                    if ($vs['is_helper_bid'] == 0) {
                        $bidcount = mt_rand(0, 12);
                        while ($bidcount > 0) {
                            if ($vs['endTime'] <= time()+1000) {
                                break;
                            }
                            $mic = mt_rand(time() + 1000, $vs['endTime']);
                            $dt = Carbon::createFromTimestamp($mic);
                            //时间在零晨12-6点则放弃
                            if ($dt->hour <= 6 || $dt->hour == 12) {
                                continue;
                            }
                            $data = ['goods_id' => $vs['goods_id'], 'datetime' => $mic];
                            echo "AutoBidProductServerof page {$vs['goods_id']}  task run\n";
                            $insertid = Db::name('forindex')->insertGetId($data);
                            if ($insertid > 0) {
                                //添加消息列队
                                try {
                                    $JobQeueobj->autoBidGoods($vs['goods_id'], $mic, $insertid);
                                } catch (\RuntimeException $e) {
                                    Log::record('日志信息', 'info' . $e);
                                }
                            }
                            echo "AutoBidProductServerof page 1  task run\n";
                            $bidcount--;
                        }
                        Db::name('goods')->where('goods_id', $vs['goods_id'])->update(['is_helper_bid' => 1]);
                    } else {
                        echo "indexserver \r\n" . date('Y-m-d H:i:s');
                    }
                }
            }
    }

    //首页作品更新指定到随机用户
    function indexProductRandTouser()
    {
        $Image = new Image();
        $user_id = [22];
        $hot_goods = Db::name('Goods')
            ->whereIn('user_id', $user_id)
            ->whereNull('delete_time')
            ->field('goods_id,user_id,endTime,upload_time,last_update')
            ->order('goods_id DESC')
            ->find();
        Db::startTrans();
        try {
            $ddd = $Image->flagimage(0);
            echo "{$hot_goods['goods_id']}", PHP_EOL;
            //判断作品的时间段
            if($hot_goods['last_update']>0){
                $timeslot = $hot_goods['endTime'] -$hot_goods['last_update'];
            }else{
                $timeslot = $hot_goods['endTime'] -$hot_goods['upload_time'];
            }
            if($ddd['store_level']==1){
                $level = mt_rand(4,6);
                if ($timeslot> 60*3600) {
                    //钻石
                    $level = 5;
                }elseif($timeslot> 48*3600){
                    //金牌
                    $level =4;
                }
                //修改这个人的用户等级
                $UserServerobj = new UserServer();
                $flag = $UserServerobj->updateUserinfo($ddd['user_id'], ['is_authentication' => 1, 'store_level' => $level]);
                if (!$flag) {
                    Db::rollback();
                }
            }
            Db::name('Goods')->where('goods_id', $hot_goods['goods_id'])->update(['is_toplatform' => 1, 'user_id' => $ddd['user_id']]);

            // 提交事务
            Db::commit();
            Response::create(['status' => '2000', 'code' => '1', 'error' => '', 'message' => '成功'], 'json')->header($this->header)->send();
        } catch (\RuntimeException $e) {
            // 回滚事务
            Db::rollback();
            Response::create(['status' => '2002', 'code' => '2', 'error' => '', 'message' => '失败'], 'json')->header($this->header)->send();
        }
    }


    /*
    * 赠送会员粉丝 匹配一定数量
    *
    */
    function autoGiveFanUser()
    {
        $userpic = [];
        $imageobj = new Image();
        $hot_users = Db::name('users')
            ->alias('users')
            ->where('fictitious', 0)
            ->where('is_give_freefans_over', 0)
            ->whereNull('delete_time')
            ->field('is_give_freefans,user_id,fictitious,store_level')
            ->limit(1)
            ->select();
        $userLogic = new UsersLogic();
        $usersevers = new UsersServer();
        foreach ($hot_users as $v) {
            if (!$v['user_id']) {
                continue;
            }

            $user_info = $userLogic->get_info($v['user_id']); // 获取用户信息
            $user_data = $usersevers->usermsg($v['user_id']);
            $memberProductCount = $user_data['fans'];
            echo "正在执行{$v['user_id']}\r\n";
            echo "需要匹配的粉丝数量-{$memberProductCount}\r\n";
            if (!$memberProductCount) {
                continue;
            }
            $x = $memberProductCount - $v['is_give_freefans'];


            //每次随机给多少个
            if($x>=100){
                $x = mt_rand(10,30);
            }elseif($x<100 && $x>=1){
                $x = mt_rand(1,$x);
            }elseif($x <= 0){
                Db::name('users')
                    ->where('user_id', $v['user_id'])
                    ->update([
                        'is_give_freefans_over' =>1
                    ]);
                continue;
            }

            //实例化出
            while ($x > 0) {
                $r = mt_rand(1,9);
                if($r>4){
                    $arruserneed_p = flagimageAll(9,1);
                }else{
                    $arruserneed_p = flagimageAll(2,1);
                }
                //转换为一维数组
                if(!empty($arruserneed_p)){
                    $arruserneed_p = $arruserneed_p[0];
                }
                $arruserneed_p['user_id'] = intval($arruserneed_p['user_id']);

                // 启动事务
                Db::startTrans();
                try {
                    $flag = Db::table('tp_fans_collect')->where(['user_id' => $v['user_id'], 'fans_id' => $arruserneed_p['user_id']])->find();
                    if (!empty($flag)) {
                        // 回滚事务
                        Db::rollback();
                        continue;
                    }

                    //1代表是虚拟用户
                    if ($v['fictitious'] == 1 && $v['store_level'] == 1) {
                        if( $arruserneed_p['user_id']>0){
                            Db::table('tp_users')
                                ->where('user_id', $arruserneed_p['user_id'])
                                ->where('store_level', 1)
                                ->update([
                                    'store_level' => mt_rand(2, 5)
                                ]);
                        }
                    }
                    Db::table('tp_fans_collect')->insertGetId([
                        'user_id' => $v['user_id'],
                        'fans_id' => $arruserneed_p['user_id'],
                        'add_time' => time()
                    ]);
                    echo $x;
                    echo "\r\n";
                    ob_flush();  //把数据从PHP的缓冲中释放出来
                    flush();     //将释放出来的数据发送到浏览器
                    Db::table('tp_users')
                        ->where('user_id', $v['user_id'])
                        ->update([
                            'is_give_freefans' => ['exp', 'is_give_freefans+1'],
                            'is_give_freefans_datetime' => ['exp', 'unix_timestamp()']
                        ]);
                    // 提交事务
                    $is_give_freefans = Db::name('users')->where(['user_id' => $v['user_id']])->value('is_give_freefans');
                    if(isset($is_give_freefans) && $is_give_freefans ==$memberProductCount && $v['user_id']>0){
                        Db::name('users')
                            ->where('user_id', $v['user_id'])
                            ->update([
                                'is_give_freefans_over' =>1
                            ]);
                    }


                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }

                $x--;
            }
        }
        Response::create(['status' => '2002', 'code' => '2', 'error' => '', 'message' => 'ok'], 'json')->header($this->header)->send();
    }



}
