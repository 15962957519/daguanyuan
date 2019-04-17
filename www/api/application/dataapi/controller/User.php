<?php
namespace app\dataapi\controller;
use Carbon\Carbon;
use app\dataapi\server\GoodCollectServer;
use app\dataapi\server\ProductServer;
use app\dataapi\server\UserServer;
use app\Home\Logic\UsersLogic;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Controller;
use think\Hook;
use think\Request;
use think\Response;
use app\dataapi\server\Weixin;
use BaconQrCode\Renderer\Color\Rgb;
use app\dataapi\model\Goods;
use app\dataapi\server\BidOrderServer;
use app\dataapi\model\BidOrder;
use app\dataapi\server\AliyunOss;
use think\Db;
use think\Cache;
define("TOKEN", "jknsadjknasdjkasjkas88");

class User extends Controller
{
    private $user_data;
    private $header;

    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        $this->header = ['Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept', 'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST,DELETE,OPTIONS'];
    }

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        $data = ['name' => 'thinkphp', 'url' => 'thinkphp.cn'];
        Response::create(['data' => $data, 'code' => '2000', 'message' => '用户信息获取成功'], 'json')->header($this->header)->send();
    }
    /*
     * 个人信息
     */
    public function userinfo()
    {
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($userid); // 获取用户信息
        $user_info = $user_info['result'];
        Response::create(['data' => $user_info, 'code' => '2000', 'message' => 'token解析失败'], 'json')->header($this->header)->send();
    }

    /*
      * 个人信息
      */
    public function userinfoall(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_infoAll($userid); // 获取用户信息
        $user_info = $user_info['result'];
        Response::create(['data' => $user_info, 'code' => '2000', 'message' => '用户信息获取成功'], 'json')->header($this->header)->send();
    }


    public function do_login()
    {
        $username = I('post.username');
        $password = I('post.password');
        $username = trim($username);
        $password = trim($password);
        $logic = new UsersLogic();
        $res = $logic->login($username, $password);
        if ($res['status'] == 1) {
            $res['url'] = urldecode(I('post.referurl'));
            session('user', $res['result']);
            setcookie('user_id', $res['result']['user_id'], null, '/');
            setcookie('is_distribut', $res['result']['is_distribut'], null, '/');
            $nickname = empty($res['result']['nickname']) ? $username : $res['result']['nickname'];
            setcookie('uname', $nickname, null, '/');
            setcookie('cn', 0, time() - 3600, '/');
            $cartLogic = new \Home\Logic\CartLogic();
            $cartLogic->login_cart_handle($this->session_id, $res['result']['user_id']);  //用户登录后 需要对购物车 一些操作
        }
        exit(json_encode($res));
    }

    /**
     *  注册
     */
    public function reg()
    {
        if ($this->user_id > 0) header("Location: " . U('Mobile/User/index'));
        if (IS_POST) {
            $logic = new UsersLogic();
            //验证码检验
            //$this->verifyHandle('user_reg');
            $username = I('post.username', '');
            $password = I('post.password', '');
            $password2 = I('post.password2', '');
            //是否开启注册验证码机制

            if (check_mobile($username) && tpCache('sms.regis_sms_enable')) {
                $code = I('post.mobile_code', '');

                if (!$code)
                    $this->error('请输入验证码');
                $check_code = $logic->sms_code_verify($username, $code, $this->session_id);
                if ($check_code['status'] != 1)
                    $this->error($check_code['msg']);

            }

            $data = $logic->reg($username, $password, $password2);
            if ($data['status'] != 1)
                $this->error($data['msg']);
            session('user', $data['result']);
            setcookie('user_id', $data['result']['user_id'], null, '/');
            setcookie('is_distribut', $data['result']['is_distribut'], null, '/');
            $cartLogic = new \Home\Logic\CartLogic();
            $cartLogic->login_cart_handle($this->session_id, $data['result']['user_id']);  //用户登录后 需要对购物车 一些操作
            $this->success($data['msg'], U('Mobile/User/index'));
            exit;
        }
        $this->assign('regis_sms_enable', tpCache('sms.regis_sms_enable')); // 注册启用短信：
        $this->assign('sms_time_out', tpCache('sms.sms_time_out')); // 手机短信超时时间
        $this->display();
    }


    function is_not_json($str)
    {
        return is_null(json_decode($str));
    }

