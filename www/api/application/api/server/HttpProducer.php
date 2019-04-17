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
namespace app\api\server;

use think\Config;
use  app\dataapi\server\UtilMq as Util;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
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
    //签名
    private static $signature = "Signature";
    //在MQ控制台创建的Producer ID
    private static $producerid = "ProducerID";
    //阿里云身份验证码
    private static $aks = "AccessKey";
    //配置信息
    private static $configs = null;

    public $topic ='';
    public $pid ='';
    //构造函数
    function __construct($unixstampdata)
    {
        //读取配置信息
        $this::$configs =$unixstampdata;
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
        $t =time();
        if($startdelivertime<=$t){
            $startdelivertime =$t;
        }
        //处理数据 转换为字符类型
       $strbody =  json_encode($senddata);
        //打印配置信息
       //var_dump($this::$configs);
        //获取Topic
            $topic = self::$configs["Topic"];
        //获取保存Topic的URL路径
        $url = self::$configs["URL"];
        //读取阿里云访问码
        $ak = self::$configs["Ak"];
        //读取阿里云密钥
        $sk = self::$configs["Sk"];
        //读取Producer ID
            $pid = self::$configs["ProducerID"];
        //HTTP请求体内容
        $body = $strbody;
        $newline = "\n";
        //构造工具对象
        $util = new Util();
     //   for ($i = 0; $i<1; $i++) {
            //计算时间戳
            $date = time()*1000;
            $startdelivertime = (string)($startdelivertime*1000);
            //POST请求url
            $postUrl = $url."/message/?topic=".$topic."&time=".$date."&startdelivertime=".$startdelivertime."&tag=http&key=http";
            //签名字符串
            $signString = $topic.$newline.$pid.$newline.$this->md5($body).$newline.$date;
            //计算签名
            $sign = $util->calSignature($signString,$sk);
            //初始化网络通信模块
            $ch = curl_init();
            //构造签名标记
            $signFlag = $this::$signature.":".$sign;
            //构造密钥标记
            $akFlag = $this::$aks.":".$ak;
            //标记
            $producerFlag = $this::$producerid.":".$pid;
            //构造HTTP请求头部内容类型标记
            $contentFlag = "Content-Type:text/html;charset=UTF-8";
            //构造HTTP请求头部
            $headers = array(
                $signFlag,
                $akFlag,
                $producerFlag,
                $contentFlag,
            );

            //设置HTTP头部内容
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            //设置HTTP请求类型,此处为POST
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
            //设置HTTP请求的URL
            curl_setopt($ch,CURLOPT_URL,$postUrl);
            //设置HTTP请求的body
            curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            //构造执行环境
            ob_start();
            //开始发送HTTP请求
            curl_exec($ch);
            //获取请求应答消息
            $result = ob_get_contents();
            //清理执行环境
            $errno = curl_errno( $ch );
            $info  = curl_getinfo( $ch );
            ob_end_clean();
            //打印请求应答结果
            //解析为数组
           $res =   json_decode($result);
            //关闭连接
            curl_close($ch);
           if(isset($res->sendStatus) && $res->sendStatus=='SEND_OK'){
                return $res->msgId ;
           }else{
               return false;
           }
         //   var_dump($info);
    //    }
    }
}
