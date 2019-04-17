<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;

use  weixinpayment\JsApiPayTwo;
use  weixinpayment\lib\WxPayNotify;
use  weixinpayment\lib\WxPayOrderQuery;
use weixinpayment\lib\WxPayApii;
use  weixinpayment\JsApiPayOrderFinal;
use app\dataapi\server\UserServer;


class payNotify    extends  controller
{

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        $dd= file_get_contents("php://input") ;
        file_put_contents(RUNTIME_PATH.'WEIXINLOG.PHP',var_export($dd,true),FILE_APPEND|LOCK_EX);


        $dd = new  JsApiPayTwo();
        $dd->Handle(false);
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function isfinalorderpayment()
    {
        $dd= file_get_contents("php://input") ;
        file_put_contents(RUNTIME_PATH.'WEIXINLOG.PHP',var_export($dd,true),FILE_APPEND|LOCK_EX);


        $dd = new  JsApiPayOrderFinal();
        $dd->Handle(false);
    }

}
