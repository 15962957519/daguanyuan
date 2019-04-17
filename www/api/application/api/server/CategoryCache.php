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
use think\Db;
use think\cache\driver\Redis;
/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */





class CategoryCache
{

    public static  function  getCategory($name){
            $goods_category = md5('goods_category');
        $goods_category_value =  Cache::get($goods_category);
            if(empty($goods_category_value)){
                $goods_category_value = Db::name('goods_category')
                    ->column('id,name');
                Cache::set($goods_category,$goods_category_value);
            }
          return   $goods_category_value[$name]??'';
    }


}


