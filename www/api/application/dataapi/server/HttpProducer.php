<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 * 参考地址 http://www.cnblogs.com/txw1958/p/weixin-js-sharetimeline.html
 * http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html  微信JS-SDK说明文档
 */
namespace app\dataapi\server;

use think\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use Hprose\Client as goalngclient;

/**
 * 阿里云消息发送
 * Class CatsLogic
 * @package Home\Logic
 */
//包含工具类
/*
 * 消息发布者者
 */

class HttpProducer
{

    //构造函数
    function __construct()
    {

    }

    //计算md5
    private function md5($str)
    {
        return md5($str);
    }


    //发布消息流程
    public function process(array $senddata)
    {
        $startdelivertime = $senddata['ts'];
        $t = time();
        if ($startdelivertime <= $t) {
            $startdelivertime = $t;
        }
        //处理数据 转换为字符类型
        $strbody = \GuzzleHttp\json_encode($senddata);
        //打印配置信息
        $flag =false;
        try{
            $configdata = Config::get('rpc');
            $golangurl = $configdata['golang']['url'];

            $freetemp = $configdata['golang']['freetemp'];
            $dbname = $configdata['golang']['topicquenuedbname'];
            $dependent_jobs= "";

            $client = goalngclient::create($golangurl, false);
            //毫秒
            $time = $startdelivertime * 1000;
            //发送时间 毫秒
            $sendtime = time() * 1000;
            $tcpurl = $configdata['golang']['middlerserver'];
            $qeuename =  $configdata['golang']['queue'];
            $flag = $client->generatetaskDelay($freetemp,$dependent_jobs,(string)$time , (string)$sendtime,$dbname,$strbody, $qeuename);
            //$flag = $client->generateTask($strbody, $time, $sendtime, $qeuename, $tcpurl);
        }catch(\Exception $e){
            return  false;
        }
        return $flag;
    }
}
