<?php

namespace app\dataapi\controller;

use think\Controller;
use think\Db;
use think\Config;
use think\Request;
use think\Response;
use app\dataapi\server\Weixin;
use think\Cache;
//生成token
class Token extends BaseApi
{

    public $filecacheobj = null;

    public function __construct()
    {
        $this->filecacheobj =  Cache::store('filetoken');
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        //刷新中控服务器
        $access_token =   (new Weixin())->set_access_token($this->filecacheobj);
        Response::create(['status' => '200', 'code' => '1', 'message' => '','token'=>$access_token], 'json')->header($this->header)->send();
    }



    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function get_js_sdk_access_token()
    {
        //刷新中控服务器
        $access_token = (new Weixin())->setSignature($this->filecacheobj);
        Response::create(['status' => '200', 'code' => '1', 'message' => '','token'=>$access_token], 'json')->header($this->header)->send();
    }
}
