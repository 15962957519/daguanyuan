<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
ini_set('max_execution_time', '50');//

class Index    extends BaseApi
{

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        $hot_goods =  Db::name('goods')->where('is_hot',1)->where('is_on_sale',1)->order('goods_id DESC')->limit(5)->cache(true,TPSHOP_CACHE_TIME)->select();

        return ['data'=>['hot_goods'=>$hot_goods],'code'=>1,'message'=>'获取成功'];
    }


    public function indexpaimaiprolist(Request $request){
        $ProductServerobj = new ProductServer();
        $limit =5;
        $user_id =(int)$request->param('user_id');
        $goods_id =(int)$request->param('goods_id');
        $type =(int)$request->param('type');
        $nickname =$request->param('nickname')??'';
        $mobile =$request->param('mobile')??'';
        $page =(int)$request->param('page')>1?(int)$request->param('page'):1;
        $self_user_id=(int)$request->param('pm');
        $selectdata = $ProductServerobj->getStaticProductsForpaimaiquan($limit,$user_id,$type,$self_user_id ,$goods_id,$nickname,$mobile,$page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists'=>$selectdata], 'json')->header($this->header)->send();
    }



    public function indexpaimaiprolistCase(Request $request){
        $ProductServerobj = new ProductServer();
        $limit =1;
        $goods_id =(int)$request->param('goods_id');
        $selectdata = $ProductServerobj->getProductsForpaimaiquanCase($limit,0,0,1 ,$goods_id,1,$request);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists'=>$selectdata], 'json')->header($this->header)->send();
    }

}
