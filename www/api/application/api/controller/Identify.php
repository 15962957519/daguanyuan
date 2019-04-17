<?php
namespace app\api\controller;
use think\Db;
use think\Hook;
use think\Request;
use think\Session;
use think\Response;
use think\Config;
use think\Cache;
use app\api\behavior\Curl;
use app\dataapi\server\Weixin;
use app\dataapi\server\AliyunOss;

class Identify extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }
    //加载认证修改页面 （内容提交过）
    public function identify_edit(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $request = Db::name('user_verifty')->where(['user_id'=>$user_id])->find();
        if(!empty($request)){
            $oss = new AliyunOss(true);
            $res_id1 = $request['verifyIdcodefront'];
            $request['verifyIdcodefront'] = $oss->getnomatergetCurlofimgverfity(Config::get('aliyun.privatebucket'), $res_id1, 31536000);
            if($request['verifyIdcodeback'] != ''){
                $res_id2 = $request['verifyIdcodeback'];
                $request['verifyIdcodeback'] = $oss->getnomatergetCurlofimgverfity(Config::get('aliyun.privatebucket'), $res_id2, 31536000);
            }
            $request['is_identify'] = 1;

        }else{
            $request['is_identify'] = 0;
        }

        Response::create(['data'=>$request,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }


    /**[ 用户店铺升级 上传支付证明后提交 ]
     * @param Request $request
     * @return array
     */
    public function updateStore(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;

        $update_before =$request->param('update_before');  //店铺更新前
        $update_after =$request->param('update_after');  //店铺更新后
        $data['store_update_content'] = $update_before.'->'.$update_after;  //店铺升级内容
        $data['store_update_money'] =$request->param('update_money');  //店铺更新金额
        $data['store_update_time'] = time();                          //店铺升级时间
        $re = Db::name('users')->where(['user_id'=>$user_id])->update($data);
        if($re){
            $message=array(
                'code'=>2000,
                'status'=>1,
                'message'=>'提交成功'
            );
        }else{
            $message=array(
                'code'=>2001,
                'status'=>0,
                'message'=>'提交失败'
            );
        }
        return $message;
    }

    /**[ 卖家客服问题（提交） ]
     * @param Request $request
     * @return \think\response\Json
     */
    public function seller_service(Request $request){
        $methods =  $request->method();
        if('OPTIONS'==$methods){
            Response::create(['code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
        }
        $data['user_id'] = (int)$this->user_data['user_id']; //当前登录者user_id;

        $data['user_name'] = $this->user_data['nickname']; //当前登录者微信昵称;
        $data['msg_type'] = $request->param('msg_type');   //问题类型
        $data['msg_content'] = $request->param('msg_content');   //问题类型
        $data['message_img'] = $request->param('message_img');  //问题图片
        $data['msg_time'] = time();  //提交时间

        if(true){
            $re = Db::name('feedback')->insert($data);
            $message=array(
                'status'=>1,
                'code'=>2000,
                'message'=>'提交成功'
            );
        }else{
            $message=array(
                'status'=>0,
                'code'=>2001,
                'message'=>'提交失败'
            );
        }
        return json($message);
    }

    /**[ 上传客服问题图片/店铺升级图片 ]
     * @param Request $request
     */
    public function publish(Request $request)
    {
        $methods = $request->method();
        if ('OPTIONS' == $methods) {
            Response::create(['code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();
        }
        $files =  $request->file('file');
        $info = $files->move(ROOT_PATH . 'public/upload' . DS .'kefu_images');
        if($info) {
            try {
                // 成功上传后 获取上传信息    guwan/2018-01-05/2018-01-05-15-20_img_5a4f272ed9ec3.jpg
                // 输出 jpg
                $type = $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $fullfilename =   ROOT_PATH . 'public/upload' . DS .'kefu_images'.DS.$info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                $objDateTime = new \DateTime();
                $currenttime = date('Y-m-d-H-i');
                $pre_ = Config::get('aliyun.pre_') . '/';
                $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                //获取图片上传到阿里云
                //先上传图片
                $tmp = $pre_name . $currenttime . '_img_' . uniqid() . '.'.$type;
                $flag = (new AliyunOss(false))->uploadFile(Config::get('aliyun.bucket'), $tmp, $fullfilename);
                //获取图片url
                $aliiamgobj = new AliyunOss(true);
                $img = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $tmp, 31536000);
                $rescourseiddata =str_replace(Config::get('aliyun.aliyundomain'),Config::get('aliyun.cdntianbao'),$img);
                Response::create(['status' => '200', 'code' => '2000','data' => $rescourseiddata, 'message' => '上传成功'], 'json')->header($this->header)->send();
            } catch (\RuntimeException $e) {
                $me = $e->getMessage();
                Response::create(['status' => '200', 'code' => '4006', 'message' => $me], 'json')->header($this->header)->send();
            }
        }else{
            Response::create(['status' => '200', 'code' => '4006', 'message' => "图片不能为空"], 'json')->header($this->header)->send();
        }
    }




    /**[ 认证(个人) 新]
     * @param Request $request
     * @return \think\response\Json
     */
    public function new_person_identify(Request $request){
        $card = $request->param('card');
        $form_data = json_decode($card, true);

        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $data = [];
        $data['user_id']=$user_id;
        $data['name'] = $form_data['uname'];       //姓名
        $data['idcode'] = $form_data['number'];   //身份证号
        $data['telephone'] = $form_data['mobile']; //电话
        $identify_pic = $form_data['frontid'].','.$form_data['backid'];   //获取图片字符串（,号隔开）
        $data['date_time']=time();    //认证时间
        $data['identity_type']=1; //认证类型 1 代表个人
        //判断是否上传过认证信息
        $is_idetify_own = Db::name('user_verifty')->where(['user_id'=>$data['user_id']])->find();
        if(!empty($is_idetify_own)){
            $message=array(
                'code'=>2000,
                'status'=>3,
                'message'=>'已提交过认证信息,无需再次提交'
            );
        }else{
            $rescourseiddata = [];
            if($identify_pic){
                $img_arr = array_filter(explode(',',$identify_pic));
                foreach($img_arr as $key=>$v){
                    //$img = (new Weixin())->downWeixinImage($v);
                    $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
                    $cacheobj = Cache::store('filetoken');
                    $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($cacheobj);
                    $url = str_replace('MEDIA_ID', $v, $url);
                    $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
                    $img = Curl::file_get_contents_curl($url);
                    $objDateTime = new \DateTime();
                    $currenttime = date('Y-m-d-H-i');
                    $pre_ = Config::get('aliyun.pre_') . '/';
                    $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                    //获取图片上传到阿里云
                    //先上传图片
                    $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                    $flag = (new AliyunOss(false))->upload(Config::get('aliyun.privatebucket'), $tmp, $img);
                    if($flag){
                        $rescourseiddata[] = $tmp;  //远程路径
                        if($key == 0){
                            $data['verifyIdcodefront'] = $rescourseiddata[0];   //身份证正面
                        }
                        if($key == 1){
                            $data['verifyIdcodeback'] = $rescourseiddata[1];   //身份证反面
                        }
                    }

                }
                try{
                    Db::name('user_verifty')->insert($data);
                    Db::name('users')->where('user_id',$user_id)->update(['is_authentication'=>1]);
                    $message=array(
                        'code'=>2000,
                        'status'=>1,
                        'message'=>'认证成功，等待审核'
                    );
                }catch (\Exception $e){
                    $message=array(
                        'code'=>2001,
                        'status'=>2,
                        'message'=>'认证失败，请重新认证'
                    );
                }
            }else{
                $message=array(
                    'code'=>2001,
                    'status'=>3,
                    'message'=>'图片加载异常'
                );
            }
        }
        Response::create($message, 'json')->header($this->header)->send();
    }

    /**[ 认证(公司)  新]
     * @param Request $request
     * @return \think\response\Json
     */
    public function new_company_identify(Request $request){
        $card = $request->param('card');
        $form_data = json_decode($card, true);

        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $data['user_id'] = $user_id;
        $data['name'] = $form_data['uname'];       //企业名称
        $data['telephone'] = $form_data['mobile'];  //电话
        $verifyIdcodefront = $form_data['frontid']; //企业营业执照
        $data['date_time']=time();    //认证时间
        $data['identity_type']=2; //认证类型 2 代表企业
        //判断是否认证过
        $is_idetify_own = Db::name('user_verifty')->where(['user_id'=>$data['user_id']])->find();
        if(!empty($is_idetify_own)){
            $message=array(
                'code'=>2000,
                'status'=>3,
                'message'=>'已提交过认证信息,无需再次提交'
            );
        }else{
            if($verifyIdcodefront){
                $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
                $cacheobj = Cache::store('filetoken');
                $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($cacheobj);
                $url = str_replace('MEDIA_ID', $verifyIdcodefront, $url);
                $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
                $img = Curl::file_get_contents_curl($url);

                $objDateTime = new \DateTime();
                $currenttime = date('Y-m-d-H-i');
                $pre_ = Config::get('aliyun.pre_') . '/';
                $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';

                //获取图片上传到阿里云
                //先上传图片
                $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                $flag = (new AliyunOss(false))->upload(Config::get('aliyun.privatebucket'), $tmp, $img);
                if($flag){
                    $data['verifyIdcodefront'] = $tmp;
                }

                try{
                    Db::name('user_verifty')->insert($data);
                    Db::name('users')->where('user_id',$user_id)->update(['is_authentication'=>1]);
                    $message=array(
                        'code'=>2000,
                        'status'=>1,
                        'message'=>'认证成功'
                    );
                }catch (\Exception $e){
                    $message=array(
                        'code'=>2000,
                        'status'=>2,
                        'message'=>'认证失败'
                    );
                }
            }else{
                $message=array(
                    'code'=>2001,
                    'status'=>3,
                    'message'=>'图片加载异常'
                );
            }

        }
        Response::create($message, 'json')->header($this->header)->send();
    }

    /**[ 认证修改(个人)  新]
     * @param Request $request
     * @return \think\response\Json
     */
    public function new_identify_person_edit(Request $request){
        $card = $request->param('card');
        $form_data = json_decode($card, true);

        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;

        $data = [];
        $data['user_id'] = $user_id;
        $data['name'] = $form_data['uname'];      //姓名
        $data['idcode'] = $form_data['number'];   //身份证号
        $data['telephone'] = $form_data['mobile'];  //电话
        $identify_pic = $form_data['frontid'].','.$form_data['backid'];   //获取图片字符串（,号隔开）
        $data['date_time']=time();    //认证时间
        $data['identity_type']=1; //认证类型 1 代表个人
        if($identify_pic){
            $rescourseiddata = [];
            $img_arr = array_filter(explode(',',$identify_pic));
            foreach($img_arr as $key=>$v){
                $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
                $cacheobj = Cache::store('filetoken');
                $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($cacheobj);
                $url = str_replace('MEDIA_ID', $v, $url);
                $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
                $img = Curl::file_get_contents_curl($url);

                $objDateTime = new \DateTime();
                $currenttime = date('Y-m-d-H-i');
                $pre_ = Config::get('aliyun.pre_') . '/';
                $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                //获取图片上传到阿里云
                //先上传图片
                $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                $flag = (new AliyunOss(false))->upload(Config::get('aliyun.privatebucket'), $tmp, $img);
                if($flag){
                    $rescourseiddata[] = $tmp;  //远程路径
                    if($key == 0){
                        $data['verifyIdcodefront'] = $rescourseiddata[0];   //身份证正面
                    }
                    if($key == 1){
                        $data['verifyIdcodeback'] = $rescourseiddata[1];   //身份证反面
                    }
                }

            }
            try{
                Db::name('user_verifty')->where(array('user_id'=>$user_id))->update($data);
                Db::name('users')->where(array('user_id'=>$user_id))->update(['mobile'=>$data['telephone']]);
                $message=array(
                    'code'=>2000,
                    'status'=>1,
                    'message'=>'修改成功'
                );
            }catch (\Exception $e){
                $message=array(
                    'code'=>2000,
                    'status'=>2,
                    'message'=>'修改失败'
                );
            }
        }else{
            $message=array(
                'code'=>2001,
                'status'=>3,
                'message'=>'图片加载异常'
            );
        }

        Response::create($message, 'json')->header($this->header)->send();
    }

    /**[ 认证修改(公司) 新]
     * @param Request $request
     * @return \think\response\Json
     */
    public function new_company_identify_edit(Request $request){
        $card = $request->param('card');
        $form_data = json_decode($card, true);

        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;

        $data['user_id'] = $user_id;
        $data['name'] = $form_data['uname'];       //企业名称
        $data['telephone'] = $form_data['mobile'];  //电话
        $verifyIdcodefront = $form_data['frontid'];  //企业营业执照
        $data['date_time']=time();    //认证时间
        $data['identity_type']=2; //认证类型 2 代表企业
        if($verifyIdcodefront){
            $url = Config::get('weixin_api.get_down_weixinuploadimg_url');
            $cacheobj = Cache::store('filetoken');
            $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode($cacheobj);
            $url = str_replace('MEDIA_ID', $verifyIdcodefront, $url);
            $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);

            $img = Curl::file_get_contents_curl($url);
            $objDateTime = new \DateTime();
            $currenttime = date('Y-m-d-H-i');
            $pre_ = Config::get('aliyun.pre_') . '/';
            $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
            //获取图片上传到阿里云
            //先上传图片
            $tmp = $pre_name . $currenttime . '_img_' . uniqid();
            $flag = (new AliyunOss(false))->upload(Config::get('aliyun.privatebucket'), $tmp, $img);
            if($flag){
                $data['verifyIdcodefront'] = $tmp;
            }

            try{
                Db::name('user_verifty')->where(array('user_id'=>$user_id))->update($data);
                Db::name('users')->where(array('user_id'=>$user_id))->update(['mobile'=>$data['telephone']]);
                $message=array(
                    'code'=>2000,
                    'status'=>1,
                    'message'=>'修改成功'
                );
            }catch (\Exception $e){
                $message=array(
                    'code'=>2001,
                    'status'=>2,
                    'message'=>'修改失败'
                );
            }
        }else{
            $message=array(
                'code'=>2001,
                'status'=>3,
                'message'=>'图片加载异常'
            );
        }
        Response::create($message, 'json')->header($this->header)->send();
    }


}