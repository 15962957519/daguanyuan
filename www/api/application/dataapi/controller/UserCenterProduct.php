<?php
namespace app\dataapi\controller;
use app\dataapi\server\ProductServer;
use think\Db;
use think\Hook;
use think\Request;
use think\Response;
use app\Home\Logic\UsersLogic;
class UserCenterProduct extends BaseApi
{

    private $user_data;

    public function _initialize()
    {

          $result = Hook::exec('app\\dataapi\\behavior\\Jwt','appEnd',$this->user_data);
        parent::_initialize();
    }

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {

            //获取分类信息
            $goods_category = Db::name('goods_category')->where(['is_show' => 1, 'parent_id' => 844])->field('id,name,mobile_name')->order('sort_order ASC')->cache(true, TPSHOP_CACHE_TIME)->select();

            //排序
            $ordermethods = array();
            $ordermethods[] = ['id' => 1, 'name' => '默认排序', 'content' => ''];
            $ordermethods[] = ['id' => 2, 'name' => '出价最高', 'content' => ''];
            $ordermethods[] = ['id' => 3, 'name' => '浏览人数最多', 'content' => ''];
            $ordermethods[] = ['id' => 4, 'name' => '收藏人数最多', 'content' => ''];
            $ordermethods[] = ['id' => 5, 'name' => '截拍时间', 'content' => ''];

            //状态
            $orderstatus[] = ['id' => 1, 'name' => '正在拍卖', 'content' => ''];
            $orderstatus[] = ['id' => 2, 'name' => '拍卖成功', 'content' => ''];
            $orderstatus[] = ['id' => 3, 'name' => '流拍', 'content' => ''];
             $user_id =  $this->user_data['user_id'];
            $ProductServer = new     ProductServer();
            $lists = $ProductServer->getProductsByUserid($user_id);
            $data = ['name' => $goods_category, 'ordermethods' => $ordermethods, 'orderstatus' => $orderstatus, 'lists' => $lists];
            Response::create(['data' => $data, 'code' => '2000', 'message' => '获取我的作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
           // Response::create(['code' => '4000', 'message' => '获取我的作品信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function indexsendmessage(Request $request)
    {
        $user_id =  $this->user_data['user_id'];
        $ProductServer = new  ProductServer();
        $lists = $ProductServer->getSendMessageProductsByUserid($user_id);
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($user_id);
        if(isset($abcdata['result']['level'])) {
            $memberlevel = (int)$abcdata['result']['level'];
            $membersendactivecount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'memberSendActiveCount', $memberlevel);
            //获取用户剩余发送数量
            $messagecount =  $userLogic->getCountSendMessage($user_id);
            $membersendactivecount=($membersendactivecount-$messagecount)>0?$membersendactivecount-$messagecount:0;
        $data = [ 'lists' => $lists,'userinfolevel'=>(string)$memberlevel,'membersendactivecount'=>(string)$membersendactivecount];
        Response::create(['data' => $data, 'code' => '2000', 'message' => '获取我的作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }else{

            Response::create(['data' => [], 'code' => '2008', 'message' => '获取我的作品信息失败'], 'json')->header($this->header)->send();
        }

     }

    /*
     * 删除作品
     *
     */
    public function delate(Request $request){
        $good_ids = $request->param('good_ids');
        $user_id =  $this->user_data['user_id'];
        $ProductServer = new     ProductServer();


        if(strpos(',',$good_ids)!=false){
            $good_ids=explode(',',$good_ids);
        }else{
            $good_ids=array($good_ids);
        }

        $user = $ProductServer->delate($good_ids,$user_id);
        if($user===1 ){
            Response::create(['code' => '2000', 'message' => '作品' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();

        }elseif($user===2){
            Response::create(['code' => '2000', 'message' => '作品' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }elseif($user===3){
            Response::create(['code' => '2000', 'message' => '作品' . $request->param('actionname') . '作品不存在'], 'json')->header($this->header)->send();
        }

    }


    /*
 * 删除作品
 *
 */
    public function getProductinfoBygoodId(Request $request){
        $good_ids = $request->param('good_id');
        $user_id =  $this->user_data['user_id'];
        $ProductServer = new     ProductServer();
        $productdata = $ProductServer->getProductsOneByProductId($good_ids,$user_id);
        if(empty($productdata )){
            Response::create(['code' => '4000', 'message' => '作品' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
        }
        Response::create(['data'=>$productdata,'code' => '2000', 'message' => '作品' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
    }
}
