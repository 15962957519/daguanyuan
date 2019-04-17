<?php
namespace app\api\server;

use app\api\model\Goods;
use app\api\model\Users;
use think\Db;
use think\Request;
use think\Config;
use app\dataapi\server\AliyunOss;
use app\api\server\CategoryCache;
/*
 * 产品逻辑处理
 */
class GoodsServer
{
    //商品区详情
    static public function get_goods_detail($goods_id,$user_id){
        $model = Db::name('goods');
        $goods_collectobj = Db::name('goods_collect');
        $goods_id = (int)$goods_id;
        $model->where('goods_id', $goods_id)->setInc('click_count', 1);  //增加点击浏览量
        $goods = $model->field('goods_id,upload_time,endTime,vistorassign')->where('goods_id',$goods_id)->find();
        self::addclick($goods);


        //查询该商品区图 缓存
        $goods_images_key = md5('fh_goods_images'.$goods_id);
        $goods_images = Cache::get($goods_images_key);
        if(empty($goods_images)){
            $goods_images = Db::name('goods_images')->field('goods_id,image_url as img')->where(array('goods_id' => $goods_id))->select();

            Cache::set($goods_images_key,$goods_images);
        }
        //关注者头像
        $care_goods_pics = md5('care_goods_pics'.$goods_id);
        $fans_head_pic = Cache::get($care_goods_pics);
        if(empty($fans_head_pic)){
            $fans_head_pic = Db::name('goods_collect')->alias('gc')
                ->join('__USERS__ G','gc.user_id=G.user_id')
                ->field('G.head_pic')
                ->where('gc.goods_id',$goods_id)
                ->whereNull('gc.delete_time')
                ->limit(20)
                ->select();
            Cache::set($care_goods_pics,$fans_head_pic);
        }
        //商品详情
        $goods_detail_key = md5('goods_detail_key'.$goods_id);
        $goods_detail = Cache::get($goods_detail_key);
        if(empty($goods_detail)){
            $goods_detail = $model->field('goods_id,start_price,every_add_price,last_update,upload_time,protect_price,endTime,goods_name,goods_content,exchange_integral,
            original_img,store_count,goods_status,is_distribute,is_free_shipping,enableReturn,is_talk_price')
                ->where('goods_id',$goods_id)
                ->find();
            $goods_detail['current_time'] = $_SERVER['REQUEST_TIME'];
            $goods_detail['goods_content'] = ihtmlspecialchars($goods_detail['goods_content']);
            Cache::set($goods_detail_key,$goods_detail);
        }
        $goods_detail['click_count'] = Db::name('goods')->where('goods_id',$goods_id)->value('click_count');
        $goods_user_id = $model->where('goods_id',$goods_id)->value('user_id');

        $goods_usermsg = Db::name('users')->where('user_id',$goods_user_id)->field('user_id,head_pic,is_authentication,nickname,user_level')->find();
        $goods_detail = array_merge($goods_detail,$goods_usermsg);
        //该商品议价信息
        $bidperson = Db::name('bid_order')->alias('B')
            ->join('__USERS__ U','B.user_id = U.user_id')
            ->where(['B.goods_id'=>$goods_id])
            ->whereNull('B.delete_time')
            ->field('B.*,U.nickname,U.head_pic')
            ->order('B.id DESC')->limit(6)
            ->select();
        if($bidperson == false){
            $bidperson = [];
        }

        $care_count = $goods_collectobj->where(['goods_id' => $goods_id])->whereNull('delete_time')->count();  //获取点赞数
        $re = $goods_collectobj->where(['user_id' => $user_id, 'goods_id' => $goods_id])->value('collect_id');   //当前登陆者是否已点赞当前商品区
        if (!empty($re)) {
            $is_dianzan = ['code' => 2, 'msg' => '已点赞'];
        } else {
            $is_dianzan = ['code' => 1, 'msg' => '未点赞'];
        }
        //判断当前登录者是否关注该商品区发布者
        $currend_id = $model->where(array('goods_id' => $goods_id))->value('user_id');  //获取商品区发布者user_id
        $users = new UsersServer();
        $care = $users->is_get_care($currend_id, $user_id);  //判断是否被关注
        //是否被购买
        $is_sale = self::is_sale($goods_id);

        //添加访问记录(个人足迹)
        $visitobj =Db::name('goods_visit');
        $is_re = $visitobj ->where(['goods_id' => $goods_id, 'user_id' => $user_id])->value('visit_id');
        if (empty($is_re)) {
            $visitobj->insert(['goods_id' => $goods_id, 'user_id' => $user_id, 'visittime' => time()]);
        }
        $is_re_de = $visitobj->where(['goods_id' => $goods_id, 'user_id' => $user_id])->value('delete_time');
        if($is_re_de != null){
            $visitobj->where(['goods_id' => $goods_id, 'user_id' => $user_id])->update(['visittime' => time(),'delete_time' => ['exp','null']]);
        }


        $caution = Db::name('config')->where('name','caution_money')->value('value');

        return ['user_id'=>$user_id,
            'goods_images' => $goods_images,
            'fans_head_pic' => $fans_head_pic,
            'goods_detail' => $goods_detail,
            'is_care' => $care,
            'is_dianzan' => $is_dianzan,
            'care_count' => $care_count,
            'bidperson' => $bidperson,
            'is_sale' => $is_sale,
            'caution' =>$caution
        ];

    }

    //商品详情页同步点赞浏览
    public static function addclick($goods){
        $time = time();
        $goods_collect = Db::name('goods_collect');
        $timespace = $goods['endTime'] - $goods['upload_time'];  //时间差
        if($timespace > 86400*3){  //大于3天 缩进一天
            $timespace = $timespace - 86400;
        }elseif ($timespace > 86400 && $timespace < 86400*3){   //大于1天小于3天  缩进半天
            $timespace = $timespace - 43200;
        }

        if($goods['vistorassign'] > 0){
            $vistorassign = $goods['vistorassign'];
        }else{
            $vistorassign = 1;
        }

        $cur_timespace = $time-$goods['upload_time']; //当前时间差
        $cur_vistorassign = intval($cur_timespace/($timespace/$vistorassign));  //当前需要分配的收藏个数
        if(mt_rand(1,10)/10 > 0.7){
            $cur_vistorassign = $cur_vistorassign + 1;
        }
        $goods_collect_count = $goods_collect->where(['goods_id' => $goods['goods_id']])->whereNull('delete_time')->count();
        $need_nums = $cur_vistorassign - $goods_collect_count;   // 需要执行的次数

        if($need_nums > 0){
            $goodsobj = Db::name('goods');
            $image = new \app\dataapi\lib\Image();
            $UserServer = new \app\dataapi\server\UserServer();
            do{
                $need_nums -- ;
                $ddd = $image->flagimage();
                if($ddd['user_id'] > 0 && $goods['goods_id'] > 0){
                    try{
                        $UserServer->vhostuserUpdateLike($ddd['user_id'], ['goods_id' => $goods['goods_id']]);
                    }catch (\Exception $e){
                        echo $e->getMessage();
                    }
                }
            }while($need_nums > 0);

            $goodsobj->where('goods_id',$goods['goods_id'])->update(['hasvistorassin'=> $cur_vistorassign]);
        }else{
            //echo '已操作完毕,无需执行';
        }
        return true;

    }

    //删除我的商品区
    public function delPaipin($goods_id,$user_id){
        Db::startTrans();
        try{
            model('goods')->where(array('goods_id'=>$goods_id))->update(['delete_time'=>time()]);   //删除商品区

            Db::commit();
            $message = array('code'=>1,'msg'=>'删除成功');
        }catch(\Exception $e){
            Db::rollback();
            $message = array('code'=>0,'msg'=>'删除失败');
        }
        return $message;
    }

    //获取首页虚拟用户上传的虚拟产品
    public function virtua_user_goods($p,$limitnum,$user_id){
        /*$virtuagoodids = Db::name('goods')->where('endTime','>=',time()-10800)
            ->where('is_toplatform',1)
            ->order('upload_time DESC')
            ->whereNull('delete_time')
            ->page($p,$limitnum)
            ->column('goods_id');*/
        $virtuagoodids = Db::name('goods')->alias('G')
            ->join('__USERS__ U','G.user_id = U.user_id')
            ->whereNull('G.delete_time')
            ->where('U.store_level','>',2)
            ->order('G.upload_time DESC')
            ->page($p,$limitnum)
            ->column('G.goods_id');


        $virtuagoods = [];

        $goodsobj = Db::name('goods');
        if(!empty($virtuagoodids)){
            foreach($virtuagoodids as $key=>$virtuagood_id){
                $virtuagood_id = (int)$virtuagood_id;
                $virtuagood_id_key = md5('yipinfanghua_virtua_list_keykey'.$virtuagood_id);
                $virtuagoodidonevalue = Cache::get($virtuagood_id_key);
                if(empty($virtuagoodidonevalue)){
                    $virtuagoodidonevalue= $goodsobj
                        ->where('goods_id',$virtuagood_id)
                        ->field('goods_id,goods_name,start_price,original_img,user_id,goods_content,upload_time,endTime,click_count,goods_status,is_distribute,distribute_proportion,distribute_money')
                        ->find();
                    $goodsobj->where('goods_id',$virtuagood_id)->setInc('click_count');
                    $virtuagoodidonevalue['care_count'] = Db::name('goods_collect')->where(['goods_id'=>$virtuagood_id])->whereNull('delete_time')->count();  //获取点赞数
                    $cur_price = Db::name('bid_order')->where('goods_id',$virtuagood_id)->order('id DESC')->value('bid_price');
                    if(isset($cur_price) && $cur_price>0){
                        $virtuagoodidonevalue['cur_price'] = $cur_price;
                    }else{
                        $virtuagoodidonevalue['cur_price'] = $virtuagoodidonevalue['start_price'];
                    }
                    Cache::set($virtuagood_id_key,$virtuagoodidonevalue);
                }

                $userinfo = getuserinfofromcache($virtuagoodidonevalue['user_id']);
                if(!empty($userinfo)){
                    if (!empty($userinfo['nickname'])) {
                        $virtuagoodidonevalue['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $userinfo['nickname']);
                    } else {
                        $virtuagoodidonevalue['nickname'] = '';
                    }
                    $virtuagoodidonevalue['head_pic']=$userinfo['head_pic'];
                    $virtuagoodidonevalue['store_name']=$userinfo['store_name'];
                    $virtuagoodidonevalue['store_level']=$userinfo['store_level'];
                    $virtuagoodidonevalue['is_authentication']=$userinfo['is_authentication'];

                }else{
                    $virtuagoodidonevalue['nickname'] = '';
                    $virtuagoodidonevalue['head_pic']='';
                    $virtuagoodidonevalue['store_name']='';
                    $virtuagoodidonevalue['store_level']='';
                    $virtuagoodidonevalue['is_authentication']='';
                }

                $virtuagoods[] = $virtuagoodidonevalue;

            }
        }
        return $virtuagoods;
    }

    //精选好店列表加载更多
    public function store_getmore($start,$user_id){
        $good_store = Db::name('users')->order('user_level DESC')->field('user_id,nickname,head_pic')->where(['is_goodstore'=>1])->limit($start,10)->select();
        foreach($good_store as $k=>$v){
            $arr = Db::name('goods')->where(['user_id'=>$v['user_id']])->whereNull('delete_time')->field('goods_id,original_img,goods_name,shop_price')->limit(8)->order('goods_id DESC')->select(); //当前店铺下的商品区
            $good_store[$k]['arr_count'] = Db::name('goods')->where(['user_id'=>$v['user_id']])->whereNull('delete_time')->count();  //当前店铺共计多少件商品区
            foreach($arr as $sk=>$sv){
                $res_id = $sv['original_img'];
                $arr[$sk]['original_img'] = (new AliyunOss(true))->getCurlofimgUsenoAuth(Config::get('aliyun.bucket'), $res_id);
            }
            $good_store[$k]['goods'] = $arr;
            //查询当前登陆者是否关注了该用户店铺
            $users = new UsersServer();
            $is_care = $users->is_get_care($v['user_id'],$user_id);  //判断是否被关注
            $good_store[$k]['is_care'] = $is_care;
        }
        return $good_store;
    }

    //是否被购买
    static public function is_sale($goods_id){

        $orderMsg = Db::name('order_goods')->alias('OG')
            ->join('__ORDER__ OR','OG.order_id = OR.order_id')
            ->where('goods_id',$goods_id)
            ->where('OR.pay_status',1)
            ->find();
        if(!empty($orderMsg)){
            $sale_msg = Db::name('users')->field('nickname,head_pic')->where(['user_id'=>$orderMsg['user_id']])->find();
            $msg = ['code'=>5000,'msg'=>'商品已被购买','result'=>$sale_msg];
        }else{
            $msg = ['code'=>4000,'msg'=>'商品未被购买','result'=>''];
        }
        return $msg;


    }

}