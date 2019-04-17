<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
use think\Hook;
use think\Cache;
use app\dataapi\server\Weixin;
use app\dataapi\model\ThirdUsers;
use app\dataapi\model\Users;
class UpdateWeixin    extends BaseApi
{
    private $user_data;
    protected $header;

    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        $this->header = ['Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept', 'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST,DELETE,OPTIONS'];
    }
    public function index(Request $request){
        //获取用户信息 里面的opensi
        $userid = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_infobyfield($userid,'openid,user_id'); // 获取用户信息
        $jsapiobj = new  Weixin();
        $cacheobj= Cache::store('filetoken');

        $token = $jsapiobj->getAccessToeknCode($cacheobj);
        $userinfoobj = $jsapiobj->getUserinfo($token, $user_info['openid']);
        $userinfoobj = stripslashes($userinfoobj);
        $userinfoobj = json_decode($userinfoobj, true);

        if (!isset($userinfoobj['errmsg']) && (isset($userinfoobj['subscribe']) && 1 == $userinfoobj['subscribe'])) {
            // 启动事务
            $Users = new  ThirdUsers();
            $Users_l = new  Users();
            $userinfoobj['nickname'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $userinfoobj['nickname']);
            Db::startTrans();
            try {
                $data = [
                    'head_pic' => $userinfoobj['headimgurl'],
                    'nickname' => $userinfoobj['nickname'],
                    'lastsynctime' => time()
                ];
                //更新用户头像和姓名
                $Users->where(['user_id' => $userid, 'openid' => $user_info['openid']])->update($data);
                unset($data['lastsynctime']);
                $Users_l->where('user_id', $user_info['user_id'])->update($data);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {

                echo $e->getMessage();
                // 回滚事务
                Db::rollback();
                Response::create(['code'=>2000,'message'=>'更新用户微信头像失败'], 'json')->header($this->header)->send();
            }
            Response::create(['code'=>2000,'message'=>'更新用户微信头像成功'], 'json')->header($this->header)->send();
        }
        unset($userinfoobj);
        Response::create(['code'=>2000,'message'=>'更新用户微信头像失败'], 'json')->header($this->header)->send();
    }

}
