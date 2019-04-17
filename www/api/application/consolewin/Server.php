#!/usr/bin/env php
<?php
namespace  app\consolewin;
use Workerman\Worker;
use Workerman\Lib\Timer;
use app\dataapi\server\AutoBidProductServer;
define('APP_PATH', __DIR__ . '/../application/');
// ThinkPHP 引导文件

define('BIND_MODULE','consolewin/server');
// 加载基础文件
require __DIR__ . '/../../thinkphp/start.php';

class Server{

static function index(){


    $autobidproductserver = new AutoBidProductServer();

    var_dump($autobidproductserver);

}

//
//$task = new Worker();
//// 开启多少个进程运行定时任务，注意多进程并发问题
//$task->count = 1;
//$task->onWorkerStart = function ($task) {
//// 每2.5秒执行一次
//    $time_interval = 2.5;
//    Timer::add($time_interval, function () {
//        echo "task run\n";
//    });
//};
//
//
//$task->onWorkerStart = function($task)
//{
//    // 10秒后执行
//    $autobidproductserver = new AutoBidProductServer();
//    $to = 'workerman@workerman.net';
//    $content = 'hello workerman';
//    Timer::add(10, array($autobidproductserver, 'getIndexProductlists'), array(1), false);
//    echo "AutoBidProductServer task run\n";
//};
//
//
//
//
//
//
//
//
//
//// 运行worker
//Worker::runAll();












}



