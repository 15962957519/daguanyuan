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
//每月底 定时清理 累计签到
class SignClear extends Command
{
    protected function configure()
    {
        $this->setName('signclear')
            ->setDescription('Command signclear');
    }

    protected function execute(Input $input, Output $output)
    {

        $this->like();
        $output->writeln('Command signclear end');
    }
    public function like(){
        $data = ['rignin_count'=>0,'sign_get_points'=>0];
        Db::name('users')->where('1=1')->update($data);

    }


}