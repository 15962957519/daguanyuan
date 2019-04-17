<?php

namespace app\consolewin;

use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\Db;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Hook;
use think\Cache;
use app\dataapi\server\UserServer;
use app\dataapi\lib\Image;
use app\dataapi\server\BidOrderServer;
use app\dataapi\controller\JobQeue;
use app\dataapi\server\Weixin;
use app\dataapi\model\ThirdUsers;
use app\dataapi\model\GoodsCollect;
use app\dataapi\model\Users;
use think\Config;
use think\Queue;
use Hprose\Client as goalngclient;
use app\dataapi\model\FansCollect;
use app\dataapi\model\Goods;
use app\dataapi\server\HttpConsumer;
use app\dataapi\server\AutoBidProductServer;
ini_set("memory_limit", "1024M");

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Test extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Command Test');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->indexExpireProduct();
        $output->writeln('Command end');
    }


//首页作品更新指定到随机用户
    function indexProductRandTouser()
    {


        $Image = new Image();
        $user_id = [4164];
        //修改这个人的用户等级
        $UserServerobj = new UserServer();
        $hot_goods = Db::name('Goods')
            ->whereIn('user_id', $user_id)
            ->whereNull('delete_time')
            ->field('goods_id')
            ->chunk(5,
                function ($vulue) use ($Image, $UserServerobj) {
                    Db::startTrans();
                    try {
                        $ddd = $Image->flagimage(0);
                        foreach ($vulue as $v) {
                            echo "{$v['goods_id']}", PHP_EOL;
                            Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['is_toplatform' => 1, 'user_id' => $ddd['user_id']]);
                        }
                        $start = config('bidmembergrder.start');
                        $end   =   config('bidmembergrder.end');
                        $level = mt_rand( $start,$end);
                        //30*86400
                        $AADDTIME = $level * 0x278D00;
                        $flag = $UserServerobj->updateUserinfo($ddd['user_id'], ['is_authentication' => 1, 'level' => mt_rand(2, 5), 'reg_time' => time() - $AADDTIME]);
                        if (!$flag) {
                            Db::rollback();
                        }
                        // 提交事务
                        Db::commit();
                        return true;
                    } catch (\RuntimeException $e) {
                        // 回滚事务
                        Db::rollback();
                        return false;
                    }
                }
                , 'goods_id');
    }



    /*
          *
          * 同步喜欢收藏到mycat数据库

          */
    function indexExpireProduct()
    {
        //$goods_collect->whereNull('delete_time')
        $goods_collect = Db::connect('mycat104')->name('goods_collect');
        $hot_goods = $goods_collect->where('collect_id','>',10000000)
            ->field('*')
            ->chunk(10,
                function ($vulue) use ($goods_collect) {
                    $ids = [];
                    $insertsql = '';
                    foreach ($vulue as $v) {
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
                            //$likecount = Db::connect('mycat104')->name('goods_collect01')->where(['collect_id' => $v['collect_id']])->field('collect_id')->count();
                            $likecount = 0;
                            if (!$likecount) {
                                $ids[] = $v['collect_id'];
                                $insertsqlbefare = "insert into tp_goods_collect01 (collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time)  values (%collect_id%,%user_gooods_id%,%user_id%,%goods_id%,%add_time%,null);";
                                $insertsql .= str_replace(['%collect_id%', '%user_gooods_id%', '%user_id%', '%goods_id%', '%add_time%'], $data, $insertsqlbefare);
                                // insetintogoolect($data);
                            } else {
                                Db::connect('mycat104')->name('goods_collect')->where('collect_id', $v['collect_id'])->delete(true);
                            }
                        } catch (\ErrorException $e) {
                        }
                    }
                    if (!empty($insertsql)) {
                        Db::connect('mycat104')->execute($insertsql);
                    }
                    echo "ing", PHP_EOL;
                    if(!empty($ids)){
                        Db::transaction(function () use ($goods_collect,$ids) {
                            $ids = implode(',', $ids);
                            $deletesql = "delete from tp_goods_collect where collect_id in (" . $ids . ")";
                            echo $deletesql, PHP_EOL;
                            $goods_collect->execute($deletesql);
                        });
                    }
                },'collect_id');
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
        return;
    }

    /*
       *
       * 同步喜欢收藏到mycat数据库

       */
    function indexExpireProduct1()
    {
        $hot_goods = Db::name('goods_collect01');
        $hot_goods->where('is_send', 0)
            ->field('*')
            ->chunk(10,
                function ($vulue) {
                    $ids = [];
                    $insertsql = '';
                    foreach ($vulue as $v) {
                        if( $v['delete_time']>0){
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
                            $likecount = Db::connect('mycat104')->name('goods_collect')->where(['collect_id' => $v['collect_id']])->value('collect_id');
                            if (!$likecount) {
                                $ids[] = $v['collect_id'];
                                $insertsqlbefare= "insert into tp_goods_collect (collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time)  values (%collect_id%,%user_gooods_id%,%user_id%,%goods_id%,%add_time%,null);";
                                $insertsql .= str_replace(['%collect_id%', '%user_gooods_id%','%user_id%','%goods_id%', '%add_time%'],$data,$insertsqlbefare);
                                // insetintogoolect($data);
                            }else{
                                Db::name('goods_collect01')->where('collect_id', $v['collect_id'])->delete(true);
                            }
                        } catch (\ErrorException $e) {
                        }
                    }
                    if(!empty($insertsql)){
                       Db::connect('mycat104')->execute($insertsql);
                    }
                    echo "ing", PHP_EOL;
                   Db::name('goods_collect01')->whereIn('collect_id', $ids)->delete(true);
                }
            );
    }



    public function bidserver(){
        $autobidproductserver = new AutoBidProductServer();
        //获取首页24条数据
        $data = $autobidproductserver->getIndexProductlistsBy(24);

        if (isset($data['data']) && !empty($data['data'])) {
            foreach ($data['data'] as $ks => $vs) {
                //判断是否分配过，分配过则不分配了，放弃操作
                echo "AutoBidProductServerof page {$vs['goods_id']}  task run\n";
                Db::name('goods')->where('goods_id', $vs['goods_id'])->update(['is_helper_bid' => 1]);

            }
        }
    }


