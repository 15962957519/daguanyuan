<?php
namespace app\dataapi\controller;
use think\Db;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
use app\dataapi\model\LikegoodMessageList;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use  app\dataapi\server\UserFanServer;
use app\dataapi\server\UserBidServer;
use think\exception\PDOException;
ini_set('max_execution_time', '50');//

class Consumer    extends BaseApi
{


    //通用消费
    public function commonconsumer(Request $request){

        $type= (int)$request->param('type');

        if($type ==1){
         return    $this->consumerlike($request);
        }

        if($type ==2){
            return    $this->consumerbid($request);
        }

        if($type ==3){
            return    $this->consumerfanserver($request);
        }

        Response::create(['status' => '200', 'code' => '1', 'message' => ''], 'json')->header($this->header)->send();
    }






    //消费喜欢
    public  function consumerlike(Request $request)
    {
        $goods_id = (int)$request->param('goods_id');
        if($goods_id<=0){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'','message' => 'html goods_id is zero'], 'json')->header($this->header)->send();
        }
        $id =  (int)$request->param('id');
        if($id<=0){
            Response::create(['status' => '400', 'code' => '1', 'error'=>'', 'message' => 'html id is zero'], 'json')->header($this->header)->send();
        }
        $tz=  (int)$request->param('ts');
        $randlike = mt_rand(1, 3);
        $image = new Image();
        $UserServer = new UserServer();
        $tmpdatauseridarray = Db::name('goods')->where('goods_id', $goods_id)->where('goods_status',1)->field(['user_id','endTime'])->find();

        if(isset($tmpdatauseridarray['endTime']) && $tmpdatauseridarray['endTime']<=time()){
            //更新数据库
            $LikegoodMessageList = new LikegoodMessageList();
            $LikegoodMessageList->where('id', $id)->delete(true);
            Response::create(['status' => '200', 'code' => '1', 'message' => 'goods_id is exprie'], 'json')->header($this->header)->send();
        }
      //  do {
         //   $randlike--;
            $ddd = $image->flagimage();
            $userid = $ddd['user_id'];
            if ($userid > 0 && $goods_id > 0) {
                try{
                    $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
                }catch (PDOException $e){
                    Response::create(['status' => '400', 'code' => '1',  'error'=>'','message' => 'html'], 'json')->header($this->header)->send();
                }catch (\Exception $e){
                    Response::create(['status' => '400', 'code' => '1', 'error'=>'', 'message' => 'html'], 'json')->header($this->header)->send();
                }
            }
      //  } while ($randlike > 0);
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


}
