#!/bin/env php
<?php

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */

class Test{

 public  function  index(){

return  666;

 }

}

$http = new swoole_http_server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) {

    $a =new Test();
   $ttt = $a->index();
    $response->end($ttt);

});

$http->start();

