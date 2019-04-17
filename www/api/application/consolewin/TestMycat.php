<?php

namespace app\consolewin;
ini_set('memory_limit', '256M');
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
use app\dataapi\model\Users;
use think\Config;
use think\Queue;
use app\dataapi\model\GoodsCollect;
use app\dataapi\server\MutilProcressServer;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class TestMycat extends Command
{
    protected function configure()
    {
        $this->setName('testmycat')
            ->setDescription('Command Test');
    }

    protected function execute(Input $input, Output $output)
    {
        $pic = $this->flagimageAll();
        $output->writeln('Command end');
    }



//自动修改没有执行的粉丝情况
    function flagimageAll()
    {
        $UserServer = new UserServer();
        $image = new Image();
        $hot_users = Db::connect('mycat104')->name('likegoodmessagelist')
            ->where('is_send', 0)
            ->where('start_time', '<', time())
            ->field('SQL_NO_CACHE goods_id,id,start_time,is_send,end_time,delete_time')
            ->limit(10)
            ->select();
        //循环处理数据库
        $deleteids = [];
        $pool = new \Jenner\SimpleFork\Pool();
        foreach ($hot_users as $value) {
            $pool->execute(new \Jenner\SimpleFork\Process(new MutilProcressServer(1,$value['goods_id'], $value['id'])));
        }
        $pool->wait();
    }


    public function ytest()
    {
        $GoodsCollect =new GoodsCollect();
        $hot_users = Db::table('tp_goods_collect')
            ->where('collect_id','>',43390459)
            ->field('collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time')
            ->chunk(200,
                function ($vulues)use($GoodsCollect){
                    $deleteids=[];
                    $str='insert into tp_goods_collect  (collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time) values  ';
                    foreach ($vulues as $value) {
                        if($value['delete_time']>0){
                            continue;
                        }
                        $strsql = $str ."({$value['collect_id']},{$value['user_gooods_id']},{$value['user_id']},{$value['goods_id']},{$value['add_time']},null)";

                        echo $strsql."\r\n";
                        $GoodsCollect->execute($strsql);

                    }
                   // echo  "is inserting\r\n";
                    //一次性保存
                }, 'collect_id');
    }


        //补录没有的数据
    public function buluno()
    {
        $GoodsCollect =new GoodsCollect();
        $hot_users = Db::table('tp_goods_collect')
            ->where('collect_id','>',40029153)
            ->where('collect_id','<',40135393)
            ->field('collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time')
            ->chunk(200,
                function ($vulues)use($GoodsCollect){
                    $str='insert into tp_goods_collect  (collect_id,user_gooods_id,user_id,goods_id,add_time,delete_time) values  ';
                    foreach ($vulues as $value) {
                        echo $value['collect_id']."\r\n";
                        if($value['delete_time']!=''){
                            continue;
                        }
                        if($GoodsCollect->where('collect_id',$value['collect_id'])->value('collect_id')){
                            continue;
                        }
                        try{
                            $strsql = $str ."({$value['collect_id']},{$value['user_gooods_id']},{$value['user_id']},{$value['goods_id']},{$value['add_time']},null)";
                            echo $strsql."\r\n";
                            $GoodsCollect->execute($strsql);
                        }catch(PDOException $e){

                            continue;

                        }
                    }
                    // echo  "is inserting\r\n";
                    //一次性保存
                }, 'collect_id');
    }

}