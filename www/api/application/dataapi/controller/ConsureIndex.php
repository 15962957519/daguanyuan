<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\dataapi\server\GoodMessageQuene;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
use app\dataapi\model\LikegoodMessageList;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use  app\dataapi\server\UserFanServer;
use app\dataapi\server\UserBidServer;
use think\exception\PDOException;
use think\Config;
use app\dataapi\server\Jssdk;
ini_set('max_execution_time', '50');//

class ConsureIndex    extends BaseApi
{
    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {
            $goods_id = (int)$request->get('goods_id');
            $id =  (int)$request->get('id');
            $tz=  (int)$request->get('ts');


            if(0===$goods_id){
                return ['code'=>0,'message'=>$_GET];
            }
            if(0===$id){
                return ['code'=>0,'message'=>'用户id参数错误'];
            }

            $randlike = mt_rand(1, 3);
            $image = new Image();
            $UserServer = new UserServer();
            $tmpdatauseridarray = Db::name('goods')->where('goods_id', $goods_id)->where('goods_status',1)->field(['user_id','endTime'])->find();
            if(isset($tmpdatauseridarray['endTime']) && $tmpdatauseridarray['endTime']<=time()){
                return ['code'=>1,'message'=>'已经下架'];
            }
            do {
                $randlike--;
                $ddd = $image->flagimage();
                $userid = $ddd['user_id'];
                echo "is like {$goods_id}", PHP_EOL;
                if ($userid > 0 && $goods_id > 0) {
                    $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
                }
            } while ($randlike > 0);
            //更新数据库
            $LikegoodMessageList = new LikegoodMessageList();
            $LikegoodMessageList->where('id', $id)->delete(true);
            unset($LikegoodMessageList,$UserServer,$image);
        return ['code'=>1,'message'=>'获取成功'];
    }


    //消费喜欢
    public  function consumerlike(Request $request)
    {
        $goods_id = (int)$request->param('goods_id');
        if($goods_id<=0){
            Response::create(['status' => '400', 'code' => '1', 'message' => 'html goods_id is zero'], 'json')->header($this->header)->send();
        }
        $id =  (int)$request->param('id');
        if($id<=0){
            Response::create(['status' => '400', 'code' => '1', 'message' => 'html id is zero'], 'json')->header($this->header)->send();
        }
        $randlike = mt_rand(1, 3);
        $image = new Image();
        $UserServer = new UserServer();
        $tmpdatauseridarray = Db::name('goods')->where('goods_id', $goods_id)->where('goods_status',1)->field(['user_id','endTime'])->find();
        if(isset($tmpdatauseridarray['endTime']) && $tmpdatauseridarray['endTime']<=time()){
            Response::create(['status' => '200', 'code' => '1', 'message' => 'goods_id is exprie'], 'json')->header($this->header)->send();
        }


            $ddd = $image->flagimage();
            if(isset($ddd['user_id']) && $ddd['user_id']>0){
                $userid = $ddd['user_id'];
                if ($userid > 0 && $goods_id > 0) {
                    try{
                        $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
                    }catch (PDOException $e){
                        echo $e->getMessage();
                        Response::create(['status' => '400', 'code' => '1', 'message' => 'html'], 'json')->header($this->header)->send();
                    }catch (\Exception $e){
                        Response::create(['status' => '400', 'code' => '1', 'message' => 'html'], 'json')->header($this->header)->send();
                    }
                }
            }else{
                Response::create(['status' => '400', 'code' => '1', 'message' => 'html '], 'json')->header($this->header)->send();
            }
        //更新数据库
        $LikegoodMessageList = new LikegoodMessageList();
        $LikegoodMessageList->where('id', $id)->delete(true);
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }




    /*
     *@//自动粉丝
     *
     */
    public function consumerfanserver(Request $request)
    {
        //{"ts":1510133071,"bizId":"5a02cd4f6529d","id":0,"user_id":125062,"is_or_member":false,"collectgoodmessagelist_insertid":"506067","tz":1510201728}
        $user_id = (int)$request->param('user_id');
        $inseridid = (int)$request->param('collectgoodmessagelist_insertid');
        if($user_id<=0){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'1000','message' => 'html  user_id is zero'], 'json')->header($this->header)->send();
        }
        if($inseridid<=0){
            Response::create(['status' => '400', 'code' => '1',  'error'=>'1000','message' => 'html collectgoodmessagelist_insertid is zero'], 'json')->header($this->header)->send();
        }
        try{
           $flag =  UserFanServer::autoGiveFanUser($user_id, $inseridid);
           if(!$flag){
               Response::create(['status' => '400', 'code' => '1', 'error'=>'1000', 'message' => 'html'], 'json')->header($this->header)->send();
           }
        }catch (\Exception $e){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'1000', 'message' => 'html'], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }

    /*
      *@//自动出价
      *
      */
    public function consumerbid(Request $request)
    {
        //{"ts":1510050495,"bizId":"59ff34fa68d4b","id":2220,"goods_id":512659,"is_or_member":false}
        $goods_id= (int)$request->param('goods_id');
        $id = (int)$request->param('id');
        if($goods_id<=0){
            Response::create(['status' => '200', 'code' => '1','error'=>'1000', 'message' => 'html goods_id is zero'], 'json')->header($this->header)->send();
        }
        if($id<=0){
            Response::create(['status' => '200', 'code' => '1', 'error'=>'1000','message' => 'collectgoodmessagelist_insertid is zero'], 'json')->header($this->header)->send();
        }
        try{
            $flag =   UserBidServer::indexbid(['goods_id'=>$goods_id,'id'=>$id]);
            if(!$flag){
                Response::create(['status' => '400', 'code' => '1', 'error'=>'1000','message' => 'html is error'], 'json')->header($this->header)->send();
            }
        }catch (\Exception $e){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'1000','message' => 'html is error'], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }



    /*
     *@//首次进入平台赠送粉丝
     *
     */
    public function consumerfirstenterplatorm(Request $request)
    {
        //{"ts":1510133071,"bizId":"5a02cd4f6529d","id":0,"user_id":125062,"is_or_member":false,"collectgoodmessagelist_insertid":"506067","tz":1510201728}
        $user_id = (int)$request->param('user_id');
        $inseridid = (int)$request->param('collectgoodmessagelist_insertid');
        if($user_id<=0){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'1000','message' => 'html  user_id is zero'], 'json')->header($this->header)->send();
        }
        try{
            $flag =  UserFanServer::autoGiveFanUser($user_id, $inseridid);
            if(!$flag){
                Response::create(['status' => '400', 'code' => '1', 'error'=>'1000', 'message' => 'html'], 'json')->header($this->header)->send();
            }
        }catch (\Exception $e){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'1000', 'message' => 'html'], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }




    //通用消费
    public function commonconsumer(Request $request){

        //收集入口文件
        $data =$request->param();
        $data = json_encode($data);

        $logconfig =[
            'type'  => 'File',
            'single' => true,
            'user' => 'apache',
            'group' => 'apache',
            'path'  => LOG_PATH.'four_seasonsnconsure.log'
        ];
        if(IS_WIN==true){
            $logconfig['path']= LOG_PATH.'four_seasonsnconsure.log';
            \think\Log::init($logconfig);
            \think\Log::write($data,'info');
        }else{
            \think\Log::init($logconfig);
            \think\Log::write($data,'info');
        }
        $type= $request->param('type');
        $type= trim($type);
        switch($type){
            case 'like':
               $t=  $this->consumerlike($request);
                if($t){
                    Response::create(['status' => '200', 'code' => '1', 'message' =>' '], 'json')->header($this->header)->send();
                }else{
                    Response::create(['status' => '200', 'code' => '1', 'message' => 'html is not process','error'=>''], 'json')->header($this->header)->send();
                }
                break;
            case 'consumerbid':
                return    $this->consumerbid($request);
            case 'fanserver':
                return    $this->consumerfanserver($request);
            case 'consumerfirstenterplatorm':
                return    $this->consumerfirstenterplatorm($request);
            case 'carefinishwarm':
                $t = GoodMessageQuene::careFinishWarm($request);
                if($t){
                    Response::create(['status' => '200', 'code' => '1', 'message' =>' '], 'json')->header($this->header)->send();
                }else{
                    Response::create(['status' => '200', 'code' => '1', 'message' => 'html is not process','error'=>''], 'json')->header($this->header)->send();
                }
            case 'modifytodaynogoods':
                $t = GoodMessageQuene::modifytodaynogoods($request);
                if($t){
                    Response::create(['status' => '200', 'code' => '1', 'message' =>' '], 'json')->header($this->header)->send();
                }else{
                    Response::create(['status' => '200', 'code' => '1', 'message' => 'html is not process','error'=>''], 'json')->header($this->header)->send();
                }
            case 'send2fansmessage':
                $t = GoodMessageQuene::send2fansmessage($request);
                if($t){
                    Response::create(['status' => '200', 'code' => '1', 'message' =>' '], 'json')->header($this->header)->send();
                }else{
                    Response::create(['status' => '200', 'code' => '1', 'message' => 'html is not process','error'=>''], 'json')->header($this->header)->send();
                }
            case 'ordercancel':
                $t = GoodMessageQuene::ordercancel($request);
                if($t){
                    Response::create(['status' => '200', 'code' => '1', 'message' =>' '], 'json')->header($this->header)->send();
                }else{
                    Response::create(['status' => '200', 'code' => '1', 'message' => 'html is not process','error'=>''], 'json')->header($this->header)->send();
                }
            case 'sendToCarers':
                if(GoodMessageQuene::carersMsg($request)){
                    Response::create(['status' => '200', 'code' => '1', 'message' =>' '], 'json')->header($this->header)->send();
                }

        }
        Response::create(['status' => '200', 'code' => '1', 'message' => 'html is not process','error'=>''], 'json')->header($this->header)->send();
    }



    public  function testsendmessageweixin(){

        $JobQeueobj = new  \app\dataapi\server\MqProducer();
        $jobData = ['type'=>'modifytodaynogoods','ts' => time(), 'bizId' => uniqid(), 'id' => 38, 'is_or_member' => false];
        if($JobQeueobj->process($jobData)){


        }
        exit;
        //发送给粉丝的信息
        $jobData = ['type'=>'send2fansmessage','ts' => time(), 'bizId' => uniqid(), 'data' => json_encode(['id'=>'oUimF05c7IA8u-FIuV7hqVGkbd9w','msg'=>"艺品芳华测试"]), 'is_or_member' => false];
        $JobQeueobj->process($jobData);
        exit;

    }


}