//补录当天没有执行的产品
    function modifytodaynogoods()
    {
        $gd = new Goods();
        $JobQeueobj = new  JobQeue();
        Db::table('tp_goods')
            ->where('upload_time', '>', 1508860800)
            ->where('endTime', '>', time())
            ->where('is_heler_likeand', 0)
            ->field('user_id,goods_id,is_upload,is_heler_likeand,endTime')
            ->chunk(10,
                function ($vulues) use ($JobQeueobj, $gd) {
                    $ctime = time();
                    //    Db::transaction(function () use ($vulues, $JobQeueobj, $gd, $ctime) {
                    foreach ($vulues as $value) {
                        //新添加的增加粉丝
                        echo "" . $value['goods_id'] . "\r\n";
                        if ($value['user_id'] != 4164 && $value['user_id'] != 4145 && $value['is_upload'] == 1) {
                            $JobQeueobj->autoForFansererVhostbyAli($value['user_id'], $ctime);
                        }
                        //使用阿里云的消息队列
                          $JobQeueobj->autoLikeGoods($value['user_id'], $value['goods_id'], $value['endTime']);
                        Db::name('goods')->where('goods_id', $value['goods_id'])->update(['is_heler_likeand' => 1]);
                        echo "" . $value['goods_id'] . " is ending\r\n";
                    }
                    //   });
                }, 'goods_id');
    }


