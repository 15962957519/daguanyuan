<?php
namespace app\dataapi\server;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Cache;
use think\Config;
use app\Home\Logic\UsersLogic;
use Carbon\Carbon;
use app\dataapi\server\ProductServer;
use app\dataapi\server\Jssdk;
use think\Db;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Weixin
{
    public function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    function  set_access_token($cacheobj){
        static $n=0;
     //   $access_token = $cacheobj->get('access_token');
        $access_token =null;
        Cache::rm('access_token');
        if (empty($access_token) ||$access_token ==false) {
            $access_token = $this->get_access_token();
            // 切换到file操作
            $access_token = json_decode($access_token,true);
            if(is_array($access_token)){
                $access_token['add_time'] = time();
            }
            $access_token = json_encode($access_token);

           $flag = $cacheobj->set('access_token', $access_token, 7000);
        }
        $data =json_decode($access_token,true);
        if($n>=10){
            return '';
        }
        if(!isset($data['access_token']) || $data['access_token']==''){
            $n++;
            return $this->set_access_token($cacheobj);
        }
        return $data['access_token'];
    }


//js-sdk
    function get_access_token()
    {
        static $n=0;
        $configdata = Config::get('configweixin');
        $url= $configdata['weixin_api']['get_access_url'];
        $appid = $configdata['weixin_api']['appid'];
        $secret = $configdata['weixin_api']['secret'];
        $url = str_replace('APPID', $appid, $url);
        $url = str_replace('SECRET', $secret, $url);
        $client = new Client([
            'timeout' => 8.0,
        ]);
        $request = new GuRequest('get', $url);
        $response = $client->send($request, ['timeout' => 30, 'verify' => false, 'headers' => [
            'Accept' => 'application/json',
        ]]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        $n++;
        if($n>=4){
            return '';
        }
        //判断是否正确
        if(stripos($remainingBytes,'access_token')!==false){
            return $remainingBytes;
        }else{
           return  $this->get_access_token();
        }
    }


//js-sdk
    function get_js_sdk_access_token($ACCESS_TOKEN = 'GooqFPSO3hHpPKIXfZJvsgzlVdNyQwHa5jOiAFOCIARbjgdPPatTRjegBMuoMTKoNhWXI8_2qxao72_CIY2oA9YwJHXHZGBMWPErLCKJFQwSPIgABABYR')
    {
        $configdata = Config::get('configweixin');
        $url= $configdata['weixin_api']['get_jsticket_access_url'];
        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $client = new Client([
            // Base URI is used with relative requests
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $request = new GuRequest('get', $url);
        $response = $client->send($request, ['timeout' => 20, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();

//        if($this->is_not_json($remainingBytes)){
//            $access_toke_data =    json_decode($remainingBytes);
//            return  $access_toke_data;
//        }
        return $remainingBytes;
    }


    public function setSignature($cacheobj)
    {
        static $n=0;
        $weixinconfig = '';
        $n++;
       // $weixinconfig = $cacheobj->get('get_js_sdk_access_token');
        Cache::rm('get_js_sdk_access_token');
        $weixinconfig =null;
        if (empty($weixinconfig) || !$weixinconfig) {
            $access_token = $this->getAccessToeknCode($cacheobj);
            if(empty($access_token)){

                $access_token =  $this->set_access_token($cacheobj);
            }
            if (!empty($access_token)) {
                $weixinconfig = $get_js_sdk_access_token = $this->get_js_sdk_access_token($access_token);
            }else{
                sleep(1);
                if($n>5){
                    return null;
                }
                return $this->setSignature($cacheobj);
            }
            // 切换到file操作
            $cacheobj->set('get_js_sdk_access_token', $get_js_sdk_access_token, 7000);
        }

        //检查是否过期
        $data =json_decode($weixinconfig,true);
        if(!isset($data['errcode']) || $data['errcode']!=0){
            $cacheobj->set('access_token', null);
            $cacheobj->set('get_js_sdk_access_token', null);
            return ;
            $this->set_access_token($cacheobj);
            sleep(1);
            if($n>5){
                return null;
            }
            return $this->setSignature($cacheobj);
        }

        //加密签名
        return $weixinconfig;
    }


    public function getSignature()
    {
        $cacheobj = Cache::store('filetoken');
        if(config('app_test')===true){
            $weixinconfig =   $this->getconfig('jskapi',$cacheobj);
            return $weixinconfig;
        }else{
            $weixinconfig = $cacheobj->get('get_js_sdk_access_token');
            //加密签名
            return $weixinconfig;
        }
    }

//js-sdk
    function getUserinfo($ACCESS_TOKEN, $openid)
    {
        $configdata = Config::get('configweixin');
        $url= $configdata['weixin_api']['get_userinfo'];
        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $url = str_replace('OPENID', $openid, $url);
        $client = new Client();
        $request = new GuRequest('GET', $url);
        $response = $client->send($request, ['timeout' => 2, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        return $remainingBytes;
    }

//js-sdk
    function getUserinfosnsapi_userinfo($ACCESS_TOKEN, $openid)
    {

        $url = Config::get('weixin_api.get_userinfo_userinfo');

        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $url = str_replace('OPENID', $openid, $url);
        $client = new Client([
            // Base URI is used with relative requests
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $request = new GuRequest('get', $url);
        $response = $client->send($request, ['timeout' => 2, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        return $remainingBytes;
    }




    function getAccessToeknCode($cacheobj){
        if(config('app_test')===true){
            $weixinconfig =   $this->getconfig('token',$cacheobj);
            $access_toke_data = json_decode($weixinconfig, true);
            return $access_toke_data['access_token'];
        }else{
            $access_token =$cacheobj->get('access_token');
            if (empty($access_token) ||$access_token ==false) {
                return null;
            }
            $data =json_decode($access_token,true);
            if(!isset($data['access_token']) || $data['access_token']==''){
                return null;
            }
            return $data['access_token'];
        }
    }




    public function downWeixinImage($media_data)
    {

        //下载微信图片接口
        $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
        $cacheobj = Cache::store('filetoken');
        $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($cacheobj);
        $url = str_replace('MEDIA_ID', $media_data, $url);
        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => '',
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $GuRequest = new GuRequest('get', $url);
        $response = $client->send($GuRequest, ['timeout' => 20, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        return $remainingBytes;
    }

    public function downImage($media_id)
    {
        $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
        $cacheobj = Cache::store('filetoken');
        $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($cacheobj);
        $url = str_replace('MEDIA_ID', $media_id, $url);
        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $img = file_get_contents($url);

        $objDateTime = new \DateTime();
        $currenttime = date('Y-m-d-H-i');
        $pre_ = Config::get('aliyun.gwpre_') . '/';
        $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';

        //获取图片上传到阿里云
        //先上传图片
        $tmp = $pre_name . $currenttime . '_img_' . uniqid();
        $flag = (new AliyunOss())->upload(Config::get('aliyun.bucket'), $tmp, $img);
        return $tmp;
    }


    //群发微信消息
    public function realSendWeixinSMS(string $openid,string $content){
        try{
            $jssdkobj = new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));
            //http://w.tianbaoweipai.com/mymain?goods_id=72743&type=2
            //发送给需要被提醒的人
            $jssdkobj->push_msg($openid,$content);
            return ['status'=>true,'msg'=>'发送成功'];
        }catch(\Exception $e){
            return ['status'=>false,'msg'=>'信息送失败'];
        }

        return ['status'=>true,'msg'=>''];
    }


    public  function getconfig($name,$cacheobj){
            if(config('app_test')===true){
                $url =config('default_domain_api').'/user/getAccess_token_p';
                //判断是否是测试站
                $client = new Client([
                    // Base URI is used with relative requests
                    // You can set any number of default request options.
                    'timeout' => 2.0,
                ]);
                $header = ['Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept', 'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE'];
                try {
                    $requestobj = new GuRequest('get', $url);
                    $response = $client->send($requestobj, ['timeout' => 2, 'verify' => false]);
                    $body = $response->getBody();
                    $remainingBytes = $body->getContents();
                    $access_toke_data = json_decode($remainingBytes, true);
                } catch (\Exception $e) {
                     return '';
                }
                return $access_toke_data[$name];
            }else{
                $jsapiobj = new  Weixin();
                $cacheobj = Cache::store('filetoken');
                $token = $cacheobj->get('grant_access_token');
                $token_a = $jsapiobj->getAccessToeknCode($cacheobj);
                $get_js_sdk_access_token = $cacheobj->get('get_js_sdk_access_token');
                return $token_a;
              //  Response::create(['grant_access_token' => $token, 'token' => $token_a, 'jskapi' => $get_js_sdk_access_token, 'statue_code' => '2003'], 'json')->send();
            }
        }
}