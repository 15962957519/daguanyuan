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

ini_set("memory_limit", "1024M");

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Autoconsurelike extends Command
{
    protected function configure()
    {
        $this->setName('autoconsurelike')
            ->setDescription('Command Test');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->consumermessage();
        $output->writeln('Command end');
    }


//消费消息
    function consumermessage()
    {
        //构造消息订阅者
        $configdata = Config::get('vhostmember');
        $configdata = $configdata['alimqconfig'];
        $consumer = new HttpConsumer($configdata);
        //启动消息订阅者
        $consumer->process();
        return;
    }


}