<?php

// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
use think\Config;
$config =Config::get('cache');
return [
    'connector'  => 'Redis',		    // Redis 驱动
    'type'  => 'Redis',		    // Redis 驱动
    'expire'     => 60,				// 任务的过期时间，默认为60秒; 若要禁用，则设置为 null
    'default'    => 'default',		// 默认的队列名称
//    'host'       => '101.37.175.6',	    // redis 主机ip
    'host'       => '47.97.4.127',	    // redis 主机ip
    'port'       => 6379,			// redis 端口
    'password'   =>'qwjktianok',				// redis 密码
    'select'     => 0,				// 使用哪一个 db，默认为 db0
    'timeout'    => 10,				// redis连接的超时时间
    'persistent' => false		// 是否是长连接

];