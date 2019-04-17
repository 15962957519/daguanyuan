<?php
namespace teegon;

use function GuzzleHttp\headers_from_lines;
use Shopex\TeegonClient\TeegonClient as TeegonClient;
use think\Config;

class Teegonservice{

    /**
     * @param $data
     * 天工支付
     */
    public function index($data){
        $client = new TeegonClient(
            $url = Config::get('teegon_api.baseurl'),
            $key = Config::get('teegon_api.key'),
            $secret = Config::get('teegon_api.secret'),
            false,
            $config = ['connect_timeout'=>3.2]
        );
        try{
            $res = $client->post('teegon.payment.charge.pay', $data, [], $config=[]);
            $result = json_decode($res,true);
            if($result['ecode'] == 0 ){
                header('Content-Type:text/html;charset=utf-8');
                echo "<script language='javascript' type='text/javascript'>";
                echo $result['result']['action']['params'];
                echo "</script>";exit;
            }
        }catch(\Exception $e){
            echo $e->getMessage();
            exit;
        }


    }

}

/**
// 运行example前先要起个服务器，比如我们用PHP的自带dev-server:
// php -S 0.0.0.0:8080 example/server-router.php

// 新建对象 填入在Teegon平台上注册的信息 本地测试的话随意填写就行了
// 第四个参数 $socket socket文件地址，如果有则优先选择socke方式 ,$socket = "unix:///tmp/api_provider.sock"
$client = new TeegonClient($url = 'http://api.teegon.com/router', $key = 'xjMdeBd4h', $secret = 'FkJdftb5wgeE4dSNYX8waj4');


// 发起请求
$res = $client->post('shopex.queue.read', array('topic'=>'orders', 'num'=>1,'drop'=>false));
echo $res;
// 返回: pong

**/
