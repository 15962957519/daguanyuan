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
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class ModfiyProductTime extends Command
{
    protected function configure()
    {
        $this->setName('producttime')
            ->setDescription('Command producttime');
    }

    protected function execute(Input $input, Output $output)
    {

        $this->product();
        $output->writeln('Command producttime end');
    }

    public function product(){

        $dt = Carbon::now();
        $dt->addHours(30);
        $unixtime=  $dt->getTimestamp();

        $dt->addHours(100);
        $unixtimeeee=  $dt->getTimestamp();
        $hot_goods = Db::name('Goods')
            ->whereNull('delete_time')
            ->where('endTime','>',$unixtimeeee)
            ->field('*')
            ->chunk(5,
                function ($vulue) use($unixtime)  {
                    Db::startTrans();
                    try{
                        foreach ($vulue as $v) {
                            echo $v['goods_id'];
                            echo "\r\n";
                            Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['endTime' => ['exp',$unixtime]]);
                        }
                        Db::commit();
                        return true;
                    } catch (\RuntimeException $e) {
                        // 回滚事务
                        Db::rollback();
                        return false;
                    }


                }
            );
    }
}