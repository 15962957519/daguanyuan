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
use app\dataapi\server\UserServer;
use app\dataapi\lib\Image;
use app\dataapi\model\GoodsShouc;
/**
 * @title   自动出价系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class AutoBidProduct extends Command
{
    protected function configure()
    {
        $this->setName('autobid')
            ->setDescription('Command autolike');
    }

    protected function execute(Input $input, Output $output)
    {

        $this->like();
        $output->writeln('Command autolike end');
    }


}