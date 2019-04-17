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
use think\Log;
use app\dataapi\controller\JobQeue;
use think\Hook;
use think\Cache;
use app\dataapi\server;
use app\Home\Logic\UsersLogic;
use app\dataapi\server\Jssdk;
use think\Config;
use Carbon\Carbon;
class MassSendMessage    extends BaseApi
{
    private $user_data;
    private $result;
    public function _initialize()
    {
        $this->result = Hook::exec('app\\dataapi\\behavior\\Jwt','appEnd',$this->user_data);
        parent::_initialize();
    }
    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {
        $userid = $this->user_data['user_id'];
       // $productids =$request->param('productids')??'';
        $productids =   $_POST['productids']??[];
        if(empty($productids)){
            Response::create([ 'code' => '4101', 'message' => '群发消息发送失败-藏品没有选择 '], 'json')->header($this->header)->send();
        }
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($userid);
        if(isset($abcdata['result']['level'])) {
            $memberlevel = (int)$abcdata['result']['level'];
            $membersendactivecount = Hook::exec('app\\dataapi\\behavior\\MemberLimit','memberSendActiveCount',$memberlevel);
            if(!is_array($productids) ){
                $productids = explode(',',$productids);
            }
            $today = Carbon::today();
            $unixttimetodaytime = strtotime($today);
            $carbonobj = new  Carbon();
            $datacc = Db::name('user_sendmessage')->where('user_id', $userid)->where('addtime', '>', $unixttimetodaytime)->lock(true)->value('sendactivemessagecount');
            $datacc = $datacc!==null?(int)$datacc:0;
            if($membersendactivecount<count($productids) || (count($productids) >($membersendactivecount-$datacc))){
                Response::create([ 'code' => '2009', 'message' =>'超出发送数量限制'], 'json')->header($this->header)->send();
            }
            if(isset($this->result['token']) && $this->result['token']!=''){
              $flag =$this->realSendWeixinSMS($userid, $productids);
// 启动事务
                Db::startTrans();
                    try{
                        if($flag['status']===true) {
                            $currentstamptime = $carbonobj->getTimestamp();
                            $data = Db::name('user_sendmessage')->where('user_id', $userid)->where('addtime', '>', $unixttimetodaytime)->lock(true)->value('sendactivemessagecount');
                            if (!empty($data)) {
                                //更新
                                Db::name('user_sendmessage')->where('user_id', $userid)->where('addtime', '>', $unixttimetodaytime)->update(['sendactivemessagecount' => ['exp', 'sendactivemessagecount+1']]);
                            } else {
                                //新增加
                                $datainsert = ['user_id' => $userid, 'addtime' => time(), 'sendactivemessagecount' => count($productids)];
                                $data = Db::name('user_sendmessage')->insert($datainsert);
                            }
                            // 提交事务
                            Db::commit();
                            Response::create([ 'code' => '2000', 'message' =>$flag['msg']], 'json')->header($this->header)->send();
                        }

                    } catch (\Exception $e) {
                        // 回滚事务
                        echo $e->getMessage();
                        Db::rollback();
                    }
                }
            Response::create([ 'code' => '4101', 'message' => $flag['msg']], 'json')->header($this->header)->send();
        }else{
            Response::create([ 'code' => '4101', 'message' => '短信发送失败 '], 'json')->header($this->header)->send();
        }
    }


    //群发微信消息
    private function realSendWeixinSMS(int $userid,array $productids){
         //获取到关注的我人，即我的粉丝
        $data=[];
        $userLogic = new UsersLogic();




        $user_info = $userLogic->getFancusAllAndRealy($userid,0,1,10); // 获取用户信息


        //获取作品列表
        $ProductServer = new  ProductServer();
        $data = $ProductServer->getProductListsById($productids);


        $jssdkobj = new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));
        $template_id ='Jj-9ccS2t7V7BVG-3ru75qX-VpdscrQzmIWL8oYGhFY';
        $datatime =(new \DateTime())->format("Y-m-d H:i:s");
        try{
            foreach ($user_info as $k=>$v){
                foreach ($data as $sk=>$sv){
                    //http://w.tianbaoweipai.com/mymain?goods_id=72743&type=2
                    $urlto =Config::get('domain')."mymain?goods_id={$sv['goods_id']}&type=2";
                    //发送给粉丝的信息
                    $content="{$sv['goods_name']}\n\n{$sv['goods_name']}作品\n截拍时间：{$sv['endTime']}\n 点击链接出价：{$urlto}";
                    $jssdkobj->push_msg($v['openid'],$content);
                }
              break;
            }
            //给用户本人发送信息
            $res  =  $userLogic->get_info($userid);
            $rest  =  $userLogic->get_infoThird($userid);
            $nickname = $res['result']['nickname']??'';
            $fans_count = Db::name('fans_collect')->alias('a')->join('third_users w','a.fans_id=w.user_id')->where('a.user_id',$userid)->whereNull('w.delete_time')->whereNull('a.delete_time')->count(); //获取收藏数量    粉丝
            $content=[
                'first'  =>  ["value"=>"尊敬的“{$nickname}”您好，您的商品区已经群发成功",'color'=>"#173177"],
                'keyword1'  =>  ["value"=>"",'color'=>"#173177"],
                'keyword2'  =>  ["value"=>$datatime,'color'=>"#173177"],
                'remark'  => ["value"=>"已经有{$fans_count}粉丝收到了您的商品区信息！",'color'=>"#173177"],
            ];
            $urlto =Config::get('domain').'paipinmessage';
         $msg =   $jssdkobj->activePushMsg($rest['openid'],$template_id,$urlto,$content);

            return ['status'=>true,'msg'=>$msg];
        }catch(\Exception $e){
            return ['status'=>false,'msg'=>'短信发送失败'];
        }

      return ['status'=>true,'msg'=>''];
    }
}
