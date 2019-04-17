<?php

namespace app\api\controller;

use app\api\model\GoodsImages;
use think\Db;
use think\Hook;
use think\Request;
use think\Response;
use app\api\server\GoodsServer;
use app\dataapi\server\AliyunOss;
use think\Config;
use app\api\server\Cache;

class Goods extends BaseApi
{
    protected $user_data;

    public function _initialize()
    {
        parent::_initialize();
        Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
       // \app\api\behavior\Addclick::appInit($this->user_data);

    }

    /**[ 商品区区列表页 (按goods_id DESC 倒叙按需下拉加载) ]
     * @param Request $request
     */
    public function goods_list(Request $request)
    {
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 8;
        $where = array(
            'G.is_on_sale' => 1,        //上架商品区
            'G.exchange_integral' => 0,      //不检索积分商品区
            'G.endTime' => ['>',time()]
        );
        $cat_id = (int)$request->param('id',-1);
        if ($cat_id != -1) {   //如果$cat_id = -1 默认所有商品区
            $where['G.cat_id'] = $cat_id;
        }
        input('keywords') ? input('keywords') : false;
        $where['G.goods_content|goods_name'] = ['like', '%' . input('keywords') . '%'];
        $list_data = self::goodslists($p,$limitnum,$where,$user_id);
        Response::create(['data' =>$list_data , 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();
    }

    /**[ 商品区详情页 ]
     * @param Request $request
     */
    public function goods_detail(Request $request)
    {
        $goods_id = $request->param('goods_id', 1);  //商品区id
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $GoodsDetail = GoodsServer::get_goods_detail($goods_id,$user_id); //调用获取详情页相关信息
        Response::create(['data' => $GoodsDetail, 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();
    }

    public static function goodslists($p,$limitnum,$where,$user_id){
        $goods_list_goodids = Db::name('goods')
            ->alias('G')
            ->join('__USERS__ U','G.user_id=U.user_id')
            ->where($where)
            ->whereNull('G.delete_time')
            ->order('U.store_level DESC,upload_time')
            ->page($p,$limitnum)
            ->column('G.goods_id');
        $goods_list = [];
        $goods_collect = Db::name('goods_collect');
        $users = Db::name('users');
        $goodsObj = Db::name('goods');
        foreach($goods_list_goodids as $key=>$good_id){
            $good_id = (int)$good_id;
            $good_id_key = md5('yipinfanghua_lists_fh'.$good_id);
            $goodOneMsg = Cache::get($good_id_key);
            if(empty($goodOneMsg)){
                $goodOneMsg= $goodsObj->where('goods_id',$good_id)
                    ->field('goods_id,goods_name,start_price,exchange_integral,original_img,endTime,click_count,user_id')
                    ->find();
                $goodsObj->where('goods_id',$good_id)->setInc('click_count');
                $goodOneMsg['care_count'] = $goods_collect->where('goods_id',$good_id)->count();  //获取点赞数
                $goodOneMsg['user_msg'] = $users->field('nickname,head_pic')->where('user_id',$goodOneMsg['user_id'])->find();

                Cache::set($good_id_key,$goodOneMsg);
            }
            $goods_list[] = $goodOneMsg;
        }
        //1 轮播
        $fh_banners = md5('fh_banners');
        $three_banners =  Cache::get($fh_banners);
        if(empty($three_banners)){
            $three_banners = Db::name('article')->where(['cat_id'=>1,'is_open'=>1])->field('thumb,link')->limit(5)->select(); //首页5条轮播
            Cache::set($fh_banners,$three_banners);
        }

        $list_data = [
            'goods_list' => $goods_list,
            'three_banners'=>$three_banners
        ];

        return $list_data;
    }

    //商品分类树
    public function categoryList(){
        $goods_category_tree = get_goods_category_tree();
        $search_keyword = Db::name('config')->where('name','search_keyword')->value('value');
        $search_keyword = explode('|',$search_keyword);

        Response::create(['data' => $goods_category_tree, 'search_keyword'=>$search_keyword, 'code' => 2000, 'message' => '获取成功'], 'json')->header($this->header)->send();
    }


    /**
     * 百度收录商品详情链接
     */
    public function collection(Request $request){
        $goods_id = $request->param('goods_id');  //商品区id
        $baidu_collection = $request->param('baidu_collection');
        if($baidu_collection == 1){  //百度推广
            $result = self::baidu_collection($goods_id);
            Response::create(['code' => 2000, 'message' => $result], 'json')->header($this->header)->send();
        }elseif($baidu_collection == 2){  //订阅号

        }else{

        }

    }

    static public function baidu_collection($goods_id){
        $urls = array(
            'http://wap.yipinfanghua.com/#/index/'.$goods_id,   //http://wap.yipinfanghua.com/#/index/7363
        );
        $api = 'http://data.zz.baidu.com/urls?site=wap.yipinfanghua.com&token=vspG42b2ghMcjBlP';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;

    }

}