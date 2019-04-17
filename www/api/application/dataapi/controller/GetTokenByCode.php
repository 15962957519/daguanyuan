<?php
namespace app\dataapi\controller;

use app\dataapi\server\UserServer;
use app\api\behavior\Curl;
use app\dataapi\server\Weixin;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Cache;
use think\Controller;
use think\Hook;
use think\Request;
use think\Response;
use think\Exception;
use app\dataapi\controller\JobQeue;
use think\Log;
use think\App;
use app\dataapi\server\MqProducer;
define("TOKEN", "jknsadjknasdjkasjkas88");

class GetTokenByCode extends Controller
{

    public $filecacheobj = null;

    public function __construct()
    {
        $this->filecacheobj =  Cache::store('filetoken');
    }

    public function pgettoken()
    {
        if(config('app_test')===true){
            $url =config('default_domain_api').'/user/getAccess_token_p';
        //判断是否是测试站
            $client = new Client([
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
                Response::create(['errormsg' => $e->getMessage(), 'statue_code' => '4003'], 'json')->header($header)->send();
            }
            Response::create($access_toke_data, 'json')->send();
        }else{
            $token = $this->filecacheobj->get('grant_access_token');
            $access_token =  $this->filecacheobj->get('access_token');
            $get_js_sdk_access_token =  $this->filecacheobj->get('get_js_sdk_access_token');
            Response::create(['grant_access_token' => $token, 'token' => $access_token, 'jskapi' => $get_js_sdk_access_token, 'statue_code' => '2003'], 'json')->send();
        }
    }


    public function getToken(Request $request)
    {
        $code = $request->param('code');
        $state = $request->param('state');
        $url = Config::get('weixin_api.url');
        $appid = Config::get('weixin_api.appid');
        $secret = Config::get('weixin_api.secret');
        $url = str_replace('APPID', $appid, $url);
        $url = str_replace('SECRET', $secret, $url);
        $url = str_replace('CODE', $code, $url);
        $client = new Client([
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
            Response::create(['errormsg' => $e->getMessage(), 'statue_code' => '4003'], 'json')->header($header)->send();
        }

        $userinfoobj = '';
        if (isset($access_toke_data['openid'])) {
            //通过openid 获取用户个人信息
            $uu = new UserServer();
            $userong = $uu->getThirdUserinfo($access_toke_data['openid']);
            if (empty($userong)) {
                $grant_token = $access_toke_data['access_token']??'';
                if (!empty($grant_token)) {
                   $this->filecacheobj->set('grant_access_token', $grant_token, 7000);
                }
                $jsapiobj = new  Weixin();
                //$url = urlencode($url);
                //access_token
                $token = $jsapiobj->getAccessToeknCode($this->filecacheobj);
                try {
                   if ('snsapi_userinfo' == $state) {
                      //$token 需要重新获取
                        $userinfoobj = $jsapiobj->getUserinfosnsapi_userinfo($grant_token, $access_toke_data['openid']);
                   } else {
                        $userinfoobj = $jsapiobj->getUserinfo($token, $access_toke_data['openid']);
                    }

                    $userinfoobj = stripslashes($userinfoobj);
                    $userinfoobj = json_decode($userinfoobj, true);
                    if (isset($userinfoobj['subscribe']) && 0 == $userinfoobj['subscribe']) {
                        //没有关注
                        Response::create(['errormsg' => $userinfoobj, 'statue_code' => '4006'], 'json')->header($header)->send();
                    }
                    if ((is_array($userinfoobj) && !isset($userinfoobj['errmsg'])) || (isset($userinfoobj['subscribe']) && 0 != $userinfoobj['subscribe'])) {
                        $userong = $uu->addThirdUserinfo($access_toke_data['openid'], $request, $userinfoobj, 1);
                        //添加消息列队
                        try {
                            $JobQeueobj = new  MqProducer();
                            $sumtime =time()+2;
                            $jobData = ['type'=>'consumerfirstenterplatorm','ts' => $sumtime, 'bizId' => uniqid(), 'id' => $userong->user_id, 'is_or_member' => false];
                            $JobQeueobj->process($jobData);

                            //会员首次赠送fisrtentergiftfans =0
                            $uu->updatefirstenterfans($userong->user_id,1);

                        } catch (\RuntimeException $e) {
                            App::$debug &&   Log::record('日志信息', 'info' . $e->getMessage());
                        }
                    } else {
                        Response::create(['errormsg' => '', 'statue_code' => '4007'], 'json')->header($header)->send();
                    }
                } catch (\Exception $e) {
                    App::$debug &&   Log::record('日志信息', 'info' . $e->getMessage());
                    Response::create(['errormsg' => $e->getMessage(), 'statue_code' => '4004'], 'json')->header($header)->send();
                }
            }
            $access_toke_data['userong'] = $userong;
            $access_toke_data['user'] = $uu->getUserinfo($userong->user_id);
            $access_toke_data['statue_code'] = '2000';
            Hook::add('appInit', 'app\\dataapi\\behavior\\Jwt');
            Hook::listen('appInit', $access_toke_data);
            //{"subscribe":1,"openid":"oASY5wXqQnE1Q8AWXAQPsc0VYA9Q","nickname":"牧童","sex":1,"language":"zh_CN","city":"普陀","province":"上海","country":"中国","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/uI5pczeERTYiaOhicicADBR2MdoPQy7Nk2ofoxRhcRmHBCrs3RdlLobszakIVJUnSOXb5L949bNGCxMRc4rJOlibcjrgrUc0sugB\/0","subscribe_time":1484811704,"remark":"","groupid":0,"tagid_list":[]}
            Response::create($access_toke_data, 'json')->header($header)->send();
        } else {
            Response::create(['errormsg' => '', 'statue_code' => '4005'], 'json')->header($header)->send();
        }

    }


    public function valid(Request $request)
    {
        $echoStr = $request->param('echostr');

        //valid signature , option
        if ($this->checkSignature($request)) {
            echo $echoStr;
            exit;
        }
    }


    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                echo "Input something...";
            }

        } else {
            echo "";
            exit;
        }
    }

    private function checkSignature($request)
    {
        $signature = $request->param('signature');
        $timestamp = $request->param('timestamp');
        $nonce = $request->param('nonce');

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function makesign(Request $request)
    {
        $url = $request->param('url');
        if (empty($url)) {
            $header = ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept,authorization', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE'];
            Response::create(['code'=>1], 'json')->header($header)->send();
        }
        $jsapiobj = new  Weixin();
        $url = urldecode($url);
        $config = array();
        $config['noncestr'] = $jsapiobj->createNonceStr();
        $config['timestamp'] = (string)$_SERVER['REQUEST_TIME'];
        $config['url'] = trim($url);//  // 注意 URL 一定要动态获取，不能 hardcode.
        $config['appid'] = Config::get('weixin_api.appid');//  // 注意 URL 一定要动态获取，不能 hardcode.
        $jsapijsonobj = $jsapiobj->getSignature();

        if (empty($jsapijsonobj)) {
            $header = ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept,authorization', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE'];
            Response::create(['code'=>2], 'json')->header($header)->send();
        }

        $jsapijsonobj_data = \GuzzleHttp\json_decode($jsapijsonobj);

        if (!isset($jsapijsonobj_data->ticket) || empty($jsapijsonobj_data->ticket)) {
            $header = ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept,authorization', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE'];
            Response::create(['code'=>3], 'json')->header($header)->send();
        }
        // file_put_contents(RUNTIME_PATH.'/TICKETTEST.php',var_export($jsapijsonobj_data,true),FILE_APPEND);

        $tmpArr = array('jsapi_ticket' => $jsapijsonobj_data->ticket, 'timestamp' => $config['timestamp'], 'noncestr' => $config['noncestr'], 'url' => $config['url']);
        // use SORT_STRING rule
        ksort($tmpArr, SORT_STRING);
        $tmpStr = '';
        $nickname = function ($key, $v) use (&$tmpStr) {
            $tmpStr .= '&' . $v . '=' . $key;
        };
        array_walk($tmpArr, $nickname);
        $tmpStr = substr($tmpStr, 1);
        $config['signature'] = sha1($tmpStr);
        $header = ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept,authorization', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE', 'Content-Type' => 'application/x-www-form-urlencoded,multipart/form-data,text/plain'];
        Response::create($config, 'json')->header($header)->send();
    }

    /**
     * [ 获取token ]
     */
    public function get_accessToken(){

        $jsapiobj = new  Weixin();
        $token = $jsapiobj->getAccessToeknCode($this->filecacheobj);
        Response::create(['token'=>$token,'code'=>2000], 'json')->send();

    }

}
