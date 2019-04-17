<?php

namespace app\dataapi\controller;

use app\dataapi\server\GoodCollectServer;
use app\dataapi\server\ProductServer;
use app\dataapi\server\UserServer;
use app\Home\Logic\UsersLogic;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Controller;
use think\Hook;
use think\Request;
use think\Response;
use app\dataapi\server\Weixin;
use BaconQrCode\Renderer\Color\Rgb;
use app\dataapi\model\Goods;
use app\dataapi\server\BidOrderServer;
use app\dataapi\model\BidOrder;
use app\dataapi\server\AliyunOss;
use think\Db;

define("TOKEN", "jknsadjknasdjkasjkas88");

class UserAddress extends Controller
{
    private $user_data;
    private $header;

    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        $this->header = ['Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept', 'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST,DELETE,OPTIONS'];
    }


    /*
     * 用户地址列表
     */
    public function address_list()
    {
        $userid = $this->user_data['user_id'];
        $address_lists = get_user_address_list(20);
        Response::create(['data' => $address_lists, 'code' => '2000', 'message' => '用户信息地址信息列表获取成功'], 'json')->header($this->header)->send();
    }

    /*
     * 添加地址
     */
    public function add_address(Request $request)
    {
        if ($request->isPost()) {
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_data['user_id'], 0, $request->getInput());
            Response::create(['data' => $data, 'code' => '2000', 'message' => '用户信息地址信息列表获取成功'], 'json')->header($this->header)->send();
        }
        Response::create([ 'code' => '2004', 'message' => '参数传递错误'], 'json')->header($this->header)->send();
    }

    /*
     * 地址编辑
     */
    public function edit_address(Request $request)
    {
        $id = $request->param('id');
        $address = M('user_address')->where(array('address_id' => $id, 'user_id' => $this->user_data['user_id']))->find();
        if (IS_POST) {
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_data['user_id'], $id,$request->getInput());
            Response::create(['data' => $data, 'code' => '2000', 'message' => '用户信息地址添加成功'], 'json')->header($this->header)->send();
        }
        Response::create([ 'code' => '2004', 'message' => '参数传递错误'], 'json')->header($this->header)->send();
    }

    /*
     * 设置默认收货地址
     */
    public function set_default(Request $request)
    {
        $id = $request->param('id');
        M('user_address')->where(array('user_id' => $this->user_data['user_id']))->save(array('is_default' => 0));
        $row = M('user_address')->where(array('user_id' =>$this->user_data['user_id'], 'address_id' => $id))->save(array('is_default' => 1));
        Response::create(['data' => $row, 'code' => '2000', 'message' => '用户信息地址设置成功'], 'json')->header($this->header)->send();
    }

    /*
     * 地址删除
     */
    public function del_address(Request $request)
    {
        $id = $request->param('id');
        $address = M('user_address')->where("address_id = $id")->find();
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->delete();
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M('user_address')->where("user_id = {$this->user_id}")->order('address_id desc')->find();
            $address2 && M('user_address')->where("address_id = {$address2['address_id']}")->save(array('is_default' => 1));
        }
        if (!$row)
            Response::create(['code' => '2004', 'message' => '用户信息地址删除失败'], 'json')->header($this->header)->send();
        else
            Response::create(['data' => $row, 'code' => '2000', 'message' => '用户信息地址删除成功'], 'json')->header($this->header)->send();
    }

}
