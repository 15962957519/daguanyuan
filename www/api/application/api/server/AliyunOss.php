<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 * 参考地址 http://www.cnblogs.com/txw1958/p/weixin-js-sharetimeline.html
 * http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html  微信JS-SDK说明文档
 */
namespace app\api\server;
use OSS\Core\OssException;
use OSS\OssClient;
use think\Config;

/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class AliyunOss
{
    private  $accessKeyId;
    private  $accessKeySecret;
    private  $endpoint;
    public $bucket;
    private  $isCName;
    private  $ossClient=null;

    public  function __construct($isCName=true)
    {
        $this->accessKeyId = Config::get('aliyun.accessKeyId');
        $this->accessKeySecret = Config::get('aliyun.accessKeySecret');
        if($isCName===true){
            $this->endpoint = Config::get('aliyun.custom');
        }else{
            $this->endpoint = Config::get('aliyun.endpoint');
        }
        $this->bucket = Config::get('aliyun.bucket');
        $this->isCName = $isCName;
        $this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
    }

    public  function upload($bucket, $object, $content){
        try {
            return  $this->ossClient->putObject($this->bucket, $object, $content);
        } catch (OssException $e) {
            print $e->getMessage();
        }
    }

    public function createBucket ($bucket){
        try {
            $this->ossClient->createBucket($bucket);
        } catch (OssException $e) {
            print $e->getMessage();
        }
    }



    /**
     * 获取带水印的图片
     * @param OBJ $request 参数解释
     * @return  json
     */
    public function getCurlofimg($bucketName,$object,$timeout=3600){
        /**
         *  生成一个带签名的可用于浏览器直接打开的url, URL的有效期是3600秒
         */
        $waterurl ='logo/yijian.png?x-oss-process=image/resize,P_20';
        $waterurl = rtrim(strtr(base64_encode($waterurl), '+/', '-_'), '=');
        $options = array(
            OssClient::OSS_PROCESS => 'image/resize,m_fixed,h_700,w_700/sharpen,100/watermark,image_'.$waterurl.',t_90,g_se,x_10,y_10',
        );
        $signedUrl = $this->ossClient->signUrl($bucketName, $object, $timeout, "GET", $options);

        return  $signedUrl;
    }


    public function nomatergetCurlofimg($bucketName,$object,$timeout=3600){
        /**
         *  生成一个带签名的可用于浏览器直接打开的url, URL的有效期是3600秒v
         */

        $options = array(
            OssClient::OSS_PROCESS => 'image/resize,m_fill,h_160,w_160/sharpen,100',
        );
        $signedUrl = $this->ossClient->signUrl($bucketName, $object, $timeout, 'GET', $options);

        return  $signedUrl;
    }


    /*
     * 身份证水印图片
     *
     */
    public function getnomatergetCurlofimgverfity($bucketName,$object,$timeout=3600){
        /**
         *  生成一个带签名的可用于浏览器直接打开的url, URL的有效期是3600秒
         */

        $waterurl =Config::get('aliyunwatertextweixin.yongtu');
        $waterurl = rtrim(strtr(base64_encode($waterurl), '+/', '-_'), '=');


        $fontType ='fangzhengshusong';
        $fontType = rtrim(strtr(base64_encode($fontType), '+/', '-_'), '=');

        $options = array(
            OssClient::OSS_PROCESS => 'image/resize,m_fixed,h_1200,w_1200/sharpen,100/watermark,type_'.$fontType.',size_40,text_'.$waterurl.',color_FFFFFF,size_30,rotate_315,t_90,g_center,x_10,y_10',
        );
        $signedUrl = $this->ossClient->signUrl($bucketName, $object, $timeout, "GET", $options);

        return  $signedUrl;
    }


    public  function uploadFile($bucket, $object, $file_name){
        try {
            return  $this->ossClient->uploadFile($bucket, $object, $file_name);
        } catch (OssException $e) {
            $e->getMessage();
            return false;
        }
    }


    /**
     * 获取带水印的图片 直接使用非授权的
     * http://bucket.<endpoint>/object?x-oss-process=image/action,parame_value/action,parame_value/...
     * @param OBJ $request 参数解释
     * @return  json
     */
    public function getCurlofimgUsenoAuth($bucketName,$object,$width=350,$height=350){
        $waterurl ='logo/yijian.png?x-oss-process=image/resize,P_20';
        $waterurl = rtrim(strtr(base64_encode($waterurl), '+/', '-_'), '=');
        $waterurl_t ='image/resize,m_pad,h_'.$width.',w_'.$height.'/sharpen,100/watermark,image_'.$waterurl.',t_90,g_se,x_10,y_10,image/bright,10';
        $thhpimgurl = 'http://'. $bucketName .'.'.$this->endpoint.'/'.$object.'?x-oss-process='.$waterurl_t;
        return  $thhpimgurl;
    }


}