//自动修改没有执行的粉丝情况
    function flagimageAll()
    {
        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        $UserServer = new UserServer();
        $image = new Image();
//        $hot_users =  Db::connect('mycat104')->name('likegoodmessagelist')
        $hot_users = Db::name('likegoodmessagelist')
            ->where('is_send', 0)
            ->where('start_time', '<', time())
            ->field('SQL_NO_CACHE goods_id,id,start_time,is_send,end_time,delete_time')
            ->chunk(100,
                function ($vulues) use ($UserServer, $image, $unixstamp, $configdata) {
                    $deleteids = [];
                    $need_delete = array_column($vulues, 'id');
                    $maxid = Db::table($configdata['membertime']['membername'])->max('id');
                    $ttmaxid = mt_rand(1, $maxid);
                    $datauser = Db::table($configdata['membertime']['membername'])->where('id', '>', $ttmaxid)->limit(100)->column('user_id');
                    echo "like  is ing";
                    foreach ($vulues as $k => $value) {
                        // 启动事务
                        try {
                            //  $randlike = mt_rand(1, 3);
                            //  do {
                            //    $randlike--;
                            if ($value['goods_id'] > 0) {
                                $UserServer->vhostuserUpdateLike($datauser[$k], ['goods_id' => $value['goods_id']]);
                            }
                            //   } while ($randlike > 0);
                            //更新数据库
//                            $deleteids[]=$value['id'];
                            // 提交事务
                        } catch (\Exception $e) {
                            // 回滚事务
                        }
                    }
                    Db::name('likegoodmessagelist')->whereIn('id', $need_delete)->delete(true);
                    echo "like  is end", PHP_EOL;
                    //一次性保存
                }, 'id');
    }


    public function paimaiquan()
    {

        //缓存用户数据 加 特定标签方便删除
        $configdata = config('cache');
        Cache::connect($configdata['redis']);

        $user_id_tag = 'userinfo';


        $userid = 86793;
        $user_info = Db::name('users')->where('user_id', 86793)->field('real_name,nickname,user_id,sex,user_money,address_id,last_login,mobile,nickname,is_lock,usersingnature,token,user_type,level,is_authentication,mobile_validated,reg_time,head_pic,is_give_freefans')->find();
        $userid = md5($userid);
        $flag = Cache::store('redis')->tag($user_id_tag)->set($userid, $user_info, 6000);

        $user_info = Cache::store('redis')->get($userid);

        var_dump($user_info);

        exit;
        $user_id = [4173, 2596];
        $Image = new Image();
        $hot_users = Db::table('tp_goods')
            ->whereIn('user_id', $user_id)
            ->where('endTime', '>', time())
            ->field('goods_id,endTime')
            ->chunk(3, function ($value) use ($Image) {
                try {
                    $ddd = $Image->flagimage();
                    $flag = false;
                    foreach ($value as $v) {
                        //    Db::startTrans();
                        Db::name('goods')->where('goods_id', $v['goods_id'])->update(['is_toplatform' => 3, 'user_id' => $ddd['user_id']]);
                        $currenttime = time();
                        if ($v['endTime'] <= $currenttime) {
//                            Db::rollback();
                            continue;
                        }
                        //--------------出价结束
                        //添加消息列队
                        $bidcount = mt_rand(0, 12);
                        while ($bidcount > 0) {
                            $mic = mt_rand(time(), $v['endTime']);
                            $dt = Carbon::createFromTimestamp($mic);
                            //时间在零晨12-6点则放弃
                            if ($dt->hour <= 6 || $dt->hour == 12) {
                                continue;
                            }
                            $data = ['goods_id' => $v['goods_id'], 'datetime' => $mic];
                            echo "AutoBidProductServerof page {$v['goods_id']}  task run\n";
                            $insertid = Db::name('forindex')->insertGetId($data);
                            if ($insertid > 0) {
                                //添加消息列队
                                try {
                                    $JobQeueobj = new  JobQeue();
                                    $JobQeueobj->autoBidGoods($v['goods_id'], $mic, $insertid);
                                } catch (\RuntimeException $e) {
                                    Log::record('日志信息', 'info' . $e);
                                }
                            }
                            // $mic = $mic - time();
                            //  Timer::add($mic, array($autobidproductserver, 'index'), array($vs['goods_id']), false);
                            echo "AutoBidProductServerof page 1  task run\n";
                            $bidcount--;
                        }
                        // 提交事务
                        //    Db::commit();
                        $flag = true;
                        //--------------出价结束
                    }

                    if ($flag === true) {
                        //修改这个人的用户等级
                        $UserServerobj = new UserServer();
                        $level = mt_rand(1, 5);
                        $AADDTIME = $level * 30 * 86400;
                        $UserServerobj->updateUserinfo($ddd['user_id'], ['is_authentication' => 1, 'level' => mt_rand(1, 5), 'reg_time' => time() - $AADDTIME]);


                        //关注用户不需要事务 影响效率
                        $FansCollect = new FansCollect();
                        $time = time();
                        $hot_users = Db::table('tp_users')
                            ->where('fictitious', 0)
                            ->where('level', '>', 1);
                        $randquery = mt_rand(0, 8);
                        if ($randquery > 4) {
                            $hot_users = $hot_users->where('user_id & 1');
                        } else {
                            $hot_users = $hot_users->where('mod(user_id,2)=0');
                        }
                        $hot_users = $hot_users->whereNull('delete_time')
                            ->field('user_id,fictitious')
                            ->chunk(20,
                                function ($vulue) use ($FansCollect, $ddd, $time) {
                                    $useriddata = [];
                                    //获取数组里面的userid
                                    if (!empty($vulue)) {
                                        $useriddata = array_column($vulue, 'user_id');
                                    }
                                    $insertsql = "insert into tp_fans_collect (user_id,fans_id,add_time) values ";
                                    $valuesstr = '';
                                    foreach ($useriddata as $k => $v) {
                                        if ($v % 4 == 0) {
                                            continue;
                                        }
                                        $valuesstr .= "({$v},{$ddd['user_id']},{$time}), ";
                                    }
                                    $valuesstr = substr($valuesstr, 0, -2);
                                    $insertsql .= $valuesstr;
                                    Db::execute($insertsql . " ON DUPLICATE KEY UPDATE delete_time=Null");
                                    //   echo "DUPLICATE page 1  task run{$ddd['user_id']}\n";
                                    echo "DUPLICATE page 1  task run\n";
                                }, 'user_id');
                    }
                    return true;
                } catch (\RuntimeException $e) {

                    echo $e->getMessage();
                    // 回滚事务
                    //   Db::rollback();
                    return false;
                }
            });


    }


    public function autoGiveFanUserss(int $user_id)
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($user_id); // 获取用户信息

        if (isset($user_info['result']['level']) && $user_info['result']['level'] == 1) {
            $fancollectcount = Db::name('fans_collect')->alias('a')->join('third_users w', 'a.fans_id=w.user_id')->where('a.user_id', $user_id)->whereNull('a.delete_time')->whereNull('w.delete_time')->count(); //获取收藏数量    粉丝
            if ($fancollectcount > 80) {
                //减少粉丝
                $limit = mt_rand(1, 20);
                $useridarray = Db::table('tp_fans_collect')->where('user_id', $user_id)->whereNull('delete_time')->order('add_time asc')->limit($limit)->column('collect_id');

                if (!empty($useridarray)) {

                    $useridarray = array_unique($useridarray);
                }
                Db::table('tp_fans_collect')
                    ->whereIn('collect_id', $useridarray)
                    ->update([
                        'delete_time' => time()
                    ]);
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

    //测试用户喜欢是否正常
    public function usergoodslike()
    {


        $configdata = Config::get('rpc');
        $golangurl = $configdata['golang']['url'];
        $client = goalngclient::create($golangurl, false);


        var_dump($client->hello("world"));
        //var_dump($client->sum(1, 2, 3));
        $limit = 5;

        exit;


        $arruserneed_np = $this->flagimageAll(2);
        $arruserneed_p = $this->flagimageAll(9);
        $UserServer = new UserServer();
        $hot_users = Db::table('tp_likegoodmessagelist')
            ->where('is_send', 0)
            ->where('start_time', '<', time())
            ->where('id', '>', 7000000)
            ->field('goods_id,id,start_time,is_send,end_time,delete_time')
            ->chunk(50,
                function ($vulues) use ($arruserneed_np, $arruserneed_p, $UserServer) {
                    foreach ($vulues as $value) {
                        // 启动事务
                        try {
                            $randlike = mt_rand(1, 3);
                            do {
                                $randlike--;
                                $rangmax = mt_rand(1, 10);
                                if ($rangmax > 5) {
                                    $needuseridata_index = array_rand($arruserneed_p, 1);
                                    if (!isset($arruserneed_p[$needuseridata_index])) {
                                        continue;
                                    }
                                    $ddd = $arruserneed_p[$needuseridata_index];
                                } else {
                                    $needuseridata_index = array_rand($arruserneed_np, 1);
                                    if (!isset($arruserneed_np[$needuseridata_index])) {
                                        continue;
                                    }
                                    $ddd = $arruserneed_np[$needuseridata_index];
                                }
                                //  $ddd = (new Image())->flagimage();
                                $userid = $ddd['user_id'];

                                if ($userid > 0 && $value['goods_id'] > 0) {
                                    $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $value['goods_id']]);
                                }
                            } while ($randlike > 0);
                            echo "喜欢了" . $value['id'] . "\r\n";
                            //更新数据库
                            Db::name('likegoodmessagelist')->where('id', $value['id'])->update(['is_send' => 1]);


                            // 提交事务
                        } catch (\Exception $e) {
                            // 回滚事务
                        }
                    }
                }, 'id');
        exit;
        $data['goods_id'] = 247389;
        $data['id'] = 7645840;
        $goods_id = $data['goods_id'];
        $randlike = mt_rand(1, 3);
        do {
            $randlike--;
            $ddd = (new Image())->flagimage();
            $userid = $ddd['user_id'];
            $UserServer = new UserServer();
            if ($userid > 0 && $goods_id > 0) {
                $flag = $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
            }
            //file_put_contents(RUNTIME_PATH . 'FANWERSER.php', $goods_id . PHP_EOL, FILE_APPEND|LOCK_EX);
            //  echo "喜欢了{$goods_id}\r\n";
        } while ($randlike > 0);

        //更新数据库
        Db::name('likegoodmessagelist')->where('id', $data['id'])->update(['is_send' => 1]);
    }


    //复制表信息到临时表

    public function copy2tpgoodscollect2_temp()
    {
        $aaa = time();
        $GoodsCollect = new GoodsCollect();
        $userpic = [];
        $hot_users = Db::table('tp_goods_collect')
            ->field('collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time')
            ->chunk(900,
                function ($vulue) use ($GoodsCollect) {
                    // 启动事务
                    try {
//                        foreach ($vulue as $v) {
//                            unset($v['collect_id']);
//                          //  echo time()."\r\n";
//                            if (isset($v) && !empty($v)) {
//                              //  $GoodsCollect->insert($v);
//                            }
//                        }
                        // 提交事务
                    } catch (\Exception $e) {
                        // 回滚事务
                    }
                }, 'collect_id');

        echo time() - $aaa;
    }


    //临时喜欢或者更新

    public function updatevhost()
    {
        $JobQeueobj = new  JobQeue();
        $goods_id_array = [245612, 245193, 244484, 243598, 243526, 243316, 243135];
        foreach ($goods_id_array as $goods_id_value) {
            $data = Db::table('tp_goods')->where('goods_id', $goods_id_value)->find();
            $JobQeueobj->autoLikeGoods($data['user_id'], $goods_id_value, (int)$data['endTime']);
            $JobQeueobj->autoForFansererVhost($data['user_id'], time());
            $JobQeueobj->autoLikeGoods($data['user_id'], $goods_id_value, $data['endTime']);
        }
    }

    public function addautoLikeGoodsspec($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        $JobQeueobj = new  JobQeue();
        $JobQeueobj->autoLikeGoods(9871, 126202, 1501196400);

    }


    public function modifyYesterdayUserNotLikeGoods()
    {


        $subQuery = Db::name('bid_order')
            ->alias('bo')
            ->where('bo.goods_id=a.goods_id')
            //->where('bo.is_gernerorder',0)
            ->field('*')
            ->buildSql();
        $BidOrderServer = new BidOrderServer();
        $hot_goods = Db::name('Goods')
            ->alias('a')
            //->where('a.is_gernerorder',0)
            ->where('a.endTime', '<', time())
            ->whereNull('a.delete_time')
            ->whereExists($subQuery)
            ->field('a.*')
            ->chunk(50,
                function ($vulue) use ($BidOrderServer) {
                    if (!empty($vulue)) {
                        foreach ($vulue as $v) {
                            //发送给微信
                            $tempborderdata = Db::name('bid_order')->where('goods_id', $v['goods_id'])->where('upload_time', $v['upload_time'])
                                ->where('bid_price', '=', function ($query) use ($v) {
                                    $query->name('bid_order')
                                        ->where('goods_id', $v['goods_id'])
                                        ->where('upload_time', $v['upload_time'])
                                        ->field('max(bid_price) as bid_price');
                                })->find();


                            if (!isset($tempborderdata['id'])) {
                                continue;
                            }
                            $tempborderdataordergoods = Db::name('order_goods')->where('goods_id', $v['goods_id'])->where('goods_price', $tempborderdata['bid_price'])->where('upload_time', $v['upload_time'])->count();
                            if ($tempborderdataordergoods > 0) {
                                continue;
                            }

                            $order_no = $BidOrderServer->build_order_no();
                            //生成订单表数据
                            $data = [
                                'order_sn' => $order_no,
                                'user_id' => $tempborderdata['user_id']??0,
                                'add_time' => time(),
                                'sale_user_id' => $v['user_id'],
                                'order_amount' => $tempborderdata['bid_price'],
                                'total_amount' => $tempborderdata['bid_price']
                            ];

                            try {
                                $insetid = Db::name('order')->insertGetId($data);
                            } catch (PDOException $e) {
                                echo $e->getMessage();
                                continue;
                            }
                            echo $v['goods_id'];
                            echo "\r\n";
                            Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['goods_status' => 2, 'is_gernerorder' => 1]);
                            Db::name('bid_order')->where('id', $tempborderdata['id'])->update(['is_gernerorder' => 1]);

                            if ($insetid > 0) {
                                $dataorder = [
                                    'order_id' => $insetid,
                                    'goods_id' => $v['goods_id'],
                                    'goods_price' => $tempborderdata['bid_price'],
                                    'upload_time' => $v['upload_time'],
                                ];
                                Db::name('order_goods')->insertGetId($dataorder);


                                $dataorderaction = [
                                    'order_id' => $insetid,
                                    'log_time' => time(),
                                ];
                                Db::name('order_action')->insertGetId($dataorderaction);


                            }

                        }
                    }
                }, 'a.goods_id');

        exit;


        $data = Db::table('tp_goods')->where('user_id', 3794)->select();
        $JobQeueobj = new  JobQeue();
        foreach ($data as $goods_id_value) {
            $tempcount = Db::table('tp_goods_collect')->where('goods_id', $goods_id_value['goods_id'])->whereNotNull('delete_time')->count();

            //  if($tempcount<20){
            echo "{$goods_id_value['goods_id']}\r\n";
            $JobQeueobj->autoLikeGoods($goods_id_value['user_id'], $goods_id_value['goods_id'], (int)$goods_id_value['endTime']);
            $JobQeueobj->autoForFansererVhost($goods_id_value['user_id'], time());
            // }

        }


    }


    public function generdatabases()
    {
        $dbnode = range(7, 300);

        foreach ($dbnode as $v) {
            $sql = "create database db{$v} DEFAULT CHARSET utf8 COLLATE utf8_general_ci";
            Db::query($sql);
            echo "{$v}";
        }
        exit;
    }

    public function ytest()
    {


//        $redis = new \Redis;
//       $flag = $redis->connect('101.37.175.6', 6379, 0);
//        $flag = $redis->auth('qwjktianok');
//       var_dump($flag );
//        exit;
//       Cache::store('redis')->set('daad',77777);
//     $ff =  Cache::store('redis')->get('daad');
//     echo $ff;
        //    Queue::push( 'dadadadadadadadadad' , [] , 'daadadaad' );
        exit;
        $tempsql = 'SELECT u.user_id ,u.head_pic,u.delete_time from tp_users as u  where  u.`fictitious` = 1   AND u.`level` > 1 and u.reg_time> ' . $unixstamp . ' and u.delete_time is null and not  EXISTS (select *  from tp_bid_order as bo WHERE  u.user_id=bo.user_id and bo.add_time >' . $larest15day . ' ) order by rand() limit 1 ';


        $rows = Db::query($tempsql);

        var_dump($rows[0]['user_id']);

        $newros = $rows;


    }

    //复制表信息到临时表

    public function copytpuserstotp_userstemp()
    {
        $imageobj = new Image();
        $userpic = [];
        $hot_users = Db::table('tp_users')
            ->where('fictitious', 1)
            ->where('reg_time', '>', 1497574800)
            ->whereNotNull('head_pic')
            ->whereNull('delete_time')
            ->field('user_id,head_pic')
            ->chunk(100,
                function ($vulue) use ($imageobj) {
                    $data = [];
                    foreach ($vulue as $v) {
                        if (!$v['user_id'] || empty($v['head_pic'])) {
                            continue;
                        }
                        $flag = $imageobj->imagetest($v['head_pic']);
                        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                            continue;
                        }
                        $data[] = ['user_id' => $v['user_id'], 'head_pic' => $v['head_pic']];
                        echo "{$v['user_id']}正在执行\r\n";
                    }
                    if (isset($data) && !empty($data)) {
                        Db::table('tp_users_20170914')->insertAll($data);
                    }
                }, 'user_id');
    }

    public function asss()
    {


    }


    public function weixinavatar()
    {
        $jsapiobj = new  Weixin();
        $Users = new  ThirdUsers();
        $Users_l = new  Users();
        $hot_goods = Db::table('tp_third_users')
            ->alias('g')
            ->where('g.subscribe', 1)
            ->where('g.user_id', 86276)
            ->whereNull('g.delete_time')
            //->whereLike('g.head_pic','%mmopen/ver_1/%')
            ->field('g.user_id,g.openid,g.head_pic,g.subscribe')
            ->chunk(1, function ($values) use ($jsapiobj, $Users, $Users_l) {
                foreach ($values as $v) {
                    echo "正在执行{$v['user_id']}\r\n";
                    //  $token = $jsapiobj->getAccessToeknCode();
                    $token = 'cPpO453xbrO_bl2BQotoOGM5xxHKNtATh1xMXm63JA3ceLs_uWtsOVrmqHttRGHWMhOsnT3zscyks7cJ9EMTWhk6b-wjwdNvcK0DmcFKG4HGwUyUAHLBR39SaJLK0z56RQFcAEAWVK';
                    $userinfoobj = $jsapiobj->getUserinfo($token, $v['openid']);
                    $userinfoobj = stripslashes($userinfoobj);
                    $userinfoobj = json_decode($userinfoobj, true);
                    if (!isset($userinfoobj['errmsg']) && (isset($userinfoobj['subscribe']) && 1 == $userinfoobj['subscribe'])) {
                        // 启动事务
                        Db::startTrans();
                        try {
                            $data = [
                                'head_pic' => $userinfoobj['headimgurl'],
                                'nickname' => $userinfoobj['nickname'],
                                'lastsynctime' => time()
                            ];
                            //更新用户头像和姓名
                            $Users->where(['user_id' => $v['user_id'], 'openid' => $v['openid']])->update($data);
                            unset($data['lastsynctime']);
                            $Users_l->where('user_id', $v['user_id'])->update($data);
                            // 提交事务
                            Db::commit();
                        } catch (\Exception $e) {

                            echo $e->getMessage();
                            // 回滚事务
                            Db::rollback();
                        }
                    }
                    unset($userinfoobj);
                }
            });
    }
