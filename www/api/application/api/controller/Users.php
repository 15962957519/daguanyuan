<?php
namespace app\api\controller;
use think\Db;
use think\Hook;
use think\Request;
use think\Session;
use think\Response;
use think\Config;
use think\Cache;
use GuzzleHttp\Client;
use app\dataapi\server\Weixin;
use BaconQrCode\Renderer\Color\Rgb;
use app\dataapi\server\AliyunOss;
use app\api\server\GoodsServer;
use app\api\server\UsersServer;
use app\dataapi\model\Users as Userss;
use app\Home\Logic\UsersLogic;
use Carbon\Carbon;

class Users extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }
    //个人中心 （我的）
    public function index()
    {
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        //当前用户信息等级
        $usersevers = new UsersServer();
        $user_data = $usersevers->usermsg($user_id);

        $fansobj = Db::name('fans_collect');
        $fans_nums = $fansobj->where('user_id',$user_id)->whereNull('delete_time')->count();  //粉丝数
        $care_nums = $fansobj->where('fans_id',$user_id)->whereNull('delete_time')->count();  //关注数
        $goods_collect_count = Db::name('goods_collect')->where('user_id',$user_id)->whereNull('delete_time')->count();  //关注数
        $footprint=Db::name('goods_visit')->where('user_id',$user_id)->order('visit_id DESC')->whereNull('delete_time')->limit(20)->count();  //足迹(限制20条)


        //群发次数
        $current = $usersevers->usermsg($user_id);
        $today = Carbon::today();
        $unixttimetodaytime = strtotime($today);
        $datacc = Db::name('user_sendmessage')->where('user_id', $user_id)->where('addtime', '>', $unixttimetodaytime)->value('sendactivemessagecount');
        $datacc = $datacc!==null?(int)$datacc:0;
        $qunfa_count = $current['products'] - $datacc;

        $sharknum = Db::name('user_sharkedcount')->where('user_id', $user_id)->where('addtime', '>', $unixttimetodaytime)->value('sendactivemessagecount');
        $sharknum = $sharknum!==null?(int)$sharknum:0;
        $shark_count = $current['yaoyao'] - $sharknum;

        Response::create([
            'data'=>[
                'user_data' => $user_data,
                'fans_num' => $fans_nums,
                'care_num' => $care_nums,
                'qunfa_count' => $qunfa_count,
                'shark_count' => $shark_count,
                'goods_collect_count' => $goods_collect_count,
                'footprint' => $footprint
            ],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //个人资料
    public function personal(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $personal=Db::name('users')->where(array('user_id'=>$user_id))->field('nickname,head_pic,mobile')->find();

        Response::create(['data'=>$personal,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //足迹内部列表
    public function footprint(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 8;
        $footprint=Db::name('goods_visit')
            ->alias('v')
            ->join('__GOODS__ g','v.goods_id=g.goods_id')
            ->field('v.visittime,v.goods_id,g.start_price,g.goods_name,g.shop_price,g.original_img,g.click_count,g.endTime,g.is_distribute,g.distribute_proportion,g.distribute_money')
            ->where(array('v.user_id'=>$user_id))
            ->whereNull('v.delete_time')
            ->order('v.visit_id DESC')
            ->page($p,$limitnum)
            ->select();
        $ossobj = new AliyunOss(true);
        $aliyunbucket = Config::get('aliyun.bucket');
        foreach($footprint as $k=>$v){
            $footprint[$k]['original_img'] = $ossobj->getCurlofimgUsenoAuth($aliyunbucket, $v['original_img']);
            //获取当前出价
            $cur_price = Db::name('bid_order')->where(['goods_id'=>$v['goods_id']])->order('id DESC')->find();
            if($cur_price != false){
                $footprint[$k]['cur_price'] = $cur_price['bid_price'];
            }else{
                $footprint[$k]['cur_price'] = $v['start_price'];
            }
        }

        Response::create(['data'=>$footprint,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //足迹删除
    public function delfootprint(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id');
        if(!isset($goods_id)){
            Response::create(['code'=>5001,'message'=>'操作错误'], 'json')->header($this->header)->send();
        }

        $res = Db::name('goods_visit')->where(['user_id'=>$user_id,'goods_id'=>$goods_id])->update(['delete_time'=>time()]);
        if($res){
            Response::create(['code'=>2000,'message'=>'删除成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>5000,'message'=>'删除失败'], 'json')->header($this->header)->send();
        }
    }
    //足迹清除所有
    public function clearfootprint(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $res = Db::name('goods_visit')->where(['user_id'=>$user_id])->update(['delete_time'=>time()]);
        if($res){
            Response::create(['code'=>2000,'message'=>'清除成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>5000,'message'=>'清除失败'], 'json')->header($this->header)->send();
        }
    }
    //收藏列表
    public function collect_list(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 8;
        $collect_list = Db::name('goods_collect')->alias('c')
            ->field('c.collect_id,c.add_time,g.goods_id,g.original_img,g.goods_name,g.shop_price,g.is_on_sale,g.store_count,g.cat_id')
            ->join('goods g','g.goods_id = c.goods_id','INNER')
            ->where("c.user_id = $user_id")
            ->whereNull('c.delete_time')
            ->page($p,$limitnum)
            ->select();
        $ossobj = new AliyunOss(true);
        $aliyunbucket = Config::get('aliyun.bucket');
        foreach ($collect_list as $key=>&$vo){
            $vo['original_img'] = $ossobj->getCurlofimgUsenoAuth($aliyunbucket, $vo['original_img']);

        }
        Response::create(['data'=>$collect_list,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();


    }
    //收藏删除
    public function del_collectlist(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id');
        if(!isset($goods_id)){
            Response::create(['code'=>5001,'message'=>'操作错误'], 'json')->header($this->header)->send();
        }

        $res = Db::name('goods_collect')->where(['user_id'=>$user_id,'goods_id'=>$goods_id])->update(['delete_time'=>time()]);
        if($res){
            Response::create(['code'=>2000,'message'=>'删除成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>5000,'message'=>'删除失败'], 'json')->header($this->header)->send();
        }
    }
    //收藏清除所有
    public function clearcollectlist(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $res = Db::name('goods_collect')->where('user_id',$user_id)->update(['delete_time'=>time()]);
        if($res){
            Response::create(['code'=>2000,'message'=>'清除成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>5000,'message'=>'清除失败'], 'json')->header($this->header)->send();
        }
    }
    //粉丝列表页
    public function fans_list(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 8;
        $fans_list=Db::name('fans_collect')->alias('f')
            ->join('__USERS__ u','f.fans_id=u.user_id')
            ->where('f.user_id',$user_id)
            ->field('u.nickname,u.head_pic,f.fans_id')
            ->page($p,$limitnum)
            ->select();
        foreach($fans_list as $k=>$v){
            $arr = Db::name('fans_collect')->where(array('fans_id'=>$user_id,'user_id'=>$v['fans_id']))->find();
            if($arr){
                $fans_list[$k]['is_care'] = '1';  //已关注
            }else{
                $fans_list[$k]['is_care'] = '0';  //未关注
            }
        }
        Response::create(['data'=>$fans_list,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //关注列表页
    public function care_list(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 8;

        $care_list=Db::name('fans_collect')->alias('f')
            ->join('__USERS__ u','f.user_id=u.user_id')
            ->join('__STORE_LEVEL__ sl','u.store_level=sl.store_level_id')
            ->where(array('f.fans_id'=>$user_id))
            ->field('u.nickname,u.head_pic,u.user_level,f.user_id as fans_id,sl.store_name')
            ->page($p,$limitnum)
            ->select();
        Response::create(['data'=>$care_list,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //加关注
    public function create_care(Request $request){
        $sharked = $request->param('sharked');
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $care_id = $request->param('fans_id'); //关注人id
        if(isset($sharked) && $sharked == 1){
            $user_id = $request->param('fans_id'); //关注人id
            $care_id = (int)$this->user_data['user_id']; //当前登录者user_id;

        }
        $Re = Db::name('fans_collect')->where(array('fans_id'=>$user_id,'user_id'=>$care_id))->find();
        if($Re){
            $message = array('state'=>2,'code'=>2000,'msg'=>'已关注');
        }else{
            Db::name('fans_collect')->insert(['user_id'=>$care_id,'fans_id'=>$user_id,'add_time'=>time()]);
            $message = array('state'=>1,'code'=>2000,'msg'=>'关注成功');
        }
        return json($message);
    }
    //取消关注
    public function cancel_care(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $care_id=input('fans_id'); //取消关注人id

        $Re=Db::name('fans_collect')->where(array('fans_id'=>$user_id,'user_id'=>$care_id))->delete();
        if($Re){
            return json(array('state'=>0,'code'=>2000,'msg'=>'取消关注'));
        }

    }

    //新品开拍
    public function new_product(Request $request){
        $p = $request->param('page',1);
        $limitnum = 8;
        //获取24小时之内发布的新品
        $pai_new_goods=Db::name('goods')
            //->where('upload_time','>',time()-3600*3)
            ->whereNull('delete_time')
            ->field('goods_id,goods_name,upload_time,original_img,user_id')
            ->order('upload_time DESC')
            ->page($p,$limitnum)
            ->select();
        $goodsObj = Db::name('goods');
        $goods_list = [];
        foreach($pai_new_goods as $k=>&$v){
            $goods_list[] = $goodsObj->field('goods_id,goods_name,upload_time,original_img')->where('user_id',$v['user_id'])->order('goods_id DESC')->limit(6)->select();

        }
        Response::create(['data'=>$goods_list,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //我的抢购
    public function my_buying(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $where = [
            'is_on_sale'=>1,        //上架商品区
            'exchange_integral'=>0      //不检索积分商品区
        ];
        /*input('cat_id') ? $where['cat_id'] = input('cat_id') : false;  //商品区分类id
        input('is_on_sale') ? $where['is_on_sale'] = input('is_on_sale') : false;  //商品区分类id*/
        if(input('cat_id')){
            $where['cat_id'] = input('cat_id');
        }
        if(input('endtime')){
            $sort = input('endtime');
        }else{
            $sort = 'desc';      //截拍时间默认降序
        }
        if(input('is_on_sale') == 1 ){         //2：下架；1上架
            $where['endTime'] = ['gt',time()];  //上架 大于当前时间
        }else if(input('is_on_sale') == 2){
            $where['endTime'] = ['lt',time()];   //下架 小于当前时间
        }
        $my_cart = Db::name('goods')
            ->field('goods_id,goods_name,original_img,goods_content,upload_time,endTime,shop_price,click_count,store_count')
            ->where($where)
            ->whereNull('delete_time')
            ->order("endTime $sort")
            ->select();
        foreach($my_cart as $k=>$v){
            $res_id = $v['original_img'];
            $my_cart[$k]['original_img'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
        }
        Response::create(['data'=>$my_cart,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
    //摇一摇
    public function shaked(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;

        //当前用户信息等级
        $usersevers = new UsersServer();
        $user_data = $usersevers->usermsg($user_id);
        $shaked_num = $user_data['yaoyao'];  //当前等级瑶瑶次数

        $today = Carbon::today();
        $unixttimetodaytime = strtotime($today);
        $datacc = Db::name('user_sharkedcount')->where('user_id', $user_id)->where('addtime', '>', $unixttimetodaytime)->value('sendactivemessagecount');
        $datacc = $datacc!==null?(int)$datacc:0;
        $shaked_num = $shaked_num - $datacc;   //剩余瑶瑶次数

        if($shaked_num <= 0){
            Response::create(['status' => '4000', 'code' => '2001', 'message' => 'sorry!摇一摇次数已为0', 'data'=>[]], 'json')->header($this->header)->send();
        }

        //查询当前用户的粉丝id
        $fans_ids = Db::name('fans_collect')->where(['user_id'=>$user_id])->field('fans_id')->select();
        if(count($fans_ids) > 0){
            $fams_ids_arr = array_column($fans_ids,'user_id');
        }else{
            $fams_ids_arr = '';   //如果没有粉丝 定义为空；
        }
        //随机抽取一个用户粉丝
        $Users = new Userss();
        $user_info = $Users->where('user_id','not in',$fams_ids_arr)->field('user_id,nickname,head_pic,user_level')->order('rand()')->limit(1)->find();
        $user_info['shark_num'] = $shaked_num;
        if($user_info){
            $data = Db::name('user_sharkedcount')->where(['user_id'=>$user_id])->where('addtime', '>', $unixttimetodaytime)->value('sendactivemessagecount');
            if(!empty($data)){
                //跟新
                Db::name('user_sharkedcount')->where('user_id', $user_id)->where('addtime', '>', $unixttimetodaytime)->update(['sendactivemessagecount' => ['exp', 'sendactivemessagecount+1']]);
            }else{
                //新增加
                $datainsert = ['user_id' => $user_id, 'addtime' => time(), 'sendactivemessagecount' => 1];
                Db::name('user_sharkedcount')->insert($datainsert);
            }

            Response::create(['status' => '2000', 'code' => '2000', 'message' => '摇一摇成功', 'data'=>$user_info], 'json')->header($this->header)->send();
        }else{
            Response::create(['status' => '4000', 'code' => '2001', 'message' => '摇一摇失败', 'data'=>[]], 'json')->header($this->header)->send();
        }
    }
    //商户专区
    public function businessPlace(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $mycare = Db::name('fans_collect')->field('user_id')->where(['fans_id'=>$user_id])->select();  //我的关注
        $mycares = [];
        foreach($mycare as $k=>$v){
            $mycares[] = $v['user_id'];
        }
        $businessPlace = Db::name('users')->field('user_id,head_pic,nickname,user_level,is_authentication')->where('user_id','not in',$mycares)->where('user_id','<>',$user_id)->limit(30)->order('user_level DESC')->select();
        Response::create([ 'businessPlace'=>$businessPlace,'status' => '2000', 'code' => '2000', 'message' => '数据获取成功'], 'json')->header($this->header)->send();

    }

    //店铺等级相关权限
    public function storeLevel(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $paipincount = Db::name('goods')->where(['user_id'=>$user_id])->whereNull('delete_time')->count();  //当前在线商品区件数
        //当前用户信息等级
        $usersevers = new UsersServer();
        $currentlevel = $usersevers->usermsg($user_id);
        $currentlevel['startutime'] = time();  //开始时间
        $currentlevel['entutime'] = time() + $currentlevel['line_time2'] * 3600;  //结束时间

        Response::create([ 'data'=>['paipincount'=>$paipincount,'currentlevel'=>$currentlevel],'status' => '2000', 'code' => '2000', 'message' => '数据获取成功'], 'json')->header($this->header)->send();
    }

    //今日推广下的二维码
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
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($qrcodeuid);
        //先获取头像
        $url = $abcdata['result']['head_pic'];
        $response = $client->request('GET', $url);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        $resourceoba = imagecreatefromstring($remainingBytes);
        //源图像
        $background = imagecreatefrompng(ROOT_PATH.'public/static/img/spread/saoyisao.png');
        imagecopyresized($background, $resourceoba, 130, 103, 0, 0, 40, 40, imagesx($resourceoba), imagesy($resourceoba));
        imagejpeg($background);
        imagedestroy($background);
        imagedestroy($resourceoba);
    }

    //个人店铺下的二维码分享
    public function getUserImage(Request $request)
    {
        ob_end_clean();
        $qrcodeuid = $request->param('userid');
        input('type') ? $type = input('type') : false;
        if(isset($type)){
            $qrcodeuid = $this->user_data['user_id'];
        }
        $client = new Client();
        //生成二维码现在到图片
        $renderer = new \BaconQrCode\Renderer\Image\Png();
        $color = new Rgb(255, 255, 255);
        $renderer->setBackgroundColor($color);
        $color = new Rgb(10, 10, 10);
        $renderer->setForegroundColor($color);
        $renderer->setHeight(630);
        $renderer->setWidth(630);
        $renderer->setMargin(3);
        $writer = new \BaconQrCode\Writer($renderer);   //http://wap.yipinfanghua.com/#/user/seller_shop/2
        $url = Config::get('domain');
        $ddd = $writer->writeString($url . "#/user/seller_shop/{$qrcodeuid}");
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
        imageTTFText($background, 14, 0, 66, 470, $gray, $font, "扫一扫，加我为艺品芳华好友！");
        header("Content-type: image/jpg");
        imagejpeg($background);
        imagedestroy($background);
        imagedestroy($resourceobj);
    }

    /**获取当前用户等级
     * @param Request $request
     */
    public function curUserLevel(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        //当前用户信息等级
        $usersevers = new UsersServer();
        $userLevel = $usersevers->usermsg($user_id);
        Response::create(['code' => '2000', 'msg' => '获取成功', 'data'=>$userLevel], 'json')->header($this->header)->send();
    }

    /**
     * 头像上传
     * @return string
     *
     */
    public function personalsettings(Request $request)
    {
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        if($user_id > 0){
            // 获取表单上传文件 例如上传了001.jpg
            $file = $request->file('head_pic');//拍品图片
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->validate(['ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'upload/head_pic/');
                if($info){

                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    $newpic = ROOT_PATH . 'public' . DS . 'upload/head_pic/'.$info->getSaveName();
                    $objDateTime = new \DateTime();
                    $currenttime = date('Y-m-d-H-i');
                    $pre_ = Config::get('aliyun.bucket') . '/';
                    $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                    $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                    try{
                        $flag = (new AliyunOss(false))->uploadFile(Config::get('aliyun.bucket'), $tmp, $newpic);
                        if($flag == false){
                            Response::create(['code' => '4000', 'message' => '文件上传OSS失败'], 'json')->header($this->header)->send();
                        }
                    }catch(\Exception $e){
                        Response::create(['code' => '4000', 'message' => $e->getMessage()], 'json')->header($this->header)->send();
                    }

                }else{
                    // 上传失败获取错误信息
                    Response::create(['code' => '4000', 'message' => '文件上传服务器错误'], 'json')->header($this->header)->send();
                }
                $data['head_pic'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $tmp,350,350,1);
            }

            //开始入库操作
            if(empty($request->param('nickname'))){
                Response::create(['code' => '4000', 'message' => '参数错误'], 'json')->header($this->header)->send();
            }
            $data['nickname'] = $request->param('nickname');
            Db::name('users')->where('user_id',$user_id)->update($data);

            Response::create(['data'=>$data,'code' => '2000', 'message' => '个人信息设置成功'], 'json')->header($this->header)->send();
        }

    }


}