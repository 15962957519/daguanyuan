<?php
namespace app\dataapi\controller;


use app\dataapi\model\RechareImages;
use app\dataapi\model\Recharge;
use app\dataapi\server\AliyunOss;
use app\dataapi\server\Weixin;
use app\Home\Logic\UsersLogic;
use think\Config;
use think\Db;
use think\Hook;
use think\Request;
use think\Response;

class UserUploadFinance extends BaseApi
{

    private $user_data;

    public function _initialize()
    {

         $result = Hook::exec('app\\dataapi\\behavior\\Jwt','appEnd',$this->user_data);
        parent::_initialize();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function loading(Request $request)
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
            $pre_ =  Config::get('aliyun.pre_').'/';
            $pre_name =$pre_. $objDateTime->format('Y-m-d').'/';

      //  file_put_contents(RUNTIME_PATH .'aaaa',var_export($media_data,true));
         $Jssdkobj =new    Weixin();
        foreach ($media_data as $v) {
            $birarystr = $Jssdkobj->downWeixinImage($v);
            $rescourseiddata[] = $tmp =$pre_name. $currenttime . '_img_' . uniqid();
            $flag = (new AliyunOss())->upload(Config::get('aliyun.bucket'), $tmp, $birarystr);
        }


        //内容上传到服务器


        $rescourse_id = '';
        //保存到数据 资源id
        try {

            $data=['datetime'=>time()];
             $id =   Db::name('rechare_images_id')->insertGetId($data);
                $list =[];
                $RechareImagesOBJ = new     RechareImages();
                foreach ($rescourseiddata as $v) {
                    $list[] = ['rec_order_id'=>intval($id),'rescourse_id'=>$v];
                }
              $RechareImagesOBJ->saveAll($list);

            Response::create(['data'=>$id,'code' => '2000', 'message' => '作品上传' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();

        } catch (\Exception $e) {

            Response::create(['code' => '4000', 'message' => '获取作品上传信息' . $e->getMessage() . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();

        }
        Response::create(['code' => '4000', 'message' => '获取作品上传信息--分类参数错误' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    public function save(Request $request)
        {

            $rechargeobj =      new    Recharge();
            $account = $request->param('account');
            $pingzhengid = $request->param('pingzhengid')??0;
            $account =floatval($account);
            $ctime =time();
            $user_id = $this->user_data['user_id']??0;


            $userinfo = ( new   UsersLogic())->get_info($user_id);
            $nickname =$userinfo['result']['nickname'];


            $order_sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $rechargeobj->data([
                'user_id'=>$user_id,
                'nickname'=>$nickname,
                'order_sn'=>$order_sn,
                'account'=>$account,
                'ctime'=>$ctime,
                'pingzhengid'=>$pingzhengid,


            ]);
            $rechargeobj->save();
            Response::create(['code' => '2000', 'message' => '提交用户付款记录' .  $request->param('actionname') . '成功'], 'json')->header($this->header)->send();

        }

}
