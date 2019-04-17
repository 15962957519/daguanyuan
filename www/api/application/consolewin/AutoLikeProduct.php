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
class AutoLikeProduct extends Command
{
    protected function configure()
    {
        $this->setName('autolike')
            ->setDescription('Command autolike');
    }

    protected function execute(Input $input, Output $output)
    {

        $this->like();
        $output->writeln('Command autolike end');
    }



    public function like(){
        $hot_goods = Db::name('Goods')
            ->whereNull('delete_time')
            ->where('goods_status',1)
            ->where('is_toplatform',1)
            ->field('user_id,goods_id')
            ->order('upload_time DESC')
            ->chunk(10,function($goods){
                foreach ($goods as $good){
                    $randlike= mt_rand(3,10);
                    $i=0;
                    do{
                    $i++;
                        $ddd=(new Image())->flagimage();
                        echo "\r\nis like product {$good['goods_id'] }  {$ddd['user_id']}  ";

                        $userid =$ddd['user_id'];
                        $UserServer = new UserServer();
                        $goods_id= $good['goods_id'];
                        if($userid>0 && $goods_id>0){
                            $flag = $UserServer->userUpdateLike($userid,['goods_id'=>$goods_id]);
                        }
                    }while($i<$randlike);
                }
            });
    }
}