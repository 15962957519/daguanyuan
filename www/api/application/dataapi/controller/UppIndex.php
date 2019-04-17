<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use app\dataapi\server\UserServer;
use think\Request;
use think\Response;
use app\dataapi\server\BidOrderServer;
use app\dataapi\controller\BaseApi;
use app\dataapi\server\ProductServer;
use app\dataapi\server\GoodCollectServer;
use app\dataapi\model\Users;
use app\dataapi\lib\Image;
class UppIndex    extends BaseApi
{

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {
        $UserServerobj = new UserServer();

        $headimgurl =$request->param('headimgurl');
        $nickname =$request->param('nickname');

//|| (stripos($headimgurl,'.jpg')===false ||stripos($headimgurl,'.png')===false )


        if(empty($headimgurl) ||empty($nickname) || (stripos($headimgurl,'.jpg')===false  && stripos($headimgurl,'.png')===false && stripos($headimgurl,'.gif')===false)){

            return '参数错误';

        }



        $userinfo=[
            'headimgurl'=>$request->param('headimgurl'),
            'nickname'=>$request->param('nickname'),
            'subscribe'=>0,
            'country'=>0,
            'province'=>0,
            'city'=>0,
        ];

        $dd =$UserServerobj->vhostaddThirdUserinfo('', $request,$userinfo,1);
        if(!is_object($dd) &&  $dd==-1){
            return '名称重复';
        }
        if(!is_object($dd)  && $dd==-2){
            return '图片重复';
        }
        if(is_object($dd) ){
        return ['data'=>$dd,'code'=>200,'message'=>'生成成功'];
        }

        return '参数错误';

    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function bidProudctById(Request $request)
    {

        $good_id = (int)$request->param('good_id');
        if($good_id<=0){
            Response::create(['code' => '4008', 'message' => '出价失败,作品不参数没有传入'], 'json')->header($this->header)->send();
        }


        $datauser =  Db::name('users')->where('fictitious',1)->order('rand()')->find();
        $Image =new Image();
        $datauser = $Image->flagimage();
        $userid =$datauser['user_id'];
        if($userid<=0){
            Response::create(['code' => '4000', 'message' => '出价失败，用户没有指定到'], 'json')->header($this->header)->send();
        }

        $page = (int)$request->param('page');
        $page =$page??1;

        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->vhostAddIsToplatform($userid, $good_id, $request);

        if ($data > 0) {
            //返回最新出价列表
            $datalist = $BidOrderServer->listsarray($good_id, 15,false,['page'=>$page]);
            Response::create(['data' => $datalist, 'code' => '2000', 'message' => '出价成功'], 'json')->header($this->header)->send();
        }

        Response::create(['code' => '4000', 'message' => '出价失败'], 'json')->header($this->header)->send();
    }
    public function flagimage(){
        $datauser =  Db::name('users')->where('fictitious',1)->order('rand()')->find();
        $flag = $this->imagetest($datauser['head_pic']);
        if(!$flag or !isset($flag['width']) or   !isset($flag['height']) or ($flag['width']==0 or   $flag['height']==0)){
            return  $this->flagimage();
        }
        return $datauser;

    }

    public function imagetest($url){
        if(empty($url)){
            return false;
        }
        $FastImageSize = new \FastImageSize\FastImageSize();
        $imageSize = $FastImageSize->getImageSize($url);
        return  $imageSize;
    }


    /*
* 用户关注作品 喜欢作品
*/
    public function userLikeProduct(Request $request){
        $ddd= $this->flagimage();
        $userid =$ddd['user_id'];
        $UserServer = new UserServer();
        $goods_id= (int)$request->param('goods_id');
        if($userid>0 && $goods_id>0){
            $flag = $UserServer->userUpdateLike($userid,$request->except('token'));
            if($flag===true){
                $data =   $UserServer->getUserUpdateLike($request->param('goods_id'),20);
                $ProductServerobj =  new ProductServer();
                $clickcount =   $ProductServerobj->getOne($goods_id);

                $GoodCollectServerobj = new    GoodCollectServer();
                $likecount = $GoodCollectServerobj->getOne($goods_id);


                Response::create(['data' =>$data,'click_count'=>current($clickcount),'likecount'=>$likecount, 'code' => '2000', 'message' => '喜欢'.$request->param('actionname').'成功'], 'json')->header($this->header)->send();
            }
            if($flag===false){
                Response::create(['data' =>['data'=>'eee'], 'code' => '4000', 'message' => '已经喜欢'], 'json')->header($this->header)->send();
            }
        }
        Response::create([ 'code' => '4001', 'message' => '更新'.$request->param('actionname').'失败'], 'json')->header($this->header)->send();
    }





    //意向用户查询
    public function IntendedUser(Request $request)
    {
        $mobile= $request->param('mobile');
        $nickname =$request->param('nickname');
        $data =  Db::name('users')->alias('a')->join('goods g','g.user_id=a.user_id');
        if($mobile!=''){
            $data =  $data->where('a.mobile','like','%'.$mobile.'%');
        }
        if($nickname!=''){
            $data =  $data->whereOr('a.nickname','like','%'.$nickname.'%');
        }
        $data =$data->field('a.user_id,a.mobile,a.nickname,a.head_pic,g.goods_id')->whereNull('g.delete_time')->where('g.endTime','>',$_SERVER['REQUEST_TIME'])->limit(20)->select();

//        echo "<pre>";
//        print_r($data);
        if(!empty($data)){
            foreach ($data as $v){  // http://wap.yipinfanghua.com/#/user/seller_shop/7992
                echo "用户id ". $v['user_id']."<a target=\"_blank\" href=\"http://wap.yipinfanghua.com/#/user/seller_shop/".$v['user_id']."\">点击查看用户个人官网</a>". '<br>';
                echo "用户手机号码 ".$v['mobile'].'<br>';
                echo "用户微信昵称  ". $v['nickname'].'<br>';
                echo "用户头像<a target=\"_blank\" href=\"".$v['head_pic']."\">点击查看</a>". '<br>';
                echo "作品id ". $v['goods_id'].'<br>';
                echo "--------------------------------------------------------------------------------------------------------------------<br>";
            }
        }
    }

}
