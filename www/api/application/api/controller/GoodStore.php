<?php
namespace app\api\controller;
use think\Db;
use think\Hook;
use think\Request;
use think\Response;
use think\Session;
use think\Log;
use think\Config;
use app\api\server\GoodsServer;
use app\api\server\UsersServer;
use app\dataapi\server\AliyunOss;
use Carbon\Carbon;
class GoodStore extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }
    //精选好店列表
    public function index(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 6;
        $good_store = Db::name('users')->order('user_level DESC,user_id')->field('user_id,nickname,head_pic')->whereNull('delete_time')->where(['is_goodstore'=>1])
            ->page($p,$limitnum)->select();
        $goodsObj =  Db::name('goods');
        $users = new UsersServer();
        foreach($good_store as $k=>&$v){
            $v['arr_count'] = $goodsObj->where(['user_id'=>$v['user_id']])->whereNull('delete_time')->count();  //当前店铺共计多少件商品区
            //查询当前登陆者是否关注了该用户店铺
            $v['is_care'] = $users->is_get_care($v['user_id'],$user_id);  //判断是否被关注
        }
        return json(['data'=>['good_store'=>$good_store,'good_store_count'=>count($good_store),'user_id'=>$user_id],'code'=>2000,'message'=>'获取成功']);
    }


    /**[ 商品区管理(我的商品区列表) ]
     * @param Request $request
     */
    public function goods_manager(Request $request){
        $where = [
            'user_id'=>(int)$this->user_data['user_id']    //当前登录者user_id
        ];
        if(input('time') == 2){
            $where['endTime'] = ['lt',time()];  //2 下线
        }else{
            $where['endTime'] = ['gt',time()];   //1 在线
        }
        //当前用户信息等级
        $usersevers = new UsersServer();
        $usermsg = $usersevers->usermsg($where['user_id']);
        $my_goods = Db::name('goods')
            ->where($where)
            ->whereNull('delete_time')
            ->order("endTime desc")
            ->field('goods_id,goods_name,start_price,every_add_price,original_img,goods_content,last_update,upload_time,goods_status,endTime,shop_price,click_count,store_count')
            ->select();
        $ossObj = new AliyunOss(true);
        $aliyunbucket = Config::get('aliyun.bucket');
        foreach($my_goods as $k=>$v){
            $my_goods[$k]['original_img'] = $ossObj->getCurlofimgUsenoAuth($aliyunbucket, $v['original_img']);
            //获取当前出价
            $cur_price = Db::name('bid_order')->where(['goods_id'=>$v['goods_id']])->order('id DESC')->find();
            if($cur_price != false){
                $my_goods[$k]['cur_price'] = $cur_price['bid_price'];
            }else{
                $my_goods[$k]['cur_price'] = $v['start_price'];
            }
        }
        Response::create(['data'=>['where'=>$where,'my_goods'=>$my_goods,'usermsg'=>$usermsg],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    /**[ 删除我的商品区 ]
     * @param Request $request
     */
    public function del_paipin(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id');   //获取商品区id
        if($goods_id == null){
            Response::create(['code'=>2002,'message'=>'请选择商品区哦'], 'json')->header($this->header)->send();
        }
        $goodobj = model('goods');
        $goodobj->tempgoods_id=$goods_id;
        $res = $goodobj->save(['delete_time'=>time()],['goods_id'=>$goods_id]);
        if($res){
            Response::create(['code'=>2000,'message'=>'删除成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>2001,'message'=>'删除失败'], 'json')->header($this->header)->send();
        }

    }

    /**[ 下架我的商品区（修改下架时间） ]
     * @param Request $request
     */
    public function down_paipin(Request $request){
        $goods_id = $request->param('goods_id');   //获取商品区id
        $goodobj = model('goods');
        $goodobj->tempgoods_id=$goods_id;

        $down_goods = $goodobj->save(['endTime'=>time()],['goods_id'=>$goods_id]);
        if($down_goods){
            Response::create(['code'=>2000,'message'=>'下架成功'], 'json')->header($this->header)->send();
        }else{
            Response::create(['code'=>2001,'message'=>'下架失败'], 'json')->header($this->header)->send();
        }

    }

    /**[ 群发商品区列表 ]
     * @param Request $request
     */
    public function spread_goods(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        //查询当前店铺等级及群发次数
        $usersevers = new UsersServer();
        $current = $usersevers->usermsg($user_id);

        $today = Carbon::today();
        $unixttimetodaytime = strtotime($today);
        $carbonobj = new  Carbon();
        $datacc = Db::name('user_sendmessage')->where('user_id', $user_id)->where('addtime', '>', $unixttimetodaytime)->lock(true)->value('sendactivemessagecount');
        $datacc = $datacc!==null?(int)$datacc:0;
        //剩余群发次数
        $current['products'] = $current['products'] - $datacc;

        $my_goods = Db::name('goods')
            ->where('user_id',$user_id)
            ->whereNull('delete_time')
            ->where('endTime','>',time())
            ->field('user_id,goods_id,goods_name,original_img,goods_content,upload_time,endTime,shop_price,click_count,store_count')
            ->select();
        Response::create(['data'=>['my_goods'=>$my_goods,'current'=>$current],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //店铺升级（费用）
    public function store_level(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        //当前用户信息等级
        $usersevers = new UsersServer();
        $user_data = $usersevers->usermsg($user_id);

        $store_level = Db::name('store_level')->field('store_level_id,store_name,store_img,store_cost')->where('store_level_id','>',2)->select();
        foreach($store_level as $key=>&$vo){
            $vo['store_img'] = 'http://admin.jiuxintangwenhua.com/Public' . $vo['store_img'];
        }


        Response::create(['data'=>$store_level,'user_data'=>$user_data,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //今日推荐
    public function is_recommend(Request $request){
        $p = $request->param('page',1);
        $limitnum = 8;
        $where = [
            'is_recommend' => 1,
            'endTime' => ['>=',time()]
        ];
        $recommend_list = Db::name('goods')->where($where)
            ->order('upload_time DESC')
            ->page($p,$limitnum)
            ->select();
        $oss = new AliyunOss(true);
        $bucket = Config::get('aliyun.bucket');
        $goods_collect_obj  = Db::name('goods_collect');
        foreach($recommend_list as $key=>&$vo){
            $vo['original_img'] = $oss->getCurlofimgUsenoAuth($bucket, $vo['original_img']);
            $vo['care_count'] = $goods_collect_obj->where('goods_id',$vo['goods_id'])->whereNull('delete_time')->count();
        }
        Response::create(['data' => $recommend_list, 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();

    }

    //精选
    public function well_chosen(Request $request){
        $p = $request->param('page',1);
        $limitnum = 8;
        $where = [
            'is_hot' => 1,
            'endTime' => ['>=',time()]
        ];
        $recommend_list = Db::name('goods')->where($where)
            ->whereNull('delete_time')
            ->order('upload_time DESC')
            ->page($p,$limitnum)
            ->select();
        $oss = new AliyunOss(true);
        $bucket = Config::get('aliyun.bucket');
        $goods_collect_obj  = Db::name('goods_collect');
        foreach($recommend_list as $key=>&$vo){
            $vo['original_img'] = $oss->getCurlofimgUsenoAuth($bucket, $vo['original_img']);
            $vo['care_count'] = $goods_collect_obj->where('goods_id',$vo['goods_id'])->whereNull('delete_time')->count();
        }
        Response::create(['data' => $recommend_list, 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();

    }


}