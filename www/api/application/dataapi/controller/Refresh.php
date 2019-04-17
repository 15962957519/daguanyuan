<?php

namespace app\dataapi\controller;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Cache;
use think\Controller;
use think\Hook;
use think\Request;
use think\Response;
use think\Exception;
use app\dataapi\server\Weixin;

class Refresh extends BaseApi
{
    public function settoken()
    {
        //刷新中控服务器
       $cacheobj = Cache::store('filetoken');
        $access_token = (new Weixin())->set_access_token($cacheobj);
        Response::create(['status' => $access_token, 'code' => '1', 'message' => 'sucess'], 'json')->header($this->header)->send();
    }


    public function setjsapi()
    {
        $cacheobj = Cache::store('filetoken');
        $access_token = (new Weixin())->setSignature($cacheobj);
        Response::create(['status' => $access_token, 'code' => '1', 'message' => 'sucess'], 'json')->header($this->header)->send();
    }

}
