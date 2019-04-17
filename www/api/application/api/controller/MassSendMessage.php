<?php
namespace app\api\controller;
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
use app\dataapi\model\FansCollect;
use think\Log;
use app\dataapi\controller\JobQeue;
use think\Hook;
use think\Cache;
use app\dataapi\server;
use app\Home\Logic\UsersLogic;
use app\dataapi\server\Jssdk;
use think\Config;
use Carbon\Carbon;
class MassSendMessage  extends BaseApi
{
    public $user_data;
    public $result;
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
    public function realSendWeixinSMS(Request $request){
        $userid = $this->user_data['user_id'];  //商品区发布者即当前登陆者
        $goods_id = $request->request('goods_id');   //商品区id
        $goods_msg = Db::name('goods')->where(['goods_id'=>$goods_id])->field('goods_name,endTime')->find();
        $goods_name = $goods_msg['goods_name'];   //商品区名称
        $endTime = date('Y-m-d H:i:s',$goods_msg['endTime']); //截止时间

        //查询当前店铺等级及群发次数
        $spendcount = Db::name('users')
            ->alias('U')
            ->join('__STORE_LEVEL__ S','U.store_level = S.store_level_id')
            ->where(['U.user_id'=>$userid])
            ->value('S.products');
        $today = Carbon::today();
        $unixttimetodaytime = strtotime($today);
        $carbonobj = new  Carbon();
        $datacc = Db::name('user_sendmessage')->where('user_id', $userid)->where('addtime', '>', $unixttimetodaytime)->lock(true)->value('sendactivemessagecount');
        $datacc = $datacc!==null?(int)$datacc:0;

        if($datacc>=$spendcount){
            Response::create([ 'code' => '2009', 'msg' =>'超出发送数量限制'], 'json')->header($this->header)->send();
        }
        if($goods_id == ''){
            Response::create([ 'code' => '2001', 'status'=>'2', 'msg' =>'未选择商品'], 'json')->header($this->header)->send();
        }

        if($userid>0){
            $userLogic = new UsersLogic();
            $user_info = $userLogic->getFancusAllAndRealy($userid,0,1,10); // 获取粉丝用户信息

            Db::startTrans();
            try{
                //self::send2fansmessage($user_info,['goods_name'=>$goods_name,'goods_id'=>$goods_id,'endTime'=>$endTime]);
                self::send2selfmessage($userid);
                $data = Db::name('user_sendmessage')->where('user_id', $userid)->where('addtime', '>', $unixttimetodaytime)->lock(true)->value('sendactivemessagecount');
                if (!empty($data)) {
                    //更新
                    Db::name('user_sendmessage')->where('user_id', $userid)->where('addtime', '>', $unixttimetodaytime)->update(['sendactivemessagecount' => ['exp', 'sendactivemessagecount+1']]);
                } else {
                    //新增加
                    $datainsert = ['user_id' => $userid, 'addtime' => time(), 'sendactivemessagecount' => 1];
                     Db::name('user_sendmessage')->insert($datainsert);
                }
                // 提交事务
                Db::commit();
                $message = ['code'=>'2000','status'=>'1','msg'=>'群发成功'];
            }catch(\Exception $e){
                dump($e->getMessage());
                Db::rollback();
                $message = ['code'=>'2001','status'=>'0','msg'=>'群发失败'];
            }

        }
        Response::create($message, 'json')->header($this->header)->send();
    }

    //粉丝发送消息

    public static function send2fansmessage(array $user_info,array $goods){

        $domain = Config::get('domain');

        //
        try{
            $JobQeueobj = new  \app\dataapi\server\MqProducer();
            $sumtime = time();
            foreach ($user_info as $k=>$v){
//                $urlto =$domain."index.html?#!dist/item?a={$goods['goods_id']}";
                $urlto =$domain."item/{$goods['goods_id']}.html";
                $content="{$goods['goods_name']}\n\n{$goods['goods_name']}作品\n截止时间：{$goods['endTime']}\n 点击链接出价：{$urlto}";
                //发送给粉丝的信息

               $sendcont = json_encode(['id'=>$v['openid'],'msg'=>$content]);
                $jobData = ['type'=>'send2fansmessage','ts' => $sumtime, 'bizId' => uniqid(), 'data' => $sendcont, 'is_or_member' => false];
                $JobQeueobj->process($jobData);
            }
        }catch(ErrorException $e){



        }


        return;
    }

    //给自己发消息
    public  static function send2selfmessage(int $userid){
        $template_id ='vtU8dZI9QyDwLDaBsXAgodoNddlTR4rMmx223wvN4eA';
        $datatime =(new \DateTime())->format("Y-m-d H:i:s");
        $userLogic = new UsersLogic();
        //给用户本人发送信息
        $res  =  $userLogic->get_info($userid);
        $rest  =  $userLogic->get_infoThird($userid);
        $nickname = $res['result']['nickname']??'';

        $FansCollectobj = new  FansCollect();
        $fans_count = $FansCollectobj->where('user_id',$userid)->whereNull('delete_time')->count(); //获取收藏数量粉丝

        $jssdkobj =   new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));
        $content=[
            'first'  =>  ["value"=>"尊敬的“{$nickname}”您好，您的商品已经群发成功",'color'=>"#173177"],
            'keyword1'  =>  ["value"=>"商品群发",'color'=>"#173177"],
            'keyword2'  =>  ["value"=>$datatime,'color'=>"#173177"],
            'remark'  => ["value"=>"已经有{$fans_count}粉丝收到了您的商品信息！",'color'=>"#173177"],
        ];
        $urlto =Config::get('domain').'all_send.html';
        $msg =   $jssdkobj->activePushMsg($rest['openid'],$template_id,$urlto,$content);
       return  ['code'=>2000,'status'=>true,'msg'=>'群发成功'];

    }


}
