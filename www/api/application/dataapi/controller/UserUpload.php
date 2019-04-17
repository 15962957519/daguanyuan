<?php
namespace app\dataapi\controller;

use app\dataapi\model\Goods;
use app\dataapi\model\GoodsImages;
use app\dataapi\model\UserVerifty;
use app\dataapi\server\AliyunOss;
use app\dataapi\server\Weixin;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Db;
use app\api\server\UsersServer;
use think\Hook;
use think\Request;
use think\Response;
use app\Home\Logic\UsersLogic;
use app\dataapi\server\UserServer;
use  app\dataapi\model\GoodsCollect;
use app\api\server\UsersServer as apiserver;
use think\Cache;
class UserUpload extends BaseApi
{

    private $user_data;

    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }

    public function mobileisorcheck(Request $request){
        $userid = $this->user_data['user_id'];
        if($userid>0){
            $userLogic = new UsersLogic();
            $abcdata = $userLogic->get_info($userid);
            $userinfo['mobile_validated'] = $abcdata['result']['mobile_validated'];
            Response::create(['data' => $userinfo, 'code' => '2000', 'message' => '获取信息信息成功' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data'=>['mobile_validated'=>'0'],'code' => '4000', 'message' => '获取信息信息失败' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }



    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {
        //获取分类信息
        $goods_category = Db::name('goods_category')->where(['is_show' => 1,'level'=>1])->field('id,name,mobile_name')->order('sort_order ASC')->cache(false, TPSHOP_CACHE_TIME)->select();
        $cdow = Carbon::now();
        /*$toady = $cdow->format('m月d日');   //今天
        $tomorrow = $cdow->tomorrow()->format('m月d日'); //明天*/
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($userid);
        if (isset($abcdata['result']['store_level'])) {
            $usersevers = new UsersServer();
            $user_data = $usersevers->usermsg($userid);
            $userinfo['memberProductCount'] = $user_data['line_nums'];
            $productmax = Db::name('goods')->where('user_id', $userid)->where('endTime', '>', $_SERVER['REQUEST_TIME'])->whereNull('delete_time')->count();
            //已经上传的数量
            $userinfo['memberProductCounthased'] = (int)$productmax;
            $userinfo['astime'] = $cdow->format('Y-m-d H:i');
            $endDatec = $_SERVER['REQUEST_TIME'] + $user_data['line_time2']*3600;
            $userinfo['endDatec'] = date("Y-m-d H:i",$endDatec);

            $userinfo['mobile_validated'] = $abcdata['result']['mobile_validated'];
            $userinfo['mobile'] = $abcdata['result']['mobile'];
            $userinfo['is_authentication'] = $abcdata['result']['is_authentication'];
            $userinfo['store_level'] = $abcdata['result']['store_level'];

            if (!empty($goods_category)) {
                $data = ['name' => $goods_category, 'userinfo' => $userinfo];
                Response::create(['data' => $data, 'code' => '2000', 'message' => '获取作品上传信息分类等' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4000', 'message' => '获取作品上传信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function loading(Request $request)
    {
        $userid = (int)$this->user_data['user_id'];
        if ($userid <= 0) {
            Response::create(['code' => '4000', 'message' => '用户没有登陆' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }

        $cdow = Carbon::now();
        $usersevers = new apiserver();
        $user_data = $usersevers->usermsg($userid);
        $cdow = $cdow->addHour($user_data['line_time2']);
        $goodsjson = $request->param('goods');
        $data = json_decode($goodsjson,true);
        $endDatec = $cdow->getTimestamp();

        $datasql['endTime'] =strtotime($data['datetimeup']);
        if ($datasql['endTime'] > $endDatec) {
            Response::create(['userid' => $userid, 'code' => '4009', 'message' => '您的截止时间超过等级限制'  ], 'json')->header($this->header)->send();
        }
        $publishcount = Db::name('goods')->where(['user_id'=>$userid])->whereNull('delete_time')->where(['endTime'=>['>=',time()]])->count();
        if($publishcount >= $user_data['line_nums']){
            Response::create(['code' => 2001, 'message' => '您的上传件数超过等级限制！'], 'json')->header($this->header)->send();
            exit;
        }

        $objDateTime = new \DateTime();
        //下载下来微信传递的资源，然后上传到阿里云服务器上面
        if (isset($data['cat_id'])) {
            $goods_category = Db::name('goods_category')->where('id', (int)$data['cat_id'])->limit(1)->cache(true, TPSHOP_CACHE_TIME)->select();
            if (empty($goods_category)) {
                Response::create(['code' => '4000', 'message' => '获取作品上传信息--分类' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
            }
            $MEDIA_IDstr = $request->param('MEDIA_ID');
            $media_data = array();
            if (strpos($MEDIA_IDstr, ',') !== false) {
                $media_data = explode(',', $MEDIA_IDstr);
            } else {
                $media_data[] = $MEDIA_IDstr;
            }
            $currenttime = date('Y-m-d-H-i');
            $rescourseiddata = [];
            $pre_ = Config::get('aliyun.pre_') . '/';
            $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
            //  file_put_contents(RUNTIME_PATH .'aaaa',var_export($media_data,true));
            $media_data = array_filter($media_data);
            if (!empty($media_data)) {
                $aliyunoss = new AliyunOss(false);
                foreach ($media_data as $v) {
                    if (!empty($v)) {
                        $birarystr = $this->downWeixinImage($v);
                        if (stripos($birarystr, 'errmsg') !== false) {
                            file_put_contents(RUNTIME_PATH .'error.php',var_export($birarystr,true),FILE_APPEND|LOCK_EX);
                            Response::create(['code' => '4000', 'message' => '上传图片失败，请重新上传：失败原因'.$birarystr . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
                            break;
                        }
                        $rescourseiddata[] = $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                        try{
                            $aliyunoss->upload(Config::get('aliyun.bucket'), $tmp, $birarystr);
                        }catch(\OSS\Core\OssException $e ){
                            Response::create(['code' => '4000', 'message' => '上传图片失败，请重新上传：失败原因'.$e->getMessage()], 'json')->header($this->header)->send();
                            break;
                        }
                    }
                }
            }
            $datasql['original_img'] = $rescourseiddata[0] ?? '';
            //保存到数据 资源id
            try {
                $goodsobj = new Goods();
                $goods_id = 0;
                $datasql['user_id'] = $userid;
                if (isset($data['is_free_shipping']) && $data['is_free_shipping'] === true) {
                    $datasql['is_free_shipping'] = 1;
                }
                if (isset($data['enableReturn']) && $data['enableReturn'] === true) {
                    $datasql['enableReturn'] = 1;
                }
                if (isset($data['is_special_price']) && $data['is_special_price'] === true) {
                    $datasql['is_special_price'] = 1;
                }
                if (isset($data['is_talk_price']) && $data['is_talk_price'] === true) {
                    $datasql['is_talk_price'] = 1;
                }
                $datasql['upload_time'] = $_SERVER['REQUEST_TIME'];
                $datasql['on_time'] = $_SERVER['REQUEST_TIME'];
                $datasql['contact_mobile'] = trim($data['contact_mobile']);
                $datasql['cat_id'] = $data['cat_id'];
                $datasql['start_price'] = str_replace(',', '', $data['start_price']);

                if (!empty($data['goods_name'])) {
                    $datasql['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $data['goods_name']);
                }
                if (!empty($data['goods_content'])) {
                    $data['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $data['goods_content']);
                    $data['goods_content'] = ihtmlspecialchars($data['goods_content']);
                    $datasql['goods_content'] = filter_Emoji($data['goods_content']);
                }

                // 启动事务
                Db::startTrans();
                if ($goodsobj->allowField(true)->save($datasql) > 0) {
                    $goods_id = $goodsobj->goods_id;
                }
                if ($goods_id > 0) {
                    $list = [];
                    $GoodsImagesobj = new  GoodsImages();
                    foreach ($rescourseiddata as $v) {
                        $list[] = ['goods_id' => intval($goods_id), 'image_url' => $v, 'rescourse_id'=>$v];
                    }
                    $GoodsImagesobj->saveAll($list);
                    // 提交事务
                    Db::commit();
                }
                //发送30分钟到期提醒队列
                /*try{
                    $JobQeueobj = new  \app\dataapi\server\MqProducer();
                    $sumtime = $datasql['endTime'] -14*60;
                    $jobData = ['from'=>'fanghua','type'=>'carefinishwarm','ts' => $sumtime, 'bizId' => uniqid(), 'id' => $goods_id, 'is_or_member' => false];
                    $JobQeueobj->process($jobData);
                }catch(\Exception $e){

                }*/
                Response::create(['userid' => $userid, 'goods_id'=> $goods_id, 'code' => '2000', 'message' => '作品上传' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                Response::create(['code' => '4000', 'message' => '获取作品上传信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
            }
        }
        Response::create(['code' => '4000', 'message' => '获取作品上传信息--分类参数错误' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    public function downWeixinImage($media_data)
    {
        //下载微信图片接口
        $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
        $filecacheobj =  Cache::store('filetoken');
        $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($filecacheobj);
        $url = str_replace('MEDIA_ID', $media_data, $url);
        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => '',
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $GuRequest = new GuRequest('get', $url);
        $response = $client->send($GuRequest, ['timeout' => 200, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        return $remainingBytes;
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function editloading(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $goods_id = $request->param('goods_id');  //修改id
        $goods_id =intval($goods_id);
        if(request()->isPost()){
            $goodsjson = $request->param('goods');
            $data = json_decode($goodsjson,true);
            $goods_id = $data['goods_id'];
            if (isset($data['cat_id']) && $data['cat_id'] != '' && $goods_id > 0 && $userid > 0) {
                $goods_category = Db::name('goods_category')->where('id', (int)$data['cat_id'])->limit(1)->cache(true, TPSHOP_CACHE_TIME)->select();
                if (empty($goods_category)) {
                    Response::create(['code' => '4000', 'message' => '获取作品上传信息--分类' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
                }
                $userLogic = new UsersLogic();
                $abcdata = $userLogic->get_info($userid);
                $cdow = Carbon::now();
                $usersevers = new apiserver();
                $user_data = $usersevers->usermsg($userid);
                $cdow = $cdow->addHour($user_data['line_time2']);
                $endDatec = $cdow->getTimestamp();

                $datasql['endTime'] =strtotime($data['datetimeup']);
                if ($datasql['endTime'] > $endDatec) {
                    Response::create(['userid' => $userid, 'code' => '4009', 'message' => '您的截止时间超过等级限制'  ], 'json')->header($this->header)->send();
                }
                $publishcount = Db::name('goods')->where(['user_id'=>$userid])->whereNull('delete_time')->where(['endTime'=>['>=',time()]])->count();
                if($publishcount >= $user_data['line_nums']){
                    Response::create(['code' => 2001, 'message' => '您的上传件数超过等级限制！'], 'json')->header($this->header)->send();
                    exit;
                }

                $MEDIA_IDstr = $request->param('MEDIA_ID');
                $media_data = array();
                if (strpos($MEDIA_IDstr, ',') !== false) {
                    $media_data = explode(',', $MEDIA_IDstr);
                } else {
                    $media_data[] = $MEDIA_IDstr;
                }

                $objDateTime = new \DateTime();
                $currenttime = date('Y-m-d-H-i');
                $rescourseiddata = [];

                $pre_ = Config::get('aliyun.gwpre_') . '/';
                $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                $media_data = array_filter($media_data);
                $oss = new AliyunOss(false);
                if (!empty($media_data)) {
                    foreach ($media_data as $v) {
                        if (!empty($v)) {
                            $birarystr = $this->downWeixinImage($v);
                            $rescourseiddata[] = $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                            $oss->upload(Config::get('aliyun.bucket'), $tmp, $birarystr);
                        }
                    }
                }

                if (!empty($data['newedimage']) && is_array($data['newedimage']) && $goods_id > 0) {
                    $data['newedimage'] = array_column($data['newedimage'],'img_id');

                    $needdelete = new  GoodsImages();
                    $needdelete->where('goods_id', $goods_id);
                    $needdeleteobj = $needdelete->whereNotIn('image_url', $data['newedimage']);
                    if (!is_null($needdeleteobj) && is_object($needdeleteobj)) {
                        $needdeleteobj->update(['delete_time'=>time()]);
                    }

                    $datasql['original_img'] = $data['newedimage'][0] ?? '';  //商品区封面图

                }else{
                    $datasql['original_img'] = $rescourseiddata[0] ?? '';  //商品区封面图
                }

                try {

                    $goodsobj = model('goods');
                    $datasql['user_id'] = $userid;
                    $datasql['is_goods'] = 0;
                    if (isset($data['is_free_shipping']) && $data['is_free_shipping'] === true) {
                        $datasql['is_free_shipping'] = 1;
                    }else if($data['is_free_shipping'] === false){
                        $datasql['is_free_shipping'] = 0;
                    }
                    if (isset($data['enableReturn']) && $data['enableReturn'] === true) {
                        $datasql['enableReturn'] = 1;
                    }else if($data['enableReturn'] === false){
                        $datasql['enableReturn'] = 0;
                    }
                    if (isset($data['is_special_price']) && $data['is_special_price'] === true) {
                        $datasql['is_special_price'] = 1;
                    }else if($data['is_special_price'] === false){
                        $datasql['is_special_price'] = 0;
                    }
                    if (isset($data['is_talk_price']) && $data['is_talk_price'] === true) {
                        $datasql['is_talk_price'] = 1;
                    }else if($data['is_talk_price'] === false){
                        $datasql['is_talk_price'] = 0;
                    }
                    $datasql['upload_time'] = $_SERVER['REQUEST_TIME'];
                    $datasql['last_update'] = $_SERVER['REQUEST_TIME'];
                    $datasql['is_recommend'] = 0;
                    $datasql['is_upload'] = 2;
                    $datasql['is_heler_likeand'] = 0;
                    $datasql['is_gernerorder'] = 0;
                    $datasql['cat_id'] = $data['cat_id'];
                    $datasql['contact_mobile'] = trim($data['contact_mobile']);
                    $datasql['start_price'] = str_replace(',', '', $data['start_price']);
                    $datasql['autoremindproductserver'] = 0;
                    //作品清零 正对会员5月15号之前的
                    $temppdata = $goodsobj->where('goods_id', $goods_id)->field('goods_id,upload_time')->find();
                    $datasql['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $data['goods_name']);

                    $data['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $data['goods_content']);
                    $data['goods_content'] = ihtmlspecialchars($data['goods_content']);
                    $datasql['goods_content'] = filter_Emoji($data['goods_content']);

                    if (Config::get('userupdatezero.timestamp') > 0) {
                        if (isset($abcdata['result']['reg_time']) && $abcdata['result']['reg_time'] >= Config::get('userupdatezero.timestamp') || (isset($temppdata['upload_time']) && $temppdata['upload_time'] >= Config::get('userupdatezero.timestamp'))) {
                            //   需要清零
                            $datasql['click_count'] = 0;
                            //更新用户喜欢
                            $goodsshouc = new GoodsCollect();
                            $goodsshouc->where('goods_id', $goods_id)->update(['delete_time'=>time()]);
                            $goodsobj->tempgoods_id=$goods_id;
                            $goodsobj->save(['is_heler_likeand'=>0,'vistorassign'=>0,'hasvistorassin'=>0],['goods_id'=>$goods_id]);

                        }
                    }
                    // 启动事务
                    Db::startTrans();
                    model('goods')->allowField(true)->save($datasql, ['goods_id' => $goods_id]);
                    $list = [];
                    $GoodsImagesobj = new GoodsImages();
                    foreach ($rescourseiddata as $v) {
                        $list[] = ['goods_id' => intval($goods_id), 'image_url' => $v, 'rescourse_id'=>$v];
                    }
                    $GoodsImagesobj->saveAll($list);
                    //cachecatchrm('goodsimages'.$goods_id);
                    // 提交事务
                    Db::commit();
                    //发送30分钟到期提醒队列
                    /*try{
                        $JobQeueobj = new  \app\dataapi\server\MqProducer([]);
                        $sumtime = $datasql['endTime'] -14*60;
                        $jobData = ['from'=>'fanghua','type'=>'carefinishwarm','ts' => $sumtime, 'bizId' => uniqid(), 'id' => $goods_id, 'is_or_member' => false];
                        $JobQeueobj->process($jobData);
                    }catch(\Exception $e){

                    }*/
                    Response::create(['userid' => $userid, 'goods_id'=>$goods_id, 'code' => '2000', 'message' => '作品修改' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();

                } catch (\Exception $e) {
                    //回滚事务
                    Db::rollback();
                    Response::create(['code' => '4000', 'message' => '获取作品修改信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();

                }
            }else{
                Response::create(['code' => '4000', 'message' => '获取品修改信息--分类参数错误' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
            }

        }

        $goods_edit = Db::name('goods')
            ->alias('G')
            ->join('__GOODS_CATEGORY__ C', 'G.cat_id = C.id')
            ->field('G.goods_id,G.cat_id,G.goods_name,G.start_price,G.goods_content,G.is_talk_price,G.is_free_shipping,G.endTime,G.is_distribute,G.distribute_proportion,G.distribute_money,
            G.contact_mobile,G.contact_wx,G.enableReturn,C.name')
            ->where(array('goods_id' => $goods_id))
            ->find();
        $goods_images = Db::name('goods_images')->field('img_id,image_url')->where(['goods_id' => $goods_id])->select();
        foreach ($goods_images as $k=>$v){
            $res_id = $v['image_url'];
            $goods_images[$k]['image_url'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
            $goods_images[$k]['img_id'] =$res_id;
        }

        Response::create(['data' => ['goods_edit' => $goods_edit, 'goods_images' => $goods_images], 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();

    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function userverfityimgback(Request $request)
    {
        $objDateTime = new \DateTime();
        //下载下来微信传递的资源，然后上传到阿里云服务器上面
        //获取分类信息
        $data = $this->request->except('token');

        $MEDIA_IDstr = $request->param('MEDIA_ID');
        $userid = $this->user_data['user_id'];

        $media_data = array();
        if (strpos($MEDIA_IDstr, ',') !== false) {
            $media_data = explode(',', $MEDIA_IDstr);
        } else {
            $media_data[] = $MEDIA_IDstr;
        }
        $currenttime = date('Y-m-d-H-i');
        $rescourseiddata = [];
        $verfityshenfzimg = '';
        $pre_ = Config::get('aliyun.pre_') . '/';
        $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';

        $media_data = array_filter($media_data);


        if (!empty($media_data)) {
            foreach ($media_data as $v) {
                if (!empty($v)) {
                    $birarystr = $this->downWeixinImage($v);
                    $rescourseiddata[] = $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                    $flag = (new AliyunOss())->upload(Config::get('aliyun.bucket'), $tmp, $birarystr);
                    $aliiamgobj = new AliyunOss(false);
                    $verfityshenfzimg = $aliiamgobj->getnomatergetCurlofimgverfity(Config::get('aliyun.bucket'), $rescourseiddata[0], 31536000);

                }
            }
        }
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');

        $expritetime = $datetime->getTimestamp();


        //内容上传到服务器
        //保存到数据 资源id
        try {
            if ($userid > 0 && !empty($rescourseiddata[0]) && $verfityshenfzimg != '') {
                $UserVeriftyobj = new  UserVerifty();

                $flag = $UserVeriftyobj->where(['user_id' => $userid])->find();
                if (!empty($flag)) {
                    $UserVeriftyobj->save([
                        'verifyIdcodeback' => $rescourseiddata[0],
                        'verifyIdcodeback_romote' => $verfityshenfzimg,
                        'verifyIdcodeback_expire' => $expritetime,
                        'date_time' => $_SERVER['REQUEST_TIME']
                    ], ['user_id' => intval($userid)]);
                    $UserVeriftyobj->isUpdate(true)->save();
                } else {
                    $UserVeriftyobj->data([
                        'user_id' => intval($userid),
                        'verifyIdcodeback' => $rescourseiddata[0],
                        'verifyIdcodeback_romote' => $verfityshenfzimg,
                        'verifyIdcodeback_expire' => $expritetime,
                        'date_time' => $_SERVER['REQUEST_TIME']
                    ]);
                    $UserVeriftyobj->save();
                }


                Response::create(['data' => $verfityshenfzimg, 'code' => '2000', 'message' => '上传' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            }

        } catch (\Exception $e) {
            Response::create(['code' => '4000', 'message' => '上传身份证信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }
        Response::create(['code' => '4001', 'message' => '上传身份证信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function userverfityimgfront(Request $request)
    {
        $objDateTime = new \DateTime();
        //下载下来微信传递的资源，然后上传到阿里云服务器上面
        //获取分类信息
        $data = $this->request->except('token');

        $MEDIA_IDstr = $request->param('MEDIA_ID');
        $userid = $this->user_data['user_id'];

        $media_data = array();
        if (strpos($MEDIA_IDstr, ',') !== false) {
            $media_data = explode(',', $MEDIA_IDstr);
        } else {
            $media_data[] = $MEDIA_IDstr;
        }
        $currenttime = date('Y-m-d-H-i');
        $rescourseiddata = [];
        $verfityshenfzimg = '';
        $pre_ = Config::get('aliyun.pre_') . '/';
        $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';

        $media_data = array_filter($media_data);


        if (!empty($media_data)) {
            foreach ($media_data as $v) {
                if (!empty($v)) {
                    $birarystr = $this->downWeixinImage($v);
                    $rescourseiddata[] = $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                    $flag = (new AliyunOss())->upload(Config::get('aliyun.bucket'), $tmp, $birarystr);
                    $aliiamgobj = new AliyunOss(false);
                    $verfityshenfzimg = $aliiamgobj->getnomatergetCurlofimgverfity(Config::get('aliyun.bucket'), $rescourseiddata[0], 31536000);

                }
            }
        }
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');

        $expritetime = $datetime->getTimestamp();
        $UserVeriftyobj = new  UserVerifty();

        $flag = $UserVeriftyobj->where(['user_id' => $userid])->find();


        //内容上传到服务器
        //保存到数据 资源id
        try {
            if ($userid > 0 && !empty($rescourseiddata[0]) && $verfityshenfzimg != '') {
                $list = [];
                $UserVeriftyobj = new  UserVerifty();

                $flag = $UserVeriftyobj->where(['user_id' => $userid])->find();

                if (!empty($flag)) {
                    $UserVeriftyobj->save([
                        'verifyIdcodefront' => $rescourseiddata[0],
                        'verifyIdcodefront_remote' => $verfityshenfzimg,
                        'verifyIdcodefront_expire' => $expritetime,
                        'date_time' => time(),
                    ], ['user_id' => intval($userid)]);
                    $UserVeriftyobj->isUpdate(true)->save();
                } else {
                    $UserVeriftyobj->data([
                        'user_id' => intval($userid),
                        'verifyIdcodefront' => $rescourseiddata[0],
                        'verifyIdcodefront_remote' => $verfityshenfzimg,
                        'verifyIdcodefront_expire' => $expritetime,
                        'date_time' => time(),
                    ]);
                    $UserVeriftyobj->save();
                }

                //   $verfityshenfzimg ='https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=2722582827,3890415943&fm=23&gp=0.jpg';
                Response::create(['data' => $verfityshenfzimg, 'code' => '2000', 'message' => '上传' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            }

        } catch (\Exception $e) {
            Response::create(['code' => '4000', 'message' => '上传身份证信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }
        Response::create(['code' => '4001', 'message' => '上传身份证信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function userverfityimghold(Request $request)
    {
        $objDateTime = new \DateTime();
        //下载下来微信传递的资源，然后上传到阿里云服务器上面
        //获取分类信息
        $data = $this->request->except('token');

        $MEDIA_IDstr = $request->param('MEDIA_ID');
        $userid = $this->user_data['user_id'];

        $media_data = array();
        if (strpos($MEDIA_IDstr, ',') !== false) {
            $media_data = explode(',', $MEDIA_IDstr);
        } else {
            $media_data[] = $MEDIA_IDstr;
        }
        $currenttime = date('Y-m-d-H-i');
        $rescourseiddata = [];
        $verfityshenfzimg = '';
        $pre_ = Config::get('aliyun.pre_') . '/';
        $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';

        $media_data = array_filter($media_data);


        if (!empty($media_data)) {
            foreach ($media_data as $v) {
                if (!empty($v)) {
                    $birarystr = $this->downWeixinImage($v);
                    $rescourseiddata[] = $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                    $flag = (new AliyunOss())->upload(Config::get('aliyun.bucket'), $tmp, $birarystr);
                    $aliiamgobj = new AliyunOss(false);
                    $verfityshenfzimg = $aliiamgobj->getnomatergetCurlofimgverfity(Config::get('aliyun.bucket'), $rescourseiddata[0], 31536000);

                }
            }
        }
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');

        $expritetime = $datetime->getTimestamp();


        //内容上传到服务器
        //保存到数据 资源id
        try {
            if ($userid > 0 && !empty($rescourseiddata[0]) && $verfityshenfzimg != '') {
                $list = [];
                $UserVeriftyobj = new  UserVerifty();

                $flag = $UserVeriftyobj->where(['user_id' => $userid])->find();
                if (!empty($flag)) {
                    $UserVeriftyobj->save([
                        'verifyIdcodehold' => $rescourseiddata[0],
                        'verifyIdcodehold_remote' => $verfityshenfzimg,
                        'verifyIdcodehold_expire' => $expritetime,
                        'date_time' => $_SERVER['REQUEST_TIME']
                    ], ['user_id' => intval($userid)]);
                    $UserVeriftyobj->isUpdate(true)->save();
                } else {
                    $UserVeriftyobj->data([
                        'user_id' => intval($userid),
                        'verifyIdcodehold' => $rescourseiddata[0],
                        'verifyIdcodehold_remote' => $verfityshenfzimg,
                        'verifyIdcodehold_expire' => $expritetime,
                        'date_time' => time(),
                    ]);
                    $UserVeriftyobj->save();
                }

                Response::create(['data' => $verfityshenfzimg, 'code' => '2000', 'message' => '上传' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            }

        } catch (\Exception $e) {
            Response::create(['code' => '4000', 'message' => '上传身份证信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }
        Response::create(['code' => '4001', 'message' => '上传身份证信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function userverfityimgsave(Request $request)
    {
        $objDateTime = new \DateTime();
        //下载下来微信传递的资源，然后上传到阿里云服务器上面
        //获取分类信息
        $data = $this->request->except('token');

        $userid = $this->user_data['user_id'];


        //内容上传到服务器
        //保存到数据 资源id
        try {
            if ($userid > 0) {

                $UserVeriftyobj = new  UserVerifty();

                $flag = $UserVeriftyobj->where(['user_id' => $userid])->find();
                if (!empty($flag)) {
                    $UserVeriftyobj->save([
                        'name' => $data['name'],
                        'idcode' => $data['idcode'],
                        'telephone' => $data['telephone'],
                        'type' => 1,
                        'date_time' => $_SERVER['REQUEST_TIME']
                    ], ['user_id' => intval($userid)]);
                    $UserVeriftyobj->isUpdate(true)->save();
                } else {
                    $UserVeriftyobj->data([
                        'user_id' => intval($userid),
                        'name' => $data['name'],
                        'idcode' => $data['idcode'],
                        'telephone' => $data['telephone'],
                        'type' => 1,
                        'date_time' => time(),
                    ]);
                    $UserVeriftyobj->save();
                    $UserServer = new    UserServer();
                    $UserServer->UpdateUserinfoP(intval($userid), ['telephone' => $data['telephone']]);
                }

                Response::create(['code' => '2000', 'message' => '上传用户审核信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            }

        } catch (\Exception $e) {
            Response::create(['code' => '4000', 'message' => '上传用户审核信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }
        Response::create(['code' => '4001', 'message' => '上传用户审核信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

}