//js-sdk
    function get_access_token()
    {

        $url = Config::get('weixin_api.get_access_url');
        $appid = Config::get('weixin_api.appid');
        $secret = Config::get('weixin_api.secret');
        $url = str_replace('APPID', $appid, $url);
        $url = str_replace('SECRET', $secret, $url);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://httpbin.org/head',
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $request = new GuRequest('get', $url);
        $response = $client->send($request, ['timeout' => 2, 'verify' => false, 'headers' => [
            'Accept' => 'application/json',
        ]]);
        $body = $response->getBody();

        $remainingBytes = $body->getContents();
        return $remainingBytes;
//        if($this->is_not_json($remainingBytes)){
//            $access_toke_data =    json_decode($remainingBytes);
//            return  $access_toke_data;
//        }

    }


//js-sdk
    function get_js_sdk_access_token($ACCESS_TOKEN = 'GooqFPSO3hHpPKIXfZJvsgzlVdNyQwHa5jOiAFOCIARbjgdPPatTRjegBMuoMTKoNhWXI8_2qxao72_CIY2oA9YwJHXHZGBMWPErLCKJFQwSPIgABABYR')
    {

        $url = Config::get('weixin_api.get_jsticket_access_url');

        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://httpbin.org/head',
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $request = new GuRequest('get', $url);
        $response = $client->send($request, ['timeout' => 2, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();

//        if($this->is_not_json($remainingBytes)){
//            $access_toke_data =    json_decode($remainingBytes);
//            return  $access_toke_data;
//        }
        return $remainingBytes;
    }


    /*
     *
     * 用户签名提交
     */
    public function msignature(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();

        if ($userLogic->updateUserSinature($request->except('token'), $userid)) {
            Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    /*
 *
 * 用户签名提交
 */
    public function weixinNumber(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();

        if ($userLogic->thirdUpdateUserSinature($request->except('token'), $userid)) {
            Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    /*
    * 用户签名提交
    */
    public function userVerifty(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();

        $flag = $UserServer->userVerifty($userid, $request->except('token'));

        if ($flag) {
            Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    /*
    * 用户签名提交
    */
    public function userGetVerfityInfo(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();

        $flag = $UserServer->userGetVerfityInfo($userid, $request->except('token'));

        if (!empty($flag)) {
            Response::create(['data' => $flag, 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data' => [], 'code' => '2000', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    /*
* 用户关注作品 喜欢作品
*/
    public function userLikeProduct(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();
        $goods_id = (int)$request->param('goods_id');
        if ($userid > 0 && $goods_id > 0) {
            $flag = $UserServer->userUpdateLike($userid, $request->except('token'), 1);
            if ($flag === true) {
                $data = $UserServer->getUserUpdateLike($request->param('goods_id'), 20);
                $ProductServerobj = new ProductServer();
                $clickcount = $ProductServerobj->getOne($goods_id);
                $GoodCollectServerobj = new  GoodCollectServer();
                $likecount = $GoodCollectServerobj->getOne($goods_id);
                Response::create(['data' => $data, 'click_count' => current($clickcount), 'likecount' => $likecount, 'code' => '2000', 'message' => '喜欢' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            }
            if ($flag === false) {
                Response::create(['data' => ['data' => 'eee'], 'code' => '4000', 'message' => '已经喜欢'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    /*
* 用户关注作品 喜欢作品
*/
    public function userFoucs(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();
        $user_id = (int)$request->param('u_id');
        if ($userid > 0 && $user_id > 0) {
            $flag = $UserServer->userFoucs($userid, $request->except('token'));
            if ($flag === true) {
                Response::create(['data' => ['data' => 'eee'], 'u_id' => $user_id, 'code' => '2000', 'message' => '喜欢' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['data' => ['data' => 'eee'], 'code' => '4000', 'message' => '已经喜欢'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    /*
* 用户吸收粉丝
*/
    public function userfoucsfansan(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();
        $user_id = (int)$request->param('u_id');
        if ($userid > 0 && $user_id > 0) {
            $today = Carbon::today();
            $ssskey = $today->toDateTimeString();
            $tempproductrandone = Cache::store('file')->get(md5('productrandone' . $userid . $ssskey));
            if($tempproductrandone<=0){
                Response::create(['data' => ['data' => 'eee'], 'code' => '4000', 'message' => '粉丝次数超过限制 ，请隔天继续'], 'json')->header($this->header)->send();
            }
            $flag = $UserServer->userFoucsFansan($userid, $request->except('token'));
            if ($flag === true) {

                //减少
                //剩余次数
                $today = Carbon::today();
                $ssskey = $today->toDateTimeString();


               cachecatchsdecuser('productrandone' . $userid . $ssskey, $tempproductrandone, 86410);


                Response::create(['data' => ['data' => 'eee'], 'u_id' => $user_id, 'code' => '2000', 'message' => '粉丝' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['data' => ['data' => 'eee'], 'code' => '4000', 'message' => '粉丝失败'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    /*
* 用户吸收粉丝 增加限制功能
*/
    public function userfoucsfansanlimit(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();
        $user_id = (int)$request->param('u_id');
        if ($userid > 0 && $user_id > 0) {
            $flag = $UserServer->userFoucsFansan($userid, $request->except('token'));
            if ($flag === true) {
                Response::create(['data' => ['data' => 'eee'], 'u_id' => $user_id, 'code' => '2000', 'message' => '粉丝' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['data' => ['data' => 'eee'], 'code' => '4000', 'message' => '粉丝失败'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    /*
        * 用户取消关注
        */
    public function unuserFoucs(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();
        $user_id = (int)$request->param('u_id');

        if ($userid > 0 && $user_id > 0) {
            $flag = $UserServer->unuserFoucs($userid, $request->except('token'));
            if ($flag === true) {
                Response::create(['data' => ['data' => 'eee'], 'u_id' => $user_id, 'code' => '2000', 'message' => '取消关注' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['data' => ['data' => 'eee'], 'code' => '4000', 'message' => '取消关注失败'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '更新' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    /*
* 用户关注作品 喜欢作品
*/
    public function bondCheck(Request $request)
    {

        $userid = $this->user_data['user_id'];
        $UserServer = new UserServer();
        $goods_id = (int)$request->param('good_id');
        //good_id 检查用户id 情况

        if ($goods_id > 0 && $userid > 0) {
            $goods = new   Goods();
            $datagood = $goods->where('goods_id', $goods_id)->where('user_id', $userid)->field('user_id')->find();
            if (!empty($datagood)) {
                Response::create(['code' => '5001', 'message' => '用户不能给自己出价' . $request->param('actionname')], 'json')->header($this->header)->send();
            }
        }

        if ($userid > 0) {
            $flag = $UserServer->bondCheck($userid);
            if ($flag === true) {
                Response::create(['data' => ['data' => 'eee'], 'code' => '2000', 'message' => '保证金已经支付' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['data' => ['data' => 'eee'], 'code' => '2002', 'message' => '没有支付保证经'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '没有支付保证经' . $request->param('actionname')], 'json')->header($this->header)->send();
    }


    /*
* 用户关注作品 喜欢作品
*/
    public function singlebondCheck(Request $request)
    {
        $userid = $this->user_data['user_id'];
        if ($userid > 0) {
            $UserServer = new UserServer();
            $flag = $UserServer->bondCheck($userid);
            if ($flag === true) {
                Response::create(['data' => ['data' => 'eee'], 'code' => '2000', 'message' => '保证金已经支付' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['data' => ['data' => 'eee'], 'code' => '2002', 'message' => '没有支付保证经'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4001', 'message' => '没有支付保证经' . $request->param('actionname')], 'json')->header($this->header)->send();
    }


    /*
      *获取粉丝
      */
    public function userinfoFocusAll(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $is_fans = $request->param('is_fans');
        $page = $request->param('page')??1;
        if ($is_fans == '1') {
            $is_fans = true;
        } else {
            $is_fans = false;
        }

        $userLogic = new UsersLogic();
        $user_info = $userLogic->getFancusAll($userid, $is_fans, $page, 10); // 获取用户信息
        foreach ($user_info as &$v) {
            $v['timelevel'] = ProductServer::memberLevel($_SERVER['REQUEST_TIME'], $v['reg_time']);
        }
        if (!empty($user_info)) {
            Response::create(['data' => $user_info, 'code' => '2000', 'message' => '获取粉丝成功'], 'json')->header($this->header)->send();
        } else {

            Response::create(['data' => [], 'code' => '4000', 'message' => '获取粉丝失败'], 'json')->header($this->header)->send();
        }

    }


    //获取用户二维码推广

    public function qrcode(Request $request)
    {
        ob_end_clean();
        header("Content-type: image/jpg");
        $userid = $this->user_data['user_id'];
        $user_id = $request->param('user_id');
        if($user_id>0){
            $qrcodeuid =$user_id;
        }else{
            $qrcodeuid = $userid;
        }
        $client = new Client();
        //创建二维码ticket
//        $jsapiobj = new  Weixin();
//        $token = $jsapiobj->getAccessToeknCode();
//        $url = Config::get('weixin_api.qrcodeurl');
//        $url = str_replace('TOKEN', $token, $url);
//        //  $url = urlencode($url);
//        $stream = "{\"expire_seconds\": 604800, \"action_name\": \"QR_SCENE\", \"action_info\": {\"scene\": {\"scene_id\": 123}}}";
//        $stream = [
//            'expire_seconds' => 604800,
//            'action_name' => 'QR_SCENE',
//            'action_info' => ['scene' => ['scene_id' => 123]]
//        ];
//        $response = $client->request('POST', $url, ['body' => json_encode($stream)]);
//        $body = $response->getBody();
//        $remainingBytes = $body->getContents();
        //生成二维码现在到图片
//        $renderer = new \BaconQrCode\Renderer\Image\Png();
//
//        $color = new Rgb(255, 255, 0);
//        $renderer->setBackgroundColor($color);
//        $color = new Rgb(255, 0, 255);
//        $renderer->setForegroundColor($color);
//        $renderer->setHeight(630);
//        $renderer->setWidth(630);
//        $renderer->setMargin(3);
//        $writer = new \BaconQrCode\Writer($renderer);
//        $url = Config::get('domain');
//        $ddd = $writer->writeString($url . "mymain/{$qrcodeuid}/1");
//        $ddd = base64_encode($ddd);

        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($qrcodeuid);

        //先获取头像
        $url = $abcdata['result']['head_pic'];
        $response = $client->request('GET', $url);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        $resourceoba = imagecreatefromstring($remainingBytes);

        //源图像
        $background = imagecreatefromjpeg(ROOT_PATH.'public/static/img/spread/saoyisao.png');

        imagecopyresized($background, $resourceoba, 130, 103, 0, 0, 40, 40, imagesx($resourceoba), imagesy($resourceoba));

        imagejpeg($background);
        imagedestroy($background);
       imagedestroy($resourceoba);
    }

    function get_extension($file)
    {
        return substr(strrchr($file, '.'), 1);
    }

    public function getUserImage(Request $request)
    {
        ob_end_clean();
        header("Content-type: image/jpeg");
        $userid = $this->user_data['user_id'];
        $qrcodeuid = $request->param('userid');
        $client = new Client();
        //创建二维码ticket
//        $jsapiobj = new  Weixin();
//        $token = $jsapiobj->getAccessToeknCode();
//        $url = Config::get('weixin_api.qrcodeurl');
//        $url = str_replace('TOKEN', $token, $url);
//        //  $url = urlencode($url);
//        $client = new Client();
//        $stream = "{\"expire_seconds\": 604800, \"action_name\": \"QR_SCENE\", \"action_info\": {\"scene\": {\"scene_id\": 123}}}";
//        $stream = [
//            'expire_seconds' => 604800,
//            'action_name' => 'QR_SCENE',
//            'action_info' => ['scene' => ['scene_id' => 123]]
//        ];
//        $response = $client->request('POST', $url, ['body' => json_encode($stream)]);
//        $body = $response->getBody();
//        $remainingBytes = $body->getContents();
        //生成二维码现在到图片
        $renderer = new \BaconQrCode\Renderer\Image\Png();
        $color = new Rgb(255, 255, 0);
        $renderer->setBackgroundColor($color);
        $color = new Rgb(255, 0, 255);
        $renderer->setForegroundColor($color);
        $renderer->setHeight(630);
        $renderer->setWidth(630);
        $renderer->setMargin(3);
        $writer = new \BaconQrCode\Writer($renderer);
        $url = Config::get('domain');
        $ddd = $writer->writeString($url . "mymain/{$qrcodeuid}/1");

        //字符串
        //  $ddd = base64_encode($ddd);
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($qrcodeuid);
        $bg_width = 400;
        $bg_height = 500;
//创建一张背景图
        $background = imagecreatetruecolor($bg_width, $bg_height); // 背景图片
        $radius = 20;
        $color = imagecolorallocate($background, 255, 255, 255); // 为真彩色画布创建白色背景，再设置为透明
        imagefill($background, 0, 0, $color);
        imageColorTransparent($background, $color);
        imagefilledarc($background, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $color, IMG_ARC_PIE);

        //获取背景图片  后拷贝到图片中间

//        $image_path = './qorde.png';
//        $image_path_a = './avator.png';
//
//        $pathinfo = $this->get_extension($image_path);
//        switch (strtolower($pathinfo)) {
//            case 'jpg':
//            case 'jpeg':
//                $imagecreatefromjpeg = 'imagecreatefromjpeg';
//                break;
//            case 'png':
//                $imagecreatefromjpeg = 'imagecreatefrompng';
//                break;
//            case 'gif':
//            default:
//                $imagecreatefromjpeg = 'imagecreatefromstring';
//                $image_path = file_get_contents($image_path);
//                break;
//        }

        //先获取头像
        $url = $abcdata['result']['head_pic'];
        $response = $client->request('GET', $url);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        $resourceoba = imagecreatefromstring($remainingBytes);
        imagecopyresized($background, $resourceoba, 30, 25, 0, 0, 60, 60, imagesx($resourceoba), imagesy($resourceoba));

        $resourceobj = imagecreatefromstring($ddd);

        imagecopyresized($background, $resourceobj, 30, 100, 0, 0, 340, 340, imagesx($resourceobj), imagesy($resourceobj));

        $black = imagecolorallocate($background, 0x56, 0x53, 0x53);//字体颜色
        $whilte = imagecolorallocate($background, 255, 255, 255);
        $font = ROOT_PATH . '/public/static/font/kt14.ttf';
        //imagefilledrectangle($background, 0, 0, 399, 59, $whilte);
        imageTTFText($background, 14, 0, 120, 60, $black, $font, $abcdata['result']['nickname']);
        $gray = imagecolorallocate($background, 0x56, 0x53, 0x53);//字体颜色
        imageTTFText($background, 14, 0, 30, 470, $gray, $font, "扫一扫上面的二维码图案，加我为好友！");
        header("Content-type: image/jpg");
        imagejpeg($background);
        imagedestroy($background);
        imagedestroy($resourceobj);
    }

    /*
  *获取粉丝
  */
    public function message(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->getMessage($userid, 10); // 获取用户信息
        if (!empty($user_info)) {
            Response::create(['data' => $user_info, 'code' => '2000', 'message' => '获取消息成功'], 'json')->header($this->header)->send();
        } else {

            Response::create(['data' => [], 'code' => '4000', 'message' => '获取消息失败'], 'json')->header($this->header)->send();
        }
    }


    /**
     * @return mixed
     */
    public function getMyOrder(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $issale = $request->param('issale');
        $bidobj = new BidOrderServer();
        $wherearray = [
            'field' => 'max(bid_price),goods_id,add_time,user_id',
            'user_id' => $userid,
            'limit' => 20,
            'map'=>['a.user_id'=>['=',$userid]],
            'issale' => $issale,
            'endtime' => time(),
            'page' => (int)$request->param('page'),
        ];
        $count = 0;
        if ($issale == 2) {
            //卖家
            $wherearray['map'] =['a.sale_user_id'=>['=',$userid]];
            $data = $bidobj->Orderlists($wherearray, $count, false);
        } else {
            $data = $bidobj->Orderlists($wherearray, $count, true);
        }
// status  '1默认状态  2已付款  3已经发货 4已结收货成功 5已经评价 6订单自动取消 7 协议退款 8 协议退货 9 退货加退款'
        if (!empty($data)) {
            $bidorder = new BidOrder();
            $BidOrderServer = new BidOrderServer();
            $aliiamgobj = new AliyunOss(false);
            $ProductServerobj = new ProductServer();
            $datetime = new \DateTime('now');
            $currnet = $_SERVER['REQUEST_TIME'];
            $timev = 0.00;
            $expritetime = 0;
            $goods = new   Goods();
            $imgdataobj = null;
            foreach ($data as $key => &$v) {
                $userLogic = new UsersLogic();
                $user_info = $userLogic->get_info($v['sale_user_id']); // 获取用户信息
                $v['nickname'] = $user_info['result']['nickname'];
                $v['head_pic'] = $user_info['result']['head_pic'];
                $v['statusdesc'] ='';
                $datagood = $goods->where('goods_id', $v['goods_id'])->field('goods_name,goods_status,goods_content,start_price,every_add_price,upload_time,endTime,reserveprice')->find();
                if($datagood){
                    $datagood =$datagood->toArray();
                    $temporder_statusdata = Db::name('order_action')->where('order_id', $v['order_id'])->field('order_status')->find();
                    if ($issale == 2) {
                        $v['statusdesc'] = $BidOrderServer->getorderstatusdesc($temporder_statusdata['order_status'], $datagood);
                    } else {
                        $v['statusdesc'] = $BidOrderServer->getorderstatusdescforbuyer($temporder_statusdata['order_status'], $datagood);
                    }
                    $v['order_status'] = $temporder_statusdata['order_status']??'';
                    if (!empty($datagood['goods_name'])) {
                        $datagood['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $datagood['goods_name']);
                        $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $datagood['goods_name']);
                        $v['goods_name'] = mb_substr($v['goods_name'], 0, 8);
                    }
                }
                $v['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
                $v['bid_price'] = sprintf("%1\$.2f", $v['goods_price']);

                $imgdataobj = Db::name('GoodsImages');
                $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                    ->where(['goods_id' => $v['goods_id']])
                    ->whereNull('delete_time')
                    ->order('img_id')
                    ->cache(true)
                    ->limit(1)
                    ->select();
                foreach ($imgdata as $vs) {
                    if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                        $v['img'][] = ['img' => $vs['image_url_remote']];
                        $v['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']]??'';
                    } else {
                        try {
                            $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                            $v['img'][] = ['img' => $img];
                            $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                            $v['nowaterimg'][] = ['img' => $nowaterimg];
                            $imgdataobj->where('img_id', $vs['img_id'])
                                ->update([
                                    'image_url_remote' => $img,
                                    'image_url_remote_expire' => ['exp', 31536000],
                                    'image_url_remote_nowater' => $nowaterimg
                                ]);
                        } catch (\Exception $e) {
                            $v['img'] = [];
                            $v['nowaterimg'] = [];
                        }
                    }
                }
            }

        }


        Response::create(['data' => $data, 'page' => ['total' => $count, 'current' => (int)$request->param('page'), 'limit' => 10], 'code' => '2000', 'message' => '获取订单信息'], 'json')->header($this->header)->send();
    }


}
