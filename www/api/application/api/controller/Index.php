<?php
namespace app\api\controller;
use think\Db;
use think\Hook;
use think\Loader;
use think\Request;
use think\Response;
use think\Config;
use app\api\server\GoodsServer;
use app\api\server\UsersServer;
use app\api\server\OrederServer;
use app\dataapi\server\AliyunOss;
use app\api\controller\BaseApi;
use think\Session;
use think\cache\driver\Redis;
use app\api\server\Cache;
use app\api\server\CategoryCache;
class Index extends BaseApi
{
    protected  $user_data;
    public function _initialize()
    {
        Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }
    public function index(){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;

        $indexlist = self::indexlist($user_id);

        //返回json数据
        Response::create(['data'=>$indexlist, 'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //首页虚拟作品加载
    public function virtua_goods_get(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $p = $request->param('page',1);
        $limitnum = 6;
        //首页商品区展示10条（热卖）
        $GoodsServer = new GoodsServer();
        $user_id = $user_id > 0 ? $user_id : 0;
        $virtua_goods = $GoodsServer->virtua_user_goods($p,$limitnum,$user_id);

        Response::create(['data'=>$virtua_goods,'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();

    }

    //获取头条新闻列表页
    public function new_list(Request $request){
        $cate_id = $request->param('cat_id',2);
        $new_cates=Db::name('article_cat')->field('cat_id,cat_name')->where('parent_id',1)->select();

        $newlist=Db::name('article')->order('article_id DESC')->where('cat_id',$cate_id)->limit(20)->select();  //个分类头条
        $url = config::get('adminurl');
        foreach($newlist as $key=>&$vo){
            $vo['thumb'] =  $url.$vo['thumb'];

        }

        Response::create(['data'=>['new_cates'=>$new_cates,'newlist'=>$newlist],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //获取头条新闻详情页
    public function news_detail(Request $request){
        $article_id = $request->param('article_id');
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        //当前用户信息等级
        $user_head_pic = Db::name('users')->where('user_id',$user_id)->value('head_pic');

        if(!isset($article_id)){
            Response::create(['code' => '4000', 'message' => "无新闻id"], 'json')->header($this->header)->send();
        }
        $new_detail=Db::name('article')->where(array('article_id'=>$article_id))->find();  //新闻详情
        $url = config::get('adminurl');
        $new_detail['content'] = self::get_img_thumb_url( $new_detail['content'],$url);
        $new_detail['content'] = htmlspecialchars_decode(  $new_detail['content']);
        $new_detail['thumb'] = $url.$new_detail['thumb'];

        //今日推广商品区
        $goods_speard = Db::name('goods')
            ->field('goods_id,goods_name,original_img,goods_content,start_price,click_count,endTime')
            ->where(['is_spread'=>1,'endTime'=>['>',time()]])
            ->order('endTime DESC')->limit(5)
            ->select();
        $ossobj = new AliyunOss(true);
        $aliyunbucket = Config::get('aliyun.bucket');
        foreach($goods_speard as $k=>&$v){
            $v['original_img'] = $ossobj->getCurlofimgUsenoAuth($aliyunbucket, $v['original_img']);
        }

        Response::create(['data' => ['user_head_pic'=>$user_head_pic,'new_detail'=>$new_detail,'goods_speard'=>$goods_speard], 'code' => '2000', 'message' => "获取成功"], 'json')->header($this->header)->send();

    }

    //商品区列表页的点赞 取消点赞功能
    public function click_heart(Request $request){
        $user_id = (int)$this->user_data['user_id']; //当前登录者user_id;
        $goods_id = $request->param('goods_id');  //获取点赞的goods_id
        $goods_collect = Db::name('goods_collect');
        if($user_id > 0 && $goods_id > 0){

            $fff = mt_rand(8,16);
            Db::name('goods')->where(['goods_id' => $goods_id])->update(['click_count' => ['exp', 'click_count+' . $fff]]);

            $re = $goods_collect->where(['user_id'=>$user_id,'goods_id'=>$goods_id])->whereNull('delete_time')->find();
            if(!empty($re)){
                Response::create(['code'=>2,'msg'=>'已收藏'], 'json')->header($this->header)->send();
            }

            $exist = $goods_collect->where(['user_id'=>$user_id,'goods_id'=>$goods_id])->find();
            if($exist){
                $goods_collect->where(['user_id'=>$user_id,'goods_id'=>$goods_id])->update(['delete_time' => ['exp','null']]);
                Response::create(['code'=>3,'msg'=>'收藏成功'], 'json')->header($this->header)->send();
            }else{
                $getid = $goods_collect->insertGetId(['user_id'=>$user_id,'goods_id'=>$goods_id,'add_time'=>time()]);  //点赞成功
                if($getid>0){
                    Response::create(['code'=>3,'msg'=>'收藏成功'], 'json')->header($this->header)->send();
                }else{
                    Response::create(['code'=>4,'msg'=>'收藏失败'], 'json')->header($this->header)->send();
                }

            }
        }else{
            Response::create(['code'=>4,'msg'=>'收藏失败'], 'json')->header($this->header)->send();
        }




    }

    static  function  get_img_thumb_url($content="",$preffix="")
    {
        $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?([\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
        $callback = function($matches) use($preffix){
            $url= $matches[1];
            if(stripos($url,'http://')===false || stripos($url,'https://')===false){
                $pre = mb_substr($url,0,1);
                if($pre =='.'){
                    $url = mb_substr($url,1);
                }
                $pre = mb_substr($url,0,1);
                if($pre =='/'){
                    $url = mb_substr($url,1);
                }
                $url = $preffix.$url;
            }
            return   '<img src='.$url.' style="max-width:100%">';

        };
        $content = preg_replace_callback ($pregRule,$callback,$content);
        return $content;
    }

    public static function indexlist($user_id){

        //文章缓存
        $article_cat_ids=Db::name('article_cat')->where(array('parent_id'=>1))->column('cat_id');
        $fh_two_articles = md5('fh_two_articles');
        $two_articles =  Cache::get($fh_two_articles);
        if(empty($two_articles)){
            $two_articles=Db::name('article')->field('article_id,title')->order('article_id DESC')->where('cat_id','in',$article_cat_ids)->limit(6)->select();
            Cache::set($fh_two_articles,$two_articles);
        }

        //上方banners缓存
        $fh_top_banners = md5('fh_three_banners');
        $top_banners =  Cache::get($fh_top_banners);
        if(empty($top_banners)){
            $top_banners=Db::name('ad')->where(['pid'=>1,'enabled'=>1])->field('ad_name,ad_link,ad_code')->limit(5)->select(); //首页5条轮播
            foreach($top_banners as $k=>&$v){
                $v['ad_code'] = 'http://admin.jiuxintangwenhua.com'.$v['ad_code'];
            }
            Cache::set($fh_top_banners,$top_banners);
        }
        //中部banners缓存
        $fh_center_banners = md5('fh_center_banners');
        $center_banners =  Cache::get($fh_center_banners);
        if(empty($center_banners)){
            $center_banners=Db::name('ad')->where(['pid'=>2,'enabled'=>1])->field('ad_name,ad_link,ad_code')->limit(1)->select(); //首页5条轮播
            foreach($center_banners as $kk=>&$vv){
                $vv['ad_code'] = 'http://admin.jiuxintangwenhua.com'.$vv['ad_code'];
            }
            Cache::set($fh_center_banners,$center_banners);
        }

        //首页每日推荐区(60条数据)缓存
        $tuijian_goods_ids = Db::name('goods')
            ->whereNull('delete_time')
            ->where('is_recommend',1)
            ->where('endTime','>',time())
            ->limit(20)
            ->column('goods_id');
        $tuijian_goods =[];
        if(!empty($tuijian_goods_ids)){
            foreach ($tuijian_goods_ids as  $key=>$tuijian_goods_id){
                $tuijian_goods_id= (int)$tuijian_goods_id;
                $tuijian_goods_id_key = md5('is_recommend_list'.$tuijian_goods_id);
                $tuijian_goods_id_value = Cache::get($tuijian_goods_id_key);
                if(empty($tuijian_goods_id_value)){
                    $tuijian_goods_id_value= Db::name('goods')
                        ->where('goods_id',$tuijian_goods_id)
                        ->field('goods_id,cat_id,goods_name,original_img')
                        ->find();
                    //$tuijian_goods_id_value['original_img'] =  $oss->getCurlofimgUsenoAuthfmort($aliyun_bucket, $tuijian_goods_id_value['original_img']);
                    //分类
                   $getCategorydata = CategoryCache::getCategory($tuijian_goods_id_value['cat_id']);
                   if(!empty($tuijian_goods_id_value)){
                       $tuijian_goods_id_value['name']  =$getCategorydata;
                   }
                    Cache::set($tuijian_goods_id_key,$tuijian_goods_id_value);
                }
                $tuijian_goods[]=$tuijian_goods_id_value;
            }
        }
        //返回json数据
        $data = [
            'two_articles'=>$two_articles,
            'top_banners'=>$top_banners,
            'center_banners'=>$center_banners,
            'tuijian_goods'=>$tuijian_goods

        ];
        return $data;
    }



}