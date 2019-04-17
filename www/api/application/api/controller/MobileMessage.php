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
use think\Log;
use app\dataapi\controller\JobQeue;
use think\Hook;
use think\Cache;
use app\dataapi\server;
class MobileMessage    extends BaseApi
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
        $mobile =$request->param('mobile','');
        //检测手机号码
       if(!preg_match('#^1[\d]{10}$#', $mobile)){
           Response::create([ 'code' => '4101', 'message' => '手机号码格式不对 '], 'json')->header($this->header)->send();
       }
        $ud = new   UserServer();
        if(!$ud->userGetVerfityInfoCheckMobilewidthuserid($mobile,$userid)){
            Response::create([ 'code' => '4101', 'message' => '已经被占用 '], 'json')->header($this->header)->send();
        }

        $smsSign ='艺品芳华';
        $templateCode='SMS_140770071';
        $code=mt_rand(1000,9999)  ;
        if(isset($this->result['token']) && $this->result['token']!=''){
            $flag =realApi_10SendSMS($mobile, $smsSign, $templateCode,$code);
            if($flag['status']===1){
                Cache::store('file')->set(md5($this->result['token']),$code.'_'.$mobile,180);
                Response::create([ 'code' => '2000', 'message' => '短信发送成功'], 'json')->header($this->header)->send();
            }
                Response::create([ 'code' => '4101', 'message' => $flag['msg']], 'json')->header($this->header)->send();
        }else{
            Response::create([ 'code' => '4101', 'message' => '短信发送失败 '], 'json')->header($this->header)->send();
        }
    }

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function checkmobilecode(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $mobile =$request->param('mobile');
        $mobilecode =$request->param('mobilecode');

        //检测手机号码
        if(!preg_match('#^1[\d]{10}$#', $mobile)){
            Response::create([ 'code' => '4101', 'message' => '手机号码格式不对 '], 'json')->header($this->header)->send();
        }
        if(strlen($mobilecode)!=4){
            Response::create([ 'code' => '4101', 'message' => '验证码不正确 '], 'json')->header($this->header)->send();
        }

        if(isset($this->result['token']) && $this->result['token']!=''){
            $code =  Cache::store('file')->get(md5($this->result['token']));
            if(!empty($code) && $mobilecode.'_'.$mobile==$code){
                Cache::rm(md5($this->result['token']));
                //更新用户
                //验证手机号码是否已经验证了
                $ud = new   UserServer();
                if(1){
                    Cache::rm(md5($this->result['token']));
                    //更新用户
                    $Users = new  UserServer();
                    if(!$ud->userGetVerfityInfoCheckMobile($mobile)){
                        //存在号码了 进行账号修复
                        //更换openid
                        if(isset($this->user_data['openid']) && strlen($this->user_data['openid'])>0){
                            //获取老的userid
                            $Usersobj = new  Users();
                            $olduser_id =$Usersobj->where('mobile',$mobile)->order('user_id asc')->value('user_id');
                            if($olduser_id>0){
                                $usinfodata = $Users->getThirdUserinfoByUserid($olduser_id);
                                $Users->updateUserinfoOfThird($olduser_id,['openid'=>$this->user_data['openid']]);
                            }

                            //记录更换历史
                            $dataOpenidhistory = [
                                'user_id' => $olduser_id,
                                'openid' => $usinfodata['openid']??'',
                                'datetime' => time()
                            ];
                            $openidhistoryobj = new  \app\api\model\Openidhistory();
                            $openidhistoryobj->insert($dataOpenidhistory);
                        }

                        $Users->updateUserinfo($userid,['mobile_validated'=>1,'mobile'=>(string)$mobile]);
                        Response::create([ 'code' => '20012', 'message' => '重新登录去修复'], 'json')->header($this->header)->send();
                    }

                    if($Users->updateUserinfo($userid,['mobile_validated'=>1,'mobile'=>(string)$mobile])===true){
                        Response::create([ 'code' => '2000', 'message' => '短信验证成功'], 'json')->header($this->header)->send();
                    }
                    Response::create([ 'code' => '4101', 'message' => '已经被占用 '], 'json')->header($this->header)->send();
                }




                $Users = new  UserServer();
              if($Users->updateUserinfo($userid,['mobile_validated'=>1,'mobile'=>(string)$mobile])===true){
                  Response::create([ 'code' => '2000', 'message' => '短信验证成功'], 'json')->header($this->header)->send();
              }
            }else{

                Response::create([ 'code' => '4101', 'message' => '短信验证失败'], 'json')->header($this->header)->send();
            }
        }else{
            Response::create([ 'code' => '4102', 'message' => '短信验证失败 '], 'json')->header($this->header)->send();
        }
    }
}
