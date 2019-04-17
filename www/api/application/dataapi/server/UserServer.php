<?php
namespace app\dataapi\server;

use app\dataapi\model\FansCollect;
use app\dataapi\model\GoodsShouc;
use app\dataapi\model\GoodsCollect;
use app\dataapi\model\ThirdUsers;
use app\dataapi\model\UserBond;
use app\dataapi\model\Goods;
use app\dataapi\model\Users;
use app\dataapi\model\UserVerifty;
use Defuse\Crypto\KeyProtectedByPassword;
use think\Db;
use think\Request;
use think\Config;
use think\exception\PDOException;
use app\dataapi\lib\Image;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class UserServer
{

    public static function getUserinfostatic($userid,$filed='user_id')
    {
        $Users = new  Users();
        if(empty($filed)){
            $filed = $Users->getPk();
        }
        $userdata = $Users::get(['user_id' => $userid])->value($filed);
        return $userdata;
    }


    public function getUserinfo($userid)
    {
        $Users = new  Users();
        $userdata = $Users::get(['user_id' => $userid]);
        return $userdata;
    }

    public function getThirdUserinfo($openid)
    {
        $ThirdUsers = new  ThirdUsers();
        $useronedata = $ThirdUsers::get(['openid' => $openid]);

        return $useronedata;
    }

    public static  function getThirdUserinfoByUserids(int $userid,$field='*')
    {
        $ThirdUsers = new  ThirdUsers();
        $useronedata = $ThirdUsers->where(['user_id' => $userid])->field($field)->find();
        return $useronedata;
    }

    public function getThirdUserinfoByUserid(int $userid)
    {
        $ThirdUsers = new  ThirdUsers();
        $useronedata = $ThirdUsers::get(['user_id' => $userid]);
        return $useronedata;
    }

    public function vhostaddThirdUserinfo($openid, Request $request, array $userinfo, int $type = 1)
    {


        $Users = new  Users();


        $dddflag = $Users->where('nickname', trim($userinfo['nickname']))->whereNull('delete_time')->find();

        if (!empty($dddflag)) {

            return -1;

        }

        $dddflag = $Users->where('head_pic', trim($userinfo['headimgurl']))->find();

        if (!empty($dddflag)) {

            return -2;

        }
        //随机生成密码
        $keye = uniqid();
        $user_name = uniqid();


        try{
            $Users->data([
                'user_name' => $user_name,//随机
                // 'password'=>$this->CreateUserAccount($user_name,$keye),
                'password' => md5('dddddddd'),
                'sex' => '',
                'reg_time' => $_SERVER['REQUEST_TIME'],
                'last_ip' => $request->ip(),
                'user_level' => mt_rand(1,5),
                'head_pic' => $userinfo['headimgurl'],
                'nickname' => $userinfo['nickname'],
                'hash_code'=> $userinfo['hash_code'],
                'image_url_remote_expire' => $userinfo['image_url_remote_expire'],
                'fictitious' => 1,
                'is_authentication' => 1
            ]);
            $Users->save();
        }catch (PDOException $e){
            return -3;
        }
        $ThirdUsers = new  ThirdUsers();
        $ThirdUsers->data([
            'oauth' => $type,
            'user_id' => $Users->user_id,
            'openid' => $keye,
            'head_pic' => $userinfo['headimgurl'],
            'country' => $userinfo['country'],
            'subscribe' => $userinfo['subscribe'],
            'sex' => mt_rand(1, 2),
            'nickname' => $userinfo['nickname'],
            'province' => $userinfo['province'],
            'city' => $userinfo['city'],
        ]);
        $ThirdUsers->save();


        return $Users::where(['user_id' => $Users->user_id])->find();


    }


    public function addThirdUserinfo($openid, Request $request, array $userinfo, int $type = 1)
    {


        $Users = new  Users();
        //随机生成密码
        $keye = uniqid();
        $user_name = uniqid();
        $userinfo['nickname'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $userinfo['nickname']);
        $userinfo['nickname'] = filter_Emoji($userinfo['nickname']);

        $Users->data([
            'user_name' => $user_name,//随机
            // 'password'=>$this->CreateUserAccount($user_name,$keye),
            'password' => md5('dddddddd'),
            'sex' => $userinfo['sex'],
            'reg_time' => $_SERVER['REQUEST_TIME'],
            'last_ip' => $request->ip(),
            'head_pic' => $userinfo['headimgurl'],
            'nickname' => $userinfo['nickname']
        ]);
         $Users->save();

         $ThirdUsers = new  ThirdUsers();
        $ThirdUsers->data([
            'oauth' => $type,
            'user_id' => $Users->user_id,
            'openid' => $openid,
            'head_pic' => $userinfo['headimgurl'],
            'country' => $userinfo['country'],
            'subscribe' => $userinfo['subscribe']??0,
            'sex' => $userinfo['sex'],
            'nickname' => $userinfo['nickname'],
            'province' => $userinfo['province'],
            'city' => $userinfo['city'],
        ]);
        $res = $ThirdUsers->save();

        return $Users::where(['user_id' => $Users->user_id])->find();


    }


    public function CreateUserAccount($username, $password)
    {
        // ... other user account creation stuff, including password hashing

        $protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($password);


        $protected_key_encoded = $protected_key->saveToAsciiSafeString();

        var_dump($protected_key_encoded);
        exit;
        return $protected_key_encoded;
    }

    public function updateUserinfo(int $userid, array $o): bool
    {
        $Users = new  Users();
        if ($Users->allowField(true)->save($o, ['user_id' => $userid]) > 0) {
            return true;
        }
        return false;
    }


    public function updateUserinfoOfThird(int $userid, array $o): bool
    {
        $Users = new  ThirdUsers();
        if ($Users->allowField(true)->save($o, ['user_id' => $userid]) > 0) {
            return true;
        }
        return false;
    }

    public function UpdateUserinfoP(int $userid, $post)
    {
        $Users = new  Users();
        if ($userid <= 0) {
            return false;
        }
        // 过滤post数组中的非数据表字段数据
        return $Users->allowField(true)->save($post, ['user_id' => $userid]);
    }


    public function thirdUpdateUserinfo(int $userid, array $o): bool
    {
        $Users = new  ThirdUsers();
        if ($Users->allowField(true)->save($o, ['user_id' => $userid]) > 0) {
            return true;
        }
        return false;
    }


    /*
     *
     * 用户认证
     */
    public function userVerifty(int $userid, array $o): bool
    {
        $Users = new  Users();

        $UserVerifty = new UserVerifty();


        if ($Users->allowField(true)->save($o, ['user_id' => $userid]) > 0) {
            return true;
        }
        return false;
    }


    /*
     *
     * 用户认证
     */
    public function userGetVerfityInfo(int $userid, array $o)
    {

        $UserVerifty = new UserVerifty();

        $data = $UserVerifty->where('user_id', $userid)->find();
        return $data;

    }

    /*
     *
     * 用户认证 检查手机号码
     */
    public function userGetVerfityInfoCheckMobile($mobile)
    {
        $UserVerifty = Db::name('users');
        $data = $UserVerifty->where('mobile', $mobile)->where('mobile_validated',1)->field('mobile_validated,mobile')->find();
        if(!isset($data['mobile'])){
            return true;
        }
        return false;
    }

    /*
*
* 用户认证 检查手机号码
*/
    public function userGetVerfityInfoCheckMobilewidthuserid($mobile,$user_id)
    {
        $UserVerifty = Db::name('users');
        $data = $UserVerifty->where('user_id',$user_id)->where('mobile', $mobile)->where('mobile_validated', 1)->field('mobile_validated,mobile')->find();
        if (!isset($data['mobile'])) {
            return true;
        }
        return false;
    }

    /*
     *
     * 用户认证
     */
    public function userUpdateLike(int $userid, array $o,int $istomutl=0): bool
    {
        $GoodsCollect = new GoodsCollect();
        $data = $GoodsCollect->where(['user_id' => $userid, 'goods_id' => $o['goods_id']])->count();
        if (0==$data) {
            $tmpdatauseridarray = Db::name('goods')->where('goods_id', $o['goods_id'])->field('user_id')->find();
            $goods_collect = 0;
            Db::transaction(function () use ($userid, $o, &$goods_collect, $tmpdatauseridarray,$istomutl) {
                $tmpdatauserid = (int)$tmpdatauseridarray['user_id'];
                if ($tmpdatauserid > 0) {
                    $data = [
                        'user_id' => $userid,
                        'goods_id' => $o['goods_id'],
                        'user_gooods_id' => $tmpdatauserid,
                        'add_time' => time()
                    ];
                } else {
                    return false;
                }
                $goods_collect =  \app\dataapi\server\GoodCollectServer::insetintogoolect($data);
                if($istomutl===1){
                    $fff = 1;
                }else{
                    $fff = mt_rand(4, 6);
                }
                if(Db::name('goods')->where(['goods_id' => $o['goods_id']])->value('click_count') <= 2000){   ////浏览量限制2000以下
                    $goods = Db::name('goods')->where(['goods_id' => $o['goods_id']])->update(['click_count' => ['exp', 'click_count+'.$fff]]);
                }
                return true;
            });
            if ($goods_collect > 0) {
                return true;
            }
        }
        return false;
    }
    /*
     *
     * 虚拟点赞专用用户认证
     */
    public function vhostuserUpdateLike(int $userid, array $o): bool
    {
        $goodscollectobj = new GoodsCollect();
        if(Db::name('goods')->where(['goods_id' => $o['goods_id']])->value('click_count') >= 2000){
            return false;
        }
        $data =0;
        //获取喜欢的数量 超过30的就自动减少50%
        if ($data==0) {
            $tmpdatauseridarray = Db::name('goods')->where('goods_id', $o['goods_id'])->field(['user_id','endTime'])->find();
            if(isset($tmpdatauseridarray['endTime']) && $tmpdatauseridarray['endTime']<=time()){
                return false;
            }

            $tmpdatauserid = $tmpdatauseridarray['user_id'];
            if ($tmpdatauserid > 0) {
                $data = [
                    'user_id' => $userid,
                    'goods_id' => $o['goods_id'],
                    'user_gooods_id' => $tmpdatauserid,
                    'add_time' => time()
                ];
            } else {
                return false;
            }

            $GoodsCollectid = $goodscollectobj->insertGetId($data);
           if(!$GoodsCollectid){
               throw   new \Exception("insert good_collect is not able");
           }
            $fff = mt_rand(8, 14);
            Db::name('goods')->where(['goods_id' => $o['goods_id']])->update(['click_count' => ['exp', 'click_count+' . $fff]]);

        }
        return true;
    }


    /*
     *
     * 用户认证
     */
    public function getUserUpdateLike(int $good_id, int $limit)
    {
        $goodsobj = new Goods();
        $user_iddata = $goodsobj->where(['goods_id' => $good_id])->field('user_id')->value('user_id');
        if ($user_iddata>0) {
            $data =  getusercollectlists($good_id);
            return $data;
        } else {
            return null;
        }
    }

//查询用户关注情况

    public  function queryUserFocus(int $userid, array $o){
        $FansCollect = new FansCollect();
        $data = $FansCollect->where(['user_id' => $o['u_id'], 'fans_id' => $userid])->field('collect_id')->find();
        if(!empty($data)){
            return true;
        }
        return false;
    }
    /*
     *
     * 用户关注
     */
    public function userFoucs(int $userid, array $o): bool
    {
        $FansCollect = new FansCollect();
        $data = $FansCollect->where(['user_id' => $o['u_id'], 'fans_id' => $userid])->find();
        if (empty($data)) {
            $FansCollect->data([
                'user_id' => $o['u_id'],
                'fans_id' => $userid,
                'add_time' => time()]);
            $FansCollect->save();
            if ($FansCollect->collect_id > 0) {
                return $FansCollect->collect_id ? true : false;
            }
        }
        return false;
    }


    /*
     *
     * 用户关注
     */
    public function userFoucsFansan(int $userid, array $o): bool
    {
        $FansCollect = new FansCollect();
        $data = $FansCollect->where(['user_id' =>$userid , 'fans_id' =>$o['u_id'] ])->find();
        if (empty($data)) {
            $FansCollect->data([
                'user_id' =>$userid,
                'fans_id' => $o['u_id'],
                'add_time' => time()]);
            $FansCollect->save();
            if ($FansCollect->collect_id > 0) {
                return $FansCollect->collect_id ? true : false;
            }
        }
        return false;
    }

    /*
     *
     * 用户关注取消关注
     */
    public function unuserFoucs(int $userid, array $o): bool
    {
        $FansCollect = new FansCollect();
        if ($o['u_id'] == $userid) {
            return false;
        }
        $data = $FansCollect->where(['user_id' => $o['u_id'], 'fans_id' => $userid])->find();
        if (empty($data)) {
            return false;
        } else {
            $data->delete();
            return true;
        }
        return false;
    }

    /*
     *
     * 用户关注
     */
    public function bondCheck(int $userid): bool
    {
        $UserBond = new UserBond();
        $data = $UserBond->where(['user_id' => $userid])->find();
        if (!empty($data)) {
            if ($data['bond'] == Config::get('user_bond.money')) {
                return true;
            }
        }
        return false;
    }


    //更新到虚拟用户表
    public function copytpuserstotpUsers(int $user_id)
    {
        $imageobj = new Image();
        $hot_users = Db::name('users')
            ->where('fictitious', 1)
            ->where('user_id',$user_id)
            ->field('user_id,head_pic')
            ->select();
        $data = [];
        foreach ($hot_users as $v) {
            if (!$v['user_id'] || empty($v['head_pic'])) {
                continue;
            }
            $flag = $imageobj->imagetest($v['head_pic']);
            if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                continue;
            }
            //判断是否存在在表里
            $data[] = ['user_id' => $v['user_id'], 'head_pic' => $v['head_pic']];
        }
        if (isset($data) && !empty($data)) {
            $table_name =   Config::get('updateFictitiousGenerationserver');
            Db::name($table_name)->insertAll($data);
        }
        return;
    }

    //用户登录首次赠送

    public  function updatefirstenterfans(int $userid,int $status){
        $data =  Db::name('users')->where(['user_id' => $userid])->update(['fisrtentergiftfans'=>$status]);
        if($data!==false){
            return true;
        }
        return false;
    }



    //获取当前用户等级信息
    public function usermsg($user_id){
        $user_data=model('users')
            ->alias('U')
            ->join('__STORE_LEVEL__ S','U.store_level=S.store_level_id','LEFT')
            ->where(['U.user_id'=>$user_id])
            ->field('U.user_id,U.nickname,U.head_pic,U.user_level,U.is_authentication,U.is_goodstore,S.*')
            ->find();//个人数据
        return $user_data;
    }



    /*
     * 用户是否关注
     * $user_id  用户,$fans_id  粉丝
     */
    public function is_get_care($user_id,$fans_id){
         if($user_id==0 || $fans_id==0){
             $message = [
                 'code' =>1,
                 'msg' =>'已关注'
             ];
                return $message;
         }

        if($user_id == $fans_id){
            $message = [
                'code' =>2,
                'msg' =>'发布人为自己，无需点击关注(隐藏)'
            ];
            return $message;
        }
        $re = Db::name('fans_collect')->where(array('user_id'=>$user_id,'fans_id'=>$fans_id))->value('collect_id');
        if($re>0){
            $message = [
                'code' =>1,
                'msg' =>'已关注'
            ];
        }else{
            $message = [
                'code' =>0,
                'msg' =>'未关注'
            ];
        }
        return $message;
    }

}