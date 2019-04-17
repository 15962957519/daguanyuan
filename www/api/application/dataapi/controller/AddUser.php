<?php
namespace app\dataapi\controller;

use app\dataapi\server\UserServer;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Controller;
use think\Hook;
use think\Request;
use think\Response;
use think\Exception;
use app\dataapi\controller\JobQeue;
use think\Log;
use think\Image;
use app\dataapi\server\AliyunOss;
use app\dataapi\controller\BaseApi;
define("TOKEN", "jknsadjknasdjkasjkas88");

class AddUser extends   BaseApi
{
    public function uploadImg(Request $request)
    {
        $userinfoobj['nickname'] = $request->param('nickname');
        $userinfoobj['country'] = (int)$request->param('country');
        $userinfoobj['sex'] =(int) $request->param('sex');//0保密  1 男 2 女
        $userinfoobj['subscribe'] = (int)$request->param('subscribe');
        $userinfoobj['province'] =(int) $request->param('province');
        $userinfoobj['city'] = (int)$request->param('city');
        $objDateTime = new \DateTime();
        $objDateTime->modify('+315360000 second');
        $userinfoobj['image_url_remote_expire'] = $objDateTime->getTimestamp();
        try{
            $file=request()->file('image');
            if(!is_null($file)){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $type =  ".".$info->getExtension();
                    $fileath = ROOT_PATH . 'public' . DS . 'uploads'.DS.$info->getSaveName();

                    //  Log::write($userinfoobj['nickname'].'会员添加，并且实时写入','notice');
                    $objDateTime = new \DateTime();
                    $currenttime = date('Y-m-d-H-i');
                    $pre_ = Config::get('aliyun.pre_') . '/';
                    $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                    //获取图片上传到阿里云
                    $tmp = $pre_name . $currenttime . '_img_' . uniqid().$type;

                    //先上传图片
                    $ossiamgobj =new AliyunOss(false);
                    $flag = $ossiamgobj->uploadFile(Config::get('aliyun.bucket'), $tmp, $fileath);


                    if(!$flag){
                        Response::create(['status' => '200', 'code' => '4006', 'message' => "上传到阿里云失败"], 'json')->header($this->header)->send();
                    }
                    $hash_code =  sha1_file($fileath);
                    //获取图片url
                    $ossiamgobj =new AliyunOss(true);
                    $head_pic =  $ossiamgobj->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $tmp);
                    $userinfoobj['headimgurl'] = $head_pic;
                    $userinfoobj['hash_code'] = $hash_code;
                    //通过openid 获取用户个人信息
                    $uu = new UserServer();
                    $userong = $uu->vhostaddThirdUserinfo('', $request, $userinfoobj, 1);

                    if(!is_object($userong) && $userong<0){
                        if($userong==-3){
                            Response::create(['status' => '200', 'code' => '4006', 'message' => "图片重复"], 'json')->header($this->header)->send();
                        }
                        Response::create(['status' => '200', 'code' => '4006', 'message' => "添加失败上传重复或者其他原因"], 'json')->header($this->header)->send();
                    }
                    $UserServerobj = new  UserServer();
                    $UserServerobj->copytpuserstotpUsers($userong['user_id']);
                    Response::create(['userinfo'=>$userong,'data'=>$userinfoobj,'status' => '200', 'code' => '2000', 'message' => '添加会员成功'], 'json')->header($this->header)->send();
                    //获取url
                }
            }else{
                Response::create(['status' => '200', 'code' => '4006', 'message' => "图片不能为空"], 'json')->header($this->header)->send();
            }


        }catch (\RuntimeException $e){
            $me= $e->getMessage();
            Response::create(['status' => '200', 'code' => '4006', 'message' => $me], 'json')->header($this->header)->send();
        }
    }
}
