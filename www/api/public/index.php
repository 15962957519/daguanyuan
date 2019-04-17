<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
//define('UPLOAD_PATH','public/upload/'); // 编辑器图片上传路径
const UPLOAD_PATH='public/upload/'; // 编辑器图片上传路径
const TPSHOP_CACHE_TIME =0x1da9c00;
//define('TPSHOP_CACHE_TIME',0x1da9c00); // TPshop 缓存时间  31104000
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']); // 网站域名
//define('APP_HOOK',true);
const APP_HOOK =true;
// 加载框架引导文件


register_shutdown_function(function(){
    $crror =error_get_last();
    if(isset($crror)){
        var_dump(error_get_last());
        file_put_contents(APP_PATH.'500.php',var_export($crror,true),FILE_APPEND);
    }
});
require __DIR__ . '/../thinkphp/start.php';
