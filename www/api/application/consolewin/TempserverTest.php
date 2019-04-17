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
use app\dataapi\server\BidOrderServer;
use app\dataapi\controller\JobQeue;
use app\dataapi\model\Goods;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class TempserverTest extends Command
{
    protected function configure()
    {
        $this->setName('TempserverTest')
            ->setDescription('Command Test');
    }

    protected function execute(Input $input, Output $output)
    {
        $pic = $this->modifytodaynogoods();
        $output->writeln('autoModtifyUserLevel end');
    }

//补录当天没有执行的产品
    function modifytodaynogoods()
    {
        $gd = new Goods();
        $JobQeueobj = new  JobQeue();
        $hot_users = Db::table('tp_goods')
            ->where('upload_time', '>', 1505444400)
            ->where('endTime', '>', time())
            ->where('is_heler_likeand', 0)
            ->field('user_id,goods_id,is_upload,is_heler_likeand,endTime')
            ->chunk(10,
                function ($vulues) use ($JobQeueobj, $gd) {
                    $ctime = time();
                    Db::transaction(function () use ($vulues, $JobQeueobj, $gd, $ctime) {
                        foreach ($vulues as $value) {
                            //新添加的增加粉丝
                            echo "" . $value['goods_id'] . "\r\n";
                            if ($value['user_id'] != 4164 && $value['user_id'] != 4145 && $value['is_upload'] == 1) {
                                $JobQeueobj->autoForFansererVhost($value['user_id'], $ctime);
                            }
                            $JobQeueobj->autoLikeGoods($value['user_id'], $value['goods_id'], $value['endTime']);
                            Db::name('goods')->where('goods_id', $value['goods_id'])->update(['is_heler_likeand'=>1]);
                            echo "" . $value['goods_id'] . " is ending\r\n";
                        }
                    });
                }, 'goods_id');
    }



//修改用户的level

    /*
 * 赠送会员粉丝
 *
 */
    public function autoModtifyUserLevel()
    {

        //获取用户等级
        $db1 =  Db::connect([
            // 数据库类型
            'type'        => 'mysql',
            // 服务器地址
            'hostname'    => '116.62.41.143',
            // 数据库名
            'database'    => 'tpshop',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => '*y*us!Ci:41HH',
            // 数据库连接端口
            'hostport'    => '3306',
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => 'tp_',
        ]);
        $userpic = [];
        $hot_users = Db::table('tp_users')
            ->where('fictitious', 0)
            ->where('level', '>', 1)
            ->whereNull('delete_time')
            ->field('is_give_freefans,user_id,fictitious,level')
            ->chunk(50,
                function ($vulue) use (&$userpic,$db1) {
                    foreach ($vulue as $v) {
                        if (!$v['user_id']) {
                            continue;
                        }
                         $tempdata =   $db1->table('tp_users')
                                ->where('user_id', $v['user_id'])
                                ->field('user_id,level')
                                ->find();

                        if(!isset($tempdata['level']) || !isset($tempdata['user_id'])){
                            continue;
                        }
                            if ($v['user_id'] === $tempdata['user_id'] && $v['level'] !=$tempdata['level'] ) {
                                echo "{$v['user_id']}\r\n";
                                Db::table('tp_users')
                                    ->where('user_id', $v['user_id'])
                                    ->update([
                                        'level' =>$tempdata['level']
                                    ]);
                            }

                    }
                }
            );
        return $userpic;
    }
}
