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
use think\Config;
/**
 * 阿里云消息发送
 * Class CatsLogic
 * @package Home\Logic
 */
class UtilMq
{
//计算签名
    public static function calSignature($str, $key)
    {
        $sign = "";
        if (function_exists("hash_hmac")) {
            $sign = base64_encode(hash_hmac("sha1", $str, $key, true));
        } else {
            $blockSize = 64;
            $hashfunc = "sha1";
            if (strlen($key) > $blockSize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blockSize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blockSize);
            $opad = str_repeat(chr(0x5c), $blockSize);
            $hmac = pack(
                'H*', $hashfunc(
                    ($key ^ $opad) . pack(
                        'H*', $hashfunc($key ^ $ipad) . $str
                    )
                )
            );
            $sign = base64_encode($hmac);
        }
        return $sign;
    }

//计算时间戳
    public static function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

}