//赠送会员粉丝

    /*
 * 赠送会员粉丝
 *
 */
    public function autoGiveFanUser()
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        //实例化出
        $arruserneed_p = $this->flagimageAll(9);
        $arruserneed_np = $this->flagimageAll(2);
        $hot_users = Db::table('tp_users')
            ->where('fictitious', 0)
            ->where('level', '>', 1)
            ->whereNull('delete_time')
            ->field('is_give_freefans,user_id,fictitious')
            ->chunk(10,
                function ($vulue) use (&$userpic, $arruserneed_p, $arruserneed_np, $userLogic) {
                    foreach ($vulue as $v) {
                        if (!$v['user_id']) {
                            continue;
                        }
                        $user_info = $userLogic->get_info($v['user_id']); // 获取用户信息
                        $memberlevel = $user_info['result']['level'];
                        $memberProductCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'membeFansCount', $memberlevel);
                        echo "正在执行{$v['user_id']}\r\n";
                        if (!$memberProductCount) {
                            continue;
                        }
                        $x = $memberProductCount - $v['is_give_freefans'];
                        if ($x <= 0) {
                            continue;
                        }
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

//                            $flag = $this->imagetest($tempdatauser['head_pic']);
//                            if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
//                                continue;
//                            }
                            // 启动事务
                            Db::startTrans();
                            try {
                                //   $ddd = $this->flagimage();
                                $flag = Db::table('tp_fans_collect')->where(['user_id' => $v['user_id'], 'fans_id' => $tempdatauser['user_id']])->find();
                                if (!empty($flag)) {
                                    // 回滚事务
                                    Db::rollback();
                                    continue;
                                }
                                //1代表是虚拟用户
                                if ($v['fictitious'] == 1 && $v['level'] == 1) {
                                    Db::table('tp_users')
                                        ->where('user_id', $tempdatauser['user_id'])
                                        ->where('level', 1)
                                        ->update([
                                            'level' => mt_rand(2, 5)
                                        ]);
                                }
                                Db::table('tp_fans_collect')->insertGetId([
                                    'user_id' => $v['user_id'],
                                    'fans_id' => $tempdatauser['user_id'],
                                    'add_time' => time()
                                ]);
                                echo $x;
                                echo "\r\n";
                                $x--;
                                Db::table('tp_users')
                                    ->where('user_id', $v['user_id'])
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
                }
            );
        return $userpic;
    }

    /*
   * 自动关注 会员的 非会员的
   *
   */
    public function autoLikeNotMemberUser()
    {
        $userpic = [];
        $hot_users = Db::table('tp_users')
            ->whereNull('delete_time')
            ->where('fictitious', 0)
            ->where('level', 1)
            ->field('user_id')
            ->chunk(10,
                function ($vulue) use (&$userpic) {
                    foreach ($vulue as $v) {
                        //获取作品id
                        $ProductServerobj = new       ProductServer();
                        $productids = $ProductServerobj->getUserProductds($v['user_id']);

                        foreach ($productids as $id) {
                            $x = mt_rand(1, 30);
                            $y = 0;

                            // 启动事务
                            Db::startTrans();
                            try {
                                do {
                                    $ddd = $this->flagimage();
                                    $flag = Db::table('tp_goods_collect')->where(['user_gooods_id' => $ddd['user_id'], 'user_id' => $v['user_id']])->whereOr(['goods_id' => $id, 'user_id' => $ddd['user_id']])->find();
                                    if (!empty($flag)) {
                                        continue;
                                    }
                                    Db::table('tp_goods_collect')->insertGetId([
                                        'user_id' => $ddd['user_id'],
                                        'user_gooods_id' => $v['user_id'],
                                        'goods_id' => $id,
                                        'add_time' => time()
                                    ]);
                                    $x--;
                                } while ($x >= 0);

                                //更新作品的浏览次数
                                $xf = mt_rand($x, $x + 30);
                                if ($id > 0) {
                                    Db::table('tp_goods')
                                        ->where('goods_id', $id)
                                        ->update([
                                            'click_count' => ['exp', $xf]
                                        ]);
                                }
                                // 提交事务
                                Db::commit();
                            } catch (\Exception $e) {
                                // 回滚事务
                                Db::rollback();
                            }
                        }
                    }
                }
            );
        return $userpic;
    }

    /*
 * 自动关注 会员的
 *
 */
    public function autoLikeUser()
    {
        $userpic = [];
        $hot_users = Db::table('tp_users')
            ->whereNull('delete_time')
            ->where('fictitious', 0)
            ->where('level', '>', 1)
            //  ->where('user_id',3065)
            ->field('user_id')
            ->chunk(10,
                function ($vulue) use (&$userpic) {
                    foreach ($vulue as $v) {
                        //获取作品id
                        $ProductServerobj = new       ProductServer();
                        $productids = $ProductServerobj->getUserProductds($v['user_id']);

                        foreach ($productids as $id) {
                            $x = mt_rand(1, 30);
                            $y = 0;

                            // 启动事务
                            Db::startTrans();
                            try {
                                do {
                                    $ddd = $this->flagimage();
                                    $flag = Db::table('tp_goods_collect')->where(['user_gooods_id' => $ddd['user_id'], 'user_id' => $v['user_id']])->whereOr(['goods_id' => $id, 'user_id' => $ddd['user_id']])->find();

                                    if (!empty($flag)) {
                                        continue;
                                    }
                                    Db::table('tp_goods_collect')->insertGetId([
                                        'user_id' => $ddd['user_id'],
                                        'user_gooods_id' => $v['user_id'],
                                        'goods_id' => $id,
                                        'add_time' => time()
                                    ]);
                                    $x--;
                                } while ($x >= 0);

                                //更新作品的浏览次数
                                $xf = mt_rand($x, $x + 30);
                                if ($id > 0) {
                                    Db::table('tp_goods')
                                        ->where('goods_id', $id)
                                        ->update([
                                            'click_count' => ['exp', $xf]
                                        ]);
                                }
                                // 提交事务
                                Db::commit();
                            } catch (\Exception $e) {
                                // 回滚事务
                                Db::rollback();
                            }
                        }
                    }
                }
            );
        return $userpic;
    }


    /*
     * 虚拟会员点亮头像
     *
     */
    public function vhostmemberUpdateLevel()
    {
        $userpic = [];
        $hot_users = Db::table('tp_users')
            ->whereNull('delete_time')
            ->where('fictitious', 1)
            ->where('level', 1)
            ->field('user_id,head_pic')
            ->chunk(10,
                function ($vulue) use (&$userpic) {
                    foreach ($vulue as $v) {
                        Db::table('tp_users')->where('user_id', $v['user_id'])->update(['level' => mt_rand(2, 5)]);
                    }
                }
            );
        return $userpic;
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


    public function filterUserHeadPicRelation()
    {
        $userpic = [];
        $hot_goods = Db::table('tp_goods')
            ->alias('a')
            ->join('users yyyq', 'a.user_id=yyyq.user_id')
            ->whereNull('a.delete_time')
            ->where('a.is_toplatform', 1)
            ->group('a.user_id')
            ->field('a.*,yyyq.*')
            ->chunk(10,
                function ($vulue) {
                    foreach ($vulue as $v) {
                        $datauser = Db::table('tp_users')->where('user_id', $v['user_id'])->where('fictitious', 1)->order('rand()')->find();
                        if (empty($datauser['head_pic'])) {
                            $ddd = $this->flagimage();
                            // 启动事务
                            Db::startTrans();
                            try {
                                Db::table('tp_goods')->where('goods_id', $v['goods_id'])->update(['user_id' => $ddd['user_id']]);
                                Db::table('tp_goods')->where('user_id', $v['user_id'])->update(['user_id' => $ddd['user_id']]);
                                Db::table('tp_users')->where('user_id', $v['user_id'])->update(['level' => mt_rand(2, 5)]);
                                // 提交事务
                                Db::commit();
                            } catch (\Exception $e) {
                                // 回滚事务
                                Db::rollback();
                            }
                            continue;
                        } else {
                            $flag = $this->imagetest($datauser['head_pic']);
                            if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                                $ddd = $this->flagimage();
                                // 启动事务
                                Db::startTrans();
                                try {
                                    Db::table('tp_goods')->where('goods_id', $v['goods_id'])->update(['user_id' => $ddd['user_id']]);
                                    Db::table('tp_goods')->where('user_id', $v['user_id'])->update(['user_id' => $ddd['user_id']]);
                                    Db::table('tp_users')->where('user_id', $v['user_id'])->update(['level' => mt_rand(2, 5)]);
                                    // 提交事务
                                    Db::commit();
                                } catch (\Exception $e) {
                                    // 回滚事务
                                    Db::rollback();
                                }
                                continue;
                            }
                        }
                    }
                }
            );
        return $userpic;
    }


    public function filterUserHeadPic()
    {
        $userpic = [];
        $hot_users = Db::name('users')
            ->where('fictitious', 1)
            ->whereNull('delete_time')
            ->field('user_id,head_pic')
            ->chunk(10,
                function ($vulue) use (&$userpic) {
                    foreach ($vulue as $v) {
                        if (!$this->imagetest($v['head_pic'])) {
                            $userpic[$v['user_id']] = $v['head_pic'];
                        }
                    }
                }
            );
        return $userpic;
    }

    //首页作品更新指定到随机用户
    protected function indexProduct()
    {
        exit;
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id')
            ->whereNull('a.delete_time')
            ->where('a.endTime', '<=', time())
            ->where('a.is_toplatform', 1)
            ->field('a.*,q.*')
            ->chunk(10,
                function ($vulue) {
                    foreach ($vulue as $v) {
                        $datauser = Db::name('users')->where('user_id', $v['user_id'])->where('fictitious', 1)->order('rand()')->find();
                        if (empty($datauser['head_pic'])) {
                            $ddd = $this->flagimage();
                            Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['user_id' => $ddd['user_id']]);
                            continue;
                        } else {
                            $flag = $this->imagetest($datauser['head_pic']);
                            if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                                $ddd = $this->flagimage();
                                Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['user_id' => $ddd['user_id']]);
                                continue;
                            }
                        }
                    }
                }
            );
    }


    public function flagimage()
    {
        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        $rangmax = mt_rand(1, 10);
        if ($rangmax >= 3) {
            $datauser = Db::name('Goods')
                ->alias('a')
                ->join('users q', 'a.user_id=q.user_id')
                ->whereNull('a.delete_time')
                ->field('a.*,q.*')
                ->group('a.user_id')
                ->order('rand()')
                ->find();
        } else {
            $datauser = Db::name('users')->where('fictitious', 1)->where('reg_time', '>', $unixstamp)->whereNull('delete_time')->order('rand()')->field('user_id,head_pic')->find();
        }
        $flag = $this->imagetest($datauser['head_pic']);
        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
            return $this->flagimage();
        }
        return $datauser;

    }

