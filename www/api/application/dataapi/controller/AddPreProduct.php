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

class AddPreProduct extends   BaseApi
{
    public function uploadImg(Request $request)
    {
        $outimgurl=[];
        $objDateTime = new \DateTime();
        $objDateTime->modify('+31536000 second');
        $userinfoobj['image_url_remote_expire'] = $objDateTime->getTimestamp();
        try{
        if(!is_null( request()->file('image'))){

            $files = request()->file('image');
            foreach($files as $file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $file_name =$file->getPathname();
                $image = Image::open($file);
                $type = $image->type();
                if($type=='jpeg' || $type=='jpg'){
                    $type ='.jpg';
                }elseif( $type =='png'){
                    $type ='.png';
                }elseif( $type =='gif'){
                    $type ='.gif';
                }else{
                    $type ='.png';
                }
                $currenttime = date('Y-m-d-H-i');
                $pre_ = Config::get('aliyun.pre_') . '/';
                $pre_name = $pre_ . $objDateTime->format('Y-m-d') . '/';
                //获取图片上传到阿里云
                $tmp = $pre_name . $currenttime . '_img_' . uniqid().$type;
                $ossiamgobj =new AliyunOss();
                $flag = $ossiamgobj->uploadFile(Config::get('aliyun.bucket'), $tmp, $file_name);
                $aliiamgobj = new AliyunOss(false);
                $img = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $tmp, 31536000);
                $outimgurl[] =str_replace(Config::get('aliyun.aliyundomain'),Config::get('aliyun.cdntianbao'),$img);
                //替换图片
            }
            Response::create(['data'=>$outimgurl,'status' => '200', 'code' => '2000', 'message' => '添加图片成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['status' => '200', 'code' => '4006', 'message' => "图片不能为空"], 'json')->header($this->header)->send();
        }
          //  Log::write($userinfoobj['nickname'].'会员添加，并且实时写入','notice');

        }catch (\RuntimeException $e){
            $me= $e->getMessage();
            Response::create(['status' => '200', 'code' => '4006', 'message' => $me], 'json')->header($this->header)->send();
        }
    }
}
