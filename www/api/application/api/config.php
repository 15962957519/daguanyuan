<?php
return [

    'session'            => [
        'prefix'         => 'api',
        'type'           => '',
        'auto_start'     => true,
    ],
    // 默认输出类型
    'default_return_type'    => 'json',
    'domain'=>'http://wap.yipinfanghua.com/',
    'adminurl'=>'http://admin.jiuxintangwenhua.com',
    'weixin_api' => [
        'url' => 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code',
        'get_access_url' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=SECRET',
        'get_jsticket_access_url' => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi',
        'get_userinfo' => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID',
        'get_userinfo_userinfo' => 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN',
        'get_down_weixinuploadimg_url' => 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID',
        'appid' => 'wx732d013f1dd089a8',
        'secret' => '9e8b0751f77d0973b4d424ec367c3924',
        'key_secret' => 'def00000ca8525acf937abdeddb68be408c858dad2474564050122e000bb22567d91fc869b24fea5172e65611dd9ff905f1f92fb97663c0fd66a06c11fe860953477bf99',
        'app_secret' => 'gfhf7wgf7fbfw8wH8f8wjwstwgwvsttw',
        'merchant_number' => '1509046741',
        'notify' => 'http://wap.yipinfanghua.com/paymenting/notify',
        'finalorder' => 'http://wap.yipinfanghua.com/paymenting/isfinalorderpayment',
        'qrcodeurl' => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN'
    ],
    'aliyun'=>[
        'accessKeyId'=> 'LTAIBBb5LRFPVOxH',
        'accessKeySecret'=> 'VWcs6k434FnPqbe1S4eLbFo87UXFZ5',
        'endpoint'=> 'oss-cn-hangzhou-internal.aliyuncs.com',
        'endpoint_out'=> 'oss-cn-hangzhou.aliyuncs.com',
        'custom'=> 'oss-cn-hangzhou.aliyuncs.com',
        'bucket'=> 'yipinfanghuaweipai',
        'privatebucket'=> 'yipinfanghuaweipaiprivate',
        'pre_'=> 'fh',
        'aliyundomain'=> 'yipinfanghuaweipai.oss-cn-hangzhou-internal.aliyuncs.com',
        'cdntianbao'=> 'http://yipinfanghuaweipai.oss-cn-hangzhou.aliyuncs.com'
    ],

    'aliyunwatertextweixin'=>[
        'yongtu'=>'仅供艺品芳华认证使用 他用无效'
    ],
    'user_bond'=>[
        'money'=>100
    ],
    'userupdatezero'=>[
        'timestamp'=>1494777600
    ],
    'userregisterunixstamp'=>[
        'timestamp'=>1498838400
    ],

];