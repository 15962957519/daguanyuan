<?php
namespace app\index\controller;

use think\Controller;
use think\Db;


class weixinIndex extends Controller
{


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        $hot_goods = Db::name('goods')->where('is_hot', 1)->where('is_on_sale', 1)->order('goods_id DESC')->limit(5)->cache(true, TPSHOP_CACHE_TIME)->select();

        return ['data' => ['hot_goods' => $hot_goods], 'code' => 1, 'message' => '获取成功'];
    }
}
