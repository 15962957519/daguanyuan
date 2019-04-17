<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use app\dataapi\server\UserServer;

class LiqipengIndex    extends Controller
{

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {
        $UserServerobj = new UserServer();
        $userinfo=[
            'headimgurl'=>$request->param('headimgurl'),
            'nickname'=>$request->param('nickname'),
            'subscribe'=>0,
            'country'=>0,
            'province'=>0,
            'city'=>0,
        ];

        $dd =$UserServerobj->vhostaddThirdUserinfo('', $request,$userinfo,1);

        return ['code'=>200,'message'=>'生成成功'];
    }
}
