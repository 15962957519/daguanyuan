<?php

namespace app\dataapi\server;

use think\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use Hprose\Client as goalngclient;

class MqProducer
{

    static private $instance;
    private   $client=null;
    //构造函数
    function __construct($config = [])
    {
        $configdata = Config::get('rpc');
        $golangurl = $configdata['golang']['url'];
        $this->client = new \Hprose\Socket\Client($golangurl, false);
        $this->client->setFailswitch(true);
        $this->client->setSimple(true);
        $this->client->setTimeout(2000);
        return ;
    }


    static public function getInstance($config)
    {
        //判断$instance是否是Uni的对象
        //没有则创建
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    //发布消息流程
    public function process(array $senddata): bool
    {
        $startdelivertime = $senddata['ts'];
        $t = time();
        if ($startdelivertime <= $t) {
            $startdelivertime = $t;
        }
        //处理数据 转换为字符类型
        $strbody = \GuzzleHttp\json_encode($senddata);
        //打印配置信息
        $flag = false;
        try {
            $configdata = Config::get('rpc');
            $freetemp = $configdata['golang']['freetemp'];
            $dbname = $configdata['golang']['topicquenuedbname'];
            $dependent_jobs = "";
            //毫秒
            $time = $startdelivertime * 1000;
            //发送时间 毫秒
            $sendtime = time() * 1000;
            $qeuename = $configdata['golang']['queue'];
            $flags = $this->client->generatetaskDelay($freetemp, $dependent_jobs, (string)$time, (string)$sendtime, $dbname, $strbody, $qeuename);
            if ($flags == "200") {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $e->getMessage();
            return false;
        }
        return $flag;
    }
}
