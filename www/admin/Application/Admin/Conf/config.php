<?php
return array(
     'URL_HTML_SUFFIX'       =>  '',  // URL伪静态后缀设置
    // 'OUTPUT_ENCODE' =>  true, //页面压缩输出支持   配置了 没鸟用
    'PAYMENT_PLUGIN_PATH' =>  PLUGIN_PATH.'payment',
    'LOGIN_PLUGIN_PATH' =>  PLUGIN_PATH.'login',
    'SHIPPING_PLUGIN_PATH' => PLUGIN_PATH.'shipping',
    'FUNCTION_PLUGIN_PATH' => PLUGIN_PATH.'function',
	'SHOW_PAGE_TRACE' => false,
	'CFG_SQL_FILESIZE'=>5242880,
    //'URL_MODEL'=>1, // 
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',

    //采集图片本地存储地址
    'TMPL_PARSE_STRING'  =>array('__COLLECT_IMG_PATH__' => '/Public/download/'),

    //采集图片上传接口地址
    'COLLECT_ONLINE_URL'  => 'http://api.guwan.tianbaoweipai.com/',//线上接口
    'COLLECT_DEV_URL'  => 'http://api.dev.com/',//本地接口

    'aliyun'=>[
        'accessKeyId'=> 'LTAIyGEXtNcWk3rp',
        'accessKeySecret'=> '7CcKEgB2gyqcO0cNmdNWrYxIMZWAQu',
        'endpoint'=> 'oss-cn-shanghai-internal.aliyuncs.com',
        'endpoint_out'=> 'oss-cn-shanghai.aliyuncs.com',
        'custom'=> 'oss-cn-shanghai.aliyuncs.com',
        'bucket'=> 'siji-online',
        'gwpre_'=> 'siji',
        'aliyundomain'=> 'siji-online.oss-cn-shanghai-internal.aliyuncs.com',
        'cdntianbao'=> 'http://img.sijiweipai.com'
    ],

    'aliyunwatertextweixin'=>[
        'yongtu'=>'仅供艺品芳华认证使用 他用无效'
    ],

);