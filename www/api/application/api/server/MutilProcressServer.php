<?php

namespace app\api\server;

use app\dataapi\model\FansCollect;
use app\dataapi\model\GoodsShouc;
use app\dataapi\model\ThirdUsers;
use app\dataapi\model\UserBond;
use app\dataapi\model\Goods;
use app\dataapi\model\Users;
use app\dataapi\model\UserVerifty;
use Defuse\Crypto\KeyProtectedByPassword;
use think\Db;
use think\Request;
use think\Config;
use app\dataapi\lib\Image;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class MutilProcressServer implements \Jenner\SimpleFork\Runnable
{

    public  $mode = 1;
    public  $goods_id = 0;
    public  $id = 0;
    public  function  __construct($mode=1,$goods_id=0,$id=0)
    {
        $this->mode= $mode;
        $this->goods_id= $goods_id;
        $this->id= $id;
    }

    /**
     * Entrance
     * @return mixed
     */
    public function run()
    {
        $UserServer = new UserServer();
        $image = new Image();
        // 启动事务
        try {
            $randlike = mt_rand(1, 3);
            do {
                $randlike--;
                $ddd = $image->flagimage();
                $userid = $ddd['user_id'];
                if ($userid > 0 && $this->goods_id > 0) {
                    $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $this->goods_id]);
                }
                echo "like " . $this->goods_id . "\r\n";
                Db::connect('mycat104')->name('likegoodmessagelist')->where('id', $this->id)->delete(true);
            } while ($randlike > 0);
        } catch (\Exception $e) {
            // 回滚事务
        }
    }
}