<?php
namespace app\api\model;
use think\Model;
use think\Request;
/*
 * 产品图片（多张）处理
 */
class Goods extends Model
{
    public function upload_images($files){
        // 获取表单上传文件
        //$files = request()->file('image');
        $arr=array();
        foreach($files as $k=>$file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public/upload' . DS . 'goods');
            if($info){
                $arr[].='public/upload'.DS .'goods/'.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        return $arr;
    }

}