//    public function flagimageAll($rangmax)
//    {
//
//        $configdata = Config::get('vhostmember');
//        $unixstamp = $configdata['membertime']['unixstamp'];
//        if ($rangmax >= 3) {
//            $datauser = Db::name('Goods')
//                ->alias('a')
//                ->join('users q', 'a.user_id=q.user_id')
//                ->whereNull('a.delete_time')
//                ->whereNull('q.delete_time')
//                ->field('q.head_pic,q.user_id')
//                ->group('a.user_id')
//                ->select();
//        } else {
//            //  $datauser = Db::name('users')->where('fictitious', 1)->where('reg_time', '>', $unixstamp)->whereNull('delete_time')->field('user_id,head_pic')->select();
//            $datauser = Db::table($configdata['membertime']['membername'])->field('user_id,head_pic')->select();
//        }
//
//        return $datauser;
//
//    }


    protected function updateUseridProduct()
    {


//        $client = new Client([
//            // Base URI is used with relative requests
//            'base_uri' => '',
//            // You can set any number of default request options.
//            'timeout' => 2.0,
//        ]);
//        $GuRequest = new GuRequest('get', 'http://w.tianbaoweipai.com/static/img/user/nanlhhhhian.jpg');
//        $response = $client->send($GuRequest, ['timeout' => 2, 'verify' => false]);
//        $body = $response->getHeader('Content-Length');

        $specuserid = [2609, 3102, 2617, 3179, 3214, 2717, 2612];


        $hot_goods = Db::name('goods')
            ->alias('a')
            ->join('users u', 'u.user_id=a.user_id')
            ->whereNull('a.delete_time')
            ->whereIn('a.user_id', $specuserid)
            ->where('a.is_assgin', 0)
            ->field('a.*')
            ->chunk(3,
                function ($vulue) {
                    $datauser = Db::name('users')->where('fictitious', 1)->order('rand()')->find();

                    if (empty($datauser['head_pic'])) {
                        return false;
                    }

                    $flag = $this->imagetest($datauser['head_pic']);

                    if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                        return false;
                    }

                    $userid = $datauser['user_id'];
                    $cat_idata = Db::name('goods')->where('user_id', $userid)->field('cat_id')->select();
                    if (is_array($cat_idata)) {
                        $cat_idnew_data = array_column($cat_idata, 'cat_id');
//                        foreach ($vulue as $key=>$v){
//                            if(in_array($v['cat_id'],$cat_idnew_data)){
//                                        continue;
//                            }
//                            //更新用户user_id
//                            if(!in_array($v['goods_id'],[1913,1877,1873,1874,1933])){
//                                // 启动事务
//                                Db::startTrans();
//                                try{
//                                    Db::name('Goods')->where('goods_id',$v['goods_id'])->update(['user_id' =>$userid,'is_assgin'=>1,'is_toplatform'=>1]);
//                                    Db::name('users')->where('user_id',$userid)->update(['level' =>mt_rand(1,5)]);
//                                    // 提交事务
//                                    Db::commit();
//                                } catch (\Exception $e) {
//                                    // 回滚事务
//                                    Db::rollback();
//                                }
//                            }
//                        }
                    }
                }
            );
    }


    protected function aupdateUseridProduct()
    {


//        $client = new Client([
//            // Base URI is used with relative requests
//            'base_uri' => '',
//            // You can set any number of default request options.
//            'timeout' => 2.0,
//        ]);
//        $GuRequest = new GuRequest('get', 'http://w.tianbaoweipai.com/static/img/user/nanlhhhhian.jpg');
//        $response = $client->send($GuRequest, ['timeout' => 2, 'verify' => false]);
//        $body = $response->getHeader('Content-Length');

        $specuserid = [2609, 3102, 2617, 3179, 3214, 2717, 2612];


        $hot_goods = Db::name('goods')
            ->alias('a')
            ->join('users u', 'u.user_id=a.user_id')
            ->whereNull('a.delete_time')
            ->whereIn('a.user_id', $specuserid)
            ->where('a.is_assgin', 0)
            ->field('a.*')
            ->chunk(3,
                function ($vulue) {
                    $datauser = Db::name('users')->where('fictitious', 1)->order('rand()')->find();

                    if (empty($datauser['head_pic'])) {
                        return false;
                    }

                    $flag = $this->imagetest($datauser['head_pic']);

                    if ($flag && isset($flag['width']) && $flag['width'] > 0 && isset($flag['height']) && $flag['height'] > 0) {
                        $userid = $datauser['user_id'];
                        $cat_idata = Db::name('goods')->where('user_id', $userid)->field('cat_id')->select();
                        if (is_array($cat_idata)) {
                            $cat_idnew_data = array_column($cat_idata, 'cat_id');
                            foreach ($vulue as $key => $v) {
                                // 启动事务
                                Db::startTrans();
                                try {
                                    Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['is_assgin' => 1, 'is_toplatform' => 1]);
                                    // 提交事务
                                    Db::commit();
                                } catch (\Exception $e) {
                                    // 回滚事务
                                    Db::rollback();
                                }

                            }
                        }
                    }
                }
            );
    }


    public function dd()
    {

        $subQuery = Db::name('GoodsImages')
            ->field('min(img_id),goods_id,rescourse_id ')
            ->whereNull('delete_time')
            ->group('goods_id')
            ->buildSql();


        $specuserid = [2609, 3102, 2617, 3179, 3214, 2717, 2612];

        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join($subQuery . ' w', 'a.goods_id = w.goods_id')
            ->join('users q', 'a.user_id=q.user_id')
            ->whereNull('a.delete_time')
            ->whereIn('a.user_id', $specuserid)
            ->where('a.endTime', '>', $_SERVER['REQUEST_TIME'])
            ->field('a.*,q.*')
            ->group('a.user_id')
            ->order('a.user_id DESC')
            ->buildSql();


        echo $hot_goods;
    }

    //首页作品更新指定到随机用户
    protected function indexUserAuthentication()
    {
        $hot_users = Db::name('users')
            ->whereNull('delete_time')
            ->where('level', ">", 1)
            ->field('user_id,head_pic')
            ->chunk(10,
                function ($vulue) use (&$userpic) {
                    foreach ($vulue as $v) {
                        // 启动事务
                        Db::startTrans();
                        echo "\r\n";
                        echo $v['user_id'];
                        try {
                            Db::name('users')->where('user_id', $v['user_id'])->update(['is_authentication' => 1]);
                            // 提交事务
                            Db::commit();
                        } catch (\Exception $e) {
                            // 回滚事务
                            Db::rollback();
                        }
                    }
                }
            );
    }

}