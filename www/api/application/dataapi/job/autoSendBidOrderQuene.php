<?php
namespace app\dataapi\job;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Cache;
use think\Config;
use think\Db;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Hook;
use think\queue\Job;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class autoSendBidOrderQuene
{
    function index()
    {
        return function ($data){
            $goods_id = $data['goods_id'];
            //发送给微信
            //更新数据库
            Db::name('bid_order')->where('id', $data['bid'])->update(['is_remind' => 1]);
            return true;
        };
    }
}
