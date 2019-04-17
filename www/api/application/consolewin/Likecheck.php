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
use app\dataapi\model\Users;
use think\Config;
use think\Queue;
use app\dataapi\model\GoodsCollect;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Likecheck extends Command
{
    protected function configure()
    {
        $this->setName('likecheck')
            ->setDescription('Command Test');
    }

    protected function execute(Input $input, Output $output)
    {
        $pic = $this->ytest();
        $output->writeln('Command end');
    }


    //修正浏览与喜欢数量
    public function ytest()
    {
        $GoodsCollect =new GoodsCollect();
        $hot_users = Db::name('goods')
            ->where('goods_status', 1)
            ->where('is_toplatform',0)
            ->where('endTime', '>', time())
            ->field('goods_id,click_count')
            ->chunk(1,
                function ($vulues)use($GoodsCollect){
                    foreach ($vulues as $value) {
                     $goods_colect =   getusercollectcount($value['goods_id']);
                        if($goods_colect>$value['click_count']){
                            //增加喜欢数量
                            $fff = $goods_colect+ mt_rand(0,50);
                           $flag = Db::name('goods')->where('goods_id',$value['goods_id'])->update(['click_count' => $fff]);
                            echo "is inserting".$value['goods_id']."\r\n";
                        }

                    }
                }, 'goods_id');
    }


}