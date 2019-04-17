<?php
namespace app\consolewin;
use app\dataapi\controller\JobQeue;
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
use app\dataapi\model\Goods;
use app\dataapi\server\AutoBidProductServer;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class TestAvatar extends Command
{
    protected function configure()
    {
        $this->setName('avatat')
            ->setDescription('Command avatat');
    }

    protected function execute(Input $input, Output $output)
    {

        $this->flagimageAll();
        $output->writeln('Command avatat end');
    }



    /*
 * 赠送会员粉丝
 *
 */
    public function autoTestAvatar(String $url){
      $Image =new   Image();
       $flag = $Image->imagetest($url);
        return $flag;
    }


    public function flagimage()
    {
        $datauser = Db::name('users')->where('fictitious', 1)->order('rand()')->find();
        echo $datauser['user_id'];
        echo "\r\n";
        $flag = $this->imagetest($datauser['head_pic']);
        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
           return  $this->flagimage();
        }
        return $datauser;

    }

    public function imagetest($url)
    {
        $FastImageSize = new \FastImageSize\FastImageSize();
        $imageSize = $FastImageSize->getImageSize($url);
        return $imageSize;
    }


    //修改正头像里面空白的

    public function modifyimagetest()
    {
        $datauser = Db::name('users')->where('fictitious', 1)->chunk(100, function($users) {
            foreach ($users as $user) {
                 $UserServerobj =new  UserServer();
                $tmp =$UserServerobj->getThirdUserinfoByUserid($user['user_id']);

                 if(!isset($tmp['head_pic']) || $user['head_pic']!=$tmp['head_pic']){
                     $UserServerobj->updateUserinfoOfThird($user['user_id'],['head_pic'=>$user['head_pic']]);
                     //判断user问题
                 }
                echo "\r\n 正在处理 用户id {$user['user_id']},{$user['head_pic']}";
                //
            }
        });
    }

    //修改正头像里面不存在的


    //修改正头像里面空白的

    public function modifyimageOfGoodsCollect()
    {
        $datauser = Db::name('goods_collect')->whereNull('delete_time')->chunk(20, function($users) {
            foreach ($users as $user) {
             $dadad = Db::table('tp_users')->where('user_id',$user['user_id'])->field('head_pic,fictitious')->find();
                if(isset($dadad['fictitious'])&& $dadad['fictitious']==1){
                    if( isset($dadad['head_pic'])){
                        $flag = $this->imagetest($dadad['head_pic']);
                        if(!$flag){
                            echo "\r\n processing user {$user['user_id']}";
                            $collects =GoodsShouc::get($user['collect_id']);
                            $collects->delete();
                        }
                    }else{
                        $collects =GoodsShouc::get($user['collect_id']);
                        $collects->delete();
                    }
                }

            }
        });
    }



 function like_products()
 {
     $nowobj = Carbon::now();
     $like_pff =null;
     $currenttimeunixtime = time();
     $userLogic = new UsersLogic();
     $hot_goods = Db::name('likegoodmessagelist')
         ->where('end_time', '>', $currenttimeunixtime)
         ->where('is_send', 0)
         ->field('goods_id')
         ->chunk(50, function ($goods) use ($like_pff, $currenttimeunixtime, $userLogic) {
             foreach ($goods as $good) {
                 //判断作品状态

                 $map['goods_status'] = ['=', 1];
                 $map['goods_id'] = ['=', $good['goods_id']];
                 $flag = Db::name('goods')->where($map)->whereNull('delete_time')->field('user_id,goods_id')->find();
                 if (!$flag) {
                     continue;
                 }

                 $level = $userLogic->get_infobykey($flag['user_id'], 'level');
                 $nowobj = Carbon::now();
                 $code = cachecatchg($good['goods_id'], 'likefansserver');
                 $data = Db::name('likegoodmessagelist')->where('goods_id', $good['goods_id'])->whereNull('delete_time')->find();
                 if (!empty($data)) {
                     continue;
                 }
                 //通过判断获取不同级别的情况
                 switch ($level) {
                     case 1:
                         $time_interval = 2 * 12 * 3600;
                         $tmplevel1 = $nowobj->addHours(24)->getTimestamp();

                         $a = range(1, mt_rand(5, 7));//5-7个点赞
                         break;
                     case 2:
                         $time_interval = 3 * 12 * 3600;
                         $tmplevel1 = $nowobj->addHours(36)->getTimestamp();

                         $a = range(1, mt_rand(15, 20));
                         break;
                     case 3:
                         $time_interval = 4 * 12 * 3600;
                         $tmplevel1 = $nowobj->addHours(48)->getTimestamp();

                         $a = range(1, mt_rand(30, 40));
                         break;
                     case 4:
                         $time_interval = 6 * 12 * 3600;
                         $tmplevel1 = $nowobj->addHours(60)->getTimestamp();

                         $a = range(1, mt_rand(50, 60));
                         break;
                     case 5:
                         $time_interval = 6 * 12 * 3600;
                         $tmplevel1 = $nowobj->addHours(72)->getTimestamp();

                         $a = range(1, mt_rand(80, 100));
                         break;
                     default:
                         $time_interval = 2 * 12 * 3600;
                         $tmplevel1 = $nowobj->addHours(24)->getTimestamp();
                         $a = range(1, mt_rand(5, 7));//5-7个点赞
                         break;
                 }

                 cachecatchset($good['goods_id'], 'true', 'likefansserver', $tmplevel1 - $currenttimeunixtime);
                 $loopcount = count($a);
                 while ($loopcount > 0) {
                     $mic = mt_rand($currenttimeunixtime, $tmplevel1);
                     $dt = Carbon::createFromTimestamp($mic);
                     if ($dt->hour <= 5) {
                         continue;
                     }
                     $mic = $mic - $currenttimeunixtime;
                     if ($mic < 0) {
                         continue;
                     }
                     $sumtime = $mic + $currenttimeunixtime;
                     //   Timer::add($mic, $like_pff, array($good['goods_id']), false);
                     //插入数据库
                     $data = ['goods_id' => $good['goods_id'], 'start_time' => $sumtime, 'end_time' => $currenttimeunixtime + $time_interval];
                     $id = Db::name('likegoodmessagelist')->insertGetId($data);
                     if ($id > 0) {
                         //添加消息列队
                         try {
                             $JobQeueobj = new  JobQeue();
                             $JobQeueobj->autoLikeGoodsForFanserer($good['goods_id'], $sumtime, $id);

                             echo "喜欢了" . $good['goods_id'] . "\r\n";
                         } catch (\RuntimeException $e) {
                             // Log::record('日志信息', 'info' . $e);
                         }
                     } else {
                         continue;
                     }
                     $loopcount--;
                 }
             }
         },'id');
 }

}